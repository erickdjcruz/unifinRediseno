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
$viewdefs['ForecastManagerWorksheets']['base']['view']['selection-list'] = [
    'css_class' => 'forecast-manager-worksheet',
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'userLink',
                    'label' => 'LBL_NAME',
                    'link' => true,
                    'route' => [
                        'recordID' => 'user_id',
                    ],
                    'sortable' => true,
                    'default' => true,
                    'enabled' => true,
                    'related_fields' => [
                        'user_id',
                        'is_manager',
                        'show_history_log',
                        'draft',
                        'pipeline_opp_count',
                        'pipeline_amount',
                        'closed_amount',
                        'opp_count',
                        'timeperiod_id',
                    ],
                ],
                [
                    'name' => 'quota',
                    'type' => 'currency',
                    'label' => 'LBL_QUOTA_ADJUSTED',
                    'sortable' => true,
                    'default' => true,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                    'click_to_edit' => true,
                ],
                [
                    'name' => 'worst_case',
                    'type' => 'currency',
                    'label' => 'LBL_WORST',
                    'sortable' => true,
                    'default' => true,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                ],
                [
                    'name' => 'worst_case_adjusted',
                    'type' => 'currency',
                    'label' => 'LBL_WORST_ADJUSTED',
                    'sortable' => true,
                    'default' => true,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                    'click_to_edit' => true,
                ],
                [
                    'name' => 'likely_case',
                    'type' => 'currency',
                    'label' => 'LBL_LIKELY',
                    'sortable' => true,
                    'default' => false,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                ],
                [
                    'name' => 'likely_case_adjusted',
                    'type' => 'currency',
                    'label' => 'LBL_LIKELY_ADJUSTED',
                    'sortable' => true,
                    'default' => false,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                    'click_to_edit' => true,
                ],
                [
                    'name' => 'best_case',
                    'type' => 'currency',
                    'label' => 'LBL_BEST',
                    'sortable' => true,
                    'default' => false,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                ],
                [
                    'name' => 'best_case_adjusted',
                    'type' => 'currency',
                    'label' => 'LBL_BEST_ADJUSTED',
                    'sortable' => true,
                    'default' => false,
                    'enabled' => true,
                    'convertToBase' => true,
                    'related_fields' => [
                        'base_rate',
                        'currency_id',
                    ],
                    'align' => 'right',
                    'click_to_edit' => true,
                ],
            ],
        ],
    ],
];
