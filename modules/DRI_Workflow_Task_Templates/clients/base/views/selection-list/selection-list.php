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
$viewdefs['DRI_Workflow_Task_Templates']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
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
                    'sortable' => false,
                    'default' => true,
                ],
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'activity_type',
                    'label' => 'LBL_ACTIVITY_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'type',
                    'label' => 'LBL_TYPE',
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
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'readonly' => true,
                    'default' => false,
                ],
                [
                    'name' => 'task_due_date_type',
                    'label' => 'LBL_TASK_DUE_DATE_TYPE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'points',
                    'label' => 'LBL_POINTS',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'duration_minutes',
                    'label' => 'LBL_DURATION_MINUTES',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'date_modified',
        'direction' => 'desc',
    ],
];
