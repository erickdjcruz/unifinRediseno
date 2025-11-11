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

$viewdefs['OutboundEmail']['base']['view']['selection-list-for-bpm'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                    'related_fields' => [
                        'type',
                    ],
                ],
                [
                    'name' => 'email_address',
                    'type' => 'email-address',
                    'enabled' => true,
                    'default' => true,
                    'link' => false,
                ],
                [
                    'name' => 'reply_to_name',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'reply_to_email_address',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'team_name',
                    'enabled' => true,
                    'default' => true,
                    'label' => 'LBL_PRIMARY_TEAM',
                ],
            ],
        ],
    ],
];
