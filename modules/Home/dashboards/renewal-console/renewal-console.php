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
        'css_class' => 'console_dashboard',
        'type' => 'renewals_console',
        'tabs' => [
            // TAB 1
            [
                'name' => 'LBL_RENEWALS_CONSOLE_OVERVIEW',
                'components' => [
                    [
                        'rows' => [
                            [
                                [
                                    'width' => 12,
                                    'view' => [
                                        'limit' => '10',
                                        'label' => 'LBL_PLANNED_ACTIVITIES_DASHLET',
                                        'type' => 'planned-activities',
                                        'template' => 'tabbed-dashlet',
                                    ],
                                ],
                            ],
                            [
                                [
                                    'width' => 12,
                                    'view' => [
                                        'limit' => '10',
                                        'type' => 'active-tasks',
                                        'name' => 'active-tasks',
                                        'label' => 'LBL_ACTIVE_TASKS_DASHLET',
                                        'template' => 'tabbed-dashlet',
                                    ],
                                ],
                            ],
                        ],
                        'width' => 4,
                    ],
                    [
                        'rows' => [
                            [
                                [
                                    'width' => 12,
                                    'view' => [
                                        'type' => 'sales-pipeline',
                                        'label' => 'LBL_DASHLET_PIPLINE_NAME',
                                        'visibility' => 'user',
                                    ],
                                ],
                            ],
                            [
                                [
                                    'width' => 12,
                                    'view' => [
                                        'type' => 'bubblechart',
                                        'label' => 'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME',
                                        'filter_duration' => 'current',
                                        'visibility' => 'user',
                                    ],
                                ],
                            ],
                        ],
                        'width' => 8,
                    ],
                ],
            ],
            // TAB 2
            [
                'name' => 'LBL_ACCOUNTS',
                'components' => [
                    [
                        'layout' => [
                            'name' => 'kpi-metrics',
                            'type' => 'base',
                            'css_class' => 'kpi-metrics flex border-b border-[--border-color]',
                            'metric_module' => 'Accounts',
                            'metric_context' => 'renewals_console',
                            'order_by_primary' => 'next_renewal_date',
                            'components' => [
                                [
                                    'context' => [
                                        'module' => 'Accounts',
                                    ],
                                    'layout' => 'kpi-metrics-tabs',
                                ],
                                [
                                    'context' => [
                                        'module' => 'Accounts',
                                    ],
                                    'view' => 'kpi-metrics-tools',
                                ],
                            ],
                        ],
                    ],
                    [
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'layout' => 'multi-line-filterpanel',
                    ],
                    [
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'view' => 'multi-line-list',
                    ],
                    [
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'view' => [
                            'name' => 'multi-line-list-pagination',
                            'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                        ],
                    ],
                ],
            ],
            // TAB 3
            [
                'name' => 'LBL_OPPORTUNITIES',
                'components' => [
                    [
                        'layout' => [
                            'name' => 'kpi-metrics',
                            'type' => 'base',
                            'css_class' => 'kpi-metrics flex border-b border-[--border-color]',
                            'metric_module' => 'Opportunities',
                            'metric_context' => 'renewals_console',
                            'order_by_primary' => 'date_closed',
                            'components' => [
                                [
                                    'context' => [
                                        'module' => 'Opportunities',
                                    ],
                                    'layout' => 'kpi-metrics-tabs',
                                ],
                                [
                                    'context' => [
                                        'module' => 'Opportunities',
                                    ],
                                    'view' => 'kpi-metrics-tools',
                                ],
                            ],
                        ],
                    ],
                    [
                        'context' => [
                            'module' => 'Opportunities',
                        ],
                        'layout' => 'multi-line-filterpanel',
                    ],
                    [
                        'context' => [
                            'module' => 'Opportunities',
                        ],
                        'view' => 'multi-line-list',
                    ],
                    [
                        'context' => [
                            'module' => 'Opportunities',
                        ],
                        'view' => [
                            'name' => 'multi-line-list-pagination',
                            'css_class' => 'flex-table-pagination',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_RENEWALS_CONSOLE',
    'id' => 'da438c86-df5e-11e9-9801-3c15c2c53980',
];
