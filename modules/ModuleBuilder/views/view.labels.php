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

class ViewLabels extends ViewModulefields
{
    /**
     * @see SugarView::_getModuleTitleParams()
     */
    protected function _getModuleTitleParams($browserTitle = false)
    {
        global $mod_strings;

        return [
            translate('LBL_MODULE_NAME', 'Administration'),
            ModuleBuilderController::getModuleTitle(),
        ];
    }

    //STUDIO LABELS ONLY//
    //TODO Bundle Studio and ModuleBuilder label handling to increase maintainability.
    public function display()
    {
        $translatedEditModule = null;
        global $mod_strings;

        // Check if the user has access to the module
        $editModule = $this->request->getValidInputRequest('view_module', 'Assert\ComponentName');
        if (!AccessControlManager::instance()->allowModuleAccess($editModule)) {
            throw new SugarApiExceptionModuleDisabled();
        }

        // Check if we are requesting all labels
        $labels = $this->request->getValidInputRequest('labels');
        $allLabels = $labels === 'all';

        // Prepare the Smarty template
        $smarty = new Sugar_Smarty();
        $this->setupLanguageVariables($editModule, $allLabels, $smarty);
        $smarty->assign('mod_strings', $mod_strings);
        $smarty->assign('view_module', $editModule);
        $smarty->assign('APP', $GLOBALS['app_strings']);
        $smarty->assign('defaultHelp', 'labelsBtn');
        $smarty->assign('assistant', ['key' => 'labels', 'group' => 'module']);
        $smarty->assign('labels_choice', $mod_strings['labelTypes']);
        $smarty->assign('labels_current', $allLabels ? 'all' : '');

        // Prepare the Ajax object
        $ajax = new AjaxCompose();
        $ajax->addCrumb($mod_strings['LBL_STUDIO'], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard")');
        if (!isset($_REQUEST['MB'])) {
            global $app_list_strings;
            $moduleNames = array_change_key_case($app_list_strings['moduleList']);
            $translatedEditModule = $moduleNames[strtolower($editModule)];
        }
        $ajax->addCrumb($translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $editModule . '")');
        $ajax->addCrumb($mod_strings['LBL_LABELS'], '');

        // Output the template result
        $html = $smarty->fetch('modules/ModuleBuilder/tpls/labels.tpl');
        $ajax->addSection('center', $GLOBALS['mod_strings']['LBL_SECTION_EDLABELS'], $html);
        echo $ajax->getJavascript();
    }

    /**
     * Prepares variable data for the Smarty template regarding the selected
     * language and comparison language
     *
     * @param string $module the module name to setup language settings for
     * @param bool $allLabels determines whether all labels should be prepared,
     *                        whether they are used or not
     * @param Sugar_Smarty $smarty the Smarty template to prepare data for
     */
    private function setupLanguageVariables(string $module, bool $allLabels, Sugar_Smarty $smarty)
    {
        global $locale;
        global $sugar_config;
        global $mod_strings;

        // Get the vnames for the module (the list of all module labels that
        // are actually used)
        $vnames = $this->getVnames($module);

        // Setup the options for the "Language" selector
        $smarty->assign('available_languages', get_languages());

        // Setup the key/value label pairs for the selected language
        $selectedLang = $this->request->getValidInputRequest(
            'selected_lang',
            'Assert\Language',
            $locale->getAuthenticatedUserLanguage()
        );
        $selectedLangStrings = $this->getModStrings($module, $selectedLang, $allLabels, $vnames);
        ksort($selectedLangStrings);
        $smarty->assign('selected_lang', $selectedLang);
        $smarty->assign('MOD', $selectedLangStrings);

        // If the selected language is not the default language, and there are
        // other languages available to compare it to, setup the
        // "Compare Language" selector
        $systemDefaultLanguage = $sugar_config['default_language'];
        $availableCompareLanguages = array_diff_key(get_languages(), [
            $selectedLang => '',
        ]);
        if ($systemDefaultLanguage !== $selectedLang && !empty($availableCompareLanguages)) {
            $smarty->assign('showCompareLanguage', true);
            $smarty->assign('availableCompareLanguages', $availableCompareLanguages);

            // Setup the key/value label pairs for the selected comparison language
            $comparisonLang = $this->request->getValidInputRequest(
                'comparison_lang',
                'Assert\Language',
                $locale->getAuthenticatedUserLanguage()
            );
            if (empty($availableCompareLanguages[$comparisonLang])) {
                $comparisonLang = $systemDefaultLanguage;
            }
            $comparisonLangStrings = $this->getModStrings($module, $comparisonLang, $allLabels, $vnames);
            ksort($comparisonLangStrings);
            $smarty->assign('comparisonLang', $comparisonLang);
            $smarty->assign('comparisonLangStrings', $comparisonLangStrings);

            // Setup the list of labels where the selected language label is
            // identical to the selected comparison language label. Also setup
            // the HTML for the info icon to be shown next to these labels
            $matchingLabels = [];
            foreach ($selectedLangStrings as $key => $label) {
                if (isset($comparisonLangStrings[$key]) && $label === $comparisonLangStrings[$key]) {
                    $matchingLabels[$key] = true;
                }
            }
            $smarty->assign('matchingLabels', $matchingLabels);
            $smarty->assign('matchingLabelHelp', generateBwcHelpIcon($mod_strings['LBL_LABEL_NOT_TRANSLATED']));
        } else {
            // Cannot use Compare Language functionality
            $smarty->assign('showCompareLanguage', false);
        }
    }

    /**
     * Gets the list of label keys that are used in the given module as
     * 'vname's or 'label's
     *
     * @param string $module the name of the module
     * @return array the list of label keys
     */
    private function getVnames($module)
    {
        global $dictionary;
        $vnames = [];
        $objectName = BeanFactory::getObjectName($module);
        VardefManager::loadVardef($module, $objectName);

        // Get view/layout labels
        $parser = ParserFactory::getParser(MB_LISTVIEW, $module);
        foreach ($parser->getLayout() as $key => $def) {
            if (isset($def['label'])) {
                $vnames[$def['label']] = $def['label'];
            }
        }
        $variableMap = $this->getVariableMap($module);
        foreach ($variableMap as $key => $value) {
            $gridLayoutMetaDataParserTemp = ParserFactory::getParser($key, $module);
            foreach ($gridLayoutMetaDataParserTemp->getLayout() as $panel) {
                foreach ($panel as $row) {
                    foreach ($row as $fieldArray) { // fieldArray is an array('name'=>name,'label'=>label)
                        if (isset($fieldArray['label'])) {
                            $vnames[$fieldArray['label']] = $fieldArray['label'];
                        }
                    }
                }
            }
        }

        // Get subpanel labels
        $subList = SubPanel::getModuleSubpanels($module);
        foreach ($subList as $subpanel => $titleLabel) {
            $vnames[$titleLabel] = $titleLabel;
        }

        // Get field labels
        foreach ($dictionary[$objectName]['fields'] as $name => $def) {
            if (isset($def['vname'])) {
                $vnames[$def['vname']] = $def['vname'];
            }
        }

        return $vnames;
    }

    /**
     * Returns the key/value pairs of the formatted labels for the given module in the given language
     *
     * @param string $module the module to retrieve labels for
     * @param string $language the language to retrieve labels for
     * @param bool $allLabels return all labels, whether they are used or not
     * @param array $vnames the list of vnames used in the module
     * @return array the key/value language strings for the module/language, with values formatted
     */
    private function getModStrings($module, $language, $allLabels, $vnames)
    {
        global $mod_strings;
        $formattedModStrings = [];
        $modStringsBackup = $mod_strings;

        // We shouldn't set the $refresh = true here, or we will lose template
        // language mod_strings.
        // return_module_language($selected_lang, $module, false) :
        // the mod_strings will be included from cache files here.
        foreach (return_module_language($language, $module, false) as $name => $label) {
            //#25294
            if ($allLabels || isset($vnames[$name]) || preg_match('/lbl_city|lbl_country|lbl_billing_address|lbl_alt_address|lbl_shipping_address|lbl_postal_code|lbl_state$/si', $name)) {
                // Bug 58174 - Escaped labels are sent to the client escaped
                // even in the label editor in studio
                $formattedModStrings[$name] = html_entity_decode($label, null, 'UTF-8');
            }
        }

        //Grab everything from the custom files
        $files = [
            "custom/modules/$module/language/$language.lang.php",
            "custom/modules/$module/Ext/Language/$language.lang.ext.php",
        ];
        foreach ($files as $langfile) {
            $mod_strings = [];
            if (is_file($langfile)) {
                include $langfile;
                foreach ($mod_strings as $key => $label) {
                    // Bug 58174 - Escaped labels are sent to the client escaped
                    // even in the label editor in studio
                    $formattedModStrings[$key] = html_entity_decode($label, null, 'UTF-8');
                }
            }
        }

        $mod_strings = $modStringsBackup;
        return $formattedModStrings;
    }

    // fixing bug #39749: Quick Create in Studio
    public function getVariableMap($module)
    {
        if (isModuleBWC($module)) {
            $variableMap = [
                MB_EDITVIEW => 'EditView',
                MB_DETAILVIEW => 'DetailView',
                MB_QUICKCREATE => 'QuickCreate',
            ];

            $hideQuickCreateForModules = [
                'Campaigns',
                'Quotes',
                'ProductTemplates',
                'ProjectTask',
            ];

            if (in_array($module, $hideQuickCreateForModules)) {
                if (isset($variableMap['quickcreate'])) {
                    unset($variableMap['quickcreate']);
                }
            }
        } else {
            $variableMap = [
                MB_RECORDVIEW => 'record',
            ];
        }

        return $variableMap;
    }
}
