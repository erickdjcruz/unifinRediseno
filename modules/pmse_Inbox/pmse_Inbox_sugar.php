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


class pmse_Inbox_sugar extends Basic
{
    public $new_schema = true;
    public $module_name = 'pmse_Inbox';
    public $module_dir = 'pmse_Inbox';
    public $object_name = 'pmse_Inbox';
    public $table_name = 'pmse_inbox';
    public $importable = false;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $activities;
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
    public $cas_id;
    public $cas_parent;
    public $cas_status;
    public $pro_id;
    public $cas_title;
    public $pro_title;
    public $cas_custom_status;
    public $cas_init_user;
    public $cas_create_date;
    public $cas_update_date;
    public $cas_finish_date;
    public $cas_pin;
    public $cas_assigned_status;

    public const CAS_STATUS_COMPLETED = 'COMPLETED';
    public const CAS_STATUS_TERMINATED = 'TERMINATED';
    public const CAS_STATUS_IN_PROGRESS = 'IN PROGRESS';
    public const CAS_STATUS_CANCELLED = 'CANCELLED';
    public const CAS_STATUS_ERROR = 'ERROR';


    public function __construct()
    {
        parent::__construct();
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
     * Populate the cas_status enum field. Marked as final so that these values can never be overwritten by extending
     * the class
     * @return array
     */
    final public function getCasStatusTypes(): array
    {
        return [
            static::CAS_STATUS_COMPLETED => translate('LBL_STATUS_COMPLETED_ENUM', 'pmse_Inbox'),
            static::CAS_STATUS_TERMINATED => translate('LBL_STATUS_TERMINATED_ENUM', 'pmse_Inbox'),
            static::CAS_STATUS_IN_PROGRESS => translate('LBL_STATUS_IN_PROGRESS_ENUM', 'pmse_Inbox'),
            static::CAS_STATUS_CANCELLED => translate('LBL_STATUS_CANCELLED_ENUM', 'pmse_Inbox'),
            static::CAS_STATUS_ERROR => translate('LBL_STATUS_ERROR_ENUM', 'pmse_Inbox'),
        ];
    }
}
