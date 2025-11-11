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

use Doctrine\DBAL\Connection;

require_once 'include/workflow/workflow_utils.php';
require_once 'include/workflow/action_utils.php';

/**
 *  WorkFlowSchedule is used to process workflow time cron objects
 */
class WorkFlowSchedule extends SugarBean
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

    public $date_expired;
    public $module;
    public $workflow_id;
    public $bean_id;

    public $parameters;

    public $table_name = 'workflow_schedules';
    public $module_dir = 'WorkFlow';
    public $object_name = 'WorkFlowSchedule';
    public $module_name = 'WorkFlowSchedule';
    public $disable_custom_fields = true;

    public $rel_triggershells_table = 'workflow_triggershells';
    public $rel_triggers_table = 'workflow_triggers';
    public $rel_alertshells_table = 'workflow_alertshells';
    public $rel_alerts_table = 'workflow_alerts';
    public $rel_actionshells_table = 'workflow_actionshells';
    public $rel_actions_table = 'workflow_actions';
    public $rel_workflow_table = 'workflow';


    public $new_schema = true;

    public $column_fields = ['id'
        , 'module'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'
        , 'date_expired'
        , 'workflow_id'
        , 'bean_id'
        , 'parameters',
    ];


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    // This is the list of fields that are in the lists.
    public $list_fields = [];

    public $relationship_fields = [];


    // This is the list of fields that are required
    public $required_fields = ['module' => 1, 'bean_id' => 1, 'workflow_id' => 1];

    public $disable_row_level_security = true;


    public function __construct()
    {
        global $dictionary;
        if (isset($this->module_dir) && isset($this->object_name) && !isset($dictionary[$this->object_name])) {
            require SugarAutoLoader::existingCustomOne('metadata/workflow_schedulesMetaData.php');
        }

        parent::__construct();
    }


    public function get_summary_text()
    {
        return "$this->module";
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
        $temp_array = null;
        global $app_strings, $mod_strings;
        global $app_list_strings;
        global $current_user;
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
        array_push($where_clauses, "module like '$the_query_string%'");

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }


////////////////Time Cron Scheduling Components///////////////////////

    public function check_existing_trigger($bean_id, $workflow_id)
    {

        $query = "	SELECT id
                    FROM $this->table_name
                    WHERE $this->table_name.bean_id = '" . $bean_id . "'
                    AND $this->table_name.workflow_id = '" . $workflow_id . "'
                    AND $this->table_name.deleted=0";
        $result = $this->db->query($query, true, ' Error checking for existing scheduled trigger: ');

        // Get the id and the name.
        $row = $this->db->fetchByAssoc($result);

        if ($row != null) {
            $this->retrieve($row['id']);
            return true;
        } else {
            return false;
        }
        //end function check_existing_trigger
    }


    public function set_time_interval($bean_object, $time_array, $update = false)
    {

        if ($update == false && $time_array['time_int_type'] == 'normal') {
            //take current date and add the time interval
            $this->date_expired = get_expiry_date('datetime', $time_array['time_int']);
            //end if update is false, then create a new time expiry
        }

        if ($update == true || $time_array['time_int_type'] == 'datetime') {
            // Bug # 46938, cannot call get_expiry_date in action_utils directly
            $this->date_expired = $this->get_expiry_date($bean_object, $time_array['time_int'], true, $time_array['target_field']);
            //end if update is true, then just update existing expiry
        }
        //end function set_time_interval
    }

    /**
     * @deprecated
     */
    public function get_expiry_date($bean_object, $time_interval, $is_update = false, $target_field = 'none')
    {
        $target_stamp = null;

        if ($is_update) {
            if ($target_field == 'none') {
                $target_stamp = TimeDate::getInstance()->nowDb();
            } else {
                if (!empty($bean_object->$target_field)) {
                    //Date fields need to be reformated to datetimes to be used with scheduler
                    if ($bean_object->field_defs[$target_field]['type'] == 'date' &&
                        is_string($bean_object->$target_field)) {
                        $date = TimeDate::getInstance()->fromDbDate($bean_object->$target_field);
                        $target_stamp = TimeDate::getInstance()->asDb($date);
                    } else {
                        $target_stamp = $bean_object->$target_field;
                    }
                }
            }
        }

        return get_expiry_date('datetime', $time_interval, false, $is_update, $target_stamp);
    }

    public function process_scheduled()
    {
        $current_stamp = $this->db->now();

        $query = "SELECT *
                    FROM $this->table_name
                    WHERE $this->table_name.date_expired < " . $current_stamp . "
                    AND $this->table_name.deleted = 0
                    ORDER BY $this->table_name.id, $this->table_name.workflow_id";

        $result = $this->db->query(
            $query,
            true,
            ' Error checking scheduled triggers to process: '
        );

        // Collect workflows related to the same bean_id, and process them together
        $removeExpired = [];
        $beans = [];
        while ($row = $this->db->fetchByAssoc($result)) {
            if (!isset($beans[$row['bean_id']])) {
                $beans[$row['bean_id']] = [
                    'id' => $row['bean_id'],
                    'module' => $row['target_module'],
                    'workflows' => [$row['workflow_id']],
                    'parameters' => [
                        $row['workflow_id'] => $row['parameters'],
                    ],
                ];
            } else {
                $beans[$row['bean_id']]['workflows'][] = $row['workflow_id'];
                $beans[$row['bean_id']]['parameters'][$row['workflow_id']] = $row['parameters'];
            }
            $removeExpired[] = $row['id'];
        }

        foreach ($beans as $bean) {
            $_SESSION['workflow_cron'] = 'Yes';
            $_SESSION['workflow_id_cron'] = $bean['workflows'];
            // Set the extension variables in case we need them
            $_SESSION['workflow_parameters'] = $bean['parameters'];

            $tempBean = BeanFactory::getBean($bean['module'], $bean['id']);

            if ($tempBean->fetched_row['deleted'] == '0') {
                $tempBean->update_date_modified = false;
                $tempBean->save();
            }

            unset($_SESSION['workflow_cron']);
            unset($_SESSION['workflow_id_cron']);
            unset($_SESSION['workflow_parameters']);
        }

        $this->remove_expired($removeExpired);
    }

    public function remove_expired($ids)
    {
        $this->db->getConnection()
            ->executeUpdate(
                "DELETE FROM {$this->table_name}
                WHERE {$this->table_name}.id IN (?)",
                [$ids],
                [Connection::PARAM_STR_ARRAY]
            );
    }
}
