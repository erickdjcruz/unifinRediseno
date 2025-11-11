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

use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;

require_once 'data/SugarACLStrategy.php';

/**
 * This class is used to enforce ACLs on Sugar Automate modules.
 */
class SugarACLCustomerJourney extends SugarACLStrategy
{
    private static $moduleCacheAccess =[];

    private static $regularUsersNoAccessModules = [
        'DRI_Workflow_Templates',
        'DRI_Workflow_Task_Templates',
        'DRI_SubWorkflow_Templates',
    ];

    private static $regularUsersAccessibleModules = [
        'DRI_Workflows',
        'CJ_Forms',
        'DRI_SubWorkflows',
        'CJ_WebHooks',
    ];

    /**
     * Only allow access to users with the user admin setting
     *
     * @param string $module
     * @param string $view
     * @param array $context
     * @return bool
     */
    public function checkAccess($module, $view, $context)
    {
        $accessValue = $this->getModuleFieldCache($module, $view, $context);
        if (isset($accessValue)) {
            return $accessValue;
        } else {
            try {
                $user = $this->getCurrentUser($context);
                $isAdminUser = $user->isAdmin();
                $isDeveloperUser = $user->isDeveloperForModule($module);

                //CJ-545: In case of fresh install return true so that enabled tabs list
                //only has the modules added in performsetup enabledtabs  list
                if (isset($GLOBALS['installing']) && $GLOBALS['installing'] === true) {
                    return $this->setModuleFieldCache($module, $view, $context, true);
                }

                $isAutomateUser = ConfigurationManager::ensureAutomateUser();

                if ($isAdminUser || $isDeveloperUser || safeInArray($module, self::$regularUsersAccessibleModules)) {
                    return $this->setModuleFieldCache($module, $view, $context, $isAutomateUser);
                }

                if (safeInArray($module, self::$regularUsersNoAccessModules)) {
                    // always allow to read data in all Sugar Automate modules
                    if (!safeInArray(strtolower($view), ['edit', 'delete', 'import'], true)) {
                        return $this->setModuleFieldCache($module, $view, $context, true);
                    }

                    if ($view === 'field' &&
                        safeInArray(strtolower($context['action']), ['list', 'detail', 'read', 'access'], true)
                    ) {
                        return $this->setModuleFieldCache($module, $view, $context, true);
                    }
                }
            } catch (Exception $e) {
                return $this->setModuleFieldCache($module, $view, $context, false);
            }
        }

        return $this->setModuleFieldCache($module, $view, $context, false);
    }

    /**
     * Set the vaule for that module field and view
     *
     * @param string $module
     * @param string $view
     * @param array $context
     * @param bool $flag
     * @return bool
     */
    private function setModuleFieldCache($module, $view, $context, $flag)
    {
        if ($view === 'field') {
            return self::$moduleCacheAccess[$module][$view][$context['field']][$context['action']] = $flag;
        }
        return self::$moduleCacheAccess[$module][$view] = $flag;
    }

    /**
     *  the vaule for that module field and view
     *
     * @param string $module
     * @param string $view
     * @param array $context
     * @return bool|[]
     */
    private function getModuleFieldCache($module, $view, $context)
    {
        $cache = self::$moduleCacheAccess[$module] ?? null;
    
        if (!$cache) {
            return null;
        }
    
        if ($view === 'field') {
            return $cache[$view][$context['field']][$context['action']] ?? null;
        }
    
        return $cache[$view] ?? null;
    }
}
