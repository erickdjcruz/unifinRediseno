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

use Sugarcrm\Sugarcrm\MetaData\ViewdefManager;

/**
 *  Lead is used to store profile information for people who may become customers.
 */
class Lead extends Person
{
    // Stored fields
    public $id;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $team_id;
    public $description;
    public $salutation;
    public $first_name;
    public $last_name;
    public $title;
    public $department;
    public $reports_to_id;
    public $do_not_call;
    public $phone_home;
    public $phone_mobile;
    public $phone_work;
    public $phone_other;
    public $phone_fax;
    public $refered_by;
    public $email1;
    public $email2;
    public $primary_address_street;
    public $primary_address_city;
    public $primary_address_state;
    public $primary_address_postalcode;
    public $primary_address_country;
    public $alt_address_street;
    public $alt_address_city;
    public $alt_address_state;
    public $alt_address_postalcode;
    public $alt_address_country;
    public $name;
    public $full_name;
    public $portal_name;
    public $portal_app;
    public $contact_id;
    public $contact_name;
    public $account_id;
    public $opportunity_id;
    public $opportunity_name;
    public $opportunity_amount;
    //used for vcard export only
    public $birthdate;
    public $status;
    public $status_description;

    public $lead_source;
    public $lead_source_description;
    // These are for related fields
    public $account_name;
    public $acc_name_from_accounts;
    public $account_site;
    public $account_description;
    public $case_role;
    public $case_rel_id;
    public $case_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $assigned_user_name;
    public $campaign_id;
    public $campaign_name;
    public $alt_address_street_2;
    public $alt_address_street_3;
    public $primary_address_street_2;
    public $primary_address_street_3;

    public $team_name;

    public $business_center_name;
    public $business_center_id;

    //Marketo
    public $mkto_sync;
    public $mkto_id;
    public $mkto_lead_score;

    public $table_name = 'leads';
    public $object_name = 'Lead';
    public $object_names = 'Leads';
    public $module_dir = 'Leads';
    public $new_schema = true;
    public $emailAddress;

    public $importable = true;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id'];
    public $relationship_fields = [
        'business_center_id' => 'business_centers',
        'email_id' => 'emails',
        'call_id' => 'calls',
        'meeting_id' => 'meetings',
        'task_id' => 'tasks',
    ];

    public function create_list_query($order_by, $where, $show_deleted = 0)
    {
        $custom_join = $this->getCustomJoin();
        $query = 'SELECT ';


        $query .= "$this->table_name.*, users.user_name assigned_user_name";
        $query .= ', teams.name team_name';
        $query .= $custom_join['select'];
        $query .= ' FROM leads ';

        // We need to confirm that the user is a member of the team of the item.
        $this->add_team_security_where_clause($query);
        $query .= '			LEFT JOIN users
                                ON leads.assigned_user_id=users.id ';
        $query .= "LEFT JOIN email_addr_bean_rel eabl  ON eabl.bean_id = leads.id AND eabl.bean_module = 'Leads' and eabl.primary_address = 1 and eabl.deleted=0 ";
        $query .= 'LEFT JOIN email_addresses ea ON (ea.id = eabl.email_address_id) ';
        $query .= getTeamSetNameJoin('leads');
        $query .= $custom_join['join'];
        $where_auto = '1=1';
        if ($show_deleted == 0) {
            $where_auto = ' leads.deleted=0 ';
        } elseif ($show_deleted == 1) {
            $where_auto = ' leads.deleted=1 ';
        }

        if ($where != '') {
            $query .= "where ($where) AND " . $where_auto;
        } else {
            $query .= 'where ' . $where_auto; //."and (leads.converted='0')";
        }

        if (!empty($order_by)) {
            $query .= " ORDER BY $order_by";
        }

        return $query;
    }

    public function create_new_list_query(
        $order_by,
        $where,
        $filter = [],
        $params = [],
        $show_deleted = 0,
        $join_type = '',
        $return_array = false,
        $parentbean = null,
        $singleSelect = false,
        $ifListForExport = false
    ) {


        $ret_array = parent::create_new_list_query(
            $order_by,
            $where,
            $filter,
            $params,
            $show_deleted,
            $join_type,
            true,
            $parentbean,
            $singleSelect,
            $ifListForExport
        );
        if (strpos($ret_array['select'], 'leads.account_name') == false && strpos($ret_array['select'], 'leads.*') == false) {
            $ret_array['select'] .= ' ,leads.account_name';
        }
        if (!$return_array) {
            return $ret_array['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
        }
        return $ret_array;
    }

    public function converted_lead($leadid, $contactid, $accountid, $opportunityid)
    {
        $query = "UPDATE leads set converted='1', contact_id=$contactid, account_id=$accountid, opportunity_id=$opportunityid where  id=$leadid and deleted=0";
        $this->db->query($query, true, 'Error converting lead: ');

        //we must move the status out here in order to be able to capture workflow conditions
        $leadid = str_replace("'", '', $leadid);
        $lead = BeanFactory::getBean('Leads', $leadid);
        $lead->status = 'Converted';
        $lead->save();
    }

    public function get_list_view_data($filter_fields = [])
    {
        $temp_array = parent::get_list_view_data();
        if (!empty($temp_array['ACC_NAME_FROM_ACCOUNTS'])) {
            $temp_array['ACC_NAME_FROM_ACCOUNTS'] = $temp_array['ACC_NAME_FROM_ACCOUNTS'];
        } elseif (!empty($temp_array['ACCOUNT_NAME'])) {
            $temp_array['ACC_NAME_FROM_ACCOUNTS'] = $temp_array['ACCOUNT_NAME'];
        } else {
            $temp_array['ACC_NAME_FROM_ACCOUNTS'] = '';
        }
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

        array_push($where_clauses, "leads.last_name like '$the_query_string%'");
        array_push($where_clauses, "leads.account_name like '$the_query_string%'");
        array_push($where_clauses, "leads.first_name like '$the_query_string%'");
        array_push($where_clauses, "ea.email_address like '$the_query_string%'");

        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "leads.phone_home like '%$the_query_string%'");
            array_push($where_clauses, "leads.phone_mobile like '%$the_query_string%'");
            array_push($where_clauses, "leads.phone_work like '%$the_query_string%'");
            array_push($where_clauses, "leads.phone_other like '%$the_query_string%'");
            array_push($where_clauses, "leads.phone_fax like '%$the_query_string%'");
        }

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }

    public function set_notification_body($xtpl, $lead)
    {
        global $app_list_strings;
        global $locale;

        $xtpl->assign('LEAD_NAME', $locale->formatName($lead));
        $xtpl->assign('LEAD_SOURCE', (isset($lead->lead_source) && isset($app_list_strings['lead_source_dom'][$lead->lead_source]) ? $app_list_strings['lead_source_dom'][$lead->lead_source] : ''));
        $xtpl->assign('LEAD_STATUS', (isset($lead->status) ? $app_list_strings['lead_status_dom'][$lead->status] : ''));
        $xtpl->assign('LEAD_DESCRIPTION', $lead->description);

        return $xtpl;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function listviewACLHelper()
    {
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->account_name)) {
            if (!empty($this->account_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_name_owner;
            }
        }
        if (ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }
        $is_owner = false;
        if (!empty($this->opportunity_name)) {
            if (!empty($this->opportunity_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->opportunity_name_owner;
            }
        }
        if (ACLController::checkAccess('Opportunities', 'view', $is_owner)) {
            $array_assign['OPPORTUNITY'] = 'a';
        } else {
            $array_assign['OPPORTUNITY'] = 'span';
        }


        $is_owner = false;
        if (!empty($this->contact_name)) {
            if (!empty($this->contact_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->contact_name_owner;
            }
        }
        if (ACLController::checkAccess('Contacts', 'view', $is_owner)) {
            $array_assign['CONTACT'] = 'a';
        } else {
            $array_assign['CONTACT'] = 'span';
        }

        return $array_assign;
    }

    public function save($check_notify = false)
    {
        //Set business_center_id to the same as related account when not provided
        if (empty($this->business_center_id)) {
            $related_account = BeanFactory::retrieveBean('Accounts', $this->account_id);
            if (!empty($related_account) && !empty($related_account->business_center_id)) {
                $this->business_center_id = $related_account->business_center_id;
            }
        }

        // call save first so that $this->id will be set
        $value = parent::save($check_notify);
        return $value;
    }

    public function get_unlinked_email_query($type = [])
    {

        return get_unlinked_email_query($type, $this);
    }

    /**
     * Returns query to find the related calls created pre-5.1
     *
     * @return string SQL statement
     */
    public function get_old_related_calls()
    {
        $return_array = [];
        $return_array['select'] = 'SELECT calls.id ';
        $return_array['from'] = 'FROM calls ';
        $return_array['where'] = " WHERE calls.parent_id = '$this->id'
            AND calls.parent_type = 'Leads' AND calls.id NOT IN ( SELECT call_id FROM calls_leads ) ";
        $return_array['join'] = '';
        $return_array['join_tables'][0] = '';

        return $return_array;
    }

    /**
     * Returns array of lead conversion activity options
     *
     * @return string SQL statement
     */
    public static function getActivitiesOptions()
    {

        if (isset($GLOBALS['app_list_strings']['lead_conv_activity_opt'])) {
            return $GLOBALS['app_list_strings']['lead_conv_activity_opt'];
        } else {
            return [];
        }
    }

    /**
     * Returns query to find the related meetings created pre-5.1
     *
     * @return string SQL statement
     */
    public function get_old_related_meetings()
    {
        $return_array = [];
        $return_array['select'] = 'SELECT meetings.id ';
        $return_array['from'] = 'FROM meetings ';
        $return_array['where'] = " WHERE meetings.parent_id = '$this->id'
            AND meetings.parent_type = 'Leads' AND meetings.id NOT IN ( SELECT meeting_id FROM meetings_leads ) ";
        $return_array['join'] = '';
        $return_array['join_tables'][0] = '';

        return $return_array;
    }

    /**
     * Overriden to filter legacy calls and meetings
     * @see SugarBean::call_vardef_handler()
     */
    public function call_vardef_handler($meta_array_type = null)
    {
        $this->vardef_handler = new LeadsVarDefHandler($this, $meta_array_type);
    }

    /**
     * Returns if the Convert view is using Opportunities AND has RLIs enabled
     *
     * @return bool
     */
    public static function isUsingRLIsInConvert()
    {
        $moduleData = self::getConvertViewModulesData();

        return isset($moduleData['Opportunities']) && $moduleData['Opportunities']['enableRlis'];
    }

    /**
     * Returns an array of modules found in the Lead Convert view
     *
     * @return array
     */
    public static function getConvertViewModulesData()
    {
        $viewdefManager = new ViewdefManager();
        $convertViewDefs = $viewdefManager->loadViewdef('base', 'Leads', 'convert-main', false, true);
        $moduleData = [];

        foreach ($convertViewDefs['modules'] as $module) {
            $mod = [
                'module' => $module['module'],
                'required' => $module['required'],
                'copyData' => $module['copyData'],
            ];

            if ($module['module'] === 'Opportunities') {
                $mod['enableRlis'] = $module['enableRlis'] ?? false;
                $mod['requireRlis'] = $module['requireRlis'] ?? false;
                $mod['copyDataToRlis'] = $module['copyDataToRlis'] ?? false;
            }

            $moduleData[$module['module']] = $mod;
        }

        return $moduleData;
    }
}
