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

class VisualPipelineApi extends ConfigModuleApi
{
    public function registerApiRest()
    {
        return [
            'configCreate' => [
                'reqType' => 'POST',
                'path' => ['VisualPipeline', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'Creates the config entries for the given module',
                'longHelp' => 'include/api/help/module_config_post_help.html',
            ],
        ];
    }

    /**
     * The arguments represent only the user's changes to those modules
     * the user has access to. Any settings related to modules the user
     * does not have access to should be kept safe.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function configSave(ServiceBase $api, array $args)
    {
        $module = $args['module'];
        $admin = BeanFactory::newBean('Administration');
        $settings = $admin->getSettings($module, true)->settings;
        $collectionFields = [
            'table_header',
            'hidden_values',
            'tile_header',
            'tile_body_fields',
            'records_per_column',
            'available_columns',
            'show_column_count',
            'show_column_total',
            'total_field',
        ];

        foreach ($args['notAvailableModules'] as $naModule) {
            foreach ($collectionFields as $field) {
                $args[$field][$naModule] = $settings[$module . '_' . $field][$naModule];
            }
        }

        unset($args['notAvailableModules']);

        return parent::configSave($api, $args);
    }
}
