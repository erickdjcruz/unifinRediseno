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
$viewdefs['Meetings']['base']['view']['record'] = [
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
            'type' => 'actiondropdown',
            'name' => 'save_dropdown',
            'primary' => true,
            'switch_on_click' => true,
            'showOn' => 'edit',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:save_button:click',
                    'name' => 'save_button',
                    'label' => 'LBL_SAVE_BUTTON_LABEL',
                    'css_class' => 'btn btn-primary',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'save-and-send-invites-button',
                    'event' => 'button:save_button:click',
                    'name' => 'save_invite_button',
                    'label' => 'LBL_SAVE_AND_SEND_INVITES_BUTTON',
                    'acl_action' => 'edit',
                ],
            ],
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
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'editrecurrencesbutton',
                    'event' => 'button:edit_recurrence_button:click',
                    'name' => 'edit_recurrence_button',
                    'label' => 'LBL_EDIT_ALL_RECURRENCES',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'launchbutton',
                    'name' => 'host_button',
                    'host' => true,
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'launchbutton',
                    'name' => 'join_button',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'Meetings',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
                    'acl_action' => 'delete',
                ],
                [
                    'type' => 'deleterecurrencesbutton',
                    'name' => 'delete_recurrence_button',
                    'label' => 'LBL_REMOVE_ALL_RECURRENCES',
                    'acl_action' => 'delete',
                ],
                [
                    'type' => 'closebutton',
                    'name' => 'record-close-new',
                    'label' => 'LBL_CLOSE_AND_CREATE_BUTTON_LABEL',
                    'closed_status' => 'Held',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'closebutton',
                    'name' => 'record-close',
                    'label' => 'LBL_CLOSE_BUTTON_LABEL',
                    'closed_status' => 'Held',
                    'acl_action' => 'edit',
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
                'name',
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'status',
                    'type' => 'event-status',
                    'enum_width' => 'auto',
                    'dropdown_width' => 'auto',
                    'dropdown_class' => 'select2-menu-only',
                    'container_class' => 'select2-menu-only',
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
                    'name' => 'duration',
                    'type' => 'duration',
                    'label' => 'LBL_START_AND_END_DATE_DETAIL_VIEW',
                    'dismiss_label' => false,
                    'inline' => true,
                    'show_child_labels' => true,
                    'fields' => [
                        [
                            'name' => 'date_start',
                            'time' => [
                                'step' => 15,
                            ],
                            'readonly' => false,
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_START_AND_END_DATE_TO',
                        ],
                        [
                            'name' => 'date_end',
                            'time' => [
                                'step' => 15,
                                'duration' => [
                                    'relative_to' => 'date_start',
                                ],
                            ],
                            'readonly' => false,
                        ],
                    ],
                    'span' => 9,
                    'related_fields' => [
                        'duration_hours',
                        'duration_minutes',
                    ],
                ],
                [
                    'name' => 'repeat_type',
                    'span' => 3,
                    'related_fields' => [
                        'repeat_parent_id',
                        'rset',
                    ],
                ],
                [
                    'name' => 'recurrence',
                    'type' => 'recurrence',
                    'span' => 12,
                    'inline' => true,
                    'show_child_labels' => true,
                    'fields' => [
                        [
                            'label' => 'LBL_CALENDAR_REPEAT_INTERVAL',
                            'name' => 'repeat_interval',
                            'type' => 'enum',
                            'options' => 'repeat_interval_number',
                            'required' => true,
                            'default' => 1,
                        ],
                        [
                            'label' => 'LBL_CALENDAR_REPEAT_ON',
                            'name' => 'repeat_dow',
                            'type' => 'repeat-dow',
                            'options' => 'dom_cal_day_of_week',
                            'isMultiSelect' => true,
                        ],
                        [
                            'label' => 'LBL_CALENDAR_REPEAT_ON',
                            'name' => 'repeat_selector',
                            'type' => 'enum',
                            'options' => 'repeat_selector_dom',
                            'default' => 'None',
                        ],
                        [
                            'label' => ' ',
                            'name' => 'repeat_days',
                            'type' => 'repeat-days',
                            'options' => ['' => ''],
                            'isMultiSelect' => true,
                            'dropdown_class' => 'recurring-date-dropdown',
                            'container_class' => 'recurring-date-container select2-choices-pills-close',
                        ],
                        [
                            'label' => ' ',
                            'name' => 'repeat_ordinal',
                            'type' => 'enum',
                            'options' => 'repeat_ordinal_dom',
                        ],
                        [
                            'label' => ' ',
                            'name' => 'repeat_unit',
                            'type' => 'enum',
                            'options' => 'repeat_unit_dom',
                        ],
                        [
                            'label' => 'LBL_CALENDAR_REPEAT',
                            'name' => 'repeat_end_type',
                            'type' => 'enum',
                            'options' => 'repeat_end_types',
                            'default' => 'Until',
                        ],
                        [
                            'label' => 'LBL_CALENDAR_REPEAT_UNTIL_DATE',
                            'name' => 'repeat_until',
                            'type' => 'repeat-until',
                        ],
                        [
                            'label' => 'LBL_CALENDAR_REPEAT_COUNT',
                            'name' => 'repeat_count',
                            'type' => 'repeat-count',
                        ],
                    ],
                ],
                'location',
                [
                    'name' => 'reminders',
                    'type' => 'fieldset',
                    'inline' => true,
                    'equal_spacing' => true,
                    'show_child_labels' => true,
                    'fields' => [
                        'reminder_time',
                        'email_reminder_time',
                    ],
                ],
                [
                    'name' => 'description',
                    'span' => 12,
                    'rows' => 3,
                ],
                [
                    'name' => 'type',
                ],
                'parent_name',
                [
                    'name' => 'password',
                    'span' => 12,
                ],
                [
                    'name' => 'invitees',
                    'type' => 'participants',
                    'label' => 'LBL_INVITEES',
                    'span' => 12,
                    'fields' => [
                        'name',
                        'accept_status_meetings',
                        'picture',
                        'email',
                    ],
                    'related_fields' => [
                        'date_start',
                        'date_end',
                        'duration_hours',
                        'duration_minutes',
                    ],
                    'max_num' => 20,
                ],
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
                [
                    'name' => 'internal_notes',
                    'span' => 12,
                    'rows' => 3,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'columns' => 2,
            'hide' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
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
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
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
            ],
        ],
    ],
];
