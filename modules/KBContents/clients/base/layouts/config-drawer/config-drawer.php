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
$viewdefs['KBContents']['base']['layout']['config-drawer'] = [
    'type' => 'config-drawer',
    'components' => [
        [
            'layout' => [
                'components' => [
                    [
                        'layout' => [
                            'components' => [
                                [
                                    'view' => 'config-header-buttons',
                                ],
                                [
                                    'layout' => 'config-drawer-content',
                                ],
                            ],
                            'type' => 'simple',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8',
                        ],
                    ],
                    [
                        'layout' => [
                            'components' => [
                                [
                                    'view' => 'config-drawer-howto',
                                ],
                            ],
                            'type' => 'simple',
                            'name' => 'side-pane',
                            'span' => 4,
                        ],
                    ],
                    [
                        'layout' => [
                            'components' => [],
                            'type' => 'simple',
                            'name' => 'dashboard-pane',
                            'span' => 4,
                        ],
                    ],
                    [
                        'layout' => [
                            'components' => [],
                            'type' => 'simple',
                            'name' => 'preview-pane',
                            'span' => 8,
                        ],
                    ],
                ],
                'type' => 'default',
                'name' => 'sidebar',
                'span' => 12,
            ],
        ],
    ],
    'name' => 'base',
    'span' => 12,
];
