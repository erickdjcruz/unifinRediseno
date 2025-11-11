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
$viewdefs['Users']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'headerTpl' => 'modules/Users/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/Users/tpls/EditViewFooter.tpl',
        ],
    ],
    'panels' => [
        'LBL_USER_INFORMATION' => [
            [
                [
                    'name' => 'user_name',
                    'displayParams' => ['required' => true],
                ],
                'first_name',
            ],
            [[
                'name' => 'status',
                'displayParams' => ['required' => true],
            ],
                'last_name'],
            [[
                'name' => 'UserType',
                'customCode' =>
                    '{if $IS_ADMIN && !$IDM_MODE_ENABLED}{$USER_TYPE_DROPDOWN}{else}{$USER_TYPE_READONLY}{/if}',
            ],
                [
                    'name' => 'license_type',
                    'displayParams' => ['required' => true],
                    'customCode' =>
                        '{if $IS_ADMIN_DEV && !$IDM_MODE_LC_LOCK}{$LICENSE_TYPE_DROPDOWN}{else}{$LICENSE_TYPE_READONLY}{/if}',
                ],
            ],

            ['picture'],
        ],
        'LBL_EMPLOYEE_INFORMATION' => [
            ['employee_status',
                'show_on_employees'],
            ['title',
                'phone_work'],
            ['department',
                'phone_mobile'],
            ['reports_to_name',
                'phone_other'],
            ['', 'phone_fax'],
            ['', 'phone_home'],
            ['business_center_name', ''],
            ['messenger_type', 'messenger_id'],
            ['address_street', 'address_city'],
            ['address_state', 'address_postalcode'],
            ['address_country'],
            ['description'],
        ],
    ],
];
