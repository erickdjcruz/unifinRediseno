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
                'width' => 12,
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Bugs',
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
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'Bugs',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'accounts',
                                        'module' => 'Accounts',
                                        'limit' => 5,
                                        'fields' => [
                                            'name',
                                            'billing_address_city',
                                            'billing_address_country',
                                            'phone_office',
                                            'assigned_user_name',
                                            'email',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'cases',
                                        'module' => 'Cases',
                                        'limit' => 5,
                                        'fields' => [
                                            'case_number',
                                            'name',
                                            'account_name',
                                            'priority',
                                            'status',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                            'team_name',
                                            'primary_contact_name',
                                            'request_close',
                                            'request_close_date',
                                            'business_center_name',
                                            'service_level',
                                            'follow_up_datetime',
                                            'first_response_sla_met',
                                        ],
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'tasks',
                                        'module' => 'Tasks',
                                        'limit' => 5,
                                        'fields' => [
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
                                ],
                                'tab_list' => [
                                    'accounts',
                                    'cases',
                                    'tasks',
                                ],
                            ],
                            'context' => [
                                'module' => 'Bugs',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'history',
                                'label' => 'LBL_HISTORY_DASHLET',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_BUGS_RECORD_DASHBOARD',
    'id' => '5d6724f4-7b52-11e9-a725-f218983a1c3e',
];
