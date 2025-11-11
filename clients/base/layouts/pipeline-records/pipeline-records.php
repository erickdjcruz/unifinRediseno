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

$viewdefs['base']['layout']['pipeline-records'] = [
    'components' => [
        [
            'layout' => [
                'components' => [
                    [
                        'layout' => [
                            'components' => [
                                [
                                    'view' => 'pipeline-headerpane',
                                ],
                                [
                                    'layout' => [
                                        'type' => 'pipeline-filterpanel',
                                        'span' => 12,
                                        'last_state' => [
                                            'id' => 'list-filterpanel',
                                            'defaults' => [
                                                'toggle-view' => 'list',
                                            ],
                                        ],
                                        'refresh_button' => true,
                                        'css_class' => 'pipeline-refresh-btn',
                                        'availableToggles' => [
                                            [
                                                'name' => 'list',
                                                'icon' => 'sicon-list-view',
                                                'label' => 'LBL_LISTVIEW',
                                                'route' => 'list',
                                            ],
                                            [
                                                'name' => 'pipeline',
                                                'icon' => 'sicon-tile-view',
                                                'label' => 'LBL_PIPELINE_VIEW_BTN',
                                            ],
                                            [
                                                'name' => 'activitystream',
                                                'icon' => 'sicon-clock',
                                                'label' => 'LBL_ACTIVITY_STREAM',
                                            ],
                                        ],
                                        'components' => [
                                            [
                                                'layout' => [
                                                    'components' => [
                                                        [
                                                            'view' => 'filter-module-dropdown',
                                                        ],
                                                        [
                                                            'view' => 'filter-filter-dropdown',
                                                        ],
                                                        [
                                                            'view' => 'filter-quicksearch',
                                                        ],
                                                    ],
                                                    'type' => 'pipeline-filter',
                                                    'name' => 'filter',
                                                    'last_state' => [
                                                        'id' => 'filter',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'view' => 'filter-rows',
                                            ],
                                            [
                                                'view' => 'filter-actions',
                                            ],
                                            [
                                                'view' => 'sorting-dropdown',
                                            ],
                                            [
                                                'view' => 'pipeline-recordlist-content',
                                                'primary' => true,
                                            ],
                                            [
                                                'layout' => 'activitystream',
                                                'context' => [
                                                    'module' => 'Activities',
                                                ],
                                            ],
                                            [
                                                'layout' => [
                                                    'name' => 'side-drawer',
                                                    'type' => 'side-drawer',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'type' => 'simple',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span12 overflow-y-auto',
                            'span' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'type' => 'pipeline-records',
    'name' => 'base',
    'span' => 12,
];
