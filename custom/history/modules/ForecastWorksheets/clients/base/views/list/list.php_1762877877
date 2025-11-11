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

$viewdefs['ForecastWorksheets']['base']['view']['list'] = [
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_1',
                'fields' => [
                    [
                        'name' => 'commit_stage',
                        'type' => 'enum',
                        'searchBarThreshold' => 7,
                        'label' => 'LBL_FORECAST',
                        'sortable' => false,
                        'default' => true,
                        'enabled' => true,
                        'click_to_edit' => true,
                    ],
                    [
                        'name' => 'parent_name',
                        'label' => 'LBL_OPPORTUNITY_NAME',
                        'link' => true,
                        'id' => 'parent_id',
                        'sortable' => true,
                        'default' => true,
                        'enabled' => true,
                        'type' => 'parent',
                        'readonly' => true,
                        'related_fields' => [
                            'parent_id',
                            'parent_type',
                            'parent_deleted',
                            'name',
                        ],
                    ],
                    [
                        'name' => 'account_name',
                    ],
                    [
                        'name' => 'date_closed',
                        'label' => 'LBL_DATE_CLOSED',
                        'sortable' => true,
                        'default' => true,
                        'enabled' => true,
                        'type' => 'date',
                        'view' => 'detail',
                        'click_to_edit' => true,
                        'related_fields' => [
                            'date_closed_timestamp',
                        ],
                    ],
                    [
                        'name' => 'sales_stage',
                        'label' => 'LBL_SALES_STAGE',
                        'type' => 'enum',
                        'options' => 'sales_stage_dom',
                        'searchBarThreshold' => 7,
                        'sortable' => false,
                        'default' => true,
                        'enabled' => true,
                        'click_to_edit' => true,
                        'related_fields' => [
                            'probability',
                        ],
                    ],
                    [
                        'name' => 'probability',
                        'label' => 'LBL_OW_PROBABILITY',
                        'type' => 'int',
                        'default' => true,
                        'enabled' => true,
                        'maxValue' => 100,
                        'minValue' => 0,
                        'align' => 'right',
                        'related_fields' => [
                            'sales_stage',
                        ],
                    ],
                    [
                        'name' => 'likely_case',
                        'label' => 'LBL_LIKELY',
                        'type' => 'currency',
                        'default' => true,
                        'enabled' => true,
                        'convertToBase' => true,
                        'showTransactionalAmount' => true,
                        'skip_preferred_conversion' => true,
                        'align' => 'right',
                        'click_to_edit' => true,
                        'related_fields' => [
                            'base_rate',
                            'currency_id',
                            'best_case',
                            'worst_case',
                        ],
                    ],
                ],
            ],
    ],
];
