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

$viewdefs['PurchasedLineItems']['base']['view']['subpanel-list'] = [
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
                    'name' => 'purchase_name',
                    'enable' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_closed',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'product_template_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'revenue',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'total_amount',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'quantity',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'discount_price',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'discount_field',
                    'type' => 'fieldset',
                    'css_class' => 'discount-field',
                    'label' => 'LBL_DISCOUNT_AMOUNT_COMBINED',
                    'show_child_labels' => false,
                    'sortable' => false,
                    'fields' => [
                        [
                            'name' => 'discount_amount',
                            'label' => 'LBL_TOTAL_DISCOUNT_AMOUNT',
                            'type' => 'discount-amount',
                            'discountFieldName' => 'discount_select',
                            'related_fields' => [
                                'currency_id',
                            ],
                            'convertToBase' => true,
                            'base_rate_field' => 'base_rate',
                            'showTransactionalAmount' => true,
                        ],
                        [
                            'type' => 'discount-select',
                            'name' => 'discount_select',
                            'options' => [],
                        ],
                    ],
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'service',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'annual_revenue',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'service_start_date',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'service_end_date',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'service_duration',
                    'type' => 'fieldset',
                    'css_class' => 'service-duration-field',
                    'label' => 'LBL_SERVICE_DURATION',
                    'inline' => true,
                    'enabled' => true,
                    'default' => false,
                    'show_child_labels' => false,
                    'fields' => [
                        [
                            'name' => 'service_duration_value',
                            'label' => 'LBL_SERVICE_DURATION_VALUE',
                        ],
                        [
                            'name' => 'service_duration_unit',
                            'label' => 'LBL_SERVICE_DURATION_UNIT',
                        ],
                    ],
                    'orderBy' => 'service_duration_unit',
                    'related_fields' => [
                        'service_duration_value',
                        'service_duration_unit',
                    ],
                ],
                [
                    'name' => 'renewable',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_entered',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'team_name',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
