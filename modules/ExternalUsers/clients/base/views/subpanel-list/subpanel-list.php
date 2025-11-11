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
$viewdefs['ExternalUsers']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'fullname',
                    'fields' => [
                        'salutation',
                        'first_name',
                        'last_name',
                    ],
                    'link' => true,
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'user_name',
                    'label' => 'LBL_USER_NAME',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'link' => true,
                ],
                [
                    'label' => 'LBL_EXTERNAL_APPLICATION',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'name' => 'external_app',
                ],
                [
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'name' => 'date_entered',
                ],
                [
                    'label' => 'LBL_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'name' => 'date_modified',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'unlink-action',
                'name' => 'unlink_button',
                'icon' => 'sicon-unlink',
                'label' => 'LBL_UNLINK_BUTTON',
            ],
        ],
    ],
];
