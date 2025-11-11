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

$viewdefs['Forecasts']['base']['layout']['records'] = [
    'css_class' => 'flex-list-layout flex flex-col group/records h-full overflow-hidden',
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'css_class' => 'forecasts-records h-full',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane forecasts-main span8',
                            'components' => [
                                [
                                    'view' => 'list-headerpane',
                                ],
                                [
                                    'layout' => [
                                        'type' => 'tabbed-layout',
                                        'name' => 'tabbed-layout',
                                        'css_class' => 'h-full relative',
                                        'components' => [
                                            [
                                                'layout' => [
                                                    'css_class' => 'flex flex-col h-full',
                                                    'type' => 'filterpanel',
                                                    'last_state' => [
                                                        'id' => 'forecasts-list-filterpanel',
                                                        'defaults' => [
                                                            'toggle-view' => 'list',
                                                        ],
                                                    ],
                                                    'context' => [
                                                        'listViewModule' => 'Opportunities',
                                                        'loadModuleLabel' => true,
                                                    ],
                                                    'refresh_button' => true,
                                                    'label' => 'LBL_TAB_OPPORTUNITY',
                                                    'components' => [
                                                        [
                                                            'layout' => 'filter',
                                                            'loadModule' => 'Forecasts',
                                                            'context' => [
                                                                'module' => 'Opportunities',
                                                                'noCollectionField' => true,
                                                            ],
                                                        ],
                                                        [
                                                            'view' => 'filter-rows',
                                                        ],
                                                        [
                                                            'view' => 'filter-actions',
                                                        ],
                                                        [
                                                            'layout' => [
                                                                'name' => 'forecast-metrics',
                                                                'type' => 'base',
                                                                'css_class' => 'forecast-metrics m-4 border-solid border-b border-[--border-color]',
                                                                'components' => [
                                                                    [
                                                                        'view' => 'forecast-metrics',
                                                                    ],
                                                                    [
                                                                        'view' => 'forecast-metrics-help',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'layout' => 'list',
                                                            'context' => [
                                                                'module' => 'Opportunities',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            [
                                                'layout' => [
                                                    'name' => 'LBL_TAB_COMMITMENT',
                                                    'type' => 'base',
                                                    'module' => 'Forecasts',
                                                    'components' => [
                                                        [
                                                            'view' => 'commitment-headerpane',
                                                        ],
                                                        [
                                                            'view' => 'info',
                                                        ],
                                                        [
                                                            'layout' => 'list',
                                                            'context' => [
                                                                'module' => 'ForecastManagerWorksheets',
                                                            ],
                                                        ],
                                                        [
                                                            'layout' => 'list',
                                                            'context' => [
                                                                'module' => 'ForecastWorksheets',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'dashboard-pane',
                            'css_class' => 'dashboard-pane',
                            'components' => [
                                [
                                    'layout' => [
                                        'type' => 'dashboard',
                                        'last_state' => [
                                            'id' => 'last-visit',
                                        ],
                                    ],
                                    'context' => [
                                        'forceNew' => true,
                                        'module' => 'Home',
                                    ],
                                    'loadModule' => 'Dashboards',
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'preview-pane',
                            'css_class' => 'preview-pane',
                            'components' => [
                                [
                                    'layout' => 'preview',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
