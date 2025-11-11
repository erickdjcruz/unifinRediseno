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
$dictionary['QueryFilter'] = ['table' => 'query_filters'
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
            'required' => true,
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
            'vname' => 'LBL_FILTER_NAME',
            'type' => 'varchar',
            'len' => '50',
        ],
        'left_field' => [
            'name' => 'left_field',
            'vname' => 'LBL_LEFT_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'left_module' => [
            'name' => 'left_module',
            'vname' => 'LBL_LEFT_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'right_field' => [
            'name' => 'right_field',
            'vname' => 'LBL_RIGHT_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'right_module' => [
            'name' => 'right_module',
            'vname' => 'LBL_RIGHT_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'filter_type' => [
            'name' => 'filter_type',
            'vname' => 'LBL_FILTER_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_filter_type_dom',
            'len' => 25,
        ],
        'left_type' => [
            'name' => 'left_type',
            'vname' => 'LBL_FILTER_LEFT_TYPE',
            'type' => 'enum',
            'options' => 'query_calc_leftright_type_dom',
            'len' => 10,
        ],
        'right_type' => [
            'name' => 'right_type',
            'vname' => 'LBL_FILTER_RIGHT_TYPE',
            'type' => 'enum',
            'options' => 'query_calc_leftright_type_dom',
            'len' => 10,
        ],
        'left_value' => [
            'name' => 'left_value',
            'vname' => 'LBL_LEFT_VALUE',
            'type' => 'varchar',
            'len' => '100',
        ],
        'right_value' => [
            'name' => 'right_value',
            'vname' => 'LBL_RIGHT_VALUE',
            'type' => 'varchar',
            'len' => '100',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'parent_filter_id' => [
            'name' => 'parent_filter_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'list_order' => [
            'name' => 'list_order',
            'vname' => 'LBL_LIST_ORDER',
            'type' => 'int',
            'len' => '4',
        ],
        'parent_filter_group' => [
            'name' => 'parent_filter_group',
            'vname' => 'LBL_PARENT_FILTER_GROUP',
            'type' => 'int',
            'len' => '8',
        ],
        'operator' => [
            'name' => 'operator',
            'vname' => 'LBL_OPERATOR',
            'type' => 'varchar',
            'len' => '15',
        ],
        'calc_enclosed' => [
            'name' => 'calc_enclosed',
            'vname' => 'LBL_CALC_ENCLOSED',
            'type' => 'varchar',
            'len' => '3',
        ],

    ]
    , 'indices' => [
        ['name' => 'filter_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_filter', 'type' => 'index', 'fields' => ['name', 'deleted']],
    ],
];
