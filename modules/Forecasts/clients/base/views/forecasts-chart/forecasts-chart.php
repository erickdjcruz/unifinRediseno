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
$viewdefs['Forecasts']['base']['view']['forecasts-chart'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_FORECASTS_CHART_NAME',
            'description' => 'LBL_DASHLET_FORECASTS_CHART_DESC',
            'config' => [
                'module' => 'Forecasts',
            ],
            'preview' => [],
            'filter' => [
                'module' => [
                    'Forecasts',
                ],
            ],
        ],
    ],
    'chart' => [
        'name' => 'paretoChart',
        'label' => 'Pareto Chart',
        'type' => 'forecast-pareto-chart',
    ],
    'timeperiod' => [
        [
            'name' => 'selectedTimePeriod',
            'label' => 'TimePeriod',
            'type' => 'enum',
            'default' => true,
            'enabled' => true,
            'view' => 'edit',
        ],
    ],
    'group_by' => [
        'name' => 'group_by',
        'label' => 'LBL_DASHLET_FORECASTS_GROUPBY',
        'type' => 'enum',
        'searchBarThreshold' => 5,
        'default' => true,
        'enabled' => true,
        'view' => 'edit',
        'options' => 'forecasts_chart_options_group',
    ],
    'dataset' => [
        'name' => 'dataset',
        'label' => 'LBL_DASHLET_FORECASTS_DATASET',
        'type' => 'enum',
        'searchBarThreshold' => 5,
        'default' => true,
        'enabled' => true,
        'view' => 'edit',
        'options' => 'forecasts_options_dataset',
    ],
];
