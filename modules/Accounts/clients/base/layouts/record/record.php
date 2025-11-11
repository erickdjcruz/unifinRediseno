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
$viewdefs['Accounts']['base']['layout']['record'] = [
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
                            'css_class' => 'main-pane overflow-scroll span8',
                            'components' => [
                                [
                                    'view' => 'record',
                                    'primary' => true,
                                ],
                                [
                                    'layout' => 'extra-info',
                                ],
                                [
                                    'layout' => [
                                        'type' => 'filterpanel',
                                        'css_class' => 'subpanels-container',
                                        'last_state' => [
                                            'id' => 'record-filterpanel',
                                            'defaults' => [
                                                'toggle-view' => 'subpanels',
                                            ],
                                        ],
                                        'refresh_button' => true,
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
                                                'xmeta' => [
                                                    'layoutType' => '',
                                                ],
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
