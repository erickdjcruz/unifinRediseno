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

/**
 * Class for common ModuleBuilder utilities
 */
class MBHelper
{
    /**
     * Brittle list of roles that should not be used with RBV.
     * We need a better way of identifing these roles in the future.
     * @var array
     */
    protected static $hiddenRoles = [
        'Tracker',
        'Customer Self-Service Portal Role',
    ];

    /**
     * Returns list of roles with marker indicating whether role specific metadata exists
     *
     * @param callable $callback Callback that checks if there is role specific metadata
     * @return array
     */
    public static function getAvailableRoleList($callback)
    {
        $roles = self::getRoles($callback);

        $result = ['' => translate('LBL_BASE_LAYOUT')];
        foreach ($roles as $role) {
            $hasMetadata = $roles->offsetGet($role);
            $prefix = $hasMetadata ? '* ' : '';
            $result[$role->id] = $prefix . $role->name;
        }

        return $result;
    }

    /**
     * Returns list of roles which have role specific metadata
     *
     * @param callable $callback
     * @param $currentRole
     * @return array
     */
    public function getRoleListWithMetadata($callback, $currentRole)
    {
        $roles = self::getRoles($callback);

        $result = [];
        foreach ($roles as $role) {
            $hasMetadata = $roles->offsetGet($role);
            if ($hasMetadata && $role->id != $currentRole) {
                $result[$role->id] = $role->name;
            }
        }

        return $result;
    }

    /**
     * Returns object storage containing available roles as keys
     * and flags indicating if there is role specific metadata as value
     *
     * @param callable $callback Callback that checks if there is role specific metadata
     * @return SplObjectStorage
     */
    public static function getRoles($callback = null)
    {
        global $current_user;

        $roles = new SplObjectStorage();
        //Only super user should have access to all roles
        $allRoles = $current_user->isAdmin() ? ACLRole::getAllRoles() : ACLRole::getUserRoles($current_user->id, false);
        foreach ($allRoles as $role) {
            if (in_array($role->name, static::$hiddenRoles)) {
                continue;
            }
            $roles[$role] = $callback ? $callback([
                'role' => $role->id,
                'layoutOption' => 'role',
            ]) : null;
        }

        return $roles;
    }

    /**
     * Callback checks if there are existing metadata files
     *
     * @param string $field
     * @param string $value
     * @param callback $callback
     * @return mixed|null
     */
    public static function checkDropdownMetadata($field, $value, $callback = null)
    {
        if ($callback) {
            return $callback([
                'layoutOption' => 'dropdown',
                'dropdownField' => $field,
                'dropdownValue' => $value,
            ]);
        }
        return null;
    }

    /**
     * Returns list of dropdown values with marker indicating whether role specific metadata exists
     *
     * @param callable $callback Callback that checks if there is dropdown value specific metadata
     * @param array $params
     * @param array $dropdownFields
     * @return array
     */
    public static function getAvailableDropdownValuesList($callback, $params, $dropdownFields)
    {
        $options = getOptionsFromVardef(
            $dropdownFields[array_search($params['dropdownField'], array_column($dropdownFields, 'name'))]
        );
        $result = ['' => translate('LBL_BASE_LAYOUT')];
        foreach ($options as $key => $option) {
            if ($key) {
                $prefix = self::checkDropdownMetadata($params['dropdownField'], $key, $callback) ? '* ' : '';
                $result[$key] = $prefix . $key . ' [' . $option . ']';
            }
        }
        return $result;
    }

    /**
     * Checks if the specified dropdown field and value combination has custom metadata defined
     *
     * @param callable $callback Callback to checks if there is dropdown value specific metadata
     * @param array $params Selected parameters
     * @param array $dropdownFields Dropdown fields for respective module
     * @return array
     */
    public static function getDropdownValueWithMetadata($callback, $params, $dropdownFields)
    {
        $options = getOptionsFromVardef(
            $dropdownFields[array_search($params['dropdownField'], array_column($dropdownFields, 'name'))]
        );
        $result = [];
        foreach ($options as $key => $option) {
            if ($key) {
                $exists = self::checkDropdownMetadata($params['dropdownField'], $key, $callback);
                if ($exists) {
                    if (isset($params['dropdownValue']) && $key === $params['dropdownValue']) {
                        $result['resultForReset'][$key] = $key . ' [' . $option . ']';
                    } else {
                        $result['resultsForCopy'][$key] = $key . ' [' . $option . ']';
                    }
                }
            }
        }
        return $result;
    }
}
