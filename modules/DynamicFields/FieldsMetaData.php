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

class FieldsMetaData extends SugarBean
{
    // database table columns
    public $id;
    public $name;
    public $vname;
    public $custom_module;
    public $type;
    public $len;
    public $required;
    public $default_value;
    public $deleted;
    public $ext1;
    public $ext2;
    public $ext3;
    public $audited;
    public $duplicate_merge;
    public $reportable;
    public $autoinc_next;
    public $required_fields = ['name' => 1, 'date_start' => 2, 'time_start' => 3,];

    public $module_name = 'EditCustomFields';
    public $table_name = 'fields_meta_data';
    public $object_name = 'FieldsMetaData';
    public $module_dir = 'DynamicFields';
    public $column_fields = [
        'id',
        'name',
        'vname',
        'custom_module',
        'type',
        'len',
        'required',
        'default_value',
        'deleted',
        'ext1',
        'ext2',
        'ext3',
        'audited',
        'massupdate',
        'duplicate_merge',
        'reportable',
        'autoinc_next',
    ];

    public $list_fields = [
        'id',
        'name',
        'vname',
        'type',
        'len',
        'required',
        'default_value',
        'audited',
        'massupdate',
        'duplicate_merge',
        'reportable',
    ];

    public $new_schema = true;
    public $disable_row_level_security = true;

    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////


    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    /**
     * retrieve by custom module and field name
     * @param string $customModule
     * @param string $name
     * @param array $options
     * @return SugarBean|null
     * @throws SugarQueryException
     */
    public function retrieveByCustomModuleAndName(string $customModule, string $name, array $options = []): ?SugarBean
    {
        if (empty($customModule) || empty($name)) {
            return null;
        }
        $query = new SugarQuery();
        $query->from($this, $options);
        $query->where()->equals('custom_module', $customModule)->equals('name', $name);
        $query->limit(1);
        $result = $this->fetchFromQuery($query);
        if (!empty($result)) {
            return array_shift($result);
        }
        return null;
    }

    public function get_list_view_data($filter_fields = [])
    {
        $data = parent::get_list_view_data();
        $data['VNAME'] = translate($this->vname, $this->custom_module);
        $data['NAMELINK'] = '<input class="checkbox" type="checkbox" name="remove[]" value="' . $this->id . '">&nbsp;&nbsp;<a href="index.php?module=Studio&action=wizard&wizard=EditCustomFieldsWizard&option=EditCustomField&record=' . $this->id . '" >';
        return $data;
    }


    public function get_summary_text()
    {
        return $this->name;
    }
}
