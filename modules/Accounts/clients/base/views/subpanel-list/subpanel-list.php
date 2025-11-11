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
$viewdefs['Accounts']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'default' => true,
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'enabled' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'default' => true,
                    'label' => 'LBL_LIST_CITY',
                    'enabled' => true,
                    'name' => 'billing_address_city',
                ],
                [
                    'type' => 'varchar',
                    'default' => true,
                    'label' => 'LBL_BILLING_ADDRESS_COUNTRY',
                    'enabled' => true,
                    'name' => 'billing_address_country',
                ],
                [
                    'default' => true,
                    'label' => 'LBL_LIST_PHONE',
                    'enabled' => true,
                    'name' => 'phone_office',
                ],
            ],
        ],
    ],
];
