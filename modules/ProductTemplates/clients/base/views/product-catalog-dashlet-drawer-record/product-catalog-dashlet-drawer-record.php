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

$viewdefs['ProductTemplates']['base']['view']['product-catalog-dashlet-drawer-record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'view',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'button',
            'event' => 'button:add_to_quote_button:click',
            'name' => 'add_to_quote_button',
            'label' => 'LBL_ADD_TO_QUOTE_BUTTON',
            'css_class' => 'btn btn-primary',
            'showOn' => 'view',
            'showOnModules' => [
                'Quotes' => [
                    'create',
                    'record',
                ],
                'Opportunities' => [
                    'create',
                    'record',
                ],
            ],
            'events' => [
                'click' => 'button:add_to_quote_button:click',
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labels' => true,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                'status',
                [
                    'name' => 'website',
                    'type' => 'url'],
                'date_available',
                'tax_class',
                'qty_in_stock',
                'category_name',
                'manufacturer_name',
                'mft_part_num',
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
                'vendor_part_num',
                'weight',
                'type_name',
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
                    'default' => true,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
                'cost_usdollar',
                'date_cost_price',
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
                    'default' => true,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
                'discount_usdollar',
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
                    'default' => true,
                    'convertToBase' => true,
                    'showTransactionalAmount' => true,
                ],
                'list_usdollar',
                [
                    'name' => 'pricing_formula',
                    'related_fields' => [
                        'pricing_factor',
                    ],
                ],
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'support_name',
                'support_description',
                'support_contact',
                'support_term',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
    ],
];
