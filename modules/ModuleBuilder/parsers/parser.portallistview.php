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




class ParserPortalListView extends ParserModifyListView
{
    /**
     * @var mixed[]
     */
    public $mod_strings;
    /**
     * @var \History|mixed
     */
    //@codingStandardsIgnoreStart
    public $_history;
    //@codingStandardsIgnoreEnd
    public $listViewDefs = false;
    public $defaults = [];
    public $additional = [];
    public $available = [];
    public $language_module;
    public $columns = ['LBL_DEFAULT' => 'getDefaultFields', 'LBL_AVAILABLE' => 'getAvailableFields'];
    public $customFile; // private
    public $originalFile; // private

    public function init($module_name)
    {
        $viewdefs = [];
        global $app_list_strings;
        $this->module_name = $module_name;
        $this->mod_strings = return_module_language($GLOBALS ['current_language'], $this->module_name);


        // get our bean
        $this->module = BeanFactory::newBean($this->module_name);

        // Set our def file
        $defFile = 'modules/' . $this->module_name . '/metadata/portal.listviewdefs.php';
        $this->originalFile = $defFile;
        include $defFile;

        $this->originalListViewDefs = $viewdefs[$this->module_name]['listview'];
        $this->fixKeys($this->originalListViewDefs);
        $this->customFile = 'custom/' . $defFile;


        if (file_exists($this->customFile)) {
            include $this->customFile;
            $this->listViewDefs = $viewdefs[$this->module_name]['listview'];
            $this->fixKeys($this->listViewDefs);
        } else {
            $this->listViewDefs = &$this->originalListViewDefs;
        }

        $this->_fromNewToOldMetaData();

        $this->language_module = $this->module_name;

        $this->_history = new History($this->customFile);
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _fromNewToOldMetaData()
    {
        foreach ($this->listViewDefs as $key => $value) {
            $value['default'] = 'true';
            $this->listViewDefs[$key] = $value;
        }
    }

    public function addRelateData($fieldname, $listfielddef)
    {
        $modFieldDef = $this->module->field_defs [strtolower($fieldname)];
        if (!empty($modFieldDef['module']) && !empty($modFieldDef['id_name'])) {
            $listfielddef['module'] = $modFieldDef['module'];
            $listfielddef['id'] = strtoupper($modFieldDef['id_name']);
            $listfielddef['link'] = in_array($listfielddef['module'], ['Cases',
                'Bugs',
                'KBContents']);
            $listfielddef['related_fields'] = [strtolower($modFieldDef['id_name'])];
        }
        return $listfielddef;
    }

    public function handleSave()
    {
        $newFile = null;
        if (!file_exists($this->customFile)) {
            //Backup the orginal layout to the history
            $this->_history->append($this->originalFile);
        }

        $requestfields = $this->_loadLayoutFromRequest();
        $fields = [];
        foreach ($requestfields as $key => $value) {
            if ($value['default'] == 'true') {
                unset($value['default']);
                $fields[strtoupper($key)] = $value;
            }
        }
        mkdir_recursive(dirname($this->customFile));
        if (!write_array_to_file("viewdefs['{$this->module_name}']['listview']", $fields, $this->customFile)) {
            $GLOBALS ['log']->fatal("Could not write $newFile");
        }
    }


    /**
     * returns unused fields that are available for using in either default or additional list views
     */
    public function getAvailableFields()
    {
        $this->availableFields = [];
        $lowerFieldList = array_change_key_case($this->listViewDefs);
        foreach ($this->originalListViewDefs as $key => $def) {
            $key = strtolower($key);
            if (!isset($lowerFieldList [$key])) {
                $this->availableFields [$key] = $def;
            }
        }
        $GLOBALS['log']->debug('parser.modifylistview.php->getAvailableFields(): field_defs=' . print_r($this->availableFields, true));
        $modFields = !empty($this->module->field_defs) ? $this->module->field_defs : $this->module->field_defs;
        $invalidTypes = ['iframe', 'encrypt'];
        foreach ($modFields as $key => $def) {
            $fieldName = strtolower($key);
            if (!isset($lowerFieldList [$fieldName])) { // bug 16728 - check this first, so that other conditions (e.g., studio == visible) can't override and add duplicate entries
                //Similar parsing rules as in parser.portallayoutview.php
                if ((empty($def ['source']) || $def ['source'] == 'db' || $def ['source'] == 'custom_fields') &&
                    empty($def['function']) &&
                    strcmp($key, 'deleted') != 0 &&
                    $def['type'] != 'id' && (empty($def ['dbType']) || $def ['dbType'] != 'id') &&
                    (isset($def['type']) && !in_array($def['type'], $invalidTypes))) {
                    $label = $def ['vname'] ?? $def['label'] ?? $def['name'];
                    $this->availableFields [$fieldName] = ['width' => '10', 'label' => $label];
                }
            }
        }
        return $this->availableFields;
    }

    public function getHistory()
    {
        return $this->_history;
    }
}
