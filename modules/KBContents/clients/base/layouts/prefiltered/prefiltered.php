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

$viewdefs['KBContents']['base']['layout']['prefiltered'] = [
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
                                    'view' => 'prefiltered-headerpane',
                                ],
                                [
                                    'layout' => [
                                        'type' => 'filterpanel',
                                        'availableToggles' => [],
                                        'filter_options' => [
                                            'stickiness' => false,
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
                                                'view' => 'prefilteredlist',
                                            ],
                                            [
                                                'view' => 'list-bottom',
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
