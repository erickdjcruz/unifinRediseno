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

$dictionary['WorkFlowAction'] = ['table' => 'workflow_actions'
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
        'field' => [
            'name' => 'field',
            'vname' => 'LBL_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'value' => [
            'name' => 'value',
            'vname' => 'LBL_VALUE',
            'type' => 'text',
        ],
        'set_type' => [
            'name' => 'set_type',
            'vname' => 'LBL_SET_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_set_type_dom',
            'len' => 10,
        ],
        'adv_type' => [
            'name' => 'adv_type',
            'vname' => 'LBL_ADV_TYPE',
            'type' => 'enum',
            'options' => 'wflow_adv_type_dom',
            'len' => 10,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'ext1' => [
            'name' => 'ext1',
            'vname' => 'LBL_OPTION',
            'type' => 'varchar',
            'len' => '50',
        ],
        'ext2' => [
            'name' => 'ext2',
            'vname' => 'LBL_OPTION',
            'type' => 'varchar',
            'len' => '50',
        ],
        'ext3' => [
            'name' => 'ext3',
            'vname' => 'LBL_OPTION',
            'type' => 'varchar',
            'len' => '50',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => true],

    'indices' => [
        ['name' => 'action_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_action', 'type' => 'index', 'fields' => ['deleted']],
    ],
];
