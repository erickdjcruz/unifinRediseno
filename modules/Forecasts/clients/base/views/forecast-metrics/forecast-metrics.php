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

$forecastSettings = Forecast::getSettings();
$forecastRange = $forecastSettings['forecast_ranges'] ?? 'show_binary';
$closedWonSalesStages = $forecastSettings['sales_stage_won'] ?? [Opportunity::STAGE_CLOSED_WON];
$closedLostSalesStages = $forecastSettings['sales_stage_lost'] ?? [Opportunity::STAGE_CLOSED_LOST];
$closedSalesStages = array_merge($closedWonSalesStages, $closedLostSalesStages);
$includedCommitStages = $forecastSettings['commit_stages_included'] ?? 'include';
$includeLabel = 'LBL_INCLUDED_PIPELINE_HELP';
$excludeLabel = 'LBL_EXCLUDED_PIPELINE_HELP';
$upsideLabel = 'LBL_UPSIDE_PIPELINE_HELP';
$commitStageDom = '';

if ($forecastRange === 'show_binary') {
    $commitStageDom = 'commit_stage_binary_dom';
} elseif ($forecastRange === 'show_buckets') {
    $commitStageDom = 'commit_stage_dom';
} elseif ($forecastRange === 'show_custom_buckets') {
    $includeLabel = 'LBL_INCLUDED_PIPELINE_HELP_CUSTOM_RANGE';
    $excludeLabel = 'LBL_EXCLUDED_PIPELINE_HELP_CUSTOM_RANGE';
}

$viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
    'name' => 'forecast_list',
    'label' => 'LBL_FORECAST',
    'helpText' => 'LBL_FORECAST_HELP',
    'type' => 'forecast-metric',
    'sumFields' => 'forecasted_likely',
    'isDefaultFilter' => true,
    'filter' => [
        [
            'commit_stage' => [
                '$in' => $includedCommitStages,
            ],
        ],
    ],
];

$viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
    'name' => 'included_pipeline',
    'label' => 'LBL_INCLUDED_PIPELINE',
    'helpText' => $includeLabel,
    'commitStageDom' => $commitStageDom,
    'commitStageDomOption' => 'include',
    'type' => 'forecast-metric',
    'sumFields' => 'forecasted_likely',
    'filter' => [
        [
            'commit_stage' => [
                '$in' => $includedCommitStages,
            ],
            'sales_stage' => [
                '$not_in' => $closedSalesStages,
            ],
        ],
    ],
];

//This metric should only be displayed for show_buckets forecasts
if ($forecastRange === 'show_buckets') {
    $viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
        'name' => 'upside_pipeline',
        'label' => 'LBL_UPSIDE_PIPELINE',
        'helpText' => $upsideLabel,
        'commitStageDom' => $commitStageDom,
        'commitStageDomOption' => 'upside',
        'type' => 'forecast-metric',
        'sumFields' => 'amount',
        'filter' => [
            [
                'commit_stage' => [
                    '$equals' => 'upside',
                ],
                'sales_stage' => [
                    '$not_in' => $closedSalesStages,
                ],
            ],
        ],
    ];

    $viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
        'name' => 'excluded_pipeline',
        'label' => 'LBL_EXCLUDED_PIPELINE',
        'helpText' => $excludeLabel,
        'commitStageDom' => $commitStageDom,
        'commitStageDomOption' => 'exclude',
        'type' => 'forecast-metric',
        'sumFields' => 'amount',
        'filter' => [
            [
                'commit_stage' => [
                    '$equals' => 'exclude',
                ],
                'sales_stage' => [
                    '$not_in' => $closedSalesStages,
                ],
            ],
        ],
    ];
} else {
    $viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
        'name' => 'excluded_pipeline',
        'label' => 'LBL_EXCLUDED_PIPELINE',
        'helpText' => $excludeLabel,
        'commitStageDom' => $commitStageDom,
        'commitStageDomOption' => 'exclude',
        'type' => 'forecast-metric',
        'sumFields' => 'amount',
        'filter' => [
            [
                'commit_stage' => [
                    '$not_in' => $includedCommitStages,
                ],
                'sales_stage' => [
                    '$not_in' => $closedSalesStages,
                ],
            ],
        ],
    ];
}


$viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
    'name' => 'won',
    'label' => 'LBL_WON',
    'helpText' => 'LBL_WON_HELP',
    'type' => 'forecast-metric',
    'sumFields' => 'amount',
    'filter' => [
        [
            'sales_stage' => [
                '$in' => $closedWonSalesStages,
            ],
        ],
    ],
];

$viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
    'name' => 'lost',
    'label' => 'LBL_LOST',
    'helpText' => 'LBL_LOST_HELP',
    'type' => 'forecast-metric',
    'sumFields' => 'lost',
    'filter' => [
        [
            'sales_stage' => [
                '$in' => $closedLostSalesStages,
            ],
        ],
    ],
];

$viewdefs['Forecasts']['base']['view']['forecast-metrics']['forecast-metrics'][] = [
    'name' => 'all',
    'label' => 'LBL_ALL',
    'helpText' => 'LBL_ALL_HELP',
    'type' => 'forecast-metric',
    'sumFields' => [
        'amount',
        'lost',
    ],
    'filter' => [],
];
