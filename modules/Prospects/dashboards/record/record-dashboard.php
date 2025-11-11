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

return [
    'metadata' => [
        'dashlets' => [
            [
                'view' => [
                    'type' => 'activity-timeline',
                    'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                ],
                'context' => [
                    'module' => 'Prospects',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'name' => 'active-tasks',
                    'label' => 'LBL_ACTIVE_TASKS_DASHLET',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'history',
                    'label' => 'LBL_HISTORY_DASHLET',
                    'filter' => '7',
                    'limit' => '10',
                    'visibility' => 'user',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            [
                'view' => [
                    'type' => 'planned-activities',
                    'label' => 'LBL_PLANNED_ACTIVITIES_DASHLET',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 12,
            ],
        ],
    ],
    'name' => 'LBL_TARGETS_RECORD_DASHBOARD',
    'id' => '5d671d06-7b52-11e9-83cf-f218983a1c3e',
];
