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

$viewdefs['Contacts']['base']['layout']['records'] = [
    'css_class' => 'flex-list-layout flex flex-col group/records h-full overflow-hidden',
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'css_class' => 'h-full',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8 flex flex-col overflow-hidden h-[calc(100%-55px)]',
                            'components' => [
                                [
                                    'view' => 'list-map',
                                ],
                                [
                                    'view' => 'list-headerpane',
                                ],
                                [
                                    'layout' => [
                                        'css_class' => 'flex flex-col h-full',
                                        'type' => 'filterpanel',
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
                                                'layout' => 'list',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'tabbed-layout',
                            'name' => 'dashboard-pane',
                            'label' => 'LBL_DASHBOARD',
                            'css_class' => 'dashboard-pane',
                            'notabs' => true,
                            'components' => [
                                [
                                    'layout' => [
                                        'type' => 'base',
                                        'label' => 'LBL_DASHBOARD',
                                        'css_class' => 'dashboard-pane',
                                        'components' => [
                                            [
                                                'layout' => [
                                                    'label' => 'LBL_DASHBOARD',
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
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'tabbed-layout',
                            'name' => 'preview-pane',
                            'label' => 'LBL_PREVIEW',
                            'css_class' => 'preview-pane',
                            'notabs' => true,
                            'components' => [
                                [
                                    'layout' => 'preview',
                                    'xmeta' => [
                                        'editable' => true,
                                    ],
                                ],
                                [
                                    'layout' => 'preview',
                                    'label' => 'Hint-Tab',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
