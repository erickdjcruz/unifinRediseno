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

$viewdefs['Administration']['base']['layout']['maps-config'] = [
    'type' => 'maps-config',
    'name' => 'maps-config',
    'css_class' => 'dashboard-pane row-fluid',
    'components' => [
        [
            'view' => 'maps-config',
        ],
        [
            'view' => 'config-header',
        ],
        [
            'layout' => [
                'type' => 'base',
                'name' => 'base',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane map-admin-border-right span3 min-w-96 h-full overflow-y-auto',
                            'components' => [
                                [
                                    'layout' => 'maps-controls',
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'base',
                            'css_class' => 'map-admin-side-pane span9',
                            'components' => [
                                [
                                    'layout' => 'maps-module-setup',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
