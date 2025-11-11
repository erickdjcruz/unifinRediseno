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

$viewdefs['Dashboards']['base']['view']['dashboard-fab'] = [
    'icon' => 'sicon-hamburger-lg',
    'buttons' => [
        [
            'name' => 'add_button',
            'type' => 'rowaction',
            'icon' => 'sicon-dashboard-lg',
            'label' => 'LBL_DASHBOARD_CREATE',
            'showOn' => 'view',
            'disallowed_layouts' => [
                [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ], [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
                [
                    'name' => 'dashboard', // service console case
                    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
                ],
            ],
        ], [
            'name' => 'edit_module_tabs_button',
            'type' => 'rowaction',
            'icon' => 'sicon-edit-lg',
            'label' => 'LBL_EDIT_MODULE_TABS_BUTTON',
            'acl_action' => 'edit',
            'showOn' => 'view',
            'allowed_layouts' => [
                [
                    'name' => 'dashboard', // service console
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ], [
                    'name' => 'dashboard', // renewals console
                    'type' => 'renewals_console',
                ],
            ],
        ], [
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
                ], [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ], [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
                [
                    'name' => 'dashboard', // service console case
                    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
                ],
            ],
        ], [
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
                ], [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ], [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
                [
                    'name' => 'dashboard', // service console case
                    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
                ],
            ],
        ], [
            'name' => 'collapse_button',
            'type' => 'rowaction',
            'icon' => 'sicon-collapse-lg',
            'label' => 'LBL_DASHLET_MINIMIZE_ALL',
            'showOn' => 'view',
            'disallowed_layouts' => [
                [
                    'name' => 'dashboard',
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ], [
                    'name' => 'dashboard',
                    'type' => 'renewals_console',
                ], [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ], [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
                [
                    'name' => 'dashboard', // service console case
                    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
                ],
            ],
        ], [
            'name' => 'expand_button',
            'type' => 'rowaction',
            'icon' => 'sicon-expand-lg',
            'label' => 'LBL_DASHLET_MAXIMIZE_ALL',
            'showOn' => 'view',
            'disallowed_layouts' => [
                [
                    'name' => 'dashboard',
                    'id' => 'c108bb4a-775a-11e9-b570-f218983a1c3e',
                ], [
                    'name' => 'dashboard',
                    'type' => 'renewals_console',
                ], [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ], [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
                [
                    'name' => 'dashboard', // service console case
                    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
                ],
            ],
        ], [
            'name' => 'restore_tab_button',
            'type' => 'rowaction',
            'icon' => 'sicon-reset-lg',
            'label' => 'LBL_RESTORE_TAB_DEFAULT',
            'showOn' => 'view',
            'allowed_layouts' => [
                [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ],
            ],
        ], [
            'name' => 'restore_dashlets_button',
            'type' => 'rowaction',
            'icon' => 'sicon-refresh',
            'label' => 'LBL_RESTORE_DEFAULT_DASHLETS',
            'showOn' => 'view',
            'allowed_layouts' => [
                [
                    'name' => 'dashboard', // Portal preview dashboard
                    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
                ],
            ]
        ], [
            'name' => 'configure_summary_button',
            'type' => 'rowaction',
            'icon' => 'sicon-edit-lg',
            'label' => 'LBL_CONFIGURE_SUMMARY_PANEL',
            'showOn' => 'view',
            'allowed_layouts' => [
                [
                    'name' => 'dashboard', // omnichannel-console-config
                    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
                ],
            ],
        ], [
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
