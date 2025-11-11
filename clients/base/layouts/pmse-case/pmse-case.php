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


$viewdefs['base']['layout']['pmse-case'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8',
                            'components' => [
                                [
                                    'view' => 'pmse-case',
                                    'primary' => true,
                                ],
                                [
                                    'layout' => 'extra-info',
                                ],
                                [
                                    'layout' => [
                                        'type' => 'filterpanel',
                                        'last_state' => [
                                            'id' => 'record-filterpanel',
                                            'defaults' => [
                                                'toggle-view' => 'subpanels',
                                            ],
                                        ],
                                        'availableToggles' => [
                                            [
                                                'name' => 'subpanels',
                                                'icon' => 'sicon-list-view',
                                                'label' => 'LBL_DATA_VIEW',
                                            ],
                                            [
                                                'name' => 'list',
                                                'icon' => 'sicon-list-view',
                                                'label' => 'LBL_LISTVIEW',
                                            ],
                                            [
                                                'name' => 'activitystream',
                                                'icon' => 'sicon-clock',
                                                'label' => 'LBL_ACTIVITY_STREAM',
                                            ],
                                        ],
                                        'components' => [
                                            [
                                                'layout' => 'filter',
                                                'loadModule' => 'Filters',
                                            ],
                                            [
                                                'view' => 'filter-rows',
                                            ],
                                            [
                                                'view' => 'filter-actions',
                                            ],
                                            [
                                                'layout' => 'activitystream',
                                                'context' => [
                                                    'module' => 'Activities',
                                                ],
                                            ],
                                            [
                                                'layout' => 'subpanels',
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
                                    'loadModule' => 'Dashboards',
                                    'context' => [
                                        'forceNew' => true,
                                        'module' => 'Home',
                                    ],
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
