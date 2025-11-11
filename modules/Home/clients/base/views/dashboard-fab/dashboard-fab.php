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

$viewdefs['Home']['base']['view']['dashboard-fab'] = [
    'icon' => 'sicon-hamburger-lg',
    'buttons' => [
        [
            'name' => 'add_button',
            'type' => 'rowaction',
            'icon' => 'sicon-dashboard-lg',
            'label' => 'LBL_DASHBOARD_CREATE',
            'showOn' => 'view',
        ],
        [
            'name' => 'restore_dashboard_button',
            'type' => 'rowaction',
            'icon' => 'sicon-reset-lg',
            'label' => 'LBL_RESTORE_DASHBOARD_DEFAULT',
            'showOn' => 'view',
            'allowed_layouts' => [
                [
                    'name' => 'dashboard', // Service console
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ],
                [
                    'name' => 'dashboard', // Renewals console
                    'type' => 'renewals_console',
                ],
            ],
        ],
        [
            'name' => 'duplicate_button',
            'type' => 'rowaction',
            'icon' => 'sicon-copy-lg',
            'label' => 'LBL_DASHBOARD_DUPLICATE',
            'acl_module' => 'Dashboards',
            'acl_action' => 'create',
            'showOn' => 'view',
            'disallowed_layouts' => [
                [
                    'name' => 'dashboard', // service console
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ], [
                    'name' => 'dashboard', // renewals console
                    'type' => 'renewals_console',
                ],
            ],
        ],
        [
            'name' => 'delete_button',
            'type' => 'rowaction',
            'icon' => 'sicon-trash-lg',
            'label' => 'LBL_DASHBOARD_DELETE',
            'acl_action' => 'delete',
            'showOn' => 'view',
            'disallowed_layouts' => [
                [
                    'name' => 'dashboard',
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ], [
                    'name' => 'dashboard',
                    'type' => 'renewals_console',
                ],
            ],
        ],
        [
            'name' => 'add_dashlet_button',
            'type' => 'rowaction',
            'icon' => 'sicon-add-dashlet-lg',
            'label' => 'LBL_ADD_DASHLET_BUTTON',
            'events' => [
                'click' => 'button:add_dashlet_button:click',
            ],
            'acl_action' => 'edit',
            'showOn' => 'view',
        ],
    ],
];
