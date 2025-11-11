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
$viewdefs['Calls']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_LIST_SUBJECT',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                    'name' => 'name',
                ],
                [
                    'label' => 'LBL_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'status',
                    'type' => 'event-status',
                    'css_class' => 'full-width',
                ],
                [
                    'name' => 'date_start',
                    'label' => 'LBL_LIST_DATE',
                    'type' => 'datetimecombo-colorcoded',
                    'completed_status_value' => 'Held',
                    'enabled' => true,
                    'default' => true,
                    'css_class' => 'overflow-visible',
                    'readonly' => true,
                    'related_fields' => ['status'],
                ],
                [
                    'label' => 'LBL_DATE_END',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_end',
                    'css_class' => 'overflow-visible',
                ],
                [
                    'name' => 'assigned_user_name',
                    'target_record_key' => 'assigned_user_id',
                    'target_module' => 'Employees',
                    'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
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
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'icon' => 'sicon-pencil',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'unlink-action',
                'name' => 'unlink_button',
                'icon' => 'sicon-unlink',
                'label' => 'LBL_UNLINK_BUTTON',
            ],
            [
                'type' => 'closebutton',
                'icon' => 'sicon-close',
                'name' => 'record-close',
                'label' => 'LBL_CLOSE_BUTTON_TITLE',
                'closed_status' => 'Held',
                'acl_action' => 'edit',
            ],
        ],
    ],
];
