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
$viewdefs['DRI_Workflows']['base']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'progress',
                    'label' => 'LBL_PROGRESS',
                    'type' => 'cj-progress-bar',
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
                    'type' => 'cj-momentum-bar',
                    'default' => true,
                    'enabled' => true,
                    'related_fields' => [
                        'momentum_score',
                        'momentum_points',
                    ],
                ],
                [
                    'name' => 'current_stage_name',
                    'label' => 'LBL_CURRENT_STAGE',
                    'enabled' => true,
                    'id' => 'CURRENT_STAGE_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => true,
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_PARENT_NAME',
                    'enabled' => true,
                    'id' => 'PARENT_NAME',
                    'link' => true,
                    'sortable' => false,
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
                    'name' => 'lead_name',
                    'label' => 'LBL_LEAD',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'contact_name',
                    'label' => 'LBL_CONTACT',
                    'enabled' => true,
                    'id' => 'CONTACT_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
                [
                    'name' => 'case_name',
                    'label' => 'LBL_CASE',
                    'enabled' => true,
                    'id' => 'CASE_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
                [
                    'name' => 'opportunity_name',
                    'label' => 'LBL_OPPORTUNITY',
                    'enabled' => true,
                    'id' => 'OPPORTUNITY_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
                [
                    'name' => 'score',
                    'label' => 'LBL_SCORE',
                    'enabled' => true,
                    'readonly' => true,
                    'default' => false,
                ],
                [
                    'name' => 'points',
                    'label' => 'LBL_POINTS',
                    'enabled' => true,
                    'readonly' => true,
                    'default' => false,
                ],
                [
                    'name' => 'state',
                    'type' => 'state-archived',
                    'label' => 'LBL_STATE',
                    'enabled' => true,
                    'readonly' => true,
                    'default' => false,
                    'related_fields' => [
                        'archived',
                    ],
                ],
                [
                    'name' => 'dri_workflow_template_name',
                    'label' => 'LBL_DRI_WORKFLOW_TEMPLATE',
                    'enabled' => true,
                    'id' => 'DRI_WORKFLOW_TEMPLATE_ID',
                    'link' => true,
                    'sortable' => false,
                    'default' => false,
                ],
                [
                    'name' => 'archived',
                    'label' => 'LBL_ARCHIVED',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'CREATED_BY',
                    'link' => true,
                    'default' => false,
                ],
                [
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED',
                    'enabled' => true,
                    'readonly' => true,
                    'id' => 'MODIFIED_USER_ID',
                    'link' => true,
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
