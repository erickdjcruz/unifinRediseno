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

$viewdefs['Home']['base']['layout']['sidebar-nav-flyout-module-menu'] = [
    'collectionSettings' => [
        'dashboards' => [
            'modules' => ['Dashboards'],
            'filter' => [
                'dashboard_module' => 'Home',
                '$or' => [
                    ['$favorite' => ''],
                    ['default_dashboard' => 1],
                ],
            ],
            'order_by' => [
                'date_modified' => 'DESC',
            ],
            'limit' => 50,
            'icon' => [
                'default_dashboard' => [
                    'false' => 'sicon-dashboard',
                    'true' => 'sicon-dashboard-default',
                ],
            ],
        ],
        'recently_viewed' => [
            'modules' => 'all',
            'excludedModules' => ['Forecasts'],
            'filter' => [
                '$tracker' => '-7 DAY',
            ],
            'icon' => 'sicon-clock',
            'limit' => 3,
            'toggle' => [
                'limit' => 10,
                'label_toggle' => 'LBL_SHOW_MORE_RECENTS',
                'label_untoggle' => 'LBL_SHOW_LESS_RECENTS',
            ],
            'endpoint' => 'recent',
        ],
    ],
];
