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
$_module_name = '<_module_name>';
$viewdefs[$module_name]['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_ACCOUNT_NAME',
                    'link' => true,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'billing_address_city',
                    'label' => 'LBL_CITY',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'phone_office',
                    'label' => 'LBL_PHONE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => $_module_name . '_type',
                    'label' => 'LBL_TYPE',
                    'enabled' => true,
                ],
                [
                    'name' => 'industry',
                    'label' => 'LBL_INDUSTRY',
                    'enabled' => true,
                ],
                [
                    'name' => 'annual_revenue',
                    'label' => 'LBL_ANNUAL_REVENUE',
                    'enabled' => true,
                ],
                [
                    'name' => 'phone_fax',
                    'label' => 'LBL_PHONE_FAX',
                    'enabled' => true,
                ],
                [
                    'name' => 'billing_address_street',
                    'label' => 'LBL_BILLING_ADDRESS_STREET',
                    'enabled' => true,
                ],
                [
                    'name' => 'billing_address_state',
                    'label' => 'LBL_BILLING_ADDRESS_STATE',
                    'enabled' => true,
                ],
                [
                    'name' => 'billing_address_postalcode',
                    'label' => 'LBL_BILLING_ADDRESS_POSTALCODE',
                    'enabled' => true,
                ],
                [
                    'name' => 'billing_address_country',
                    'label' => 'LBL_BILLING_ADDRESS_COUNTRY',
                    'enabled' => true,
                ],
                [
                    'name' => 'shipping_address_street',
                    'label' => 'LBL_SHIPPING_ADDRESS_STREET',
                    'enabled' => true,
                ],
                [
                    'name' => 'shipping_address_city',
                    'label' => 'LBL_SHIPPING_ADDRESS_CITY',
                    'enabled' => true,
                ],
                [
                    'name' => 'shipping_address_state',
                    'label' => 'LBL_SHIPPING_ADDRESS_STATE',
                    'enabled' => true,
                ],
                [
                    'name' => 'shipping_address_postalcode',
                    'label' => 'LBL_SHIPPING_ADDRESS_POSTALCODE',
                    'enabled' => true,
                ],
                [
                    'name' => 'shipping_address_country',
                    'label' => 'LBL_SHIPPING_ADDRESS_COUNTRY',
                    'enabled' => true,
                ],
                [
                    'name' => 'phone_alternate',
                    'label' => 'LBL_PHONE_ALT',
                    'enabled' => true,
                ],
                [
                    'name' => 'website',
                    'label' => 'LBL_WEBSITE',
                    'enabled' => true,
                ],
                [
                    'name' => 'ownership',
                    'label' => 'LBL_OWNERSHIP',
                    'enabled' => true,
                ],
                [
                    'name' => 'employees',
                    'label' => 'LBL_EMPLOYEES',
                    'enabled' => true,
                ],
                [
                    'name' => 'ticker_symbol',
                    'label' => 'LBL_TICKER_SYMBOL',
                    'enabled' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => true,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
