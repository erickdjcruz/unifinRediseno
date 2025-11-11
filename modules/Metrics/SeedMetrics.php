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

/**
 * Gets the OOTB metric definitions to be created
 * @return array
 */
function getMetricDefs()
{
    global $viewdefs;

    $curViewDefs = $viewdefs;
    $stockMetrics = [];

    // cases
    require 'modules/Cases/clients/base/views/multi-line-list/multi-line-list.php';
    $viewdefsJson = json_encode($viewdefs['Cases']);
    $stockMetrics[] = [
        'name' => 'My Cases',
        'metric_module' => 'Cases',
        'metric_context' => 'service_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'follow_up_datetime',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                'status' => [
                    '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
                ],
                '$owner' => '',
            ],
        ]),
    ];
    $stockMetrics[] = [
        'name' => "My Team's Cases",
        'metric_module' => 'Cases',
        'metric_context' => 'service_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'follow_up_datetime',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                'status' => [
                    '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
                ],
            ],
        ]),
    ];
    $stockMetrics[] = [
        'name' => 'All Unassigned',
        'metric_module' => 'Cases',
        'metric_context' => 'service_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'follow_up_datetime',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                'status' => [
                    '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
                ],
            ],
            [
                'assigned_user_id' => [
                    '$is_null' => 1,
                ],
            ],
        ]),
    ];
    $stockMetrics[] = [
        'name' => 'All Escalated',
        'metric_module' => 'Cases',
        'metric_context' => 'service_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'follow_up_datetime',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                'status' => [
                    '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
                ],
            ],
            [
                'is_escalated' => [
                    '$equals' => '1',
                ],
            ],
        ]),
    ];

    // accounts
    require 'modules/Accounts/clients/base/views/multi-line-list/multi-line-list.php';
    $viewdefsJson = json_encode($viewdefs['Accounts']);
    $stockMetrics[] = [
        'name' => 'My Accounts',
        'metric_module' => 'Accounts',
        'metric_context' => 'renewals_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'next_renewal_date',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                '$owner' => '',
            ],
        ]),
    ];

    // opportunities
    require 'modules/Opportunities/clients/base/views/multi-line-list/multi-line-list.php';
    $viewdefsJson = json_encode($viewdefs['Opportunities']);
    $stockMetrics[] = [
        'name' => 'My Opportunities',
        'metric_module' => 'Opportunities',
        'metric_context' => 'renewals_console',
        'status' => 'Active',
        'viewdefs' => $viewdefsJson,
        'order_by_primary' => 'date_closed',
        'order_by_primary_direction' => 'asc',
        'filter_def' => json_encode([
            [
                'sales_status' => [
                    '$not_in' => ['Closed Won', 'Closed Lost'],
                ],
                '$owner' => '',
            ],
        ]),
    ];

    // To restore the global variable
    $viewdefs = $curViewDefs;

    return $stockMetrics;
}

/**
 * Creates the OOTB Metrics
 */
function createDefaultMetrics()
{
    $defaulMetrics = getMetricDefs();

    foreach ($defaulMetrics as $metricDef) {
        $metric = BeanFactory::newBean('Metrics');
        $metric->name = $metricDef['name'];
        $metric->metric_module = $metricDef['metric_module'];
        $metric->metric_context = $metricDef['metric_context'];
        $metric->status = $metricDef['status'];
        $metric->viewdefs = $metricDef['viewdefs'];
        $metric->filter_def = $metricDef['filter_def'];
        $metric->order_by_primary = $metricDef['order_by_primary'];
        $metric->order_by_primary_direction = $metricDef['order_by_primary_direction'];
        $metric->freeze_first_column = 1;
        $metric->created_by = 1;
        $metric->modified_user_id = 1;
        $metric->assigned_user_id = 1;
        $metric->team_id = 1;
        $metric->team_set_id = 1;
        $metric->deleted = 0;
        $metric->save();
    }
}
