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
$viewdefs['SugarLive']['base']['layout']['config-drawer'] = [
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
                                    'view' => 'config-header-buttons',
                                ],
                                [
                                    'layout' => 'config-drawer-content',
                                ],
                            ],
                        ],
                    ], [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'side-pane',
                            'css_class' => 'sugarlive-config-side-pane',
                            'components' => [
                                [
                                    'view' => 'config-side-pane',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
