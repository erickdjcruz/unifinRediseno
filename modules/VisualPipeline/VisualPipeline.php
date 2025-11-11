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

class VisualPipeline extends SugarBean
{
    public $object_name = 'VisualPipeline';
    public $module_name = 'VisualPipeline';
    public $module_dir = 'VisualPipeline';
    public $table_name = 'visual_pipeline';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var string[]
     */
    protected array $exclude_modules = [
        'Home',
        'Calendar',
        'Forecasts',
        'pmse_Business_Rules',
        'pmse_Emails_Templates',
        'pmse_Inbox',
        'pmse_Project',
    ];

    /**
     * Check if a given module is configured to have tile view enabled
     *
     * @param string $moduleName
     * @return bool
     */
    public function isEnabledForModule(string $moduleName): bool
    {
        $enabledModules = $this->getEnabledModules();
        return safeInArray($moduleName, $enabledModules);
    }

    /**
     * Fetch the modules for which Visual Pipeline is enabled
     * @return array
     */
    public function getEnabledModules(): array
    {
        /* @var $admin Administration */
        $admin = BeanFactory::newBean('Administration');
        return $admin->getConfigForModule('VisualPipeline')['enabled_modules'] ?? [];
    }

    /**
     * Check if module is in exclude list
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModulePipelineExcluded(string $moduleName): bool
    {
        return in_array($moduleName, $this->exclude_modules);
    }
}
