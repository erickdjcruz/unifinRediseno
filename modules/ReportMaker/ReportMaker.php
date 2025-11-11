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

// ReportMaker is used to build advanced reports from data formats.
class ReportMaker extends SugarBean
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
    public $description;
    public $title;
    public $team_id;

    //UI parameters
    public $report_align;

    //variables for joining the report schedules table
    public $schedule_id;
    public $next_run;
    public $active;
    public $time_interval;


    //for the name of the parent if an interlocked data set
    public $parent_name;

    //for related fields
    public $query_name;

    public $table_name = 'report_maker';
    public $module_dir = 'ReportMaker';
    public $object_name = 'ReportMaker';
    public $rel_dataset = 'data_sets';
    public $schedules_table = 'report_schedules';

    public $new_schema = true;


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    public function __construct()
    {
        parent::__construct();

        //make sure only people in the same team can see the reports
        $this->disable_row_level_security = false;
    }


    public function get_summary_text()
    {
        return "$this->name";
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
    }


    public function mark_deleted($id)
    {
        $query = "update data_sets set report_id='' where report_id= ? and deleted=0";
        $conn = $this->db->getConnection();
        $conn->executeStatement($query, [$id]);
        parent::mark_deleted($id);
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
        $this->get_scheduled_query();
    }

    public function get_scheduled_query()
    {
        $query = <<<EOT
        SELECT
             $this->schedules_table.id schedule_id,
             $this->schedules_table.active active,
             $this->schedules_table.next_run next_run
        FROM
             $this->schedules_table
        WHERE 
             $this->schedules_table.report_id = ? AND
             $this->schedules_table.deleted = 0
EOT;
        $result = $this->db->getConnection()->executeQuery($query, [$this->id]);

        // Get the id and the name.
        $row = $result->fetchAssociative();
        if (false !== $row) {
            $this->schedule_id = $row['schedule_id'];
            $this->active = $row['active'];
            $this->next_run = $row['next_run'];
        } else {
            $this->schedule_id = '';
            $this->active = '';
            $this->next_run = '';
        }
    }


    public function get_list_view_data($filter_fields = [])
    {
        global $timedate;
        global $app_strings, $mod_strings;
        global $app_list_strings;


        global $current_user;

        if (empty($this->published)) {
            $this->published = '0';
        }

        $temp_array = parent::get_list_view_data();
        $temp_array['NAME'] = (($this->name == '') ? '<em>blank</em>' : $this->name);
        $temp_array['ID'] = $this->id;

        //report scheduling
        if (isset($this->schedule_id) && $this->active == 1) {
            $is_scheduled_img = SugarThemeRegistry::current()->getImage('scheduled_inline.png', 'border="0" align="absmiddle"', null, null, '.gif', $mod_strings['LBL_SCHEDULE_EMAIL']);
            $is_scheduled = $timedate->to_display_date_time($this->next_run);
        } else {
            $is_scheduled_img = SugarThemeRegistry::current()->getImage('unscheduled_inline.png', 'border="0" align="absmiddle"', null, null, '.gif', $mod_strings['LBL_SCHEDULE_EMAIL']);
            $is_scheduled = $mod_strings['LBL_NONE'];
        }

        $temp_array['IS_SCHEDULED'] = $is_scheduled;
        $temp_array['IS_SCHEDULED_IMG'] = $is_scheduled_img;

        return $temp_array;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $GLOBALS['db']->quote($the_query_string);
        array_push($where_clauses, "name like '$the_query_string%'");
        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "mft_part_num like '%$the_query_string%'");
            array_push($where_clauses, "vendor_part_num like '%$the_query_string%'");
        }

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;

        //end function
    }


    public function get_data_sets($orderBy = '')
    {
        // First, get the list of IDs.
        $query = "SELECT $this->rel_dataset.id from $this->rel_dataset
					 where $this->rel_dataset.report_id='$this->id'
					 AND $this->rel_dataset.deleted=0 " . $orderBy;

        return $this->build_related_list($query, BeanFactory::newBean('DataSets'));
        //end get_data_sets
    }

//end class ReportMaker
}
