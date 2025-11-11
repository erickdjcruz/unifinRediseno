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
$module_name = '<module_name>';
$_module_name = '<_module_name>';
$viewdefs[$module_name]['base']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => $_module_name . '_number',
                    'label' => 'LBL_NUMBER',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                    'link' => true,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'priority',
                    'label' => 'LBL_PRIORITY',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'resolution',
                    'label' => 'LBL_RESOLUTION',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => true,
                    'enabled' => true,
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
                [
                    'name' => 'hours_to_resolution',
                    'type' => 'decimal',
                    'readonly' => true,
                    'label' => 'LBL_HOURS_TO_RESOLUTION',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'business_hours_to_resolution',
                    'type' => 'decimal',
                    'readonly' => true,
                    'label' => 'LBL_BUSINESS_HOURS_TO_RESOLUTION',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'follow_up_datetime',
                    'type' => 'relative-time',
                    'label' => 'LBL_FOLLOW_UP',
                    'default' => false,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
