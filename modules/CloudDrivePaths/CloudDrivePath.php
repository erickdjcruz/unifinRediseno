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

class CloudDrivePath extends Basic
{
    public $id;
    public $name;
    public $date_entered;
    public $created_by;
    public $created_by_name;
    public $is_root;
    public $folder_id;
    public $path;
    public $type;
    public $record_id;
    public $module;
    public $drive_id;
    public $table_name = 'cloud_drive_paths';
    public $object_name = 'CloudDrivePath';
    public $module_dir = 'CloudDrivePaths';
    public $new_schema = true;
}
