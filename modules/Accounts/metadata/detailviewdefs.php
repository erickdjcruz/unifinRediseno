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

$viewdefs['Accounts']['DetailView'] = [
    'templateMeta' => ['form' => ['buttons' => ['EDIT',
        'DUPLICATE',
        'DELETE',
        'FIND_DUPLICATES',
        'CONNECTOR',
    ]],
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
                    'comment' => 'Name of the Company',
                    'label' => 'LBL_NAME',
                    'displayParams' => [
                        'enableConnectors' => true,
                        'module' => 'Accounts',
                        'connectors' => [
                            0 => 'ext_rest_twitter',
                        ],
                    ],
                ],
                [
                    'name' => 'phone_office',
                    'comment' => 'The office phone number',
                    'label' => 'LBL_PHONE_OFFICE',
                ],
            ],

            [

                [
                    'name' => 'website',
                    'type' => 'link',
                    'label' => 'LBL_WEBSITE',
                    'displayParams' => [
                        'link_target' => '_blank',
                    ],
                ],
                [
                    'name' => 'phone_fax',
                    'comment' => 'The fax phone number of this company',
                    'label' => 'LBL_FAX',
                ],
            ],

            [
                [
                    'name' => 'service_level',
                    'comment' => 'Service level for this company',
                    'label' => 'LBL_SERVICE_LEVEL',
                ],
                'business_center_name',
            ],

            [
                [
                    'name' => 'billing_address_street',
                    'label' => 'LBL_BILLING_ADDRESS',
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'billing',
                    ],
                ],

                [
                    'name' => 'shipping_address_street',
                    'label' => 'LBL_SHIPPING_ADDRESS',
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'shipping',
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
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                ],
            ],
        ],
        'LBL_PANEL_ADVANCED' => [

            [
                [
                    'name' => 'account_type',
                    'comment' => 'The Company is of this type',
                    'label' => 'LBL_TYPE',
                ],
                [
                    'name' => 'industry',
                    'comment' => 'The company belongs in this industry',
                    'label' => 'LBL_INDUSTRY',
                ],
            ],

            [
                [
                    'name' => 'annual_revenue',
                    'comment' => 'Annual revenue for this company',
                    'label' => 'LBL_ANNUAL_REVENUE',
                ],
                [
                    'name' => 'employees',
                    'comment' => 'Number of employees, varchar to accomodate for both number (100) or range (50-100)',
                    'label' => 'LBL_EMPLOYEES',
                ],
            ],

            [
                [
                    'name' => 'sic_code',
                    'comment' => 'SIC code of the account',
                    'label' => 'LBL_SIC_CODE',
                ],
                [
                    'name' => 'ticker_symbol',
                    'comment' => 'The stock trading (ticker) symbol for the company',
                    'label' => 'LBL_TICKER_SYMBOL',
                ],
            ],

            [
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_MEMBER_OF',
                ],
                [
                    'name' => 'ownership',
                    'comment' => '',
                    'label' => 'LBL_OWNERSHIP',
                ],
            ],

            [
                'campaign_name',

                [
                    'name' => 'rating',
                    'comment' => 'An arbitrary rating for this company for use in comparisons with others',
                    'label' => 'LBL_RATING',
                ],
            ],
        ],
        'LBL_PANEL_ASSIGNMENT' => [
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                ],
            ],
            [
                'team_name',
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                ],
            ],
        ],
    ],
];
