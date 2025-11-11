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

namespace Sugarcrm\Sugarcrm\Hint;

use Sugarcrm\Sugarcrm\Hint\Logger\Logger as HintLogger;

class ConfigurationManager
{
    /**
     * Update hint config entry
     *
     * @param string $key
     * @param string $value
     */
    public static function updateHintConfigEntry($key, $value)
    {
        $admin = \BeanFactory::newBean('Administration');
        $module = HintConstants::HINT_CONFIG_MAP[$key]['module'];
        $platform = HintConstants::HINT_CONFIG_MAP[$key]['platform'];
        $name = HintConstants::HINT_CONFIG_MAP[$key]['name'];
        $admin->saveSetting($module, $name, $value, $platform);
    }

    /**
     * Get hint config entry
     *
     * @param string $key
     * @return mixed
     */
    public static function getHintConfigEntry($key)
    {
        $configBean = \BeanFactory::newBean('Administration');
        $query = new \SugarQuery();
        $query->select(['*']);
        $query->from($configBean);
        $query->where()
            ->equals('category', HintConstants::HINT_CONFIG_MAP[$key]['module'])
            ->equals('platform', HintConstants::HINT_CONFIG_MAP[$key]['platform'])
            ->equals('name', HintConstants::HINT_CONFIG_MAP[$key]['name']);
        $response = $query->execute();
        return safeCount($response) > 0 ? $response[0] : null;
    }

    /**
     * Create initial module config
     *
     * @param array $configFieldsForModule
     * @return array
     */
    public static function createInitialModuleConfig($configFieldsForModule)
    {
        $configArray = [];
        for ($x = 0; $x < safeCount($configFieldsForModule); $x++) {
            $key = $configFieldsForModule[$x];
            $configArray[$key] = true;
        }
        return $configArray;
    }

    /**
     * Check if table contains rows
     *
     * @param string $tableModuleName
     * @return bool
     */
    public static function doesTableContainsRows($tableModuleName)
    {
        $tableBean = \BeanFactory::newBean($tableModuleName);
        $query = new \SugarQuery();
        $query->select(['*']);
        $query->from($tableBean);
        $response = $query->execute();
        return safeCount($response) > 0 ? true : false;
    }

    /**
     * Ensure current user has admin permissions
     *
     * @throws \SugarApiExceptionNotAuthorized
     */
    public static function ensureAdminUser()
    {
        global $current_user, $app_strings;

        if (!$current_user->isAdmin()) {
            $logger = new HintLogger();
            $logger->alert('Hint: Non Admin user exception');
            throw new \SugarApiExceptionNotAuthorized($app_strings['EXCEPTION_NOT_AUTHORIZED']);
        }
    }

    /**
     * Check current user is hint user
     *
     * @return bool
     */
    public static function isHintUser()
    {
        global $current_user;

        if ($current_user) {
            return $current_user->isLicensedForHint();
        }
        return false;
    }

    /**
     * Check is sugar pro user
     *
     * @return bool
     */
    public static function isSugarProUser()
    {
        global $sugar_flavor;
        return $sugar_flavor === 'PRO';
    }
}
