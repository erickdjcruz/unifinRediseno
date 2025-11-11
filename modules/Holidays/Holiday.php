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

/*********************************************************************************
 * Description:
 ********************************************************************************/
class Holiday extends SugarBean
{
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $name;
    public $holiday_date;
    public $description;
    public $person_id;
    public $person_type;
    public $related_module;
    public $related_module_id;

    public $table_name = 'holidays';
    public $object_name = 'Holiday';
    public $module_dir = 'Holidays';
    public $new_schema = true;


    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    public function get_summary_text()
    {
        return $this->name;
    }
}
