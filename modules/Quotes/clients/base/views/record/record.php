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
$viewdefs['Quotes']['base']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
            'acl_action' => 'edit',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'module' => 'Quotes',
                    'type' => 'convert-to-opportunity',
                    'event' => 'button:convert_to_opportunity:click',
                    'name' => 'convert_to_opportunity_button',
                    'label' => 'LBL_QUOTE_TO_OPPORTUNITY_LABEL',
                    'acl_module' => 'Opportunities',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'Quotes',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:historical_summary_button:click',
                    'name' => 'historical_summary_button',
                    'label' => 'LBL_HISTORICAL_SUMMARY',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
                    'acl_action' => 'delete',
                ],
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
            'label' => 'LBL_PANEL_HEADER',
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
                    'events' => [
                        'keyup' => 'update:quote',
                    ],
                    'related_fields' => [
                        [
                            'name' => 'bundles',
                            //Probably don't need ALL these...
                            'fields' => [
                                'id',
                                'bundle_stage',
                                'currency_id',
                                'base_rate',
                                'currencies',
                                'name',
                                'deal_tot',
                                'deal_tot_usdollar',
                                'deal_tot_discount_percentage',
                                'new_sub',
                                'new_sub_usdollar',
                                'position',
                                'related_records',
                                'shipping',
                                'shipping_usdollar',
                                'subtotal',
                                'subtotal_usdollar',
                                'tax',
                                'tax_usdollar',
                                'taxrate_id',
                                'team_count',
                                'team_count_link',
                                'team_name',
                                'taxable_subtotal',
                                'total',
                                'total_usdollar',
                                'default_group',
                                [
                                    'name' => 'product_bundle_items',
                                    'fields' => [
                                        'account_id',
                                        'name',
                                        'quote_id',
                                        'description',
                                        'quantity',
                                        'product_template_name',
                                        'product_template_id',
                                        'deal_calc',
                                        'mft_part_num',
                                        'discount_price',
                                        'discount_amount',
                                        'tax',
                                        'tax_class',
                                        'subtotal',
                                        'position',
                                        'currency_id',
                                        'base_rate',
                                        'discount_select',
                                        'total_amount',
                                        'service',
                                        'service_start_date',
                                        'service_end_date',
                                        'renewable',
                                        'service_duration_value',
                                        'service_duration_unit',
                                        'catalog_service_duration_value',
                                        'catalog_service_duration_unit',
                                    ],
                                    'max_num' => -1,
                                ],
                            ],
                            'max_num' => -1,
                            'order_by' => 'position:asc',
                        ],
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'converted',
                    'label' => 'LBL_CONVERTED',
                    'type' => 'badge',
                    'readonly' => true,
                    'dismiss_label' => true,
                    'related_fields' => [
                        'opportunity_id',
                    ],
                    'badge_compare' => [
                        'comparison' => 'notEmpty',
                    ],
                    'badge_label_map' => [
                        'false' => 'LBL_NOT_CONVERTED',
                        'true' => 'LBL_CONVERTED',
                    ],
                    'css_class_map' => [
                        'false' => '',
                        'true' => 'label-success',
                    ],
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'quote_num',
                [
                    'name' => 'opportunity_name',
                    'related_fields' => [
                        'subtotal',
                        'discount',
                        'new_sub',
                        'tax',
                        'shipping',
                    ],
                ],
                'purchase_order_num',
                'quote_stage',
                'payment_terms',
                'date_quote_expected_closed',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'panel_shipping_body',
            'label' => 'LBL_SHIPPING_BODY',
            'panelDefault' => 'collapsed',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'billing_account_name',
                'shipping_account_name',
                'billing_contact_name',
                'shipping_contact_name',
                [
                    'name' => 'billing_address',
                    'type' => 'fieldset',
                    'css_class' => 'address',
                    'label' => 'LBL_BILLING_ADDRESS',
                    'fields' => [
                        [
                            'name' => 'billing_address_street',
                            'css_class' => 'address_street',
                            'placeholder' => 'LBL_BILLING_ADDRESS_STREET',
                        ],
                        [
                            'name' => 'billing_address_city',
                            'css_class' => 'address_city',
                            'placeholder' => 'LBL_BILLING_ADDRESS_CITY',
                        ],
                        [
                            'name' => 'billing_address_state',
                            'css_class' => 'address_state',
                            'placeholder' => 'LBL_BILLING_ADDRESS_STATE',
                        ],
                        [
                            'name' => 'billing_address_postalcode',
                            'css_class' => 'address_zip',
                            'placeholder' => 'LBL_BILLING_ADDRESS_POSTAL_CODE',
                        ],
                        [
                            'name' => 'billing_address_country',
                            'css_class' => 'address_country',
                            'placeholder' => 'LBL_BILLING_ADDRESS_COUNTRY',
                        ],
                    ],
                ],
                [
                    'name' => 'shipping_address',
                    'type' => 'fieldset',
                    'css_class' => 'address',
                    'label' => 'LBL_SHIPPING_ADDRESS',
                    'fields' => [
                        [
                            'name' => 'shipping_address_street',
                            'css_class' => 'address_street',
                            'placeholder' => 'LBL_SHIPPING_ADDRESS_STREET',
                        ],
                        [
                            'name' => 'shipping_address_city',
                            'css_class' => 'address_city',
                            'placeholder' => 'LBL_SHIPPING_ADDRESS_CITY',
                        ],
                        [
                            'name' => 'shipping_address_state',
                            'css_class' => 'address_state',
                            'placeholder' => 'LBL_SHIPPING_ADDRESS_STATE',
                        ],
                        [
                            'name' => 'shipping_address_postalcode',
                            'css_class' => 'address_zip',
                            'placeholder' => 'LBL_SHIPPING_ADDRESS_POSTAL_CODE',
                        ],
                        [
                            'name' => 'shipping_address_country',
                            'css_class' => 'address_country',
                            'placeholder' => 'LBL_SHIPPING_ADDRESS_COUNTRY',
                        ],
                        [
                            'name' => 'copy',
                            'label' => 'NTC_COPY_BILLING_ADDRESS',
                            'type' => 'copy',
                            'mapping' => [
                                'billing_account_name' => 'shipping_account_name',
                                'billing_account_id' => 'shipping_account_id',
                                'billing_address_street' => 'shipping_address_street',
                                'billing_address_city' => 'shipping_address_city',
                                'billing_address_state' => 'shipping_address_state',
                                'billing_address_postalcode' => 'shipping_address_postalcode',
                                'billing_address_country' => 'shipping_address_country',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_setting_body',
            'label' => 'LBL_QUOTESETTINGS',
            'panelDefault' => 'collapsed',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'currency_id',
                    'type' => 'currency-type-dropdown',
                    'label' => 'LBL_CURRENCY',
                    'related_fields' => [
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                ],
                [
                    'name' => 'taxrate_name',
                    'type' => 'taxrate',
                    'initial_filter' => 'active_taxrates',
                    'filter_populate' => [
                        'module' => ['TaxRates'],
                    ],
                    'populate_list' => [
                        'id' => 'taxrate_id',
                        'value' => 'taxrate_value',
                    ],
                ],
                'show_line_nums',
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'panelDefault' => 'collapsed',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'original_po_date',
                'date_quote_closed',
                'date_order_shipped',
                [
                    'name' => 'shipper_name',
                    'initial_filter' => 'active_shippers',
                    'filter_populate' => [
                        'module' => ['Shippers'],
                    ],
                ],
                [
                    'name' => 'geocode_status',
                    'licenseFilter' => ['MAPS'],
                ],
                'order_stage',
                'team_name',
                'assigned_user_name',
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'inline' => true,
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'inline' => true,
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
