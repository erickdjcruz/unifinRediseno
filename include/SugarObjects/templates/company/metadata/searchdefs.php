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
$searchdefs[$module_name] = [
    'templateMeta' => [
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => ['label' => '10', 'field' => '30'],
    ],
    'layout' => [
        'basic_search' => [
            'name',
            ['name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
            ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
        ],
        'advanced_search' => [
            'name',
            ['name' => 'address_street', 'label' => 'LBL_ANY_ADDRESS', 'type' => 'name'],
            ['name' => 'phone', 'label' => 'LBL_ANY_PHONE', 'type' => 'name'],
            'website',
            ['name' => 'address_city', 'label' => 'LBL_CITY', 'type' => 'name'],
            ['name' => 'email', 'label' => 'LBL_ANY_EMAIL', 'type' => 'name'],
            'annual_revenue',
            ['name' => 'address_state', 'label' => 'LBL_STATE', 'type' => 'name'],
            'employees',
            'industry',
            ['name' => 'address_postalcode', 'label' => 'LBL_POSTAL_CODE', 'type' => 'name'],
            'ticker_symbol',
            $_module_name . '_type',
            ['name' => 'address_country', 'label' => 'LBL_COUNTRY', 'type' => 'name'],
            'rating',
            ['name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => ['name' => 'get_user_array', 'params' => [false]]],
            'ownership',
            ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
        ],
    ],
];
