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
$vardefs = [
    'fields' => [
        'first_response_target_datetime' => [
            'name' => 'first_response_target_datetime',
            'vname' => 'LBL_FIRST_RESPONSE_TARGET_DATETIME',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'readonly' => true,
        ],
        'first_response_actual_datetime' => [
            'name' => 'first_response_actual_datetime',
            'vname' => 'LBL_FIRST_RESPONSE_ACTUAL_DATETIME',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'readonly' => true,
        ],
        'hours_to_first_response' => [
            'name' => 'hours_to_first_response',
            'vname' => 'LBL_HOURS_TO_FIRST_RESPONSE',
            'type' => 'decimal',
            'len' => '12',
            'precision' => '2',
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'readonly' => true,
        ],
        'business_hrs_to_first_response' => [
            'name' => 'business_hrs_to_first_response',
            'vname' => 'LBL_BUSINESS_HOURS_TO_FIRST_RESPONSE',
            'type' => 'decimal',
            'len' => '12',
            'precision' => '2',
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'readonly' => true,
        ],
        'first_response_var_from_target' => [
            'name' => 'first_response_var_from_target',
            'vname' => 'LBL_FIRST_RESPONSE_VARIANCE_FROM_TARGET',
            'type' => 'decimal',
            'len' => '12',
            'precision' => '2',
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'readonly' => true,
        ],
        'first_response_sla_met' => [
            'name' => 'first_response_sla_met',
            'vname' => 'LBL_FIRST_RESPONSE_SLA_MET',
            'type' => 'enum',
            'options' => 'first_response_met_sla_dom',
            'len' => 100,
            'audited' => false,
            'reportable' => true,
            'massupdate' => false,
            'processes' => true,
        ],
        'first_response_user_id' => [
            'name' => 'first_response_user_id',
            'vname' => 'LBL_FIRST_RESPONSE_USER_ID',
            'group' => 'first_response_user_name',
            'type' => 'id',
            'reportable' => false,
            'audited' => false,
            'massupdate' => false,
        ],
        'first_response_user_name' => [
            'name' => 'first_response_user_name',
            'link' => 'first_response_user_link',
            'vname' => 'LBL_FIRST_RESPONSE_USER_NAME',
            'rname' => 'full_name',
            'type' => 'relate',
            'reportable' => true,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'first_response_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'readonly' => true,
            'related_fields' => [
                'first_response_user_id',
            ],
        ],
        'first_response_user_link' => [
            'name' => 'first_response_user_link',
            'type' => 'link',
            'relationship' => strtolower($module) . '_first_response_user',
            'vname' => 'LBL_FIRST_RESPONSE_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'id_name' => 'first_response_user_id',
            'table' => 'users',
            'side' => 'right',
        ],
        'first_response_sent' => [
            'name' => 'first_response_sent',
            'vname' => 'LBL_FIRST_RESPONSE_SENT',
            'type' => 'bool',
            'audited' => true,
            'reportable' => true,
            'massupdate' => false,
            'processes' => true,
        ],
    ],
    'indices' => [
        'first_response_user_id' => [
            'name' => 'idx_' . strtolower($table_name) . '_first_response_del',
            'type' => 'index',
            'fields' => ['first_response_user_id', 'deleted'],
        ],
    ],
    'relationships' => [
        strtolower($module) . '_first_response_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => $module,
            'rhs_table' => strtolower($table_name),
            'rhs_key' => 'first_response_user_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
];
