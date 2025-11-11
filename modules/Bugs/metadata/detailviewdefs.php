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
$viewdefs['Bugs']['DetailView'] = [
    'templateMeta' => ['form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES',]],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],

    'panels' => [
        'lbl_bug_information' => [
            [
                'bug_number',
                'priority',
            ],

            [
                [
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                ],
            ],

            [
                'status',
                'follow_up_datetime',
            ],
            [
                'type',
                'source',
            ],

            [
                'product_category',
                'resolution',
            ],

            [
                [
                    'name' => 'found_in_release',
                    'label' => 'LBL_FOUND_IN_RELEASE',
                ],
                'fixed_in_release',
            ],

            [
                'description',
            ],

            [
                'work_log',
            ],

            [
                ['name' => 'portal_viewable',
                    'label' => 'LBL_SHOW_IN_PORTAL',
                    'hideIf' => 'empty($PORTAL_ENABLED)',
                ],
            ],
        ],

        'LBL_PANEL_ASSIGNMENT' => [

            [

                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                ],

                [
                    'name' => 'date_modified',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                    'label' => 'LBL_DATE_MODIFIED',
                ],
            ],

            [
                'team_name',

                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                    'label' => 'LBL_DATE_ENTERED',
                ],

            ],
        ],
    ],
];
