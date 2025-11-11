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
$viewdefs['DRI_Workflow_Task_Templates']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'activity_type',
                    'type' => 'activity-type',
                    'label' => 'LBL_ACTIVITY_TYPE',
                    'related_fields' => [
                        'dri_subworkflow_template_id',
                    ],
                ],
                'name',
                'sort_order',
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
                    'span' => 6,
                ],
                'priority',
                'url',
                'direction',
                'send_invite_type',
                [
                    'name' => 'time_of_day',
                    'type' => 'cj-time',
                ],
                'assignee_rule',
                [
                    'name' => 'assignee_rule_activity_name',
                    'type' => 'activity-template-relate',
                ],
                'target_assignee',
                'target_assignee_team_name',
                'target_assignee_user_name',
                'start_next_journey_name',
                [
                    'name' => 'select_to_guests',
                    'type' => 'select-to-guests',
                    'span' => '12',
                ],
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
                'date_entered',
                'created_by_name',
                'date_modified',
                'modified_by_name',
                [
                    'name' => 'allow_activity_by',
                    'type' => 'allow-activity-by',
                    'label' => 'LBL_ALLOW_ACTIVITY_BY',
                    'span' => 12,
                ],
                [
                    'name' => 'populate_fields',
                    'type' => 'cj-populate-fields',
                    'label' => 'LBL_POPULATE_FIELDS',
                    'span' => 12,
                ],
                'team_name',
            ],
        ],
    ],
];
