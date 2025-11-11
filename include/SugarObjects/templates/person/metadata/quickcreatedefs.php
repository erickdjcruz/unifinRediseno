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
$viewdefs[$module_name]['QuickCreate'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'lbl_contact_information' => [

            [
                [
                    'name' => 'first_name',
                    'customCode' => '{html_options name="salutation" options=$fields.salutation.options selected=$fields.salutation.value}&nbsp;<input name="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
                ],
                'assigned_user_name',
            ],

            [
                ['name' => 'last_name', 'displayParams' => ['required' => true]],
                ['name' => 'team_name', 'displayParams' => ['display' => true]],
            ],

            [
                'title',
                'phone_work',
            ],

            [
                'department',
                'phone_mobile',
            ],

            [
                'phone_fax',
                '',
            ],
        ],
        'lbl_email_addresses' => [
            ['email1'],
        ],

    ],


];
