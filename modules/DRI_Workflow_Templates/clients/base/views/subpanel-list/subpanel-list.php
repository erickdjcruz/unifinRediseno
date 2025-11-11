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
$viewdefs['DRI_Workflow_Templates']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'available_modules',
                    'label' => 'LBL_AVAILABLE_MODULES',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'related_activities',
                    'label' => 'LBL_RELATED_ACTIVITIES',
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
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'date_modified',
        'direction' => 'desc',
    ],
];
