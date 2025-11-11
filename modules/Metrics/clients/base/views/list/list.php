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

$viewdefs['Metrics']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'metric_module',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'team_name',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'metric_context',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'type' => 'relate',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => ['created_by'],
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_USER',
                    'id' => 'ASSIGNED_USER_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'status',
                    'link' => false,
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_entered',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
