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
    'templateMeta' => ['form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES',]],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],

    'panels' => [

        [
            $_object_name . '_number',
            'assigned_user_name',
        ],

        [
            'priority',
            'team_name',
        ],

        [
            'resolution',
            'status',
        ],
        [
            'follow_up_datetime',
        ],
        [
            'resolved_datetime',
        ],
        [
            [
                'name' => 'date_entered',
                'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                'label' => 'LBL_DATE_ENTERED',
            ],
            [
                'name' => 'date_modified',
                'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                'label' => 'LBL_DATE_MODIFIED',
            ],
        ],

        [

            [
                'name' => 'name',
                'label' => 'LBL_SUBJECT',
            ],
        ],

        [
            'description',
        ],

        [
            'work_log',
        ],
    ],
];
