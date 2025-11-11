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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate;

/**
 * This class have helper functions for the
 * `Allow Activity By` field in Task Template
 */
class AllowActivityBy
{
    private static $separator = '|';

    /**
     * Check that current user allow to complete the given activity or not
     *
     * if current user's id, team_id or role_id belongs to allow_activity_by user ids,
     * team ids or role ids then it will return true otherwise false
     * @param object $activity
     * @param string $allow_activity_by
     *
     * @return bool
     */
    public static function isActivityAllow($activity, $allow_activity_by)
    {
        $allowActivityByArray = self::getJsonDecodedData($allow_activity_by);

        // means we have [{"id":""}] in DB or nothing get in decoded result
        $allowCount = safeCount($allowActivityByArray);
        if ($allowCount === 0 || ($allowCount === 1 && empty($allowActivityByArray[0]['id']))) {
            return true;
        }

        $allowedTeamIds = self::getCurrentUserTeamIds();
        $allowedRoleIds = self::getCurrentUserRoleIds();

        $allowed = false;
        $formattedIdsExists = false;
        foreach ($allowActivityByArray as $allowActivityBy) {
            if (empty($allowActivityBy['id'])) {
                continue;
            }

            if (isset($allowActivityBy['formattedIds']) && array_key_exists('formattedIds', $allowActivityBy)) {
                $formattedIds = self::getFilteredArray(explode(self::$separator, $allowActivityBy['formattedIds']));
                $formattedIdsExists = (!$formattedIdsExists && !empty($formattedIds)) ? true : false;
            }

            if (empty($formattedIds)) {
                continue;
            }

            if ($allowActivityBy['id'] === 'users' && self::isAllowByUsers($formattedIds, $GLOBALS['current_user']->id)) {
                $allowed = true;
            }
            if (!$allowed && $allowActivityBy['id'] === 'teams' && self::isAllowByTeams($formattedIds, $allowedTeamIds)) {
                $allowed = true;
            }
            if (!$allowed && $allowActivityBy['id'] === 'roles' && self::isAllowByRoles($formattedIds, $allowedRoleIds)) {
                $allowed = true;
            }
        }

        // if no actual IDs exists and allowed is false then we have to remove restriction
        if (!$formattedIdsExists && !$allowed) {
            $allowed = true;
        }

        return $allowed;
    }

    /**
     * Decode the data of the allow_activity_by
     * type field
     *
     * @param string $string
     *
     * @return array $data
     */
    private static function getJsonDecodedData($string)
    {
        if (empty($string)) {
            return [];
        }

        $data = json_decode($string, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        return [];
    }

    /**
     * It will return the Team Ids of the current user
     * @return array
     */
    private static function getCurrentUserTeamIds()
    {
        global $current_user;
        $teams = $current_user->get_my_teams();

        return array_keys($teams);
    }

    /**
     * It will return the Roles Ids of the current user
     * @return array
     */
    private static function getCurrentUserRoleIds()
    {
        $roles = \ACLRole::getUserRoles($GLOBALS['current_user']->id, false);
        $my_roles_ids = [];

        foreach ($roles as $role) {
            $my_roles_ids[] = $role->id;
        }

        return $my_roles_ids;
    }

    /**
     * It will check that any user matches with the current user
     * or not. If yes then it will return true, otherwise false
     * @param array $formattedIds
     * @param string $currUserId
     *
     * @return bool
     */
    private static function isAllowByUsers($allowedUserIds, $currUserId)
    {
        if (empty($allowedUserIds) || empty($currUserId)) {
            return false;
        }

        return safeInArray($currUserId, $allowedUserIds);
    }

    /**
     * It will check that any allowed team matches with the current user team/teams
     * or not. If yes then it will return true, otherwise false
     * @param array $allowedTeamIds
     * @param array $currUserTeamIds
     * @return bool
     */
    private static function isAllowByTeams($allowedTeamIds, $currUserTeamIds)
    {
        if (empty($allowedTeamIds) || empty($currUserTeamIds)) {
            return false;
        }

        return safeCount(array_intersect($allowedTeamIds, $currUserTeamIds)) > 0;
    }

    /**
     * It will check that any allowed role matches with the current user role/roles
     * or not. If yes then it will return true, otherwise false
     * @param array $allowedRolesIds
     * @param array $currUserRoleIds
     * @return bool
     */
    private static function isAllowByRoles($allowedRolesIds, $currUserRoleIds)
    {
        if (empty($allowedRolesIds) || empty($currUserRoleIds)) {
            return false;
        }

        return safeCount(array_intersect($allowedRolesIds, $currUserRoleIds)) > 0;
    }

    /**
     * array_filter is black listed function so
     * as an alternate, traversing the array and
     * removing the empty index
     *
     * @param array $arr
     *
     * @return array $filteredArray
     */
    private static function getFilteredArray($arr)
    {
        $filteredArray = [];
        foreach ($arr as $key => $value) {
            if (!empty($value)) {
                $filteredArray[$key] = $value;
            }
        }
        return $filteredArray;
    }
}
