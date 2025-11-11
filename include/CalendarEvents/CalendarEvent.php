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


class CalendarEvent extends SugarBean
{
    // Stored fields
    public $date_entered;
    public $date_modified;
    public $assigned_user_id;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $team_id;
    public $description;
    public $name;
    public $status;
    public $date_start;
    public $time_start;
    public $parent_type;
    public $parent_id;
    public $contact_id;
    public $parent_name;
    public $contact_name;
    public $contact_phone;
    public $contact_email;
    public $assigned_user_name;
    public $team_name;
    public $importable = true;
    public $new_schema = true;
    public $start_field = 'date_due';

    /**
     * Unique ID for each event, used by external systems
     * @var string
     */
    public $ical_uid = null;

    /**
     * Checks wether an event is recurring or not
     * @return bool
     *
     * @throws SugarException
     */
    public function isEventRecurring()
    {
        $logmsg = 'Recurring Calendar Event - Module Unexpected: ' . $this->module_name;
        LoggerManager::getLogger()->fatal($logmsg);

        throw new SugarException('LBL_CALENDAR_EVENT_RECURRENCE_MODULE_NOT_SUPPORTED', [$this->module_name]);
    }

    /**
     * Add record defined by parent field as an invitee if it is a Contact or Lead record
     *
     * @param $parentType
     * @param $parentId
     */
    public function inviteParent(string $parentType, string $parentId)
    {
        $inviteeRelationships = [
            'Contacts' => 'contacts',
            'Leads' => 'leads',
        ];

        foreach ($inviteeRelationships as $module => $relationship) {
            if ($parentType === $module) {
                $this->load_relationship($relationship);

                if ($this->$relationship &&
                    !$this->$relationship->relationship_exists($relationship, ['id' => $parentId])) {
                    $this->$relationship->add($parentId);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get_summary_text()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function listviewACLHelper()
    {
        global $current_user;

        $arrayAssign = parent::listviewACLHelper();
        $isOwner = false;

        if (!empty($this->parent_name)) {
            if (!empty($this->parent_name_owner)) {
                $isOwner = $current_user->id == $this->parent_name_owner;
            }
        }

        if (!ACLController::moduleSupportsACL($this->parent_type) ||
            ACLController::checkAccess($this->parent_type, 'view', $isOwner)) {
            $arrayAssign['PARENT'] = 'a';
        } else {
            $arrayAssign['PARENT'] = 'span';
        }

        $isOwner = false;

        if (!empty($this->contact_name)) {
            if (!empty($this->contact_name_owner)) {
                $isOwner = $current_user->id == $this->contact_name_owner;
            }
        }

        if (ACLController::checkAccess('Contacts', 'view', $isOwner)) {
            $arrayAssign['CONTACT'] = 'a';
        } else {
            $arrayAssign['CONTACT'] = 'span';
        }

        return $arrayAssign;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultStatus(): string
    {
        global $current_language;

        $def = $this->field_defs['status'];

        if (isset($def['default'])) {
            return $def['default'] || '';
        } else {
            $app = return_app_list_strings_language($current_language);

            if (isset($def['options']) && isset($app[$def['options']])) {
                $keys = array_keys($app[$def['options']]);
                $firstKeyIndex = 0;

                return is_array($keys) && array_key_exists($firstKeyIndex, $keys) ? $keys[$firstKeyIndex] : '';
            }
        }

        return '';
    }

    /**
     * Get array of needed time data
     *
     * @return array
     */
    public function getTimeData(): array
    {
        global $timedate;

        $arr = [];

        $startField = $this->start_field;
        $endField = 'date_end';

        if (empty($this->$startField)) {
            return [];
        }

        if (empty($this->$endField)) {
            $this->$endField = $this->$startField;
        }

        if ($timedate->check_matching_format($this->$startField, TimeDate::DB_DATETIME_FORMAT)) {
            $userStartDate = $timedate->to_display_date_time($this->$startField);
            $userEndDate = $timedate->to_display_date_time($this->$endField);
        } else {
            $userStartDate = $this->$startField;
            $userEndDate = $this->$endField;
        }

        $dtmStart = SugarDateTime::createFromFormat(
            $timedate->get_date_time_format(),
            $userStartDate,
            new DateTimeZone('UTC')
        );

        $dtmEnd = SugarDateTime::createFromFormat(
            $timedate->get_date_time_format(),
            $userEndDate,
            new DateTimeZone('UTC')
        );

        $arr['timestamp'] = $dtmStart->format('U');
        $arr['time_start'] = $timedate->fromTimestamp($arr['timestamp'])->format(
            $timedate->get_time_format()
        );

        $arr['ts_start'] = $dtmStart->get(
            '-' . $dtmStart->format('H') . ' hours -' . $dtmStart->format('i') . ' minutes -' . $dtmStart->format(
                's'
            ) . ' seconds'
        )->format('U');

        $arr['offset'] = $dtmStart->format('H') * 3600 + $dtmStart->format('i') * 60;

        if ($this->object_name !== 'Task') {
            $dtmEnd->modify('-1 minute');
        }

        $arr['ts_end'] = $dtmEnd->get('+1 day')->get(
            '-' . $dtmEnd->format('H') . ' hours -' . $dtmEnd->format('i') . ' minutes -' . $dtmEnd->format(
                's'
            ) . ' seconds'
        )->format('U');

        $arr['days'] = ($arr['ts_end'] - $arr['ts_start']) / (3600 * 24);

        return $arr;
    }

    /**
     * Get array that will be sent back to ajax frontend
     * @return array
     */
    public function getBeanDataArray(): array
    {
        global $current_user;

        if (isset($this->parent_name) && isset($_REQUEST['parent_name'])) {
            $this->parent_name = $_REQUEST['parent_name'];
        }

        $users = $this->getEventUsers();
        $user_ids = [];

        foreach ($users as $u) {
            $user_ids[] = $u->id;
        }

        $field_list = CalendarEventsUtils::getInstance()->get_fields();
        $field_arr = [];

        foreach ($field_list[$this->module_dir] as $field) {
            if ($field === 'related_to') {
                $focus = BeanFactory::getBean($this->parent_type, $this->parent_id);
                $field_arr[$field] = $focus->name;
            } else {
                $field_arr[$field] = $this->$field;
            }
        }

        $arr = [
            'access' => 'yes',
            'type' => strtolower($this->object_name),
            'module_name' => $this->module_dir,
            'user_id' => $current_user->id,
            'detail' => 1,
            'edit' => 1,
            'name' => $this->name,
            'record' => $this->id,
            'users' => $user_ids,
        ];

        if (!empty($this->repeat_parent_id)) {
            $arr['repeat_parent_id'] = $this->repeat_parent_id;
        }

        $arr = array_merge($arr, $field_arr);
        $arr = array_merge($arr, $this->getTimeData());

        return $arr;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatData(): array
    {
        return [];
    }

    /**
     * Retrieve a list of occurrences
     *
     * @param string $select
     * @param string $eventType
     * @param array $occurrencesStartDates
     * @param string $limit
     * @param string $offset
     * @param string $orderBy
     * @param string $orderByDirection
     *
     * @return array
     */
    public function getOccurrences(
        string $select = '*',
        string $eventType = '',
        array $occurrencesStartDates = [],
        string $limit = '',
        string $offset = '',
        string $orderBy = '',
        string $orderByDirection = 'ASC'
    ): array {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getEventUsers(): array
    {
        return [];
    }

    /**
     * Get occurrence ID by start date
     *
     * @param string $occurrenceStartDate
     *
     * @return string
     */
    public function getOccurrenceIdFromStartDate(string $occurrenceStartDate): string
    {
        return '';
    }

    /**
     * Save External Events
     * @return bool
     */
    public function saveExternal(): bool
    {
        return true;
    }
}
