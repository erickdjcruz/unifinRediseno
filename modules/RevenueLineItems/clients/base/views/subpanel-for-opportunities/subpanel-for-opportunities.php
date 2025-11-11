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

$viewdefs['RevenueLineItems']['base']['view']['subpanel-for-opportunities'] = [
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
                    'related_fields' => [
                        'mft_part_num',
                    ],
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
                    'name' => 'product_template_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'category_name',
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
                'add_on_to_name' => [
                    'name' => 'add_on_to_name',
                    'type' => 'add-on-to',
                    'default' => false,
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
                    'related_fields' => [
                        'renewal',
                    ],
                ],
                [
                    'name' => 'forecasted_likely',
                    'comment' => 'Rollup of included RLIs on the Opportunity',
                    'readonly' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'label' => 'LBL_FORECASTED_LIKELY',
                    'span' => 6,
                ],
                [
                    'name' => 'commit_stage',
                    'type' => 'enum',
                    'disable_field' => 'closed_won_revenue_line_items',
                    'disable_positive' => true,
                    'related_fields' => [
                        0 => 'probability',
                        1 => 'closed_won_revenue_line_items',
                    ],
                    'span' => 6,
                ],
            ],
        ],
    ],
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'quote_button',
                'type' => 'button',
                'label' => 'LBL_GENERATE_QUOTE',
                'primary' => true,
                'events' => [
                    'click' => 'list:massquote:fire',
                ],
                'acl_module' => 'Quotes',
                'acl_action' => 'create',
                'related_fields' => [
                    'account_id',
                    'account_name',
                    'assigned_user_id',
                    'assigned_user_name',
                    'base_rate',
                    'best_case',
                    'book_value',
                    'category_id',
                    'category_name',
                    'commit_stage',
                    'cost_price',
                    'currency_id',
                    'date_closed',
                    'deal_calc',
                    'likely_case',
                    'list_price',
                    'mft_part_num',
                    'my_favorite',
                    'name',
                    'probability',
                    'product_template_id',
                    'product_template_name',
                    'quote_id',
                    'quote_name',
                    'worst_case',
                    'quantity',
                ],
            ],
            [
                'name' => 'massdelete_button',
                'type' => 'button',
                'label' => 'LBL_DELETE',
                'acl_action' => 'delete',
                'primary' => true,
                'events' => [
                    'click' => 'list:massdelete:fire',
                ],
                'related_fields' => ['sales_stage'],
            ],
        ],
    ],
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
