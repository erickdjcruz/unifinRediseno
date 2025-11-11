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
// DataSet Attribute is used to store attribute information for a particular data format.
class DataSet_Attribute extends SugarBean
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

    public $parent_id;             //dataset_layout id
    public $bg_color;          //bg color
    public $cell_size;                 //width in column		//height in row
    public $size_type;             //Size Type
    public $style;                 //bold, italic
    public $wrap;              //wrap or no wrap text
    public $font_color;
    public $font_size = 0;
    public $format_type;       //Text, Currency, Datastamp
    public $attribute_type;    //Head or Body
    public $display_name;      //Header only
    public $display_type = 'Normal';   //Header only	Default, Name, Scalar

    //for the name of the parent if an interlocked data set
    public $parent_name;
    //for the name of the child if an interlocked data set
    public $child_name;

    //for related fields
    public $query_name;
    public $report_name;

    public $table_name = 'dataset_attributes';
    public $module_dir = 'DataSets';
    public $object_name = 'DataSet_Attribute';
    public $rel_layout_table = 'dataset_layouts';
    public $rel_datasets_table = 'data_sets';
    public $disable_custom_fields = true;

    public $new_schema = true;

    public $column_fields = ['id'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'
        , 'parent_id'
        , 'bg_color'
        , 'cell_size'
        , 'size_type'
        , 'style'
        , 'wrap'
        , 'font_color'
        , 'font_size'
        , 'format_type'
        , 'format'
        , 'attribute_type'
        , 'display_name'
        , 'display_type',
    ];


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    // This is the list of fields that are in the lists.
    public $list_fields = [];
    // This is the list of fields that are required
    public $required_fields = [];

    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
    }

    public function get_summary_text()
    {
        return "$this->display_name";
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
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $app_strings, $mod_strings;
        global $app_list_strings;

        global $current_user;

        if (empty($this->exportable)) {
            $this->exportable = '0';
        }

        $temp_array = parent::get_list_view_data();
        $temp_array['NAME'] = (($this->name == '') ? '<em>blank</em>' : $this->name);
        $temp_array['OUTPUT_DEFAULT'] = $app_list_strings['dataset_output_default_dom'][$this->output_default];
        $temp_array['LIST_ORDER_Y'] = $this->list_order_y;
        $temp_array['EXPORTABLE'] = $this->exportable;
        $temp_array['HEADER'] = $this->header;
        $temp_array['QUERY_NAME'] = $this->query_name;
        $temp_array['REPORT_NAME'] = $this->report_name;

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

        //end function get_list_view_data
    }


//end class datasets
}
