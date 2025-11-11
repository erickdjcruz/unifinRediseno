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

class RecurringCalendarEvent extends CalendarEvent
{
    use RecurringCalendarEventTrait;

    // Stored fields
    public $duration_hours;
    public $duration_minutes;
    public $date_end;
    public $parent_type_options;
    public $user_id;
    public $reminder_time;
    public $reminder_checked;
    public $email_reminder_time;
    public $email_reminder_checked;
    public $email_reminder_sent;
    public $required;
    public $accept_status;
    public $account_id;
    public $opportunity_id;
    public $case_id;
    public $outlook_id;
    public $update_vcal = true;
    public $contacts_arr = [];
    public $users_arr = [];
    public $leads_arr = [];
    public $minutes_value_default = 15;
    public $minutes_values = ['0' => '00', '15' => '15', '30' => '30', '45' => '45'];
    public $recurring_source;
    public $fill_additional_column_fields = true;
    public $send_invites = false;
    public $start_field = 'date_start';

    /**
     * Parent id of recurring.
     * @var string
     */
    public $repeat_parent_id = null;

    /**
     * Recurrence id. Original start date of event.
     * @var string
     */
    public $recurrence_id = null;

     /**
     * The Rset for the recurrence events
     * @var string
     */
    public $rset = '';

    /**
     * The recurring event original start date
     * @var string
     */
    public $original_start_date = null;

    /**
     * The Event type of each occurrence
     * @var string
     */
    public $event_type = null;

    private $MASTER_MEETING = 'master';
    private $OCCURRENCE_MEETING = 'occurrence';
    private $EXCEPTION_MEETING = 'exception';

    public function __construct()
    {
        global $app_list_strings;

        parent::__construct();
        $this->setupCustomFields($this->module_dir);

        foreach ($this->field_defs as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $this->field_defs[$field['name']] = $field;
        }

        if (!empty($app_list_strings['duration_intervals'])) {
            $this->minutes_values = $app_list_strings['duration_intervals'];
        }
    }

    /**
     * Go through all occurrences and see if any is an exception
     * If so, mark it accordingly
     *
     * @return void
     */
    public function sanitizeOccurrences()
    {
        // we don't need to sanitize anything if this is an occurrence
        if ($this->repeat_parent_id) {
            return;
        }

        $this->generateRset();
        $this->event_type = $this->MASTER_MEETING;
        $this->original_start_date = $this->date_start;

        $occurrences = $this->getOccurrences('id');

        foreach ($occurrences as $occurrence) {
            $occurrenceBean = BeanFactory::retrieveBean($this->module_name, $occurrence['id']);

            if ($occurrenceBean) {
                $occurrenceBean->rset = '';
                $occurrenceBean->original_start_date = $occurrenceBean->date_start;
                $occurrenceBean->event_type = $this->OCCURRENCE_MEETING;

                $occurrenceBean->processed = true;
                $occurrenceBean->save(false, false);
            }
        }
    }

     /**
     * Generates the Rset from a sugar recurrence pattern
     *
     * @return void
     */
    public function generateRset()
    {
        $calendarUtils = CalendarEventsUtils::getInstance();

        if ($this->repeat_type  && empty($this->repeat_parent_id)) {
            $repeatFields = ['repeat_count', 'repeat_until', 'repeat_interval', 'repeat_dow', 'repeat_selector', 'repeat_days', 'repeat_ordinal', 'repeat_type', 'repeat_unit', 'date_start'];
            $newArgs = [];

            foreach ($repeatFields as $field) {
                $newArgs[$field] = $this->{$field};
            }
            $exdate = $this->getRsetExDate();

            $rrule = $calendarUtils->getRruleStringFromParams($newArgs);

            $this->rset = json_encode([
                'rrule' => $rrule,
                'exdate' => $exdate,
                'sugarSupportedRrule' => true,
            ]);
        } else {
            $this->rset = '';
        }
    }

     /**
     * Generates the sugar recurrence pattern from a rset
     * @param string $rset
     * @return void
     */
    public function generateRecurrencePattern(string $rset)
    {
        $calendarUtils = CalendarEventsUtils::getInstance();

        $rSet = json_decode($rset, true);
        $rrule = isset($rSet['rrule']) ? $rSet['rrule'] : '';
        $exdate = isset($rSet['exdate']) ? $rSet['exdate'] : [];
        $newArgs = $calendarUtils->translateRRuleToSugarRecurrence($rrule);

        $newRset = [
            'rrule' => $rrule,
            'exdate' => $exdate,
            'sugarSupportedRrule' => $newArgs['sugarSupportedRrule'],
        ];

        $this->rset = json_encode($newRset);

        global $timedate;
        $startDate = SugarDateTime::createFromFormat('Y-m-d'.'\T'.'H:i:s', $newArgs['date_start']);
        $dateStartFormatted = $startDate->format($timedate->get_date_time_format());
        $newArgs['date_start'] = $dateStartFormatted;

        $repeatFields = ['repeat_count', 'repeat_until', 'repeat_interval', 'repeat_dow', 'repeat_selector', 'repeat_days', 'repeat_ordinal', 'repeat_type', 'date_start'];
        $repeatFieldsUnsupported = ['repeat_type', 'date_start'];

        if ($newArgs['sugarSupportedRrule']) {
            foreach ($repeatFields as $field) {
                if (array_key_exists($field, $newArgs)) {
                    $this->{$field} = $newArgs[$field];
                }
            }
        } else {
            foreach ($repeatFieldsUnsupported as $field) {
                if (array_key_exists($field, $newArgs)) {
                    $this->{$field} = $newArgs[$field];
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $retrievedBean = parent::retrieve($id, $encode, $deleted);

        if (!empty($this->repeat_parent_id)) {
            $masterEvent = BeanFactory::retrieveBean($this->module_name, $this->repeat_parent_id);

            $masterEventRset = $this->setHumanReadableInCurrentUserLanguage($masterEvent)->rset;

            if ($masterEvent) {
                $retrievedBean->rset = $masterEventRset;
            }
        } elseif (isset($retrievedBean->event_type) && $retrievedBean->event_type === 'master') {
            $retrievedBean = $this->setHumanReadableInCurrentUserLanguage($retrievedBean);
        }

        return $retrievedBean;
    }

    /**
     * Sets the Human Readable string from the rrule of the event and the current_user language.
     *
     * @param SugarBean $bean the bean of the event
     * @return SugarBean
     */
    public function setHumanReadableInCurrentUserLanguage($bean)
    {
        if (isset($bean->rset) && !empty($bean->rset)) {
            $decodeRset = json_decode($bean->rset, true);
            $rrule = isset($decodeRset['rrule']) ? $decodeRset['rrule'] : '';

            // if somehow the rset is malformed we have to regenerate it
            if (!$decodeRset && $bean->rset) {
                $bean->generateRset();

                $decodeRset = json_decode($bean->rset, true);

                if (!$decodeRset) {
                    $bean->rset = '';

                    return $bean;
                }

                $decodeRset['exdate'] = [];

                // we need to build the exceptions list as well
                $occurrences = $bean->getOccurrences(
                    [
                        'id',
                        'original_start_date',
                        'date_start',
                    ],
                    $this->EXCEPTION_MEETING
                );

                foreach ($occurrences as $occurrence) {
                    $occurenceUID = $occurrence['original_start_date'] ?
                                    $occurrence['original_start_date'] :
                                    $occurrence['date_start'];

                    $decodeRset['exdate'][] = $occurenceUID;
                }

                $bean->rset = json_encode($decodeRset);
                $bean->processed = true;
                $bean->save();

                $rrule = isset($decodeRset['rrule']) ? $decodeRset['rrule'] : '';
            }

            if (!$rrule) {
                $bean->rset = '';

                return $bean;
            }

            $humanReadableString = CalendarEventsUtils::getInstance()->getHumanReadableString($rrule);
            $decodeRset['humanReadableString'] = $humanReadableString;

            $encodedRset = json_encode($decodeRset);
            $bean->rset = $encodedRset;
        }

        return $bean;
    }

    // save date_end by calculating user input
    public function save($check_notify = false, $manageRecurrence = true)
    {
        global $timedate, $current_user;

        $isUpdate = $this->isUpdate();

        if (isset($this->date_start)) {
            $td = $timedate->fromDb($this->date_start);

            if (!$td) {
                $this->date_start = $timedate->to_db($this->date_start);
                $td = $timedate->fromDb($this->date_start);
            }

            if ($td) {
                $this->setStartAndEndDateTime($td);
            }
        }

        if ($this->repeat_type && $this->repeat_type !== 'Weekly') {
            $this->repeat_dow = '';
        }

        if ($this->repeat_selector === 'None') {
            $this->repeat_unit = '';
            $this->repeat_ordinal = '';
            $this->repeat_days = '';
        }

        $check_notify = $this->send_invites;

        if ($this->send_invites === false && $this->isEmailNotificationNeeded()) {
            $this->special_notification = true;
            $check_notify = true;

            CalendarEventsUtils::getInstance()->setOldAssignedUserValue($this->assigned_user_id);

            if (isset($_REQUEST['assigned_user_name'])) {
                $this->new_assigned_user_name = $_REQUEST['assigned_user_name'];
            }
        }

        // prevent a mass mailing for recurring events created in Calendar module
        $isRecurringInCalendar = empty($this->id) && !empty($_REQUEST['module']) && $_REQUEST['module'] == 'Calendar' &&
            !empty($_REQUEST['repeat_type']) && !empty($this->repeat_parent_id);

        if ($isRecurringInCalendar) {
            $check_notify = false;
        }

        if (empty($this->status)) {
            $this->status = $this->getDefaultStatus();
        }

        if ($isUpdate && empty($this->event_type)) {
            if ($this->repeat_parent_id) {
                $parentBean = BeanFactory::retrieveBean($this->module_name, $this->repeat_parent_id);

                if ($parentBean) {
                    $parentBean->save();
                }
            } else {
                $this->sanitizeOccurrences();
            }
        }

        if ($this->isEventRecurring() && $manageRecurrence) {
            $this->manageRecurringMeetings();
        }

        $this->saveExternal();

        $returnId = parent::save($check_notify);

        // This function requires that the ID be set and therefore must come after parent::save()
        $this->handleInviteesForUserAssign($isUpdate);

        if ($this->update_vcal) {
            $assigned_user = BeanFactory::retrieveBean('Users', $this->assigned_user_id);

            if ($assigned_user) {
                vCal::cache_sugar_vcal($assigned_user);

                if ($this->assigned_user_id != $current_user->id) {
                    vCal::cache_sugar_vcal($current_user);
                }
            }
        }

        // CCL - Comment out event to set $current_user as invitee
        // set organizer to auto-accept
        // if there isn't a fetched row its new
        if (!$isUpdate) {
            $organizer = ($this->assigned_user_id == $current_user->id) ?
                $current_user : BeanFactory::retrieveBean('Users', $this->assigned_user_id);

            if ($organizer) {
                $this->set_accept_status($organizer, 'accept');
            }
        }

        return $returnId;
    }

    /**
     * @inheritdoc
     */
    public function mark_deleted($id)
    {
        if (!$id) {
            return null;
        }

        if ($this->id !== $id) {
            $bean = BeanFactory::retrieveBean($this->module_name, $id);

            if ($bean) {
                $bean->mark_deleted($id);
            }

            return null;
        }

        $this->correctRecurrences($id);
        parent::mark_deleted($id);
    }

    /**
     * Returns the summary text that should show up in the recent history list for this object.
     *
     * @return string
     * @deprecated Not used in the REST API
     */
    public function get_summary_text()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function get_list_view_data($filter_fields = [])
    {
        global $action, $timedate;
        $eventFields = $this->get_list_view_array();

        if (isset($this->parent_type) && $this->parent_type !== null) {
            $eventFields['PARENT_MODULE'] = $this->parent_type;
        }

        if ($this->status === 'Planned') {
            //cn: added this if() to deal with sequential Closes in Events.  this is a hack to a hack (formbase.php->handleRedirect)
            if (empty($action)) {
                $action = 'index';
            }

            $setCompleteUrl = "<a id='{$this->id}' onclick='SUGAR.util.closeActivityPanel.show(\"{$this->module_dir}" .
                                "\",\"{$this->id}\",\"Held\",\"listview\",\"1\");'>";

            if ($this->ACLAccess('edit')) {
                $image = SugarThemeRegistry::current()->getImage(
                    'close_inline',
                    " border='0'",
                    null,
                    null,
                    '.gif',
                    translate('LBL_CLOSEINLINE')
                );
                $eventFields['SET_COMPLETE'] = $setCompleteUrl . $image . '</a>';
            } else {
                $eventFields['SET_COMPLETE'] = '';
            }
        }

        $today = $timedate->nowDb();
        $nextday = $timedate->asDbDate($timedate->getNow()->modify('+1 day'));
        $mergeTime = $eventFields['DATE_START'];
        $date_db = $timedate->to_db($mergeTime);

        if ($date_db < $today) {
            $eventFields['DATE_START'] = "<font class='overdueTask'>" . $eventFields['DATE_START'] . '</font>';
        } elseif ($date_db < $nextday) {
            $eventFields['DATE_START'] = "<font class='todaysTask'>" . $eventFields['DATE_START'] . '</font>';
        } else {
            $eventFields['DATE_START'] = "<font class='futureTask'>" . $eventFields['DATE_START'] . '</font>';
        }

        $this->fill_in_additional_detail_fields();

        //make sure we grab the localized version of the contact name, if a contact is provided
        if (!empty($this->contact_id)) {
            // Bug# 46125 - make first name, last name, salutation and title of Contacts respect field level ACLs
            $contact_temp = BeanFactory::retrieveBean('Contacts', $this->contact_id);

            if (!empty($contact_temp)) {
                $contact_temp->_create_proper_name_field();
                $this->contact_name = $contact_temp->full_name;
            }
        }

        $eventFields['CONTACT_ID'] = $this->contact_id;
        $eventFields['CONTACT_NAME'] = $this->contact_name;
        $eventFields['PARENT_NAME'] = $this->parent_name;
        $eventFields['REMINDER_CHECKED'] = $this->reminder_time == -1 ? false : true;
        $eventFields['EMAIL_REMINDER_CHECKED'] = $this->email_reminder_time == -1 ? false : true;

        return $eventFields;
    }

    /**
     * @inheritdoc
     */
    public function set_notification_body($xtpl, &$event)
    {
        global $app_list_strings;

        $singularModuleLabel = strtoupper($app_list_strings['moduleListSingular'][$this->module_name]);

        $xtpl->assign($singularModuleLabel . '_TO', $event->current_notify_user->new_assigned_user_name);
        $xtpl->assign($singularModuleLabel . '_SUBJECT', $event->name);
        $xtpl->assign($singularModuleLabel . '_HOURS', $event->duration_hours);
        $xtpl->assign($singularModuleLabel . '_MINUTES', $event->duration_minutes);
        $xtpl->assign($singularModuleLabel . '_DESCRIPTION', $event->description);

        return $xtpl;
    }

    public function set_accept_status($user, $status)
    {
        if ($user->object_name === 'User') {
            $relate_values = ['user_id' => $user->id, $this->object_id => $this->id];
            $data_values = ['accept_status' => $status];

            $this->set_relationship($this->rel_users_table, $relate_values, true, true, $data_values);

            if ($this->update_vcal) {
                vCal::cache_sugar_vcal($user);
            }
        } elseif ($user->object_name === 'Contact') {
            $relate_values = ['contact_id' => $user->id, $this->object_id => $this->id];
            $data_values = ['accept_status' => $status];

            $this->set_relationship($this->rel_contacts_table, $relate_values, true, true, $data_values);
        } elseif ($user->object_name === 'Lead') {
            $relate_values = ['lead_id' => $user->id, $this->object_id => $this->id];
            $data_values = ['accept_status' => $status];

            $this->set_relationship($this->rel_leads_table, $relate_values, true, true, $data_values);
        }
    }

    /**
     * @inheritdoc
     */
    public function save_relationship_changes($isUpdate, $exclude = [])
    {
        global $soap_server_object;
        $exclude = [];

        if (empty($this->in_workflow)) {
            if (empty($this->in_import)) {//if an event is being imported then contact_id  should not be excluded
                //if the global soap_server_object variable is not empty (as in from a soap/OPI call), then process the assigned_user_id relationship, otherwise
                //add assigned_user_id to exclude list and let the logic from the respective FormBase determine whether assigned user id gets added to the relationship
                if (!empty($soap_server_object)) {
                    $exclude = array_merge(['contact_id', 'user_id'], $exclude);
                } else {
                    $exclude = array_merge(['contact_id', 'user_id', 'assigned_user_id'], $exclude);
                }
            } else {
                $exclude = ['user_id'];
            }
        }

        parent::save_relationship_changes($isUpdate, $exclude);
    }

    /**
     * Add or delete invitee from Event.
     *
     * @param string $link_name
     * @param array $invitees
     * @param array $existing
     */
    public function upgradeAttachInvitees($linkName, $invitees, $existing)
    {
        $this->load_relationship($linkName);

        foreach (array_diff($this->{$linkName}->get(), $invitees) as $id) {
            if ($this->created_by != $id) {
                $this->{$linkName}->delete($this->id, $id);
            }
        }

        foreach (array_diff($invitees, $this->{$linkName}->get()) as $id) {
            if (!isset($existing[$id])) {
                $this->{$linkName}->add($id);
            }
        }
    }

    /**
     * Stores user invitees.
     *
     * @param array $userInvitees Array of user invitees ids
     * @param array $existingUsers
     *
     * @return boolean true if no users given.
     */
    public function setUserInvitees($userInvitees, $existingUsers = [])
    {
        // If both are empty, don't do anything.
        // From the App these will always be set [they are set to at least current-user].
        // For the api, these sometimes will not be set [linking related records]
        if (empty($userInvitees) && empty($existingUsers)) {
            return true;
        }

        $this->users_arr = $userInvitees;
        $this->upgradeAttachInvitees('users', $userInvitees, $existingUsers);
    }

    /**
     * Stores contact invitees.
     *
     * @param array $contactInvitees Array of contact invitees ids
     * @param array $existingContacts
     */
    public function setContactInvitees($contactInvitees, $existingContacts = [])
    {
        $this->contacts_arr = $contactInvitees;
        $this->upgradeAttachInvitees('contacts', $contactInvitees, $existingContacts);
    }

    /**
     * Stores lead invitees.
     *
     * @param array $leadInvitees Array of lead invitees ids
     * @param array $existingLeads
     */
    public function setLeadInvitees($leadInvitees, $existingLeads = [])
    {
        $this->leads_arr = $leadInvitees;
        $this->upgradeAttachInvitees('leads', $leadInvitees, $existingLeads);
    }

    /**
     * @inheritdoc
     */
    public function get_notification_recipients()
    {
        if ($this->special_notification) {
            return parent::get_notification_recipients();
        }

        return $this->buildInvitesList();
    }

    /**
     * Build notification list for Evebts.
     *
     * @return string[]
     * @throws Exception
     */
    public function buildInvitesList()
    {
        global $log;

        $list = [];

        if (!is_array($this->contacts_arr)) {
            $this->contacts_arr = [];
        }

        if (!is_array($this->users_arr)) {
            $this->users_arr = [];
        }

        if (!is_array($this->leads_arr)) {
            $this->leads_arr = [];
        }

        foreach ($this->users_arr as $userId) {
            $notifyUser = BeanFactory::retrieveBean('Users', $userId);

            if (!empty($notifyUser->id)) {
                $notifyUser->new_assigned_user_name = $notifyUser->full_name;

                $log->info("Notifications: recipient is $notifyUser->new_assigned_user_name");

                $list[$notifyUser->id] = $notifyUser;
            }
        }

        foreach ($this->contacts_arr as $contactId) {
            $notifyUser = BeanFactory::retrieveBean('Contacts', $contactId);

            if (!empty($notifyUser->id)) {
                $notifyUser->new_assigned_user_name = $notifyUser->full_name;

                $log->info("Notifications: recipient is $notifyUser->new_assigned_user_name");

                $list[$notifyUser->id] = $notifyUser;
            }
        }

        foreach ($this->leads_arr as $leadId) {
            $notifyUser = BeanFactory::retrieveBean('Leads', $leadId);

            if (!empty($notifyUser->id)) {
                $notifyUser->new_assigned_user_name = $notifyUser->full_name;

                $log->info("Notifications: recipient is $notifyUser->new_assigned_user_name");

                $list[$notifyUser->id] = $notifyUser;
            }
        }

        return $list;
    }

    /**
     * {@inheritDoc}
     */
    public function loadFromRow($arr, $convert = false)
    {
        $fields = [
            'reminder_time' => 'reminder_checked',
            'email_reminder_time' => 'email_reminder_checked',
        ];

        foreach ($fields as $value => $flag) {
            if (isset($arr[$value]) && !isset($arr[$flag])) {
                $arr[$flag] = $arr[$value] > -1;
            }
        }

        parent::loadFromRow($arr, $convert);
    }

    /**
     * @param boolean $fill_additional_column_fields
     */
    public function setFillAdditionalColumnFields($fill_additional_column_fields)
    {
        $this->fill_additional_column_fields = $fill_additional_column_fields;
    }

    /**
     * Handles invitees list when Event is assigned to a user.
     * - new user should be added to invitees, if it is not already there;
     * - on create when current user assigns Event not to himself, add current user to invitees.
     * @param boolean $isUpdate Value captured prior to SugarBean Save
     */
    public function handleInviteesForUserAssign($isUpdate)
    {
        $this->load_relationship('users');
        $existingUsers = $this->users->get();

        if (isset($this->assigned_user_id) && !safeInArray($this->assigned_user_id, $existingUsers)) {
            $this->users->add($this->assigned_user_id);
        }
    }

    /**
     * Manages the changes that are done to a recurring meeting.
     * It adds the event_type label and original_start_date;
     * creates and updates the Rset with the Rrule and EXDATE when needed
     */
    public function manageRecurringMeetings()
    {
        $isUpdate = $this->isUpdate();

        // check if meeting used to be recurring and now it's not
        if ($isUpdate && $this->MASTER_MEETING && !$this->repeat_type) {
            // if so let's make sure we reset evertything
            $this->rset = '';
            $this->event_type = null;
            $this->original_start_date = null;
            return;
        }

        if (empty($this->repeat_parent_id)) {
            $exdate = $isUpdate ? $this->getRsetExDate() : [];
            $rrule = $this->getRsetRrule();
            $sugarSupportedRrule = $this->getRsetSugarSupportedRrule();

            $rset = [
                'rrule' => $rrule,
                'exdate' => $exdate,
                'sugarSupportedRrule' => $sugarSupportedRrule,
            ];

            $this->event_type = $this->MASTER_MEETING;
            $this->rset = json_encode($rset);
        } else {
            $exceptionFieldChanged = false;

            // we only consider the occurrence an exception if one of these fields gets changed
            $exceptionFields = [
                'date_start',
                'date_end',
                'duration_hours',
                'duration_minutes',
            ];

            foreach ($exceptionFields as $exceptionField) {
                if (array_key_exists($exceptionField, $this->fetched_row) &&
                    property_exists($this, $exceptionField) &&
                    $this->fetched_row[$exceptionField] !== $this->{$exceptionField} &&
                    !(empty($this->fetched_row[$exceptionField]) && empty($this->{$exceptionField}))) {
                    $exceptionFieldChanged = true;
                }
            }

            $isException = $isUpdate && $exceptionFieldChanged ? true : false;

            $this->rset = '';
            $this->event_type = $isException ? $this->EXCEPTION_MEETING : $this->OCCURRENCE_MEETING;

            if ($isException) {
                $masterEvent = BeanFactory::retrieveBean($this->module_name, $this->repeat_parent_id);

                if ($masterEvent) {
                    $masterEvent->updateRsetExDate($this->original_start_date);
                }
            }
        }

        if (!$isUpdate || !$this->original_start_date) {
            $this->original_start_date = $this->date_start;
        }
    }
}
