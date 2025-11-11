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

class DRICustomerJourneyConfigApi extends ConfigModuleApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'saveConfigureModules' => [
                'reqType' => 'PUT',
                'path' => ['DRI_Workflows', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'saveConfigureModules',
                'shortHelp' => 'Saves the config for enabled modules of CJ',
                'longHelp' => 'include/api/help/customer_journeyDRI_Workflows_config_module_put_help.html',
                'minVersion' => '11.18',
            ],
        ];
    }

    /**
     * Saves the config for enabled modules of CJ
     *
     * @param ServiceBase $api
     * @param array $args -readConfigureModules
     * @return array
     * @throws SugarApiExceptionMissingParameter
     */
    public function saveConfigureModules(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Admin
        ConfigurationManager::ensureAdminUser();

        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['enabled_modules', 'recordview_display_settings']);

        $hasEnabledModuleChanged = $this->changeInEnabledModules($args['enabled_modules']);
        $removedModules = $this->getRemovedModules($args['enabled_modules']);

        $configurator = new Configurator();
        if (isset($args['enabled_modules'])) {
            if (is_array($args['enabled_modules'])) {
                $enabledModules = '';
                foreach ($args['enabled_modules'] as $enabledModule) {
                    $enabledModules .= $enabledModule . ',';
                }
                $enabledModules = rtrim($enabledModules, ',');
                $configurator->config['customer_journey']['enabled_modules'] = $enabledModules;
            } else {
                $configurator->config['customer_journey']['enabled_modules'] = $args['enabled_modules'];
            }
        }
        if (isset($args['recordview_display_settings'])) {
            $configurator->config['customer_journey']['recordview_display_settings'] =
                $args['recordview_display_settings'];
        }
        $configurator->saveConfig();

        if ($hasEnabledModuleChanged) {
            $driworkflowapi = new DRI_WorkflowsApi();
            $driworkflowapi->removeCJParentVardef($removedModules);
            $driworkflowapi->createModuleRelationships($api, ['repair' => true]);
        }

        return true;
    }

    /**
     * Get the difference of enabled modules
     * From config and request
     *
     * @param string $enabledModules
     * @return boolean
     */
    private function changeInEnabledModules($enabledModules)
    {
        $changed = false;
        global $sugar_config;
        $configEnabledModules = [];

        if (isset($sugar_config['customer_journey']['enabled_modules']) &&
            is_string($sugar_config['customer_journey']['enabled_modules'])) {
            if (is_string($enabledModules)) {
                $enabledModules = explode(',', $enabledModules);
            }
            $configEnabledModules = explode(',', $sugar_config['customer_journey']['enabled_modules']);
            $changed = array_diff($configEnabledModules, $enabledModules) ||
                array_diff($enabledModules, $configEnabledModules);
        }

        return $changed;
    }

    /**
     * Get the removed modules
     *
     * @param string $enabledModules
     * @return boolean
     */
    private function getRemovedModules($enabledModules)
    {
        $removedModules = false;
        global $sugar_config;
        $configEnabledModules = [];

        if (isset($sugar_config['customer_journey']['enabled_modules']) &&
            is_string($sugar_config['customer_journey']['enabled_modules'])) {
            if (is_string($enabledModules)) {
                $enabledModules = explode(',', $enabledModules);
            }
            $configEnabledModules = explode(',', $sugar_config['customer_journey']['enabled_modules']);
            $removedModules = array_diff($configEnabledModules, $enabledModules);
        }

        return $removedModules;
    }
}
