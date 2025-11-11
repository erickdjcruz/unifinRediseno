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
$viewdefs['Leads']['base']['view']['subpanel-list'] = [
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
                ],
                [
                    'label' => 'LBL_LIST_REFERED_BY',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'refered_by',
                ],
                [
                    'label' => 'LBL_LIST_LEAD_SOURCE',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'lead_source',
                ],
                [
                    'label' => 'LBL_LIST_PHONE',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'phone_work',
                ],
                [
                    'label' => 'LBL_LIST_EMAIL_ADDRESS',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'email',
                ],
                [
                    'name' => 'lead_source_description',
                    'label' => 'LBL_LIST_LEAD_SOURCE_DESCRIPTION',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
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
                'icon' => 'sicon-edit',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'convertbutton',
                'name' => 'lead_convert_button',
                'label' => 'LBL_CONVERT_BUTTON_LABEL',
                'acl_action' => 'edit',
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
