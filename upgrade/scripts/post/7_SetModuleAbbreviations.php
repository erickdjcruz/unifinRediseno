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

/**
 * Upgrade script to set all module abbreviations in language files
 */
class SugarUpgradeSetModuleAbbreviations extends UpgradeScript
{
    public $order = 7101;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->log('Updating moduleIconList for all languages...');
            $this->updateModuleAbbreviations();
            $this->log('Done updating moduleIconList for all languages');
        }
    }

    /**
     * Returns whether this upgrade script should run
     *
     * @return bool true if the upgrade script should run
     */
    protected function shouldRun()
    {
        return version_compare($this->from_version, '12.3.0', '<');
    }

    /**
     * Returns the list of all available language keys
     *
     * @return array the list of language keys
     */
    protected function getAvailableLanguages()
    {
        return array_keys(get_all_languages());
    }

    /**
     * Returns whether the given language has a custom language file
     * that overrides the core one
     *
     * @param string $language The key of the language
     * @return bool true if the language is customized
     */
    protected function isLanguageCustomized($language)
    {
        return file_exists("custom/include/language/$language.lang.php");
    }

    /**
     * Returns the core app list strings for the given language
     *
     * @param string $language The key of the language
     */
    protected function getCoreAppListStrings($language)
    {
        $app_list_strings = [];
        if (file_exists("include/language/$language.lang.php")) {
            include "include/language/$language.lang.php";
        }
        return $app_list_strings;
    }

    /**
     * Returns the custom app list strings for the given language
     *
     * @param string $language The key of the language
     */
    protected function getCustomAppListStrings($language)
    {
        $app_list_strings = [];
        if ($this->isLanguageCustomized($language)) {
            include "custom/include/language/$language.lang.php";
        }
        return $app_list_strings;
    }

    /**
     * Returns the overall app list strings for the given language
     *
     * @param string $language The key of the language
     */
    protected function getAllAppListStrings($language)
    {
        return return_app_list_strings_language($language, false);
    }

    /**
     * Updates the module abbreviations stored in language files
     */
    protected function updateModuleAbbreviations()
    {
        $languages = $this->getAvailableLanguages();
        foreach ($languages as $language) {
            $this->updateCustomLanguageFile($language);
            $this->updateExtensions($language);
        }
    }

    /**
     * Copies abbreviations for OOB modules to the main custom language file
     *
     * @param string $language The key of the language to update
     */
    protected function updateCustomLanguageFile($language)
    {
        // If there is no custom language file, then there is nothing to do
        if (!$this->isLanguageCustomized($language)) {
            return;
        }

        $coreAppListStrings = $this->getCoreAppListStrings($language);
        $customAppListStrings = $this->getCustomAppListStrings($language);

        $coreAbbreviations = $coreAppListStrings['moduleIconList'] ?? [];
        $customAbbreviations = $customAppListStrings['moduleIconList'] ?? [];

        $appListStringsToUpdate = [];
        foreach ($coreAbbreviations as $moduleName => $moduleAbbreviation) {
            if (empty($customAbbreviations[$moduleName])) {
                $appListStringsToUpdate['moduleIconList'][$moduleName] = $coreAbbreviations[$moduleName];
            }
        }

        if (!empty($appListStringsToUpdate)) {
            $this->updateCustomLanguageFileAppListStrings($language, $appListStringsToUpdate);
        }
    }

    /**
     * Writes app_list_strings updates to a custom language file
     *
     * @param string $language The key of the language to update
     * @param array $entries Updated app_list_strings entries to add to the custom file
     */
    protected function updateCustomLanguageFileAppListStrings($language, $entries)
    {
        if (empty($entries)) {
            return;
        }

        $fileData = trim(file_get_contents("custom/include/language/$language.lang.php"));
        if (substr($fileData, -2) == '?>') {
            // strip closing tag
            $fileData = substr($fileData, 0, -2);
        }
        $fileData .= "\n/* This file was modified by Sugar Upgrade */\n";
        foreach ($entries as $key => $array) {
            foreach ($array as $akey => $aval) {
                $fileData .= "\$app_list_strings['$key']['$akey'] = " . var_export($aval, true) . ";\n";
            }
        }

        $this->putFile("custom/include/language/$language.lang.php", $fileData);
        $this->log("Updated custom/include/language/$language.lang.php");
    }

    /**
     * Updates the moduleIconList extensions for a given language. If a
     * custom moduleIconList entry already exists for a module in the language,
     * we will use that, but limit it to two characters. Otherwise, we will
     * build the default module abbreviation based on the module label
     *
     * @param string $language The key of the language to update
     */
    protected function updateExtensions($language)
    {
        $newAbbreviations = [];

        $coreAppListStrings = $this->getCoreAppListStrings($language);
        $coreAbbreviations = $coreAppListStrings['moduleIconList'] ?? [];

        $allAppListStrings = $this->getAllAppListStrings($language);
        $allAbbreviations = $allAppListStrings['moduleIconList'] ?? [];
        $allModuleList = $allAppListStrings['moduleList'] ?? [];

        foreach ($allModuleList as $moduleName => $moduleLabel) {
            if (!empty($allAbbreviations[$moduleName]) &&
                $allAbbreviations[$moduleName] !== $coreAbbreviations[$moduleName]) {
                $newAbbreviations[$moduleName] = sugarSubstr($allAbbreviations[$moduleName], 0, 2);
            } else {
                $newAbbreviations[$moduleName] = MBModule::getModuleAbbreviatedLabel($moduleLabel);
            }
        }

        if (!empty($newAbbreviations)) {
            $this->updateDropdownExtensions($language, 'moduleIconList', $newAbbreviations);
        }
    }

    /**
     * Helper function to perform the update of the moduleIconList extensions
     *
     * @param string $language The key of the language to update
     * @param string $dropdown The key of the dropdown to update
     * @param array $items the key/value pairs to update in the dropdown
     */
    protected function updateDropdownExtensions($language, $dropdown, $items)
    {
        $params = [
            'dropdown_name' => $dropdown,
            'dropdown_lang' => $language,
            'use_push' => true,
        ];

        $count = 0;
        foreach ($items as $key => $value) {
            $params['slot_' . $count] = $count;
            $params['key_' . $count] = $key;
            $params['value_' . $count] = $value;
            $params['delete_' . $count] = '';
            $count++;
        }

        DropDownHelper::saveDropDown($params, true);
    }
}
