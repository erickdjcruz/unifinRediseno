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

class Calendar extends Basic
{
    public $new_schema = true;
    public $module_dir = 'Calendar';
    public $object_name = 'Calendar';
    public $table_name = 'calendar';
    public $importable = true;
    public $team_id;
    public $team_set_id;
    public $team_count;
    public $team_name;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $doc_owner;
    public $user_favorites;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $following;
    public $following_link;
    public $my_favorite;
    public $favorite_link;
    public $options;
    public $event_start;
    public $event_end;
    public $duration_minutes;
    public $duration_hours;
    public $duration_days;
    public $color;
    public $dblclick_event;
    public $allow_create;
    public $allow_update;
    public $allow_delete;
    public $event_tooltip_template;
    public $day_event_template;
    public $week_event_template;
    public $month_event_template;
    public $agenda_event_template;
    public $timeline_event_template;
    public $schedulermonth_event_template;
    public $ical_event_template;
    public $calendar_type;

    /**
     * Keeps the type of calendar of events
     *
     * @var string
     */
    public $intervalType = '';

    public static $INTERVAL_TIME = 'time';
    public static $INTERVAL_DURATION = 'duration';

    /**
     * Set interval type
     *
     * Use this to facilitate calendar processing
     * @return void
     */
    public function setIntervalType(): void
    {
        if ($this->calendar_module === 'Calls'
            || $this->calendar_module === 'Meetings'
            || (
                (is_string($this->duration_minutes) && $this->duration_minutes !== '')
                || (is_string($this->duration_hours) && $this->duration_hours !== '')
                || (is_string($this->duration_days) && $this->duration_days !== '')
            )
        ) {
            $this->intervalType = self::$INTERVAL_DURATION;
        } else {
            $this->intervalType = self::$INTERVAL_TIME;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
