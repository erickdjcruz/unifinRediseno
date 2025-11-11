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
$viewdefs['Forecasts']['base']['view']['config-timeperiods'] = [
    'label' => 'LBL_FORECASTS_CONFIG_BREADCRUMB_TIMEPERIODS',
    'panels' => [
        [
            'fields' => [
                [
                    'name' => 'timeperiod_interval',
                    'type' => 'enum',
                    'options' => 'forecasts_timeperiod_options_dom',
                    'searchBarThreshold' => 5,
                    'label' => 'LBL_FORECASTS_CONFIG_TIMEPERIOD',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'edit',
                ],
                [
                    'name' => 'timeperiod_start_date',
                    'type' => 'date',
                    'label' => 'LBL_FORECASTS_CONFIG_START_DATE',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'detail',
                ],
                [
                    'name' => 'timeperiod_fiscal_year',
                    'type' => 'fiscal-year',
                    'options' => 'forecast_fiscal_year_options',
                    'label' => 'LBL_FORECASTS_CONFIG_TIMEPERIOD_FISCAL_YEAR',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'edit',
                ],
                [
                    'name' => 'timeperiod_shown_forward',
                    'type' => 'enum',
                    'options' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                    ],
                    'searchBarThreshold' => 5,
                    'label' => 'LBL_FORECASTS_CONFIG_TIMEPERIODS_FORWARD',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'edit',
                ],
                [
                    'name' => 'timeperiod_shown_backward',
                    'type' => 'enum',
                    'options' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                    ],
                    'searchBarThreshold' => 5,
                    'label' => 'LBL_FORECASTS_CONFIG_TIMEPERIODS_BACKWARD',
                    'default' => false,
                    'enabled' => true,
                    'view' => 'edit',
                ],
            ],
        ],
    ],
];
