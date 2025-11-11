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
return [
    'metadata' => [
        'dashlets' => [
            [
                'view' => [
                    'type' => 'activity-timeline',
                    'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                ],
                'context' => [
                    'module' => 'Tasks',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'label' => 'LBL_MY_TASKS',
                    'type' => 'dashablelist',
                    'module' => 'Tasks',
                    'intelligent' => '0',
                    'limit' => 10,
                    'filter_id' => 'assigned_to_me',
                    'display_columns' => [
                        'name',
                        'contact_name',
                        'parent_name',
                        'date_due',
                        'team_name',
                        'assigned_user_name',
                        'date_start',
                        'status',
                        'date_modified',
                        'date_entered',
                    ],
                ],
                'context' => [
                    'module' => 'Tasks',
                ],
                'width' => 12,
                'height' => 4,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'dashablerecord',
                    'label' => 'LBL_RELATED_CONTACT',
                    'module' => 'Tasks',
                    'tabs' => [
                        [
                            'active' => true,
                            'label' => 'LBL_MODULE_NAME_SINGULAR',
                            'link' => 'contacts',
                            'module' => 'Contacts',
                        ],
                    ],
                    'tab_list' => [
                        'contacts',
                    ],
                    'base_module' => 'Tasks',
                ],
                'context' => [
                    'module' => 'Tasks',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            [
                'view' => [
                    'type' => 'commentlog-dashlet',
                    'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 12,
            ],
        ],
    ],
    'name' => 'LBL_TASKS_RECORD_DASHBOARD',
];
