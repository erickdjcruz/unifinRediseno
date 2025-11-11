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

use Sugarcrm\IdentityProvider\Srn\Converter as SrnConverter;
use Sugarcrm\IdentityProvider\Srn\Manager as SrnManager;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

class UsersApi extends ModuleApi
{
    public function registerApiRest()
    {
        return [
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['Users', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'retrieveRecord',
                'shortHelp' => 'Returns a single record',
                'longHelp' => 'include/api/help/module_record_get_help.html',
            ],
            'create' => [
                'reqType' => 'POST',
                'path' => ['Users'],
                'pathVars' => ['module'],
                'method' => 'createUser',
                'minVersion' => '11.6',
                'shortHelp' => 'This method creates a User record',
                'longHelp' => 'modules/Users/clients/base/api/help/UsersApi_create.html',
                'ignoreSystemStatusError' => true,
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['Users', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateUser',
                'minVersion' => '11.6',
                'shortHelp' => 'This method updates a User record',
                'longHelp' => 'modules/Users/clients/base/api/help/UsersApi_update.html',
                'ignoreSystemStatusError' => true,
            ],
            'delete' => [
                'reqType' => 'DELETE',
                'path' => ['Users', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'deleteUser',
                'shortHelp' => 'This method deletes a User record',
                'longHelp' => 'modules/Users/clients/base/api/help/UsersApi.html',
                'ignoreSystemStatusError' => true,
            ],
            'getFreeBusySchedule' => [
                'reqType' => 'GET',
                'path' => ['Users', '?', 'freebusy'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'getFreeBusySchedule',
                'shortHelp' => 'Retrieve a list of calendar event start and end times for specified person',
                'longHelp' => 'include/api/help/user_get_freebusy_help.html',
            ],
            'reset' => [
                'reqType' => 'PUT',
                'path' => ['Users', '?', 'resetPreferences'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'resetPreferences',
                'minVersion' => '11.23',
                'shortHelp' => 'This method resets User preference.',
                'longHelp' => 'modules/Users/clients/base/api/help/UsersApi_resetPreferences.html',
                'ignoreSystemStatusError' => true,
            ],
            'getUserAccess' => [
                'reqType' => 'GET',
                'path' => ['Users', '?', 'userAccess'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'getUserAccess',
                'minVersion' => '11.23',
                'shortHelp' => 'Retrieve the users role access defined by the admin in Roles',
                'longHelp' => 'modules/Users/clients/base/api/help/UsersApi_get_access.html',
            ],
        ];
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotFound
     */
    public function retrieveRecord(ServiceBase $api, array $args)
    {
        // If the API user should not have access to the requested User record,
        // indicate that the record was not found. This is in line with the
        // error returned when the user fails ACL checks
        if (!($api->user->isAdminForModule('Users') || $api->user->isDeveloperForModule('Users') || $api->user->id === $args['record'] || $this->isSudoUserRetrievingSelf($api->user, $args['record']))) {
            throw new SugarApiExceptionNotFound('Could not find record: ' . $args['record'] . ' in module: ' . $args['module']);
        }
        return parent::retrieveRecord($api, $args);
    }

    /**
     * Reset user preferences
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     */
    public function resetPreferences(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['record']);
        if (!($args['record'] == $api->user->id || $api->user->isAdminForModule('Users'))) {
            return [];
        }

        $user = BeanFactory::getBean('Users', $args['record']);
        $user->resetPreferences();

        if (!empty($args['skipResponse']) && isTruthy($args['skipResponse'])) {
            return [];
        }

        $settingsHelper = $this->getUserPreferenceFieldsHelper();
        $settingsFields = $user->getFieldDefinitions('user_preference', [true]);
        $result = [];

        foreach ($settingsFields as $settingsField) {
            $fieldName = $settingsField['name'] ?? '';
            if (!empty($fieldName)) {
                $result[$fieldName] = $settingsHelper->getPreferenceField($user, $fieldName) ?? '';
            }
        }

        return $result;
    }

    /**
     * create user for REST version >= 11.6, enforce license type validation
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function createUser(ServiceBase $api, array $args)
    {
        $isAdmin = $args['is_admin'] ?? false;
        $isGroup = $args['is_group'] ?? false;
        $isPortalOnly = $args['portal_only'] ?? false;

        // Need to enforce that the license type is provided for non-group or
        // non-portal-only users.
        if (!$isAdmin && ($isGroup || $isPortalOnly)) {
            $this->requireArgs($args, ['module']);
        } else {
            $this->requireArgs($args, ['module', 'license_type']);

            // Validate license types and empty is not allowed
            if (!$this->validateLicenseTypes($args['license_type'])) {
                throw new SugarApiExceptionInvalidParameter('Invalid license_type in module: Users');
            }
        }

        return $this->createRecord($api, $args);
    }

    /**
     * update user for REST API version >= 11.6
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function updateUser(ServiceBase $api, array $args)
    {
        return $this->updateRecord($api, $args);
    }

    /**
     * @inheritdoc
     *
     * Overrides the parent method to handle updating any user preferences
     */
    protected function saveBean(SugarBean $bean, ServiceBase $api, array $args)
    {
        $this->updateUserPreferenceFields($bean, $args);
        parent::saveBean($bean, $api, $args);
    }

    /**
     * Checks the arguments for any user preference fields that have been
     * changed, and updates the preferences for the User as needed
     *
     * @param SugarBean $bean the User bean being updated
     * @param array $args the API arguments
     */
    protected function updateUserPreferenceFields(SugarBean $bean, array $args)
    {
        $settingsHelper = $this->getUserPreferenceFieldsHelper();
        $settingsFields = $bean->getFieldDefinitions('user_preference', [true]);
        foreach ($settingsFields as $settingsField) {
            $fieldName = $settingsField['name'] ?? '';
            if (isset($args[$fieldName])) {
                $newValue = $args[$fieldName] ?? '';
                $oldValue = $settingsHelper->getPreferenceField($bean, $fieldName) ?? '';
                if ($oldValue !== $newValue) {
                    $settingsHelper->setPreferenceField($bean, $fieldName, $newValue);
                }
            }
        }
    }

    /**
     * @return UserPreferenceFieldsHelper
     */
    protected function getUserPreferenceFieldsHelper()
    {
        return new UserPreferenceFieldsHelper();
    }

    /**
     * Delete the user record and set the appropriate flags. Handled in a separate api call from the base one because
     * the base api delete field doesn't set user status to 'inactive' or employee_status to 'Terminated'
     *
     * The non-api User deletion logic is handled in /modules/Users/controller.php::action_delete()
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function deleteUser(ServiceBase $api, array $args)
    {
        // Users can be deleted only in cloud console for IDM mode.
        if (safeInArray($args['module'], $this->idmModeDisabledModules)
            && $this->isIDMModeEnabled()
            && empty($args['skip_idm_mode_restrictions'])) {
            throw new SugarApiExceptionNotAuthorized();
        }

        // Ensure we have admin access to this module
        if (!($api->user->isAdmin() || $api->user->isAdminForModule('Users'))) {
            throw new SugarApiExceptionNotAuthorized();
        }

        $this->requireArgs($args, ['module', 'record']);
        // loadBean() handles exceptions for bean validation
        $user = $this->loadBean($api, $args, 'delete');

        $user->mark_deleted($user->id);

        return ['id' => $user->id];
    }

    /**
     * Retrieve a list of calendar event start and end times for specified person
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getFreeBusySchedule(ServiceBase $api, array $args)
    {
        $bean = $this->loadBean($api, $args, 'view');
        return [
            'module' => $bean->module_name,
            'id' => $bean->id,
            'freebusy' => $bean->getFreeBusySchedule($args),
        ];
    }

    /**
     * Retrieve the users role access defined by the admin in Roles
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     *
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getUserAccess(ServiceBase $api, array $args)
    {
        // Ensure we have access for this action
        if (!($api->user->isAdminForModule('Users')
            || $api->user->isDeveloperForModule('Users')
            || $api->user->id === $args['record'])
        ) {
            throw new SugarApiExceptionNotAuthorized();
        }

        //the logic is taken from - /modules/ACLRoles/DetailUserRole.php
        $categories = ACLAction::getUserActions($args['record'], true);
        $acm = AccessControlManager::instance();

        $categories = array_filter($categories, function ($module) use ($acm) {
            return $acm->allowModuleAccess($module);
        }, ARRAY_FILTER_USE_KEY);

        //adding 'key' and 'value' properties to handle displaying data in hbs template
        return ACLAction::setupCategories($categories);
    }

    /**
     * validate license types. Only allow system entitled license types to go through
     *
     * @param $licenseTypes
     *
     * @return bool
     */
    protected function validateLicenseTypes($licenseTypes): bool
    {
        $seed = BeanFactory::newBean('Users');
        return $seed->validateLicenseTypes($seed->processLicenseTypes($licenseTypes, false));
    }

    /**
     * check if sudoer is trying to recieve information about self to display it in impersonation banner
     * @param User $user
     * @param $record
     * @return bool
     */
    protected function isSudoUserRetrievingSelf(User $user, $record): bool
    {
        if ($user->sudoer === null) {
            return false;
        }
        try {
            $srn = SrnConverter::fromString($user->sudoer);
        } catch (Exception $e) {
            return false;
        }
        if (!SrnManager::isUser($srn)) {
            return false;
        }
        $userResources = $srn->getResource();
        if (empty($userResources[1])) {
            return false;
        }
        return $userResources[1] === $record;
    }
}
