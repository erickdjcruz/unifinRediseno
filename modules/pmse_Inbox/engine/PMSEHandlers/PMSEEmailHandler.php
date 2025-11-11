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

require_once 'include/workflow/alert_utils.php';

use Sugarcrm\Sugarcrm\ProcessManager;

class PMSEEmailHandler
{
    /**
     * The Bean Handler object
     * @var PMSEBeanHandler
     */
    private $beanUtils;

    /**
     * The Administration bean
     * @var Administration
     */
    private $admin;

    /**
     * The Localization Bean
     * @deprecated Will be removed in a future release
     * @var PMSELogger
     */
    private $locale;

    /**
     * The Logger object
     * @var PMSELogger
     */
    private $logger;

    /**
     * The Related Module object
     * @var PMSERelatedModule
     */
    private $pmseRelatedModule;

    /**
     * Current flow data
     * @var array
     */
    private $flowData = [];

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $msg = 'The %s method will be removed in a future release and should no longer be used';
        LoggerManager::getLogger()->deprecated(sprintf($msg, __METHOD__));
    }

    /**
     * Get the PMSE Related Module object
     * @return PMSERelatedModule
     */
    protected function getRelatedModuleObject()
    {
        if (empty($this->pmseRelatedModule)) {
            $this->pmseRelatedModule = ProcessManager\Factory::getPMSEObject('PMSERelatedModule');
        }

        return $this->pmseRelatedModule;
    }

    /**
     * Gets the proper bean for processing
     * @param SugarBean $bean The target bean
     * @param string $module The related module name
     * @return SugarBean
     * @deprecated Will be removed in a future release
     */
    protected function getProperBean(SugarBean $bean, $module)
    {
        global $beanList;
        // Module in this case could be a relationship name, link name or
        // some other value
        if (!isset($beanList[$module])) {
            return $this->getRelatedModuleObject()->getRelatedModule($bean, $module);
        }
        // If the module is an actual module, send the original bean back
        return $bean;
    }

    /**
     * Gets the Bean Handler object
     * @return PMSEBeanHandler
     * @codeCoverageIgnore
     */
    public function getBeanUtils()
    {
        if (empty($this->beanUtils)) {
            $this->beanUtils = ProcessManager\Factory::getPMSEObject('PMSEBeanHandler');
        }

        return $this->beanUtils;
    }

    /**
     * Gets the localization object
     * @return type
     * @codeCoverageIgnore
     * @deprecated Will be removed in a future release
     */
    public function getLocale()
    {
        $msg = 'The %s method will be removed in a future release and should no longer be used';
        LoggerManager::getLogger()->deprecated(sprintf($msg, __METHOD__));

        global $locale;
        return $locale;
    }

    /**
     * Gets the PMSE Logger object
     * @return PMSELogger
     * @codeCoverageIgnore
     */
    public function getLogger()
    {
        if (empty($this->logger)) {
            $this->logger = PMSELogger::getInstance();
        }

        return $this->logger;
    }

    /**
     * Gets the administration object
     * @return Administration
     * @codeCoverageIgnore
     */
    public function getAdmin()
    {
        if (empty($this->admin)) {
            $this->admin = new Administration();
        }

        return $this->admin;
    }

    /**
     * Sets the administration object
     * @param Administration $admin
     */
    public function setAdmin(Administration $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Sets the bean handler object
     * @param PMSEBeanHandler $beanUtils
     * @codeCoverageIgnore
     */
    public function setBeanUtils(PMSEBeanHandler $beanUtils)
    {
        $this->beanUtils = $beanUtils;
    }

    /**
     * Sets the localization object
     * @param type $locale
     * @codeCoverageIgnore
     * @deprecated Will be removed in a future release
     */
    public function setLocale($locale)
    {
        $msg = 'The %s method will be removed in a future release and should no longer be used';
        LoggerManager::getLogger()->deprecated(sprintf($msg, __METHOD__));

        $this->locale = $locale;
    }

    /**
     * Sets the logger oject
     * @param PMSELogger $logger
     * @codeCoverageIgnore
     */
    public function setLogger(PMSELogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets the bean
     * @param $module
     * @param null $beanId
     * @param array $params
     * @return null|SugarBean
     */
    public function retrieveBean($module, $beanId = null, $params = [])
    {
        return BeanFactory::getBean($module, $beanId, $params);
    }

    /**
     * Get the email data stored in a json string and also processes and parses the variable data.
     * @param type $bean
     * @param type $json
     * @param type $flowData
     * @return \StdClass
     */
    public function processEmailsFromJson($bean, $json, $flowData)
    {
        $addresses = json_decode($json);
        $result = new stdClass();
        if (isset($addresses->to) && is_array($addresses->to)) {
            $result->to = $this->processEmailsAndExpand($bean, $addresses->to, $flowData);
        }
        if (isset($addresses->cc) && is_array($addresses->cc)) {
            $result->cc = $this->processEmailsAndExpand($bean, $addresses->cc, $flowData);
        }
        if (isset($addresses->bcc) && is_array($addresses->bcc)) {
            $result->bcc = $this->processEmailsAndExpand($bean, $addresses->bcc, $flowData);
        }

        return $result;
    }

    /**
     * Process the email and also obtains the bean data that needs to be inserted in the email object,
     * replacing the variables instances with the actual value.
     * @param type $bean
     * @param type $to
     * @param type $flowData
     * @return \StdClass
     * @codeCoverageIgnore
     */
    public function processEmailsAndExpand($bean, $to, $flowData)
    {
        $res = [];

        foreach ($to as $entry) {
            switch (strtoupper($entry->type)) {
                case 'USER':
                    $res = array_merge(
                        $res,
                        $this->processUserEmails($bean, $entry, $flowData)
                    );
                    break;
                case 'TEAM':
                    $res = array_merge(
                        $res,
                        $this->processTeamEmails($bean, $entry, $flowData)
                    );
                    break;
                case 'ROLE':
                    $res = array_merge(
                        $res,
                        $this->processRoleEmails($bean, $entry, $flowData)
                    );
                    break;
                case 'RECIPIENT':
                    $res = array_merge(
                        $res,
                        $this->processRecipientEmails($bean, $entry, $flowData)
                    );
                    break;
                case 'EMAIL':
                    $res = array_merge(
                        $res,
                        $this->processDirectEmails($bean, $entry, $flowData)
                    );
                    break;
            }
        }

        return $res;
    }

    public function processUserEmails($bean, $entry, $flowData)
    {
        $res = $users = [];

        // Get all the related beans
        $beans = $this->getRelatedModuleObject()->getChainedRelationshipBeans([$bean], $entry);

        foreach ($beans as $b) {
            switch ($entry->value) {
                case 'last_modifier':
                    $users[] = $this->getLastModifier($b);
                    break;
                case 'record_creator':
                    $users[] = $this->getRecordCreator($b);
                    break;
                case 'is_assignee':
                    $users[] = $this->getCurrentAssignee($b);
                    break;
            }
        }
        foreach ($users as $user) {
            $res = array_merge($res, $this->getUserEmails($user, $entry));
        }
        return $res;
    }

    /**
     * Get the contact bean associated with the given ID
     *
     * @param $contactId String ID of the contact, or a variable user ID
     * @param $targetBean SugarBean target bean of the process
     * @return SugarBean|null
     */
    protected function getContactBeanFromTarget($contactId, $targetBean)
    {
        switch ($contactId) {
            case 'system_email':
                return null;
            case 'created_by':
                return $this->getRecordCreator($targetBean);
            case 'currentuser':
                return $this->getCurrentUser();
            case 'modified_user_id':
                return $this->getLastModifier($targetBean);
            case 'owner':
                return $this->getCurrentAssignee($targetBean);
            case 'supervisor':
                $userBean = $this->getCurrentAssignee($targetBean);
                return $this->getSupervisor($userBean);
        }

        return $this->getContactBeanFromId($contactId);
    }

    /**
     * @return SugarBean of the current user
     */
    public function getCurrentUser()
    {
        $userHandler = ProcessManager\Factory::getPMSEObject('PMSEUserAssignmentHandler');
        $currentUserId = $userHandler->getCurrentUserId();
        $userBean = $this->retrieveBean('Users', $currentUserId);
        return $userBean;
    }

    public function getCurrentAssignee($bean)
    {
        $userBean = $this->retrieveBean('Users', $bean->assigned_user_id);
        return $userBean;
    }

    public function getRecordCreator($bean)
    {
        $userBean = $this->retrieveBean('Users', $bean->created_by);
        return $userBean;
    }

    public function getLastModifier($bean)
    {
        $userBean = $this->retrieveBean('Users', $bean->modified_user_id);
        return $userBean;
    }

    /**
     * Checks if a User bean is for an active user
     * @param $userBean
     * @return bool
     */
    public function isUserActiveForEmail(User $userBean)
    {
        // Emails should only be sent when Employee Status is Active AND User Status is Active
        return PMSEEngineUtils::isUserActive($userBean) && !empty($userBean->full_name) && !empty($userBean->email1);
    }

    public function getUserEmails($userBean, $entry)
    {
        $res = [];
        $user = $userBean;
        if ($entry->user === 'manager_of') {
            $user = $this->getSupervisor($userBean);
        }

        if (isset($user) && $this->isUserActiveForEmail($user)) {
            $item = new stdClass();
            $item->name = $user->full_name;
            $item->address = $user->email1;
            $res[] = $item;
        }
        return $res;
    }

    public function getSupervisor($user)
    {
        if (isset($user->reports_to_id) && $user->reports_to_id != '') {
            $supervisor = $this->retrieveBean('Users', $user->reports_to_id);
            if (isset($supervisor->full_name) &&
                !empty($supervisor->full_name) &&
                isset($supervisor->email1) &&
                !empty($supervisor->email1)
            ) {
                return $supervisor;
            } else {
                return null;
            }
        }
    }

    public function processTeamEmails($bean, $entry, $flowData)
    {
        $res = [];
        $teams = [];
        if ($entry->value === 'assigned_teams') {
            if (!empty($bean->team_set_id)) {
                $teams = BeanFactory::newBean('TeamSets')->getTeams($bean->team_set_id);
            }
        } else {
            $teams[] = $this->retrieveBean('Teams', $entry->value);
        }
        foreach ($teams as $team) {
            $members = $team->get_team_members();
            foreach ($members as $user) {
                $userBean = $this->retrieveBean('Users', $user->id);
                if ($this->isUserActiveForEmail($userBean)) {
                    $item = new stdClass();
                    $item->name = $userBean->full_name;
                    $item->address = $userBean->email1;
                    $res[] = $item;
                }
            }
        }
        return $res;
    }

    public function processRoleEmails($bean, $entry, $flowData)
    {
        $res = [];
        if ($entry->value === 'is_admin') {
            $userList = $this->getAdminBeans();
        } else {
            $role = $this->retrieveBean('ACLRoles', $entry->value);
            $userList = $role->get_linked_beans('users', 'User');
        }

        foreach ($userList as $user) {
            if ($this->isUserActiveForEmail($user)) {
                $item = new stdClass();
                $item->name = $user->full_name;
                $item->address = $user->email1;
                $res[] = $item;
            }
        }
        return $res;
    }

    /**
     * Return list of Admin Beans
     *
     * @return array
     * @throws SugarQueryException
     */
    private function getAdminBeans(): array
    {
        $query = new SugarQuery();

        $query
            ->from(BeanFactory::newBean('Users'))
            ->where()
            ->equals('status', 'Active')
            ->equals('is_admin', '1');
        $data = $query->execute();

        $userList = [];
        foreach ($data as $row) {
            $bean = BeanFactory::newBean('Users');
            $bean->populateFromRow($row);
            $bean->populateFetchedEmail('bean_field');
            $bean->emailAddress->handleLegacyRetrieve($bean);
            $userList[] = $bean;
        }

        return $userList;
    }

    public function processRecipientEmails($bean, $entry, $flowData)
    {
        $res = [];
        $field = $entry->value;

        $beans = $this->getRelatedModuleObject()->getChainedRelationshipBeans([$bean], $entry);

        foreach ($beans as $b) {
            if (!empty($b->$field)) {
                $item = new stdClass();
                $item->name = $b->$field;
                $item->address = $b->$field;
                $res[] = $item;
            }
        }
        return $res;
    }

    public function processDirectEmails($bean, $entry, $flowData)
    {
        $res = [];
        $item = new stdClass();
        if (isset($entry->id)) {
            if (isset($entry->module)) {
                $recipientBean = $this->retrieveBean($entry->module, $entry->id);
            } else {
                // try getting the bean we need to do this for backward compatibility when module
                // value isn't set when process definition was created
                $modules = ['Users', 'Contacts', 'Leads', 'Prospects', 'Accounts'];
                foreach ($modules as $module) {
                    if (!empty($recipientBean = $this->retrieveBean($module, $entry->id, ['strict_retrieve' => true]))) {
                        break;
                    }
                }
            }

            if (!empty($recipientBean)) {
                $item->name = $recipientBean->full_name ?? $recipientBean->name;
                $item->address = $recipientBean->email1;
                $res[] = $item;
            } else {
                LoggerManager::getLogger()->warn("Could not find a record bean for the given id {$entry->id}");
            }
        } else {
            // for typed-in emails
            $item->name = $entry->value;
            $item->address = $entry->value;
            $res[] = $item;
        }

        return $res;
    }

    /**
     * Returns a Mailer object
     *
     * @param OutboundEmailConfiguration $config
     * @return mixed
     */
    protected function retrieveMailer(OutboundEmailConfiguration $config = null)
    {
        if (!empty($config)) {
            return MailerFactory::getMailer($config);
        }
        return MailerFactory::getSystemDefaultMailer();
    }

    /**
     * Get the OutboundEmailConfig
     *
     * @param pmse_EmailMessage $email
     * @return null|OutboundEmailConfiguration
     * @throws MailerException
     * @throws SugarApiExceptionNotFound
     */
    protected function getMailerConfig(pmse_EmailMessage $email)
    {
        $config = null;
        if (!empty($email->outbound_email_id)) {
            $outboundEmail = BeanFactory::getBean('OutboundEmail', $email->outbound_email_id);
            if (!empty($outboundEmail->id) && $outboundEmail->isConfigured()) {
                $config = OutboundEmailConfigurationPeer::buildOutboundEmailConfiguration(
                    $GLOBALS['current_user'],
                    [
                        'config_id' => $outboundEmail->id,
                        'config_type' => $outboundEmail->type,
                        'from_email' => $outboundEmail->email_address,
                        'from_name' => $outboundEmail->name,
                        'replyto_email' => $outboundEmail->reply_to_email_address,
                        'replyto_name' => $outboundEmail->reply_to_name,
                    ],
                    $outboundEmail
                );
            }
        }

        if (empty($config)) {
            $config = OutboundEmailConfigurationPeer::getSystemDefaultMailConfiguration();
            if (!empty($email->from_addr)) {
                $config->setFrom($email->from_addr, $email->from_name);
            }
            if (!empty($email->reply_to_addr)) {
                $config->setReplyTo($email->reply_to_addr, $email->reply_to_name);
            }
        }
        return $config;
    }

    /**
     * Send the email based in an email template and with the email data parsed.
     * @param type $moduleName
     * @param type $beanId
     * @param type $addresses
     * @param type $templateId
     * @param type $evnDefBean
     * @return type
     */
    public function sendTemplateEmail($moduleName, $beanId, $addresses, $templateId, $evnDefBean = null)
    {
        $mailTransmissionProtocol = 'unknown';
        if (PMSEEngineUtils::isEmailRecipientEmpty($addresses)) {
            $this->getLogger()->alert('All email recipients are filtered out of the email recipient list.');
            return;
        }
        try {
            $bean = $this->retrieveBean($moduleName, $beanId);
            $templateObject = $this->retrieveBean('pmse_Emails_Templates');
            $templateObject->disable_row_level_security = true;

            $mailObject = $this->retrieveMailer();
            $mailTransmissionProtocol = $mailObject->getMailTransmissionProtocol();

            $this->addRecipients($mailObject, $addresses);

            if (isset($templateId) && $templateId != '') {
                $templateObject->retrieve($templateId);
            } else {
                $this->getLogger()->warning('template_id is not defined');
            }

            if (!empty($templateObject->from_name) && !empty($templateObject->from_address)) {
                $mailObject->setHeader(EmailHeaders::From, new EmailIdentity($templateObject->from_address, $templateObject->from_name));
            }

            $sender = $this->getSenderFromEventDefinition($evnDefBean, $bean);
            $this->setEmailHeaders($mailObject, $sender);

            $emailBody = $this->getEmailBody($templateObject, $bean);
            $mailObject->setHtmlBody($emailBody['htmlBody']);
            $mailObject->setTextBody($emailBody['textBody']);

            $mailObject->setSubject($this->getSubject($templateObject, $bean));

            $mailObject->send();
        } catch (MailerException $mailerException) {
            $message = $mailerException->getMessage();
            $this->getLogger()->warning("Error sending email (method: {$mailTransmissionProtocol}), (error: {$message})");
        }
    }

    /**
     * Save the email's content to the DB and then add it to the job queue to send later.
     * Called by SendMessageEvent and EndSendMessageEvents.
     *
     * @param $flowData
     */
    public function queueEmail($flowData)
    {
        $id = $this->saveEmailContent($flowData);
        $this->addIdToEmailQueue($id);
    }

    /**
     * Save the email's content to the DB and then add it to the job queue to send later.
     * Called by UserActivity.
     *
     * @param $flowData
     * @param $actDefBean
     * @param $userId
     */
    public function queueActivityEmail($flowData, $actDefBean, $userId)
    {
        $id = $this->saveActivityEmailContent($flowData, $actDefBean, $userId);
        $this->addIdToEmailQueue($id);
    }

    public function addIdToEmailQueue($id)
    {
        if (isset($id)) {
            $this->addEmailToQueue($id);
        } else {
            $this->getLogger()->warning('Unable to queue email for flow id ' . $id);
        }
    }

    /**
     * Add pmse_EmailMessage ID to the job queue to send the email through the job queue
     *
     * @param $id The ID of the pmse_EmailMessage bean
     */
    public function addEmailToQueue($id)
    {
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = 'SugarBPM Email Queue';
        $job->target = 'class::SugarJobSendAWFEmail';
        $job->data = json_encode(['id' => $id]);

        $jq = new SugarJobQueue();
        $jq->submitJob($job);
    }

    /**
     * Get email from pmse_email_message table
     *
     * @param string $id ID of the pmse_EmailMessage bean
     * @return SugarBean
     */
    public function getQueuedEmail($id)
    {
        return BeanFactory::getBean('pmse_EmailMessage', $id);
    }

    /**
     * Send email using an array of email values
     * @param pmse_EmailMessage $email
     * @return bool `true` if email is sent
     */
    public function sendEmailFromQueue(pmse_EmailMessage $email)
    {
        if (empty($email) || empty($email->id)) {
            $this->getLogger()->warning(
                'Error sending email. Email data not found'
            );
            return false;
        }

        $mailObject = $this->retrieveMailer($this->getMailerConfig($email));
        $mailTransmissionProtocol = $mailObject->getMailTransmissionProtocol();

        $addresses = new stdClass();
        $addresses->to = json_decode($email->to_addrs);
        $addresses->cc = json_decode($email->cc_addrs);
        $addresses->bcc = json_decode($email->bcc_addrs);

        $this->addRecipients($mailObject, $addresses);
        $mailObject->setHtmlBody($email->body_html);
        $mailObject->setTextBody($email->body);
        $mailObject->setSubject($email->subject);

        // if we need to attach a generated document
        if ($email->doc_merge_attach_id) {
            $document = BeanFactory::retrieveBean('Documents', $email->doc_merge_attach_id);
            $documentRevisionId = $document->document_revision_id;

            $fileLocation = "upload://{$documentRevisionId}";
            $filename = $document->filename;
            $mimeType = $document->file_mime_type;

            if ($document) {
                $mailObject->addAttachment(new Attachment($fileLocation, $filename, Encoding::Base64, $mimeType));
            }
        }

        try {
            $mailObject->send();
            return true;
        } catch (MailerException $mailerException) {
            $message = $mailerException->getMessage();
            $this->getLogger()->warning(
                "Error sending email (method: {$mailTransmissionProtocol}), (error: {$message})"
            );
            return false;
        }
    }

    /**
     * Sets the "From" and/or "Reply To" headers of the given email object using the given sender data. Each
     * header is set only if both its name and address data are specified in the sender data.
     *
     * @param $mailObject the email object
     * @param $sender array containing 'from'
     */
    public function setEmailHeaders($mailObject, $sender)
    {
        $this->setEmailHeader($mailObject, $sender, 'from', EmailHeaders::From);
        $this->setEmailHeader($mailObject, $sender, 'reply', EmailHeaders::ReplyTo);
    }

    /**
     * Sets the given header type of the given email object to the given sender information
     * @param $mailObject the email object
     * @param $sender array containing the 'from' and 'reply' subarrays, each with 'name' and 'address'
     * @param $senderType 'from' or 'reply'
     * @param $headerType EmailHeaders the type of email header to set
     */
    public function setEmailHeader($mailObject, $sender, $senderType, $headerType)
    {
        if (!empty($sender[$senderType]['address']) && !empty($sender[$senderType]['name'])) {
            $senderObject = new EmailIdentity($sender[$senderType]['address'], $sender[$senderType]['name']);
            $mailObject->setHeader($headerType, $senderObject);
        }
    }

    /**
     * Save email to pmse_email_message table with runtime values.
     *
     * @param array $flowData
     * @return string|null mixed
     */
    public function saveEmailContent($flowData)
    {
        $this->setFlowData($flowData);
        $beans = $this->getBeansForEmailContentSave($flowData);

        if (is_null($beans)) {
            return null;
        }

        [$evnDefBean, $templateBean, $targetBean, $emailMessageBean] = $beans;

        $addresses = $this->getRecipients($evnDefBean, $targetBean, $flowData);
        $sender = $this->getSenderFromEventDefinition($evnDefBean, $targetBean);
        $outboundId = $this->getOutBoundEmailIdFromEventDefinition($evnDefBean, $targetBean);
        return $this->saveEmailBean($targetBean, $templateBean, $sender, $addresses, $emailMessageBean, $outboundId);
    }

    /**
     * Save email to pmse_email_message table with runtime value based on
     * current activity. Called by UserActivity.
     *
     * @param $flowData
     * @param $activityDefinitionBean
     * @param $userId
     * @return string|null
     */
    public function saveActivityEmailContent($flowData, $activityDefinitionBean, $userId)
    {
        $this->setFlowData($flowData);
        $beans = $this->getBeansForActivityEmailSave($flowData, $activityDefinitionBean, $userId);

        if (is_null($beans)) {
            return null;
        }

        [$templateBean, $targetBean, $emailMessageBean, $assignee] = $beans;

        $addresses = new stdClass();
        $addresses->to = $this->getUserEmails($assignee, '');

        if (empty($templateBean) || empty($targetBean) || empty($emailMessageBean)) {
            return null;
        }

        $systemEmail = $this->getContactInformationFromId('system_email', $targetBean);
        $sender = [
            'from' => $systemEmail,
            'reply' => $systemEmail,
        ];

        return $this->saveEmailBean($targetBean, $templateBean, $sender, $addresses, $emailMessageBean, null);
    }

    /**
     * Util method to get beans needed for Activity Emails
     *
     * @param $flowData
     * @param $activityDefinitionBean
     * @param $userId
     * @return array
     */
    public function getBeansForActivityEmailSave($flowData, $activityDefinitionBean, $userId)
    {
        $beans = $this->getBeansForEmailSave($flowData, $activityDefinitionBean->act_email_template_id);
        $assignee = BeanFactory::retrieveBean('Users', $userId);
        if (is_null($beans) || is_null($assignee)) {
            return null;
        }
        array_push($beans, $assignee);
        return $beans;
    }

    /**
     * @param SugarBean $targetBean
     * @param SugarBean $templateBean
     * @param array|User $sender
     * @param stdClass $addresses
     * @param SugarBean $emailMessageBean
     * @param string $outboundId
     * @return string
     */
    public function saveEmailBean($targetBean, $templateBean, $sender, $addresses, $emailMessageBean, $outboundId)
    {
        if (PMSEEngineUtils::isEmailRecipientEmpty($addresses)) {
            $this->getLogger()->alert('All email recipients are filtered out of the email recipient list.');
            return null;
        }

        foreach ($addresses as $recipientType => $emailAddresses) {
            $emailMessageBean->{$recipientType . '_addrs'} = json_encode($emailAddresses);
        }

        $emailBody = $this->getEmailbody($templateBean, $targetBean);
        $emailMessageBean->body = $emailBody['textBody'];
        $emailMessageBean->body_html = $emailBody['htmlBody'];

        $emailMessageBean->subject = $this->getSubject($templateBean, $targetBean);

        $emailMessageBean->from_addr = !empty($sender['from']['address']) ? $sender['from']['address'] : null;
        $emailMessageBean->from_name = !empty($sender['from']['name']) ? $sender['from']['name'] : null;
        $emailMessageBean->reply_to_addr = !empty($sender['reply']['address']) ? $sender['reply']['address'] : null;
        $emailMessageBean->reply_to_name = !empty($sender['reply']['name']) ? $sender['reply']['name'] : null;
        $emailMessageBean->outbound_email_id = $outboundId;
        $emailMessageBean->flow_id = $this->flowData['id'];

        /**
         * if the message is triggered from a doc_merge action
         * make sure we setup the attachement id
         */
        if (isset($this->flowData)
            && isset($this->flowData['doc_merge_attach_id'])) {
            $emailMessageBean->doc_merge_attach_id = $this->flowData['doc_merge_attach_id'];
        }

        return $emailMessageBean->save();
    }

    /**
     * Helper method to get all the beans required for saveEmailContent
     * @param array $flowData
     * @return array|null All the beans needed to save
     */
    protected function getBeansForEmailContentSave($flowData)
    {
        $evnDefBean = BeanFactory::retrieveBean('pmse_BpmEventDefinition', $flowData['bpmn_id']);
        $beans = $this->getBeansForEmailSave($flowData, $evnDefBean->evn_criteria);
        if (is_null($beans) || is_null($evnDefBean)) {
            return null;
        }
        array_unshift($beans, $evnDefBean);
        return $beans;
    }

    /**
     * Util method for retrieving Email Template, Target Module, and Email Message
     * beans as those are used by both Activity and Event emails
     * @param $flowData
     * @param $emailTemplateId
     * @return array|null
     */
    protected function getBeansForEmailSave($flowData, $emailTemplateId)
    {
        $templateBean = BeanFactory::retrieveBean('pmse_Emails_Templates', $emailTemplateId);
        $targetBean = BeanFactory::retrieveBean($flowData['cas_sugar_module'], $flowData['cas_sugar_object_id']);
        $emailMessageBean = BeanFactory::newBean('pmse_EmailMessage');

        if (is_null($templateBean)) {
            $this->getLogger()->warning('Email Template not found. Unable to save email');
            return null;
        }

        if (is_null($targetBean) || empty($targetBean->id)) {
            $this->getLogger()->warning('Target Bean not found. Unable to save email');
            return null;
        }
        return [$templateBean, $targetBean, $emailMessageBean];
    }

    /**
     * Get the recipients for the email
     *
     * @param SugarBean $eventDefinitionBean Bean containing the event definition
     * @param SugarBean $targetBean Target module bean
     * @param array $flowData Flow Data
     * @return StdClass
     */
    public function getRecipients($eventDefinitionBean, $targetBean, $flowData)
    {
        $json = htmlspecialchars_decode($eventDefinitionBean->evn_params, ENT_COMPAT);
        return $this->processEmailsFromJson($targetBean, $json, $flowData);
    }

    /**
     * Get the email body (html and text) from the template
     *
     * @param SugarBean $templateBean Email template bean
     * @param SugarBean $targetBean Target module bean
     * @return array
     */
    public function getEmailBody($templateBean, $targetBean)
    {
        if (empty($templateBean->body) && !empty($templateBean->body_html)) {
            $templateBean->body = $templateBean->body_html;
        }

        // We should hit this condition almost every time so let's save some processing
        // by only merging the bean into the template once
        if ($templateBean->body === $templateBean->body_html) {
            $mergedHtmlContent = $mergedTextContent
                = $this->mergeBeanContentIntoTemplate($templateBean->body_html, $targetBean);
        } else {
            // Edge case when body is different from html body
            // Should never happen unless someone has customized or made their own API calls
            $mergedHtmlContent = $this->mergeBeanContentIntoTemplate($templateBean->body_html, $targetBean);
            $mergedTextContent = $this->mergeBeanContentIntoTemplate($templateBean->body, $targetBean);
        }

        return [
            'htmlBody' => $this->getHtmlEmailBody($mergedHtmlContent),
            'textBody' => $this->getTextEmailBody($mergedTextContent),
        ];
    }

    /**
     * Merge bean info into the html body
     *
     * @param $content
     * @return null|string
     */
    private function getHtmlEmailBody($content)
    {
        if (!empty($content)) {
            $textOnly = EmailFormatter::isTextOnly($content);
            if (!$textOnly) {
                return $this->fromHtml($content);
            }
        }

        $this->getLogger()->warning('Process Email Template body_html is not defined');
        return null;
    }

    /**
     * Merge bean info into text body
     *
     * @param string $content
     * @return null|string
     */
    private function getTextEmailBody($content)
    {
        if (!empty($content)) {
            return $this->getTextFromHtml($content);
        }

        $this->getLogger()->warning('Process Email Template body is not defined');
        return null;
    }

    /**
     * Get the subject from the template bean
     *
     * @param SugarBean $templateBean Email template bean
     * @param SugarBean $targetBean Target module bean
     * @return null|string
     */
    public function getSubject($templateBean, $targetBean)
    {
        if (!empty($templateBean->subject)) {
            $mergedContent = $this->mergeBeanContentIntoTemplate($templateBean->subject, $targetBean);
            return $this->getTextFromHtml($mergedContent);
        }

        $this->getLogger()->warning('template subject is not defined');
        return null;
    }

    /**
     * Get the From name and email address from the email template
     *
     * @param SugarBean $templateBean Email template bean
     * @return array|null
     */
    public function getSender($templateBean)
    {
        if (!empty($templateBean->from_name) && !empty($templateBean->from_address)) {
            return [
                'address' => $templateBean->from_address,
                'name' => $templateBean->from_name,
            ];
        }

        return null;
    }

    /**
     * Gets sender contact information (name, email address, reply to name,
     * reply to email address) from the event definition bean
     *
     * @param SugarBean $evnDefBean The event definition bean
     * @param SugarBean $targetBean The target bean of the process
     * @return array containing the sender contact information
     */
    public function getSenderFromEventDefinition($evnDefBean, $targetBean)
    {
        $sender = [];
        if (!empty($evnDefBean->evn_params)) {
            $addressesJSON = htmlspecialchars_decode($evnDefBean->evn_params, ENT_COMPAT);
            $addresses = json_decode($addressesJSON);
            $fromId = !empty($addresses->from->id) ? $addresses->from->id : null;
            $replyToId = !empty($addresses->replyTo->id) ? $addresses->replyTo->id : null;
            // save from and reply id
            $sender['from'] = $this->getContactInformationFromId($fromId, $targetBean, 'from');
            $sender['reply'] = $this->getContactInformationFromId($replyToId, $targetBean, 'reply');
        }
        return $sender;
    }

    /**
     * Extract the id for the From field from the event definition
     *
     * @param SugarBean $evnDefBean The event definition bean
     * @param SugarBean $targetBean The target bean of the process
     * @return string|null The outbound email id if it exists
     */
    public function getOutBoundEmailIdFromEventDefinition($evnDefBean, $targetBean)
    {
        if (empty($evnDefBean->evn_params)) {
            return null;
        }

        $addressesJSON = htmlspecialchars_decode($evnDefBean->evn_params, ENT_COMPAT);
        $addresses = json_decode($addressesJSON);
        $fromId = $addresses->from->id ?? null;
        if (!$fromId) {
            return null;
        }

        // Check if this is an outbound account
        $bean = $this->retrieveBean('OutboundEmail', $fromId);
        if (empty($bean->id)) {
            // if not, check if contact (User or OutboundEmail) has an outbound account
            $contactBean = $this->getContactBeanFromTarget($fromId, $targetBean);
            if (!empty($contactBean->id)) {
                $bean = $contactBean->getModuleName() === 'Users' ?
                    $this->getUserConfiguredOutboundEmailBean($contactBean->id) :
                    $contactBean;
            }
        }

        return $bean->id ?? null;
    }

    /**
     * Get a configured outbound email bean for a user
     *
     * @param string $userId
     * @return SugarBean|null
     */
    protected function getUserConfiguredOutboundEmailBean($userId)
    {
        $ob = BeanFactory::newBean('OutboundEmail');
        $bean = $ob->getUsersMailerForSystemOverride($userId);
        return $bean && $bean->isConfigured() ? $bean : null;
    }

    /**
     * Gets the contact information (email address and name) associated with the
     * given ID
     *
     * @param $contactId String ID of the contact, or a variable user ID
     * @param $targetBean SugarBean target bean of the process
     * @param string $type The value we want to set on the email. Supports `from` and `reply`
     * @return array
     */
    public function getContactInformationFromId($contactId, $targetBean, string $type = '')
    {
        if ($contactId === 'system_email') {
            // The system email account (defined in Admin settings)
            // is the fallback account when 'name' or 'address' are null
            return [
                'name' => null,
                'address' => null,
            ];
        }

        $contactBean = $this->getContactBeanFromTarget($contactId, $targetBean);

        // Return the name and primary email address associated with the contact bean
        return $this->getContactInformationFromBean($contactBean, $type);
    }

    /**
     * Gets the bean for a contact by its ID. Returns null if no contact is
     * found
     *
     * @param $contactId String ID of the contact
     * @return null|SugarBean
     */
    public function getContactBeanFromId($contactId)
    {
        ProcessManager\Registry\Registry::getInstance()->set('bpm_request', true, true);
        $bean = $this->retrieveBean('OutboundEmail', $contactId);
        if (empty($bean->id)) {
            // Get the User bean for backwards compatibility with 9.1
            $bean = $this->retrieveBean('Users', $contactId);
        }

        ProcessManager\Registry\Registry::getInstance()->set('bpm_request', false, true);

        return !empty($bean->id) ? $bean : null;
    }

    /**
     * Retrieves the contact information (name and primary email address) associated
     * with the given bean
     *
     * @param Sugarbean|null $contactBean to extract contact information from
     * @param string $type The value we want to set on the email. Supports `from` and `reply`
     * @return array containing the contact name and primary email address of the user
     */
    public function getContactInformationFromBean(SugarBean $contactBean = null, string $type = '')
    {
        $default = [
            'name' => null,
            'address' => null,
        ];
        if (empty($contactBean->id)) {
            return $default;
        }

        if ($contactBean->getModuleName() === 'Users') {
            return $this->getContactInfoFromUser($contactBean, $type);
        } elseif ($contactBean->getModuleName() === 'OutboundEmail') {
            return $this->getContactInfoFromOutboundEmail($contactBean, $type);
        }

        return $default;
    }

    /**
     * Returns the name and address fields for a User bean
     * depending on what the email field we need to set
     *
     * @param User $user The User bean to get info from
     * @param string $type The value we want to set on the email. Supports `from` and `reply`
     * @return array name and address fields
     */
    private function getContactInfoFromUser(User $user, string $type)
    {
        switch ($type) {
            case 'from':
                $info = [
                    'name' => !empty($user->full_name) ? $user->full_name : null,
                    'address' => !empty($user->email1) ? $user->email1 : null,
                ];
                break;
            case 'reply':
                $replyToAddress = $user->emailAddress->getReplyToAddress($user, true);
                $info = [
                    'name' => !empty($user->full_name) ? $user->full_name : null,
                    'address' => !empty($replyToAddress) ? $replyToAddress :
                        (!empty($user->email1) ? $user->email1 : null),
                ];
                break;
            default:
                $info = [
                    'name' => null,
                    'address' => null,
                ];
        }
        return $info;
    }

    /**
     * Returns the name and address fields for a OutboundEmail bean
     * depending on what the email field we need to set
     *
     * @param OutboundEmail $outboundEmail The OutboundEmail bean to get info from
     * @param string $type The value we want to set on the email. Supports `from` and `reply`
     * @return array name and address fields
     */
    private function getContactInfoFromOutboundEmail(OutboundEmail $outboundEmail, string $type)
    {
        switch ($type) {
            case 'from':
                $info = [
                    'name' => !empty($outboundEmail->name) ? $outboundEmail->name : null,
                    'address' => !empty($outboundEmail->email_address) ? $outboundEmail->email_address : null,
                ];
                break;
            case 'reply':
                $info = [
                    'name' => !empty($outboundEmail->reply_to_name) ? $outboundEmail->reply_to_name : null,
                    'address' =>
                        !empty($outboundEmail->reply_to_email_address) ? $outboundEmail->reply_to_email_address : null,
                ];
                break;
            default:
                $info = [
                    'name' => null,
                    'address' => null,
                ];
        }
        return $info;
    }

    /**
     * Merge any variables from the bean into the template
     *
     * @param string $content
     * @param SugarBean $targetBean Target module bean
     * @return null|string
     */
    private function mergeBeanContentIntoTemplate($content, $targetBean)
    {
        if (!empty($content)) {
            $beanUtils = $this->getBeanUtils();
            $beanUtils->setFlowData($this->flowData);
            return $beanUtils->mergeBeanInTemplate($targetBean, $content, true);
        }

        return null;
    }

    /**
     * Wrapper for db_utils.php from_html function
     *
     * @param string $text
     * @return string
     */
    protected function fromHtml($text)
    {
        return from_html($text);
    }

    /**
     * Clean out any HTML content from the content
     *
     * @param string $content
     * @return string
     */
    private function getTextFromHtml($content)
    {
        return $this->fromHtml(strip_tags(br2nl($content)));
    }

    /**
     * Add receipients to Mailer object in preparation to sending email
     * @param $mailObject Mailer object
     * @param $addresses To, CC & BCC Email addresses
     */
    protected function addRecipients($mailObject, $addresses)
    {
        foreach (['to', 'cc', 'bcc'] as $type) {
            if (isset($addresses->{$type})) {
                $method = 'addRecipients' . ucfirst($type);
                foreach ($addresses->{$type} as $key => $email) {
                    $mailObject->{$method}(new EmailIdentity($email->address, $email->name));
                }
            }
        }
    }

    /**
     * Checks if the primary email address exists
     * @param type $field
     * @param type $bean
     * @param type $historyData
     * @return boolean
     */
    public function doesPrimaryEmailExists($field, $bean, $historyData)
    {
        if ($field->field == 'email_addresses_primary') {
            $preEmail = $bean->emailAddress->getPrimaryAddress('', $bean->id, $bean->module_dir);
            if (empty($preEmail)) {
                //is a new record, it hasn't any email in DB yet
                $emailKey = $this->getPrimaryEmailKeyFromREQUEST($bean);
                if (isset($historyData)) {
                    $historyData->savePredata($field->field, $_REQUEST[$emailKey]);
                }
                $_REQUEST[$emailKey] = $field->value;
            } else {
                //the record exist in db
                if (isset($historyData)) {
                    $historyData->savePredata($field->field, $preEmail);
                }
                $this->updateEmails($bean, $field->value);
            }
            return true;
        }
        return false;
    }

    /**
     * Get the primary Key from a request in order to obtain the email id
     * @param type $bean
     * @return type
     */
    public function getPrimaryEmailKeyFromREQUEST($bean)
    {
        $module = $bean->module_dir;
        $widgetCount = 0;
        $moduleItem = '0';

        $widget_id = '';
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'emailAddress') !== false) {
                break;
            }
            $widget_id = $_REQUEST[$module . '_email_widget_id'];
        }

        while (isset($_REQUEST[$module . $widget_id . 'emailAddress' . $widgetCount])) {
            if (empty($_REQUEST[$module . $widget_id . 'emailAddress' . $widgetCount])) {
                $widgetCount++;
                continue;
            }

            $primaryValue = false;

            $eId = $module . $widget_id;
            if (isset($_REQUEST[$eId . 'emailAddressPrimaryFlag'])) {
                $primaryValue = $_REQUEST[$eId . 'emailAddressPrimaryFlag'];
            } elseif (isset($_REQUEST[$module . 'emailAddressPrimaryFlag'])) {
                $primaryValue = $_REQUEST[$module . 'emailAddressPrimaryFlag'];
            }

            if ($primaryValue) {
                return $eId . 'emailAddress' . $widgetCount;
            }
            $widgetCount++;
        }
        $_REQUEST[$bean->module_dir . '_email_widget_id'] = 0;
        $_REQUEST['emailAddressWidget'] = 1;
        $_REQUEST['useEmailWidget'] = true;
        $emailId = $bean->module_dir . $moduleItem . 'emailAddress';
        $_REQUEST[$emailId . 'PrimaryFlag'] = $emailId . $moduleItem;
        $_REQUEST[$emailId . 'VerifiedFlag' . $moduleItem] = true;
        //$_REQUEST[$emailId . 'VerifiedValue' . $moduleItem] = $myemail;

        return $emailId . $moduleItem;
    }

    /**
     * Update the email data in the REQUEST global object
     * @param type $bean
     * @param type $newEmailAddress
     */
    public function updateEmails($bean, $newEmailAddress)
    {
        //Note.- in the future will be an 'array' of change fields emails
        $moduleItem = '0';
        $addresses = $bean->emailAddress->getAddressesByGUID($bean->id, $bean->module_dir);
        if (sizeof($addresses) > 0) {
            $_REQUEST[$bean->module_dir . '_email_widget_id'] = 0;
            $_REQUEST['emailAddressWidget'] = 1;
            $_REQUEST['useEmailWidget'] = true;
        }
        foreach ($addresses as $item => $data) {
            if (!isset($data['email_address_id']) || !isset($data['primary_address'])) {
                $this->getLogger()->error(' The Email address Id or the primary address flag does not exist in DB');
                continue;
            }
            $emailAddressId = $data['email_address_id'];
            $emailId = $bean->module_dir . $moduleItem . 'emailAddress';
            if (!empty($emailAddressId) && $data['primary_address'] == 1) {
                $_REQUEST[$emailId . 'PrimaryFlag'] = $emailId . $item;
                $_REQUEST[$emailId . $item] = $newEmailAddress;
            } else {
                $_REQUEST[$emailId . $item] = $data['email_address'];
            }
            $_REQUEST[$emailId . 'Id' . $item] = $emailAddressId;
            $_REQUEST[$emailId . 'VerifiedFlag' . $item] = true;
            $_REQUEST[$emailId . 'VerifiedValue' . $item] = $data['email_address'];
        }
    }

    /**
     * Get flow data for currently executing BPM Flow
     * @return array
     */
    public function getFlowData(): array
    {
        return $this->flowData;
    }

    /**
     * Set flow data
     * @param array $flowData
     */
    public function setFlowData(array $flowData): void
    {
        $this->flowData = $flowData;
    }
}
