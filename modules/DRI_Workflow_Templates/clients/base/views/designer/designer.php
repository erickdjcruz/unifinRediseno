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
$viewdefs['DRI_Workflow_Templates']['base']['view']['designer'] = [
    'template' => 'dri-workflow',
    'activityButtons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'name' => 'activity_template_edit_button',
                    'event' => 'activity:edit_button:click',
                    'tooltip' => 'LBL_EDIT_BUTTON_TITLE',
                    'acl_action' => 'edit',
                    'label' => ' ',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-preview',
                    'name' => 'activity_template_preview_button',
                    'event' => 'activity:preview_button:click',
                    'label' => 'LBL_PREVIEW',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-user',
                    'name' => 'activity_template_add_sub_task_button',
                    'event' => 'stage:add_sub_task_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'Tasks',
                    'label' => 'LBL_ADD_SUB_TASK_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-calendar',
                    'name' => 'activity_template_add_sub_meeting_button',
                    'event' => 'stage:add_sub_meeting_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'Meetings',
                    'label' => 'LBL_ADD_SUB_MEETING_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-phone',
                    'name' => 'activity_template_add_sub_call_button',
                    'event' => 'stage:add_sub_call_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'Calls',
                    'label' => 'LBL_ADD_SUB_CALL_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'name' => 'activity_template_delete_button',
                    'event' => 'activity:delete_button:click',
                    'acl_action' => 'edit',
                    'label' => 'LBL_DELETE_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-copy',
                    'event' => 'activity:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                ],
            ],
        ],
    ],
    'activityChildButtons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'name' => 'activity_edit_button',
                    'event' => 'activity:edit_button:click',
                    'tooltip' => 'LBL_EDIT_BUTTON_TITLE',
                    'acl_action' => 'edit',
                    'label' => ' ',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-preview',
                    'name' => 'activity_template_preview_button',
                    'event' => 'activity:preview_button:click',
                    'label' => 'LBL_PREVIEW',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'name' => 'activity_delete_button',
                    'event' => 'activity:delete_button:click',
                    'acl_action' => 'edit',
                    'label' => 'LBL_DELETE_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-copy',
                    'event' => 'activity:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                ],
            ],
        ],
    ],
    'stageButtons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'name' => 'stage_template_edit_button',
                    'event' => 'stage:edit_button:click',
                    'tooltip' => 'LBL_EDIT_BUTTON_TITLE',
                    'acl_action' => 'edit',
                    'label' => ' ',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-user',
                    'name' => 'stage_template_add_task_button',
                    'event' => 'stage:add_task_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflow_Task_Templates',
                    'label' => 'LBL_ADD_TASK_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-calendar',
                    'name' => 'stage_template_add_meeting_button',
                    'event' => 'stage:add_meeting_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflow_Task_Templates',
                    'label' => 'LBL_ADD_MEETING_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-phone',
                    'name' => 'stage_template_add_call_button',
                    'event' => 'stage:add_call_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflow_Task_Templates',
                    'label' => 'LBL_ADD_CALL_BUTTON_TITLE',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'name' => 'stage_template_delete_button',
                    'event' => 'stage:delete_button:click',
                    'acl_action' => 'delete',
                    'label' => 'LBL_DELETE_STAGE_BUTTON_TITLE',
                ],
            ],
        ],
    ],
    'topButtons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'name' => 'journey_add_stage_button',
                    'event' => 'journey:add_stage_button:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_SubWorkflow_Templates',
                    'label' => 'LBL_ADD_STAGE_BUTTON_TITLE',
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'dri-workflow-template',
        'defaults' => [
            'show_more' => 'more',
        ],
    ],
];
