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
$viewdefs['Shifts']['base']['view']['subpanel-for-users'] = [
    'type' => 'subpanel-list',
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'date_start',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_end',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'timezone',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'icon' => 'sicon-edit',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'admin',
            ],
            [
                'type' => 'unlink-action',
                'name' => 'unlink_button',
                'icon' => 'sicon-unlink',
                'label' => 'LBL_UNLINK_BUTTON',
                'acl_action' => 'admin',
            ],
        ],
    ],
];
