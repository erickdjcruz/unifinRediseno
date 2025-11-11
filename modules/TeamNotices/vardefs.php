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
$dictionary['TeamNotices'] = ['table' => 'team_notices', 'archive' => false
    , 'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'reportable' => false,
            'required' => false,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
        ],
        'date_start' => [
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'date',
            'required' => true,
            'importable' => 'required',
        ],
        'date_end' => [
            'name' => 'date_end',
            'vname' => 'LBL_DATE_END',
            'type' => 'date',
            'required' => true,
            'importable' => 'required',
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => false,
            'required' => false,
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => false,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '50',
            'required' => true,
            'importable' => 'required',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'varchar',
            'len' => 100,
        ],
        'url' => [
            'name' => 'url',
            'vname' => 'LBL_URL',
            'type' => 'varchar',
            'len' => '255',
        ],
        'url_title' => [
            'name' => 'url_title',
            'vname' => 'LBL_URL_TITLE',
            'type' => 'varchar',
            'len' => '255',
        ],
    ]
    , 'indices' => [
        ['name' => 'team_noticesspk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_team_notice', 'type' => 'index', 'fields' => ['name', 'deleted']],
    ],
];

VardefManager::createVardef('TeamNotices', 'TeamNotices', [
    'team_security',
]);
