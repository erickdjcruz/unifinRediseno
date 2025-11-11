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

$viewdefs['BusinessCenters']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'address_city',
                    'label' => 'LBL_ADDRESS_CITY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'address_country',
                    'label' => 'LBL_ADDRESS_COUNTRY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'timezone',
                    'label' => 'LBL_TIMEZONE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'address_state',
                    'label' => 'LBL_ADDRESS_STATE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'address_postalcode',
                    'label' => 'LBL_ADDRESS_POSTALCODE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_entered',
                    'type' => 'datetime',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
