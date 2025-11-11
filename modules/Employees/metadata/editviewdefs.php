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
$viewdefs['Employees']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'headerTpl' => 'modules/Employees/tpls/EditViewHeader.tpl',
        ],
    ],
    'panels' => [

        'default' => [
            [
                'employee_status',
                [
                    'name' => 'picture',
                    'label' => 'LBL_PICTURE_FILE',
                ],
            ],
            [
                'first_name',
                ['name' => 'last_name', 'displayParams' => ['required' => true]],
            ],
            [
                'title',
                ['name' => 'phone_work', 'label' => 'LBL_OFFICE_PHONE'],
            ],
            [
                'department',
                'phone_mobile',
            ],
            [
                'reports_to_name',
                'phone_other',
            ],
            [
                '',
                ['name' => 'phone_fax', 'label' => 'LBL_FAX'],
            ],
            [
                '',
                'phone_home',
            ],
            [
                'messenger_type',
            ],
            [
                'messenger_id',
            ],
            [
                ['name' => 'description', 'label' => 'LBL_NOTES'],
            ],
            [
                ['name' => 'address_street', 'type' => 'text', 'label' => 'LBL_PRIMARY_ADDRESS', 'displayParams' => ['rows' => 2, 'cols' => 30]],
                ['name' => 'address_city', 'label' => 'LBL_CITY'],
            ],
            [
                ['name' => 'address_state', 'label' => 'LBL_STATE'],
                ['name' => 'address_postalcode', 'label' => 'LBL_POSTAL_CODE'],
            ],
            [
                ['name' => 'address_country', 'label' => 'LBL_COUNTRY'],
            ],
            [
                [
                    'name' => 'email',
                    'label' => 'LBL_EMAIL',
                ],
            ],
        ],
    ],

];
