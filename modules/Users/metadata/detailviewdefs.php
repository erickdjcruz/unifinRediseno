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
$viewdefs['Users']['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'headerTpl' => 'modules/Users/tpls/DetailViewHeader.tpl',
            'footerTpl' => 'modules/Users/tpls/DetailViewFooter.tpl',
        ],
    ],
    'panels' => [
        'LBL_USER_INFORMATION' => [
            ['full_name', 'user_name'],
            ['status',
                [
                    'name' => 'UserType',
                    'customCode' => '{$USER_TYPE_READONLY}',
                ],
            ],
            ['picture',
                [
                    'name' => 'license_type',
                    'customCode' => '{$LICENSE_TYPE_READONLY}',
                ],
            ],
        ],
        'LBL_EMPLOYEE_INFORMATION' => [
            ['employee_status', 'show_on_employees'],
            ['title', 'phone_work'],
            ['department', 'phone_mobile'],
            ['reports_to_name', 'phone_other'],
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
