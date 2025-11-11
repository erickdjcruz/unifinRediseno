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

use Sugarcrm\Sugarcrm\Util\Files\FileLoader;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/**
 * API for Modules Names and Icons.
 */
class ModuleNamesAndIconsApi extends AdministrationApi
{
    /**
     * @var array<string, mixed>|mixed
     */
    public $renameDefs;
    /**
     * @var mixed|array<int|string, array<string, mixed>>
     */
    public $requestModules;
    /**
     * List of all renamable modules
     *
     * @var array
     */
    private $modules = [];

    /**
     * Modules needing QRR that where renamed/change icon/color
     *
     * @var array
     */
    private $rebuild_modules = [];

    /**
     * Selected language user is renaming for (eg. en_us).
     *
     * @var string
     */
    public $selectedLanguage;

    /**
     * An array containing the modules which should be renamed.
     *
     * @var array
     */
    private $changedModules;

    /**
     * A string that contains the module currently being worked on
     *
     * @var string
     */
    private $changedModule;

    /**
     * An array containing the modules which have had their module strings modified as part of the
     * renaming process.
     *
     * @var array
     */
    private $renamedModules = [];

    /**
     * The name of the ext file
     * @var string
     */
    public const EXT_FILE_NAME = 'module_names_and_icons_admin.php';

    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getModules' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'module-names-and-icons', '?'],
                'pathVars' => ['', '', 'language'],
                'method' => 'getModules',
                'shortHelp' => 'Gets modules that whose names and icons can be changed',
                'longHelp' => 'modules/Administration/clients/base/api/help/ModuleNamesAndIconsApiGet.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.19',
            ],
            'setModules' => [
                'reqType' => 'PUT',
                'path' => ['Administration', 'module-names-and-icons', '?'],
                'pathVars' => ['', '', 'language'],
                'method' => 'setModules',
                'shortHelp' => 'Sets module names and icons',
                'longHelp' => 'modules/Administration/clients/base/api/help/ModuleNamesAndIconsApiPut.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Gets configuration details for a category
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getModules(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();

        global $locale;

        $dh = new DropDownHelper();
        $modules_list = [];

        if (!empty($args['language'])) {
            $selected_lang = $args['language'];
        } else {
            $selected_lang = $locale->getAuthenticatedUserLanguage();
        }
        if ($selected_lang == $GLOBALS['current_language']) {
            $my_list_strings = $GLOBALS['app_list_strings'];
        } else {
            $my_list_strings = return_app_list_strings_language($selected_lang);
        }

        $displayModules = $dh->filterDropDown('moduleList', $my_list_strings['moduleList']);
        $displayModulesSingular = $my_list_strings['moduleListSingular'];
        $abbreviations = $my_list_strings['moduleIconList'];
        foreach ($displayModules as $module => $pluralValue) {
            $singularValue = $displayModulesSingular[$module] ?? $pluralValue;
            $abbreviation = $abbreviations[$module] ?? '';
            array_push($modules_list, $this->buildModuleList($module, $singularValue, $pluralValue, $abbreviation));
        }

        return $modules_list;
    }

    /**
     * Saves new configuration details for a category and returns updated config
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function setModules(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();

        global $locale, $current_language, $current_user;

        if (!empty($args['language'])) {
            $this->selectedLanguage = $args['language'];
        } else {
            $this->selectedLanguage = $locale->getAuthenticatedUserLanguage();
        }

        $this->modules = $args['changedModules'];

        //Clear all relevant language caches
        $this->clearLanguageCaches();

        //Retrieve changes the user is requesting and store previous values for future use.
        $this->changedModules = $this->getChangedModules();

        foreach ($this->modules as $module) {
            $key = $module['module_key'];
            if (empty($key)) {
                continue;
            }
            if (\BeanFactory::getObjectName($key)) {
                $moduleName = \BeanFactory::getObjectName($key);
            } else {
                $moduleName = $key;
            }

            if ($moduleName === 'Home') {
                $properties = ['display_type', 'icon', 'color',];
                $admin = BeanFactory::newBean('Administration');
                foreach ($properties as $property) {
                    $admin->saveSetting('Home', $property, $module['module_' . $property], 'base');
                }
                array_push($this->rebuild_modules, $key);
            } else {
                // Update module vardefs for any changed properties that use it
                $this->changeModuleVardefs($key, $moduleName, $module);
            }
        }

        //Change module, appStrings, subpanels, and related links.
        $this->changeAppStringEntries();
        $this->changeAllModuleModStrings();
        $this->renameAllRelatedLinks();
        $this->renameAllSubpanels();
        $this->changeStringsInRelatedModules();
        $this->changeGlobalAppStrings();

        if (safeCount($this->rebuild_modules) > 0) {
            include_once 'modules/Administration/QuickRepairAndRebuild.php';
            $repair = new \RepairAndClear();
            $repair->repairAndClearAll(['rebuildExtensions'], $this->rebuild_modules, true, false);
            MetaDataManager::refreshModulesCache($this->rebuild_modules);
        }

        $cacheDefsJs = sugar_cached('modules/modules_def_' . $current_language . '_' . md5($current_user->id) . '.js');
        if (file_exists($cacheDefsJs)) {
            unlink($cacheDefsJs);
        }

        if ($this->selectedLanguage == $GLOBALS['current_language']) {
            $my_list_strings = $GLOBALS['app_list_strings'];
        } else {
            $my_list_strings = return_app_list_strings_language($this->selectedLanguage);
        }
        $displayModulesPlural = $my_list_strings['moduleList'];
        $displayModulesSingular = $my_list_strings['moduleListSingular'];
        $abbreviations = $my_list_strings['moduleIconList'];
        $modules_list = [];

        foreach ($this->rebuild_modules as $module) {
            $singularValue = $displayModulesSingular[$module];
            $pluralValue = $displayModulesPlural[$module];
            $abbreviation = $abbreviations[$module] ?? '';
            array_push($modules_list, $this->buildModuleList($module, $singularValue, $pluralValue, $abbreviation));
        }

        return $modules_list;
    }

    /**
     * Build the required metadata attributes for the provided module
     *
     * @param string $module Name of the module
     * @param string $singularValue Singular value of the module name
     * @param string $pluralValue Plural value of the module name
     * @param string $abbreviation Abbreviation of the module name
     * @return array
     */
    protected function buildModuleList(string $module, string $singularValue, string $pluralValue, string $abbreviation)
    {
        $moduleMeta = $this->getMetaData($module);
        return [
            'module_key' => $module,
            'module_name' => $module,
            'module_singular' => $singularValue,
            'module_plural' => $pluralValue,
            'module_abbreviation' => $abbreviation,
            'module_display_type' => $moduleMeta['display_type'],
            'module_color' => $moduleMeta['color'],
            'module_icon' => $moduleMeta['icon'],
        ];
    }

    /**
     * Get metadata for the given module
     *
     * @param string $module Name of module for which the metadata need to be retrieved
     * @return array
     */
    protected function getMetaData($module)
    {
        $meta = new MetaDataManager();
        $moduleMeta = $meta->getModuleData($module);
        if ($module === 'Home') {
            global $dictionary;
            VardefManager::loadVardef($module, $module);
            if (isset($dictionary[$module])) {
                $moduleMeta['icon'] = $dictionary[$module]['icon'];
                $moduleMeta['color'] = $dictionary[$module]['color'];
            }
        }
        return $moduleMeta;
    }

    /**
     * Ensure current user has admin permissions
     * @throws SugarApiExceptionNotAuthorized
     */
    protected function ensureAdminUser()
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isAdmin()) {
            throw new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['EXCEPTION_NOT_AUTHORIZED']
            );
        }
    }

    /**
     * Function to generate custom vardef properties
     *
     * @param string $key
     * @param string $moduleName
     * @param array $module
     * @return void
     */
    protected function changeModuleVardefs(string $key, string $moduleName, array $module)
    {
        $moduleMeta = $this->getMetaData($key);
        $properties = ['display_type', 'icon', 'color'];
        $values = [];
        $changed = false;
        foreach ($properties as $property) {
            $currentValue = $moduleMeta[$property] ?? null;
            $newValue = $module['module_' . $property] ?? null;
            $values[$property] = $newValue;
            if ($currentValue !== $newValue) {
                $changed = true;
            }
        }

        if ($changed) {
            $out = "<?php\n // created: " . date('Y-m-d H:i:s') . "\n";
            foreach ($values as $property => $value) {
                $out .= override_value_to_string_recursive([$moduleName, $property], 'dictionary', $value);
                $out .= "\n";
            }

            // Write to the file
            $dir = 'custom/Extension/modules/' . basename($key) . '/Ext/Vardefs';
            mkdir_recursive($dir);
            $file = $dir . '/' . self::EXT_FILE_NAME;
            sugar_file_put_contents_atomic($file, $out);
            array_push($this->rebuild_modules, $key);
        }
    }

    /**
     * Sets the rename defs array into this object for use in renaming strings in
     * related modules and in the global app and app list strings
     */
    protected function setRenameDefs()
    {
        if (empty($this->renameDefs['modules'])) {
            require 'modules/Studio/wizards/renamedefs.php';
            if (isset($renamedefs)) {
                $this->renameDefs = $renamedefs;
            }
        }
    }

    /**
     * Manually set the changedModules when running this code outside the browser.
     */
    public function setChangedModules($modules)
    {
        if (!empty($modules)) {
            $this->changedModules = $modules;
        }
    }

    /**
     * Changes module names in related module strings
     */
    public function changeStringsInRelatedModules()
    {
        $this->setRenameDefs();
        if (isset($this->renameDefs['modules']) && is_array($this->renameDefs['modules'])) {
            foreach ($this->renameDefs['modules'] as $module => $defs) {
                $this->renameCertainModuleModStrings($module, $defs);
            }
        }
    }

    /**
     * Changes module name in global app and app list strings
     */
    protected function changeGlobalAppStrings()
    {
        $this->setRenameDefs();
        if (!isset($this->renameDefs['global']) || !is_array($this->renameDefs['global'])) {
            $GLOBALS['log']->warn('No rename defs found for global app strings');
            return;
        }

        // Get the current app_strings for checking keys and values
        $app_strings = return_application_language($this->selectedLanguage);

        $new = [];
        foreach ($this->changedModules as $changedModuleName => $renameFields) {
            // Loop over all defined app string keys to handle replacements
            foreach ($this->renameDefs['global'] as $def) {
                // Make sure changes to Global app strings only affect specific
                // strings for a module
                if (isset($def['source']) && $def['source'] == $changedModuleName) {
                    // Only change something that exists to begin with
                    if (isset($app_strings[$def['name']])) {
                        // Check to see if this string has already been updated by another module renaming
                        // before we've saved it to app_strings
                        $updateStr = true;
                        if (isset($new[$def['name']])) {
                            $oldValue = $new[$def['name']];
                            $pattern = "/\b" . $renameFields[$def['type']] . "\b/i";
                            if (preg_match($pattern, $oldValue)) {
                                // this string has already been updated, dont update again
                                $updateStr = false;
                            }
                        } else {
                            $oldValue = $app_strings[$def['name']];
                        }

                        if ($updateStr) {
                            $this->changedModule = $changedModuleName;
                            $newValue = $this->replaceSingleLabel($oldValue, $renameFields, $def);
                        }

                        // If there was a change, add it to the new array
                        if ($updateStr && $newValue != $oldValue) {
                            $new[$def['name']] = $newValue;
                        }
                    }
                }
            }
        }

        // Save the new strings now
        $this->saveCustomLanguageStrings($new);
    }

    /**
     * Saves new custom language strings for the application
     *
     * @param array $appStrings The new app strings array values to save
     * @return boolean
     */
    public function saveCustomLanguageStrings($appStrings)
    {
        $changed = null;
        if (empty($appStrings)) {
            // Nothing to save...
            return true;
        }

        $contents = return_custom_app_list_strings_file_contents($this->selectedLanguage);

        // Clean up the closing PHP tag
        $contents = str_replace('?>', '', $contents);

        // Create our file opening
        if (empty($contents)) {
            $contents = "<?php\n";
        }

        // Get the current strings to see what might need to be changed
        $all_app_strings = return_application_language($this->selectedLanguage);

        // Flag that will tell us if there is a need to write
        $cached = false;

        // Now loop the new strings and check what has changed, if anything
        foreach ($appStrings as $key => $value) {
            if (!isset($all_app_strings[$key]) || $all_app_strings[$key] !== $value) {
                //clear out the old value
                $pattern_match = '/\s*\$app_strings\s*\[\s*\'' . $key . '\'\s*\]\s*=\s*[\'\"]{1}.*?[\'\"]{1};\s*/ism';
                $contents = preg_replace($pattern_match, "\n", $contents);
                $contents .= "\n\$app_strings['$key']=" . var_export_helper($value) . ';';
                $changed = true;
            }
        }

        // Only bother saving if there were changes to save
        if ($changed) {
            return save_custom_app_strings_contents($contents, $this->selectedLanguage);
        }

        // No changes, no worries
        return true;
    }

    /**
     * Rename all subpanels within the application.
     */
    private function renameAllSubpanels()
    {
        global $beanList;

        foreach ($beanList as $moduleName => $beanName) {
            if (class_exists($beanName)) {
                $this->renameModuleSubpanel($moduleName, $beanName);
            } else {
                $GLOBALS['log']->error("Class $beanName does not exist, unable to rename.");
            }
        }
    }

    /**
     * Rename subpanels for a particular module.
     *
     * @param string $moduleName The name of the module to be renamed
     * @param string $beanName The name of the SugarBean to be renamed.
     * @return void
     */
    private function renameModuleSubpanel($moduleName, $beanName)
    {
        $GLOBALS['log']->info("About to rename subpanel for module: $moduleName");
        $bean = BeanFactory::newBean($moduleName);
        //Get the subpanel def
        $subpanelDefs = $this->getSubpanelDefs($bean);

        if (empty($subpanelDefs)) {
            $GLOBALS['log']->debug("Found empty subpanel defs for $moduleName");
            return;
        }

        $mod_strings = return_module_language($this->selectedLanguage, $moduleName);
        $replacementStrings = [];

        //Iterate over all subpanel entries and see if we need to make a change.
        foreach ($subpanelDefs as $subpanelName => $subpanelMetaData) {
            $GLOBALS['log']->debug("Examining subpanel definition for potential rename: $subpanelName ");
            //For each subpanel def, check if they are in our changed modules set.
            foreach ($this->changedModules as $changedModuleName => $renameFields) {
                if (!(isset($subpanelMetaData['type']) && $subpanelMetaData['type'] == 'collection') //Dont bother with collections
                    && isset($subpanelMetaData['module'])
                    && $subpanelMetaData['module'] == $changedModuleName
                    && isset($subpanelMetaData['title_key'])
                ) {
                    $replaceKey = $subpanelMetaData['title_key'];
                    if (!isset($mod_strings[$replaceKey])) {
                        $GLOBALS['log']->info("No module string entry defined for key: {$replaceKey}");
                        continue;
                    }
                    $oldStringValue = $mod_strings[$replaceKey];
                    $replacementStrings[$replaceKey] = $this->renameModuleRelatedStrings($oldStringValue, $renameFields);
                }
            }
        }

        //Now we can write out the replaced language strings for each module
        if (safeCount($replacementStrings) > 0) {
            $GLOBALS['log']->debug("Writing out labels for subpanel changes for module $moduleName, labels: " . var_export($replacementStrings, true));
            ParserLabel::addLabels($this->selectedLanguage, $replacementStrings, $moduleName);
            $this->renamedModules[$moduleName] = true;
        }
    }

    /**
     * Retrieve the subpanel definitions for a given SugarBean object. Unforunately we can't reuse
     * any of the SubPanelDefinion.php functions.
     *
     * @param SugarBean $bean
     * @return array The subpanel definitions.
     */
    private function getSubpanelDefs($bean)
    {
        $viewdefs = [];
        if (empty($bean->module_dir)) {
            return [];
        }

        $layout_defs = [];

        // Handle things differently for BWC modules
        if (isModuleBWC($bean->module_dir)) {
            foreach (SugarAutoLoader::existingCustom('modules/' . $bean->module_dir . '/metadata/subpaneldefs.php') as $file) {
                require FileLoader::validateFilePath($file);
            }

            $defs = SugarAutoLoader::loadExtension('layoutdefs', $bean->module_dir);
            if ($defs) {
                require FileLoader::validateFilePath($defs);
            }
        } else {
            // Handle things the new way
            foreach (SugarAutoLoader::existingCustom('modules/' . $bean->module_dir . '/clients/base/layouts/subpanels/subpanels.php') as $file) {
                require FileLoader::validateFilePath($file);
            }

            // Add in any studio customizations
            $ext = 'custom/modules/' . $bean->module_dir . '/Ext/clients/base/layouts/subpanels/subpanels.ext.php';
            if (file_exists($ext)) {
                require FileLoader::validateFilePath($ext);
            }

            // Massage defs to look like old style for use in the rename process
            if (isset($viewdefs[$bean->module_dir]['base']['layout']['subpanels']['components'])) {
                $layout_defs = $this->getSidecarSubpanelDefsAsLegacy($viewdefs[$bean->module_dir]['base']['layout']['subpanels']['components'], $bean);
            }
        }

        return $layout_defs[$bean->module_dir]['subpanel_setup'] ?? $layout_defs;
    }

    /**
     * Gets sidecar subpanel layout defs in the BWC format
     *
     * @param array $components Existing sidecar subpanel layout
     * @param SugarBean $bean The bean that the subpanels are being scraped for
     * @return array
     */
    protected function getSidecarSubpanelDefsAsLegacy(array $components, SugarBean $bean)
    {
        $return = [];

        // Used in keeping uniqueness of link name based keys
        $counter = 0;

        foreach ($components as $component) {
            // We can only really do this if there is a label and a link
            if (isset($component['label']) && isset($component['context']['link'])) {
                // The link is used as an index and a module finder
                $link = $component['context']['link'];

                // If there is a module on the link field, we are good
                if (!empty($bean->field_defs[$link]['module'])) {
                    $def['module'] = $bean->field_defs[$link]['module'];
                } else {
                    // If there isn't a module then we need to load the
                    // relationship to get the related module
                    $bean->load_relationship($link);
                    if (!empty($bean->$link)) {
                        $relMod = $bean->$link->getRelatedModuleName();
                        if (!empty($relMod)) {
                            $def['module'] = $relMod;
                        }
                    }
                }

                // If a module was found then proceed to set the def
                if (!empty($def['module'])) {
                    // Add the title key in
                    $def['title_key'] = $component['label'];

                    // Make sure we are not overriding this index
                    if (isset($return[$bean->module_dir]['subpanel_setup'][$link])) {
                        // this is actually meaningless in the scope of
                        // things so manipulating this isn't a world changer
                        $link .= '_' . ++$counter;
                    }

                    // Set the new def into the expected
                    $return[$bean->module_dir]['subpanel_setup'][$link] = $def;
                }
            }
        }

        return $return;
    }

    /**
     * Rename all related linked within the application
     */
    private function renameAllRelatedLinks()
    {
        global $beanList;

        foreach ($beanList as $moduleName => $beanName) {
            $this->renameModuleRelatedLinks($moduleName, $beanName);
        }
    }

    /**
     * Rename module-related strings such as links and dashlet strings.
     *
     * @param string $oldString The original language string.
     * @param array $renameFields Array of strings containing new singular/plural
     *  labels (from rename modules form) and previous singular/plural fields.
     * @param boolean $pluralFirst Set to true to replace plural first, otherwise, singular first.
     * @return string The language string with the new singular/plural replacements.
     */
    public function renameModuleRelatedStrings($oldString, $renameFields, $pluralFirst = true)
    {
        // Ignore empty fields. If we get to the body of this condition there is a problem...
        if ($renameFields['prev_singular'] === '' || $renameFields['prev_plural'] === '') {
            return $oldString;
        }

        // This pattern searches for whole words to replace in the old string. Also,
        // make sure characters like $, ^, /, etc. are escaped before being embedded into the pattern.
        // Note: 'u' modifier is for UTF-8 support.
        $replacePatterns = [
            'singular' => '/(?<=\W|^)(' . preg_quote($renameFields['prev_singular'], '/') . ')(?=\W|$)/u',
            'plural' => '/(?<=\W|^)(' . preg_quote($renameFields['prev_plural'], '/') . ')(?=\W|$)/u',
        ];
        $replacedString = null;
        // Replace by plural or singular first depending on $pluralFirst.
        $field = $pluralFirst ? 'plural' : 'singular';
        if ($renameFields[$field] !== '') {
            $replacedString = preg_replace($replacePatterns[$field], $renameFields[$field], $oldString);
        }

        // Swap fields and do the second replacement.
        $field = $pluralFirst ? 'singular' : 'plural';
        if (!is_null($replacedString) && $renameFields[$field] !== '') {
            $replacedString = preg_replace($replacePatterns[$field], $renameFields[$field], $replacedString);
        }

        // If any expression fails, revert to old language string.
        return (!is_null($replacedString) ? $replacedString : $oldString);
    }

    /**
     * Rename the related links within a module.
     *
     * @param string $moduleName The module to be renamed
     * @param string $moduleClass The class name of the module to be renamed
     * @return void
     */
    private function renameModuleRelatedLinks($moduleName, $moduleClass)
    {
        $GLOBALS['log']->info("Begining to renameModuleRelatedLinks for $moduleClass\n");
        $tmp = BeanFactory::newBean($moduleName);
        if (!is_object($tmp) || !method_exists($tmp, 'get_related_fields')) {
            $GLOBALS['log']->info("Unable to resolve linked fields for module $moduleClass ");
            return;
        }

        $linkedFields = $tmp->get_related_fields();
        $mod_strings = return_module_language($this->selectedLanguage, $moduleName);
        $replacementStrings = [];

        foreach ($linkedFields as $link => $linkEntry) {
            //For each linked field check if the module referenced to is in our changed module list.
            foreach ($this->changedModules as $changedModuleName => $renameFields) {
                if (isset($linkEntry['module']) && $linkEntry['module'] == $changedModuleName) {
                    $GLOBALS['log']->debug("Begining to rename for link field {$link}");
                    if (!isset($linkEntry['vname'])
                        || (isset($linkEntry['vname']) && !isset($mod_strings[$linkEntry['vname']]))) {
                        $GLOBALS['log']->debug("No label attribute for link $link, continuing.");
                        continue;
                    }

                    $replaceKey = $linkEntry['vname'] ?? '';
                    $oldStringValue = $mod_strings[$replaceKey];
                    // If the plural string is longer than singular fall-back to singular replacements first.
                    $pluralFirst = strlen($renameFields['prev_plural']) > strlen($renameFields['prev_singular']);
                    $replacementStrings[$replaceKey] = $this->renameModuleRelatedStrings($oldStringValue, $renameFields, $pluralFirst);
                }
            }
        }

        //Now we can write out the replaced language strings for each module
        if (safeCount($replacementStrings) > 0) {
            $GLOBALS['log']->debug("Writing out labels for link changes for module $moduleName, labels: " . var_export($replacementStrings, true));
            ParserLabel::addLabels($this->selectedLanguage, $replacementStrings, $moduleName);
            $this->renamedModules[$moduleName] = true;
        }
    }


    /**
     * Rename all module strings within the application.
     */
    private function changeAllModuleModStrings()
    {
        foreach ($this->changedModules as $moduleName => $replacementLabels) {
            $this->changeModuleModStrings($moduleName, $replacementLabels);
        }
    }

    /**
     * Rename all module strings within the leads module.
     *
     * @param string $targetModule The name of the module that owns the labels to be changed.
     * @param array $labelKeysToReplace The labels to be changed.
     */
    private function renameCertainModuleModStrings($targetModule, $labelKeysToReplace)
    {
        $GLOBALS['log']->debug("Beginning to rename labels for $targetModule module");
        foreach ($this->changedModules as $moduleName => $replacementLabels) {
            $this->changeCertainModuleModStrings($moduleName, $replacementLabels, $targetModule, $labelKeysToReplace);
        }
    }

    /**
     * For a particular module, rename any relevant module strings that need to be replaced.
     *
     * @param string $moduleName The name of the module to be renamed.
     * @param  $replacementLabels
     * @param string $targetModule The name of the module that owns the labels to be changed.
     * @param array $labelKeysToReplace The labels to be changed.
     * @return void
     */
    private function changeCertainModuleModStrings($moduleName, $replacementLabels, $targetModule, $labelKeysToReplace)
    {
        $GLOBALS['log']->debug("Beginning to change module labels for : $moduleName");
        $currentModuleStrings = return_module_language($this->selectedLanguage, $targetModule);

        $replacedLabels = [];
        foreach ($labelKeysToReplace as $entry) {
            if (!isset($entry['source']) || $entry['source'] != $moduleName) {
                // skip this entry if the source module does not match the module being renamed
                continue;
            }

            $formattedLanguageKey = $this->formatModuleLanguageKey($entry['name'], $replacementLabels);

            //If the static of dynamic key exists it should be replaced.
            if (isset($currentModuleStrings[$formattedLanguageKey])) {
                $oldStringValue = $currentModuleStrings[$formattedLanguageKey];
                $this->changedModule = $moduleName;
                $newStringValue = $this->replaceSingleLabel($oldStringValue, $replacementLabels, $entry);
                if ($oldStringValue != $newStringValue) {
                    $replacedLabels[$formattedLanguageKey] = $newStringValue;
                }
            }
        }

        //Save all entries
        ParserLabel::addLabels($this->selectedLanguage, $replacedLabels, $targetModule);
        $this->renamedModules[$targetModule] = true;
    }

    /**
     * For a particular module, rename any relevant module strings that need to be replaced.
     *
     * @param string $moduleName The name of the module to be renamed.
     * @param  $replacementLabels
     * @return array
     */
    public function changeModuleModStrings($moduleName, $replacementLabels)
    {
        $GLOBALS['log']->info("Beginning to change module labels for: $moduleName");
        $currentModuleStrings = return_module_language($this->selectedLanguage, $moduleName);
        $labelKeysToReplace = [
            ['name' => 'LNK_NEW_RECORD', 'type' => 'singular'], //Module built modules, Create <moduleName>
            ['name' => 'LNK_LIST', 'type' => 'plural'], //Module built modules, View <moduleName>
            ['name' => 'LNK_NEW_###MODULE_SINGULAR###', 'type' => 'singular'],
            ['name' => 'LNK_CREATE', 'type' => 'singular'],
            ['name' => 'LBL_MODULE_NAME', 'type' => 'plural'],
            ['name' => 'LBL_MODULE_NAME_SINGULAR', 'type' => 'singular'],
            ['name' => 'LBL_NEW_FORM_TITLE', 'type' => 'singular'],
            ['name' => 'LBL_NEW_FORM_BTN', 'type' => 'singular'],
            ['name' => 'LNK_###MODULE_SINGULAR###_LIST', 'type' => 'plural'],
            ['name' => 'LNK_###MODULE_SINGULAR###_REPORTS', 'type' => 'singular'],
            ['name' => 'LNK_IMPORT_VCARD', 'type' => 'singular'],
            ['name' => 'LNK_IMPORT_###MODULE_PLURAL###', 'type' => 'plural'],
            ['name' => 'MSG_SHOW_DUPLICATES', 'type' => 'singular'],
            ['name' => 'LBL_SAVE_###MODULE_SINGULAR###', 'type' => 'singular'],
            ['name' => 'LBL_LIST_FORM_TITLE', 'type' => 'singular'], //Popup title
            ['name' => 'LBL_SEARCH_FORM_TITLE', 'type' => 'singular'], //Popup title
            ['name' => 'LNK_###MODULE_SINGULAR###_PROCESS_MANAGEMENT', 'type' => 'singular'], //PA title
            ['name' => 'LNK_###MODULE_SINGULAR###_UNATTENDED_PROCESSES', 'type' => 'plural'], //PA title
            ['name' => 'LBL_###MODULE_PLURAL###_SUBPANEL_TITLE', 'type' => 'plural'],
        ];

        $replacedLabels = [];
        foreach ($labelKeysToReplace as $entry) {
            $formattedLanguageKey = $this->formatModuleLanguageKey($entry['name'], $replacementLabels);

            //If the static of dynamic key exists it should be replaced.
            if (isset($currentModuleStrings[$formattedLanguageKey])) {
                $oldStringValue = $currentModuleStrings[$formattedLanguageKey];
                $this->changedModule = $moduleName;
                $newStringValue = $this->replaceSingleLabel($oldStringValue, $replacementLabels, $entry);
                if ($newStringValue !== $oldStringValue) {
                    $replacedLabels[$formattedLanguageKey] = $newStringValue;
                }
            }
        }

        //Save all entries
        ParserLabel::addLabels($this->selectedLanguage, $replacedLabels, $moduleName);
        $this->renamedModules[$moduleName] = true;
        return $replacedLabels;
    }

    /**
     * Format our dynamic keys containing module strings to a valid key depending on the module.
     *
     * @param string $unformatedKey
     * @param string $replacementStrings
     * @return string
     */
    private function formatModuleLanguageKey($unformatedKey, $replacementStrings)
    {
        $unformatedKey = str_replace('###MODULE_SINGULAR###', strtoupper($replacementStrings['key_singular']), $unformatedKey);
        return str_replace('###MODULE_PLURAL###', strtoupper($replacementStrings['key_plural']), $unformatedKey);
    }

    /**
     * Returns true if a given label in the default mod_strings for a module contains
     * a given substring.
     *
     * @param string $key
     * @param string $substring
     *
     * @return boolean
     */
    protected function checkDefaultsForSubstring($key, $substring)
    {
        $mod_strings = [];
        // Check for the label in the static defaults file in case we need it later
        if ($this->changedModule &&
            file_exists('modules/' . $this->changedModule . '/language/' . $this->selectedLanguage . '.lang.php')) {
            include FileLoader::validateFilePath('modules/' . $this->changedModule . '/language/' . $this->selectedLanguage . '.lang.php');
            return (!empty($mod_strings[$key]) && strpos($mod_strings[$key], $substring) !== false);
        }
        return false;
    }

    /**
     * Replace a label with a new value based on metadata which specifies the label as either singular or plural.
     *
     * @param string $oldStringValue
     * @param string $replacementLabels
     * @param array $replacementMetaData
     * @return string
     */
    protected function replaceSingleLabel($oldStringValue, $replacementLabels, $replacementMetaData, $modifier = '')
    {
        $replaceKey = 'prev_' . $replacementMetaData['type'];
        $search = $replacementLabels[$replaceKey];
        $replace = $replacementLabels[$replacementMetaData['type']];

        if (!empty($modifier)) {
            $search = call_user_func($modifier, $search);
            $replace = call_user_func($modifier, $replace);
        }

        // Search string may be empty from the start or become empty after modification
        if ($search === null || strlen($search) === 0) {
            // In this case leave the label unchanged
            return $oldStringValue;
        }

        // After filtering and modification, the replacement string may appear empty
        if (!strlen($replace)) {
            // In this case leave the label unchanged
            return $oldStringValue;
        }

        if ($search === $replace) {
            return $oldStringValue;
        }

        // Get the mod_strings key from metadata
        $modKey = $this->formatModuleLanguageKey($replacementMetaData['name'], $replacementLabels);

        // If the replacement string is a substring of the original,
        // then it's impossible to say if the old value is already updated.
        // If oldStringValue is already updated, don't re-update it.
        // Also handle the corner case where we actually DO want a repeat in the string.
        if (strpos($search, (string) $replace) !== false
            || strpos($oldStringValue, (string) $replace) === false
            || $this->checkDefaultsForSubstring($modKey, $replace)
        ) {
            // Handle resetting routes in strings, since some modules like Forecasting
            // include links in their strings.
            $oldStringValue = str_replace("#{$search}/", '____TEMP_ROUTER_HOLDER____', $oldStringValue);

            $regex = '/(?<=\W|^)(' . preg_quote($search, '/') . ')(?=\W|$)/u';
            $result = preg_replace($regex, $replace, $oldStringValue);

            // Add the route back in if it was found
            $result = str_replace('____TEMP_ROUTER_HOLDER____', "#{$search}/", $result);

            return $result;
        }
        return $oldStringValue;
    }


    /**
     * Save changes to the module names to the app string entries for both the moduleList and moduleListSingular entries.
     */
    private function changeAppStringEntries()
    {
        $GLOBALS['log']->debug('Begining to save app string entries');

        //Save changes to the moduleListSingular app string entry
        $newParams = [
            'use_push' => true,
            'dropdown_lang' => $this->selectedLanguage ?? null,
        ];

        $singularNames = array_map(function ($data) {
            return $data['singular'];
        }, $this->getAllModulesFromRequest());
        $pluralNames = array_map(function ($data) {
            return $data['plural'];
        }, $this->getAllModulesFromRequest());
        $abbreviations = array_map(function ($data) {
            return $data['abbreviation'];
        }, $this->getAllModulesFromRequest());
        $this->updateModuleList('moduleListSingular', $singularNames, $newParams['dropdown_lang'], true);
        $this->updateModuleList('moduleList', $pluralNames, $newParams['dropdown_lang'], true);
        $this->updateModuleList('moduleIconList', $abbreviations, $newParams['dropdown_lang'], true);

        //Save changes to the "*type_display*" app_list_strings entry.
        global $app_list_strings;

        $typeDisplayList = getTypeDisplayList();

        foreach (array_keys($this->changedModules) as $moduleName) {
            //Save changes to the "*type_display*" app_list_strings entry.
            foreach ($typeDisplayList as $typeDisplay) {
                if (isset($app_list_strings[$typeDisplay]) && isset($app_list_strings[$typeDisplay][$moduleName])) {
                    $newParams['dropdown_name'] = $typeDisplay;
                    DropDownHelper::saveDropDown($this->createModuleListPackage($newParams, [
                        $moduleName => $this->changedModules[$moduleName]['singular'],
                    ]), true);
                }
            }
        }
        (new RepairAndClear())->repairAndClearAll(
            ['rebuildExtensions'],
            array_keys($app_list_strings['moduleList']),
            false,
            false,
            '',
            ['vardefs']
        );
    }

    /**
     * Update list of modules with the given labels
     *
     * @param string $name List name
     * @param array $labels Module labels
     * @param string $language Language ley
     * @param bool $postponeQRR Skips QRR inside DropDownHelper::saveDropDown and underlying calls
     */
    public function updateModuleList($name, array $labels, $language, $postponeQRR = false)
    {
        $params = [
            'dropdown_name' => $name,
            'dropdown_lang' => $language,
            'use_push' => true,
        ];
        $params = $this->createModuleListPackage($params, $labels);
        DropDownHelper::saveDropDown($params, $postponeQRR);
    }

    /**
     * Create an array entry that can be passed to the DropDownHelper:saveDropDown function so we can re-utilize
     * the save logic.
     *
     * @param array $params
     * @param array $data
     * @return array
     */
    private function createModuleListPackage(array $params, array $data)
    {
        $count = 0;
        foreach ($data as $key => $value) {
            $params['slot_' . $count] = $count;
            $params['key_' . $count] = $key;
            $params['value_' . $count] = $value;
            $params['delete_' . $count] = '';
            $count++;
        }

        return $params;
    }

    /**
     * Clear all related language cache files.
     *
     * @return void
     */
    private function clearLanguageCaches()
    {
        //remove the js language files
        LanguageManager::removeJSLanguageFiles();

        //remove lanugage cache files
        LanguageManager::clearLanguageCache();
    }

    /**
     * Gets all modules from the request. This is used to build the singular
     * module list for changes so that the entire list is set properly into the
     * global array after save. This is also used to get all changed modules.
     *
     * @return array
     */
    protected function getAllModulesFromRequest()
    {
        $args = $this->modules;
        // We really only want to get this once
        if (!empty($this->requestModules)) {
            return $this->requestModules;
        }

        $results = [];

        $current_app_list_string = return_app_list_strings_language($this->selectedLanguage);

        foreach ($args as $module) {
            if (!is_array($module)) {
                continue;
            }
            $module_key = $module['module_key'];
            if (!AccessControlManager::instance()->allowModuleAccess($module_key)) {
                continue;
            }
            $svalue = $module['module_singular'];
            $pvalue = $module['module_plural'];
            $abbreviation = $module['module_abbreviation'];
            $prevPlural = $current_app_list_string['moduleList'][$module_key];
            $prevSingular = $current_app_list_string['moduleListSingular'][$module_key] ?? $prevPlural;
            $prevAbbreviation = $current_app_list_string['moduleIconList'][$module_key] ?? '';
            $changed = strcmp($svalue, $prevSingular) !== 0 ||
                strcmp($pvalue, $prevPlural) !== 0 ||
                strcmp($abbreviation, $prevAbbreviation) !== 0;

            $results[$module_key] = [
                'singular' => $svalue,
                'plural' => $pvalue,
                'abbreviation' => $abbreviation,
                'prev_singular' => $prevSingular,
                'prev_plural' => $prevPlural,
                'prev_abbreviation' => $prevAbbreviation,
                'key_plural' => $module_key,
                'key_singular' => $this->getModuleSingularKey($module_key),
                'changed' => $changed,
            ];
        }

        $this->requestModules = $results;
        return $results;
    }

    /**
     * Determine which modules have been updated and return an array with the module name as the key
     * and the singular/plural entries as the value.
     *
     * @return array
     */
    private function getChangedModules()
    {
        $request = $this->getAllModulesFromRequest();
        $return = [];
        foreach ($request as $module => $params) {
            if ($params['changed']) {
                unset($params['changed']);
                $return[$module] = $params;
                array_push($this->rebuild_modules, $module);
            }
        }
        return $return;
    }

    /**
     * Return the 'singular' name of a module (Eg. Opportunity for Opportunities) given a moduleName which is a key
     * in the app string moduleList array.  If no entry is found, simply return the moduleName as this is consistant with modules
     * built by moduleBuilder.
     *
     * @param string $moduleName
     * @return string The 'singular' name of a module.
     */
    public function getModuleSingularKey($moduleName)
    {
        $tmp = BeanFactory::newBean($moduleName);
        if (empty($tmp)) {
            $GLOBALS['log']->error("Unable to get module singular key for class: $moduleName");
            return $moduleName;
        }

        if (property_exists($tmp, 'object_name')) {
            return $tmp->object_name;
        } else {
            return $moduleName;
        }
    }
}
