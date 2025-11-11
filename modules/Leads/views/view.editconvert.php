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


class ViewEditConvert extends SugarView
{
    /**
     * @var \ConvertLayoutMetadataParser|mixed
     */
    public $parser;
    public $defs;
    // @codingStandardsIgnoreStart
    protected $_viewdefs = [];
    // @codingStandardsIgnoreEnd

    protected $jsonHelper;

    public function __construct()
    {
        parent::__construct();
        global $current_user;
        if (!$current_user->isDeveloperForModule('Leads')) {
            die('Unauthorized Access to Administration');
        }

        $this->jsonHelper = getJSONobj();
        $this->parser = new ConvertLayoutMetadataParser('Contacts');

        if (isset($_REQUEST['updateConvertDef']) && $_REQUEST['updateConvertDef'] && !empty($_REQUEST['data'])) {
            $this->parser->updateConvertDef(
                object_to_array_recursive($this->jsonHelper->decode(html_entity_decode_utf8($_REQUEST['data'])))
            );
            // clear the cache for this module only
            MetaDataManager::refreshModulesCache(['Leads']);
        }
    }

    public function display()
    {
        $smarty = $this->constructSmarty();

        $ajax = new AjaxCompose();
        $ajax->addCrumb(
            translate('LBL_STUDIO', 'ModuleBuilder'),
            'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard")'
        );
        $ajax->addCrumb(
            translate('LBL_MODULE_NAME'),
            'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=Leads")'
        );
        $ajax->addCrumb(
            translate('LBL_LAYOUTS', 'ModuleBuilder'),
            'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=layouts&view_module=Leads")'
        );
        $ajax->addCrumb(translate('LBL_CONVERTLEAD'), '');
        $ajax->addSection('center', 'Convert Layout', $smarty->fetch('modules/Leads/tpls/EditConvertLead.tpl'));

        echo $ajax->getJavascript();
    }

    protected function constructSmarty()
    {
        $smarty = new Sugar_Smarty();
        $smarty->assign('translate', true);
        $smarty->assign('language', 'Leads');
        $smarty->assign('view_module', 'Leads');
        $smarty->assign('module', 'Leads');
        $smarty->assign('helpName', 'listViewEditor');
        $smarty->assign('helpDefault', 'modify');
        $smarty->assign('title', 'Convert Layout');
        $modules = $this->getModulesFromDefs();
        $smarty->assign('modules', $this->jsonHelper->encode($modules));
        $smarty->assign('usingOppsAndRlis', Opportunity::usingRevenueLineItems() ? 'true' : 'false');

        $relatableModules = DeployedRelationships::findRelatableModules();

        //pull out modules that have already been chosen
        foreach ($modules as $mDef) {
            if (isset($relatableModules[$mDef['module']])) {
                unset($relatableModules[$mDef['module']]);
            }
        }

        $displayModules = [];
        $moduleDefaults = [];
        foreach ($relatableModules as $mod => $def) {
            if ($this->parser->isModuleAllowedInConvert($mod)) {
                $displayModules[$mod] = translate($mod);
                $moduleDefaults[$mod] = $this->parser->getDefaultDefForModule($mod);
            }
        }
        foreach ($modules as $moduleDef) {
            $moduleName = $moduleDef['module'];
            $moduleDefaults[$moduleName] = $this->parser->getDefaultDefForModule($moduleName);
        }

        asort($displayModules);
        $smarty->assign('availableModules', $displayModules);
        $smarty->assign('moduleDefaults', $this->jsonHelper->encode($moduleDefaults));

        return $smarty;
    }

    protected function getModulesFromDefs()
    {
        global $app_list_strings;

        $modules = [];
        if (!isset($this->defs)) {
            $this->defs = $this->parser->getDefForModules();
        }
        foreach ($this->defs as $def) {
            $moduleDefs = [
                'module' => $def['module'],
                'moduleName' => $app_list_strings['moduleList'][$def['module']],
                'required' => $def['required'] ?? false,
                'copyData' => $def['copyData'] ?? false,
                'duplicateCheckOnStart' => $def['duplicateCheckOnStart'] ?? false,
            ];

            if ($def['module'] === 'Opportunities') {
                $additionalDefs = [
                    'enableRlis' => $def['enableRlis'] ?? false,
                    'requireRlis' => $def['requireRlis'] ?? false,
                    'copyDataToRlis' => $def['copyDataToRlis'] ?? false,
                ];

                $moduleDefs = array_merge($moduleDefs, $additionalDefs);
            }

            $modules[] = $moduleDefs;
        }
        return $modules;
    }
}
