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
                    'type' => 'dashablerecord',
                    'label' => 'LBL_DASHLET_RECORDVIEW_NAME',
                    'module' => 'ReportSchedules',
                    'tabs' => [
                        [
                            'active' => true,
                            'label' => 'LBL_MODULE_NAME_SINGULAR',
                            'link' => 'report',
                            'module' => 'Reports',
                        ],
                        [
                            'active' => false,
                            'label' => 'LBL_THIS_REPORT_SCHEDULE',
                            'link' => 'ReportSchedules',
                            'module' => 'ReportSchedules',
                        ],
                    ],
                    'tab_list' => [
                        'report',
                        'ReportSchedules',
                    ],
                    'base_module' => 'ReportSchedules',
                ],
                'context' => [
                    'module' => 'ReportSchedules',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'dashablelist',
                    'label' => 'LBL_MY_REPORTS',
                    'display_columns' => [
                        'name',
                        'module',
                        'report_type',
                        'assigned_user_name',
                        'last_run_date',
                        'date_entered',
                        'date_modified',
                        'tag',
                        'team_name',
                        'chart_type',
                    ],
                    'filter_id' => 'assigned_to_me',
                ],
                'context' => [
                    'module' => 'Reports',
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
    'name' => 'LBL_REPORT_SCHEDULES_RECORD_DASHBOARD',
];
