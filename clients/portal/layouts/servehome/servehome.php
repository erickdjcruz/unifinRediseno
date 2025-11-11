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

$viewdefs['portal']['layout']['servehome'] = [
    'type' => 'base',
    'name' => 'dashboard-pane',
    'css_class' => 'dashboard-pane main-pane row-fluid',
    'components' => [
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
