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

class RegisterContactApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => [
                    'Contacts',
                    'register',
                ],
                'pathVars' => [
                    'module',
                ],
                'method' => 'createContactRecord',
                'shortHelp' => 'This method registers contacts',
                'longHelp' => 'include/api/help/contacts_register_post_help.html',
                'noLoginRequired' => true,
                'minVersion' => '11.6',
            ],
        ];
    }

    /**
     * Fetches data from the $args array and updates the bean with that data
     * @param SugarBean $bean The bean to be updated
     * @param ServiceBase $api The API class of the request, used in cases where the API changes how the fields are pulled from the args array.
     * @param array $args The arguments array passed in from the API
     * @return string
     */
    protected function updateBean(SugarBean $bean, ServiceBase $api, array $args): string
    {
        $bean->update_modified_by = false;
        $bean->set_created_by = false;
        $admin = Administration::getSettings();
        if (!empty($admin->settings['supportPortal_RegCreatedBy'])) {
            $bean->created_by = $admin->settings['supportPortal_RegCreatedBy'];
            $bean->modified_user_id = $admin->settings['supportPortal_RegCreatedBy'];
        } else {
            $adminUser = BeanFactory::newBean('Users');
            $adminId = $adminUser->getSystemUser()->id;
            $bean->created_by = $adminId;
            $bean->modified_user_id = $adminId;
        }
        if (empty($GLOBALS['current_user']->id)) {
            $GLOBALS['current_user'] = BeanFactory::retrieveBean('Users', $bean->created_by);
        }
        return parent::updateBean($bean, $api, $args);
    }

    /**
     * Creates contact record
     * @param ServiceBase $api The API class of the request, used in cases where the API changes how the fields are pulled from the args array.
     * @param array $args The arguments array passed in from the API
     * @return string
     */
    public function createContactRecord(ServiceBase $api, array $args): string
    {
        global $app_list_strings, $current_language;

        if (!empty($args['portal_name'])) {
            if ($this->doesUserNameExist($args['portal_name'])) {
                throw new SugarApiExceptionNotAuthorized(translate('LBL_PORTAL_SIGNUP_USER_NAME_ERROR'), $args);
            }
        }

        if (!empty($args['portal_password']) &&
            !empty($args['portal_password1'])) {
            if ($args['portal_password'] !== $args['portal_password1']) {
                throw new SugarApiExceptionRequestMethodFailure(translate('LBL_PORTAL_SIGNUP_PASSWORD_ERROR'), $args);
            } elseif (!BeanFactory::getBean('Users')->check_password_rules($args['portal_password'])) {
                throw new SugarApiExceptionRequestMethodFailure(translate('LBL_PASSWORD_ENFORCE_TITLE'), $args);
            }
        }

        if (!empty($args['email'][0]['email_address']) &&
            !SugarEmailAddress::isValidEmail($args['email'][0]['email_address'])) {
            throw new SugarApiExceptionInvalidParameter(translate('LBL_PORTAL_SIGNUP_EMAIL_ERROR'), $args);
        }

        if (!isset($app_list_strings)) {
            $app_list_strings = return_app_list_strings_language($current_language);
        }

        $bean = BeanFactory::newBean('Contacts');

        // we force team and teamset because there is no current user to get them from
        $fields = [
            'team_set_id' => '1',
            'team_id' => '1',
            'lead_source' => 'Support Portal User Registration',
        ];

        $adminSettings = Administration::getSettings();

        if (!empty($adminSettings->settings['portal_defaultUser'])) {
            $fields['assigned_user_id'] = html_entity_decode((string)$adminSettings->settings['portal_defaultUser'], ENT_COMPAT);
        }

        // The field list that we want to add to Contact
        // fieldName => requiredField
        $fieldList = [
            'first_name' => false,
            'last_name' => true,
            'portal_user_company_name' => false,
            'email' => true,
            'portal_name' => true,
            'portal_password' => true,
        ];

        foreach ($fieldList as $fieldName => $requiredField) {
            if (isset($args[$fieldName])) {
                $fields[$fieldName] = $args[$fieldName];
            } else {
                if ($requiredField) {
                    throw new SugarApiExceptionInvalidParameter(translate('ERR_MISSING_REQUIRED_FIELDS') .
                        $fieldName, $args);
                }
            }
        }

        $bean->entry_source = 'external';

        return $this->updateBean($bean, $api, $fields);
    }

    /**
     * Check if user name already exists
     * @param string $portal_name The portal name entered by user
     * @return bool
     */
    public function doesUserNameExist(string $portal_name): bool
    {
        $contact = BeanFactory::newBean('Contacts');

        $query = new SugarQuery();
        $query->select(['id']);
        $query->from($contact, ['team_security' => false])
            ->where()
            ->equals('portal_name', $portal_name);
        $contactId = $query->getOne();

        return !empty($contactId);
    }
}
