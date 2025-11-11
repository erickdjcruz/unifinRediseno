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
$viewdefs['Emails']['base']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
            'acl_action' => 'edit',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'name' => 'reply_button',
                    'type' => 'reply-action',
                    'event' => 'button:reply_button:click',
                    'label' => 'LBL_BUTTON_REPLY',
                    'acl_module' => 'Emails',
                    'acl_action' => 'create',
                ],
                [
                    'name' => 'reply_all_button',
                    'type' => 'reply-all-action',
                    'event' => 'button:reply_all_button:click',
                    'label' => 'LBL_BUTTON_REPLY_ALL',
                    'acl_module' => 'Emails',
                    'acl_action' => 'create',
                ],
                [
                    'name' => 'forward_button',
                    'type' => 'forward-action',
                    'event' => 'button:forward_button:click',
                    'label' => 'LBL_BUTTON_FORWARD',
                    'acl_module' => 'Emails',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'name' => 'delete_button',
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'label' => 'LBL_DELETE_BUTTON',
                    'acl_action' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'readonly' => true,
                    'related_fields' => [
                        'state',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'from_collection',
                    'type' => 'from',
                    'label' => 'LBL_FROM',
                    'readonly' => true,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'date_sent',
                    'label' => 'LBL_DATE',
                    'readonly' => true,
                ],
                [
                    'name' => 'to_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_TO_ADDRS',
                    'readonly' => true,
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                    'span' => 12,
                ],
                [
                    'name' => 'description_html',
                    'label' => 'LBL_MESSAGE_BODY',
                    'readonly' => true,
                    'span' => 12,
                    'related_fields' => [
                        'description',
                    ],
                ],
                [
                    'name' => 'attachments_collection',
                    'type' => 'email-attachments',
                    'label' => 'LBL_ATTACHMENTS',
                    'readonly' => true,
                    'span' => 12,
                    'max_num' => -1,
                    'fields' => [
                        'name',
                        'filename',
                        'file_size',
                        'file_source',
                        'file_mime_type',
                        'file_ext',
                        'upload_id',
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'cc_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_CC',
                    'readonly' => true,
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'bcc_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_BCC',
                    'readonly' => true,
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                'assigned_user_name',
                'parent_name',
                'team_name',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
    ],
];
