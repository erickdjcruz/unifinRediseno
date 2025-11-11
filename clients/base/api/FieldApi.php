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

use Sugarcrm\Sugarcrm\Security\Validator\ConstraintBuilder;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;

class FieldApi extends SugarApi
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator = null;

    /**
     * @var \Symfony\Component\Validator\Constraint[]
     */
    protected $moduleNameConstraints = null;

    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            /**
             * Request payload of this endpoint expects the attrubutes of the new field, e.g.
             * "data": {
             *     "name": "New custom field name",
             *     "type": "decimal",
             *     ...
             *  }
             * Help document in include/api/help/module_field_post_help.html has more information.
             * Valid attribute names can be referred to $vardef_map of particular template fields
             * under /modules/DynamicFields/templates/Fields/Template<Field Type>.php.
             */
            'create' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'customfield'],
                'pathVars' => ['module', ''],
                'method' => 'createCustomField',
                'shortHelp' => 'This method creates a new custom field of the specified module',
                'longHelp' => 'include/api/help/module_field_post_help.html',
                'minVersion' => '11.11',
            ],
            'delete' => [
                'reqType' => 'DELETE',
                'path' => ['<module>', 'customfield', '?'],
                'pathVars' => ['module', '', 'field'],
                'method' => 'deleteCustomField',
                'shortHelp' => 'This method deletes the custom field of specified module',
                'longHelp' => 'include/api/help/module_field_delete_help.html',
                'minVersion' => '11.11',
            ],
        ];
    }

    /**
     * To build a validator and constraint
     */
    protected function buildModuleNameValidator()
    {
        $this->validator = Validator::getService();
        $contraintBuilder = new ConstraintBuilder();
        $this->moduleNameConstraints = $contraintBuilder->build(['Assert\Bean\ModuleName',]);
    }

    /**
     * @param string $module
     * @return bool
     */
    protected function isValidModule(string $module): bool
    {
        if (empty($this->validator)) {
            $this->buildModuleNameValidator();
        }
        $errors = $this->validator->validate($module, $this->moduleNameConstraints);
        return safeCount($errors) == 0;
    }

    /**
     * Creates a new custom field in the specified module
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionError
     */
    public function createCustomField(ServiceBase $api, array $args)
    {
        global $current_user, $sugar_config;

        // Only an admin can create new custom fields
        if (!is_admin($current_user)) {
            throw new SugarApiExceptionNotAuthorized('Current user is not authorized.');
        }

        $this->requireArgs($args, ['module', 'data']);
        $this->requireArgs($args['data'], ['name', 'type', 'label']);
        $this->requireArgs($args['localizations'], [$sugar_config['default_language']]);

        $module = $args['module'];
        if (!$this->isValidModule($module)) {
            throw new SugarApiExceptionInvalidParameter("Invalid module: {$module}");
        }

        $bean = BeanFactory::newBean($module);
        if (!empty($bean->field_defs) &&
            array_key_exists($args['data']['name'] . '_c', $bean->field_defs)) {
            throw new SugarApiExceptionInvalidParameter('This field already exists: ' . $args['data']['name'] . '_c');
        }

        require_once 'modules/DynamicFields/FieldCases.php';
        $field = get_widget($args['data']['type']);
        // TemplateText is returned if the field type doesn't match anything from FieldCases widget
        if ($field && get_class($field) === 'TemplateText' &&
            !safeInArray($args['data']['type'], ['char', 'varchar', 'varchar2'])) {
            throw new SugarApiExceptionInvalidParameter("Invalid field type: {$args['data']['type']}.");
        }

        foreach ($args['data'] as $name => $value) {
            if (!array_key_exists($name, $field->vardef_map) && !safeInArray($name, ['appendToViews', 'addIndex'])) {
                throw new SugarApiExceptionInvalidParameter("Invalid field attribute: {$name}.");
            }
        }

        // If options is an array, a new dropdown definition array should be specified for creation.
        // If options is a string, an existing dropdown is used.
        if (($args['data']['type'] === 'enum' ||
                $args['data']['type'] === 'multienum') &&
            isset($args['data']['options'])) {
            if (is_array($args['data']['options'])) {
                $newDropdown = $this->createDropdownList($args['data']['options'], $args['localizations']);
                if ($newDropdown) {
                    $args['data']['options'] = $newDropdown;
                } else {
                    throw new SugarApiExceptionInvalidParameter('Invalid field attribute: options.');
                }
            }
            $args['data']['ext1'] = $args['data']['options'];
        } elseif ($args['data']['type'] === 'decimal' || $args['data']['type'] === 'float') {
            $args['data']['ext1'] = $args['data']['precision'] ?? 8;
        }

        SugarAutoLoader::requireWithCustom('ModuleInstall/ModuleInstaller.php');
        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        $moduleInstaller = new $moduleInstallerClass();
        $args['data']['module'] = $module;
        $moduleInstaller->install_custom_fields([$args['data']]);

        $this->saveLabel($module, $args['data']['label'], $args['localizations']);
        $this->moduleRepairAndRebuild($module);

        if (isset($args['data']['appendToViews']['listview']) &&
            $args['data']['appendToViews']['listview'] === true) {
            $this->displayInListView($module, $args['data']);
        }
        if (isset($args['data']['appendToViews']['recordview']) &&
            $args['data']['appendToViews']['recordview'] === true) {
            $this->displayInRecordView($module, $args['data']);
        }
        if (isset($args['data']['appendToViews']['searchview']) &&
            $args['data']['appendToViews']['searchview'] === true) {
            $this->displayInSearchView($module, $args['data']);
        }

        $bean = BeanFactory::newBean($module);
        if (!empty($bean->field_defs) && array_key_exists($args['data']['name'] . '_c', $bean->field_defs)) {
            // The new custom field is created successfully, returns the field definition
            return $bean->field_defs[$args['data']['name'] . '_c'];
        } else {
            throw new SugarApiExceptionError("New field {$args['data']['name']} is not created to {$module}.");
        }
    }

    /**
     * Deletes the custom field in the specified module
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     */
    public function deleteCustomField(ServiceBase $api, array $args)
    {
        global $current_user;

        // Only an admin can delete custom fields
        if (!is_admin($current_user)) {
            throw new SugarApiExceptionNotAuthorized('Current user is not authorized.');
        }

        $this->requireArgs($args, ['module', 'field']);

        $module = $args['module'];
        $field = $args['field'];
        if (!$this->isValidModule($module)) {
            throw new SugarApiExceptionInvalidParameter("Invalid module: {$module}");
        }

        $bean = BeanFactory::newBean($module);
        if (empty($bean->field_defs) || !array_key_exists($field, $bean->field_defs)) {
            throw new SugarApiExceptionNotFound("This field doesn't exist: {$field}");
        }
        if (empty($bean->field_defs[$field]['source']) ||
            (!empty($bean->field_defs[$field]['source']) &&
                $bean->field_defs[$field]['source'] !== 'custom_fields')) {
            throw new SugarApiExceptionInvalidParameter("This is not a custom field: {$field}");
        }

        $dyField = new DynamicField();
        $dyField->bean = $bean;
        $dyField->module = $module;
        $dyField->deleteField($field);

        $studioModule = StudioModuleFactory::getStudioModule($module);
        $studioModule->removeFieldFromLayouts($field);

        return ['name' => $field];
    }

    /**
     * Displays field in list view
     *
     * @param string $module
     * @param array $attributes
     */
    public function displayInListView(string $module, array $attributes)
    {
        $parser = ParserFactory::getParser('listview', $module, '', null);
        $parser->addField($attributes['name'] . '_c');
        $parser->getImplementation()->deploy($parser->_viewdefs);
    }

    /**
     * Displays field in record view
     *
     * @param string $module
     * @param array $attributes
     */
    public function displayInRecordView(string $module, array $attributes)
    {
        $parser = ParserFactory::getParser('recordview', $module);
        $fieldDef = [
            'name' => $attributes['name'] . '_c',
            'label' => $attributes['label'],
        ];
        $parser->addField($fieldDef);
        $viewdefs = $parser->_viewdefs;
        $viewdefs['panels'] = $parser->convertToCanonicalForm($viewdefs['panels'], $parser->_fielddefs);
        $defs = MetaDataFiles::mapPathToArray(MetaDataFiles::getViewDefVar('recordview'), $viewdefs);
        $parser->getImplementation()->deploy($defs);
    }

    /**
     * Displays field in search view
     *
     * @param string $module
     * @param array $attributes
     */
    public function displayInSearchView(string $module, array $attributes)
    {
        $parser = new SidecarFilterLayoutMetaDataParser($module, '', 'base');
        $parser->addField($attributes['name'] . '_c');
        $parser->getImplementation()->deploy($parser->_viewdefs);
    }

    /**
     * Creates a new dropdown list
     *
     * @param array $options
     * @param array $localizations
     * @return Mixed
     */
    protected function createDropdownList(array $options, array $localizations)
    {
        global $sugar_config;

        $this->requireArgs($options, ['dropdownName', 'dropdownList']);

        SugarAutoLoader::load('include/utils.php');
        $dd = translate($options['dropdownName']);
        // If the dropdown exists, use it without creating a new dropdown
        if (!empty($dd) && is_array($dd)) {
            return $options['dropdownName'];
        }

        $allLanguages = get_languages();
        $params = [];
        $_REQUEST['view_package'] = 'studio'; //need this in parser.dropdown.php
        $params['view_package'] = 'studio';
        $params['dropdown_name'] = $options['dropdownName'];
        $params['skip_sync'] = true;

        /**
         * Dropdown list must exist to create a new dropdown:
         *
         * "options": {
         *     ...
         *     "dropdownList": [
         *         {"value":"First", "label":"LBL_DD_ITEM_ONE"},
         *         {"value":"Second", "label":"LBL_DD_ITEM_TWO"},
         *         {"value":"Third", "label":"LBL_DD_ITEM_THREE"},
         *         ...
         *     ]
         *  }
         */
        if (!empty($options['dropdownList']) && is_array($options['dropdownList'])) {
            SugarAutoLoader::requireWithCustom('modules/ModuleBuilder/parsers/parser.dropdown.php');
            $parserClass = SugarAutoLoader::customClass('ParserDropDown');
            $parser = new $parserClass();
            $defaultDropdown = '';
            foreach ($localizations as $langKey => $labelList) {
                if (!array_key_exists($langKey, $allLanguages)) {
                    continue;
                }

                $params['dropdown_lang'] = $langKey;
                $dropdownList = [];
                foreach ($options['dropdownList'] as $dropdown) {
                    if (is_array($dropdown) && isset($dropdown['value'])) {
                        $label = $dropdown['label'] ?? '';
                        if (!empty($labelList[$label])) {
                            $label = $labelList[$label];
                        }
                        $dropdownValue = trim($dropdown['value']) == '' ? '-blank-' : $dropdown['value'];
                        if ($dropdownValue === '-blank-') {
                            $label = '';
                        }
                        $dropdownList[] = [$dropdownValue, $label];
                    }
                }

                if (!empty($dropdownList)) {
                    $params['list_value'] = json_encode($dropdownList);
                    if ($langKey === $sugar_config['default_language']) {
                        $defaultDropdown = $params['list_value'];
                    }
                    $parser->saveDropDown($params, true, $langKey);
                }
            }

            // syncs to other languages using dropdown from default language
            $dropdown = $parser->formatDropdown($defaultDropdown);
            foreach ($allLanguages as $lang => $langName) {
                if (!array_key_exists($lang, $localizations)) {
                    $parser->saveDropdownToLang($options['dropdownName'], $dropdown, $lang);
                }
            }
            $this->refreshMetadata();

            return $options['dropdownName'];
        }

        return false;
    }

    /**
     * Refresh metadata
     */
    protected function refreshMetadata()
    {
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_LABELS);
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_ORDEREDLABELS);
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_EDITDDFILTERS);
    }

    /**
     * Saves the label to language file
     *
     * @param string $module
     * @param string $labelName
     * @param array $localizations
     */
    protected function saveLabel(string $module, string $labelName, array $localizations)
    {
        $allLanguages = get_languages();
        foreach ($localizations as $langKey => $labelList) {
            if (!array_key_exists($langKey, $allLanguages)) {
                continue;
            }
            $labelValue = $labelList[$labelName] ?? $labelName;
            $parser = new ParserLabel($module, null);
            $labels[strtoupper($labelName)] = SugarCleaner::cleanHtml(from_html($labelValue), false);
            $parser->addLabels($langKey, $labels, $module, null);

            // Clear the language cache to make sure the view picks up the latest
            $cache_key = LanguageManager::getLanguageCacheKey($module, $langKey);
            sugar_cache_clear($cache_key);
        }
    }

    /**
     * Repairs and rebuilds module files
     *
     * @param string $module
     */
    protected function moduleRepairAndRebuild(string $module)
    {
        $repair = new RepairAndClear();

        // Set up an array for repairing modules
        $repairModules = [$module];
        if ($module === 'Users') {
            $repairModules[] = 'Employees';
        }
        $repair->repairAndClearAll(
            ['rebuildExtensions', 'clearVardefs', 'clearTpls', 'clearSearchCache'],
            $repairModules,
            true,
            false
        );
        $objName = BeanFactory::getObjectName($module);
        //Ensure the vardefs are up to date for this module before we rebuild the cache now.
        VardefManager::loadVardef($module, $objName, true);

        $repair->module_list = [];
        $repair->clearJsFiles();

        // Clear the metadata cache for labels and the requested module
        $repair->module_list = [$module];
        $repair->repairMetadataAPICache(MetaDataManager::MM_LABELS);
        $repair->repairMetadataAPICache(MetaDataManager::MM_ORDEREDLABELS);
    }
}
