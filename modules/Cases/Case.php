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

/**
 * Class aCase
 * aCase is used to store case information.
 */
class aCase extends Issue
{
    // Stored fields
    public $id;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $team_id;
    public $case_number;
    public $resolution;
    public $description;
    public $name;
    public $priority;

    public $follow_up_datetime;

    public $created_by;
    public $created_by_name;
    public $modified_by_name;

    // These are related
    public $bug_id;
    public $account_name;
    public $account_id;
    public $business_center_name;
    public $business_center_id;
    public $contact_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $assigned_user_name;
    public $team_name;

    public $table_name = 'cases';
    public $rel_account_table = 'accounts_cases';
    public $rel_contact_table = 'contacts_cases';
    public $module_dir = 'Cases';
    public $object_name = 'Case';
    public $importable = true;
    /** "%1" is the case_number, for emails
     * leave the %1 in if you customize this
     * YOU MUST LEAVE THE BRACKETS AS WELL*/
    public $emailSubjectMacro = '[CASE:%1]';

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [
        'bug_id',
        'assigned_user_name',
        'assigned_user_id',
        'contact_id',
        'task_id',
        'note_id',
        'meeting_id',
        'call_id',
        'email_id',
    ];

    public $relationship_fields = [
        'account_id' => 'accounts',
        'bug_id' => 'bugs',
        'task_id' => 'tasks',
        'note_id' => 'notes',
        'meeting_id' => 'meetings',
        'call_id' => 'calls',
        'email_id' => 'emails',
        'business_center_id' => 'business_centers',
    ];


    public function __construct()
    {
        parent::__construct();
        global $sugar_config;
        if (empty($sugar_config['require_accounts'])) {
            unset($this->required_fields['account_name']);
        }

        $this->setupCustomFields('Cases');
        foreach ($this->field_defs as $name => $field) {
            $this->field_defs[$name] = $field;
        }
    }

    public $new_schema = true;


    /**
     * To handle SLA fields
     */
    public function handleSLAFields()
    {
        $now = TimeDate::getInstance()->nowDb();

        // first response target time
        if (!empty($this->follow_up_datetime)) {
            $this->first_response_target_datetime = $this->follow_up_datetime;
        }

        // first response actual time
        $this->first_response_actual_datetime = $now;

        // hours to first response
        $hours = $this->getHoursBetween(
            new \SugarDateTime($this->date_entered, new DateTimeZone('UTC')),
            new \SugarDateTime($now, new DateTimeZone('UTC')),
            $this->business_center_id ?: ''
        );
        $this->hours_to_first_response = $hours['calendarHours'];
        $this->business_hrs_to_first_response = $hours['businessHours'];

        if (!empty($this->first_response_target_datetime)) {
            // first response variance from target
            $this->first_response_var_from_target = $this->getFirstResponseVariance(
                $this->first_response_actual_datetime,
                $this->first_response_target_datetime
            );

            // first response SLA met
            $this->first_response_sla_met = $this->first_response_var_from_target <= 0 ? 'Yes' : 'No';
        }

        // first response user
        $this->first_response_user_id = $this->assigned_user_id;
    }

    /**
     * @param string $actual
     * @param string $target
     * @return float|int
     * @throws Exception
     */
    protected function getFirstResponseVariance(string $actual, string $target)
    {
        $actualDateTime = new \SugarDateTime($actual, new DateTimeZone('UTC'));
        $targetDateTime = new \SugarDateTime($target, new DateTimeZone('UTC'));
        $businessCenterId = $this->business_center_id ?? '';
        if ($actualDateTime >= $targetDateTime) {
            $hours = $this->getHoursBetween($targetDateTime, $actualDateTime, $businessCenterId);
        } else {
            $hours = $this->getHoursBetween($actualDateTime, $targetDateTime, $businessCenterId);
            $hours['businessHours'] = $hours['businessHours'] * -1.0;
        }
        return $hours['businessHours'];
    }

    /**
     * Set resolved_datetime to current time when a case is resolved
     * Set business_center_id to the same as related account when not provided
     *
     * @see parent::save()
     */
    public function save($check_notify = false)
    {
        if ($this->isResolvedStatus($this->status)) {
            if (empty($this->resolved_datetime) && $this->isNewlyResolved()) {
                $this->resolved_datetime = TimeDate::getInstance()->nowDb();
            }
        } elseif (isset($this->fetched_row['status'])
            && SugarConfig::getInstance()->get('clear_resolved_date')
            && $this->isResolvedStatus($this->fetched_row['status'])) {
            $this->resolved_datetime = '';
        }
        if (empty($this->business_center_id)) {
            $related_account = BeanFactory::retrieveBean('Accounts', $this->account_id);
            if (!empty($related_account) && !empty($related_account->business_center_id)) {
                $this->business_center_id = $related_account->business_center_id;
            }
        }

        // When new_rel_relname & new_rel_id are added by RelateRecordApi, parent id is set to new_rel_id by default.
        // Once the new_rel_relname is case_contact and primary_contact_name is set (because we allow users to modify),
        // we want to make sure the primary contact id is set for new_rel_id.
        if (!empty($this->new_rel_id) &&
            !empty($this->new_rel_relname) &&
            $this->new_rel_relname === 'case_contact' &&
            $this->new_rel_id !== $this->primary_contact_id) {
            $this->new_rel_id = $this->primary_contact_id;
        }

        // if first_response_sent changing from false to true
        if (empty($this->fetched_row['first_response_sent']) && !empty($this->first_response_sent)) {
            $this->handleSLAFields();
        }
        return parent::save($check_notify);
    }


    public function listviewACLHelper()
    {
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->account_id)) {
            if (!empty($this->account_id_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_id_owner;
            }
        }
        if (!ACLController::moduleSupportsACL('Accounts') ||
            ACLController::checkAccess('Accounts', 'view', $is_owner)
        ) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }

        return $array_assign;
    }

    /**
     * This function is a good location to save changes that have been made to a relationship.
     * This should be overridden in subclasses that have something to save.
     *
     * @param boolean $is_update true if this save is an update.
     * @param array $exclude a way to exclude relationships
     *
     * @see SugarBean::save_relationship_changes()
     */
    public function save_relationship_changes($is_update, $exclude = [])
    {
        parent::save_relationship_changes($is_update);

        if (!empty($this->contact_id)) {
            $this->set_case_contact_relationship($this->contact_id);
        }

        if (!empty($this->primary_contact_id)) {
            $this->set_case_contact_relationship($this->primary_contact_id);
        }
    }

    public function set_case_contact_relationship($contact_id)
    {
        global $app_list_strings;
        $default = $app_list_strings['case_relationship_type_default_key'];
        $this->load_relationship('contacts');
        $this->contacts->add($contact_id, ['contact_role' => $default]);
    }

    /**
     * Returns a list of the associated contacts
     */
    public function get_contacts()
    {
        $this->load_relationship('contacts');
        $query_array = $this->contacts->getQuery(true);

        // update the select clause in the returned query.
        $query_array['select'] = 'SELECT contacts.id, contacts.first_name, contacts.last_name, contacts.title, contacts.email1, contacts.phone_work, contacts_cases.contact_role as case_role, contacts_cases.id as case_rel_id ';

        $query = '';
        foreach ($query_array as $qstring) {
            $query .= ' ' . $qstring;
        }
        $temp = ['id', 'first_name', 'last_name', 'title', 'email1', 'phone_work', 'case_role', 'case_rel_id'];
        return $this->build_related_list2($query, BeanFactory::newBean('Contacts'), $temp);
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $current_language;
        $app_list_strings = return_app_list_strings_language($current_language);

        $temp_array = $this->get_list_view_array();
        $temp_array['NAME'] = (($this->name == '') ? '<em>blank</em>' : $this->name);
        $temp_array['PRIORITY'] = empty($this->priority)
            ? ''
            : (!isset($app_list_strings[$this->field_defs['priority']['options']][$this->priority])
                ? $this->priority
                : $app_list_strings[$this->field_defs['priority']['options']][$this->priority]);
        $temp_array['STATUS'] = empty($this->status)
            ? ''
            : (!isset($app_list_strings[$this->field_defs['status']['options']][$this->status])
                ? $this->status
                : $app_list_strings[$this->field_defs['status']['options']][$this->status]);
        $temp_array['ENCODED_NAME'] = $this->name;
        $temp_array['CASE_NUMBER'] = $this->case_number;
        $temp_array['SET_COMPLETE'] = "<a href='index.php?return_module=Home&return_action=index&action=EditView&module=Cases&record=$this->id&status=Closed'>" . SugarThemeRegistry::current()->getImage('close_inline', 'title=' . translate('LBL_LIST_CLOSE', 'Cases') . " border='0'", null, null, '.gif', translate('LBL_LIST_CLOSE', 'Cases')) . '</a>';
        $temp_array['CASE_NUMBER'] = format_number_display($this->case_number);
        return $temp_array;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $this->db->quote($the_query_string);
        array_push($where_clauses, "cases.name like '$the_query_string%'");
        array_push($where_clauses, "accounts.name like '$the_query_string%'");

        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "cases.case_number like '$the_query_string%'");
        }

        $the_where = '';

        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }

        if ($the_where != '') {
            $the_where = '(' . $the_where . ')';
        }

        return $the_where;
    }

    public function set_notification_body($xtpl, $case)
    {
        global $app_list_strings;

        $xtpl->assign('CASE_SUBJECT', $case->name);
        $xtpl->assign(
            'CASE_PRIORITY',
            (isset($case->priority) ? $app_list_strings['case_priority_dom'][$case->priority] : '')
        );
        $xtpl->assign('CASE_STATUS', (isset($case->status) ? $app_list_strings['case_status_dom'][$case->status] : ''));
        $xtpl->assign('CASE_DESCRIPTION', $case->description);

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

    /**
     * retrieves the Subject line macro for InboundEmail parsing
     * @return string
     */
    public function getEmailSubjectMacro()
    {
        global $sugar_config;
        return (isset($sugar_config['inbound_email_case_subject_macro']) && !empty($sugar_config['inbound_email_case_subject_macro'])) ?
            $sugar_config['inbound_email_case_subject_macro'] : $this->emailSubjectMacro;
    }
}
