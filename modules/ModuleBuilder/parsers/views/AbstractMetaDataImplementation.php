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

require_once 'modules/ModuleBuilder/parsers/constants.php';

/*
 * Abstract base clase for Parser Implementations (using a Bridge Pattern)
 * The Implementations hide the differences between :
 * - Deployed modules (such as OOB modules and deployed ModuleBuilder modules) that are located in the /modules directory and have metadata in modules/<name>/metadata and in the custom directory
 * - WIP modules which are being worked on in ModuleBuilder and that are located in custom
 */

use Sugarcrm\Sugarcrm\Util\Files\FileLoader;

abstract class AbstractMetaDataImplementation
{
    /**
     * Base (standard) view definitions
     *
     * @var array
     */
    protected $baseViewdefs = [];
    // @codingStandardsIgnoreStart PSR2.Classes.PropertyDeclaration.Underscore
    public $_history;
    /**
     * @var array<string, mixed>|mixed[]|mixed
     */
    public $_variables;
    /**
     * Flag for deployed state of this implementation, used by the parsers for
     * determining cache clearing and other stuff
     *
     * @var bool
     */
    protected $_deployed = false;
    protected $_view;
    protected $_viewClient = null; // base, portal, mobile
    protected $_viewType; // list, edit, search, detail
    protected $_moduleName;
    protected $_viewdefs; // Raw $viewdefs from the metadata file
    protected $_originalViewdefs = [];
    protected $_fielddefs;
    protected $_comboFieldDefs = [];
    protected $_paneldefs;
    protected $_paneldefsPath = [];
    protected $_useWorkingFile = false;
    protected $_sourceFilename = ''; // the name of the file from which we loaded the definition we're working on - needed when we come to write out the historical record
    // would like this to be a constant, but alas, constants cannot contain arrays...
    protected $_fileVariables = [
        MB_POPUPSEARCH => 'popupMeta',
        MB_POPUPLIST => 'popupMeta',
        MB_LISTVIEW => 'listViewDefs',
        MB_SIDECARQUOTEDATAGROUPLIST => 'viewdefs',
        MB_QUOTEDATAGRANDTOTALHEADER => 'viewdefs',
        MB_QUOTEDATAGRANDTOTALFOOTER => 'viewdefs',
        MB_SIDECARLISTVIEW => 'viewdefs',
        MB_SIDECARPOPUPVIEW => 'viewdefs',
        MB_SIDECARDUPECHECKVIEW => 'viewdefs',
        MB_BASICSEARCH => 'searchdefs',
        MB_ADVANCEDSEARCH => 'searchdefs',
        MB_EDITVIEW => 'viewdefs',
        MB_DETAILVIEW => 'viewdefs',
        MB_QUICKCREATE => 'viewdefs',
        MB_RECORDVIEW => 'viewdefs',
        MB_RECORDDASHLETVIEW => 'viewdefs',
        MB_PREVIEWVIEW => 'viewdefs',
        MB_WIRELESSEDITVIEW => 'viewdefs',
        MB_WIRELESSDETAILVIEW => 'viewdefs',
        MB_WIRELESSLISTVIEW => 'viewdefs',
        MB_WIRELESSBASICSEARCH => 'searchdefs',
        MB_WIRELESSADVANCEDSEARCH => 'searchdefs',
        MB_PORTALEDITVIEW => 'viewdefs',
        MB_PORTALDETAILVIEW => 'viewdefs',
        MB_PORTALRECORDVIEW => 'viewdefs',
        MB_PORTALLISTVIEW => 'viewdefs',
        MB_PORTALSEARCHVIEW => 'searchdefs',
    ];
    // @codingStandardsIgnoreEnd PSR2.Classes.PropertyDeclaration.Underscore

    /**
     * Returns an array of modules affected by this object. In almost all cases
     * this will be a single array. For subpanels, it will be more than one.
     *
     * @return array List of modules changed within this object
     */
    public function getAffectedModules()
    {
        return [$this->_moduleName];
    }

    /**
     * Gets the flag for the deployed state of this implementation
     *
     * @return bool
     */
    public function isDeployed()
    {
        return $this->_deployed;
    }

    /**
     * Checks if metadata file with the given parameters exists
     *
     * @param string $view The view type
     * @param string $moduleName The name of the module that will use this metadata
     * @param string $location The location of the file
     * @param array $params Additional metadata parameters
     *
     * @return bool
     */
    public function fileExists($view, $moduleName, $location, array $params)
    {
        $filename = $this->getFileNameByParameters($view, $moduleName, $location, null, $params);
        return file_exists($filename);
    }

    /**
     * Returns metadata file name for the given additional metadata parameters
     *
     * @param string $view The view type
     * @param string $module The name of the module that will use this metadata
     * @param string $location The location of the file
     * @param string $client The client type
     * @param array $params Additional metadata parameters
     * @return string
     */
    protected function getFileNameByParameters($view, $module, $location, $client, array $params = [])
    {
        $params = array_merge($params, [
            'client' => $this->_viewClient,
            'location' => $location,
        ]);

        if ($client) {
            $params['client'] = $client;
        }

        $file = MetaDataFiles::getFile($view, $module, $params);
        return MetaDataFiles::getFilePath($file);
    }

    /*
     * Setter function for _viewdefs property
     */
    public function setViewdefs($viewdefs)
    {
        $this->_viewdefs = $viewdefs;
    }

    /**
     * Sets given properties to the viewdef panel fields if they are present in the given fieldList
     *
     * @param array $fieldList Array of field names to which the given properties will be set
     * @param array $propertyList Array of 'property => value' to be set for each field in
     */
    public function setFieldProps($fieldList, $propertyList)
    {
        $defs = $this->_viewdefs;
        $view = $this->_viewType;
        $client = $this->_viewClient;

        // We need all of the panels for this implementation
        $panels = $defs[$client]['view'][$view]['panels'] ?? [];

        // Now we need to loop over each panel, and within each panel...
        foreach ($panels as $pKey => $panel) {
            // ... look at each field on the view
            $fields = isset($panel['fields']) && is_array($panel['fields']) ? $panel['fields'] : [];
            foreach ($fields as $fKey => $field) {
                // Used to maintain state of the fielddef, or create it from strings
                $newDef = [];

                // Fieldsets and fields with defs...
                if (is_array($field) && isset($field['name'])) {
                    // if the field is a fieldset
                    if (isset($field['fields']) && $field['type'] === 'fieldset') {
                        // Loop over each field within the fieldset
                        foreach ($field['fields'] as $sfKey => $subField) {
                            // if subField has a name and is present in the field list
                            // we check if the name is set first as some subFields may not have a name property
                            if (isset($subField['name']) && safeInArray($subField['name'], $fieldList)) {
                                // update the properties of the subField
                                $subField = array_merge($subField, $propertyList);

                                // we update the field to cover the corner case where the field and subfield both
                                // are present in the fieldList. This ensures that the subfield updates don't get
                                // overwritten by the newDef
                                $field['fields'][$sfKey] = $subField;

                                // if the field is not in the fieldList then update the panel with changes made
                                // to subfield since they won't be picked up via newDef later
                                if (!safeInArray($field['name'], $fieldList)) {
                                    $panels[$pKey]['fields'][$fKey]['fields'][$sfKey] = $subField;
                                }
                            }
                        }
                    }

                    // We will just use the field def as is since it is an array
                    // Moreover, we store the field even if it is a fieldset since we might need to update properties at
                    // the fieldset level as well
                    $newDef = $field;
                } elseif (is_string($field)) {
                    // Create an array def from the string name of the field
                    $newDef = ['name' => $field];
                } else {
                    // There are sometimes fields that aren't actual fields
                    continue;
                }

                // The logic above ensures we will always have this
                if (safeInArray($newDef['name'], $fieldList)) {
                    // Put all of it together now
                    $panels[$pKey]['fields'][$fKey] = array_merge($newDef, $propertyList);
                }
            }
        }

        $defs[$client]['view'][$view]['panels'] = $panels;
        $this->setViewdefs($defs);
    }

    /*
     * Getters for the definitions loaded by the Constructor
     */
    public function getViewdefs()
    {
        $GLOBALS['log']->debug(get_class($this) . '->getViewdefs:' . print_r($this->_viewdefs, true));

        return $this->_viewdefs;
    }

    public function getOriginalViewdefs()
    {
        return $this->_originalViewdefs;
    }

    public function getBaseViewdefs()
    {
        return $this->baseViewdefs;
    }

    public function getFielddefs()
    {
        return $this->_fielddefs;
    }

    public function getPanelDefs()
    {
        return $this->_paneldefs;
    }

    public function getPanelDefsPath()
    {
        return $this->_paneldefsPath;
    }

    /**
     * getter function for combo field definitions such as address_street
     *
     * @return array
     */
    public function getComboFieldDefs()
    {
        return $this->_comboFieldDefs;
    }

    /**
     * Flag fetcher that tells if we are using the working file
     *
     * @return bool
     */
    public function useWorkingFile()
    {
        return $this->_useWorkingFile;
    }

    /**
     * Sets the client
     *
     * @param string $client
     */
    public function setViewClient($client)
    {
        $this->_viewClient = $client;
    }

    /**
     * Gets the view client
     *
     * @return string
     */
    public function getViewClient()
    {
        return $this->_viewClient;
    }

    /**
     * Sets the view client for the metadata
     *
     * @return void
     */
    public function setViewClientFromView()
    {
        if (!empty($this->_view)) {
            $this->_viewClient = MetaDataFiles::getViewClient($this->_view);
        }
    }

    /**
     * Sets the panel defs from the view defs
     *
     * @return void
     */
    public function setPanelDefsFromViewDefs()
    {
        $client = empty($this->_viewClient) ? 'base' : $this->_viewClient;
        if (isset($this->_viewdefs[$client]) && is_array($this->_viewdefs[$client]) && isset($this->_viewdefs[$client]['view']) && is_array($this->_viewdefs[$client]['view'])) {
            $this->_paneldefsPath = [$client, 'view'];
            $viewType = key($this->_viewdefs[$client]['view']);
            if (isset($this->_viewdefs[$client]['view'][$viewType]['panels'])) {
                $this->_paneldefsPath[] = $viewType;
                $this->_paneldefs = $this->_viewdefs[$client]['view'][$viewType]['panels'];

                // Use $view type to set our type - NOT SURE THIS IS THE BEST WAY TO DO THIS
                $this->setViewType($viewType);
            }
        }
    }

    public function setViewType($type)
    {
        if (empty($this->_viewType)) {
            $this->_viewType = $type;
        }
    }

    public function getViewType()
    {
        return $this->_viewType;
    }

    /*
     * Obtain a new accessor for the history of this layout
     * Ideally the History object would be a singleton; however given the use case (modulebuilder/studio) it's unlikely to be an issue
     */
    public function getHistory()
    {
        return $this->_history;
    }

    /*
     * Load a layout from a file, given a filename
     * Doesn't do any preprocessing on the viewdefs - just returns them as found for other classes to make sense of
     * @param string filename       The full path to the file containing the layout
     * @return array The layout, null if the file does not exist
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _loadFromFile($filename)
    {
        // BEGIN ASSERTIONS
        if (!file_exists($filename)) {
            return null;
        }
        // END ASSERTIONS
        $GLOBALS['log']->debug(get_class($this) . '->_loadFromFile(): reading from ' . $filename);
        try {
            // loads the viewdef - must be a require not require_once to ensure can reload if called twice in succession
            require FileLoader::validateFilePath($filename);
            LoggerManager::getLogger()->debug(get_class($this) . "->_loadFromFile(): loading from valid filename $filename");

            // Check to see if we have the module name set as a variable rather than embedded in the $viewdef array
            // If we do, then we have to preserve the module variable when we write the file back out
            // This is a format used by ModuleBuilder templated modules to speed the renaming of modules
            // OOB Sugar modules don't use this format

            $moduleVariables = ['module_name', '_module_name', 'OBJECT_NAME', '_object_name'];

            $variables = [];
            foreach ($moduleVariables as $name) {
                if (isset(${$name})) {
                    $variables[$name] = ${$name};
                }
            }

            // Extract the layout definition from the loaded file - the layout definition is held under a variable name that varies between the various layout types (e.g., listviews hold it in listViewDefs, editviews in viewdefs)
            $viewVariable = $this->_fileVariables[$this->_view];
            $defs = ${$viewVariable};

            // Now tidy up the module name in the viewdef array
            // MB created definitions store the defs under packagename_modulename and later methods that expect to find them under modulename will fail

            $modulePath = true;
            if (isset($variables['module_name'])) {
                $mbName = $variables['module_name'];
                $modulePath = isset($defs[$mbName]);
                // Some defs define a variable but don't path from it (subpanels)
                if ($mbName != $this->_moduleName && $modulePath) {
                    $defs[$this->_moduleName] = $defs[$mbName];
                    unset($defs[$mbName]);
                }
            }
            $this->_variables = $variables;
            // now remove the modulename preamble from the loaded defs but only if
            // the defs are pathed from a module
            reset($defs);
            if ($modulePath) {
                $value = current($defs);
            } else {
                $value = $defs;
            }

            $GLOBALS['log']->debug(get_class($this) . '->_loadFromFile: returning ' . print_r($value, true));
        } catch (\TypeError $e) {
            LoggerManager::getLogger()->warn(
                sprintf('%s->_loadFromFile(): file: %s. Error: $%s', get_class($this), $filename, $e->getMessage())
            );
            return null;
        } catch (\Throwable $e) {
            LoggerManager::getLogger()->warn(get_class($this) . '->_loadFromFile(): require ' . $filename . 'failed!');
            return null;
        }
        return $value;
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _loadFromPopupFile($filename, $mod, $view, $forSave = false)
    {
        // BEGIN ASSERTIONS
        if (!file_exists($filename)) {
            return null;
        }
        // END ASSERTIONS
        $GLOBALS['log']->debug(get_class($this) . '->_loadFromFile(): reading from ' . $filename);

        if (!empty($mod)) {
            $oldModStrings = $GLOBALS['mod_strings'];
            $GLOBALS['mod_strings'] = $mod;
        }

        $defs = null;
        try {
            // loads the viewdef - must be a require not require_once to ensure can reload if called twice in succession
            require FileLoader::validateFilePath($filename);
            $viewVariable = $this->_fileVariables[$this->_view];
            $defs = ${$viewVariable};
            if (!$forSave) {
                //Now we will unset the reserve field in pop definition file.
                $limitFields = PopupMetaDataParser::$reserveProperties;
                foreach ($limitFields as $v) {
                    if (isset($defs[$v])) {
                        unset($defs[$v]);
                    }
                }
                if (isset($defs[PopupMetaDataParser::$defsMap[$view]])) {
                    $defs = $defs[PopupMetaDataParser::$defsMap[$view]];
                } else {
                    //If there are no defs for this view, grab them from the non-popup view
                    if ($view == MB_POPUPLIST) {
                        $this->_view = MB_LISTVIEW;
                        $defs = $this->_loadFromFile($this->getFileName(MB_LISTVIEW, $this->_moduleName, MB_CUSTOMMETADATALOCATION));
                        if ($defs == null) {
                            $defs = $this->_loadFromFile($this->getFileName(MB_LISTVIEW, $this->_moduleName, MB_BASEMETADATALOCATION));
                        }
                        $this->_view = $view;
                    } elseif ($view == MB_POPUPSEARCH) {
                        $this->_view = MB_ADVANCEDSEARCH;
                        $defs = $this->_loadFromFile($this->getFileName(MB_ADVANCEDSEARCH, $this->_moduleName, MB_CUSTOMMETADATALOCATION));
                        if ($defs == null) {
                            $defs = $this->_loadFromFile($this->getFileName(MB_ADVANCEDSEARCH, $this->_moduleName, MB_BASEMETADATALOCATION));
                        }
                        if (isset($defs['layout']) && isset($defs['layout']['advanced_search'])) {
                            $defs = $defs['layout']['advanced_search'];
                        }
                        $this->_view = $view;
                    }
                    if ($defs === null) {
                        $defs = [];
                    }
                }
            }

            $this->_variables = [];
            if (!empty($oldModStrings)) {
                $GLOBALS['mod_strings'] = $oldModStrings;
            }
        } catch (\Throwable $e) {
            LoggerManager::getLogger()->warn(get_class($this) . '->_loadFromFile(): require ' . $filename . 'failed!');
            return null;
        }
        return $defs;
    }

    /*
     * Save a layout to a file
     * Must be the exact inverse of _loadFromFile
     * Obtains the additional variables, such as module_name, to include in beginning of the file (as required by ModuleBuilder) from the internal variable _variables, set in the Constructor
     * @param string filename       The full path to the file to contain the layout
     * @param array defs        	Array containing the layout definition; the top level should be the definition itself; not the modulename or viewdef= preambles found in the file definitions
     * @param boolean useVariables	Write out with placeholder entries for module name and object name - used by ModuleBuilder modules
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _saveToFile($filename, $defs, $useVariables = true, $forPopup = false)
    {
        if (file_exists($filename)) {
            $filename = FileLoader::validateFilePath($filename);
            unlink($filename);
        }

        mkdir_recursive(dirname($filename));

        $useVariables = (safeCount($this->_variables) > 0) && $useVariables; // only makes sense to do the variable replace if we have variables to replace...

        // create the new metadata file contents, and write it out
        $out = "<?php\n";
        if ($useVariables) {
            // write out the $<variable>=<modulename> lines
            foreach ($this->_variables as $key => $value) {
                $out .= "\$$key = '" . $value . "';\n";
            }
        }

        $viewVariable = $this->_fileVariables[$this->_view];
        if ($forPopup) {
            $out .= "\$$viewVariable = \n" . var_export_helper($defs);
        } else {
            $moduleIndex = $useVariables ? '$module_name' : "'$this->_moduleName'";
            $out .= '$' . $viewVariable . "[$moduleIndex] = \n" . var_export_helper($defs);
        }

        $out .= ";\n"; // Leaving off the closing PHP tag

        if (sugar_file_put_contents($filename, $out) === false) {
            $GLOBALS['log']->fatal(get_class($this) . ': could not write new viewdef file ' . $filename);
        }
    }

    /**
     * Fielddefs are obtained from two locations:
     *
     * 1. The starting point is the module's fielddefs, sourced from the Bean
     * 2. Second comes any overrides from the layouts themselves. Note though that only visible fields are included in a layoutdef, which
     *    means fields that aren't present in the current layout may have a layout defined in a lower-priority layoutdef, for example, the base layoutdef
     *
     * Thus to determine the current fielddef for any given field, we take the fielddef defined in the module's Bean and then override with first the base layout,
     * then the customlayout, then finally the working layout...
     *
     * The complication is that although generating these merged fielddefs is naturally a method of the implementation, not the parser,
     * we therefore lack knowledge as to which type of layout we are merging - EditView or ListView. So we can't use internal knowledge of the
     * layout to locate the field definitions. Instead, we need to look for sections of the layout that match the template for a field definition...
     *
     * Something to note, the arrangement of the defs inside of $layout, for new
     * metadata implementations, will be in an array path like:
     * $viewdefs[$module][$viewClient]['view'][$viewType]
     *
     * $viewClient will be portal|mobile|base
     * $viewType will be list|edit|detail|search
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _mergeFielddefs(&$fielddefs, $layout)
    {
        // nothing to merge
        if (empty($layout)) {
            return;
        }
        $viewClient = empty($this->_viewClient) ? 'base' : $this->_viewClient;
        if (isset($layout[$viewClient]) && is_array($layout[$viewClient]) && isset($layout[$viewClient]['view']) && is_array($layout[$viewClient]['view'])) {
            $viewType = key($layout[$viewClient]['view']);
            $layout = $layout[$viewClient]['view'][$viewType];
            $this->setViewType($viewType);
        }

        if (isset($layout['panels'])) {
            // Do things the new way
            if (isset($layout['panels']['fields'])) {
                // If by some strange chance fields are immediately descendant from panels
                $this->_mergeFielddefs($fielddefs, $layout['panels']['fields']);
            } else {
                if (is_array($layout['panels'])) {
                    foreach ($layout['panels'] as $panel) {
                        if (isset($panel['fields'])) {
                            $this->_mergeFielddefs($fielddefs, $panel['fields']);
                        } else {
                            // Bug 57571 - tab index value not shown after save
                            $this->_mergeFielddefs($fielddefs, $panel);
                        }
                    }
                }
            }
        } else {
            foreach ($layout as $key => $def) {
                if (is_string($key) && $key == 'templateMeta') {
                    continue;
                }

                if (is_array($def)) {
                    if (isset($def['name']) && !is_array($def['name'])) {
                        // found a 'name' definition, that is not the definition of a field called name :)
                        // if this is a module field, then merge in the definition, otherwise this is a new field defined in the layout, so just take the definition
                        $fielddefs[$def['name']] = (isset($fielddefs[$def['name']])) ? array_merge($fielddefs[$def['name']], $def) : $def;
                    } elseif ((isset($def['label']) || isset($def['vname']) || isset($def['widget_class'])) && !is_numeric($key)) {
                        // dealing with a listlayout which lacks 'name' keys, but which does have 'label' keys
                        $key = strtolower($key);
                        $fielddefs[$key] = (isset($fielddefs[$key])) ? array_merge($fielddefs[$key], $def) : $def;
                    } else {
                        $this->_mergeFielddefs($fielddefs, $def);
                    }
                }
            }
        }
    }
}
