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
$searchdefs ['Leads'] =
    [
        'layout' => [
            'basic_search' => [
                0 =>
                    [
                        'name' => 'search_name',
                        'label' => 'LBL_NAME',
                        'type' => 'name',
                    ],
                1 =>
                    [
                        'name' => 'current_user_only',
                        'label' => 'LBL_CURRENT_USER_FILTER',
                        'type' => 'bool',
                    ],

                ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
                ['name' => 'open_only', 'label' => 'LBL_OPEN_ITEMS', 'type' => 'bool', 'default' => false, 'width' => '10%'],
            ],
            'advanced_search' => [
                'first_name' => [
                    'name' => 'first_name',
                    'default' => true,
                    'width' => '10%',
                ],
                'email' => [
                    'name' => 'email',
                    'label' => 'LBL_ANY_EMAIL',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'phone' => [
                    'name' => 'phone',
                    'label' => 'LBL_ANY_PHONE',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'last_name' => [
                    'name' => 'last_name',
                    'default' => true,
                    'width' => '10%',
                ],
                'address_street' => [
                    'name' => 'address_street',
                    'label' => 'LBL_ANY_ADDRESS',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'address_city' => [
                    'name' => 'address_city',
                    'label' => 'LBL_CITY',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'account_name' => [
                    'name' => 'account_name',
                    'default' => true,
                    'width' => '10%',
                ],
                'primary_address_country' => [
                    'name' => 'primary_address_country',
                    'label' => 'LBL_COUNTRY',
                    'type' => 'name',
                    'options' => 'countries_dom',
                    'default' => true,
                    'width' => '10%',
                ],
                'address_state' => [
                    'name' => 'address_state',
                    'label' => 'LBL_STATE',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'status' => [
                    'name' => 'status',
                    'default' => true,
                    'width' => '10%',
                ],
                'lead_source' => [
                    'name' => 'lead_source',
                    'default' => true,
                    'width' => '10%',
                ],
                'assigned_user_id' => [
                    'name' => 'assigned_user_id',
                    'type' => 'enum',
                    'label' => 'LBL_ASSIGNED_TO',
                    'function' => [
                        'name' => 'get_user_array',
                        'params' => [
                            0 => false,
                        ],
                    ],
                    'default' => true,
                    'width' => '10%',
                ],


                ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
            ],
        ],
        'templateMeta' => [
            'maxColumns' => '3',
            'maxColumnsBasic' => '4',
            'widths' => [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ];
