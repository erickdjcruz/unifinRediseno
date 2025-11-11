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
$dictionary['ReportMaker'] = ['table' => 'report_maker'
    , 'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => true,
            'default' => '0',
            'reportable' => false,
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
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => true,
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
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '50',
            'importable' => 'required',
            'required' => true,
        ],
        'title' => [
            'name' => 'title',
            'vname' => 'LBL_TITLE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'report_align' => [
            'name' => 'report_align',
            'vname' => 'LBL_REPORT_ALIGN',
            'type' => 'enum',
            'len' => '8',
            'options' => 'report_align_dom',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
        ],
        'scheduled' => [
            'name' => 'scheduled',
            'vname' => 'LBL_SCHEDULED',
            'type' => 'bool',
            'default' => 0,
        ],

    ]
    , 'indices' => [
        ['name' => 'rmaker_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_rmaker', 'type' => 'index', 'fields' => ['name', 'deleted']],
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'allowUserRead' => true,
        ],
    ],
];

VardefManager::createVardef('ReportMaker', 'ReportMaker', [
    'team_security',
]);
