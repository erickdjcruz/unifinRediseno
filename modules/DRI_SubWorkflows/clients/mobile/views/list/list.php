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
$viewdefs['DRI_SubWorkflows']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'name' => 'dri_workflow_name',
                    'label' => 'LBL_DRI_WORKFLOW',
                    'enabled' => true,
                    'id' => 'DRI_WORKFLOW_ID',
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
                    'name' => 'state',
                    'label' => 'LBL_STATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'progress',
                    'label' => 'LBL_PROGRESS',
                    'type' => 'cj_progress_bar',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => [
                        'score',
                        'points',
                    ],
                ],
                [
                    'name' => 'momentum_ratio',
                    'label' => 'LBL_MOMENTUM_RATIO',
                    'type' => 'cj_momentum_bar',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => [
                        'momentum_score',
                        'momentum_points',
                    ],
                ],
                [
                    'name' => 'dri_subworkflow_template_name',
                    'label' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
                    'enabled' => true,
                    'id' => 'DRI_SUBWORKFLOW_TEMPLATE_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
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
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED_NAME',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'MODIFIED_USER_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
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
            ],
        ],
    ],
];
