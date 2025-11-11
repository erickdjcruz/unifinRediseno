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
$dictionary['Quota'] = [
    'table' => 'quotas',
    'archive' => false,
    'audited' => true,
    'activity_enabled' => true,
    'favorites' => false,
    'fields' => [
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'required' => false,
            'isnull' => false,
            'reportable' => false,
            'dbType' => 'id',
            'importable' => 'required',
        ],
        'user_name' => [
            'name' => 'user_name',
            'vname' => 'LBL_USER_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
        ],
        'user_full_name' => [
            'name' => 'user_full_name',
            'vname' => 'LBL_USER_FULL_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
        ],
        'timeperiod_id' => [
            'name' => 'timeperiod_id',
            'vname' => 'LBL_TIMEPERIOD_ID',
            'type' => 'enum',
            'dbType' => 'id',
            'function' => 'getTimePeriodsDropDownForQuotas',
            'reportable' => true,
            'audited' => true,
        ],
        'quota_type' => [
            'name' => 'quota_type',
            'vname' => 'LBL_QUOTA_TYPE',
            'type' => 'enum',
            'len' => 100,
            'massupdate' => false,
            'options' => 'forecast_type_dom',
            'reportable' => false,
        ],
        'amount' => [
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'currency',
            'required' => true,
            'reportable' => true,
            'importable' => 'required',
            'audited' => true,
        ],
        'amount_base_currency' => [
            'name' => 'amount_base_currency',
            'vname' => 'LBL_AMOUNT_BASE_CURRENCY',
            'type' => 'currency',
            'required' => true,
            'reportable' => false,
            'studio' => false,
            'readonly' => true,
            'is_base_currency' => true,
            'related_fields' => [
                'currency_id',
                'base_rate',
            ],
            'formula' => 'ifElse(isNumeric($amount), currencyDivide($amount, $base_rate), "")',
            'calculated' => true,
            'enforced' => true,
        ],
        'committed' => [
            'name' => 'committed',
            'vname' => 'LBL_COMMITTED',
            'type' => 'bool',
            'default' => '0',
            'required' => false,
            'reportable' => false,
        ],
    ],
    'indices' => [
        ['name' => 'idx_quota_user_tp', 'type' => 'index', 'fields' => ['user_id', 'timeperiod_id']],
    ],
    'relationships' => [
        'quota_activities' => [
            'lhs_module' => 'Quotas',
            'lhs_table' => 'quotas',
            'lhs_key' => 'id',
            'rhs_module' => 'Activities',
            'rhs_table' => 'activities',
            'rhs_key' => 'id',
            'rhs_vname' => 'LBL_ACTIVITY_STREAM',
            'relationship_type' => 'many-to-many',
            'join_table' => 'activities_users',
            'join_key_lhs' => 'parent_id',
            'join_key_rhs' => 'activity_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Quotas',
        ],
    ],
];

VardefManager::createVardef(
    'Quotas',
    'Quota',
    [
        'default',
        'assignable',
        'currency',
    ]
);
