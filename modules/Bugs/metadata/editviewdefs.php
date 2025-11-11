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
$viewdefs['Bugs']['EditView'] = [
    'templateMeta' => ['form' => ['hidden' => ['<input type="hidden" name="account_id" value="{$smarty.request.account_id}">',
        '<input type="hidden" name="contact_id" value="{$smarty.request.contact_id}">'],
    ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],


    'panels' => [
        'lbl_bug_information' => [

            [
                [
                    'name' => 'bug_number',
                    'type' => 'readonly',
                ],
            ],

            [
                ['name' => 'name', 'displayParams' => ['size' => 60, 'required' => true]],
            ],

            [
                'priority',
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
                'found_in_release',
                'fixed_in_release',
            ],

            [
                [
                    'name' => 'description',
                    'nl2br' => true,
                ],
            ],


            [
                [
                    'name' => 'work_log',
                    'nl2br' => true,
                ],
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

                'team_name',
            ],
        ],
    ],

];
