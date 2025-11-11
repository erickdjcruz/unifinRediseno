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

use Sugarcrm\Sugarcrm\Security\Password\Utilities;
use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdpConfig;

class PasswordApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'GET',
                'path' => ['password', 'request'],
                'pathVars' => ['module'],
                'method' => 'requestPassword',
                'shortHelp' => 'This method allows a User who is not logged in to request an email be sent to reset their password',
                'longHelp' => 'include/api/help/password_request_get_help.html',
                'noLoginRequired' => true,
                'ignoreSystemStatusError' => true,
            ],
            'createAdmin' => [
                'reqType' => 'POST',
                'path' => ['password', 'adminRequest'],
                'pathVars' => ['', ''],
                'method' => 'adminRequestPassword',
                'shortHelp' => 'This method allows a User admin to request that an email be sent to a User to reset their password',
                'longHelp' => 'include/api/help/password_admin_request_post_help.html',
                'minVersion' => '11.24',
            ],
        ];
    }

    /**
     * Resets password and sends email to user
     * @param ServiceBase $api
     * @param array $args
     * @return bool true if the password reset email was successfully sent
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionMissingParameter
     */
    public function requestPassword(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['email', 'username']);

        require_once 'modules/Users/language/en_us.lang.php';

        // Forgot password feature must be enabled
        global $sugar_config;
        if (empty($sugar_config['passwordsetting']['forgotpasswordON'])) {
            $errorMsg = translate('LBL_FORGOTPASSORD_NOT_ENABLED', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }

        $this->validateSystemEmailIsConfigured($args);

        // Get the bean for the User by the given username
        $userBean = $this->getUserByUsername($args['username']);
        $this->validateUserForPasswordChange($userBean, $args);

        // Email arg must be the User's primary email address
        if (!$userBean->isPrimaryEmail($args['email'])) {
            $errorMsg = translate('LBL_PROVIDE_USERNAME_AND_EMAIL', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }

        $emailTemplateId = $sugar_config['passwordsetting']['lostpasswordtmpl'] ?? null;
        $this->validateEmailTemplateForPasswordChange($emailTemplateId, $args);

        $passwordChangeGuid = $this->generateUserPasswordChangeEntry($userBean, $api->platform);
        $url = prependSiteURL("/index.php?entryPoint=Changenewpassword&guid=$passwordChangeGuid");

        return $this->sendPasswordResetEmail($userBean, $url, $emailTemplateId, $args);
    }

    /**
     * Allows an admin to trigger a possword reset for a User. An email will be
     * sent to the User with a link to reset their password
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array The data of the provided User
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionMissingParameter
     */
    public function adminRequestPassword(ServiceBase $api, array $args)
    {
        if (!$api->user->isAdminForModule('Users')) {
            $errorMsg = translate('ERR_PASSWORD_RESET_NOT_ADMIN', 'Users');
            throw new SugarApiExceptionNotAuthorized($errorMsg, $args);
        }

        $idpConfig = $this->getIdpConfig();
        if ($idpConfig->isIDMModeEnabled() && $idpConfig->getUserLicenseTypeIdmModeLock()) {
            $errorMsg = translate('ERR_PASSWORD_RESET_IDM_LICENSING_MODE', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }

        $this->requireArgs($args, ['userId']);

        $this->validateSystemEmailIsConfigured($args);

        $userBean = $this->getUserById($args['userId']);
        $this->validateUserForPasswordChange($userBean, $args);

        global $sugar_config;
        $emailTemplateId = $sugar_config['passwordsetting']['lostpasswordtmpl'] ?? null;
        $this->validateEmailTemplateForPasswordChange($emailTemplateId, $args);

        $passwordChangeGuid = $this->generateUserPasswordChangeEntry($userBean, $api->platform);
        $url = prependSiteURL("/index.php?entryPoint=Changenewpassword&guid=$passwordChangeGuid");

        $this->sendPasswordResetEmail($userBean, $url, $emailTemplateId, $args);
        return ApiHelper::getHelper($api, $userBean)->formatForApi($userBean);
    }

    /**
     * Returns the IdP-related configuration settings
     *
     * @return IdpConfig
     */
    protected function getIdpConfig()
    {
        return new IdpConfig(\SugarConfig::getInstance());
    }

    /**
     * Validates that the system email account is configured to be able to send
     * a password reset email
     *
     * @param array $args The request arguments
     * @throws SugarApiExceptionRequestMethodFailure
     */
    protected function validateSystemEmailIsConfigured(array $args)
    {
        $systemMailer = $this->getSystemMailer();
        if (empty($systemMailer->mail_smtpserver)) {
            $errorMsg = translate('ERR_PASSWORD_RESET_INVALID_SYSTEM_EMAIL', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }
    }

    /**
     * Retrieves the system OutboundEmail account
     *
     * @return SugarBean The system OutboundEmail account
     */
    protected function getSystemMailer()
    {
        $systemMailer = BeanFactory::newBean('OutboundEmail');
        $systemMailer->getSystemMailerSettings(false);
        return $systemMailer;
    }

    /**
     * Validates that a User bean is set up correctly to receive a password
     * change request email
     *
     * @param SugarBean|null $userBean The User whose password is being reset
     * @param array $args The request arguments
     * @throws SugarApiExceptionRequestMethodFailure
     */
    protected function validateUserForPasswordChange(?SugarBean $userBean, array $args)
    {
        if (empty($userBean->id)) {
            $errorMsg = translate('ERR_PASSWORD_RESET_NO_USER', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }

        if ($userBean->portal_only || $userBean->is_group) {
            $errorMsg = translate('ERR_PASSWORD_RESET_GROUP_PORTAL', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }

        if (!SugarEmailAddress::isValidEmail($userBean->emailAddress->getPrimaryAddress($userBean))) {
            $errorMsg = translate('ERR_PASSWORD_RESET_INVALID_EMAIL', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }
    }

    /**
     * Retrieves a User bean with the given ID
     *
     * @param string $userId The ID of the User bean to retrieve
     * @return SugarBean|null The User bean if it exists
     */
    protected function getUserById(string $userId)
    {
        return BeanFactory::retrieveBean('Users', $userId);
    }

    /**
     * Retrieves a User bean with the given username
     *
     * @param string $username The username of the User bean to retrieve
     * @return SugarBean|null The User bean if it exists
     */
    protected function getUserByUsername(string $username)
    {
        $userBean = BeanFactory::newBean('Users');
        $userId = $userBean->retrieve_user_id($username);
        $userBean->retrieve($userId);
        return $userBean->id ? $userBean : null;
    }

    /**
     * Retrieves an EmailTemplate bean with the given ID
     *
     * @param string $emailTemplateId The ID of the EmailTemplate bean to retrieve
     * @return SugarBean|null
     */
    protected function getEmailTemplateById(string $emailTemplateId)
    {
        return BeanFactory::retrieveBean('EmailTemplates', $emailTemplateId, ['disable_row_level_security' => true]);
    }

    /**
     * Validates that an Email Template with the given ID exists
     *
     * @param string $emailTemplateId The ID of the Email Template
     * @param array $args The request arguments
     * @throws SugarApiExceptionRequestMethodFailure
     */
    protected function validateEmailTemplateForPasswordChange(string $emailTemplateId, array $args)
    {
        $emailTemplate = $this->getEmailTemplateById($emailTemplateId);
        if (empty($emailTemplate->id)) {
            $errorMsg = translate('ERR_PASSWORD_RESET_INVALID_EMAIL_TEMPLATE', 'Users');
            throw new SugarApiExceptionRequestMethodFailure($errorMsg, $args);
        }
    }

    /**
     * Inserts a row into the users_password_link table to track the User's password
     * reset request
     *
     * @param SugarBean $user The User whose password is being reset
     * @param string $platform The platform for which the password is being reset
     * @return string The GUID of the row created in users_password_link
     * @throws SugarApiExceptionRequestMethodFailure
     */
    protected function generateUserPasswordChangeEntry(SugarBean $user, string $platform)
    {
        $guid = Uuid::uuid1();
        $values = [
            'guid' => $guid,
            'bean_id' => $user->id,
            'bean_type' => $user->module_name,
            'name' => $user->user_name,
            'platform' => $platform,
        ];

        if (empty(Utilities::insertIntoUserPwdLink($values))) {
            throw new SugarApiExceptionRequestMethodFailure(translate('LBL_INSERT_TO_USER_PWD_FAILED'));
        }

        return $guid;
    }

    /**
     * Sends an email to a User with a link to reset their password
     *
     * @param SugarBean $user The User whose password is being reset
     * @param string $resetLink The reset password link to send in the email
     * @param string $emailTemplateId The ID of the EmailTemplate for the email
     * @param array $args The request arguments
     * @return bool true if the email send was successful
     * @throws SugarApiExceptionRequestMethodFailure
     */
    protected function sendPasswordResetEmail(SugarBean $user, string $resetLink, string $emailTemplateId, array $args)
    {
        // Due to GDPR restrictions, system generated passwords are forbidden. User must get a link to set the password
        $additionalData = [
            'link' => true,
            'url' => $resetLink,
            'password' => '',
        ];

        $result = $user->sendEmailForPassword($emailTemplateId, $additionalData);

        if ($result['status']) {
            return true;
        } elseif ($result['message'] != '') {
            throw new SugarApiExceptionRequestMethodFailure($result['message'], $args);
        } else {
            throw new SugarApiExceptionRequestMethodFailure('LBL_EMAIL_NOT_SENT', $args);
        }
    }
}
