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

// Task is used to store customer information.

/**
 * @property Link2 $contacts
 */
class Task extends CalendarEvent
{
    // Stored fields
    public $date_due_flag;
    public $date_due;
    public $time_due;
    public $date_start_flag;
    public $priority;

    //bug 28138 todo
    //	var $default_task_name_values = array('Assemble catalogs', 'Make travel arrangements', 'Send a letter', 'Send contract', 'Send fax', 'Send a follow-up letter', 'Send literature', 'Send proposal', 'Send quote', 'Call to schedule meeting', 'Setup evaluation', 'Get demo feedback', 'Arrange introduction', 'Escalate support request', 'Close out support request', 'Ship product', 'Arrange reference call', 'Schedule training', 'Send local user group information', 'Add to mailing list');

    public $table_name = 'tasks';
    public $object_name = 'Task';
    public $object_id = 'task_id';
    public $module_dir = 'Tasks';

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name', 'assigned_user_id', 'contact_name', 'contact_phone', 'contact_email', 'parent_name'];

    public function save($check_notify = false)
    {
        if (empty($this->status)) {
            $this->status = $this->getDefaultStatus();
        }

        return parent::save($check_notify);
    }

    public function fill_in_additional_list_fields()
    {
    }

    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();

        if (isset($this->contact_id)) {
            $contact = BeanFactory::getBean('Contacts', $this->contact_id);

            if ($contact->id != '') {
                $this->contact_email = $contact->emailAddress->getPrimaryAddress($contact);
            } else {
                $this->contact_email = '';
                $this->contact_id = '';
            }
        }
    }

    protected function formatStartAndDueDates(&$task_fields, $dbtime, $override_date_for_subpanel)
    {
        global $timedate;

        if (empty($dbtime)) {
            return;
        }

        $today = $timedate->nowDbDate();

        $task_fields['TIME_DUE'] = $timedate->to_display_time($dbtime);
        $task_fields['DATE_DUE'] = $timedate->to_display_date($dbtime);

        $date_due = $task_fields['DATE_DUE'];

        $dd = $timedate->to_db_date($date_due, false);
        $taskClass = 'futureTask';
        if ($dd < $today) {
            $taskClass = 'overdueTask';
        } elseif ($dd == $today) {
            $taskClass = 'todaysTask';
        }
        $task_fields['DATE_DUE'] = "<font class='$taskClass'>$date_due</font>";
        if ($override_date_for_subpanel) {
            $task_fields['DATE_START'] = "<font class='$taskClass'>$date_due</font>";
        }
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $current_module_strings, $app_list_strings, $timedate;

        $override_date_for_subpanel = false;
        if (!empty($_REQUEST['module']) && $_REQUEST['module'] != 'Calendar' && $_REQUEST['module'] != 'Tasks' && $_REQUEST['module'] != 'Home') {
            //this is a subpanel list view, so override the due date with start date so that collections subpanel works as expected
            $override_date_for_subpanel = true;
        }

        $today = $timedate->nowDb();
        $task_fields = $this->get_list_view_array();
        $dbtime = $timedate->to_db($task_fields['DATE_DUE']);
        if ($override_date_for_subpanel) {
            $dbtime = $timedate->to_db($task_fields['DATE_START']);
        }

        if (!empty($dbtime)) {
            $task_fields['TIME_DUE'] = $timedate->to_display_time($dbtime);
            $task_fields['DATE_DUE'] = $timedate->to_display_date($dbtime);
            $this->formatStartAndDueDates($task_fields, $dbtime, $override_date_for_subpanel);
        }

        if (!empty($this->priority)) {
            $task_fields['PRIORITY'] = $app_list_strings['task_priority_dom'][$this->priority];
        }
        if (isset($this->parent_type)) {
            $task_fields['PARENT_MODULE'] = $this->parent_type;
        }
        if ($this->status != 'Completed' && $this->status != 'Deferred') {
            $setCompleteUrl = "<a id='{$this->id}' onclick='SUGAR.util.closeActivityPanel.show(\"{$this->module_dir}\",\"{$this->id}\",\"Completed\",\"listview\",\"1\");'>";
            $task_fields['SET_COMPLETE'] = $setCompleteUrl . SugarThemeRegistry::current()->getImage('close_inline', 'title=' . translate('LBL_LIST_CLOSE', 'Tasks') . " border='0'", null, null, '.gif', translate('LBL_LIST_CLOSE', 'Tasks')) . '</a>';
        }

        // make sure we grab the localized version of the contact name, if a contact is provided
        if (!empty($this->contact_id)) {
            $contact_temp = BeanFactory::getBean('Contacts', $this->contact_id);
            if (!empty($contact_temp)) {
                // Make first name, last name, salutation and title of Contacts respect field level ACLs
                $contact_temp->_create_proper_name_field();
                $this->contact_name = $contact_temp->full_name;
                $this->contact_phone = $contact_temp->phone_work;
            }
        }

        $task_fields['CONTACT_NAME'] = $this->contact_name;
        $task_fields['CONTACT_PHONE'] = $this->contact_phone;
        $task_fields['TITLE'] = '';
        if (!empty($task_fields['CONTACT_NAME'])) {
            $task_fields['TITLE'] .= $current_module_strings['LBL_LIST_CONTACT'] . ': ' . $task_fields['CONTACT_NAME'];
        }
        if (!empty($this->parent_name)) {
            $task_fields['TITLE'] .= "\n" . $app_list_strings['parent_type_display'][$this->parent_type] . ': ' . $this->parent_name;
            $task_fields['PARENT_NAME'] = $this->parent_name;
        }

        return $task_fields;
    }

    public function set_notification_body($xtpl, &$task)
    {
        global $app_list_strings;
        global $timedate;
        $notifyUser = $task->current_notify_user;
        $prefDate = $notifyUser->getUserDateTimePreferences();
        $xtpl->assign('TASK_SUBJECT', $task->name);
        //MFH #13507
        $xtpl->assign('TASK_PRIORITY', (isset($task->priority) ? $app_list_strings['task_priority_dom'][$task->priority] : ''));

        if (!empty($task->date_due)) {
            $duedate = $timedate->fromDb($task->date_due);
            $xtpl->assign('TASK_DUEDATE', $timedate->asUser($duedate, $notifyUser) . ' ' . TimeDate::userTimezoneSuffix($duedate, $notifyUser));
        } else {
            $xtpl->assign('TASK_DUEDATE', '');
        }

        $xtpl->assign('TASK_STATUS', (isset($task->status) ? $app_list_strings['task_status_dom'][$task->status] : ''));
        $xtpl->assign('TASK_DESCRIPTION', $task->description);

        return $xtpl;
    }
}
