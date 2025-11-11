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
$viewdefs[$module_name]['QuickCreate'] = [
    'templateMeta' => [
        'form' => ['buttons' => ['SAVE', 'CANCEL']],
        'maxColumns' => '2',
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
            [['name' => 'name', 'displayParams' => ['required' => true]], 'assigned_user_name'],
            ['website',
                ['name' => 'team_name', 'displayParams' => ['display' => true]],
            ],
            ['industry', ['name' => 'phone_office']],
            [$_object_name . '_type', 'phone_fax'],
            ['annual_revenue', ''],
        ],
        'lbl_email_addresses' => [
            ['email1'],
        ],
    ],
];
