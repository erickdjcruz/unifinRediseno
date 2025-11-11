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
/*********************************************************************************
 * Description:
 ********************************************************************************/


include_once 'include/workflow/workflow_utils.php';
include_once 'include/workflow/field_utils.php';


// WorkFlowTrigger is used to trigger information.
class WorkFlowAction extends SugarBean
{
    // Stored fields
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $name;

    //construction
    public $field;
    public $value;
    public $set_type;
    public $adv_type;
    public $parent_id;

    public $ext1;
    public $ext2;
    public $ext3;

    public $table_name = 'workflow_actions';
    public $module_dir = 'WorkFlowActions';
    public $object_name = 'WorkFlowAction';

    public $new_schema = true;

    public $column_fields = ['id'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'
        , 'field'
        , 'value'
        , 'operator'
        , 'set_type'
        , 'adv_type'
        , 'parent_id'
        , 'ext1'
        , 'ext2'
        , 'ext3',
    ];

    public $selector_fields = [
        'set_type'
        , 'ext1'
        , 'ext2'
        , 'ext3'
        , 'value',
    ];


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    // This is the list of fields that are in the lists.
    public $list_fields = ['field', 'value', 'set_type', 'adv_type', 'parent_id'];


    // This is the list of fields that are required
    public $required_fields = [];


    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
    }


    public function get_summary_text()
    {
        return "$this->name";
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
    }


    public function mark_relationships_deleted($id)
    {
    }

    public function fill_in_additional_list_fields()
    {
    }

    public function fill_in_additional_detail_fields()
    {
    }


    public function get_list_view_data($filter_fields = [])
    {

        global $app_strings, $mod_strings;
        global $app_list_strings;

        global $current_user;

        $temp_array = [];
        $temp_array['SET_TYPE'] = $app_list_strings['wflow_set_type_dom'][$this->set_type];
        $temp_array['ADV_TYPE'] = $app_list_strings['wflow_adv_type_dom'][$this->adv_type];
        $temp_array['FIELD'] = $this->field;
        $temp_array['VALUE'] = $this->value;
        $temp_array['ID'] = $this->id;
        return $temp_array;
    }

    public function clear_deleted($id)
    {

        //end function clear_deleted
    }


    public function build_generic_where_clause($the_query_string)
    {
    }



///////////////////FILTER BUILDING////////////eventually move this all to the Filter Component
//////////////////////////////////////////////////////////////////////////////////////////////

    public function build_field_selector($field_num, $base_module, $workflow_type = '', $action_type = '')
    {
        ////Begin - New Code call to workflow_utils
        $temp_module = BeanFactory::newBean($base_module);
        //Build Selector Array
        $selector_array = [
            'value' => $this->value,
            'operator' => '',
            'time' => '',
            'field' => $this->field,
            'target_field' => $this->field,
            'ext1' => $this->ext1,
            'ext2' => $this->ext2,
            'ext3' => $this->ext3,
        ];

        $meta_array = [
            'parent_type' => 'field_' . $field_num,
            'enum_multi' => false,
            'workflow_type' => $workflow_type,
            'action_type' => $action_type,
        ];


        $output_array = get_field_output($temp_module, $selector_array, $meta_array, true);

        return $output_array;


        //end function build-filter
    }


///////////////END FILTER BUILDING////////////////////////////////////////////////////////////


/////////////////Handling Avanced WorkFlow Actions


    /**
     * Populate workflow action with data from request
     *
     * @param string $field Field name
     * @param int $i Field index
     */
    public function populate_from_save($field, $i)
    {
        if (isset($_REQUEST[$field][$i])) {
            $this->$field = $_REQUEST[$field][$i];
        }

        // handling advanced fields
        if (isset($_REQUEST['set_type'][$i]) && $_REQUEST['set_type'][$i] == 'Advanced') {
            $this->value = $_REQUEST['adv_value'][$i];
        } else {
            if (isset($_REQUEST['adv_type'][$i], $_REQUEST['ext1'][$i])
                && $_REQUEST['adv_type'][$i] == 'datetime' && $_REQUEST['ext1'][$i] != '') {
                // compensates for datetime field types
            } else {
                $this->adv_type = '';
                $this->ext1 = '';
                $this->ext2 = '';
                $this->ext3 = '';
            }
        }
    }

///End Class WorkFlowAction
}
