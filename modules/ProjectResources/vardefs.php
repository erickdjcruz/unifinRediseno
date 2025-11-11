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


$dictionary['ProjectResource'] = [
    'table' => 'project_resources',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'required' => true,
            'type' => 'id',
            'reportable' => false,
            'comment' => 'Unique identifier',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED_USER_ID',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_CREATED_BY',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'comment' => 'User who created record',
        ],
        'project_id' => [
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT_ID',
            'reportable' => false,
            'dbtype' => 'id',
            'type' => 'id',
        ],
        'resource_id' => [
            'name' => 'resource_id',
            'vname' => 'LBL_RESOURCE_ID',
            'reportable' => false,
            'dbtype' => 'id',
            'type' => 'id',
        ],
        'resource_type' => [
            'name' => 'resource_type',
            'vname' => 'LBL_RESOURCE_TYPE',
            'reportable' => false,
            'type' => 'varchar',
            'len' => '20',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'default' => '0',
            'comment' => 'Record deletion indicator',
        ],
    ],
    'indices' => [
        ['name' => 'project_resources_pk', 'type' => 'primary', 'fields' => ['id']],
    ],
];
