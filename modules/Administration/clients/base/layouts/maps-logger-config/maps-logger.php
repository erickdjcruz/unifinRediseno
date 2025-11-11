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

$viewdefs['Administration']['base']['layout']['maps-logger-config'] = [
    'type' => 'maps-logger',
    'name' => 'maps-logger',
    'css_class' => 'dashboard-pane row-fluid',
    'components' => [
        [
            'view' => 'maps-logger-config',
        ],
        [
            'view' => 'maps-logger-header',
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
                            'css_class' => 'maps-bottom-border main-pane map-admin-border-right span3 min-w-96',
                            'components' => [
                                [
                                    'view' => 'maps-logger-controls',
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
                                    'view' => 'maps-logger-display',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
