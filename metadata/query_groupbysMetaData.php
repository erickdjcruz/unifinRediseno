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
$dictionary['QueryGroupBy'] = ['table' => 'query_groupbys'
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
        'groupby_field' => [
            'name' => 'groupby_field',
            'vname' => 'LBL_GROUPBY_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'groupby_module' => [
            'name' => 'groupby_module',
            'vname' => 'LBL_GROUPBY_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'groupby_calc_field' => [
            'name' => 'groupby_calc_field',
            'vname' => 'LBL_GROUPBY_CALC_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'groupby_calc_module' => [
            'name' => 'groupby_calc_module',
            'vname' => 'LBL_GROUPBY_CALC_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'groupby_type' => [
            'name' => 'groupby_type',
            'vname' => 'LBL_GROUPBY_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_type_dom',
            'len' => 25,
        ],
        'groupby_calc_type' => [
            'name' => 'groupby_calc_type',
            'vname' => 'LBL_GROUPBY_CALC_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_calc_type_dom',
            'len' => 25,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'groupby_axis' => [
            'name' => 'groupby_axis',
            'vname' => 'LBL_GROUPBY_AXIS',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_axis_dom',
            'len' => 25,
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
        'groupby_qualifier' => [
            'name' => 'groupby_qualifier',
            'vname' => 'LBL_GROUPBY_QUALIFIER',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_qualifier_dom',
            'len' => 25,
        ],
        'groupby_qualifier_qty' => [
            'name' => 'groupby_qualifier_qty',
            'vname' => 'LBL_GROUPBY_QUALIFIER_QTY',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_qualifier_qty_dom',
            'len' => 25,
        ],
        'groupby_qualifier_start' => [
            'name' => 'groupby_qualifier_start',
            'vname' => 'LBL_GROUPBY_QUALIFIER_START',
            'type' => 'enum',
            'required' => true,
            'options' => 'query_groupby_qualifier_start_dom',
            'len' => 25,
        ],


    ]
    , 'indices' => [
        ['name' => 'querygroupby_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_querygroupby', 'type' => 'index', 'fields' => ['groupby_field', 'deleted']],
    ],
];
