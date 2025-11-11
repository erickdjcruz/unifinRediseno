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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

require_once 'modules/ModuleBuilder/parsers/constants.php';

class ViewLayoutView extends SugarView
{
    /**
     * @var mixed
     */
    public $editModule;
    /**
     * @var mixed|mixed[]
     */
    public $editLayout;
    public $package;
    /**
     * @var bool|mixed
     */
    public $fromModuleBuilder;
    public $translatedEditModule;
    public $sm;
    /** @var GridLayoutMetaDataParser */
    protected $parser;

    /**
     * @var string
     */
    protected $existingLayout;

    /**
     * @var bool
     */
    protected $warnSave = false;

    public function __construct($bean = null, $view_object_map = [], $request = null)
    {
        parent::__construct($bean, $view_object_map, $request);
        $GLOBALS ['log']->debug('in ViewLayoutView');
        $this->editModule = $this->request->getValidInputRequest('view_module', 'Assert\ComponentName');
        if (!AccessControlManager::instance()->allowModuleAccess($this->editModule)) {
            throw new SugarApiExceptionModuleDisabled();
        }
        $this->editLayout = $this->request->getValidInputRequest('view', 'Assert\ComponentName');
        $this->package = $this->request->getValidInputRequest('view_package', 'Assert\ComponentName');
        $mb = $this->request->getValidInputRequest('MB');
        $this->fromModuleBuilder = !is_null($mb) || !empty($this->package);
        if ($this->fromModuleBuilder) {
            $this->type = $this->editLayout;
        } else {
            global $app_list_strings;
            $moduleNames = array_change_key_case($app_list_strings ['moduleList']);
            $this->translatedEditModule = $moduleNames [strtolower($this->editModule)];
            $this->sm = StudioModuleFactory::getStudioModule($this->editModule);
            $this->type = $this->sm->getViewType($this->editLayout);
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

    // DO NOT REMOVE - overrides parent ViewEdit preDisplay() which attempts to load a bean for a non-existent module
    public function preDisplay()
    {
    }

    public function display($preview = false)
    {
        global $mod_strings;
        $params = [];
        $role = $this->request->getValidInputRequest('role', 'Assert\Guid');
        if (!empty($role)) {
            $params['role'] = $role;
        }
        $layoutOption = $this->request->getValidInputRequest('layoutOption');
        if (!empty($layoutOption)) {
            $params['layoutOption'] = $layoutOption;
        }
        $dropdownField = $this->request->getValidInputRequest('dropdownField');
        if (!empty($dropdownField)) {
            $params['dropdownField'] = $dropdownField;
        }
        $dropdownValue = $this->request->getValidInputRequest('dropdownValue');
        if (!empty($dropdownValue)) {
            $params['dropdownValue'] = $dropdownValue;
        }
        // The first time the layout is opened, $layoutOption and $dropdownField will be null and in that case check
        // if there already exist custom layout on specific base, to preselect values on the dropdown
        $params = array_merge($params, $this->checkExistingCustomLayouts($params));
        if (!empty($params['layoutOption']) && empty($layoutOption)) {
            $layoutOption = $params['layoutOption'];
        }
        if (!empty($params['dropdownField']) && empty($dropdownField)) {
            $dropdownField = $params['dropdownField'];
        }
        // When there is already a custom record.php existing and the layout type is changed from role-dropdown based
        // or vice-versa, the user will be warned while saving and delete any existing layouts when user confirms
        if (!empty($this->existingLayout) && $layoutOption !== $this->existingLayout) {
            $this->warnSave = true;
        }

        $resetToBase = $this->request->getValidInputRequest('resetToBase');
        // Resetting to base on a particular role-id/dropdown field-value will remove the custom record.php
        if (!empty($resetToBase) && $resetToBase === 'true') {
            if (!empty($params['layoutOption']) && $params['layoutOption'] === 'role') {
                $this->deleteExistingCustomLayout($params['layoutOption'], [$params['role']]);
            } elseif ($params['layoutOption'] === 'dropdown') {
                $this->deleteExistingCustomLayout($params['layoutOption'], [$params['dropdownField'], $params['dropdownValue']]);
            }
        }
        $this->parser = $parser = ParserFactory::getParser(
            $this->editLayout,
            $this->editModule,
            $this->package,
            null,
            null,
            $params
        );
        $history = $parser->getHistory();
        $smarty = $this->getSmarty();
        //Add in the module we are viewing to our current mod strings
        if (!$this->fromModuleBuilder) {
            global $current_language;
            $editModStrings = return_module_language($current_language, $this->editModule);
            $mod_strings = sugarArrayMerge($editModStrings, $mod_strings);
        }
        $smarty->assign('mod', $mod_strings);
        $smarty->assign('MOD', $mod_strings);
        // assign buttons
        $images = ['icon_save' => 'studio_save', 'icon_publish' => 'studio_publish', 'icon_address' => 'icon_Address', 'icon_emailaddress' => 'icon_EmailAddress', 'icon_phone' => 'icon_Phone'];
        foreach ($images as $image => $file) {
            $smarty->assign($image, SugarThemeRegistry::current()->getImage($file, '', null, null, '.gif', $file));
        }

        $requiredFields = implode(',', $parser->getRequiredFields());
        $buttons = [];
        $disableLayout = false;
        $layoutButtons = [];

        if ($preview) {
            $smarty->assign('layouttitle', translate('LBL_LAYOUT_PREVIEW', 'ModuleBuilder'));
        } else {
            $smarty->assign('layouttitle', translate('LBL_CURRENT_LAYOUT', 'ModuleBuilder'));

            //Check if we need to synch edit view to other layouts
            if ($this->editLayout == MB_DETAILVIEW || $this->editLayout == MB_QUICKCREATE) {
                $parser2 = ParserFactory::getParser(MB_EDITVIEW, $this->editModule, $this->package);
                if ($this->editLayout == MB_DETAILVIEW) {
                    $disableLayout = $parser2->getSyncDetailEditViews();
                }

                $copyFromEditView = $this->request->getValidInputRequest('copyFromEditView');
                if (!empty($copyFromEditView)) {
                    $editViewPanels = $parser2->convertFromCanonicalForm($parser2->_viewdefs['panels']);
                    $parser->_viewdefs ['panels'] = $editViewPanels;
                    $parser->_fielddefs = $parser2->_fielddefs;
                    $parser->setUseTabs($parser2->getUseTabs());
                    $parser->setTabDefs($parser2->getTabDefs());
                }
            }

            $buttons = $this->getButtons($history, $disableLayout, $params);

            $layoutButtons = $this->getLayoutButtons($params);
            $implementation = $parser->getImplementation();
            $roles = $this->getRoleList($implementation);
            if (!empty($params['layoutOption']) && $params['layoutOption'] === 'dropdown') {
                $dropdownWithMetadata = $this->getDropdownWithMetadata($implementation, $params);
                $copyFromOptions = !empty($dropdownWithMetadata['resultsForCopy']) ? $dropdownWithMetadata['resultsForCopy'] : [];
            } else {
                $rolesWithMetadata = $this->getRoleListWithMetadata($roles, $role);
                $copyFromOptions = !empty($rolesWithMetadata['resultsForCopy']) ? $rolesWithMetadata['resultsForCopy'] : [];
            }
            $smarty->assign('copy_from_options', $copyFromOptions);
        }

        $available_fields = $parser->getAvailableFields();
        $field_defs = $parser->getFieldDefs();
        $baseViewFields = $parser->getBaseViewFields();

        foreach ($available_fields as $key => $value) {
            if (isset($field_defs[$value['name']]['studio']) && $field_defs[$value['name']]['studio'] === false && !isset($baseViewFields[$value['name']]) ||
                !AccessControlManager::instance()->allowFieldAccess($this->editModule, $value['name'])) {
                unset($available_fields[$key]);
            }
        }

        $smarty->assign('buttons', $this->getButtonHTML($buttons));
        $smarty->assign('layoutButtons', $this->getButtonHTML($layoutButtons));

        // assign fields and layout
        $smarty->assign('available_fields', $available_fields);

        $smarty->assign('disable_layout', $disableLayout);
        $smarty->assign('required_fields', $requiredFields);
        $smarty->assign('layout', $parser->getLayout());
        $smarty->assign('field_defs', $field_defs);
        $smarty->assign('view_module', $this->editModule);
        $smarty->assign('view', $this->editLayout);
        $smarty->assign('selected_role', $role);
        $smarty->assign('selected_layoutOption', $layoutOption);
        $smarty->assign('selected_dropdownField', $dropdownField);
        $smarty->assign('selected_dropdownValue', $dropdownValue);
        $smarty->assign('maxColumns', $parser->getMaxColumns());
        $smarty->assign('nextPanelId', $parser->getFirstNewPanelId());
        $smarty->assign('displayAsTabs', $parser->getUseTabs());
        $smarty->assign('tabDefs', $parser->getTabDefs());
        // no tabs and collapse for preview
        if ($this->editLayout === MB_PREVIEWVIEW) {
            $smarty->assign('no_tabs', true);
            $smarty->assign('no_collapse', true);
        }
        $smarty->assign('syncDetailEditViews', $parser->getSyncDetailEditViews());
        $smarty->assign('fieldwidth', 300 / $parser->getMaxColumns());
        // Bug 57260 - LBL_PANEL_DEFAULT not translated for undeployed modules in layout editor
        $smarty->assign('translate', true);

        if ($this->fromModuleBuilder) {
            $smarty->assign('fromModuleBuilder', $this->fromModuleBuilder);
            $smarty->assign('view_package', $this->package);
        }

        // Layout labels for the breadcrumb
        $labels = [
            MB_EDITVIEW => 'LBL_EDITVIEW',
            MB_DETAILVIEW => 'LBL_DETAILVIEW',
            MB_QUICKCREATE => 'LBL_QUICKCREATE',
            MB_RECORDVIEW => 'LBL_RECORDVIEW',
            MB_WIRELESSEDITVIEW => 'LBL_WIRELESSEDITVIEW',
            MB_WIRELESSDETAILVIEW => 'LBL_WIRELESSDETAILVIEW',
        ];

        $layoutLabel = 'LBL_LAYOUTS';
        $layoutView = 'layouts';

        if (in_array($this->editLayout, [MB_WIRELESSEDITVIEW, MB_WIRELESSDETAILVIEW])) {
            $layoutLabel = 'LBL_WIRELESSLAYOUTS';
            $layoutView = 'wirelesslayouts';
            $smarty->assign('wireless', true);
        }

        $ajax = new AjaxCompose();

        $translatedViewType = '';
        if (isset($labels [strtolower($this->editLayout)])) {
            $translatedViewType = translate($labels [strtolower($this->editLayout)], 'ModuleBuilder');
        } elseif (isset($this->sm)) {
            foreach ($this->sm->sources as $file => $def) {
                if (!empty($def['view']) && $def['view'] == $this->editLayout && !empty($def['name'])) {
                    $translatedViewType = $def['name'];
                }
            }
            if (empty($translatedViewType)) {
                $label = 'LBL_' . strtoupper($this->editLayout);
                $translated = translate($label, $this->editModule);
                if ($translated != $label) {
                    $translatedViewType = $translated;
                }
            }
        }

        if ($this->fromModuleBuilder) {
            $ajax->addCrumb(translate('LBL_MODULEBUILDER', 'ModuleBuilder'), 'ModuleBuilder.main("mb")');
            $ajax->addCrumb(
                $this->package,
                'ModuleBuilder.getContent("module=ModuleBuilder&action=package&package=' . $this->package . '")'
            );
            $ajax->addCrumb(
                $this->editModule,
                'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_package='
                . $this->package . '&view_module=' . $this->editModule . '")'
            );
            $ajax->addCrumb(
                translate($layoutLabel, 'ModuleBuilder'),
                'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view='
                . $layoutView . '&view_module=' . $this->editModule . '&view_package=' . $this->package . '")'
            );
            $ajax->addCrumb($translatedViewType, '');
        } else {
            $ajax->addCrumb(translate('LBL_STUDIO', 'ModuleBuilder'), 'ModuleBuilder.main("studio")');
            $ajax->addCrumb(
                $this->translatedEditModule,
                'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")'
            );
            $ajax->addCrumb(
                translate($layoutLabel, 'ModuleBuilder'),
                'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view='
                . $layoutView . '&view_module=' . $this->editModule . '")'
            );
            $ajax->addCrumb($translatedViewType, '');
        }

        // set up language files
        $smarty->assign('language', $parser->getLanguage()); // for sugar_translate in the smarty template
        $smarty->assign('from_mb', $this->fromModuleBuilder);
        $smarty->assign('calc_field_list', json_encode($parser->getCalculatedFields()));
        if ($this->fromModuleBuilder) {
            $mb = new ModuleBuilder();
            $module = &$mb->getPackageModule($this->package, $this->editModule);
            $smarty->assign('current_mod_strings', $module->getModStrings());
        }

        $ajax->addSection(
            'center',
            $translatedViewType,
            $smarty->fetch(SugarAutoLoader::existingCustomOne('modules/ModuleBuilder/tpls/layoutView.tpl'))
        );
        if ($preview) {
            echo $smarty->fetch('modules/ModuleBuilder/tpls/Preview/layoutView.tpl');
        } else {
            echo $ajax->getJavascript();
        }
    }

    /**
     * @return Sugar_Smarty
     */
    protected function getSmarty()
    {
        if (is_null($this->ss)) {
            $this->ss = new Sugar_Smarty();
        }
        return $this->ss;
    }

    protected function getButtons($history, $disableLayout, $params)
    {
        $buttons = [];
        if (!$this->fromModuleBuilder) {
            $buttons [] = [
                'id' => 'saveBtn',
                'text' => translate('LBL_BTN_SAVE'),
                'actionScript' => "onclick='if(Studio2.checkGridLayout(\"{$this->editLayout}\")) Studio2.handleSaveWarn({$this->warnSave});'",
                'disabled' => $disableLayout,
            ];
            $buttons [] = [
                'id' => 'publishBtn',
                'text' => translate('LBL_BTN_SAVEPUBLISH'),
                'actionScript' => "onclick='if(Studio2.checkGridLayout(\"{$this->editLayout}\")) Studio2.handlePublishWarn({$this->warnSave});'",
                'disabled' => $disableLayout,
            ];
        } else {
            $buttons [] = [
                'id' => 'saveBtn',
                'text' => $GLOBALS ['mod_strings'] ['LBL_BTN_SAVE'],
                'actionScript' => "onclick='if(Studio2.checkGridLayout(\"{$this->editLayout}\")) Studio2.handlePublish();'",
                'disabled' => $disableLayout,
            ];
        }
        $buttons [] = ['id' => 'spacer', 'width' => '33px'];
        $buttons [] = [
            'id' => 'historyBtn',
            'text' => translate('LBL_HISTORY'),
            'actionScript' => "onclick='ModuleBuilder.history.browse(\"{$this->editModule}\", \"{$this->editLayout}\")'",
            'disabled' => $disableLayout,
        ];

        if (!$params) {
            $action = 'ModuleBuilder.history.revert('
                . '"' . $this->editModule . '",'
                . '"' . $this->editLayout . '",'
                . '"' . $history->getLast() . '",'
                . '""'
                . ')';
        } else {
            $action = 'ModuleBuilder.history.resetToDefault('
                . '"' . $this->editModule . '",'
                . '"' . $this->editLayout . '"'
                . ')';
        }

        $restoreDefaultDisabled = $disableLayout;

        // Handle Opps+RLI mode switch creating one history item on install.
        if ($this->editModule == 'Opportunities') {
            if (empty($GLOBALS['sugar_config']['roleBasedViews']) || empty($params) || empty($params['role'])) {
                if ($history->getCount() == 1) {
                    $restoreDefaultDisabled = true;
                } elseif ($history->getCount() > 1) {
                    $historyList = $history->getList();
                    $historyItem = $historyList[1];

                    $action = 'ModuleBuilder.history.revert('
                        . '"' . $this->editModule . '",'
                        . '"' . $this->editLayout . '",'
                        . '"' . $historyItem . '",'
                        . '""'
                        . ')';
                }
            }
        }

        $buttons [] = [
            'id' => 'historyDefault',
            'text' => translate('LBL_RESTORE_DEFAULT_LAYOUT'),
            'actionScript' => "onclick='$action'",
            'disabled' => $restoreDefaultDisabled,
        ];
        if ($this->editLayout == MB_DETAILVIEW || $this->editLayout == MB_QUICKCREATE) {
            $buttons [] = [
                'id' => 'copyFromEditView',
                'text' => translate('LBL_COPY_FROM_EDITVIEW'),
                'actionScript' => "onclick='ModuleBuilder.copyFromView(\"{$this->editModule}\", \"{$this->editLayout}\")'",
                'disabled' => $disableLayout,
            ];
        }
        return $buttons;
    }


    /**
     * Check if role based or dropdown based layouts already exist
     *
     * @param array $params
     * @return mixed
     */
    protected function checkExistingCustomLayouts($params)
    {
        $folder = [
            'custom',
            'modules',
            $this->editModule,
            'clients',
            MetaDataFiles::getViewClient($this->editLayout),
            'views',
            MetaDataFiles::$names[$this->editLayout],
        ];
        $folderRole = implode('/', $folder) . '/roles';
        $isRoleTrue = is_dir($folderRole);

        $folderDropdown = implode('/', $folder) . '/dropdowns';
        $isDropdownTrue = is_dir($folderDropdown);

        if ($isDropdownTrue && $isRoleTrue) {
            $this->existingLayout = $params['layoutOption'] === 'role' ? 'dropdown' : 'role';
        } elseif ($isRoleTrue) {
            $this->existingLayout = 'role';
        } elseif ($isDropdownTrue) {
            $this->existingLayout = 'dropdown';
        }
        // Determining if a specific role based/dropdown based layout already exist. Preselect values accordingly
        if (empty($params['layoutOption'])) {
            $params['layoutOption'] = $this->existingLayout ?: 'std';
        }
        // Similarly checking if there are existing dropdown field custom records, when there are multiple
        // preselect the first
        if ($params['layoutOption'] === 'dropdown' && empty($params['dropdownField'])) {
            $dropdownFields = $this->getDropdownFields();
            $params['dropdownField'] = $dropdownFields[0]['name'];
            foreach ($dropdownFields as $field) {
                $isEmpty = true;
                if (is_dir($folderDropdown . '/' . $field['name'])) {
                    $ddFiles = scandir($folderDropdown . '/' . $field['name']);
                    $isEmpty = (safeCount($ddFiles) === 2);
                }
                if (!$isEmpty) {
                    $params['dropdownField'] = $field['name'];
                    break;
                }
            }
        }
        return $params;
    }

    /**
     * Delete a specific custom layout (role base/dropdown based only)
     *
     * @param string $layoutType
     * @param array $layoutPath
     */
    protected function deleteExistingCustomLayout($layoutType, $layoutPath = [])
    {
        if ($layoutType === 'role') {
            $layoutType = 'roles';
        } elseif ($layoutType === 'dropdown') {
            $layoutType = 'dropdowns';
        }
        $folder = [
            'custom',
            'modules',
            $this->editModule,
            'clients',
            MetaDataFiles::getViewClient($this->editLayout),
            'views',
            MetaDataFiles::$names[$this->editLayout],
            $layoutType,
        ];
        $dir = implode('/', array_merge($folder, $layoutPath));
        rmdir_recursive($dir);
    }

    /**
     * Generate the buttons for role and dropdown based layouts
     *
     * @param array $params
     * @return array
     */
    protected function getLayoutButtons($params)
    {
        global $mod_strings;
        $buttons = [];
        $implementation = $this->parser->getImplementation();
        if (!empty($GLOBALS['sugar_config']['roleBasedViews'])
            && !isModuleBWC($this->editModule)
            && (
                $this->editLayout == MB_RECORDVIEW
                || $this->editLayout == MB_RECORDDASHLETVIEW
                || $this->editLayout == MB_WIRELESSEDITVIEW
                || $this->editLayout == MB_WIRELESSDETAILVIEW
                || $this->editLayout == MB_PREVIEWVIEW
            )
            && $implementation->isDeployed()) {
            $availableRoles = $this->getRoleList($implementation);

            $buttons [] = [
                'id' => 'layoutList',
                'type' => 'enum',
                'actionScript' => 'style="max-width:150px" onchange="ModuleBuilder.switchLayout(this,\'layoutOption\')"',
                'options' => $mod_strings['layoutDeterminedBy'],
                'selected' => $params['layoutOption'],
                'label' => translate('LBL_LAYOUT_DETERMINED_BY'),
            ];
            if (!empty($params['layoutOption']) && $params['layoutOption'] === 'role') {
                $buttons [] = ['type' => 'spacer', 'width' => '33px'];
                $buttons [] = [
                    'id' => 'roleList',
                    'type' => 'enum',
                    'actionScript' => 'style="max-width:150px" onchange="ModuleBuilder.switchLayout(this,\'role\')"',
                    'options' => $this->getAvailableRoleList($implementation),
                    'selected' => empty($params['role']) ? '' : $params['role'],
                    'label' => translate('LBL_ROLE'),
                ];
            } elseif (!empty($params['layoutOption']) && $params['layoutOption'] === 'dropdown') {
                $dropdownFields = $this->getDropdownFields();
                $fieldNames = [];
                foreach ($dropdownFields as $field) {
                    $fieldNames[$field['name']] = translate($field['vname']);
                }
                $buttons [] = ['type' => 'spacer', 'width' => '33px'];
                $buttons [] = [
                    'id' => 'dropdownFields',
                    'type' => 'enum',
                    'actionScript' => 'style="max-width:150px" onchange="ModuleBuilder.switchLayout(this,\'dropdownField\')"',
                    'options' => $fieldNames,
                    'selected' => empty($params['dropdownField']) ? '' : $params['dropdownField'],
                    'label' => translate('LBL_FIELD_NAME'),
                ];
                $buttons [] = ['type' => 'spacer', 'width' => '10px'];
                if (!empty($params['dropdownField'])) {
                    $fieldOptions = $this->getDropdownValuesList($implementation, $params, $dropdownFields);
                } else {
                    $fieldOptions = ['' => translate('LBL_BASE_LAYOUT')];
                }
                $buttons [] = [
                    'id' => 'dropdownValues',
                    'type' => 'enum',
                    'actionScript' => 'style="max-width:150px" onchange="ModuleBuilder.switchLayout(this,\'dropdownValue\')"',
                    'options' => $fieldOptions,
                    'selected' => empty($params['dropdownValue']) ? '' : $params['dropdownValue'],
                    'label' => translate('LBL_FIELD_VALUE'),
                ];
            }
            $layoutWithMetadata = [];
            if (!empty($params['layoutOption']) && $params['layoutOption'] === 'role' && !empty($params['role'])) {
                $layoutWithMetadata = $this->getRoleListWithMetadata($availableRoles, $params['role']);
            }
            if (!empty($params['layoutOption']) && $params['layoutOption'] === 'dropdown' && !empty($params['dropdownValue'])) {
                $layoutWithMetadata = $this->getDropdownWithMetadata($implementation, $params);
            }
            if (!empty($params['layoutOption']) && $params['layoutOption'] !== 'std') {
                $buttons [] = ['type' => 'spacer', 'width' => '10px'];
                $resetDisabled = !(isset($layoutWithMetadata['resultForReset']) &&
                    safeCount($layoutWithMetadata['resultForReset']));
                $copyDisabled = !(isset($layoutWithMetadata['resultsForCopy']) &&
                    safeCount($layoutWithMetadata['resultsForCopy']));

                $buttons [] = [
                    'id' => 'resetToBase',
                    'text' => translate('LBL_BTN_RESTORE_BASE_LAYOUT'),
                    'actionScript' => "onclick='ModuleBuilder.resetToBase();'",
                    'disabled' => $resetDisabled,
                ];
                $buttons [] = [
                    'id' => 'copyBtn',
                    'text' => translate('LBL_BTN_COPY_FROM'),
                    'actionScript' => "onclick='ModuleBuilder.copyLayoutFromRole();'",
                    'disabled' => $copyDisabled,
                ];
            }
        }
        return $buttons;
    }


    protected function getButtonHTML(array $buttons)
    {
        $html = '';
        $html .= '<tr>';
        foreach ($buttons as $button) {
            if (isset($button['label'])) {
                $html .= "<td><span class='label'>{$button['label']}</span></td>";
            }
            if ((isset($button['id']) && $button['id'] == 'spacer') ||
                (isset($button['type']) && $button['type'] == 'spacer')
            ) {
                $html .= "<td style='width:{$button['width']}'> </td>";
            }
        }
        $html .= '</tr><tr>';
        foreach ($buttons as $button) {
            if ((isset($button['id']) && $button['id'] == 'spacer') ||
                (isset($button['type']) && $button['type'] == 'spacer')
            ) {
                $html .= "<td style='width:{$button['width']}'> </td>";
            } elseif (isset($button['type']) && $button['type'] == 'enum') {
                $button['actionScript'] = empty($button['actionScript']) ? '' : $button['actionScript'];
                $html .= "<td><select id={$button['id']} {$button['actionScript']}>"
                    . get_select_options_with_id(
                        $button['options'],
                        $button['selected']
                    ) . '</select></td>';
            } elseif (isset($button['type']) && $button['type'] == 'label') {
                $html .= "<td><span class='label'>{$button['text']}</span></td>";
            } else {
                $html .= "<td><input id='{$button['id']}' type='button' valign='center' class='button' style='cursor:pointer' "
                    . "onmousedown='this.className=\"buttonOn\";return false;' onmouseup='this.className=\"button\"' "
                    . "onmouseout='this.className=\"button\"' {$button['actionScript']} value = '{$button['text']}'";
                if (!empty($button['disabled'])) {
                    $html .= ' disabled';
                }
                $html .= '></td>';
            }
        }
        $html .= '</tr>';
        return $html;
    }


    /**
     * Returns object storage containing available roles as keys
     * and flags indicating if there is role specific metadata as value
     *
     * @param MetaDataImplementationInterface $implementation
     * @return SplObjectStorage
     */
    protected function getRoleList(MetaDataImplementationInterface $implementation)
    {
        return MBHelper::getRoles($this->getHasMetaCallback($implementation));
    }

    /**
     * Returns list of roles with marker indicating whether role specific metadata exists
     *
     * @param MetaDataImplementationInterface $implementation
     * @return array
     */
    protected function getAvailableRoleList(MetaDataImplementationInterface $implementation)
    {
        return MBHelper::getAvailableRoleList($this->getHasMetaCallback($implementation));
    }

    /**
     * Returns object storage containing available dropdown values as keys
     * and flags indicating if there is dropdown value specific metadata as value
     *
     * @param MetaDataImplementationInterface $implementation
     * @param array $params
     * @param array $dropdownFields
     * @return array
     */
    protected function getDropdownValuesList(MetaDataImplementationInterface $implementation, $params, $dropdownFields)
    {
        return MBHelper::getAvailableDropdownValuesList($this->getHasMetaCallback($implementation), $params, $dropdownFields);
    }

    /**
     * Returns list of dropdowns which have role specific metadata
     *
     * @param MetaDataImplementationInterface $implementation
     * @param array $params
     * @return array
     */
    protected function getDropdownWithMetadata(MetaDataImplementationInterface $implementation, $params)
    {
        return MBHelper::getDropdownValueWithMetadata($this->getHasMetaCallback($implementation), $params, $this->getDropdownFields());
    }

    /**
     * Returns list of roles which have role specific metadata
     *
     * @param SplObjectStorage $roles
     * @param $currentRole
     * @return array
     */
    protected function getRoleListWithMetadata(SplObjectStorage $roles, $currentRole)
    {
        $result = [];
        foreach ($roles as $role) {
            $hasMetadata = $roles->offsetGet($role);
            if ($hasMetadata) {
                if ($role->id === $currentRole) {
                    $result['resultForReset'][$role->id] = $role->name;
                } else {
                    $result['resultsForCopy'][$role->id] = $role->name;
                }
            }
        }

        return $result;
    }

    /**
     * Get vardefs for all dropdown type of fields of a specific module
     *
     * @return array
     */
    protected function getDropdownFields()
    {
        $fieldDefs = VardefManager::getFieldDefs($this->editModule);
        $newAry = [];
        foreach ($fieldDefs as $field) {
            if (isset($field['type'])
                && $field['type'] === 'enum'
                && isset($field['name'])
                && empty($field['readonly'])
                && $this->checkStudio($field)
            ) {
                array_push($newAry, $field);
            }
        }
        return $newAry;
    }

    /**
     * Function to check if the field has studio property set to true
     *
     * @param array $field
     * @return bool
     */
    protected function checkStudio($field)
    {
        $studioSet = isset($field['studio']);
        if ($studioSet && is_bool($field['studio'])) {
            return $field['studio'];
        }
        if ($studioSet && is_string($field['studio'])) {
            return $field['studio'] === 'true' || $field['studio'] === 'visible' ? true : false;
        }
        if ($studioSet && is_array($field['studio']) && !empty($field['studio'][$this->editLayout])) {
            $value = $field['studio'][$this->editLayout];
            if (is_string($value)) {
                return $value === 'true' || $value === 'visible' ? true : false;
            }
            return $value;
        }
        return true;
    }

    /**
     * @param MetaDataImplementationInterface $implementation
     *
     * @return callable
     */
    protected function getHasMetaCallback(MetaDataImplementationInterface $implementation)
    {
        $editLayout = $this->editLayout;
        $editModule = $this->editModule;

        return function ($params) use ($implementation, $editLayout, $editModule) {
            //Remove roles that should not be used on normal users.
            return $implementation->fileExists(
                $editLayout,
                $editModule,
                MB_CUSTOMMETADATALOCATION,
                $params
            );
        };
    }
}
