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
$viewdefs['DRI_Workflow_Task_Templates']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'points',
                    'label' => 'LBL_POINTS',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'activity_type',
                    'label' => 'LBL_ACTIVITY_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'type',
                    'label' => 'LBL_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'sort_order',
        'direction' => 'asc',
    ],
];
