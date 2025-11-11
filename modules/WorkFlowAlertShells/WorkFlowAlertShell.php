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


// WorkFlowAlertShell is used to store the workflow alert shell information.
class WorkFlowAlertShell extends SugarBean
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
    public $alert_text;
    public $alert_type = 'Email';
    public $source_type;
    public $custom_template_id;
    public $parent_id;
    public $parent_base_module;
    public $parent_type;

    public $table_name = 'workflow_alertshells';
    public $module_dir = 'WorkFlowAlertShells';
    public $object_name = 'WorkFlowAlertShell';

    public $rel_workflow_table = 'workflow';
    public $rel_alerts_table = 'workflow_alerts';

    public $new_schema = true;

    public $column_fields = ['id'
        , 'name'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'
        , 'alert_text'
        , 'alert_type'
        , 'source_type'
        , 'custom_template_id'
        , 'parent_id'
        , 'parent_base_module'
        , 'parent_type',
    ];


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    // This is the list of fields that are in the lists.
    public $list_fields = ['id', 'name', 'alert_type', 'custom_template_id', 'alert_text'];

    public $relationship_fields = [];


    // This is the list of fields that are required
    public $required_fields = ['name' => 1, 'alert_type' => 1];


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
        global $current_module_strings;
        global $current_module_strings2;


        global $current_user;
        global $current_language;

        $temp_array = [];
        $translated_type = $app_list_strings['wflow_source_type_dom'][$this->source_type];
        $prepared_name = (($this->name == '') ? '<i>an alert</i>' : '<b><i>' . $this->name . '</b></i>');

        //begin - rsmith
        $focus_alertcomp_list = $this->get_linked_beans('alert_components', 'WorkFlowAlert');
        include_once 'include/ListView/ProcessView.php';
        $workflow_alert_module = return_module_language($current_language, 'WorkFlowAlerts');
        $table_html = "<table id='tbl_$this->id' style='display:none'>";
        foreach ($focus_alertcomp_list as $comp) {
            $ProcessView = new ProcessView($this->get_workflow_object(), $comp);
            $ProcessView->local_strings = $workflow_alert_module;
            $alert_prev_text = $ProcessView->get_prev_text('AlertsCreateStep1', $comp->user_type);
            if ($alert_prev_text === false) {
                if (empty($this->hasError)) {
                    $this->hasError = true;
                    echo '<p class="error"><b>' . translate('LBL_ALERT_ERRORS') . '</b></p>';
                }
                $alert_prev_text = '<span class="error">' . translate('LBL_ALERT_ERROR') . '</span>';
            }
            $table_html .= '<tr><td>';
            $table_html .= "<li>$alert_prev_text</li>";
            $table_html .= '</td></tr>';
        }
        $table_html .= '</table>';
        //end - rsmith

        //this is an alert item

        if ($this->source_type == 'Normal Message') {
            $statement = $current_module_strings['STATEMENT_PART1'] . ' ' . $prepared_name . ' ' . $current_module_strings['STATEMENT_PART2'] . ' ' . $translated_type;

            $temp_array['STATEMENT'] = $statement;

            //end if normal message
        } else {
            //custom template message

            if (!empty($this->custom_template_id)) {
                $template_object = BeanFactory::getBean('EmailTemplates', $this->custom_template_id);
            }
            if (!empty($template_object)) {
                $custom_template_name = $template_object->name;
            } else {
                $custom_template_name = '';
            }

            $statement = $current_module_strings['STATEMENT_PART1'] . ' ' . $prepared_name . ' ' . $current_module_strings['STATEMENT_PART2'] . ' ' . $translated_type . ': <b><i>' . $custom_template_name . '</b></i>';
            $temp_array['STATEMENT'] = $statement;

            //end else custom template message
        }

        $temp_array['HREF_EDIT'] = 'index.php?action=EditView&module=WorkFlowAlertShells&module_tab=WorkFlow&record=' . $this->id . '&workflow_id=' . $this->parent_id;
        $temp_array['HREF_DELETE'] = 'index.php?action=Delete&module=WorkFlowAlertShells&module_tab=WorkFlow&record=' . $this->id . '';
        $temp_array['TYPE'] = $current_module_strings['LBL_MODULE_NAME'];
        $temp_array['DETAILS_TABLE'] = $table_html;
        $temp_array['ID'] = $this->id;

        //Component information for either recipients or invitees (Meetings & Calls)
        $recipient_icon = SugarThemeRegistry::current()->getImage('Users', 'align="absmiddle" border="0"', null, null, '.gif', $app_strings['LNK_REMOVE']);
        $temp_array['COMPONENT_HREF_EDIT'] = 'index.php?action=DetailView&module=WorkFlowAlertShells&module_tab=WorkFlow&record=' . $this->id . '&workflow_id=' . $this->parent_id;
        $temp_array['COMPONENT_STATEMENT'] = $recipient_icon . $mod_strings['LBL_RECIPIENTS'];

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

    public function get_workflow_object()
    {

        $workflow_object = BeanFactory::getBean('WorkFlow', $this->parent_id);
        return $workflow_object;

        //end function get_workflow_type
    }


    public function retrieve_meta_information()
    {
        $process_dictionary = [];
        require_once 'modules/WorkFlowAlertShells/MetaArray.php';
        $this->target_meta_array = $process_dictionary['AlertShellDetailView']['elements'][$this->alert_type];

        //end function retrieve_meta_information
    }

    public function copy($parent_id)
    {
        $orig_id = $this->id;
        $new_action_shell = $this;
        $new_action_shell->id = '';
        $new_action_shell->parent_id = $parent_id;
        if (isset($new_action_shell->date_entered)) {
            $new_action_shell->date_entered = null;
        }
        if (isset($new_action_shell->created_by)) {
            $new_action_shell->created_by = null;
        }
        $new_action_shell->save();
        $new_id = $new_action_shell->id;
        $this->retrieve($orig_id);
        $alertcomp_list = $this->get_linked_beans('alert_components', 'WorkFlowAlert');

        foreach ($alertcomp_list as $comp) {
            $new_comp = $comp;
            $new_comp->id = '';
            $new_comp->parent_id = $new_id;
            if (isset($new_comp->date_entered)) {
                $new_comp->date_entered = null;
            }
            if (isset($new_comp->created_by)) {
                $new_comp->created_by = null;
            }

            $new_comp->save();
        }
    }


//Add a get_alert_contents function


//end class
}
