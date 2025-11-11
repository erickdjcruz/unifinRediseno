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

class ShiftException extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ShiftExceptions';
    public $module_name = 'ShiftExceptions';
    public $object_name = 'ShiftException';
    public $table_name = 'shift_exceptions';
    public $importable = true;

    public $id;
    public $name;
    public $deleted;
    public $description;

    public $timezone;
    public $type;
    public $all_day;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $enabled;

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

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
