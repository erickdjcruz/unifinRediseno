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
$viewdefs['Products']['base']['view']['resolve-conflicts-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT_NAME',
                    'related_fields' => ['account_id'],
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'default' => true,
                ],
                [
                    'name' => 'quote_name',
                    'link' => true,
                    'label' => 'LBL_ASSOCIATED_QUOTE',
                    'related_fields' => ['quote_id'],
                    'enabled' => true,
                    'default' => true,
                ],
                'quantity',
                [
                    'name' => 'discount_price',
                    'type' => 'currency',
                    'related_fields' => [
                        'discount_price',
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'default' => false,
                ],
                [
                    'name' => 'cost_price',
                    'readonly' => true,
                    'type' => 'currency',
                    'related_fields' => [
                        'cost_price',
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'default' => false,
                ],
                [
                    'name' => 'discount_amount',
                    'type' => 'currency',
                    'related_fields' => [
                        'discount_amount',
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'default' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'default' => false,
                ],
            ],
        ],
    ],
];
