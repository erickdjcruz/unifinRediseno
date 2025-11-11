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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

class OutboundEmailApi extends ModuleApi
{
    /**
     * {@inheritdoc}
     */
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['OutboundEmail'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new OutboundEmail record',
                'longHelp' => 'modules/OutboundEmail/clients/base/api/help/outbound_email_post_help.html',
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['OutboundEmail', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates an OutboundEmail record',
                'longHelp' => 'modules/OutboundEmail/clients/base/api/help/outbound_email_record_put_help.html',
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'testUserSystemOverride' => [
                'reqType' => 'POST',
                'path' => ['OutboundEmail', 'testUserOverride'],
                'pathVars' => ['module', ''],
                'method' => 'testUserSystemOverrideEmail',
                'shortHelp' => 'This method tests the system override email account for a user',
                'longHelp' => 'modules/OutboundEmail/clients/base/api/help/outbound_email_test_user_override_post_help.html',
                'minVersion' => '11.23',
            ],
        ];
    }

    /**
     * Only "user" accounts can be created. The "system" and "system-override" accounts are always created by the
     * application.
     *
     * {@inheritdoc}
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module']);
        $systemTypes = [
            OutboundEmail::TYPE_SYSTEM,
            OutboundEmail::TYPE_SYSTEM_OVERRIDE,
        ];

        if (isset($args['type']) && in_array($args['type'], $systemTypes)) {
            throw new SugarApiExceptionNotAuthorized(
                'EXCEPTION_CREATE_SYSTEM_ACCOUNT_NOT_AUTHORIZED',
                [
                    'type' => $args['type'],
                    'module' => translate('LBL_MODULE_NAME', $args['module']),
                ],
                $args['module']
            );
        }

        return parent::createRecord($api, $args);
    }

    /**
     * {@inheritdoc}
     * @uses OutboundEmail::saveSystem() to save the "system" account.
     */
    protected function saveBean(SugarBean $bean, ServiceBase $api, array $args)
    {
        $this->validateSmtpConfiguration($bean);

        if ($bean->type === OutboundEmail::TYPE_SYSTEM) {
            $bean->saveSystem(true);
            BeanFactory::unregisterBean($bean->module_name, $bean->id);
        } else {
            parent::saveBean($bean, $api, $args);
        }
    }

    /**
     * Get Mailer instance from MailerFactory
     *
     * @param OutboundEmailConfiguration $outboundEmailConfiguration
     * @return mixed Mailer
     */
    protected function getMailer(OutboundEmailConfiguration $outboundEmailConfiguration)
    {
        return MailerFactory::getMailer($outboundEmailConfiguration);
    }

    /**
     * Validate the SMTP account settings and verify that the SMTP server can be successfully connected to.
     *
     * @param SugarBean $oe
     * @throws SugarApiException
     */
    private function validateSmtpConfiguration(SugarBean $oe)
    {
        try {
            $configurations = ['from_email' => 'a@a'];
            $outboundEmailConfiguration = OutboundEmailConfigurationPeer::buildOutboundEmailConfiguration(
                $GLOBALS['current_user'],
                $configurations,
                $oe
            );

            $mailer = $this->getMailer($outboundEmailConfiguration);
            if (empty($mailer)) {
                throw new MailerException('Invalid Mailer', MailerException::InvalidMailer);
            }
            $mailer->connect();
        } catch (MailerException $e) {
            throw new SugarApiException(
                $e->getUserFriendlyMessage(),
                null,
                'Emails',
                422,
                'smtp_server_error'
            );
        }
    }

    /**
     * Apply a User's credentials to the system email configuration and send
     * a test email
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array containing the email test results:
     *                  'status' => True if the email was successful
     *                  'errorMessage' => If the email was not successful, the
     *                                    error message that was generated
     */
    public function testUserSystemOverrideEmail(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['user_id', 'to_address']);

        if (!AccessControlManager::instance()->allowRecordAccess('Users', $args['user_id'])) {
            $errorMsg = 'Not authorized to access record ' . $args['module'] . ':' . $args['user_id'];
            $GLOBALS['log']->fatal($errorMsg);
            return [
                'status' => false,
                'errorMessage' => $errorMsg,
            ];
        }

        try {
            // Get the user's saved override email credentials
            $userOverrideSettings = $this->getUserDefaultCredentials($args['user_id']);

            // Get the system email configuration
            $systemEmailSettings = $this->getSystemEmailSettings();

            // Apply the User's credentials/testing data, and send a test email
            return $this->sendTestEmail([
                $systemEmailSettings['mail_smtpserver'],
                $systemEmailSettings['mail_smtpport'],
                $systemEmailSettings['mail_smtpssl'],
                $systemEmailSettings['mail_smtpauth_req'],
                $args['mail_smtpuser'] ?? $userOverrideSettings['mail_smtpuser'],
                $args['mail_smtppass'] ?? $userOverrideSettings['mail_smtppass'],
                $args['from_address'] ?? $userOverrideSettings['email_address'],
                $args['to_address'],
                'SMTP',
                $args['name'] ?? $userOverrideSettings['name'],
                $systemEmailSettings['mail_smtptype'],
                $systemEmailSettings['mail_authtype'],
                $args['eapm_id'] ?? $userOverrideSettings['eapm_id'],
            ]);
        } catch (Exception $e) {
            $result = [
                'status' => false,
                'errorMessage' => translate('LBL_EMAIL_INVALID_SYSTEM_CONFIGURATION', 'Emails'),
            ];
        }

        return [
            'status' => $result['status'],
            'errorMessage' => $result['errorMessage'] ?? '',
        ];
    }

    /**
     * Returns the default email credentials for a user's system override email account
     *
     * @param string $userId the ID of the user
     * @return array the default email credentials for the user's system override email account
     */
    protected function getUserDefaultCredentials($userId)
    {
        $ob = BeanFactory::newBean('OutboundEmail');
        $userOverride = $ob->getUsersMailerForSystemOverride($userId);
        return [
            'name' => $userOverride->name ?? '',
            'mail_smtpuser' => $userOverride->mail_smtpuser ?? '',
            'mail_smtppass' => $userOverride->mail_smtppass ?? '',
            'eapm_id' => $userOverride->eapm_id ?? '',
            'email_address' => $userOverride->email_address ?? '',
        ];
    }

    /**
     * Returns the system mailer configuration
     *
     * @return Array The system mailer configuration
     */
    protected function getSystemEmailSettings()
    {
        $ob = BeanFactory::newBean('OutboundEmail');
        $systemEmailSettings = $ob->getSystemMailerSettings(false);
        return [
            'mail_smtpserver' => $systemEmailSettings->mail_smtpserver ?? '',
            'mail_smtpport' => $systemEmailSettings->mail_smtpport ?? '',
            'mail_smtpssl' => $systemEmailSettings->mail_smtpssl ?? '',
            'mail_smtpauth_req' => $systemEmailSettings->mail_smtpauth_req ?? '',
            'mail_smtptype' => $systemEmailSettings->mail_smtptype ?? '',
            'mail_authtype' => $systemEmailSettings->mail_authtype,
        ];
    }

    /**
     * Sends a test email using the given email configuration arguments
     *
     * @param array $args The email ocnfiguration settings to pass into sendEmailTest
     * @return array The results of the test email
     */
    protected function sendTestEmail($args)
    {
        return Email::sendEmailTest(...$args);
    }
}
