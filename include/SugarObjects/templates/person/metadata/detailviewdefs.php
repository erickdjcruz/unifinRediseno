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
$viewdefs[$module_name]['DetailView'] = [
    'templateMeta' => ['form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES',
    ],
    ],
        'useTabs' => true,
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [

        [

            [
                'name' => 'full_name',
                'label' => 'LBL_NAME',
            ],

            //'full_name',
            [
                'name' => 'phone_work',
            ],
        ],

        [
            'title',

            [
                'name' => 'phone_mobile',
            ],
        ],

        [
            'department',

            [
                'name' => 'phone_home',
                'label' => 'LBL_HOME_PHONE',
            ],
        ],

        [
            null,
            [
                'name' => 'phone_other',
                'label' => 'LBL_OTHER_PHONE',
            ],
        ],

        [
            [
                'name' => 'date_entered',
                'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                'label' => 'LBL_DATE_ENTERED',
            ],
            [
                'name' => 'phone_fax',
                'label' => 'LBL_FAX_PHONE',
            ],
        ],

        [
            [
                'name' => 'date_modified',
                'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                'label' => 'LBL_DATE_MODIFIED',
            ],
            'do_not_call',
        ],
        ['assigned_user_name', ''],

        [
            'team_name',
            'email1'],

        [
            [
                'name' => 'primary_address_street',
                'label' => 'LBL_PRIMARY_ADDRESS',
                'type' => 'address',
                'displayParams' => ['key' => 'primary'],
            ],
            [
                'name' => 'alt_address_street',
                'label' => 'LBL_ALT_ADDRESS',
                'type' => 'address',
                'displayParams' => ['key' => 'alt'],
            ],
        ],

        [
            'description',
        ],

    ],

];
