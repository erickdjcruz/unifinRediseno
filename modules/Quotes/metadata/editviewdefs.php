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
$viewdefs['Quotes']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => ['footerTpl' => 'modules/Quotes/tpls/EditViewFooter.tpl'],
    ],
    'panels' => [
        'lbl_quote_information' => [
            [
                'name',
                'opportunity_name',
            ],

            [
                ['name' => 'quote_num', 'type' => 'readonly', 'displayParams' => ['required' => false]],
                'quote_stage',
            ],

            [
                'purchase_order_num',
                'date_quote_expected_closed',
            ],

            [
                'payment_terms',
                'original_po_date',
            ],
            [
                'description',
            ],
        ],

        'lbl_bill_to' => [
            [
                ['name' => 'billing_account_name', 'displayParams' => ['key' => ['billing', 'shipping'], 'copy' => ['billing', 'shipping'], 'billingKey' => 'billing', 'shippingKey' => 'shipping', 'copyPhone' => false, 'call_back_function' => 'set_billing_return']],
                ['name' => 'shipping_account_name', 'displayParams' => ['key' => ['shipping'], 'copy' => ['shipping'], 'shippingKey' => 'shipping', 'copyPhone' => false, 'call_back_function' => 'set_shipping_return']],
            ],

            [
                ['name' => 'billing_contact_name', 'displayParams' => ['initial_filter' => '&account_id_advanced="+this.form.{$fields.billing_account_name.id_name}.value+"&account_name_advanced="+this.form.{$fields.billing_account_name.name}.value+"'],],
                ['name' => 'shipping_contact_name', 'displayParams' => ['initial_filter' => '&account_id_advanced="+this.form.{$fields.shipping_account_name.id_name}.value+"&account_name_advanced="+this.form.{$fields.shipping_account_name.name}.value+"'],],
            ],
        ],
        'lbl_address_information' => [
            [
                [
                    'name' => 'billing_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => ['key' => 'billing', 'rows' => 2, 'cols' => 30, 'maxlength' => 150],
                ],

                [
                    'name' => 'shipping_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => ['key' => 'shipping', 'copy' => 'billing', 'rows' => 2, 'cols' => 30, 'maxlength' => 150],
                ],
            ],
        ],

        'LBL_PANEL_ASSIGNMENT' => [
            [
                'assigned_user_name',

                ['name' => 'team_name', 'displayParams' => ['required' => true]],
            ],
        ],
    ],

];
