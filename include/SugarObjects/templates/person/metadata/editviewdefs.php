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
$viewdefs[$module_name]['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'useTabs' => true,
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
                    'customCode' => '{html_options name="salutation" id="salutation" options=$fields.salutation.options selected=$fields.salutation.value}'
                        . '&nbsp;<input name="first_name"  id="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
                ],
                'phone_work',
            ],

            [
                'last_name',
                'phone_mobile',
            ],

            [
                'title',
                'phone_home',
            ],

            [
                'department',
                'phone_other',
            ],

            [
                '',
                'phone_fax',
            ],

            [
                'assigned_user_name',
                'do_not_call',
            ],

            [
                ['name' => 'team_name', 'displayParams' => ['display' => true]],
                '',
            ],
            [
                'description',
            ],

        ],
        'lbl_email_addresses' => [
            ['email1'],
        ],
        'lbl_address_information' => [
            [
                [
                    'name' => 'primary_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => ['key' => 'primary', 'rows' => 2, 'cols' => 30, 'maxlength' => 150],
                ],

                [
                    'name' => 'alt_address_street',
                    'hideLabel' => true,
                    'type' => 'address',
                    'displayParams' => ['key' => 'alt', 'copy' => 'primary', 'rows' => 2, 'cols' => 30, 'maxlength' => 150],
                ],
            ],
        ],

    ],


];
