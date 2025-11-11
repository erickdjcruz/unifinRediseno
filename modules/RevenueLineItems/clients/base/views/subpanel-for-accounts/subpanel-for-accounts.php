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

$viewdefs['RevenueLineItems']['base']['view']['subpanel-for-accounts'] = [
    'type' => 'subpanel-list',
    'favorite' => true,
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
                    'sortable' => false,
                    'enabled' => true,
                    'default' => true,
                ],
                'sales_stage',
                'probability',
                'commit_stage',
                'date_closed',
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
                    'name' => 'product_template_name',
                    'sortable' => false,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'category_name',
                    'sortable' => false,
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
                ],
                [
                    'name' => 'discount_field',
                    'type' => 'fieldset',
                    'css_class' => 'discount-field',
                    'label' => 'LBL_DISCOUNT_AMOUNT',
                    'enabled' => true,
                    'default' => true,
                    'show_child_labels' => false,
                    'sortable' => false,
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
                    'name' => 'total_amount',
                    'type' => 'currency',
                    'label' => 'LBL_CALCULATED_LINE_ITEM_AMOUNT',
                    'readonly' => true,
                    'related_fields' => [
                        'total_amount',
                        'currency_id',
                        'base_rate',
                    ],
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                ],
                [
                    'name' => 'assigned_user_name',
                    'sortable' => false,
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
            ],
        ],
    ],
    'selection' => [],
    'rowactions' => [
        'css_class' => 'pull-right',
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'icon' => 'sicon-edit',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
        ],
    ],
];
