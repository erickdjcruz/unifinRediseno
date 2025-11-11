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
$searchdefs['Employees'] = [
    'templateMeta' => ['maxColumns' => '3', 'maxColumnsBasic' => '4',
        'widths' => ['label' => '10', 'field' => '30'],
    ],
    'layout' => [
        'basic_search' => [
            ['name' => 'search_name', 'label' => 'LBL_NAME', 'type' => 'name'],
            ['name' => 'open_only_active_users', 'label' => 'LBL_ONLY_ACTIVE', 'type' => 'bool'],
        ],
        'advanced_search' => [
            'first_name',
            'last_name',
            'employee_status',
            'title',
            'phone' => [
                'name' => 'phone',
                'label' => 'LBL_ANY_PHONE',
                'type' => 'name',
                'default' => true,
                'width' => '10%',
            ],
            'department',
            'email' => [
                'name' => 'email',
                'label' => 'LBL_ANY_EMAIL',
                'type' => 'name',
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
            'address_state' => [
                'name' => 'address_state',
                'label' => 'LBL_STATE',
                'type' => 'name',
                'default' => true,
                'width' => '10%',
            ],
            'address_postalcode' => [
                'name' => 'address_postalcode',
                'label' => 'LBL_POSTAL_CODE',
                'type' => 'name',
                'default' => true,
                'width' => '10%',
            ],

            'address_country' => [
                'name' => 'address_country',
                'label' => 'LBL_COUNTRY',
                'type' => 'name',
                'default' => true,
                'width' => '10%',
            ],
        ],
    ],
];
