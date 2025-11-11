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
$viewdefs['DRI_SubWorkflows']['mobile']['view']['detail'] = [
    'templateMeta' => [
        'form' => [
            'buttons' => [
                'EDIT',
                'DUPLICATE',
                'DELETE',
            ],
        ],
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
        'useTabs' => false,
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_PANEL_DEFAULT',
            'columns' => '1',
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                'name',
                [
                    'name' => 'dri_workflow_name',
                    'label' => 'LBL_DRI_WORKFLOW',
                ],
                [
                    'name' => 'dri_subworkflow_template_name',
                    'label' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
                ],
                [
                    'name' => 'progress',
                    'readonly' => true,
                    'label' => 'LBL_PROGRESS',
                ],
                [
                    'name' => 'momentum_ratio',
                    'readonly' => true,
                    'label' => 'LBL_MOMENTUM_RATIO',
                ],
                [
                    'name' => 'score',
                    'readonly' => true,
                    'label' => 'LBL_SCORE',
                ],
                [
                    'name' => 'points',
                    'readonly' => true,
                    'label' => 'LBL_POINTS',
                ],
                [
                    'name' => 'momentum_score',
                    'readonly' => true,
                    'label' => 'LBL_MOMENTUM_SCORE',
                ],
                [
                    'name' => 'momentum_points',
                    'readonly' => true,
                    'label' => 'LBL_MOMENTUM_POINTS',
                ],
                [
                    'name' => 'state',
                    'readonly' => true,
                    'label' => 'LBL_STATE',
                ],
                [
                    'name' => 'sort_order',
                    'readonly' => false,
                    'label' => 'LBL_SORT_ORDER',
                ],
                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                ],
                [
                    'name' => 'date_started',
                    'readonly' => true,
                    'label' => 'LBL_DATE_STARTED',
                ],
                [
                    'name' => 'date_completed',
                    'readonly' => true,
                    'label' => 'LBL_DATE_COMPLETED',
                ],
                'team_name',
                'assigned_user_name',
                'date_modified',
                'modified_by_name',
                'date_entered',
                'created_by_name',
                [
                    'name' => 'tag',
                ],
            ],
        ],
    ],
];
