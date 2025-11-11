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

$dictionary['TimePeriod'] = [
    'table' => 'timeperiods',
    'archive' => false,
    'favorites' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_TP_NAME',
            'dbType' => 'varchar',
            //'type' => 'enum',
            'type' => 'timeperiod',
            'function' => 'get_timeperiods_dom',
            'len' => '36',
            'isnull' => false,
            'importable' => 'required',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'id_name' => 'id',
            'table' => 'timeperiods',
            'reportable' => false,
        ],
        'start_date' => [
            'name' => 'start_date',
            'vname' => 'LBL_TP_START_DATE',
            'type' => 'date',
            'isnull' => false,
            'importable' => 'required',
        ],
        'start_date_timestamp' => [
            'name' => 'start_date_timestamp',
            'vname' => 'LBL_TP_START_DATE_TIMESTAMP',
            'type' => 'int',
            'len' => '11',
            'required' => true,
            'enable_range_search' => true,
            'studio' => false,
            'reportable' => false,
        ],
        'end_date' => [
            'name' => 'end_date',
            'vname' => 'LBL_TP_END_DATE',
            'type' => 'date',
            'isnull' => false,
            'importable' => 'required',
        ],
        'end_date_timestamp' => [
            'name' => 'end_date_timestamp',
            'vname' => 'LBL_TP_END_DATE_TIMESTAMP',
            'type' => 'int',
            'len' => '11',
            'required' => true,
            'enable_range_search' => true,
            'studio' => false,
            'reportable' => false,
        ],
        'created_by' => [
            'name' => 'created_by',
            'vname' => 'LBL_CREATED_BY',
            'type' => 'id',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'reportable' => false,
        ],
        'is_fiscal' => [
            'name' => 'is_fiscal',
            'default' => 0,
            'vname' => 'LBL_TP_IS_FISCAL',
            'type' => 'bool',
        ],
        'is_fiscal_year' => [
            'name' => 'is_fiscal_year',
            'default' => 0,
            'vname' => 'LBL_TP_IS_FISCAL_YEAR',
            'type' => 'bool',
        ],
        'leaf_cycle' => [
            'name' => 'leaf_cycle',
            'vname' => 'LBL_LEAF_CYCLE',
            'type' => 'int',
            'len' => '2',
            'studio' => false,
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TP_TYPE',
            'type' => 'enum',
            'options' => 'time_period_dom',
            'len' => '255',
            'audited' => true,
            'comment' => 'Time Period to be Forecast over',
            'merge_filter' => 'enabled',
            'importable' => 'required',
            'required' => true,
            'default' => '',
        ],
        'related_timeperiods' => [
            'name' => 'related_timeperiods',
            'type' => 'link',
            'relationship' => 'related_timeperiods',
            'link_type' => 'many',
            'side' => 'left',
            'source' => 'non-db',
        ],
    ],
    'acls' => ['SugarACLAdminOnly' => ['adminFor' => 'Forecasts', 'allowUserRead' => true]],
    'indices' => [
        ['name' => 'timeperiodspk', 'type' => 'primary', 'fields' => ['id'],],
        [
            'name' => 'idx_del_type_startdate_enddate',
            'type' => 'index',
            'fields' => [
                'deleted',
                'type',
                'start_date',
                'end_date',
            ],
        ],
    ],
    'relationships' => [
        'related_timeperiods' => [
            'lhs_module' => 'TimePeriods',
            'lhs_table' => 'timeperiods',
            'lhs_key' => 'id',
            'rhs_module' => 'TimePeriods',
            'rhs_table' => 'timeperiods',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ],


    ],
];
