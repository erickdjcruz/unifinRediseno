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

$dictionary['WorkFlowSchedule'] = ['table' => 'workflow_schedules'
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
            'required' => false,
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
        'date_expired' => [
            'name' => 'date_expired',
            'vname' => 'LBL_DATE_EXPIRED',
            'type' => 'datetime',
            'required' => true,
        ],
        'workflow_id' => [
            'name' => 'workflow_id',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'target_module' => [
            'name' => 'target_module',
            'vname' => 'LBL_TARGET_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'parameters' => [
            'name' => 'parameters',
            'vname' => 'LBL_PARAMETERS',
            'type' => 'varchar',
            'len' => '255',
            'required' => false,
        ],
    ]
    , 'indices' => [
        ['name' => 'schedule_k', 'type' => 'primary', 'fields' => ['id']],
    ],
];
