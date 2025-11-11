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
        'css_class' => 'agent_workbench_dashboard',
        'tabs' => [
            // TAB 1
            [
                'name' => 'LBL_AGENT_WORKBENCH_OVERVIEW',
                'components' => [[
                    'rows' => [
                        [
                            [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_135',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Cases',
                                    'saved_report_id' => '87e0c5c4-beae-11ee-9d94-095590d26ca4',
                                ],
                            ], [
                            'width' => 4,
                            'view' => [
                                'limit' => '10',
                                'date' => 'today',
                                'visibility' => 'user',
                                'label' => 'LBL_PLANNED_ACTIVITIES_DASHLET',
                                'type' => 'planned-activities',
                                'module' => null,
                                'template' => 'tabbed-dashlet',
                            ],
                            ], [
                            'width' => 4,
                            'view' => [
                                'limit' => 10,
                                'visibility' => 'user',
                                'label' => 'LBL_ACTIVE_TASKS_DASHLET',
                                'type' => 'active-tasks',
                                'module' => null,
                                'template' => 'tabbed-dashlet',
                            ],
                            ],
                        ], [
                            [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_137',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Cases',
                                    'saved_report_id' => '67f4d7d2-beae-11ee-9d94-095590d26ca4',
                                    'chart_type' => 'pie chart',
                                ],
                            ], [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                ],
                                'view' => [
                                    'label' => 'LBL_RECENTLY_VIEWED_CASES_DASHLET',
                                    'type' => 'dashablelist',
                                    'module' => 'Cases',
                                    'last_state' => [
                                        'id' => 'dashable-list',
                                    ],
                                    'intelligent' => '0',
                                    'limit' => 10,
                                    'filter_id' => 'recently_viewed',
                                    'display_columns' => [
                                        'case_number',
                                        'name',
                                        'account_name',
                                        'priority',
                                        'status',
                                        'assigned_user_name',
                                        'date_modified',
                                        'date_entered',
                                        'team_name',
                                        'business_center_name',
                                        'service_level',
                                        'follow_up_datetime',
                                    ],
                                ],
                            ], [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_138',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Cases',
                                    'saved_report_id' => '490c8d6a-beae-11ee-9d94-095590d26ca4',
                                    'chart_type' => 'horizontal group by chart',
                                ],
                            ],
                        ], [
                            [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                    'link' => null,
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_12',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Cases',
                                    'saved_report_id' => '3b90b1dc-beab-11ee-9d94-095590d26ca4',
                                    'chart_type' => 'horizontal group by chart',
                                ],
                            ], [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Cases',
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_132',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Cases',
                                    'saved_report_id' => 'aee89246-beae-11ee-9d94-095590d26ca4',
                                ],
                            ], [
                                'width' => 4,
                                'context' => [
                                    'module' => 'Tasks',
                                ],
                                'view' => [
                                    'label' => 'LBL_REPORT_DASHLET_TITLE_139',
                                    'type' => 'saved-reports-chart',
                                    'module' => 'Tasks',
                                    'saved_report_id' => '0da8f498-beae-11ee-9d94-095590d26ca4',
                                ],
                            ],
                        ],
                    ],
                    'width' => 12,
                ]],
            ],
            // TAB 2
            [
                'name' => 'LBL_CASES',
                'components' => [
                    [
                        'layout' => [
                            'name' => 'kpi-metrics',
                            'type' => 'base',
                            'css_class' => 'kpi-metrics flex border-b border-[--border-color]',
                            'metric_module' => 'Cases',
                            'metric_context' => 'service_console',
                            'order_by_primary' => 'follow_up_datetime',
                            'badges' => [
                                [
                                    'type' => 'record-count',
                                    'module' => 'Cases',
                                    'filter' => [
                                        [
                                            'follow_up_datetime' => [
                                                '$lt' => '$nowTime',
                                            ],
                                        ],
                                    ],
                                    'cssClass' => 'case-expired',
                                    'tooltip' => 'LBL_CASE_OVERDUE',
                                ],
                                [
                                    'type' => 'record-count',
                                    'module' => 'Cases',
                                    'filter' => [
                                        [
                                            'follow_up_datetime' => [
                                                '$between' => ['$nowTime', '$tomorrowTime'],
                                            ],
                                        ],
                                    ],
                                    'cssClass' => 'case-soon',
                                    'tooltip' => 'LBL_CASE_DUE_SOON',
                                ],
                                [
                                    'type' => 'record-count',
                                    'module' => 'Cases',
                                    'filter' => [
                                        [
                                            'follow_up_datetime' => [
                                                '$gt' => '$tomorrowTime',
                                            ],
                                        ],
                                    ],
                                    'cssClass' => 'case-future',
                                    'tooltip' => 'LBL_CASE_DUE_LATER',
                                ],
                            ],
                            'components' => [
                                [
                                    'context' => [
                                        'module' => 'Cases',
                                    ],
                                    'layout' => 'kpi-metrics-tabs',
                                ],
                                [
                                    'context' => [
                                        'module' => 'Cases',
                                    ],
                                    'view' => 'kpi-metrics-tools',
                                ],
                            ],
                        ],
                    ],
                    [
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'layout' => 'multi-line-filterpanel',
                    ],
                    [
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'view' => 'multi-line-list',
                    ],
                    [
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'view' => [
                            'name' => 'multi-line-list-pagination',
                            'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_AGENT_WORKBENCH',
    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
];
