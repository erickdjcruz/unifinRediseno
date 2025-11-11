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

$viewdefs['Cases']['base']['view']['recorddashlet'] = [
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
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'primary' => true,
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'escalate-action',
                    'event' => 'button:escalate_button:click',
                    'name' => 'escalate_button',
                    'label' => 'LBL_ESCALATE_BUTTON_LABEL',
                    'acl_action' => 'create',
                    'module' => 'Cases',
                ],
            ],
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
                'name',
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'is_escalated',
                    'type' => 'badge',
                    'warning_level' => 'important',
                    'badge_label' => 'LBL_ESCALATED',
                    'dismiss_label' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'case_number',
                    'readonly' => true,
                ],
                [
                    'name' => 'follow_up_datetime',
                    'label' => 'LBL_FOLLOW_UP_DATETIME',
                    'type' => 'fieldset',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'follow_up_datetime',
                            'enabled' => true,
                            'readonly' => true,
                            'type' => 'follow-up-datetime-colorcoded',
                            'hide_on_edit' => true,
                            'color_code_classes' => [
                                'overdue' => 'expired',
                                'in_a_day' => 'soon-expired',
                                'more_than_a_day' => 'white black-text',
                            ],
                        ],
                        [
                            'name' => 'follow_up_datetime',
                            'enabled' => true,
                            'type' => 'datetimecombo',
                        ],
                    ],
                ],
                'primary_contact_name',
                'account_name',
                [
                    'name' => 'description',
                    'nl2br' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'attachment_list',
                    'label' => 'LBL_ATTACHMENTS',
                    'type' => 'multi-attachments',
                    'link' => 'attachments',
                    'module' => 'Notes',
                    'modulefield' => 'filename',
                    'bLabel' => 'LBL_ADD_ATTACHMENT',
                    'span' => 12,
                    'max_num' => -1,
                    'related_fields' => [
                        'filename',
                        'file_mime_type',
                    ],
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
                'priority',
                [
                    'name' => 'status',
                    'type' => 'case-status',
                    'label' => 'LBL_STATUS',
                ],
                'type',
                'source',
                'tag',
                'business_center_name',
                'request_close',
                'request_close_date',
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'resolution',
                    'span' => 12,
                ],
                [
                    'name' => 'resolved_datetime',
                    'span' => 12,
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
