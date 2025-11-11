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
$viewdefs['ProductTemplates']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'type_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'category_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'active_status',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'qty_in_stock',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'cost_price',
                    'type' => 'currency',
                    'related_fields' => [
                        'cost_usdollar',
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
                [
                    'name' => 'list_price',
                    'type' => 'currency',
                    'related_fields' => [
                        'list_usdollar',
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
                [
                    'name' => 'discount_price',
                    'type' => 'currency',
                    'related_fields' => [
                        'discount_usdollar',
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
            ],
        ],
    ],
];
