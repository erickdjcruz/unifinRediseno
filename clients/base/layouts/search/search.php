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

$viewdefs['base']['layout']['search'] = [
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
                            'css_class' => 'main-pane span8 overflow-y-auto',
                            'components' => [
                                [
                                    'view' => 'search-headerpane',
                                ],
                                [
                                    'view' => 'search-list',
                                    'primary' => true,
                                ],
                                [
                                    'view' => [
                                        'name' => 'list-bottom',
                                        'label' => 'LBL_SHOW_MORE_RESULTS',
                                        'css_class' => 'search-more-results',
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
                            'notabs' => false,
                            'components' => [
                                [
                                    'layout' => 'preview',
                                    'label' => 'preview',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
