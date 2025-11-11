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
$viewdefs['Forecasts']['base']['view']['config-scenarios'] = [
    'label' => 'LBL_FORECASTS_CONFIG_BREADCRUMB_SCENARIOS',
    'panels' => [
        [
            'fields' => [
                [
                    'name' => 'show_worksheet_likely',
                    'type' => 'bool',
                    'label' => 'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_LIKELY',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'detail',
                ],
                [
                    'name' => 'show_worksheet_best',
                    'type' => 'bool',
                    'label' => 'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_BEST',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'forecastsWorksheet',
                ],
                [
                    'name' => 'show_worksheet_worst',
                    'type' => 'bool',
                    'label' => 'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_WORST',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'forecastsWorksheet',
                ],
            ],
        ],
    ],
];
