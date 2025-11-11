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
$viewdefs['Escalations']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_LIST_RELATED_TO',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'reason',
                    'label' => 'LBL_REASON',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'source',
                    'label' => 'LBL_SOURCE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'id' => 'ASSIGNED_USER_ID',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_LIST_TEAM',
                    'default' => false,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
