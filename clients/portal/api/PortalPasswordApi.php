<?php

declare(strict_types=1);

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

use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\Security\Password\Utilities;

/**
 * Class PortalPasswordApi
 *
 * Sends email to reset portal password
 */
class PortalPasswordApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'resetEmailPortalPassword' => [
                'reqType' => 'GET',
                'path' => ['password', 'resetemail'],
                'pathVars' => [],
                'method' => 'resetEmailPortalPassword',
                'shortHelp' => 'This method sends email requests to reset passwords for Portal users',
                'longHelp' => 'include/api/help/portal_password_reset_email_get_help.html',
                'noLoginRequired' => true,
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.6',
            ],
            'resetPortalPassword' => [
                'reqType' => 'PUT',
                'path' => ['portal_password', 'reset'],
                'pathVars' => [],
                'method' => 'resetPassword',
                'shortHelp' => 'This method resets the password of a portal user associated with a password reset token',
                'longHelp' => 'include/api/help/portal_password_reset_put_help.html',
                'noLoginRequired' => true,
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.6',
            ],
        ];
    }

    /**
     * Creates url and sends email to user to reset password for Portal
     * @param ServiceBase $api
     * @param array $args
     * @return bool
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionMissingParameter
     */
    public function resetEmailPortalPassword(ServiceBase $api, array $args): bool
    {
        $this->requireArgs($args, ['username']);

        $contactBean = $this->getBean('Contacts');
        $contactBean->disable_row_level_security = true;

        // get contact's id
        $query = $this->getSugarQuery();
        $query->select(['id']);
        $query->from($contactBean);
        $query->where()->equals('portal_name', $args['username']);

        $row = $query->getOne();

        if (!empty($row)) {
            $contactBean->retrieve($row);

            // get the password template
            $pwdSetting = $this->getConfigValue('portalpasswordsetting');
            $templateID = $pwdSetting['lostpasswordtmpl'];
            $platform = $api->platform;

            return $this->sendEmail($templateID, $contactBean, $platform);
        }

        return false;
    }

    /**
     * Wrapper to get a new SugarBean
     *
     * @param string $module The module name
     * @return null|SugarBean
     * @throws SugarApiExceptionNotFound
     */
    public function getBean(string $module): SugarBean
    {
        return BeanFactory::getBean($module);
    }

    /**
     * Wrapper to get a new SugarQuery
     *
     * @return SugarQuery
     */
    public function getSugarQuery(): SugarQuery
    {
        return new SugarQuery();
    }


    /**
     * Returns values for attributes from sugar config
     * @param string $key Sugar config attribute
     * @return mixed
     */
    public function getConfigValue(string $key)
    {
        return getValueFromConfig($key);
    }

    /**
     * Creates reset url and saves to db
     * @param SugarBean $contactBean
     * @return string|bool on failure
     */
    private function createResetLink(SugarBean $contactBean, string $platform)
    {
        $guid = Uuid::uuid1();

        // create a url with new guid
        $url = prependSiteURL('/portal/#resetpassword/' . $guid);
        $values = [
            'guid' => $guid,
            'bean_id' => $contactBean->id,
            'bean_type' => $contactBean->module_name,
            'name' => $contactBean->portal_name,
            'platform' => $platform,
        ];

        if (!empty(Utilities::insertIntoUserPwdLink($values))) {
            return $url;
        }

        return false;
    }

    /**
     * Sends link to user. Does not support HTML body due to security reasons.
     * @param string $templateId Email Template id
     * @param SugarBean $contactBean Contact bean who wants reset the password
     * @return bool
     * @throws SugarApiException
     */
    public function sendEmail(string $templateId, SugarBean $contactBean, string $platform): bool
    {
        $mailTransmissionProtocol = null;
        $result = false;

        if (empty($templateId)) {
            LoggerManager::getLogger()->fatal('No Email Template available for Portal Reset Password');
            return $result;
        }

        // get the email template
        $emailTemplate = BeanFactory::getBean('EmailTemplates', $templateId, ['disable_row_level_security' => true]);

        if (empty($emailTemplate->id)) {
            throw new SugarApiException('No Email Template');
        }

        $resetLink = $this->createResetLink($contactBean, $platform);

        if (empty($resetLink)) {
            return $result;
        }
        // replace the placeholder with the actual url
        $emailTemplate->body = str_replace('$portal_user_link_guid', $resetLink, $emailTemplate->body);

        try {
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();

            // set subject
            $mailer->setSubject($emailTemplate->subject);

            // set plain-text body
            $mailer->setTextBody($emailTemplate->body);

            // get recipient's email address
            $emailAdrs = $contactBean->emailAddress->getPrimaryAddress($contactBean);

            if (!empty($emailAdrs)) {
                // add the recipient
                $mailer->addRecipientsTo(new EmailIdentity($emailAdrs, $contactBean->full_name));

                // not a bad idea to set messageID for the Mailer
                $emailId = Uuid::uuid1();
                $mailer->setMessageId($emailId);

                // if send doesn't raise an exception, set the result status to true
                $mailer->send();
                $result = true;
            } else {
                throw new MailerException('There are no recipients', MailerException::FailedToSend);
            }
        } catch (MailerException $me) {
            // throw the exceptions
            $message = $me->getMessage();

            switch ($me->getCode()) {
                case MailerException::FailedToConnectToRemoteServer:
                    LoggerManager::getLogger()->fatal('Email Reminder: error sending email, system smtp server is not set');
                    break;
                default:
                    LoggerManager::getLogger()->fatal('Email Reminder: error sending e-mail (method: ' .
                        $mailTransmissionProtocol . '), (error: ' . $message . ')');
                    break;
            }
            throw new SugarApiException(translate('LBL_PASSWORD_RESET_EMAIL_FAIL'));
        }

        return $result;
    }

    /**
     * Validates and processes a request to reset a contact's portal password
     * @param ServiceBase $api
     * @param array $args
     * @return array containing the ID and date_modified of the matching contact bean
     * @throws SugarApiException
     */
    public function resetPassword(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['newPassword', 'resetID']);

        // Validate the arguments
        if (empty($args['newPassword']) || empty($this->validatePassword($args['newPassword']))) {
            throw new SugarApiExceptionRequestMethodFailure('LBL_PASSWORD_ENFORCE_TITLE');
        }
        if (empty($args['resetID']) || empty($contactID = $this->validateResetToken($args['resetID']))) {
            throw new SugarApiExceptionRequestMethodFailure('LBL_PORTAL_PASSWORD_RESET_ERR_GENERAL');
        }

        // Set the Portal password for the matching contact bean and expire the reset token
        $contactBean = $this->updatePortalPassword($contactID, $args['newPassword']);
        $this->expireResetToken($args['resetID']);

        // If the contact bean was not found, indicate failure
        if (empty($contactBean)) {
            throw new SugarApiExceptionRequestMethodFailure('LBL_PORTAL_PASSWORD_RESET_ERR_GENERAL');
        }
        $this->sendConfirmationEmail($contactBean);

        return [
            'id' => $contactBean->id,
            'date_modified' => $contactBean->date_modified,
        ];
    }

    /**
     * Validates a password entered by a portal user during a password reset
     * @param string $newPassword the string containing the new portal password
     * @return bool true if the password meets requirements; false otherwise
     */
    protected function validatePassword($newPassword): bool
    {
        // Validate that the password meets the minimum requirements, which
        // are the same as the requirements for base users
        $userBean = BeanFactory::newBean('Users');
        return !empty($newPassword) && is_string($newPassword) && $userBean->check_password_rules($newPassword);
    }

    /**
     * Validates a reset token referenced in a portal user password reset request
     * @param string $resetID UID of the reset token
     * @return string|null the string containing the contact bean ID associated
     *         with the reset token, or null if the token is invalid
     */
    protected function validateResetToken($resetID): ?string
    {
        // If the reset token does not exist, indicate failure
        $token = $this->findResetToken($resetID);
        if (empty($token)) {
            return null;
        }

        // If the reset token is overdue, mark it as expired and indicate failure
        $currentTime = TimeDate::getInstance()->nowDb();
        $generatedTime = TimeDate::getInstance()->fromDb($token['date_generated']);
        $expiredTime = $generatedTime
            ->add(new DateInterval('PT30M'))
            ->asDb();
        if ($currentTime > $expiredTime) {
            $this->expireResetToken($resetID);
            return null;
        }

        return $token['bean_id'];
    }

    /**
     * Finds the reset token with the given ID in the users_password_link table
     * @param string $resetID the ID corresponding to a row in the users_password_link table
     * @return mixed the array containing the token information if found; false otherwise
     */
    private function findResetToken($resetID)
    {
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        $qb->select(['bean_id', 'date_generated'])
            ->from('users_password_link')
            ->where($qb->expr()->eq('platform', $qb->createPositionalParameter('portal')))
            ->andWhere($qb->expr()->eq('deleted', $qb->createPositionalParameter(0)))
            ->andWhere($qb->expr()->eq('id', $qb->createPositionalParameter($resetID)))
            ->setMaxResults(1);
        $token = $qb->execute()->fetchAssociative();
        return $token;
    }

    /**
     * Updates the portal password for a contact with the given portal username
     * @param string $contactID the portal username of the contact to update the portal password for
     * @param string $newPassword the new password to set
     * @return Contact|null the contact bean that was updated, or null if no
     *         matching contact was found
     */
    protected function updatePortalPassword($contactID, $newPassword): ?Contact
    {
        $contactBean = BeanFactory::newBean('Contacts');
        $contactBean->disable_row_level_security = true;
        $contactBean->retrieve($contactID);
        if (empty($contactBean->id)) {
            return null;
        }

        $contactBean->portal_password = User::getPasswordHash($newPassword);
        $contactBean->save();
        return $contactBean;
    }

    /**
     * Deletes/expires a reset token
     * @param string $resetID unique ID of the password reset token
     */
    private function expireResetToken($resetID)
    {
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        $qb->update('users_password_link');
        $qb->set('deleted', '1');
        $qb->where($qb->expr()->eq('id', $qb->createPositionalParameter($resetID)));
        $qb->execute();
    }

    /**
     * Builds and sends a confirmation email to a Contact to inform them that
     * their Portal password has been changed
     * @param Contact $contactBean the Contact whose Portal password has been changed
     * @return bool true if successful; false if an error occurred
     */
    protected function sendConfirmationEmail(Contact $contactBean): bool
    {
        // Fail if no primary email address is set for the contact
        $toAddress = $contactBean->emailAddress->getPrimaryAddress($contactBean);
        if (empty($toAddress)) {
            \LoggerManager::getLogger()->fatal('Error sending password reset confirmation email: No recipient email address');
            return false;
        }

        // Get the reset password confirmation email template
        $pwdSetting = \SugarConfig::getInstance()->get('portalpasswordsetting');
        $tplID = $pwdSetting['resetpasswordtmpl'];
        $emailTemplate = BeanFactory::retrieveBean('EmailTemplates', $tplID, ['disable_row_level_security' => true]);
        if (empty($emailTemplate->id)) {
            \LoggerManager::getLogger()->fatal('Error sending password reset confirmation email: No email template found');
            return false;
        }

        // Place the site URL into the template
        $url = \SugarConfig::getInstance()->get('site_url') . '/portal/index.php';
        $emailTemplate->body = str_replace('$portal_login_url', $url, $emailTemplate->body);

        // Try to build and send the email
        try {
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailer->setSubject($emailTemplate->subject);
            $mailer->setTextBody($emailTemplate->body);
            $mailer->addRecipientsTo(new EmailIdentity($toAddress, $contactBean->full_name));
            $mailer->setMessageId(Uuid::uuid1());
            $mailer->send();
        } catch (MailerException $me) {
            $message = $me->getMessage();
            \LoggerManager::getLogger()->fatal('Error sending password reset confirmation email: ' . $message);
            return false;
        }
        return true;
    }
}
