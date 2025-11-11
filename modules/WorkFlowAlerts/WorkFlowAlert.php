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


require_once 'include/workflow/workflow_utils.php';


global $process_dictionary;
require_once 'modules/WorkFlowAlerts/MetaArray.php';

// WorkFlowAlert is used to store the workflow alert component information.
class WorkFlowAlert extends SugarBean
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

    public $user_type;
    public $array_type;
    public $relate_type;
    public $address_type = 'to';
    public $where_filter;
    public $field_value;
    public $rel_module1;
    public $rel_module1_type = 'all';
    public $rel_module2;
    public $rel_module2_type = 'all';
    public $rel_email_value;

    public $parent_id;

    public $user_display_type;

    //Used for UI
    public $base_module;

    public $table_name = 'workflow_alerts';
    public $module_dir = 'WorkFlowAlerts';
    public $object_name = 'WorkFlowAlert';

    public $rel_exp_table = 'expressions';

    public $new_schema = true;

    public $column_fields = ['id'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'
        , 'parent_id'
        , 'user_type'
        , 'array_type'
        , 'relate_type'
        , 'address_type'
        , 'where_filter'
        , 'field_value'
        , 'rel_module1'
        , 'rel_module1_type'
        , 'rel_module2'
        , 'rel_module2_type'
        , 'rel_email_value'
        , 'user_display_type',
    ];

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['rel_field_value', 'custom_user'];

    // This is the list of fields that are in the lists.
    public $list_fields = ['id', 'user_type', 'array_type', 'field_value', 'relate_type', 'address_type'];

    public $relationship_fields = [];


    // This is the list of fields that are required
    public $required_fields = ['user_type' => 1];

    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
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
        global $current_module_strings;

        global $current_user;

        include 'modules/WorkFlowAlerts/MetaArray.php';

        $temp_array = parent::get_list_view_data();

        //Grab event
        include_once 'include/ListView/ProcessView.php';

        $workflow_object = $this->get_workflow_object();
        $workflow_object = $workflow_object->get_parent_object();
        $ProcessView = new ProcessView($workflow_object, $this);
        $ProcessView->local_strings = $current_module_strings;
        $prev_display_text = $ProcessView->get_prev_text('AlertsCreateStep1', $this->user_type);
        if ($prev_display_text === false) {
            if (empty($this->hasError)) {
                $this->hasError = true;
                echo '<p class="error"><b>' . translate('LBL_ALERT_ERRORS') . '</b></p>';
            }
            $prev_display_text = '<span class="error">' . translate('LBL_RECIPIENT_ERROR') . '</span>';
        }
        unset($ProcessView);
        $temp_array['STATEMENT'] = '<i>' . $current_module_strings['LBL_LIST_STATEMENT_CONTENT'] . '</i>';
        $temp_array['STATEMENT2'] = '<b>' . $prev_display_text . '</b>';

        if ($this->user_type == 'specific_user' ||
            $this->user_type == 'specific_team' ||
            $this->user_type == 'specific_role' ||
            $this->user_type == 'login_user'
        ) {
            $temp_array['ACTION'] = 'CreateStep1';
        } else {
            $temp_array['ACTION'] = 'CreateStep2';
        }

        $temp_array['FIELD_VALUE'] = $this->field_value;
        return $temp_array;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = addslashes($the_query_string);
        array_push($where_clauses, "name like '$the_query_string%'");

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }

    public function get_field_value_array($base_module, $inclusion_type = false)
    {
        $inclusion_array = null;
        if ($inclusion_type != false) {
            if ($inclusion_type == 'User') {
                $inclusion_array = ['assigned_user_name' => 'assigned_user_name'];
            }
            if ($inclusion_type == 'Char') {
                $inclusion_array = ['char' => 'char'];
                $inclusion_array = ['varchar' => 'varchar'];
            }
            if ($inclusion_type == 'Email') {
                $inclusion_array = ['email' => 'email'];
            }
        } else {
            $inclusion_array = null;
        }

        $field_option_list = get_column_select($base_module, '', '', $inclusion_array);
        //return the field value array with an inclusion array to only have assigned users

        return $field_option_list;
    }

    public function get_rel_module_array($base_module, $include_none = false)
    {

        $inclusion_array = ['link' => 'link'];

        $field_option_list = get_column_select($base_module, '', '', $inclusion_array, $include_none);
        //return the field value array with an inclusion array to only have linking vardef elements

        return $field_option_list;
    }

    public function get_rel_module($base_module, $var_rel_name)
    {


        //get the vardef fields relationship name
        //get the base_module bean
        $module_bean = BeanFactory::newBean($base_module);
        $rel_name = Relationship::retrieve_by_modules($var_rel_name, $this->base_module, $GLOBALS['db']);
        if (!empty($module_bean->field_defs[$rel_name])) {
            $var_rel_name = $rel_name;
        }
        $var_rel_name = strtolower($var_rel_name);
        $rel_attribute_name = $module_bean->field_defs[$var_rel_name]['relationship'];
        //use the vardef to retrive the relationship attribute
        unset($module_bean);

        return get_rel_module_name($base_module, $rel_attribute_name, $this->db);
    }

    public function get_workflow_object()
    {

        $workflow_alertshell = BeanFactory::getBean('WorkFlowAlertShells', $this->parent_id);
        $workflow_object = $workflow_alertshell->get_workflow_object();
        return $workflow_object;

        //end function get_workflow_type
    }


/////Create Steps Functions

//$focus->handleFilterSave("rel1_", "rel1_alert_fil", "rel_module1_type");

    public function handleFilterSave($prefix, $target_vardef_field, $target_rel_type)
    {

        ////////////////REL TYPE FILTER
        $rel_list = &$this->get_linked_beans($target_vardef_field, 'Expression');

        if (!empty($rel_list[0])) {
            $rel_filter_id = $rel_list[0]->id;
        } else {
            $rel_filter_id = '';
        }
        $rel_object = BeanFactory::newBean('Expressions');

        //Checked if there is an advanced filter
        if ($this->$target_rel_type != 'filter') {
            //no advanced filter
            if ($rel_filter_id != '') {
                //remove existing filter;
                $rel_object->mark_deleted($rel_filter_id);
            }

            //end if no adv filter
        } else {
            //Rel1 Filter exists

            $rel_object->parent_id = $this->id;
            $rel_object->handleSave($prefix, $target_vardef_field, $rel_filter_id);

            //end if rel1 filter exists
        }

        //end function handleFilterSave
    }


    public function get_address_type_dom()
    {

        $workflow_alertshell = BeanFactory::getBean('WorkFlowAlertShells', $this->parent_id);

        if ($workflow_alertshell->alert_type == 'Invite') {
            if ($workflow_alertshell->source_type == 'System Default') {
                return 'wflow_address_type_to_only_dom';
            } else {
                return 'wflow_address_type_invite_dom';
            }

            return 'wflow_address_type_invite_dom';
        } else {
            return 'wflow_address_type_dom';
        }

        //end function get_address_type_dom
    }


//end class
}
