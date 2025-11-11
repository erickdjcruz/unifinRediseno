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

global $mod_strings;

$popupMeta = [
    'moduleMain' => 'Account',
    'varName' => 'ACCOUNT',
    'orderBy' => 'name',
    'whereClauses' => [
        'name' => 'accounts.name',
        'billing_address_city' => 'accounts.billing_address_city',
        'phone_office' => 'accounts.phone_office',
    ],
    'searchInputs' => ['name', 'billing_address_city', 'phone_office'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '40',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'link' => true,
            'default' => true,
        ],
        'BILLING_ADDRESS_STREET' => [
            'width' => '10',
            'label' => 'LBL_BILLING_ADDRESS_STREET',
            'default' => false,
        ],
        'BILLING_ADDRESS_CITY' => [
            'width' => '10',
            'label' => 'LBL_LIST_CITY',
            'default' => true,
        ],
        'BILLING_ADDRESS_STATE' => [
            'width' => '7',
            'label' => 'LBL_STATE',
            'default' => true,
        ],
        'BILLING_ADDRESS_COUNTRY' => [
            'width' => '10',
            'label' => 'LBL_COUNTRY',
            'default' => true,
        ],
        'BILLING_ADDRESS_POSTALCODE' => [
            'width' => '10',
            'label' => 'LBL_BILLING_ADDRESS_POSTALCODE',
            'default' => false,
        ],
        'SHIPPING_ADDRESS_STREET' => [
            'width' => '10',
            'label' => 'LBL_SHIPPING_ADDRESS_STREET',
            'default' => false,
        ],
        'SHIPPING_ADDRESS_CITY' => [
            'width' => '10',
            'label' => 'LBL_LIST_CITY',
            'default' => false,
        ],
        'SHIPPING_ADDRESS_STATE' => [
            'width' => '7',
            'label' => 'LBL_STATE',
            'default' => false,
        ],
        'SHIPPING_ADDRESS_COUNTRY' => [
            'width' => '10',
            'label' => 'LBL_COUNTRY',
            'default' => false,
        ],
        'SHIPPING_ADDRESS_POSTALCODE' => [
            'width' => '10',
            'label' => 'LBL_SHIPPING_ADDRESS_POSTALCODE',
            'default' => false,
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '2',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'default' => true,
        ],
        'PHONE_OFFICE' => [
            'width' => '10',
            'label' => 'LBL_LIST_PHONE',
            'default' => false,
        ],
    ],
    'searchdefs' => [
        'name',
        'billing_address_city',
        'billing_address_state',
        'billing_address_country',
        'email',
        [
            'name' => 'assigned_user_id',
            'label' => 'LBL_ASSIGNED_TO',
            'type' => 'enum',
            'function' => ['name' => 'get_user_array', 'params' => [false]],
        ],
    ],
];
