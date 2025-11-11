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

$viewdefs['Administration']['base']['layout']['portal-preview'] = [
    'type' => 'portal-preview',
    'name' => 'portal-preview',
    // Manually force portal preview to use the light theme as Portal has not received the dark theme treatment yet
    'css_class' => 'dashboard-pane row-fluid sugar-light-theme',
    'components' => [
        [
            'layout' => 'portaltheme-megamenu',
        ],
        [
            'layout' => [
                'name' => 'dashboard',
                'type' => 'dashboard',
                'components' => [
                    [
                        'view' => 'contentsearchdashlet',
                    ],
                    [
                        'layout' => 'dashlet-main',
                    ],
                    [
                        'view' => 'dashboard-fab',
                        'loadModule' => 'Dashboards',
                    ],
                ],
                'last_state' => [
                    'id' => 'last-visit',
                ],
            ],
            'context' => [
                'forceNew' => true,
                'module' => 'Dashboards',
                'modelId' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
            ],
        ],
    ],
];
