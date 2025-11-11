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
$viewdefs[$module_name]['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        $_module_name . '_type' => [],
        'industry' => [],
        'annual_revenue' => [],
        'address_street' => [
            'dbFields' => [
                'billing_address_street',
                'shipping_address_street',
            ],
            'vname' => 'LBL_STREET',
            'type' => 'text',
        ],
        'address_city' => [
            'dbFields' => [
                'billing_address_city',
                'shipping_address_city',
            ],
            'vname' => 'LBL_CITY',
            'type' => 'text',
        ],
        'address_state' => [
            'dbFields' => [
                'billing_address_state',
                'shipping_address_state',
            ],
            'vname' => 'LBL_STATE',
            'type' => 'text',
        ],
        'address_postalcode' => [
            'dbFields' => [
                'billing_address_postalcode',
                'shipping_address_postalcode',
            ],
            'vname' => 'LBL_POSTAL_CODE',
            'type' => 'text',
        ],
        'address_country' => [
            'dbFields' => [
                'billing_address_country',
                'shipping_address_country',
            ],
            'vname' => 'LBL_COUNTRY',
            'type' => 'text',
        ],
        'rating' => [],
        'phone_office' => [],
        'website' => [],
        'ownership' => [],
        'employees' => [],
        'ticker_symbol' => [],
        'email' => [],
        'tag' => [],
        'assigned_user_name' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
    ],
];
