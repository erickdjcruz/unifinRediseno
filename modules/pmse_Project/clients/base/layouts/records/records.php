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

$viewdefs['pmse_Project']['base']['layout']['records'] = [
    'css_class' => 'flex-list-layout flex flex-col group/records h-full overflow-hidden',
    'components' => [
        [
            'layout' => [
                'css_class' => 'h-full',
                'components' => [
                    [
                        'layout' => [
                            'css_class' => 'main-pane span8 flex flex-col overflow-hidden h-[calc(100%-55px)]',
                            'components' => [
                                [
                                    'view' => 'list-headerpane',
                                ],
                                [
                                    'layout' => [
                                        'css_class' => 'flex flex-col h-full',
                                        'type' => 'filterpanel',
                                        'span' => 12,
                                        'last_state' => [
                                            'id' => 'list-filterpanel',
                                            'defaults' => [
                                                'toggle-view' => 'list',
                                            ],
                                        ],
                                        'refresh_button' => true,
                                        'availableToggles' => [
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
                                                'targetEl' => '.filter',
                                                'loadModule' => 'Filters',
                                                'position' => 'prepend',
                                            ],
                                            [
                                                'view' => 'filter-rows',
                                                'targetEl' => '.filter-options',
                                            ],
                                            [
                                                'view' => 'filter-actions',
                                                'targetEl' => '.filter-options',
                                            ],
                                            [
                                                'layout' => 'activitystream',
                                                'context' => [
                                                    'module' => 'Activities',
                                                ],
                                            ],
                                            [
                                                'layout' => 'list',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'type' => 'simple',
                            'name' => 'main-pane',
                        ],
                    ],
                    [
                        'layout' => [
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
                            'type' => 'simple',
                            'name' => 'dashboard-pane',
                            'css_class' => 'dashboard-pane',
                        ],
                    ],
                    [
                        'layout' => [
                            'components' => [
                                [
                                    'layout' => 'preview',
                                ],
                            ],
                            'type' => 'simple',
                            'name' => 'preview-pane',
                            'css_class' => 'preview-pane',
                        ],
                    ],
                ],
                'type' => 'default',
                'name' => 'sidebar',
                'span' => 12,
            ],
        ],
    ],
    'type' => 'records',
    'name' => 'base',
    'span' => 12,
];
