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

$mapsDefaultModulesData = [
    'Accounts' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'billing_address_city',
            'countryRegion' => 'billing_address_country',
            'addressLine' => 'billing_address_street',
            'postalCode' => 'billing_address_postalcode',
            'adminDistrict' => 'billing_address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Opportunities' => [
        'mappings' => [
            'mappings' => true,
        ],
        'mappingType' => 'relateRecord',
        'mappingRecord' => [
            'accounts' => [
                'label' => 'LBL_ACCOUNTS',
                'module' => 'Accounts',
                'rel' => 'accounts_opportunities',
            ],
        ],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Contacts' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'primary_address_city',
            'countryRegion' => 'primary_address_country',
            'addressLine' => 'primary_address_street',
            'postalCode' => 'primary_address_postalcode',
            'adminDistrict' => 'primary_address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Leads' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'primary_address_city',
            'countryRegion' => 'primary_address_country',
            'addressLine' => 'primary_address_street',
            'postalCode' => 'primary_address_postalcode',
            'adminDistrict' => 'primary_address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Quotes' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'billing_address_city',
            'countryRegion' => 'billing_address_country',
            'addressLine' => 'billing_address_street',
            'postalCode' => 'billing_address_postalcode',
            'adminDistrict' => 'billing_address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Prospects' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'primary_address_city',
            'countryRegion' => 'primary_address_country',
            'addressLine' => 'primary_address_street',
            'postalCode' => 'primary_address_postalcode',
            'adminDistrict' => 'primary_address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
    'Users' => [
        'mappings' => [
            'mappings' => true,
            'locality' => 'address_city',
            'countryRegion' => 'address_country',
            'addressLine' => 'address_street',
            'postalCode' => 'address_postalcode',
            'adminDistrict' => 'address_state',
        ],
        'mappingType' => 'moduleFields',
        'mappingRecord' => [],
        'settings' => [
            'autopopulate' => false,
        ],
        'subpanelConfig' => [],
    ],
];

$mapsDefaultModulesData['BusinessCenters'] = [
    'mappings' => [
        'mappings' => true,
        'locality' => 'address_city',
        'countryRegion' => 'address_country',
        'addressLine' => 'address_street',
        'postalCode' => 'address_postalcode',
        'adminDistrict' => 'address_state',
    ],
    'mappingType' => 'moduleFields',
    'mappingRecord' => [],
    'settings' => [
        'autopopulate' => false,
    ],
    'subpanelConfig' => [],
];

$mapsDefaultEnabledModules = json_encode(array_keys($mapsDefaultModulesData));
$mapsDefaultModulesData = json_encode($mapsDefaultModulesData);
$mapsDefaultLogLevel = 'fatal';
$mapsDefaultUnitType = 'miles';
