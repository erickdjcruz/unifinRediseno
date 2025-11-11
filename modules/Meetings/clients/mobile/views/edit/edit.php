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
$viewdefs['Meetings']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                'name',
                [
                    'name' => 'date',
                    'type' => 'fieldset',
                    'related_fields' => ['date_start', 'date_end'],
                    'label' => 'LBL_START_AND_END_DATE_DETAIL_VIEW',
                    'fields' => [
                        [
                            'name' => 'date_start',
                        ],
                        [
                            'name' => 'date_end',
                            'required' => true,
                            'readonly' => false,
                        ],
                    ],
                ],
                'status',
                [
                    'name' => 'reminder',
                    'type' => 'fieldset',
                    'orientation' => 'horizontal',
                    'related_fields' => ['reminder_checked', 'reminder_time'],
                    'label' => 'LBL_REMINDER',
                    'fields' => [
                        [
                            'name' => 'reminder_checked',
                        ],
                        [
                            'name' => 'reminder_time',
                            'type' => 'enum',
                            'options' => 'reminder_time_options',
                        ],
                    ],
                ],
                [
                    'name' => 'email_reminder',
                    'type' => 'fieldset',
                    'orientation' => 'horizontal',
                    'related_fields' => ['email_reminder_checked', 'email_reminder_time'],
                    'label' => 'LBL_EMAIL_REMINDER',
                    'fields' => [
                        [
                            'name' => 'email_reminder_checked',
                        ],
                        [
                            'name' => 'email_reminder_time',
                            'type' => 'enum',
                            'options' => 'reminder_time_options',
                        ],
                    ],
                ],
                'description',
                'tag',
                'parent_name',
                'assigned_user_name',
                'team_name',
            ],
        ],
    ],
];
