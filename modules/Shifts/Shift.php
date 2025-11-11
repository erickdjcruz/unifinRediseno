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

class Shift extends Basic
{
    public $new_schema = true;
    public $module_dir = 'Shifts';
    public $module_name = 'Shifts';
    public $object_name = 'Shift';
    public $table_name = 'shifts';
    public $importable = true;

    public $id;
    public $name;
    public $deleted;
    public $description;

    public $timezone;

    public $date_start;
    public $date_end;

    public $is_open_sunday;
    public $sunday_open_hour;
    public $sunday_open_minutes;
    public $sunday_close_hour;
    public $sunday_close_minutes;
    public $is_open_monday;
    public $monday_open_hour;
    public $monday_open_minutes;
    public $monday_close_hour;
    public $monday_close_minutes;
    public $is_open_tuesday;
    public $tuesday_open_hour;
    public $tuesday_open_minutes;
    public $tuesday_close_hour;
    public $tuesday_close_minutes;
    public $is_open_wednesday;
    public $wednesday_open_hour;
    public $wednesday_open_minutes;
    public $wednesday_close_hour;
    public $wednesday_close_minutes;
    public $is_open_thursday;
    public $thursday_open_hour;
    public $thursday_open_minutes;
    public $thursday_close_hour;
    public $thursday_close_minutes;
    public $is_open_friday;
    public $friday_open_hour;
    public $friday_open_minutes;
    public $friday_close_hour;
    public $friday_close_minutes;
    public $is_open_saturday;
    public $saturday_open_hour;
    public $saturday_open_minutes;
    public $saturday_close_hour;
    public $saturday_close_minutes;

    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $created_by_link;
    public $modified_user_link;

    public $teams;
    public $team_id;
    public $team_set_id;
    public $team_link;
    public $team_count_link;
    public $acl_team_set_id;
    public $team_count;
    public $team_name;
    public $acl_team_names;

    /**
     * Gets the time prop for a day. This will be either open or close for a day
     * of the week, so something like `wednesday_open` or `sunday_close`.
     *
     * This method expects that `$day` has already been normalized.
     *
     * @param string $day Day of the week
     * @param string $type `open` or `close`
     * @return string
     */
    protected function getTimeProp(string $day, string $type)
    {
        return sprintf('%s_%s', $day, $type);
    }

    /**
     * Checks if this shift is open on a given day of the week
     * @param string $day the string name of a day
     * @return boolean
     */
    public function isOpen(string $day)
    {
        $field = 'is_open_' . $day;
        return (bool)$this->$field;
    }

    /**
     * Gets the open time for a day for this shift
     * @param string $day Day of the week
     * @return string|null
     */
    public function getOpenTime(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'open');
    }

    /**
     * Gets the close time for a day for this shift
     * @param string $day Day of the week
     * @return string|null
     */
    public function getCloseTime(string $day)
    {
        return $this->getTimeForTypeOnDay($day, 'close');
    }

    /**
     * Gets the open or close time for a day for this shift
     * @param string $day Day of the week
     * @param string $type `open` or `close`
     * @return array
     */
    public function getTimeForTypeOnDay(string $day, string $type)
    {
        $type = trim(strtolower($type));
        if ($type !== 'open' && $type !== 'close') {
            return null;
        }

        $prop = $this->getTimeProp($day, $type);

        return
            [
                'hour' => (int)$this->{$prop . '_hour'},
                'minutes' => (int)$this->{$prop . '_minutes'},
            ];
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
