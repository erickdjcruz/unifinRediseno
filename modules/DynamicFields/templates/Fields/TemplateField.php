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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;

$GLOBALS['studioReadOnlyFields'] = ['date_entered' => 1, 'date_modified' => 1, 'created_by' => 1, 'id' => 1, 'modified_user_id' => 1];

class TemplateField
{
    public $hidemassupdate;
    public $no_default;
    public $comments;
    public $merge_filter;
    public $studio;
    public $full_text_search;
    /**
     * @var mixed
     */
    public $calculated;
    /**
     * @var mixed|string
     */
    public $enforced;
    public $dependency;
    public $required_formula;
    public $readonly_formula;
    /**
     * @var mixed
     */
    public $options;
    public $calculation_visible;
    /**
     * @var mixed|int
     */
    public $duplicate_merge_dom_value;
    public $module;
    public $custom_module;
    /**
     * @var mixed
     */
    public $tablename;
    /*
        The view is the context this field will be used in
        -edit
        -list
        -detail
        -search
        */
    public $view = 'edit';
    public $name = '';
    public $vname = '';
    public $label = '';
    public $id = '';
    public $size = '20';

    /**
     * The DB length of the field
     *
     * @var int|null
     */
    public $len;

    public $required = false;
    public $default = null;
    public $default_value = null;
    public $type = 'varchar';
    public $comment = '';
    public $bean;
    public $ext1 = '';
    public $ext2 = '';
    public $ext3 = '';
    public $ext4 = '';
    public $audited = 0;
    public $massupdate = 0;
    public $importable = 'true';
    public $autoinc_next = '';
    public $pii = false;
    public $readonly = false;
    /** @var bool */
    public $require_option;
    /** @var bool */
    public $mass_update;
    /** @var int */
    public $max_size;
    /** @var string */
    public $id_name;
    /** @var string */
    public $dbType;
    /** @var array */
    public $related_fields;
    /** @var bool */
    public $isnull;
    /** @var string */
    public $workflow;
    /** @var string */
    public $function;
    /** @var string */
    public $function_bean;
    /** @var bool */
    public $deleted;
    /** @var string */
    public $date_modified;
    /** @var string */
    public $labelValue;

    /**
     * "duplicate_merge" attribute is considered enabled, if not specified
     * "merge_filter" attribute is considered disabled, if not specified
     *
     * @var int
     *
     * @see get_dup_merge_def()
     * @see modules/MergeRecords/Step1.php
     * @see show_field()
     * @see MergeDuplicatesView#_isDuplicateMergeEnabled
     */
    public $duplicate_merge = 1;

    public $new_field_definition;
    public $reportable = true;
    public $label_value = '';
    public $help = '';
    public $formula = '';
    public $unified_search = 0;
    public $supports_unified_search = false;
    // Bug 58560 - Allow fields to be "grouped", like address fields
    // THIS NEEDS TO BE NULL UNLESS IT IS TO BE USED SO IT DOESN'T SAVE AS AN EMPTY
    // VALUE IN DynamicField::saveExtendedAttributes() WHICH USES isset() RATHER THAN empty()
    public $group = null;
    public $group_label = null;
    public $vardef_map = [
        'name' => 'name',
        'label' => 'vname',
        // bug 15801 - need to ALWAYS keep default and default_value consistent as some methods/classes use one, some use another...
        'default_value' => 'default',
        'default' => 'default_value',
        'display_default' => 'default_value',
        'len' => 'len',
        'required' => 'required',
        'type' => 'type',
        'audited' => 'audited',
        'massupdate' => 'massupdate',
        'hidemassupdate' => 'hidemassupdate',
        'options' => 'ext1',
        'help' => 'help',
        'comments' => 'comment',
        'importable' => 'importable',
        'duplicate_merge' => 'duplicate_merge',
        'duplicate_merge_dom_value' => 'duplicate_merge_dom_value', //bug #14897
        'merge_filter' => 'merge_filter',
        'reportable' => 'reportable',
        'ext2' => 'ext2',
        'ext4' => 'ext4',
        'ext3' => 'ext3',
        'labelValue' => 'label_value',
        'unified_search' => 'unified_search',
        'full_text_search' => 'full_text_search',
        // Bug 58560 - Add a group index here so it gets written to the custom vardefs
        // for cases such as address fields
        'group' => 'group',
        'group_label' => 'group_label',
        'calculated' => 'calculated',
        'formula' => 'formula',
        'enforced' => 'enforced',
        'dependency' => 'dependency',
        'related_fields' => 'related_fields',
        'pii' => 'pii',
        'required_formula' => 'required_formula',
        'readonly' => 'readonly',
        'readonly_formula' => 'readonly_formula',
        'calculation_visible' => 'calculation_visible',
        'convertToBase' => 'convertToBase',
        'showTransactionalAmount' => 'showTransactionalAmount',
    ];

    /**
     * Optional custom validation for fields being used by
     * `TemplateField::populateFromPost`. By default no explicit constraint is
     * used which implies scalar values for every post field. Every template
     * can attach their own validators for fields which require more specific
     * validation or where the default validation does not apply.
     *
     * @var array
     */
    protected $vardefMapValidation = [
        'full_text_search' => [
            'Assert\All' => [
                'constraints' => ['Assert\Type' => ['type' => 'string']],
            ],
        ],
    ];

    // Bug #48826
    // fields to decode from post request
    public $decode_from_request_fields_map = ['formula', 'dependency', 'required_formula', 'readonly_formula'];

    /*
        HTML FUNCTIONS
        */
    public function get_html()
    {
        $view = $this->view;
        if (!empty($GLOBALS['studioReadOnlyFields'][$this->name])) {
            $view = 'detail';
        }
        switch ($view) {
            case 'search':
                return $this->get_html_search();
            case 'edit':
                return $this->get_html_edit();
            case 'list':
                return $this->get_html_list();
            case 'detail':
                return $this->get_html_detail();
        }
    }

    public function set($values)
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    public function get_html_edit()
    {
        return 'not implemented';
    }

    public function get_html_list()
    {
        return $this->get_html_detail();
    }

    public function get_html_detail()
    {
        return 'not implemented';
    }

    public function get_html_search()
    {
        return $this->get_html_edit();
    }

    public function get_html_label()
    {

        $label = '{MOD.' . $this->vname . '}';
        if (!empty($GLOBALS['app_strings'][$this->vname])) {
            $label = '{APP.' . $this->label . '}';
        }
        if ($this->view == 'edit' && $this->is_required()) {
            $label .= '<span class="required">*</span>';
        }
        if ($this->view == 'list') {
            if (isset($this->bean)) {
                if (!empty($this->id)) {
                    $name = $this->bean->table_name . '_cstm.' . $this->name;
                    $arrow = $this->bean->table_name . '_cstm_' . $this->name;
                } else {
                    $name = $this->bean->table_name . '.' . $this->name;
                    $arrow = $this->bean->table_name . '_' . $this->name;
                }
            } else {
                $name = $this->name;
                $arrow = $name;
            }
            $label = "<a href='{ORDER_BY}$name' class='listViewThLinkS1'>{MOD.$this->label}{arrow_start}{" . $arrow . '_arrow}{arrow_end}</a>';
        }
        return $label;
    }

    /*
        XTPL FUNCTIONS
        */

    public function get_xtpl($bean = false)
    {
        if ($bean) {
            $this->bean = $bean;
        }
        $view = $this->view;
        if (!empty($GLOBALS['studioReadOnlyFields'][$this->name])) {
            $view = 'detail';
        }
        switch ($view) {
            case 'search':
                return $this->get_xtpl_search();
            case 'edit':
                return $this->get_xtpl_edit();
            case 'list':
                return $this->get_xtpl_list();
            case 'detail':
                return $this->get_xtpl_detail();
        }
    }

    public function get_xtpl_edit()
    {
        return '/*not implemented*/';
    }

    public function get_xtpl_list()
    {
        return get_xtpl_detail();
    }

    public function get_xtpl_detail()
    {
        return '/*not implemented*/';
    }

    public function get_xtpl_search()
    {
        //return get_xtpl_edit();
    }

    public function is_required()
    {
        if ($this->required) {
            return true;
        }
        return false;
    }


    /*
        DB FUNCTIONS
        */

    public function get_db_type()
    {
        if (!empty($this->type)) {
            $type = $GLOBALS['db']->getColumnType($this->type);
        }
        if (!empty($type)) {
            return " $type";
        }
        $type = $GLOBALS['db']->getColumnType('varchar');
        return " $type({$this->len})";
    }

    public function get_db_default($modify = false)
    {
        $GLOBALS['log']->debug('get_db_default(): default_value=' . $this->default_value);
        if (!$modify or empty($this->new_field_definition['default_value']) or $this->new_field_definition['default_value'] != $this->default_value) {
            if (!is_null($this->default_value)) { // add a default value if it is not null - we want to set a default even if default_value is '0', which is not null, but which is empty()
                if (null == trim($this->default_value)) {
                    return ' DEFAULT NULL';
                } else {
                    return " DEFAULT '$this->default_value'";
                }
            } else {
                return '';
            }
        }
    }

    /*
     * Return the required clause for this field
     * Confusingly, when modifying an existing field ($modify=true) there are two exactly opposite cases:
     * 1. if called by Studio, only $this->required is set. If set, we return "NOT NULL" otherwise we return "NULL"
     * 2. if not called by Studio, $this->required holds the OLD value of required, and new_field_definition['required'] is the NEW
     * So if not called by Studio we want to return NULL if required=true (because we are changing FROM this setting)
     */

    public function get_db_required($modify = false)
    {
        //		$GLOBALS['log']->debug('get_db_required required='.$this->required." and ".(($modify)?"true":"false")." and ".print_r($this->new_field_definition,true));
        $req = '';

        if ($modify) {
            if (!empty($this->new_field_definition['required'])) {
                if ($this->required and $this->new_field_definition['required'] != $this->required) {
                    $req = ' NULL ';
                }
            } else {
                $req = ($this->required) ? ' NOT NULL ' : ''; // bug 17184 tyoung - set required correctly when modifying custom field in Studio
            }
        } else {
            if (empty($this->new_field_definition['required']) or $this->new_field_definition['required'] != $this->required) {
                if (!empty($this->required) && $this->required) {
                    $req = ' NOT NULL';
                }
            }
        }

        return $req;
    }

    /**
     * Oracle Support: do not set required constraint if no default value is supplied.
     * In this case the default value will be handled by the application/sugarbean.
     */
    public function get_db_add_alter_table($table)
    {
        $db = DBManagerFactory::getInstance();
        return $db->addColumnSQL($table, $this->get_field_def());
    }

    public function get_db_delete_alter_table($table)
    {
        $db = DBManagerFactory::getInstance();
        return $db->dropColumnSQL(
            $table,
            $this->get_field_def()
        );
    }

    /**
     * mysql requires the datatype clause in the alter statement.
     * It will be no-op anyway.
     */
    public function get_db_modify_alter_table($table)
    {
        $db = DBManagerFactory::getInstance();
        return $db->alterColumnSQL($table, $this->get_field_def());
    }

    /*
     * BEAN FUNCTIONS
     *
     */
    public function get_field_def()
    {
        $array = [
            'required' => $this->convertBooleanValue($this->required),
            'readonly' => $this->convertBooleanValue($this->readonly),
            'source' => 'custom_fields',
            'name' => $this->name,
            'vname' => $this->vname,
            'type' => $this->type,
            // This needs to be a boolean value so clients know how to handle it
            'massupdate' => $this->convertBooleanValue($this->massupdate),
            'hidemassupdate' => $this->hidemassupdate ?? false,
            'no_default' => !empty($this->no_default),
            'comments' => $this->comments ?? '',
            'help' => $this->help ?? '',
            'importable' => $this->importable,
            'duplicate_merge' => $this->duplicate_merge,
            'duplicate_merge_dom_value' => $this->getDupMergeDomValue(),
            'audited' => $this->convertBooleanValue($this->audited) || $this->convertBooleanValue($this->pii),
            'reportable' => $this->convertBooleanValue($this->reportable),
            'unified_search' => $this->convertBooleanValue($this->unified_search),
            'merge_filter' => empty($this->merge_filter) ? 'disabled' : $this->merge_filter,
            'pii' => $this->convertBooleanValue($this->pii),
        ];

        if (isset($this->default)) {
            $array['default'] = $this->default;
        }
        if (isset($this->studio)) {
            $array['studio'] = $this->convertBooleanValue($this->studio);
        }
        if (isset($this->full_text_search)) {
            $array['full_text_search'] = $this->full_text_search;
        }
        if (!empty($this->calculated) && !empty($this->formula) && is_string($this->formula)) {
            $array['calculated'] = $this->calculated;
            $array['formula'] = html_entity_decode($this->formula, ENT_COMPAT);
            $array['enforced'] = !empty($this->enforced) && $this->enforced == true;
            if ($array['calculated'] && $array['enforced']) {
                unset($array['default']);
                $array['massupdate'] = false;
                // Need to set it on the object as well, since some child classes
                // use that instead of the return of this method
                unset($this->default);
                $this->massupdate = false;
            }
        } else {
            $array['calculated'] = false;
        }
        if (!empty($this->dependency) && is_string($this->dependency)) {
            $array['dependency'] = html_entity_decode($this->dependency, ENT_COMPAT);
        }
        if (!empty($this->required_formula) && is_string($this->required_formula)) {
            $array['required_formula'] = html_entity_decode($this->required_formula, ENT_COMPAT);
        }
        if (!empty($this->readonly) && !empty($this->readonly_formula) && is_string($this->readonly_formula)) {
            $array['readonly_formula'] = html_entity_decode($this->readonly_formula, ENT_COMPAT);
        }
        if (!empty($this->len)) {
            $array['len'] = $this->len;
        }
        if (!empty($this->size)) {
            $array['size'] = $this->size;
        }
        // Bug 61736 - Address fields in undeployed modules do not have a group property
        if (!empty($this->group)) {
            $array['group'] = $this->group;
        }

        if (!empty($this->group_label)) {
            $array['group_label'] = $this->group_label;
        }

        if (!empty($this->options)) {
            $array['options'] = $this->options;
        }

        if (isset($this->calculation_visible)) {
            $array['calculation_visible'] = $this->convertBooleanValue($this->calculation_visible);
        }

        $this->get_dup_merge_def($array);

        return $array;
    }

    protected function convertBooleanValue($value)
    {
        if ($value === 'true' || $value === '1' || $value === 1) {
            return true;
        } elseif ($value === 'false' || $value === '0' || $value === 0) {
            return false;
        } else {
            return $value;
        }
    }


    /* if the field is duplicate merge enabled this function will return the vardef entry for the same.
     */
    public function get_dup_merge_def(&$def)
    {
        switch ($def['duplicate_merge_dom_value']) {
            case 0:
                $def['duplicate_merge'] = 'disabled';
                $def['merge_filter'] = 'disabled';
                break;
            case 1:
                $def['duplicate_merge'] = 'enabled';
                $def['merge_filter'] = 'disabled';
                break;
            case 2:
                $def['merge_filter'] = 'enabled';
                $def['duplicate_merge'] = 'enabled';
                break;
            case 3:
                $def['merge_filter'] = 'selected';
                $def['duplicate_merge'] = 'enabled';
                break;
            case 4:
                $def['merge_filter'] = 'enabled';
                $def['duplicate_merge'] = 'disabled';
                break;
        }
    }

    /**
     * duplicate_merge_dom_value drives the dropdown in the studio editor. This dropdown drives two fields though,
     * duplicate_merge and merge_filter. When duplicate_merge_dom_value is not set, we need to derive it from the values
     * of those two fields. Also, when studio sends this value down to be read in PopulateFromPost, it is set to
     * duplicate_merge rather than duplicate_merge_dom_value, so we must check if duplicate_merge is a number rather
     * than a string as well.
     * @return int
     */
    public function getDupMergeDomValue()
    {
        if (isset($this->duplicate_merge_dom_value)) {
            return $this->duplicate_merge_dom_value;
        }

        //If duplicate merge is numeric rather than a string, it is probably what duplicate_merge_dom_value was set to.
        if (is_numeric($this->duplicate_merge)) {
            return $this->duplicate_merge;
        }


        //Figure out the duplicate_merge_dom_value based on the values of merge filter and duplicate merge
        if (empty($this->merge_filter) || $this->merge_filter === 'disabled') {
            if (empty($this->duplicate_merge) || $this->duplicate_merge === 'disabled') {
                $this->duplicate_merge_dom_value = 0;
            } else {
                $this->duplicate_merge_dom_value = 1;
            }
        } else {
            if ($this->merge_filter === 'selected') {
                $this->duplicate_merge_dom_value = 3;
            } elseif (empty($this->duplicate_merge) || $this->duplicate_merge === 'disabled') {
                $this->duplicate_merge_dom_value = 4;
            } else {
                $this->duplicate_merge_dom_value = 2;
            }
        }

        return $this->duplicate_merge_dom_value;
    }

    /*
        HELPER FUNCTIONS
        */


    public function prepare()
    {
        if (empty($this->id)) {
            $this->id = $this->name;
        }
    }

    /**
     * This function supports setting the values of all TemplateField instances.
     *
     * @param $row The Array key/value pairs from fields_meta_data table
     */
    public function populateFromRow(array $row)
    {
        //Bug 24189: Copy fields from FMD format to Field objects and vice versa
        $fmd_to_dyn_map = $this->getFieldMetaDataMapping();
        foreach ($fmd_to_dyn_map as $fmd_key => $dyn_key) {
            if (isset($row[$dyn_key])) {
                $this->$fmd_key = $row[$dyn_key];
            }
            if (isset($row[$fmd_key])) {
                $this->$dyn_key = $row[$fmd_key];
            }
        }

        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Populates object from request
     *
     * @param Request $request
     */
    public function populateFromPost(Request $request = null)
    {
        if (!$request) {
            $request = InputValidation::getService();
        }

        foreach ($this->vardef_map as $vardef => $field) {
            $constraints = $this->vardefMapValidation[$field] ?? null;
            $value = $request->getValidInputRequest($vardef, $constraints);
            if ($value !== null) {
                // Bug #48826. Some fields are allowed to have special characters and must be decoded from the request
                // Bug 49774, 49775: Strip html tags from 'formula' and 'dependency'.
                if (is_string($value) && safeInArray($vardef, $this->decode_from_request_fields_map)) {
                    $this->$vardef = strip_tags($value);
                } else {
                    $this->$vardef = $value;
                }

                //Remove potential xss code from help field
                if ($field == 'help' && !empty($this->$vardef)) {
                    $this->$vardef = htmlspecialchars(remove_xss($this->$vardef), ENT_COMPAT, 'UTF-8');
                }

                if ($vardef != $field) {
                    $this->$field = $this->$vardef;
                }
            }
        }
        $this->applyVardefRules();
        $GLOBALS['log']->debug('populate: ' . print_r($this, true));
    }

    /**
     * Applies rules for type specific fields vardefs. This can be overridden in
     * child classes.
     */
    protected function applyVardefRules()
    {
        // Expected behavior of calculated fields without formula are like non-calculated fields
        if (!empty($this->calculated) && empty($this->formula)) {
            unset($this->calculated);
            $this->enforced = '';
        }

        if (!empty($this->calculated) && !empty($this->formula)
            && is_string($this->formula) && !empty($this->enforced) && $this->enforced) {
            $this->importable = 'false';
            $this->duplicate_merge = 0;
            $this->duplicate_merge_dom_value = 0;
        }

        // Handle full_text_search configuration
        if (!empty($this->full_text_search)) {
            // configure correct enabled/searchable flags
            switch ($this->full_text_search['enabled']) {
                case '2':
                    $this->full_text_search['enabled'] = true;
                    $this->full_text_search['searchable'] = true;
                    if ($this instanceof TemplateAutoIncrement) {
                        $this->full_text_search['type'] = 'int';
                    }
                    break;
                default:
                    // only set value for searchable, no set for enabled! see BR-2852
                    $this->full_text_search['searchable'] = false;
            }
        }
    }

    public function get_additional_defs()
    {
        return [];
    }

    public function delete($df)
    {
        $df->deleteField($this);
    }

    /**
     * get_field_name
     *
     * This is a helper function to return a field's proper name.  It checks to see if an instance of the module can
     * be created and then attempts to retrieve the field's name based on the name lookup skey supplied to the method.
     *
     * @param String $module The name of the module
     * @param String $name The field name key
     * @return string The field name for the module
     */
    protected function get_field_name($module, $name)
    {
        $bean = BeanFactory::newBean($module);
        if (empty($bean) || is_null($bean)) {
            return $name;
        }

        $field_defs = $bean->field_defs;
        return $field_defs[$name]['name'] ?? $name;
    }

    /**
     * This function saves the field template by calling the DynamicField addFieldObject function. It then
     * checks to see if updates are needed for the SearchFields.php file.  In the event that the unified_search
     * member variable is set to true, a search field definition is updated/created to the SearchFields.php file.
     *
     * @param DynamicField $df
     */
    public function save($df)
    {
        $df->addFieldObject($this);

        $searchFieldParser = new ParserSearchFields($df->getModuleName(), $df->getPackageName());
        //If unified_search is enabled for this field, then create the SearchFields entry
        $fieldName = $this->get_field_name($df->getModuleName(), $this->name);
        if ($this->unified_search && !isset($searchFieldParser->searchFields[$df->getModuleName()][$fieldName])) {
            $searchFieldParser->addSearchField($fieldName, ['query_type' => 'default']);
            $searchFieldParser->saveSearchFields($searchFieldParser->searchFields);
        }
    }

    /**
     * Gets mapping of fields_meta_data to DynamicField properties. This can be
     * overridden or extended in child classes.
     *
     * @return array
     */
    public function getFieldMetaDataMapping()
    {
        return [
            'comments' => 'comment',
            'require_option' => 'required',
            'label' => 'vname',
            'mass_update' => 'massupdate',
            'max_size' => 'len',
            'default_value' => 'default',
            'id_name' => 'ext3',
        ];
    }

    /**
     * Utility function to return the name of the DB we should using. If the field is custom, then it'll be
     * <module_name>_cstm. If the field is not custom it'll fetch the correct module name from the field data.
     */
    public function getTableName()
    {
        $table = null;
        if (isset($this->module) && isset($this->module->table_name)) {
            $table = $this->module->table_name;
        }

        if (isset($this->custom_module)) {
            $table = strtolower($this->custom_module) . '_cstm';
        }

        if (isset($this->tablename)) {
            $table = $this->tablename;
        }

        return $table;
    }

    /**
     * Field defs of which this field consists: list with single item (field itself) for simple fields, list of
     * components for composite. Used for database change verification.
     * @param DynamicField $dynamicField needed to check field existence in some ancestors
     * @return array
     * @see DynamicFieldVerification
     */
    public function getContainedDefs(DynamicField $dynamicField): array
    {
        return [$this->get_field_def()];
    }
}
