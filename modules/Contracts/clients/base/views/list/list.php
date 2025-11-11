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
$viewdefs['Contracts']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_LIST_CONTRACT_NAME',
                    'link' => true,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'link' => true,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'start_date',
                    'label' => 'LBL_LIST_START_DATE',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'end_date',
                    'label' => 'LBL_LIST_END_DATE',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_LIST_TEAM',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_TO_USER',
                    'default' => true,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
