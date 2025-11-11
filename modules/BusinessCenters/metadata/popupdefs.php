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

$popupMeta = [
    'moduleMain' => 'BusinessCenter',
    'varName' => 'BUSINESS_CENTER',
    'orderBy' => 'business_centers.name',
    'whereClauses' => [
        'name' => 'business_centers.name',
        'address_city' => 'business_centers.address_city',
        'address_country' => 'business_centers.address_country',
    ],
    'searchInputs' => [
        0 => 'name',
        1 => 'address_city',
        2 => 'address_country',
    ],
    'listviewdefs' => [
        'name' => [
            'type' => 'name',
            'label' => 'LBL_NAME',
            'width' => 10,
            'default' => true,
        ],
        'address_city' => [
            'type' => 'varchar',
            'label' => 'LBL_ADDRESS_CITY',
            'width' => 10,
            'default' => true,
        ],
        'address_country' => [
            'type' => 'varchar',
            'label' => 'LBL_ADDRESS_COUNTRY',
            'width' => 10,
            'default' => true,
        ],
        'timezone' => [
            'type' => 'enum',
            'label' => 'LBL_TIMEZONE',
            'width' => 10,
            'default' => true,
        ],
    ],
];
