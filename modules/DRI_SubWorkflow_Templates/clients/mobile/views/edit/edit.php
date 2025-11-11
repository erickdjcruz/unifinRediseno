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
$viewdefs['DRI_SubWorkflow_Templates']['mobile']['view']['edit'] = [
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
                'dri_workflow_template_name',
                'sort_order',
                'points',
                'related_activities',
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'start_next_journey_name',
                'date_modified',
                'modified_by_name',
                'date_entered',
                'created_by_name',
                'team_name',
                [
                    'name' => 'tag',
                ],
            ],
        ],
    ],
];
