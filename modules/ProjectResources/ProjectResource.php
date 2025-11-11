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


/**
 *
 */
class ProjectResource extends SugarBean
{
    // database table columns
    public $id;
    public $date_modified;
    public $assigned_user_id;
    public $modified_user_id;
    public $created_by;

    public $team_id;
    public $deleted;

    // related information
    public $modified_by_name;
    public $created_by_name;

    public $team_name;

    public $project_id;
    public $resource_id;
    public $resource_type;

    public $object_name = 'ProjectResource';
    public $module_dir = 'ProjectResources';
    public $new_schema = true;
    public $table_name = 'project_resources';
}
