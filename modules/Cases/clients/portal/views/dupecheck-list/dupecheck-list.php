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
$viewdefs['Cases']['portal']['view']['dupecheck-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'case_number',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'link' => true,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'priority',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'status',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'id' => 'ASSIGNED_USER_ID',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_entered',
                    'default' => false,
                    'enabled' => true,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];
