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

$module_name = '<module_name>';
$_object_name = '<_object_name>';
$viewdefs[$module_name]['DetailView'] = [
    'templateMeta' => ['form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES']],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        ['name', 'phone_office'],
        [['name' => 'website', 'type' => 'link'], 'phone_fax'],
        ['ticker_symbol', ['name' => 'phone_alternate', 'label' => 'LBL_OTHER_PHONE']],
        ['', 'employees'],
        ['ownership', 'rating'],
        ['industry'],
        [$_object_name . '_type', 'annual_revenue'],
        ['service_level'],
        [
            ['name' => 'date_modified', 'label' => 'LBL_DATE_MODIFIED', 'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}'],
            'team_name',
        ],
        [['name' => 'assigned_user_name', 'label' => 'LBL_ASSIGNED_TO_NAME'],
            ['name' => 'date_entered', 'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}']],
        [
            [
                'name' => 'billing_address_street',
                'label' => 'LBL_BILLING_ADDRESS',
                'type' => 'address',
                'displayParams' => ['key' => 'billing'],
            ],
            [
                'name' => 'shipping_address_street',
                'label' => 'LBL_SHIPPING_ADDRESS',
                'type' => 'address',
                'displayParams' => ['key' => 'shipping'],
            ],
        ],

        ['description'],
        ['email1'],
    ],


];
