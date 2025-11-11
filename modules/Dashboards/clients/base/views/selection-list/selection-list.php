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
$viewdefs['Dashboards']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                    'width' => 'xlarge',
                ],
                [
                    'name' => 'dashboard_module',
                    'label' => 'LBL_MODULE',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'view_name',
                    'label' => 'LBL_VIEW',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'default_dashboard',
                    'label' => 'LBL_DEFAULT_DASHBOARD',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'is_template',
                    'label' => 'LBL_TEMPLATE',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_USER',
                    'id' => 'ASSIGNED_USER_ID',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_LIST_TEAM',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_entered',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
