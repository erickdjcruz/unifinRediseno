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
$module_name = '<module_name>';
$_object_name = '<_object_name>';
$viewdefs[$module_name]['EditView'] = [
    'templateMeta' => [
        'form' => ['buttons' => ['SAVE', 'CANCEL']],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'includes' => [
            ['file' => 'modules/Accounts/Account.js'],
        ],
    ],

    'panels' => [
        'lbl_account_information' => [
            ['name', 'phone_office'],
            ['website', 'phone_fax'],
            ['ticker_symbol', 'phone_alternate'],
            ['rating', 'employees'],
            ['ownership', 'industry'],

            [$_object_name . '_type', 'annual_revenue'],
            ['service_level'],
            [
                ['name' => 'team_name', 'displayParams' => ['display' => true]],
                '',
            ],
            ['assigned_user_name'],
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

        'lbl_email_addresses' => [
            ['email1'],
        ],

        'lbl_description_information' => [
            ['description'],
        ],

    ],
];
