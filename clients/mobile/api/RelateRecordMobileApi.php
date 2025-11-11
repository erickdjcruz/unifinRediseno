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

class RelateRecordMobileApi extends RelateRecordApi
{
    /**
     * Loads the API implementation for the given module
     *
     * @param ServiceBase $api
     * @param string $module Relate module name
     *
     * @return ModuleApi Module API implementation
     */
    protected function loadModuleApi(ServiceBase $api, $module)
    {
        $mobileTemplates = [
            ['custom/modules/%s/clients/%s/api/', 'Custom%sMobileApi'],
            ['modules/%s/clients/%s/api/', '%sMobileApi'],
        ];

        $targetModuleApi = $this->loadTargetModuleApi($mobileTemplates, $api->platform, $module);

        if (!$targetModuleApi) {
            $baseTemplates = [
                ['custom/modules/%s/clients/%s/api/', 'Custom%sApi'],
                ['modules/%s/clients/%s/api/', '%sApi'],
            ];
            $defaultPlatform = 'base';

            $targetModuleApi = $this->loadTargetModuleApi($baseTemplates, $defaultPlatform, $module);
        }

        return $targetModuleApi;
    }

    /**
     * Loads target module api implementation
     *
     * @param array $templates
     * @param string $platform Platform type
     * @param string $module Relate module name
     *
     * @return ModuleApi Module API implementation
     */
    private function loadTargetModuleApi(array $templates, string $platform, string $module)
    {
        foreach ($templates as $template) {
            [$directoryTemplate, $classTemplate] = $template;
            $class = sprintf($classTemplate, $module);
            $file = sprintf($directoryTemplate, $module, $platform) . $class . '.php';

            if (file_exists($file)) {
                require_once $file;
                return new $class();
            }
        }

        return null;
    }
}
