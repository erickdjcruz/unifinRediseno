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

$viewdefs['Quotes']['base']['view']['preview'] = [
    'templateMeta' => [
        'maxColumns' => 1,
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                'name',
                [
                    'name' => 'quote_stage',
                    'type' => 'event-status',
                    'enum_width' => 'auto',
                    'dropdown_width' => 'auto',
                    'dropdown_class' => 'select2-menu-only',
                    'container_class' => 'select2-menu-only',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'fields' => [
                'opportunity_name',
                'quote_num',
                'purchase_order_num',
                'date_quote_expected_closed',
                'payment_terms',
                'original_po_date',
                'billing_account_name',
                'billing_contact_name',
                [
                    'name' => 'billing_address_fieldset',
                    'inline' => false,
                    'type' => 'fieldset',
                    'label' => 'LBL_BILLING_ADDRESS_STREET',
                    'fields' => [
                        [
                            'name' => 'billing_address_street',
                            'placeholder' => 'LBL_STREET',
                        ],
                        [
                            'name' => 'billing_address_city',
                            'placeholder' => 'LBL_CITY',
                        ],
                        [
                            'name' => 'billing_address_state',
                            'placeholder' => 'LBL_STATE',
                        ],
                        [
                            'name' => 'billing_address_postalcode',
                            'placeholder' => 'LBL_POSTAL_CODE',
                        ],
                        [
                            'name' => 'billing_address_country',
                            'placeholder' => 'LBL_COUNTRY',
                        ],
                    ],
                ],
                [
                    'name' => 'deal_tot',
                    'label' => 'LBL_LIST_DEAL_TOT',
                    'css_class' => 'quote-totals-row-item',
                    'related_fields' => ['deal_tot_discount_percentage'],
                ],
                [
                    'name' => 'new_sub',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'tax',
                    'label' => 'LBL_TAX_TOTAL',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'shipping',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'total',
                    'label' => 'LBL_LIST_GRAND_TOTAL',
                    'css_class' => 'quote-totals-row-item',
                ],
                'shipping_account_name',
                'shipping_contact_name',
                [
                    'name' => 'shipping_address_fieldset',
                    'inline' => false,
                    'type' => 'fieldset',
                    'label' => 'LBL_SHIPPING_ADDRESS_STREET',
                    'fields' => [
                        [
                            'name' => 'shipping_address_street',
                            'placeholder' => 'LBL_STREET',
                        ],
                        [
                            'name' => 'shipping_address_city',
                            'placeholder' => 'LBL_CITY',
                        ],
                        [
                            'name' => 'shipping_address_state',
                            'placeholder' => 'LBL_STATE',
                        ],
                        [
                            'name' => 'shipping_address_postalcode',
                            'placeholder' => 'LBL_POSTAL_CODE',
                        ],
                        [
                            'name' => 'shipping_address_country',
                            'placeholder' => 'LBL_COUNTRY',
                        ],
                    ],
                ],
                'description',
                'tag',
            ],
        ],
        [
            'name' => 'panel_hidden',
            'hide' => true,
            'fields' => [
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
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
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
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
            ],
        ],
    ],
];
