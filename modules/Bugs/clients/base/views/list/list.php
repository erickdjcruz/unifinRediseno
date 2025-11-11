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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['Bugs']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'bug_number',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'status',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'type',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'priority',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'fixed_in_release_name',
                    'enabled' => true,
                    'default' => true,
                    'link' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'release_name',
                    'enabled' => true,
                    'default' => false,
                    'link' => false,
                ],
                [
                    'name' => 'resolution',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'team_name',
                    'enabled' => true,
                    'default' => false,
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
                    'name' => 'is_escalated',
                    'label' => 'LBL_ESCALATED',
                    'badge_label' => 'LBL_ESCALATED',
                    'warning_level' => 'important',
                    'type' => 'badge',
                    'enabled' => true,
                    'default' => false,
                ],
            ],

        ],
    ],
];
