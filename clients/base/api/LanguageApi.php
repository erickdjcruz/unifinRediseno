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

class LanguageApi extends SugarApi
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
    public function registerApiRest(): array
    {
        return [
            'updateModules' => [
                'reqType' => 'PUT',
                'path' => ['lang', 'labels', 'module'],
                'pathVars' => ['', '', ''],
                'method' => 'updateModules',
                'shortHelp' => 'This method updates translations for fields in a module',
                'longHelp' => 'include/api/help/language_labels_module_put_help.html',
                'minVersion' => '11.13',
            ],
            'updateDropdowns' => [
                'reqType' => 'PUT',
                'path' => ['lang', 'labels', 'dropdown'],
                'pathVars' => ['', '', ''],
                'method' => 'updateDropdowns',
                'shortHelp' => 'This method updates translations for items in a dropdown',
                'longHelp' => 'include/api/help/language_labels_dropdown_put_help.html',
                'minVersion' => '11.13',
            ],
        ];
    }

    /**
     * To build a validator and constraint
     */
    protected function buildModuleNameValidator()
    {
        $this->validator = Validator::getService();
        $constraintBuilder = new ConstraintBuilder();
        $this->moduleNameConstraints = $constraintBuilder->build(['Assert\Bean\ModuleName',]);
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
     * Updates translations for fields in modules
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     */
    public function updateModules(ServiceBase $api, array $args): array
    {
        global $current_user;

        // Only an admin can create new custom fields
        if (!is_admin($current_user)) {
            throw new SugarApiExceptionNotAuthorized('Current user is not authorized.');
        }

        $allLanguages = get_languages();
        $result = [];
        foreach ($args as $key => $block) {
            if (!isset($key) || !is_numeric($key)) {
                continue;
            }

            try {
                $this->requireArgs($block, ['name', 'labels']);
            } catch (SugarApiExceptionMissingParameter $e) {
                $error = 'Array ' . $key . ' has error: ' . $e->getMessage();
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionMissingParameter($error);
            }

            $moduleName = $block['name'];
            if (!$this->isValidModule($moduleName)) {
                $error = 'Array ' . $key . ' has error: ' . 'Invalid module: ' . $moduleName;
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }

            $labels = $block['labels'];
            if (empty($labels)) {
                $error = 'Array ' . $key . ' has error: ' . 'labels are not defined';
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }

            $result[$key]['name'] = $moduleName;
            foreach ($labels as $lang => $fields) {
                if (!array_key_exists($lang, $allLanguages)) {
                    $error = 'Module ' . $moduleName . ' contains invalid lang key: ' . $lang;
                    LoggerManager::getLogger()->fatal($error);
                    throw new SugarApiExceptionInvalidParameter($error);
                }
                if (empty($fields)) {
                    $error = 'In module ' . $moduleName . ' language ' . $lang . ' contains empty fields.';
                    LoggerManager::getLogger()->fatal($error);
                    throw new SugarApiExceptionMissingParameter($error);
                }
                try {
                    $result[$key]['labels'][$lang] = $this->saveFieldLabels($moduleName, $lang, $fields);
                } catch (SugarApiExceptionInvalidParameter $e) {
                    throw new SugarApiExceptionInvalidParameter($e);
                }
            }
        }
        return $result;
    }

    /**
     * Updates translations for dropdown items in dropdowns
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     */
    public function updateDropdowns(ServiceBase $api, array $args): array
    {
        global $current_user;

        // Only an admin can create new custom fields
        if (!is_admin($current_user)) {
            throw new SugarApiExceptionNotAuthorized('Current user is not authorized.');
        }

        $dropdownParams = [
            'view_package' => 'studio',
            'skip_sync' => true,
        ];
        //needs this setting in parser.dropdown.php
        $_REQUEST['view_package'] = 'studio';
        $dropdownParser = $this->getDropdownParser();

        $allLanguages = get_languages();
        $result = [];
        foreach ($args as $key => $block) {
            if (!isset($key) || !is_numeric($key)) {
                continue;
            }

            try {
                $this->requireArgs($block, ['name', 'labels']);
            } catch (SugarApiExceptionMissingParameter $e) {
                $error = 'Array ' . $key . ' has error: ' . $e->getMessage();
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionMissingParameter($error);
            }

            $dropdownName = $block['name'];
            SugarAutoLoader::load('include/utils.php');
            if (empty(translate($dropdownName))) {
                $error = 'Dropdown doesn\'t exist: ' . $dropdownName;
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }

            $labels = $block['labels'];
            if (empty($labels)) {
                $error = 'Array ' . $key . ' has error: ' . 'labels are not defined';
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }

            $result[$key]['name'] = $dropdownName;
            foreach ($labels as $lang => $list) {
                if (!array_key_exists($lang, $allLanguages)) {
                    $error = 'Dropdown ' . $dropdownName . ' contains invalid lang key: ' . $lang;
                    LoggerManager::getLogger()->fatal($error);
                    throw new SugarApiExceptionInvalidParameter($error);
                }
                $params = $this->prepareDropdownLabels($dropdownName, $lang, $list);
                if (!empty($params)) {
                    $dropdownParser->saveDropDown(
                        array_merge($dropdownParams, $params),
                        true,
                        $lang
                    );
                    $result[$key]['labels'][$lang] = $list;
                } else {
                    $error = 'In dropdown ' . $dropdownName . ' language ' . $lang . ' contains empty list.';
                    LoggerManager::getLogger()->fatal($error);
                    throw new SugarApiExceptionMissingParameter($error);
                }
            }
        }
        return $result;
    }

    /**
     * Saves field labels to associated module's language files
     *
     * @param string $module
     * @param string $lang
     * @param array $fields
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function saveFieldLabels(string $module, string $lang, array $fields): array
    {
        $bean = BeanFactory::newBean($module);
        if (empty($bean->field_defs)) {
            $error = 'This module has invalid field_defs: ' . $module;
            LoggerManager::getLogger()->fatal($error);
            throw new SugarApiExceptionInvalidParameter($error);
        }

        $labels = [];
        $parser = $this->getLabelParser($module);
        // Groups labels for the same language in the module
        foreach ($fields as $fieldName => $label) {
            if (!array_key_exists($fieldName, $bean->field_defs)) {
                $error = 'Module ' . $module . ' contains invalid field: ' . $fieldName;
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }
            $labelName = $bean->field_defs[$fieldName]['vname'] ?? '';
            $labelValue = !empty($label) ? $label : $fieldName;
            if (!empty($labelName) && !empty($labelValue)) {
                $labels[strtoupper($labelName)] = SugarCleaner::cleanHtml(from_html($labelValue), false);
            } else {
                $error = 'Module ' . $module . ' contains invalid data: ' . $fieldName .
                    ' doesn\'t have a vname or label value is not provided.';
                LoggerManager::getLogger()->fatal($error);
                throw new SugarApiExceptionInvalidParameter($error);
            }
        }
        if (!empty($labels)) {
            $parser->addLabels($lang, $labels, $module, null);

            // Clears the language cache to make sure the view picks up the latest
            $cache_key = LanguageManager::getLanguageCacheKey($module, $lang);
            sugar_cache_clear($cache_key);
        }
        return $labels;
    }

    /**
     * Gets label parser
     *
     * @param string $module
     * @return object
     */
    protected function getLabelParser(string $module): object
    {
        return ParserFactory::getParser(MB_LABEL, $module);
    }

    /**
     * Gets dropdown parser
     *
     * @return object
     */
    protected function getDropdownParser(): object
    {
        return ParserFactory::getParser(MB_DROPDOWN);
    }

    /**
     * Formats system dropdown item list to be ready for save
     *
     * @param string $name
     * @param string $lang
     * @return array
     */
    protected function formatSysDropdownItems(string $name, string $lang): array
    {
        $appListStrings = return_app_list_strings_language($lang);
        $currentDD = isset($appListStrings[$name]) && is_array($appListStrings[$name]) ? $appListStrings[$name] : [];
        $dropdownItems = [];
        foreach ($currentDD as $key => $value) {
            $itemName = $key == '' ? '-blank-' : $key;
            $dropdownItems[] = [$itemName, $value];
        }
        return $dropdownItems;
    }

    /**
     * Formats dropdown item list to be ready for save
     *
     * @param array $itemList
     * @return array
     */
    protected function formatDropdownItems(array $itemList): array
    {
        $dropdownItems = [];
        foreach ($itemList as $dropdownKey => $dropdownValue) {
            if (!empty($dropdownKey) && !empty($dropdownValue)) {
                $dropdownItems[] = [$dropdownKey, $dropdownValue];
            }
        }
        return $dropdownItems;
    }

    /**
     * Prepares dropdown labels to associated dropdown/language
     *
     * @param string $name
     * @param string $lang
     * @param array $list
     * @return array
     */
    protected function prepareDropdownLabels(string $name, string $lang, array $list): array
    {
        $dropdownList = $this->formatDropdownItems($list);
        if (!empty($dropdownList)) {
            $sysDDList = $this->formatSysDropdownItems($name, $lang);
            // Merges new list to system list
            $dropdownList = array_merge($sysDDList, $dropdownList);

            return [
                'dropdown_name' => $name,
                'dropdown_lang' => $lang,
                'list_value' => json_encode($dropdownList),
            ];
        }
        return [];
    }
}
