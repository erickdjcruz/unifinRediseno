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
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_SCHEDULED_MEETINGS',
                                'display_columns' => [
                                    'date_start',
                                    'name',
                                    'parent_name',
                                ],
                                'filter_id' => 'my_scheduled_meetings',
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'commentlog-dashlet',
                                'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_CONTACT_GUESTS',
                                'module' => 'Meetings',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts',
                                        'module' => 'Contacts',
                                        'limit' => 5,
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
                                    ],
                                ],
                                'tab_list' => [
                                    'contacts',
                                ],
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_LEAD_GUESTS',
                                'module' => 'Meetings',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'leads',
                                        'module' => 'Leads',
                                        'limit' => 5,
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
                                    ],
                                ],
                                'tab_list' => [
                                    'leads',
                                ],
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_MEETINGS_RECORD_DASHBOARD',
];
