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


class Call extends RecurringCalendarEvent
{
    public $lead_id;
    public $direction;
    public $reminder_time_options;
    public $note_id;

    public $default_call_name_values = ['Assemble catalogs', 'Make travel arrangements', 'Send a letter', 'Send contract', 'Send fax', 'Send a follow-up letter', 'Send literature', 'Send proposal', 'Send quote'];
    public $table_name = 'calls';
    public $rel_users_table = 'calls_users';
    public $rel_contacts_table = 'calls_contacts';
    public $rel_leads_table = 'calls_leads';
    public $module_dir = 'Calls';
    public $object_name = 'Call';
    public $object_id = 'call_id';

    public $aws_contact_id;
    public $call_recording_url;
    public $call_recording;
    public $sentiment_score_agent;
    public $sentiment_score_customer;
    public $sentiment_score_agent_string;
    public $sentiment_score_customer_string;


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name', 'assigned_user_id', 'contact_id', 'user_id', 'contact_name'];
    public $relationship_fields = ['account_id' => 'accounts',
        'opportunity_id' => 'opportunities',
        'contact_id' => 'contacts',
        'case_id' => 'cases',
        'user_id' => 'users',
        'assigned_user_id' => 'users',
        'note_id' => 'notes',
        'lead_id' => 'leads',
    ];

    /** Returns a list of the associated contacts
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_contacts()
    {
        // First, get the list of IDs.
        $query = "SELECT contact_id as id from calls_contacts where call_id='$this->id' AND deleted=0";

        return $this->build_related_list($query, BeanFactory::newBean('Contacts'));
    }

    public function create_list_query($order_by, $where, $show_deleted = 0)
    {
        $custom_join = $this->getCustomJoin();
        $query = 'SELECT ';
        $query .= '
			calls.*,';
        if (preg_match("/calls_users\.user_id/", $where)) {
            $query .= 'calls_users.required,
				calls_users.accept_status,';
        }

        $query .= '
			users.user_name as assigned_user_name';
        $query .= ', teams.name AS team_name';
        $query .= $custom_join['select'];

        // this line will help generate a GMT-metric to compare to a locale's timezone

        if (preg_match('/contacts/', $where)) {
            $query .= ', contacts.first_name, contacts.last_name';
            $query .= ', contacts.assigned_user_id contact_name_owner';
        }
        $query .= ' FROM calls ';

        // We need to confirm that the user is a member of the team of the item.
        $this->add_team_security_where_clause($query);
        if (preg_match('/contacts/', $where)) {
            $query .= 'LEFT JOIN calls_contacts
	                    ON calls.id=calls_contacts.call_id
	                    LEFT JOIN contacts
	                    ON calls_contacts.contact_id=contacts.id ';
        }
        if (preg_match('/calls_users\.user_id/', $where)) {
            $query .= 'LEFT JOIN calls_users
			ON calls.id=calls_users.call_id and calls_users.deleted=0 ';
        }
        $query .= ' LEFT JOIN teams ON calls.team_id=teams.id';
        $query .= '
			LEFT JOIN users
			ON calls.assigned_user_id=users.id ';
        $query .= $custom_join['join'];
        $where_auto = '1=1';
        if ($show_deleted == 0) {
            $where_auto = " $this->table_name.deleted=0  ";
        } elseif ($show_deleted == 1) {
            $where_auto = " $this->table_name.deleted=1 ";
        }

        //$where_auto .= " GROUP BY calls.id";

        if ($where != '') {
            $query .= "where $where AND " . $where_auto;
        } else {
            $query .= 'where ' . $where_auto;
        }

        $order_by = $this->process_order_by($order_by);
        if (empty($order_by)) {
            $order_by = 'calls.name';
        }
        $query .= ' ORDER BY ' . $order_by;

        return $query;
    }

    public function fill_in_additional_detail_fields()
    {
        if ($this->fill_additional_column_fields) {
            parent::fill_in_additional_detail_fields();
        }

        if (!isset($this->duration_minutes)) {
            $this->duration_minutes = $this->minutes_value_default;
        }

        global $timedate;
        //setting default date and time
        if (is_null($this->date_start)) {
            $this->date_start = $timedate->now();
        }

        if (is_null($this->duration_hours)) {
            $this->duration_hours = '0';
        }
        if (is_null($this->duration_minutes)) {
            $this->duration_minutes = '1';
        }

        if ($this->fill_additional_column_fields) {
            $this->fill_in_additional_parent_fields();
        }

        global $app_list_strings;
        if (empty($this->reminder_time)) {
            $this->reminder_time = -1;
        }

        if (empty($this->id)) {
            $reminder_t = $GLOBALS['current_user']->getPreference('reminder_time');
            if (isset($reminder_t)) {
                $this->reminder_time = $reminder_t;
            }
        }
        $this->reminder_checked = $this->reminder_time == -1 ? false : true;

        if (empty($this->email_reminder_time)) {
            $this->email_reminder_time = -1;
        }
        if (empty($this->id)) {
            $reminder_t = $GLOBALS['current_user']->getPreference('email_reminder_time');
            if (isset($reminder_t)) {
                $this->email_reminder_time = $reminder_t;
            }
        }
        $this->email_reminder_checked = $this->email_reminder_time == -1 ? false : true;

        if (isset($_REQUEST['parent_type']) && (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'SubpanelEdits')) {
            $this->parent_type = $_REQUEST['parent_type'];
        } elseif (is_null($this->parent_type)) {
            $this->parent_type = $app_list_strings['record_type_default_key'];
        }
    }

    public function set_notification_body($xtpl, &$call)
    {
        global $sugar_config;
        global $app_list_strings;
        global $current_user;
        global $timedate;

        // rrs: bug 42684 - passing a contact breaks this call
        $notifyUser = ($call->current_notify_user->object_name == 'User') ? $call->current_notify_user : $current_user;

        // Assumes $call dates are in user format
        $calldate = $timedate->fromDb($call->date_start);
        $xOffset = $timedate->asUser($calldate, $notifyUser) . ' ' . $timedate->userTimezoneSuffix($calldate, $notifyUser);

        if (strtolower(get_class($call->current_notify_user)) == 'contact') {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Calls&contact_id=' .
                $call->current_notify_user->id . '&record=' . $call->id
            );
        } elseif (strtolower(get_class($call->current_notify_user)) == 'lead') {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Calls&lead_id=' .
                $call->current_notify_user->id . '&record=' . $call->id
            );
        } else {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Calls&user_id=' .
                $call->current_notify_user->id . '&record=' . $call->id
            );
        }

        $xtpl = parent::set_notification_body($xtpl, $call);

        $singularModuleLabel = strtoupper($app_list_strings['moduleListSingular'][$this->module_name]);
        $xtpl->assign($singularModuleLabel . '_STARTDATE', $xOffset);
        $xtpl->assign($singularModuleLabel . '_STATUS', ((isset($call->status)) ? $app_list_strings['call_status_dom'][$call->status] : ''));

        return $xtpl;
    }

    public function getEventUsers(): array
    {
        // First, get the list of IDs.
        $query = "SELECT calls_users.required, calls_users.accept_status, calls_users.user_id from calls_users where calls_users.call_id='$this->id' AND calls_users.deleted=0";
        $GLOBALS['log']->debug("Finding linked records $this->object_name: " . $query);
        $result = $this->db->query($query, true);
        $list = [];

        while ($row = $this->db->fetchByAssoc($result)) {
            $record = BeanFactory::retrieveBean('Users', $row['user_id']);
            if (empty($record)) {
                continue;
            }
            $record->required = $row['required'];
            $record->accept_status = $row['accept_status'];
            $list[] = $record;
        }
        return $list;
    }

    public function get_invite_calls($user)
    {
        // First, get the list of IDs.
        $query = "SELECT calls_users.required, calls_users.accept_status, calls_users.call_id from calls_users where calls_users.user_id='$user->id' AND ( calls_users.accept_status IS NULL OR  calls_users.accept_status='none') AND calls_users.deleted=0";
        $GLOBALS['log']->debug("Finding linked records $this->object_name: " . $query);

        $result = $this->db->query($query, true);

        $list = [];

        while ($row = $this->db->fetchByAssoc($result)) {
            $record = BeanFactory::retrieveBean($this->module_dir, $row['call_id']);
            if (empty($record)) {
                continue;
            }
            $record->required = $row['required'];
            $record->accept_status = $row['accept_status'];
            $list[] = $record;
        }
        return $list;
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
        $exclude = ['lead_id'];

        parent::save_relationship_changes($is_update, $exclude);
    }
}
