<?php

use Sugarcrm\Sugarcrm\Security\Validator\Constraints\ComponentName;

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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

require_once 'modules/ModuleBuilder/parsers/constants.php';

class ViewPopupview extends ViewListView
{
    public $editPackage;
    public $searchlayout;

    public function __construct()
    {
        parent::__construct();
        $this->editModule = $this->request->getValidInputRequest('view_module', 'Assert\ComponentName');
        if (!AccessControlManager::instance()->allowModuleAccess($this->editModule)) {
            throw new SugarApiExceptionModuleDisabled();
        }

        $this->editLayout = $this->request->getValidInputRequest('view', 'Assert\ComponentName');
        $this->editPackage = $this->request->getValidInputRequest('view_package', 'Assert\ComponentName');

        $this->fromModuleBuilder = isset($_REQUEST['MB']) || (!empty($this->editPackage) && $this->editPackage != 'studio');
        if (!$this->fromModuleBuilder) {
            global $app_list_strings;
            $moduleNames = array_change_key_case($app_list_strings ['moduleList']);
            $this->translatedEditModule = $moduleNames [strtolower($this->editModule)];
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
            translate('LBL_MODULE_NAME', 'Administration'),
            ModuleBuilderController::getModuleTitle(),
        ];
    }

    /**
     * {@inheritDoc}
     * Pseudo-constructor to enable subclasses to call a parent's constructor without knowing the parent in PHP4
     *
     * @param SugarBean $bean Ignored
     * @param array $view_object_map Ignored
     */
    public function init($bean = null, $view_object_map = [])
    {
    }

    public function preDisplay()
    {
    }

    public function display(
        $preview = false
    ) {


        $parser = ParserFactory::getParser($this->editLayout, $this->editModule, $this->editPackage);

        $smarty = $this->constructSmarty($parser);
        if ($preview) {
            echo $smarty->fetch('modules/ModuleBuilder/tpls/Preview/listView.tpl');
        } else {
            $ajax = $this->constructAjax();
            $ajax->addSection('center', translate('LBL_POPUP'), $smarty->fetch('modules/ModuleBuilder/tpls/listView.tpl'));
            echo $ajax->getJavascript();
        }
    }

    public function constructAjax()
    {
        $ajax = new AjaxCompose();

        if ($this->fromModuleBuilder) {
            $ajax->addCrumb(translate('LBL_MODULEBUILDER', 'ModuleBuilder'), 'ModuleBuilder.main("mb")');
            $ajax->addCrumb($this->editPackage, 'ModuleBuilder.getContent("module=ModuleBuilder&action=package&package=' . $this->editPackage . '")');
            $ajax->addCrumb($this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_package=' . $this->editPackage . '&view_module=' . $this->editModule . '")');
            $ajax->addCrumb(translate('LBL_LAYOUTS', 'ModuleBuilder'), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&MB=1&view_package=' . $this->editPackage . '&view_module=' . $this->editModule . '")');
            $ajax->addCrumb(translate('LBL_POPUP', 'ModuleBuilder'), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=popup&MB=1&view_package=' . $this->editPackage . '&view_module=' . $this->editModule . '")');

            $ViewLabel = ($this->editLayout == MB_POPUPSEARCH) ? 'LBL_POPUPSEARCH' : 'LBL_POPUPLISTVIEW';
            $ajax->addCrumb(translate($ViewLabel, 'ModuleBuilder'), '');
        } else {
            $ajax->addCrumb(translate($this->editModule), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")');
            $ajax->addCrumb(translate('LBL_LAYOUTS', 'ModuleBuilder'), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->editModule . '")');
            $ajax->addCrumb(translate('LBL_POPUP', 'ModuleBuilder'), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view=popup&view_module=' . $this->editModule . '")');

            $ViewLabel = ($this->editLayout == MB_POPUPSEARCH) ? 'LBL_POPUPSEARCH' : 'LBL_POPUPLISTVIEW';
            $ajax->addCrumb(translate($ViewLabel, 'ModuleBuilder'), '');
        }
        return $ajax;
    }

    public function constructSmarty(
        $parser
    ) {


        $smarty = new Sugar_Smarty();
        $smarty->assign('translate', true);
        $smarty->assign('language', $parser->getLanguage());

        $smarty->assign('view', $this->editLayout);
        $smarty->assign('action', 'popupSave');
        $smarty->assign('module', 'ModuleBuilder');
        $smarty->assign('view_module', $this->editModule);
        $smarty->assign('field_defs', $parser->getFieldDefs());
        $helpName = (isset($_REQUEST['view']) && $_REQUEST['view'] == MB_POPUPSEARCH) ? 'searchViewEditor' : 'popupListViewEditor';
        $smarty->assign('helpName', $helpName);
        $smarty->assign('helpDefault', 'modify');
        if ($this->fromModuleBuilder) {
            $mb = new ModuleBuilder();
            $module = &$mb->getPackageModule($this->editPackage, $this->editModule);
            $smarty->assign('current_mod_strings', $module->getModStrings());
        }

        $smarty->assign('title', $this->_constructTitle());
        $groups = [];
        foreach ($parser->columns as $column => $function) {
            $groups [$GLOBALS ['mod_strings'] [$column]] = $parser->$function();
        }
        foreach ($groups as $groupKey => $group) {
            foreach ($group as $fieldKey => $field) {
                if (isset($field ['width'])) {
                    if (substr($field ['width'], -1, 1) == '%') {
                        $groups [$groupKey] [$fieldKey] ['width'] = substr($field ['width'], 0, strlen($field ['width']) - 1);
                    }
                }
                if ($groupKey === 'Hidden' &&
                    !AccessControlManager::instance()->allowFieldAccess($this->editModule, $fieldKey)) {
                    unset($groups[$groupKey][$fieldKey]);
                }
            }
        }

        $smarty->assign('groups', $groups);

        global $image_path, $mod_strings;
        $imageSave = SugarThemeRegistry::current()->getImage('studio_save', '', null, null, '.gif', $mod_strings['LBL_BTN_SAVE']);


        $histaction = "ModuleBuilder.history.browse(\"{$this->editModule}\", \"{$this->editLayout}\")";
        if (isset($this->searchlayout)) {
            $histaction = "ModuleBuilder.history.browse(\"{$this->editModule}\", \"{$this->editLayout}\", \"{$this->searchlayout}\")";
        }

        $smarty->assign(
            'onsubmit',
            'studiotabs.generateGroupForm("edittabs");' .
            'ModuleBuilder.state.markAsClean();ModuleBuilder.submitForm("edittabs"); return false;'
        );

        $buttons = [];
        if (!$this->fromModuleBuilder) {
            $buttons[] = [
                'name' => 'savebtn',
                'image' => $imageSave,
                'text' => $GLOBALS['mod_strings']['LBL_BTN_SAVEPUBLISH'],
                'type' => 'submit',
            ];
        } else {
            $buttons[] = [
                'name' => 'mbsavebtn',
                'image' => $imageSave,
                'text' => $GLOBALS['mod_strings']['LBL_BTN_SAVE'],
                'type' => 'submit',
            ];
        }
        $buttons [] = ['name' => 'historyBtn', 'text' => translate('LBL_HISTORY'), 'actionScript' => "onclick='$histaction'"];
        $smarty->assign('buttons', $this->_buildImageButtons($buttons));
        $editImage = SugarThemeRegistry::current()->getImage('edit_inline', '', null, null, '.gif', $mod_strings['LBL_EDIT']);

        $smarty->assign('editImage', $editImage);
        $deleteImage = SugarThemeRegistry::current()->getImage('delete_inline', '', null, null, '.gif', $mod_strings['LBL_MB_DELETE']);

        $smarty->assign('deleteImage', $deleteImage);
        $smarty->assign('MOD', $GLOBALS ['mod_strings']);

        if ($this->fromModuleBuilder) {
            $smarty->assign('MB', true);
            $smarty->assign('view_package', $this->editPackage);
            $smarty->assign('description', $GLOBALS ['mod_strings'] ['LBL_LISTVIEW_DESCRIPTION']);
        } else {
            $smarty->assign('description', $GLOBALS ['mod_strings'] ['LBL_LISTVIEW_DESCRIPTION']);
        }

        return $smarty;
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _constructTitle()
    {

        global $app_list_strings;

        if ($this->fromModuleBuilder) {
            $title = $this->editModule;
        } else {
            $title = $app_list_strings ['moduleList'] [$this->editModule];
        }
        return $GLOBALS ['mod_strings'] ['LBL_LISTVIEW_EDIT'] . ':&nbsp;' . $title;
    }
}
