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
    'name' => 'LBL_TASKS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f8d02-13cb-11eb-8e1f-acde48001122',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    // Row 1
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Tasks',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Tasks',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Tasks',
                            ],
                            'width' => 6,
                            'height' => 16,
                        ],
                        [
                            'context' => [
                                'module' => 'Tasks',
                            ],
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
                            'width' => 6,
                            'height' => 16,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
