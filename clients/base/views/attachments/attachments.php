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
$viewdefs['base']['view']['attachments'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_ATTACHMENTS_NAME',
            'description' => 'LBL_DASHLET_ATTACHMENTS_DESCRIPTION',
            'config' => [
                'auto_refresh' => '0',
                'module' => 'Notes',
                'link' => 'notes',
            ],
            'preview' => [
                'module' => 'Notes',
                'link' => 'notes',
            ],
            'filter' => [
                'module' => [
                    'Accounts',
                    'Contacts',
                    'Opportunities',
                    'Leads',
                    'Bugs',
                    'Cases',
                    'RevenueLineItems',
                    'KBContents',
                ],
                'view' => 'record',
            ],
            'fields' => [
                'name',
                'date_entered',
                'filename',
                'file_mime_type',
                'assigned_user_id',
                'assigned_user_name',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'actiondropdown',
                'icon' => 'sicon-plus',
                'no_default_action' => true,
                'buttons' => [
                    [
                        'type' => 'dashletaction',
                        'css_class' => '',
                        'label' => 'LBL_CREATE_RELATED_RECORD',
                        'action' => 'openCreateDrawer',
                    ],
                    [
                        'type' => 'dashletaction',
                        'css_class' => '',
                        'label' => 'LBL_ASSOC_RELATED_RECORD',
                        'action' => 'openSelectDrawer',
                    ],
                ],
            ],
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'toggleClicked',
                        'label' => 'LBL_DASHLET_MINIMIZE',
                        'event' => 'minimize',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                ],
            ],
        ],
    ],
    'rowactions' => [
        [
            'type' => 'rowaction',
            'icon' => 'sicon-unlink',
            'css_class' => 'btn btn-mini',
            'event' => 'attachment:unlinkrow:fire',
            'target' => 'view',
            'tooltip' => 'LBL_UNLINK_BUTTON',
            'acl_action' => 'edit',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'limit',
                    'label' => 'Display Rows',
                    'type' => 'enum',
                    'options' => [
                        5 => 5,
                        10 => 10,
                        15 => 15,
                        20 => 20,
                    ],
                ],
                [
                    'name' => 'auto_refresh',
                    'label' => 'Auto Refresh',
                    'type' => 'enum',
                    'options' => 'sugar7_dashlet_auto_refresh_options',
                ],
            ],
        ],
    ],
    'supportedImageExtensions' => [
        'image/jpeg' => 'JPG',
        'image/gif' => 'GIF',
        'image/png' => 'PNG',
    ],
    'defaultType' => 'txt',
];
