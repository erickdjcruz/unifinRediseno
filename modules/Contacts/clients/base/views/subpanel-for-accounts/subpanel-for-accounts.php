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
$viewdefs['Contacts']['base']['view']['subpanel-for-accounts'] = [
    'type' => 'subpanel-list',
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'fullname',
                    'fields' => [
                        'salutation',
                        'first_name',
                        'last_name',
                    ],
                    'link' => true,
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'primary_address_city',
                    'label' => 'LBL_LIST_CITY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'primary_address_state',
                    'label' => 'LBL_LIST_STATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'email',
                    'label' => 'LBL_LIST_EMAIL',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'phone_work',
                    'label' => 'LBL_LIST_PHONE',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
