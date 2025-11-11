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
$viewdefs['DRI_Workflow_Task_Templates']['base']['view']['record'] = [
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
                    'acl_action' => 'edit',
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
                    'acl_module' => 'DRI_Workflow_Task_Templates',
                    'acl_action' => 'create',
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
            'label' => 'LBL_RECORD_HEADER',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'width' => 42,
                    'height' => 42,
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'link' => false,
                ],
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
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'activity_type',
                    'type' => 'activity-type',
                    'label' => 'LBL_ACTIVITY_TYPE',
                    'related_fields' => [
                        'dri_subworkflow_template_id',
                    ],
                ],
                [
                    'name' => 'type',
                    'label' => 'LBL_TYPE',
                ],
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                ],
                'points',
                [
                    'name' => 'blocked_by',
                    'label' => 'LBL_BLOCKED_BY',
                    'type' => 'blocked-by',
                    'related_fields' => [
                        'dri_workflow_template_id',
                    ],
                    'span' => 6,
                ],
                [
                    'name' => 'blocked_by_stages',
                    'label' => 'LBL_BLOCKED_BY_STAGES',
                    'type' => 'blocked-by',
                    'related_fields' => [
                        'dri_workflow_template_id',
                    ],
                    'span' => 6,
                ],
                [
                    'name' => 'dri_workflow_template_name',
                    'readonly' => true,
                    'label' => 'LBL_DRI_WORKFLOW_TEMPLATE',
                    'span' => 6,
                ],
                [
                    'name' => 'priority',
                    'label' => 'LBL_PRIORITY',
                ],
                [
                    'name' => 'url',
                    'label' => 'LBL_URL',
                ],
                [
                    'name' => 'direction',
                ],
                [
                    'name' => 'send_invite_type',
                    'label' => 'LBL_SEND_INVITES',
                    'span' => 6,
                ],
                [
                    'name' => 'time_of_day',
                    'type' => 'cj-time',
                ],
                [
                    'name' => 'assignee_rule',
                    'label' => 'LBL_ASSIGNEE_RULE',
                ],
                [
                    'name' => 'assignee_rule_activity_name',
                    'type' => 'activity-template-relate',
                ],
                [
                    'name' => 'target_assignee',
                ],
                [
                    'name' => 'target_assignee_team_name',
                ],
                'target_assignee_user_name',
                [
                    'name' => 'start_next_journey_name',
                ],
                [
                    'name' => 'select_to_guests',
                    'type' => 'select-to-guests',
                    'span' => '12',
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL6',
            'label' => 'LBL_RECORDVIEW_PANEL6',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'task_start_date_type',
                    'label' => 'LBL_TASK_START_DATE_TYPE',
                ],
                [
                    'name' => 'task_start_days',
                    'label' => 'LBL_TASK_START_DAYS',
                ],
                [
                    'name' => 'start_date_module',
                    'type' => 'module-data',
                    'onChangeTriggerField' => 'task_start_date_type',
                    'onChangeTriggerValueEqualTo' => 'days_from_parent_date_field',
                    'fieldListName' => 'start_date_field',
                ],
                [
                    'name' => 'start_date_field',
                    'type' => 'module-data',
                ],
                [
                    'name' => 'start_date_activity_name',
                    'type' => 'activity-template-relate',
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL2',
            'label' => 'LBL_RECORDVIEW_PANEL2',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'task_due_date_type',
                    'label' => 'LBL_TASK_DUE_DATE_TYPE',
                ],
                [
                    'name' => 'task_due_days',
                    'label' => 'LBL_TASK_DUE_DAYS',
                ],
                [
                    'name' => 'due_date_module',
                    'type' => 'module-data',
                    'onChangeTriggerField' => 'task_due_date_type',
                    'onChangeTriggerValueEqualTo' => 'days_from_parent_date_field',
                    'fieldListName' => 'due_date_field',
                ],
                [
                    'name' => 'due_date_field',
                    'type' => 'module-data',
                ],
                [
                    'name' => 'due_date_criteria',
                    'label' => 'LBL_DUE_DATE_CRITERIA',
                ],
                [
                    'name' => 'due_date_activity_name',
                    'type' => 'activity-template-relate',
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL3',
            'label' => 'LBL_RECORDVIEW_PANEL3',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'momentum_start_type',
                    'label' => 'LBL_MOMENTUM_START_TYPE',
                ],
                [
                    'name' => 'momentum_points',
                    'label' => 'LBL_MOMENTUM_POINTS',
                ],
                [
                    'name' => 'momentum_due_days',
                    'label' => 'LBL_MOMENTUM_DUE_DAYS',
                ],
                [
                    'name' => 'momentum_due_hours',
                    'label' => 'LBL_MOMENTUM_DUE_HOURS',
                ],
                [
                    'name' => 'momentum_start_activity_name',
                    'type' => 'activity-template-relate',
                ],
                [
                    'name' => 'momentum_start_module',
                    'label' => 'LBL_MOMENTUM_START_MODULE',
                    'type' => 'module-data',
                    'onChangeTriggerField' => 'momentum_start_type',
                    'onChangeTriggerValueEqualTo' => 'parent_date_field',
                    'fieldListName' => 'momentum_start_field',
                ],
                [
                    'name' => 'momentum_start_field',
                    'type' => 'module-data',
                    'label' => 'LBL_MOMENTUM_START_FIELD',
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL1',
            'label' => 'LBL_RECORDVIEW_PANEL1',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                    'span' => 12,
                ],
                [
                    'name' => 'tag',
                    'span' => 6,
                ],
                [
                    'name' => 'team_name',
                    'span' => 6,
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
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
                    'span' => 6,
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
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
                    'span' => 6,
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL5',
            'label' => 'LBL_RECORDVIEW_PANEL5',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'allow_activity_by',
                    'type' => 'allow-activity-by',
                    'label' => 'LBL_ALLOW_ACTIVITY_BY',
                    'span' => 12,
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL4',
            'label' => 'LBL_RECORDVIEW_PANEL4',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'populate_fields',
                    'type' => 'cj-populate-fields',
                    'label' => 'LBL_POPULATE_FIELDS',
                    'span' => 12,
                ],
            ],
        ],
    ],
    'templateMeta' => [
        'useTabs' => false,
    ],
];
