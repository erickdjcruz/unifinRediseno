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
$dictionary['QueryColumn'] = ['table' => 'query_columns'
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
        'column_name' => [
            'name' => 'column_name',
            'vname' => 'LBL_COLUMN_NAME',
            'type' => 'varchar',
            'len' => '50',
        ],
        'column_module' => [
            'name' => 'column_module',
            'vname' => 'LBL_COLUMN_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'column_type' => [
            'name' => 'column_type',
            'vname' => 'LBL_COLUMN_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_column_type_dom',
            'len' => 25,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'list_order_x' => [
            'name' => 'list_order_x',
            'vname' => 'LBL_LIST_ORDER_X',
            'type' => 'int',
            'len' => '4',
        ],
        'list_order_y' => [
            'name' => 'list_order_y',
            'vname' => 'LBL_LIST_ORDER_Y',
            'type' => 'int',
            'len' => '4',
        ],

    ]
    , 'indices' => [
        ['name' => 'querycolumn_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_querycolumn', 'type' => 'index', 'fields' => ['column_name', 'deleted']],
    ],
];
