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
$viewdefs['RevenueLineItems']['base']['view']['resolve-conflicts-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'opportunity_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'account_name',
                    'readonly' => true,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'sales_stage',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'probability',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_closed',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'commit_stage',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'product_template_name',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'category_name',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'quantity',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'likely_case',
                    'required' => true,
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'best_case',
                    'required' => true,
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'worst_case',
                    'required' => true,
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'quote_name',
                    'label' => 'LBL_ASSOCIATED_QUOTE',
                    'related_fields' => ['quote_id'],
                    'readonly' => true,
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
