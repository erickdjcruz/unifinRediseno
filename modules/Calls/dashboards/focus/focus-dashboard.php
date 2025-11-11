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
    'name' => 'LBL_CALLS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64ec0e8-13cb-11eb-b348-acde48001122',
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
                                'module' => 'Calls',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Calls',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Calls',
                            ],
                            'width' => 6,
                            'height' => 16,
                            'x' => 0,
                            'y' => 0,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_SCHEDULED_CALLS',
                                'display_columns' => [
                                    'date_start',
                                    'name',
                                    'parent_name',
                                ],
                                'filter_id' => 'my_scheduled_calls',
                            ],
                            'context' => [
                                'module' => 'Calls',
                            ],
                            'width' => 6,
                            'height' => 8,
                            'x' => 6,
                            'y' => 0,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_EXTERNAL_GUESTS',
                                'module' => 'Calls',
                                'tabs' => [
                                    [
                                        'type' => 'list',
                                        'module' => 'Contacts',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts',
                                        'fields' => [
                                            'name',
                                            'title',
                                            'account_name',
                                            'email',
                                            'phone_work',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                    [
                                        'type' => 'list',
                                        'module' => 'Leads',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'leads',
                                        'fields' => [
                                            'name',
                                            'status',
                                            'account_name',
                                            'phone_work',
                                            'email',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                ],
                                'tab_list' => [
                                    'contacts',
                                    'leads',
                                ],
                            ],
                            'context' => [
                                'module' => 'Calls',
                            ],
                            'width' => 6,
                            'height' => 8,
                            'x' => 6,
                            'y' => 8,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
