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


class AdministrationViewEnablewirelessmodules extends SugarView
{
    /**
     * @see SugarView::preDisplay()
     */
    public function preDisplay()
    {
        if (!is_admin($GLOBALS['current_user'])) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
    }

    /**
     * @see SugarView::_getModuleTitleParams()
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getModuleTitleParams($browserTitle = false)
    {
        global $mod_strings;

        return [
            "<a href='#Administration'>" . $mod_strings['LBL_MODULE_NAME'] . '</a>',
            translate('LBL_WIRELESS_MODULES_ENABLE'),
        ];
    }

    /**
     * @see SugarView::display()
     */
    public function display()
    {
        require_once 'modules/Administration/Forms.php';

        global $mod_strings;
        global $app_list_strings;
        global $app_strings;
        global $license;

        $configurator = new Configurator();
        $this->ss->assign('config', $configurator->config);

        $enabled_modules = [];
        $disabled_modules = [];
        // The modules which should be enabled in Mobile for other functionalities to
        // work but not to show in Administration > Mobile
        $hide_modules = [
            'CJ_Forms',
            'CJ_WebHooks',
            'DRI_SubWorkflow_Templates',
            'DRI_Workflow_Task_Templates',
            'DRI_Workflow_Templates',
        ];

        // todo: should we move this config to another place?
        $wireless_not_supported_modules = [
            'Bugs',
            'Campaigns',
            'Prospects', // this is Targets
            'Users',
            'pmse_Business_Rules',
            'pmse_Emails_Templates',
            'pmse_Project',
            'DataPrivacy',
            'ProductBundles',
            'ProductTemplates',
            'Project',
            'ExternalUsers',
        ];

        // replicate the essential part of the behavior of the private loadMapping() method in SugarController
        foreach (SugarAutoLoader::existingCustom('include/MVC/Controller/wireless_module_registry.php') as $file) {
            require $file;
        }

        $moduleList = $GLOBALS['moduleList'];
        array_push($moduleList, 'Employees');

        foreach ($wireless_module_registry as $e => $def) {
            if (safeInArray($e, $moduleList) && !in_array($e, $wireless_not_supported_modules)) {
                $enabled_modules [$e] = empty($app_list_strings['moduleList'][$e]) ? $e : $app_list_strings['moduleList'][$e];
            }
        }

        // Employees should be in the mobile module list by default
        if (!empty($wireless_module_registry['Employees'])) {
            $enabled_modules ['Employees'] = $app_strings['LBL_EMPLOYEES'];
        }

        $browser = new StudioBrowser();
        $browser->loadModules();

        foreach ($browser->modules as $e => $def) {
            if (empty($enabled_modules[$e]) && safeInArray($e, $GLOBALS['moduleList']) &&
                !in_array($e, $wireless_not_supported_modules) && !in_array($e, $hide_modules)) {
                $disabled_modules[$e] = empty($app_list_strings['moduleList'][$e]) ? $e : ($app_list_strings['moduleList'][$e]);
            }
        }

        if (empty($wireless_module_registry['Employees'])) {
            $disabled_modules ['Employees'] = $app_strings['LBL_EMPLOYEES'];
        }

        // NOMAD-1793
        // Handling case when modules from initial wireless list are vanishing because they're not listed in studio browser.
        include 'include/MVC/Controller/wireless_module_registry.php';
        foreach ($wireless_module_registry as $moduleName => $def) {
            // not in any list
            if (empty($enabled_modules[$moduleName]) &&
                empty($disabled_modules[$moduleName]) &&
                safeInArray($moduleName, $GLOBALS['moduleList']) &&
                !in_array($moduleName, $wireless_not_supported_modules)) {
                // add module to disabled modules list
                $disabled_modules[$moduleName] = empty($app_list_strings['moduleList'][$moduleName]) ? $moduleName : ($app_list_strings['moduleList'][$moduleName]);
            }
        }

        $json_enabled = [];
        foreach ($enabled_modules as $mod => $label) {
            $json_enabled[] = ['module' => $mod, 'label' => $label];
        }

        $json_disabled = [];
        foreach ($disabled_modules as $mod => $label) {
            $json_disabled[] = ['module' => $mod, 'label' => $label];
        }

        // We need to grab the license key
        $key = $license->settings['license_key'];
        $this->ss->assign('url', '');

        $this->ss->assign('enabled_modules', json_encode($json_enabled));
        $this->ss->assign('disabled_modules', json_encode($json_disabled));
        $this->ss->assign('mod', $GLOBALS['mod_strings']);
        $this->ss->assign('APP', $GLOBALS['app_strings']);

        echo getClassicModuleTitle(
            'Administration',
            [
                "<a href='#Administration'>{$mod_strings['LBL_MODULE_NAME']}</a>",
                translate('LBL_WIRELESS_MODULES_ENABLE'),
            ],
            false
        );
        echo $this->ss->fetch('modules/Administration/templates/enableWirelessModules.tpl');
    }

    /**
     * Grab the mobile edge server link by polling the licensing server.
     * @returns string url
     * @deprecated
     */
    protected function getMobileEdgeUrl($license)
    {
        LoggerManager::getLogger()->deprecated('AdministrationViewEnablewirelessmodules::getMobileEdgeUrl is deprecated and will be removed.');
    }
}
