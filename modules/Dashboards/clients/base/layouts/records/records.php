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
$viewdefs['Dashboards']['base']['layout']['records'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'default_hide' => '1',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8',
                            'components' => [
                                [
                                    'view' => 'list-headerpane',
                                ],
                                [
                                    'layout' => [
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
                            'type' => 'base',
                            'name' => 'help-pane',
                            'css_class' => 'help-pane',
                            'components' => [
                                [
                                    'layout' => 'help',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
