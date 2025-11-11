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
$viewdefs['DRI_Workflow_Templates']['mobile']['view']['edit'] = [
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
                'name',
                'active',
                'available_modules',
                [
                    'name' => 'stage_numbering',
                    'type' => 'toggle',
                    'css_class' => 'horizontal-vertical',
                ],
                'update_assignees',
                'active_limit',
                'disabled_stage_actions',
                'disabled_activity_actions',
                'assignee_rule',
                'target_assignee',
                'cancel_action',
                'not_applicable_action',
                'points',
                'related_activities',
                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                    'span' => 12,
                ],
                'date_entered',
                'created_by_name',
                'date_modified',
                'modified_by_name',
                'team_name',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
    ],
];
