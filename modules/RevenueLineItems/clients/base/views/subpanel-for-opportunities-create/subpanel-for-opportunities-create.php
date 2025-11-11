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
$viewdefs['RevenueLineItems']['base']['view']['subpanel-for-opportunities-create'] = [
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn deleteBtn',
                'icon' => 'sicon-minus',
                'event' => 'list:deleterow:fire',
            ],
            [
                'type' => 'rowaction',
                'css_class' => 'btn addBtn',
                'icon' => 'sicon-plus',
                'event' => 'list:addrow:fire',
            ],
        ],
    ],
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
                'date_closed',
                [
                    'name' => 'worst_case',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                        'total_amount',
                        'quantity',
                        'discount_amount',
                        'discount_price',
                    ],
                    'showTransactionalAmount' => true,
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'likely_case',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                        'total_amount',
                        'quantity',
                        'discount_amount',
                        'discount_price',
                    ],
                    'showTransactionalAmount' => true,
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'best_case',
                    'type' => 'currency',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                        'total_amount',
                        'quantity',
                        'discount_amount',
                        'discount_price',
                    ],
                    'showTransactionalAmount' => true,
                    'convertToBase' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                'sales_stage',
                [
                    'name' => 'probability',
                    'readonly' => true,
                ],
                'commit_stage',
                [
                    'name' => 'product_template_name',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => [
                        'lock_duration',
                        'catalog_service_duration_unit',
                        'catalog_service_duration_value',
                    ],
                ],
                [
                    'name' => 'category_name',
                    'enabled' => true,
                    'default' => true,
                ],
                'quantity',
                [
                    'name' => 'discount_field',
                    'type' => 'fieldset',
                    'css_class' => 'discount-field',
                    'label' => 'LBL_DISCOUNT_AMOUNT',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                    'show_child_labels' => false,
                    'fields' => [
                        [
                            'name' => 'discount_amount',
                            'label' => 'LBL_DISCOUNT_AMOUNT',
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
                ],
                [
                    'name' => 'assigned_user_name',
                    'enabled' => true,
                    'default' => true,
                ],
                'service',
                'service_start_date' => [
                    'name' => 'service_start_date',
                    'label' => 'LBL_SERVICE_START_DATE',
                    'type' => 'date',
                ],
                'service_end_date' => [
                    'name' => 'service_end_date',
                    'label' => 'LBL_SERVICE_END_DATE',
                    'type' => 'service-enddate',
                ],
                [
                    'name' => 'service_duration',
                    'type' => 'fieldset',
                    'css_class' => 'service-duration-field',
                    'label' => 'LBL_SERVICE_DURATION',
                    'inline' => true,
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
                ],
                'renewable' => [
                    'name' => 'renewable',
                    'label' => 'LBL_RENEWABLE',
                    'type' => 'bool',
                ],
                'add_on_to_name' => [
                    'name' => 'add_on_to_name',
                    'type' => 'add-on-to',
                    'default' => false,
                    'related_fields' => [
                        'add_on_to_id',
                    ],
                ],
            ],
        ],
    ],
];
