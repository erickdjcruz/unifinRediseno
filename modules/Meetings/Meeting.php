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


class Meeting extends RecurringCalendarEvent
{
    public $location;
    public $type;
    public $time_meridiem;
    public $meeting_id;

    public $sequence;

    public $meetings_arr;
    // when assoc w/ a user/contact:
    public $table_name = 'meetings';
    public $rel_users_table = 'meetings_users';
    public $rel_contacts_table = 'meetings_contacts';
    public $rel_leads_table = 'meetings_leads';
    public $module_dir = 'Meetings';
    public $object_name = 'Meeting';
    public $object_id = 'meeting_id';

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name', 'assigned_user_id', 'contact_id', 'user_id', 'contact_name', 'accept_status'];
    public $relationship_fields = [
        'account_id' => 'accounts',
        'opportunity_id' => 'opportunity',
        'case_id' => 'case',
        'assigned_user_id' => 'users',
        'contact_id' => 'contacts',
        'user_id' => 'users',
    ];

    // so you can run get_users() twice and run query only once
    public $cached_get_users = null;
    public $date_changed = false;

    /**
     * Stub for integration
     * @return bool
     */
    public function hasIntegratedMeeting()
    {
        return false;
    }

    public function saveExternal(): bool
    {
        // Do any external API saving
        // Clear out the old external API stuff if we have changed types
        if (isset($this->fetched_row['type']) && $this->fetched_row['type'] != $this->type) {
            $this->join_url = null;
            $this->host_url = null;
            $this->external_id = null;
            $this->creator = null;
        }

        if (!empty($this->type) && $this->type != 'Sugar') {
            $api = ExternalAPIFactory::loadAPI($this->type);
        }

        if (empty($this->type)) {
            $this->type = 'Sugar';
        }

        if (isset($api) && is_a($api, 'WebMeeting') && empty($this->in_relationship_update)) {
            // Make sure the API initialized and it supports Web Meetings
            // Also make sure we have an ID, the external site needs something to reference
            if (!isset($this->id) || empty($this->id)) {
                $this->id = create_guid();
                $this->new_with_id = true;
            }
            // formatting fix required because our schedule meeting APIs expect data in a specific format
            $this->fixUpFormatting();
            $response = $api->scheduleMeeting($this);
            if ($response['success'] == true) {
                // Need to send out notifications
                if ($api->canInvite) {
                    $notifyList = $this->get_notification_recipients();
                    foreach ($notifyList as $person) {
                        $api->inviteAttendee($this, $person, $check_notify);
                    }
                }
            } else {
                // Generic Message Provides no value to End User - Log the issue with message detail and continue
                // SugarApplication::appendErrorMessage($GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL']);
                $GLOBALS['log']->warn('ERR_EXTERNAL_API_SAVE_FAIL' . ': ' . $this->type . ' - ' . $response['errorMessage']);
            }

            $api->logoff();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function mark_deleted($id)
    {
        parent::mark_deleted($id);

        if ($this->update_vcal) {
            global $current_user;
            vCal::cache_sugar_vcal($current_user);
        }
    }

    public function fill_in_additional_detail_fields()
    {
        if ($this->fill_additional_column_fields) {
            parent::fill_in_additional_detail_fields();
        }

        if (!isset($this->time_hour_start)) {
            $this->time_start_hour = intval(substr((string)$this->time_start, 0, 2));
        } //if-else

        if (isset($this->time_minute_start)) {
            $time_start_minutes = $this->time_minute_start;
        } else {
            $time_start_minutes = substr((string)$this->time_start, 3, 5);
            if ($time_start_minutes > 0 && $time_start_minutes < 15) {
                $time_start_minutes = '15';
            } elseif ($time_start_minutes > 15 && $time_start_minutes < 30) {
                $time_start_minutes = '30';
            } elseif ($time_start_minutes > 30 && $time_start_minutes < 45) {
                $time_start_minutes = '45';
            } elseif ($time_start_minutes > 45) {
                $this->time_start_hour += 1;
                $time_start_minutes = '00';
            } //if-else
        } //if-else


        if (isset($this->time_hour_start)) {
            $time_start_hour = $this->time_hour_start;
        } else {
            $time_start_hour = intval(substr((string)$this->time_start, 0, 2));
        }

        global $timedate;
        $this->time_meridiem = $timedate->AMPMMenu('', $this->time_start, 'onchange="SugarWidgetScheduler.update_time();"');
        $hours_arr = [];
        $num_of_hours = 13;
        $start_at = 1;

        if (empty($time_meridiem)) {
            $num_of_hours = 24;
            $start_at = 0;
        } //if

        for ($i = $start_at; $i < $num_of_hours; $i++) {
            $i = $i . '';
            if (strlen($i) == 1) {
                $i = '0' . $i;
            }
            $hours_arr[$i] = $i;
        } //for

        if (!isset($this->duration_minutes)) {
            $this->duration_minutes = $this->minutes_value_default;
        }

        //setting default date and time
        if (is_null($this->date_start)) {
            $this->date_start = $timedate->now();
        }
        if (is_null($this->time_start)) {
            $this->time_start = $timedate->to_display_time(TimeDate::getInstance()->nowDb(), true);
        }
        if (is_null($this->duration_hours)) {
            $this->duration_hours = '0';
        }
        if (is_null($this->duration_minutes)) {
            $this->duration_minutes = '1';
        }

        if (empty($this->id) && !empty($_REQUEST['date_start'])) {
            $this->date_start = $_REQUEST['date_start'];
        }
        if (!empty($this->date_start)) {
            $td = $timedate->fromDb($this->date_start);
            if (!empty($td)) {
                if (!empty($this->duration_hours) && $this->duration_hours != '') {
                    $td = $td->modify("+{$this->duration_hours} hours");
                }
                if (!empty($this->duration_minutes) && $this->duration_minutes != '') {
                    $td = $td->modify("+{$this->duration_minutes} mins");
                }
                $this->date_end = $timedate->asDb($td);
            } else {
                $GLOBALS['log']->fatal("Meeting::save: Bad date {$this->date_start} for format " . $GLOBALS['timedate']->get_date_time_format());
            }
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

        if (isset($_REQUEST['parent_type']) && empty($this->parent_type)) {
            $this->parent_type = $_REQUEST['parent_type'];
        } elseif (is_null($this->parent_type)) {
            $this->parent_type = $app_list_strings['record_type_default_key'];
        }

        // Fill in the meeting url for external account types
        if (!empty($this->id) && !empty($this->type) && $this->type != 'Sugar' && !empty($this->join_url)) {
            // It's an external meeting
            global $mod_strings;

            $meetingLink = '';
            if ($GLOBALS['current_user']->id == $this->assigned_user_id) {
                $meetingLink .= '<a href="index.php?module=Meetings&action=JoinExternalMeeting&meeting_id=' . $this->id . '&host_meeting=1" target="_blank">' . SugarThemeRegistry::current()->getImage('start_meeting_inline', 'border="0" ', 18, 19, '.png', translate('LBL_HOST_EXT_MEETING', $this->module_dir)) . '</a>';
            }

            $meetingLink .= '<a href="index.php?module=Meetings&action=JoinExternalMeeting&meeting_id=' . $this->id . '" target="_blank">' . SugarThemeRegistry::current()->getImage('join_meeting_inline', 'border="0" ', 18, 19, '.png', translate('LBL_JOIN_EXT_MEETING', $this->module_dir)) . '</a>';

            $this->displayed_url = $meetingLink;
        }
    }

    public function get_list_view_data($filter_fields = [])
    {
        $meeting_fields = parent::get_list_view_data($filter_fields);

        $join_icon = null;
        $oneHourAgo = gmdate($GLOBALS['timedate']->get_db_date_time_format(), time() - 3600);
        if (!empty($this->host_url) && $date_db >= $oneHourAgo) {
            if ($this->assigned_user_id == $GLOBALS['current_user']->id) {
                $join_icon = SugarThemeRegistry::current()->getImage('start_meeting_inline', 'border="0"', null, null, '.gif', translate('LBL_HOST_EXT_MEETING', $this->module_dir));
                $meeting_fields['OBJECT_IMAGE_ICON'] = 'start_meeting_inline';
                $meeting_fields['DISPLAYED_URL'] = 'index.php?module=Meetings&action=JoinExternalMeeting&meeting_id=' . $this->id . '&host_meeting=1';
            } else {
                $join_icon = SugarThemeRegistry::current()->getImage('join_meeting_inline', 'border="0"', null, null, '.gif', translate('LBL_JOIN_EXT_MEETING', $this->module_dir));
                $meeting_fields['OBJECT_IMAGE_ICON'] = 'join_meeting_inline';
                $meeting_fields['DISPLAYED_URL'] = 'index.php?module=Meetings&action=JoinExternalMeeting&meeting_id=' . $this->id . '&host_meeting=0';
            }
        }

        $meeting_fields['JOIN_MEETING'] = '';
        if (!empty($meeting_fields['DISPLAYED_URL'])) {
            $meeting_fields['JOIN_MEETING'] = '<a href="' . $meeting_fields['DISPLAYED_URL'] . '" target="_blank" rel="nofollow noopener noreferrer">' . $join_icon . '</a>';
        }

        return $meeting_fields;
    }

    public function set_notification_body($xtpl, &$meeting)
    {
        global $app_list_strings, $sugar_config, $timedate, $current_user;

        $singularModuleLabel = strtoupper($app_list_strings['moduleListSingular'][$this->module_name]);

        // cn: bug 9494 - passing a contact breaks this call
        $notifyUser = ($meeting->current_notify_user->object_name == 'User') ? $meeting->current_notify_user : $current_user;
        // cn: bug 8078 - fixed call to $timedate

        if (strtolower(get_class($meeting->current_notify_user)) == 'contact') {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Meetings&contact_id=' .
                $meeting->current_notify_user->id . '&record=' . $meeting->id
            );
        } elseif (strtolower(get_class($meeting->current_notify_user)) == 'lead') {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Meetings&lead_id=' .
                $meeting->current_notify_user->id . '&record=' . $meeting->id
            );
        } else {
            $xtpl->assign(
                'ACCEPT_URL',
                $sugar_config['site_url'] . '/index.php?entryPoint=acceptDecline&module=Meetings&user_id=' .
                $meeting->current_notify_user->id . '&record=' . $meeting->id
            );
        }

        $xtpl = parent::set_notification_body($xtpl, $meeting);

        $typestring = null;
        $xtpl->assign($singularModuleLabel . '_STATUS', (isset($meeting->status) ? $app_list_strings['meeting_status_dom'][$meeting->status] : ''));
        $typekey = strtolower($meeting->type);
        if (isset($meeting->type)) {
            if (!empty($app_list_strings['eapm_list'][$meeting->type])) {
                $typestring = $app_list_strings['eapm_list'][$meeting->type];
            } elseif (!empty($app_list_strings['eapm_list'][$typekey])) {
                $typestring = $app_list_strings['eapm_list'][$typekey];
            } else {
                $typestring = $app_list_strings['meeting_type_dom'][$meeting->type];
            }
        }
        $xtpl->assign($singularModuleLabel . '_TYPE', isset($meeting->type) ? $typestring : '');
        $startdate = $timedate->fromDb($meeting->date_start);
        $xtpl->assign(
            $singularModuleLabel . '_STARTDATE',
            $timedate->asUser($startdate, $notifyUser) . ' ' . TimeDate::userTimezoneSuffix($startdate, $notifyUser)
        );
        $enddate = $timedate->fromDb($meeting->date_end);
        $xtpl->assign(
            $singularModuleLabel . '_ENDDATE',
            $timedate->asUser($enddate, $notifyUser) . ' ' . TimeDate::userTimezoneSuffix($enddate, $notifyUser)
        );
        if (!empty($meeting->join_url)) {
            $xtpl->assign($singularModuleLabel . '_URL', $meeting->join_url);
            $xtpl->parse('Meeting.Meeting_External_API');
        }

        return $xtpl;
    }

    /**
     * Redefine method to attach ics file to notification email
     */
    public function create_notification_email($notify_user)
    {
        // reset acceptance status for non organizer if date is changed
        if (($notify_user->id != $GLOBALS['current_user']->id) && $this->date_changed) {
            $this->set_accept_status($notify_user, 'none');
        }

        $mailer = parent::create_notification_email($notify_user);

        $path = 'upload://' . $this->id;

        $content = vCal::get_ical_event($this, $GLOBALS['current_user']);

        if (file_put_contents($path, $content)) {
            $attachment = new Attachment($path, 'meeting.ics', Encoding::Base64, 'text/calendar');
            $mailer->addAttachment($attachment);
        }

        return $mailer;
    }

    /**
     * Redefine method to remove ics after email is sent
     */
    public function send_assignment_notifications($notify_user, $admin)
    {
        parent::send_assignment_notifications($notify_user, $admin);

        $path = 'upload://' . $this->id;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getEventUsers(): array
    {
        // First, get the list of IDs.
        $query = "SELECT meetings_users.required, meetings_users.accept_status, meetings_users.user_id from meetings_users where meetings_users.meeting_id='$this->id' AND meetings_users.deleted=0";
        $GLOBALS['log']->debug("Finding linked records $this->object_name: " . $query);
        $result = $this->db->query($query, true);
        $list = [];

        while ($row = $this->db->fetchByAssoc($result)) {
            $record = BeanFactory::retrieveBean('Users', $row['user_id']);
            if (!empty($record)) {
                $record->required = $row['required'];
                $record->accept_status = $row['accept_status'];
                $list[] = $record;
            }
        }
        return $list;
    }

    public function get_invite_meetings(&$user)
    {
        $template = $this;
        $query = "SELECT meetings_users.required, meetings_users.accept_status, meetings_users.meeting_id from meetings_users where meetings_users.user_id='$user->id' AND( meetings_users.accept_status IS NULL OR	meetings_users.accept_status='none') AND meetings_users.deleted=0";
        $result = $this->db->query($query, true);
        $list = [];

        while ($row = $this->db->fetchByAssoc($result)) {
            $record = BeanFactory::retrieveBean($this->module_dir, $row['meeting_id']);
            if (!empty($record)) {
                $record->required = $row['required'];
                $record->accept_status = $row['accept_status'];
                $list[] = $record;
            }
        }
        return $list;
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
        $exclude = [];

        parent::save_relationship_changes($is_update, $exclude, []);
    }
} // end class def

/**
 * Global functions used to get enum list for Meetings Type field
 * TODO: Move these into Meeting class when we no longer need to support BWC
 */

/**
 * External API integration, for the Meetings drop-down list of what external APIs are available
 * @param SugarBean $focus
 * @param string $name
 * @param string $value
 * @param string $view
 * @return array External integrations available for meetings
 */
//TODO: do we really need focus, name and view params for this function
function getMeetingsExternalApiDropDown($focus = null, $name = null, $value = null, $view = null)
{
    global $dictionary, $app_list_strings;

    $cacheKeyName = 'meetings_type_drop_down';
    $apiList = sugar_cache_retrieve($cacheKeyName);
    if ($apiList === null) {
        $apiList = ExternalAPIFactory::getModuleDropDown('Meetings');
        $apiList = array_merge(['Sugar' => $app_list_strings['eapm_list']['Sugar']], $apiList);
        sugar_cache_put($cacheKeyName, $apiList);
    }

    if (!empty($value) && empty($apiList[$value])) {
        $apiList[$value] = $value;
    }

    // if options list name is defined in vardef and is a different list than eapm_list then use that list
    $typeField = $dictionary['Meeting']['fields']['type'];
    if (isset($typeField['options']) && $typeField['options'] != 'eapm_list') {
        $apiList = array_merge(getMeetingTypeOptions($dictionary, $app_list_strings), $apiList);
    }

    return $apiList;
}

/**
 * Meeting Type Options Array for dropdown list
 * @param array $dictionary - getting type name
 * @param array $app_list_strings - getting type options
 * @return array Meeting Type Options Array for dropdown list
 */
function getMeetingTypeOptions($dictionary, $app_list_strings)
{
    $result = [];

    // getting name of meeting type to fill dropdown list by its values
    if (isset($dictionary['Meeting']['fields']['type']['options'])) {
        $typeName = $dictionary['Meeting']['fields']['type']['options'];

        if (!empty($app_list_strings[$typeName])) {
            $typeList = $app_list_strings[$typeName];

            foreach ($typeList as $key => $value) {
                $result[$value] = $value;
            }
        }
    }

    return $result;
}
