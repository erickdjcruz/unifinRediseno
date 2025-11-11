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

use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\Capability\GlobalSearch\GlobalSearchCapable;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

class ViewModulefield extends SugarView
{
    public $mbModule;
    /**
     * @var FieldViewer
     */
    protected $fv;

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

    public function display()
    {
        $ac = $this->fetch();
        echo $ac->getJavascript();
    }

    public function fetch(
        $ac = false
    ) {


        $this->fv = new FieldViewer();

        $field_name = '';
        if (!empty($this->view_object_map['field_name'])) {
            $field_name = $this->view_object_map['field_name'];
        } elseif (!empty($_REQUEST['field'])) {
            $field_name = trim($this->request->getValidInputRequest(
                'field',
                null,
                $this->request->getValidInputRequest('name', 'Assert\ComponentName')
            ));
        }

        // If this is a new field mark it as such
        $isNew = empty($field_name) || !empty($_REQUEST['is_new']);

        $action = 'saveField'; // tyoung bug 17606: default action is to save as a dynamic field; but for standard OOB
        // fields we override this so don't create a new dynamic field instead of updating the existing field

        $isClone = false;
        if (!empty($this->view_object_map['is_clone']) && $this->view_object_map['is_clone']
            && (strcmp($field_name, 'name') != 0)   // bug #35767, do not allow cloning of name field
        ) {
            $isClone = true;
        }
        /*
        $field_types =  array('varchar'=>'YourField', 'int'=>'Integer', 'float'=>'Decimal','bool'=>'Checkbox','enum'=>'DropDown',
                'date'=>'Date', 'phone' => 'Phone', 'currency' => 'Currency', 'html' => 'HTML', 'radioenum' => 'Radio',
                'relate' => 'Relate', 'address' => 'Address', 'text' => 'TextArea', 'url' => 'Link');
        */
        $field_types = $GLOBALS['mod_strings']['fieldTypes'];

        $moduleName = $this->request->getValidInputRequest('view_module', 'Assert\ComponentName');

        if (hasMapsLicense()) {
            $administration = new Administration();
            $administration->retrieveSettings(false, true);
            $mapsModules = $administration->settings['maps_enabled_modules'];

            if (!safeInArray($moduleName, $mapsModules)) {
                unset($field_types['geocodestatus']);
            }
        } else {
            unset($field_types['geocodestatus']);
        }

        //For input validation we want to ensure we validate against all possible field types.  The parent field type
        //is added to the field_type array dynamically throughout this flow and therefore is not in the filed_types array
        //durng certain validation attempts.
        $allValidFieldTypes = array_merge($field_types, ['parent' => $GLOBALS['mod_strings']['parent']]);

        //bug 22264: Field name must not be an SQL keyword.
        $field_name_exceptions = array_merge(
            array_keys($GLOBALS['db']->getReservedWords()),
            ['ID', 'ID_C', 'PARENT_NAME', 'PARENT_ID']
        );

        //C.L. - Add support to mark related module id columns as reserved keywords
        $relatedModules = array_keys(DeployedRelationships::findRelatableModules());
        global $beanList, $current_language;
        foreach ($relatedModules as $relModule) {
            if (isset($beanList[$relModule])) {
                $field_name_exceptions[] = strtoupper($beanList[$relModule]) . '_ID';
            }
        }
        if (!AccessControlManager::instance()->allowFieldAccess($moduleName, $field_name)) {
            throw new SugarApiExceptionFieldDisabled();
        }
        if (empty($_REQUEST['view_package']) || $_REQUEST['view_package'] == 'studio') {
            $objectName = BeanFactory::getObjectName($moduleName);
            $module = BeanFactory::newBean($moduleName);

            VardefManager::loadVardef($moduleName, $objectName, true);
            global $dictionary;
            if (empty($module->mbvardefs)) {
                $module->mbvardefs = new stdClass();
            }
            $module->mbvardefs->vardefs = $dictionary[$objectName];

            $module->name = $moduleName;

            // hide audited and pii fields if the module is not 'audited'
            if ($module->is_AuditEnabled()) {
                $this->fv->ss->assign('auditable', true);
            } else {
                $this->fv->ss->assign('auditable', false);
            }

            if (!$ac) {
                $ac = new AjaxCompose();
            }
            $vardef = (!empty($module->mbvardefs->vardefs['fields'][$field_name])) ? $module->mbvardefs->vardefs['fields'][$field_name] : [];
            if ($isClone) {
                unset($vardef['name']);
            }

            // If this is a new field but we are loading this form a second time,
            // like from coming back from a dropdown create on a new field, then
            // keep the 'name' field open to allow the create field process to
            // continue like normal
            if (empty($vardef['name']) || $isNew) {
                if (!empty($_REQUEST['type'])) {
                    $vardef['type'] = $this->request->getValidInputRequest('type', ['Assert\Choice' => ['choices' => array_keys($allValidFieldTypes)]], '');
                }
                $this->fv->ss->assign('hideLevel', 0);
            } elseif (isset($vardef['custom_module'])) {
                $this->fv->ss->assign('hideLevel', 2);
            } else {
                $action = 'saveSugarField'; // tyoung - for OOB fields we currently only support modifying the label
                $this->fv->ss->assign('hideLevel', 3);
            }
            if ($isClone && isset($vardef['type']) && $vardef['type'] == 'datetime') {
                $vardef['type'] = 'datetimecombo';
            }

            require_once 'modules/DynamicFields/FieldCases.php';
            $tf = get_widget(empty($vardef ['type']) ? '' : $vardef ['type']);

            if (!$isNew) {
                $tf->module = $module;
            }

            $tf->populateFromRow($vardef);
            $vardef = array_merge($vardef, $tf->get_field_def());

            //          $GLOBALS['log']->debug('vardefs after loading = '.print_r($vardef,true));


            //Check if autoincrement fields are allowed
            $allowAutoInc = true;
            $enumFields = [];
            foreach ($module->field_defs as $field => $def) {
                if (!empty($def['type']) && $def['type'] == 'int' && !empty($def['auto_increment'])) {
                    $allowAutoInc = false;
                    continue;
                }
                if (!empty($def['type']) && $def['type'] == 'enum' && $field != $vardef['name']) {
                    //skip if $def has studio element set to false or "false".  NULL is a valid value
                    if (isset($def['studio']) && ($def['studio'] === false || $def['studio'] === 'false')) {
                        continue;
                    }
                    $enumFields[$field] = translate($def['vname'], $moduleName);
                    if (substr($enumFields[$field], -1) == ':') {
                        $enumFields[$field] = substr($enumFields[$field], 0, strlen($enumFields[$field]) - 1);
                    }
                }
            }
            $this->fv->ss->assign('allowAutoInc', $allowAutoInc);

            $GLOBALS['log']->warn(
                'view.modulefield: hidelevel ' . $this->fv->ss->getTemplateVars('hideLevel') . ' ' . print_r($vardef, true)
            );
            if (!empty($vardef['vname'])) {
                $this->fv->ss->assign(
                    'lbl_value',
                    htmlentities(translate($vardef['vname'], $moduleName), ENT_QUOTES, 'UTF-8')
                );
            }
            $this->fv->ss->assign('module', $module);
            if (empty($module->mbvardefs->vardefs['fields']['parent_name']) || (isset($vardef['type']) && $vardef['type'] == 'parent')) {
                $field_types['parent'] = $GLOBALS['mod_strings']['parent'];
            }

            $edit_or_add = 'editField';

            if (isset($module->disableImportFields) && safeInArray($vardef['name'], $module->disableImportFields)) {
                $this->fv->ss->assign('hideImportable', true);
            }
        } else {
            require_once 'modules/ModuleBuilder/MB/ModuleBuilder.php';
            $mb = new ModuleBuilder();
            $packageName = $this->request->getValidInputRequest('view_package', 'Assert\ComponentName');
            $module =& $mb->getPackageModule($packageName, $moduleName);
            $package =& $mb->packages[$packageName];
            $module->getVardefs();
            if (!$ac) {
                $ac = new AjaxCompose();
            }
            $vardef = (!empty($module->mbvardefs->vardefs['fields'][$field_name])) ? $module->mbvardefs->vardefs['fields'][$field_name] : [];

            if ($isClone) {
                unset($vardef['name']);
            }

            if (empty($vardef['name'])) {
                if (!empty($_REQUEST['type'])) {
                    $vardef['type'] = $this->request->getValidInputRequest('type', ['Assert\Choice' => ['choices' => array_keys($allValidFieldTypes)]], '');
                }
                $this->fv->ss->assign('hideLevel', 0);
            } else {
                if (!empty($module->mbvardefs->vardef['fields'][$vardef['name']])) {
                    $this->fv->ss->assign('hideLevel', 1);
                } elseif (isset($vardef['custom_module'])) {
                    $this->fv->ss->assign('hideLevel', 2);
                } else {
                    // tyoung bug 17350 - effectively mark template derived fields as readonly
                    $this->fv->ss->assign('hideLevel', 3);
                }
            }

            require_once 'modules/DynamicFields/FieldCases.php';
            $tf = get_widget(empty($vardef ['type']) ? '' : $vardef ['type']);
            $tf->module = $module;
            $tf->populateFromRow($vardef);
            $vardef = array_merge($vardef, $tf->get_field_def());

            $this->fv->ss->assign('module', $module);
            $this->fv->ss->assign('package', $package);
            $this->fv->ss->assign('MB', '1');

            // always show audited and pii field in module build
            $this->fv->ss->assign('auditable', true);

            if (isset($vardef['vname'])) {
                $this->fv->ss->assign(
                    'lbl_value',
                    htmlentities($module->getLabel('en_us', $vardef['vname']), ENT_QUOTES, 'UTF-8')
                );
            }
            if (empty($module->mbvardefs->vardefs['fields']['parent_name']) || (isset($vardef['type']) && $vardef['type'] == 'parent')) {
                $field_types['parent'] = $GLOBALS['mod_strings']['parent'];
            }

            $enumFields = [];
            if (!empty($module->mbvardefs->vardefs['fields'])) {
                foreach ($module->mbvardefs->vardefs['fields'] as $field => $def) {
                    if (!empty($def['type']) && $def['type'] == 'enum' && $field != $vardef['name']) {
                        $enumFields[$field] = isset($module->mblanguage->strings[$current_language][$def['vname']]) ?
                            $this->mbModule->mblanguage->strings[$current_language][$def['vname']] : translate($field);
                        if (is_string($enumFields[$field]) && substr($enumFields[$field], -1) == ':') {
                            $enumFields[$field] = substr($enumFields[$field], 0, strlen($enumFields[$field]) - 1);
                        }
                    }
                }
            }

            $edit_or_add = 'mbeditField';
        }

        if ($_REQUEST['action'] == 'RefreshField') {
            require_once 'modules/DynamicFields/FieldCases.php';
            $field = get_widget($_POST['type']);
            $field->populateFromPost();
            $vardef = $field->get_field_def();
            $vardef['options'] = $this->request->getValidInputRequest('new_dropdown');
            $labelValue = $this->request->getValidInputRequest('labelValue');
            $this->fv->ss->assign('lbl_value', htmlentities($labelValue, ENT_QUOTES, 'UTF-8'));
        }

        foreach (['formula', 'default', 'comments', 'help', 'visiblityGrid'] as $toEscape) {
            if (!empty($vardef[$toEscape]) && is_string($vardef[$toEscape])) {
                $vardef[$toEscape] = htmlentities($vardef[$toEscape], ENT_QUOTES, 'UTF-8');
            }
        }

        $disallowed_field_names = [
            'name', 'parent_type', 'parent_id', 'parent_name',
        ];
        if (!empty($vardef['studio']['no_duplicate'])
            || in_array($field_name, $disallowed_field_names)
            || ViewModulefields::isRelationshipField($vardef)
            // bug #35767, do not allow cloning of name field
            || (isset($vardef['type']) && $vardef['type'] === 'name')) {
            $this->fv->ss->assign('no_duplicate', true);
        }

        // Do not allow cloning of non-supported field types
        if (isset($vardef['type']) && !array_key_exists($vardef['type'], $field_types)) {
            $this->fv->ss->assign('no_duplicate', true);
        }

        $this->fv->ss->assign('action', $action);
        $this->fv->ss->assign('isClone', ($isClone ? 1 : 0));
        $this->fv->ss->assign('isNew', $isNew);
        $this->fv->ss->assign('module_dd_fields', $enumFields);
        $json = getJSONobj();

        $this->fv->ss->assign('field_name_exceptions', $json->encode($field_name_exceptions));

        // Only one AutoIncrement field can exist on a module, with the exception of Cases, that can have 2 because
        // case_number is a Number field that behaves with auto increment properties. Check if this module has an
        // existing AutoIncrement field, if so remove it from the list of field types in the Studio dropdown editor.
        $existingAutoIncrementField = $module->getFieldDefinitions('type', ['autoincrement']);
        if (!empty($existingAutoIncrementField)) {
            $field_types = array_diff_key($field_types, ['autoincrement' => '']);
        }

        // ActionButtons are not supported on BWC modules, therefore we need to check for that condition and
        // remove it as an available option if that is the case.
        // Same for ModuleBuilder, we don't currently support creating ActionButtons from Module Builder on
        // account that the AB configuration screens are dependend on already deployed metadata, therefore
        // actions such as update field / create field will run into issues with metadata not being available
        $isModuleBwc = safeInArray($moduleName, $GLOBALS['bwcModules']);
        $isMB = !empty($_REQUEST['view_package']);

        if ($isModuleBwc || $isMB) {
            $field_types = array_diff_key($field_types, ['actionbutton' => '']);
        }

        ksort($field_types);

        // GeocodeStatuses are not supported on BWC modules, therefore we need to check for that condition and
        // remove it as an available option if that is the case.
        // Same for ModuleBuilder, we don't currently support creating GeocodeStatuses from Module Builder on
        // account that the AB configuration screens are dependend on already deployed metadata, therefore
        // actions such as update field / create field will run into issues with metadata not being available
        $isModuleBwc = safeInArray($moduleName, $GLOBALS['bwcModules']);
        $isMB = !empty($_REQUEST['view_package']);

        if ($isModuleBwc || $isMB) {
            $field_types = array_diff_key($field_types, ['geocodestatus' => '']);
        }

        ksort($field_types);
        $this->fv->ss->assign('field_types', $field_types);

        // Handle a bunch of special case stuff for relate fields tied to relationships:
        // 1. They use a different label than other relate fields.
        // 2. Many options in studio are disabled - just set that flag here so we don't need
        //    to make the check for stock relate everywhere.
        // 3. The `audited` flag could be stored on either relate or ID field, so we need
        //    to check both to get this properly.
        if (ViewModulefields::isRelationshipField($vardef)) {
            $this->fv->ss->assign('display_type', translate('LBL_RELATIONSHIP_TYPE', 'ModuleBuilder'));
            $this->fv->ss->assign('is_relationship_field', true);
            $this->fv->ss->assign('is_custom_relationship', ViewModulefields::isCustomRelationshipField(
                $vardef['name'],
                $moduleName,
                $module->mbvardefs->vardefs['fields'],
            ));
            $this->fv->ss->assign('is_one_to_one_field', ViewModulefields::isOneToOneRelationshipField(
                $vardef['name'],
                $moduleName,
                $module->mbvardefs->vardefs['fields'],
            ));

            $fields = $module->mbvardefs->vardefs['fields'];
            $audited = array_key_exists('audited', $vardef) ? $vardef['audited'] : false;
            if (array_key_exists($vardef['id_name'], $fields)) {
                $idVardefs = $fields[$vardef['id_name']];
                $idAudited = array_key_exists('audited', $idVardefs) ? $idVardefs['audited'] : false;
                $audited = $audited || $idAudited;
                $this->fv->ss->assign('relationship_field_audited', $audited);
            }
        } else {
            $this->fv->ss->assign('is_relationship_field', false);
        }

        // Full Text Search settings
        $engine = SearchEngine::getInstance()->getEngine();
        if ($engine instanceof GlobalSearchCapable && safeInArray($vardef['type'], $engine->getStudioSupportedTypes())) {
            // default fts parameters
            $ftsFieldConfig = '0';
            $ftsBoost = '1';

            // determine fts configuration
            if (!empty($vardef['full_text_search']) &&
                !empty($vardef['full_text_search']['searchable']) &&
                $vardef['full_text_search']['searchable'] == true) {
                $ftsFieldConfig = '2';
            }

            // determine boost value
            if (!empty($vardef['full_text_search']['boost'])) {
                $ftsBoost = (float)$vardef['full_text_search']['boost'];
            }

            $this->fv->ss->assign('fts_field_config', $GLOBALS['app_list_strings']['fts_field_config']);
            $this->fv->ss->assign('fts_field_config_selected', $ftsFieldConfig);
            $this->fv->ss->assign('fts_field_boost_value', $ftsBoost);
            $this->fv->ss->assign('show_fts', true);
        } else {
            $this->fv->ss->assign('show_fts', false);
        }

        //Ensure certain field types always have correct formula return types for validation.
        if (!empty($vardef['type'])) {
            switch ($vardef['type']) {
                case 'date':
                case 'datetime':
                case 'datetimecombo':
                    $this->fv->ss->assign('calcFieldType', 'date');
                    break;
                case 'bool':
                    $this->fv->ss->assign('calcFieldType', 'boolean');
                    break;
                default:
                    $this->fv->ss->assign('calcFieldType', '');
                    break;
            }
        }

        $this->fv->ss->assign('importable_options', $GLOBALS['app_list_strings']['custom_fields_importable_dom']);
        $this->fv->ss->assign('duplicate_merge_options', $GLOBALS['app_list_strings']['custom_fields_merge_dup_dom']);

        $triggers = [];
        $existing_field_names = [];
        foreach ($module->mbvardefs->vardefs['fields'] as $field) {
            if ($field ['type'] == 'enum' || $field ['type'] == 'multienum') {
                $triggers [] = $field ['name'];
            }

            if (!isset($field['source']) || $field['source'] != 'non-db') {
                if (preg_match('/^(.*?)(_c)?$/', $field['name'], $matches)) {
                    $existing_field_names [] = strtoupper($matches[1]);
                }
            }
        }

        $this->fv->ss->assign('triggers', $triggers);
        $this->fv->ss->assign('existing_field_names', $json->encode($existing_field_names));
        $this->fv->ss->assign('mod_strings', $GLOBALS['mod_strings']);

        // jchi #24880
        if (!isset($vardef['reportable'])) {
            $vardef['reportable'] = 1;
        }
        // end

        $layout = $this->fv->getLayout($vardef);

        $this->fv->ss->assign('fieldLayout', $layout);
        if (empty($vardef['type'])) {
            $vardef['type'] = 'varchar';
        }

        $this->fv->ss->assign('vardef', $vardef);

        if (empty($_REQUEST['field'])) {
            $edit_or_add = 'addField';
        }

        $this->fv->ss->assign('help_group', $edit_or_add);
        $body = $this->fetchTemplate('modules/ModuleBuilder/tpls/MBModule/field.tpl');
        $ac->addSection('east', translate('LBL_SECTION_FIELDEDITOR', 'ModuleBuilder'), $body);
        return $ac;
    }

    /**
     * fetchTemplate
     * This function overrides fetchTemplate from SugarView.  For view.modulefield.php we go through the FieldViewer
     * class to fetch the display contents.
     *
     * @param string $template the file to fetch
     * @return string contents from calling the fetch method on the FieldViewer Sugar_Smarty instance
     */
    protected function fetchTemplate($template)
    {
        if (func_num_args() > 1) { // for BC, @todo remove it in future
            $GLOBALS['log']->deprecated('Invalid call to ' . __METHOD__ . ', only $template argument is expected');
            if (!is_string($template)) {
                $template = func_get_arg(1);
            }
        }
        return $this->fv->ss->fetch($this->getCustomFilePathIfExists($template));
    }
}
