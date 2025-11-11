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

$viewdefs['Accounts']['EditView'] = [
    'templateMeta' => [
        'form' => ['buttons' => ['SAVE', 'CANCEL']],
        'maxColumns' => '2',
        'useTabs' => true,
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'includes' => [
            ['file' => 'modules/Accounts/Account.js'],
        ],
    ],

    'panels' => [

        'lbl_account_information' => [
            [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'displayParams' => [
                        'required' => true,
                    ],
                ],
                [
                    'name' => 'phone_office',
                    'label' => 'LBL_PHONE_OFFICE',
                ],
            ],

            [

                [
                    'name' => 'website',
                    'type' => 'link',
                    'label' => 'LBL_WEBSITE',
                ],

                [
                    'name' => 'phone_fax',
                    'label' => 'LBL_FAX',
                ],
            ],

            [
                'service_level',
                'business_center_name',
            ],

            [

                [
                    'name' => 'billing_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'billing',
                        'rows' => 2,
                        'cols' => 30,
                        'maxlength' => 150,
                    ],
                ],

                [
                    'name' => 'shipping_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'shipping',
                        'copy' => 'billing',
                        'rows' => 2,
                        'cols' => 30,
                        'maxlength' => 150,
                    ],
                ],
            ],

            [

                [
                    'name' => 'email',
                    'studio' => 'false',
                    'label' => 'LBL_EMAIL',
                ],
            ],

            [

                [
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION',
                ],
            ],
        ],
        'LBL_PANEL_ADVANCED' => [

            [
                'account_type',
                'industry',
            ],

            [
                'annual_revenue',
                'employees',
            ],

            [
                'sic_code',
                'ticker_symbol',
            ],

            [
                'parent_name',
                'ownership',
            ],

            [
                'campaign_name',
                'rating',
            ],
        ],
        'LBL_PANEL_ASSIGNMENT' => [

            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ],
                [
                    'name' => 'team_name', 'displayParams' => ['display' => true],
                ],
            ],
        ],
    ],
];
