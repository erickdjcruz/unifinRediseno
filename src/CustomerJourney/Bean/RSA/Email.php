<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\SelectToOption as SelectToOption;

/**
 * This class is here to have functions for email
 * functionality for RSA/CJ_Form against a field
 * `Select "To" Email Address`
 */
class Email
{
    /**
     * return the Email Template
     *
     * @param string $email_templates_id
     * @return \SugarBean | null
     */
    protected function getEmailTemplate($email_templates_id)
    {
        if (empty($email_templates_id)) {
            return;
        }

        $emailTemplateBean = \BeanFactory::getBean('EmailTemplates', $email_templates_id, ['disable_row_level_security' => true]);
        if (!empty($emailTemplateBean->id)) {
            return $emailTemplateBean;
        }
        return null;
    }

    /**
     * Send the email according to the select_to_email_address
     * field of RSA
     *
     * @param \SugarBean $email
     * @param \SugarBean $RSABean
     * @param \SugarBean $activityOrStage
     *
     * @return undefined
     * @codeCoverageIgnore
     */
    public function sendEmail($email, $RSABean, $activityOrStage)
    {
        try {
            if (empty($activityOrStage->id) || empty($RSABean->email_templates_id)) {
                $GLOBALS['log']->fatal('There is no Email Template Or Parent Record.');
                return;
            }

            $emailTemplateBean = $this->getEmailTemplate($RSABean->email_templates_id);
            if (is_null($emailTemplateBean) || (!empty($emailTemplateBean) && empty($emailTemplateBean->id))) {
                $GLOBALS['log']->fatal('There is no Email Template.');
                return;
            }

            $parentRecord = SelectToOption::getParentRecord($activityOrStage);
            if (empty($parentRecord->id)) {
                $GLOBALS['log']->fatal('There are no Parent Record.');
                return;
            }

            $recipientsInfo = SelectToOption::getRecipients($RSABean->select_to_email_address, $parentRecord);
            if (empty($recipientsInfo)) {
                $GLOBALS['log']->fatal('There are no Recipents.');
                return;
            }

            $recipientsEmails = SelectToOption::getRecipientsEmails($recipientsInfo);
            if (empty($recipientsEmails)) {
                $GLOBALS['log']->fatal('There are no Recipents Emails.');
                return;
            }

            if (empty($email)) {
                $email = new \Email();
            }

            $email->name = $emailTemplateBean->subject;
            $email->description_html = $emailTemplateBean->body_html;
            $email->description = $emailTemplateBean->body;
            $email->parent_type = $parentRecord->getModuleName();
            $email->parent_id = $parentRecord->id;
            $email->state = 'Draft';
            $email->save();

            $this->addEmailParticipants($recipientsEmails, $email);
            $this->addAttachments($emailTemplateBean->id, $email);

            $config = null;
            $oe = null;

            if (empty($email->outbound_email_id)) {
                $seed = \BeanFactory::newBean('OutboundEmail');
                $q = new \SugarQuery();
                $q->from($seed);
                $q->where()->in('type', [\OutboundEmail::TYPE_SYSTEM, \OutboundEmail::TYPE_SYSTEM_OVERRIDE]);
                // There should only be one system or system-override account that is accessible. The admin can actually
                // access both a system and system-override account. Sorting in descending order by type and setting a
                // limit guarantees that the system-override account is prioritized when finding the default record to
                // use.
                $q->orderBy('type');
                $q->limit(1);
                $beans = $seed->fetchFromQuery($q, ['id']);

                if (!empty($beans)) {
                    $bean = array_shift($beans);
                    $email->outbound_email_id = $bean->id;
                }
            }

            if (!empty($email->outbound_email_id)) {
                $oe = \BeanFactory::retrieveBean('OutboundEmail', $email->outbound_email_id);
            }

            if ($oe->id) {
                if ($oe->isConfigured()) {
                    $config = \OutboundEmailConfigurationPeer::buildOutboundEmailConfiguration(
                        $GLOBALS['current_user'],
                        [
                            'config_id' => $oe->id,
                            'config_type' => $oe->type,
                            'from_email' => $oe->email_address,
                            'from_name' => $oe->name,
                            'replyto_email' => $oe->reply_to_email_address,
                            'replyto_name' => $oe->reply_to_name,
                        ],
                        $oe
                    );
                } else {
                    $GLOBALS['log']->fatal('The configuration for sending email is invalid', \MailerException::InvalidConfiguration);
                    return;
                }
            }

            if (empty($config)) {
                $GLOBALS['log']->fatal('Could not find a configuration for sending email', \MailerException::InvalidConfiguration);
                return;
            }

            $email->sendEmail($config);
        } catch (\MailerException $e) {
            switch ($e->getCode()) {
                case MailerException::FailedToSend:
                case MailerException::FailedToConnectToRemoteServer:
                case MailerException::InvalidConfiguration:
                    $GLOBALS['log']->fatal('smtp_server_error : ' . $e->getUserFriendlyMessage());
                    // no break
                case MailerException::InvalidHeader:
                case MailerException::InvalidEmailAddress:
                case MailerException::InvalidAttachment:
                case MailerException::FailedToTransferHeaders:
                case MailerException::ExecutableAttachment:
                    $GLOBALS['log']->fatal('smtp_payload_error : ' . $e->getUserFriendlyMessage());
                    // no break
                default:
                    $GLOBALS['log']->fatal($e->getUserFriendlyMessage());
            }
        } catch (\Exception $e) {
            $GLOBALS['log']->fatal('Failed to send the email: ' . $e->getMessage());
        }
    }

    /**
     * create the Participants and then add in Email
     *
     * @param array $recipientsEmails
     * @param \SugarBean $email
     *
     * @return undefined
     */
    protected function addEmailParticipants($recipientsEmails, $email)
    {
        if (empty($recipientsEmails) || empty($email)) {
            return;
        }

        $toCache = \BeanFactory::newBean('EmailParticipants');
        foreach ($recipientsEmails as $emailAddressInfo) {
            $to = clone $toCache;
            $to->new_with_id = true;
            $to->name = $emailAddressInfo['bean_module'];
            $to->id = \Sugarcrm\Sugarcrm\Util\Uuid::uuid1();
            $to->parent_type = $emailAddressInfo['bean_module'];
            $to->parent_id = $emailAddressInfo['bean_id'];
            $to->save();
            if ($email->load_relationship('to')) {
                $email->to->add($to);
            }
        }
    }

    /**
     * Add the attachments of Email Template in Current Email
     *
     * @param string $emailTemplateId
     * @param \SugarBean $email
     *
     * @return undefined
     */
    protected function addAttachments($emailTemplateId, $email)
    {
        if (empty($emailTemplateId)) {
            return;
        }

        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean('Notes'), ['team_security' => false]);
        $query->select('id');
        $query->where()->equals('email_id', $emailTemplateId);

        if (version_compare($GLOBALS['sugar_config']['sugar_version'], '11.0', '>=')) {
            $query->where()
                ->equals('email_type', 'EmailTemplates');
        } else {
            $query->where()->queryOr()
                ->equals('email_type', 'Emails')
                ->equals('email_type', 'EmailTemplates');
        }

        $results = $query->execute();
        $emailTemplateNotes = array_unique(array_column($results, 'id'));
        $notesIDs = $this->createNotesCopies($emailTemplateNotes);
        if ($email->load_relationship('attachments') && !empty($notesIDs)) {
            $email->attachments->add($notesIDs);
        }
    }

    /**
     * Create the copies of Email Templates attachments
     * for the current email
     *
     * @param array $emailTemplateNotes
     *
     * @return array $notesIDs
     */
    protected function createNotesCopies($emailTemplateNotes)
    {
        $notesIDs = [];
        foreach ($emailTemplateNotes as $noteId) {
            $emailTemplateNote = \BeanFactory::getBean('Notes', $noteId, ['disable_row_level_security' => true, 'use_cache' => false]);
            $note = clone $emailTemplateNote;
            $note->new_with_id = true;

            // copy the orginal note id in upload_id field so that
            // in notes save function the file will automatically
            // get and then copied with new id
            $note->upload_id = $noteId;
            $note->id = \Sugarcrm\Sugarcrm\Util\Uuid::uuid1();
            $note->save();
            $notesIDs[] = $note->id;
        }
        return array_unique($notesIDs);
    }
}
