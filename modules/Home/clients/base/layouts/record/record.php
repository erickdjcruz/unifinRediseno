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

$viewdefs['Home']['base']['layout']['record'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'base',
                'name' => 'main-pane',
                'css_class' => 'main-pane home-dashboard row-fluid bg-[--secondary-content-background]',
                'components' => [
                    [
                        'layout' => [
                            'name' => 'dashboard',
                            'type' => 'dashboard',
                            'components' => [
                                [
                                    'view' => 'dashboard-headerpane',
                                    'xmeta' => [
                                        'loadModule' => 'Dashboards',
                                    ],
                                ],
                                [
                                    'view' => 'tabbed-dashboard',
                                ],
                                [
                                    'layout' => 'dashboard-main',
                                ],
                                [
                                    'view' => 'dashboard-fab',
                                ],
                            ],
                            'last_state' => [
                                'id' => 'last-visit',
                            ],
                        ],
                        'loadModule' => 'Dashboards',
                    ],
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'last-visit',
    ],
];
