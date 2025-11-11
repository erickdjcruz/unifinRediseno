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
$viewdefs['DRI_Workflow_Task_Templates']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'dri_subworkflow_template_name',
                    'label' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
                    'enabled' => true,
                    'id' => 'DRI_SUBWORKFLOW_TEMPLATE_ID',
                    'link' => true,
                    'sortable' => true,
                    'default' => true,
                ],
                [
                    'name' => 'dri_workflow_template_name',
                    'label' => 'LBL_DRI_WORKFLOW_TEMPLATE',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'DRI_WORKFLOW_TEMPLATE_ID',
                    'link' => true,
                    'sortable' => true,
                    'default' => true,
                ],
                [
                    'name' => 'activity_type',
                    'label' => 'LBL_ACTIVITY_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'priority',
                    'label' => 'LBL_PRIORITY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'label' => 'LBL_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_modified',
                    'readonly' => true,
                ],
                [
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_entered',
                    'readonly' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'CREATED_BY',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
                [
                    'name' => 'task_due_days',
                    'label' => 'LBL_TASK_DUE_DAYS',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'task_due_date_type',
                    'label' => 'LBL_TASK_DUE_DATE_TYPE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED_NAME',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'MODIFIED_USER_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
            ],
        ],
    ],
];
