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


// DataSet is used to store data formatting for CSQL queries.
class DataSet extends SugarBean
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

    public $team_id;

    public $name;
    public $description;
    public $query_id;
    public $list_order_y = 0;
    public $output_default;

    //UI settings
    public $table_width = 100;
    public $font_size = 0;
    public $header;
    public $exportable;
    public $prespace_y;
    public $table_width_type;
    public $body_text_color;
    public $header_text_color;
    public $use_prev_header;
    public $header_back_color;
    public $body_back_color;

    //Layout variable
    public $custom_layout;


    //formatting variables - might move this
    public $symbol;


    //sub query information
    public $interlock = false;
    public $sub_id = null;
    public $sub_query = false;


    //for the name of the parent if an interlocked data set
    public $parent_name;
    public $parent_id;

    //for the name of the child if an interlocked data set
    public $child_name;
    public $child_id;

    //for related fields
    public $query_name;
    public $report_name;
    public $report_id;

    public $table_name = 'data_sets';
    public $module_dir = 'DataSets';
    public $object_name = 'DataSet';
    public $rel_custom_queries = 'custom_queries';
    public $rel_dataset_layout = 'dataset_layouts';
    public $report_table = 'report_maker';

    public $new_schema = true;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['query_name', 'report_name'];

    //Controller Array for list_order element
    public $controller_def = [
        'list_x' => 'N'
        , 'list_y' => 'Y'
        , 'parent_var' => 'report_id'
        , 'start_var' => 'list_order_y'
        , 'start_axis' => 'y',
    ];


    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = false;
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
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();
        $this->get_custom_query();
        $this->get_parent_dataset();
        $this->get_report_name();
        $this->get_child_dataset();
    }


    public function get_custom_query()
    {
        $query = sprintf(
            'SELECT cq.name FROM %s cq, %s p1 WHERE cq.id = p1.query_id AND p1.id=%s AND p1.deleted=0 AND cq.deleted=0',
            $this->rel_custom_queries,
            $this->table_name,
            $this->db->quoted($this->id)
        );
        $result = $this->db->query($query, true, ' Error filling in additional custom query detail fields: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $this->query_name = $row['name'];
        } else {
            $this->query_name = '';
        }
    }

    public function get_parent_dataset()
    {
        $query = sprintf(
            'SELECT id, name FROM %s WHERE id = %s AND deleted = 0',
            $this->table_name,
            $this->db->quoted($this->parent_id)
        );
        $result = $this->db->query($query, true, ' Error filling in additional parent detail fields: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $this->parent_name = $row['name'];
            $this->parent_id = $row['id'];
        } else {
            $this->parent_name = '';
            $this->parent_id = '';
        }
    }

    public function get_child_dataset()
    {
        $query = sprintf(
            'SELECT name, id FROM %s WHERE parent_id=%s AND deleted=0 ',
            $this->table_name,
            $this->db->quoted($this->id)
        );
        $result = $this->db->query($query, true, ' Error filling in additional child detail fields: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $this->child_name = $row['name'];
            $this->child_id = $row['id'];
        } else {
            $this->child_name = 'None';
            $this->child_id = '';
        }
    }


    public function get_report_name()
    {
        $connection = $this->db->getConnection();
        $tableName = $this->db->getValidDBName($this->table_name, false, 'table');
        $reportTableName = $this->db->getValidDBName($this->report_table, false, 'table');
        $sql = <<<SQL
SELECT {$reportTableName}.id, {$reportTableName}.name FROM {$tableName}
LEFT JOIN {$reportTableName} ON {$reportTableName}.id = ?
WHERE {$tableName}.deleted=0 AND {$reportTableName}.deleted=0
SQL;

        $result = $connection->executeQuery($sql, [$this->report_id]);

        // Get the id and the name.
        $row = $result->fetchAssociative();

        if (false !== $row) {
            $this->report_name = $row['name'];
            $this->report_id = $row['id'];
        } else {
            $this->report_name = '';
            $this->report_id = '';
        }
    }


    public function get_list_view_data($filter_fields = [])
    {
        global $app_strings, $mod_strings;
        global $app_list_strings;

        global $current_user;
        global $focus;

        if (empty($this->exportable)) {
            $this->exportable = '0';
        }

        $temp_array = parent::get_list_view_data();
        $temp_array['NAME'] = (($this->name == '') ? '<em>blank</em>' : $this->name);
        $temp_array['OUTPUT_DEFAULT'] = $app_list_strings['dataset_output_default_dom'][isset($this->output_default) && !empty($this->output_default) ? $this->output_default : 'table'];

        $temp_array['LIST_ORDER_Y'] = $this->list_order_y;
        $temp_array['EXPORTABLE'] = $this->exportable;
        $temp_array['HEADER'] = $this->header;
        $temp_array['QUERY_NAME'] = $this->query_name;
        $temp_array['REPORT_NAME'] = $this->report_name;

        if (SugarACL::checkAccess('DataSets', 'edit')) {
            $temp_array['UP_BUTTON'] = $this->getButton('uparrow_inline', 'LNK_UP', [
                'module' => 'DataSets',
                'action' => 'Save',
                'data_set_id' => $this->id,
                'direction' => 'Up',
            ], $focus);
            $temp_array['DOWN_BUTTON'] = $this->getButton('downarrow_inline', 'LNK_DOWN', [
                'module' => 'DataSets',
                'action' => 'Save',
                'data_set_id' => $this->id,
                'direction' => 'Up',
            ], $focus);
            $temp_array['EDIT_BUTTON'] = $this->getButton('edit_inline', 'LNK_EDIT', [
                'module' => 'DataSets',
                'action' => 'EditView',
                'record' => $this->id,
            ], $focus);
        }

        return $temp_array;
    }

    /**
     * Returns HTML for a record action button
     *
     * @param string $image
     * @param string $label
     * @param array $params
     * @param SugarBean $focus
     *
     * @return string
     */
    protected function getButton($image, $label, $params, SugarBean $focus = null)
    {
        $image = SugarThemeRegistry::current()->getImage(
            $image,
            'align="absmiddle" border="0"',
            null,
            null,
            '.gif',
            translate($label)
        );

        if ($focus) {
            $params = array_merge($params, [
                'return_module' => $focus->module_name,
                'return_action' => 'DetailView',
                'return_id' => $focus->id,
            ]);
        }

        $url = 'index.php?' . http_build_query($params);
        return '<a class="listViewTdToolsS1" href="' . htmlspecialchars($url, ENT_COMPAT) . '">'
            . $image . htmlspecialchars(translate($label), ENT_COMPAT) . '</a>&nbsp;&nbsp;';
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $this->db->quote($the_query_string);
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

/////////////////////////////////The following functions process sub queries for reports

    public function process_interlock($list_array)
    {
        global $mod_strings;

        $sub_data_set = BeanFactory::getBean('DataSets', $this->sub_id);
        $sub_data_set->sub_query = true;

        //OUTPUT THE SUB-DATASET
        $data_set = BeanFactory::getBean('CustomQueries', $sub_data_set->query_id);
        $data_set->sub_query_array = $list_array;
        $SubView = new ReportListView();
        $SubView->initNewXTemplate('modules/CustomQueries/QueryView.html', $mod_strings);
        $SubView->setDisplayHeaderAndFooter(false);
        $SubView->setup($data_set, $sub_data_set, 'main', 'CUSTOMQUERY', true);

        return $SubView->processDataSet();

        //end function process_interlock
    }

    public function check_interlock()
    {

        $query = sprintf(
            'SELECT id FROM %s WHERE deleted=0 AND parent_id=%s',
            $this->table_name,
            $this->db->quoted($this->id)
        );
        $result = $this->db->query($query, true, ' Error checking for interlock: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $this->interlock = true;
            $this->sub_id = $row['id'];
        } else {
            $this->interlock = false;
            $this->sub_id = null;
        }

        //end function check_interlock
    }

/////////////////Custom Layout Functions//////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////


    public function enable_custom_layout()
    {

        //First examine the query
        $query_object = BeanFactory::getBean('CustomQueries', $this->query_id);
        $query_object->get_custom_results(false, true);

        if (!empty($query_object->column_array)) {
            foreach ($query_object->column_array as $key => $value) {
                if (empty($value)) {
                    $column_name[$key] = '&nbsp;';
                }
                $layout_object = new DataSet_Layout();
                $layout_object->construct($this->id, 'Column', $key, 'Normal', $value);
            }
            //if empty column_array
        }

        //end function enable_custom_layout
    }

    public function disable_custom_layout()
    {

        $layout_object = new DataSet_Layout();
        $layout_object->clear_all_layout($this->id);


        //end function disable_custom_layout
    }

    public function get_layout_id_from_parent_value($parent_value)
    {

        $query = sprintf(
            'SELECT id FROM %s WHERE parent_value=%s AND deleted=0 AND parent_id=%s',
            $this->rel_dataset_layout,
            $this->db->quoted($parent_value),
            $this->db->quoted($this->id)
        );
        $result = $this->db->query($query, true, ' get layout id: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if (!empty($row['id']) && $row['id'] != '') {
            return $row['id'];
        } else {
            return false;
        }

        //end function get_layout_id_from_parent_value
    }

    public function get_layout_array($hide_columns = false)
    {

        $layout_object = new DataSet_Layout();
        return $layout_object->get_layout_array($this->id, $hide_columns);

        //end function get_layout_array
    }

    public function format_accounting($amount)
    {

        $formatted_amount = $this->symbol . '' . number_format($amount);
        return $formatted_amount;

        //end function format_accounting
    }

    public function format_date($date_value)
    {
        global $timedate;
        $return_val = $timedate->to_display_date($date_value);

        return $return_val;
    }

    public function format_datetime($date_value)
    {
        global $timedate;
        $return_val = $timedate->to_display_date_time($date_value);

        return $return_val;
    }

    public function setup_money_symbol()
    {
        global $current_language, $current_user, $mod_strings, $app_list_strings;
        $app_strings = return_application_language($current_language);
        //$symbol = $app_strings['LBL_CURRENCY_SYMBOL'];

        $currency = BeanFactory::newBean('Currencies');
        if ($current_user->getPreference('currency')) {
            $currency->retrieve($current_user->getPreference('currency'));
            $symbol = $currency->symbol;
        } else {
            $currency->retrieve('-99');
            $symbol = $currency->symbol;
        }

        $this->symbol = $symbol;

        //end function setup_money_symbol
    }


    public function export_csv()
    {
        global $current_user, $current_language, $mod_strings;
        $mod_strings = return_module_language($current_language, $this->module_dir);

        //outputs CSV content
        $query_object = BeanFactory::getBean('CustomQueries', $this->query_id);
        //check for query running error
        $result_message = $query_object->get_custom_results(true);
        if ($result_message['result'] == 'Valid') {
            //run the export
            $query = html_entity_decode($query_object->custom_query, ENT_QUOTES);
            $result = $this->db->query($query, true, 'Error exporting custom query output to dataset: ' . "<BR>$query");

            $fields_array = $this->db->getFieldsArray($result, true);

            //get a temp header array if the attributes are available
            $layout_array = $this->get_layout_array();
            //reorganize the fields_array if necessary;

            foreach ($fields_array as $key => $default_name) {
                if (!empty($layout_array[$default_name]['display_name'])) {
                    $fields_array[$key] = $layout_array[$default_name]['display_name'];
                }
                //end foreach loop
            }
            $content = "\xEF\xBB\xBF"; // utf-8 BOM
            $header = implode('","', array_values($fields_array));
            $header = '"' . $header;
            $header .= "\"\r\n";
            $content .= $header;

            $column_list = implode(',', array_values($fields_array));

            while ($val = $this->db->fetchByAssoc($result, false)) {
                $new_arr = [];

                foreach (array_values($val) as $value) {
                    array_push($new_arr, preg_replace('/"/', '""', $value));
                }

                $line = implode('","', $new_arr);
                $line = '"' . $line;
                $line .= "\"\r\n";

                $content .= $line;
                //end while statement
            }
            return $content;


            //end if the query is a valid query
        } else {
            return $mod_strings['LBL_INVALID_QUERY'];
        }

        //end function scheduled_export
    }

//end class datasets
}
