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
$viewdefs['Products']['base']['view']['quote-data-group-list'] = [
    'panels' => [
        [
            'name' => 'products_quote_data_group_list',
            'label' => 'LBL_PRODUCTS_QUOTE_DATA_LIST',
            'fields' => [
                [
                    'name' => 'line_num',
                    'label' => null,
                    'widthClass' => 'cell-xsmall',
                    'css_class' => 'line_num text-center',
                    'type' => 'line-num',
                    'readonly' => true,
                ],
                [
                    'name' => 'quantity',
                    'label' => 'LBL_QUANTITY',
                    'labelModule' => 'Products',
                    'widthClass' => 'cell-small',
                    'css_class' => 'quantity',
                    'type' => 'float',
                ],
                [
                    'name' => 'product_template_name',
                    'label' => 'LBL_PRODUCT_TEMPLATE',
                    'labelModule' => 'Products',
                    'widthClass' => 'cell-large',
                    'type' => 'quote-data-relate',
                    'required' => true,
                    'related_fields' => [
                        'service',
                        'service_start_date',
                        'service_end_date',
                        'renewable',
                        'service_duration_value',
                        'service_duration_unit',
                    ],
                ],
                [
                    'name' => 'mft_part_num',
                    'label' => 'LBL_MFT_PART_NUM',
                    'labelModule' => 'Products',
                    'type' => 'base',
                ],
                [
                    'name' => 'discount_price',
                    'label' => 'LBL_DISCOUNT_PRICE',
                    'labelModule' => 'Products',
                    'type' => 'currency',
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                    'related_fields' => [
                        'discount_price',
                        'currency_id',
                        'base_rate',
                    ],
                ],
                [
                    'name' => 'discount_field',
                    'type' => 'fieldset',
                    'css_class' => 'discount-field quote-discount-percent',
                    'label' => 'LBL_DISCOUNT_AMOUNT',
                    'labelModule' => 'Products',
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
                    'label' => 'LBL_LINE_ITEM_TOTAL',
                    'labelModule' => 'Quotes',
                    'type' => 'currency',
                    'widthClass' => 'cell-medium',
                    'showTransactionalAmount' => true,
                    'related_fields' => [
                        'total_amount',
                        'currency_id',
                        'base_rate',
                    ],
                ],
            ],
        ],
    ],
];
