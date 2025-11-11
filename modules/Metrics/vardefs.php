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
$dictionary['Metric'] = [
    'table' => 'metrics',
    'audited' => true,
    'duplicate_merge' => false,
    'hidden_to_role_assignment' => true,
    'fields' => [
        'metric_module' => [
            'required' => true,
            'name' => 'metric_module',
            'vname' => 'LBL_METRIC_MODULE',
            'type' => 'enum',
            'dbType' => 'varchar',
            'len' => 100,
            'audited' => false,
            'reportable' => false,
            'options' => 'moduleList',
            'massupdate' => false,
        ],
        'metric_context' => [
            'name' => 'metric_context',
            'vname' => 'LBL_METRIC_CONTEXT',
            'type' => 'enum',
            'dbType' => 'varchar',
            'len' => 100,
            'audited' => false,
            'reportable' => false,
            'options' => 'metric_context_list',
            'massupdate' => false,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'metric_status_dom',
            'len' => '255',
            'audited' => true,
            'massupdate' => false,
        ],
        'labels' => [
            'required' => false,
            'name' => 'labels',
            'vname' => 'LBL_LABELS',
            'type' => 'json',
            'dbType' => 'text',
            'audited' => false,
            'massupdate' => false,
        ],
        'viewdefs' => [
            'required' => false,
            'name' => 'viewdefs',
            'vname' => 'LBL_VIEWDEFS',
            'type' => 'json',
            'dbType' => 'text',
            'audited' => false,
            'massupdate' => false,
        ],
        'filter_def' => [
            'name' => 'filter_def',
            'vname' => 'LBL_METRIC_FILTER',
            'type' => 'json',
            'dbType' => 'text',
            'massupdate' => false,
        ],
        'order_by_primary' => [
            'required' => true,
            'name' => 'order_by_primary',
            'vname' => 'LBL_METRIC_SORT_ORDER_PRIMARY',
            'type' => 'enum',
            'options' => '',
            'massupdate' => false,
        ],
        'order_by_secondary' => [
            'name' => 'order_by_secondary',
            'vname' => 'LBL_METRIC_SORT_ORDER_SECONDARY',
            'type' => 'enum',
            'options' => '',
            'massupdate' => false,
        ],
        'order_by_primary_direction' => [
            'required' => true,
            'name' => 'order_by_primary_direction',
            'vname' => 'LBL_METRIC_SORT_ORDER_PRIMARY_DIRECTION',
            'type' => 'enum',
            'options' => '',
            'massupdate' => false,
        ],
        'order_by_secondary_direction' => [
            'name' => 'order_by_secondary_direction',
            'vname' => 'LBL_METRIC_SORT_ORDER_SECONDARY_DIRECTION',
            'type' => 'enum',
            'options' => '',
            'massupdate' => false,
        ],
        'freeze_first_column' => [
            'name' => 'freeze_first_column',
            'vname' => 'LBL_FREEZE_FIRST_COLUMN',
            'type' => 'bool',
            'default' => true,
            'comment' => 'Decides if the first column should be frozen',
        ],
    ],
    'relationships' => [
    ],
    'indices' => [
        [
            'name' => 'user_module_context',
            'type' => 'index',
            'fields' => ['assigned_user_id', 'metric_module', 'metric_context'],
        ],
    ],
    'ignore_templates' => [
        'commentlog',
        'lockable_fields',
    ],
    'uses' => [
        'basic',
        'assignable',
        'team_security',
    ],
    'acls' => [
        'SugarACLOwnerWrite' => true,
    ],
];

VardefManager::createVardef('Metrics', 'Metric');

$dictionary['Metric']['fields']['name']['audited'] = true;
