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

$dictionary['time_aware_schedules'] = [
    'table' => 'time_aware_schedules',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'Unique ID of the schedule',
        ],
        'next_run' => [
            'name' => 'next_run',
            'vname' => 'LBL_TIME_AWARE_NEXT_RUN',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Datetime that this schedule should be valid to process',
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TIME_AWARE_TYPE',
            'type' => 'varchar',
            'required' => true,
            'comment' => 'Type of process of this schedule',
        ],
        'module' => [
            'name' => 'module',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'comment' => 'Module this schedule pertains to',
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'vname' => 'LBL_TIME_AWARE_BEAN_ID',
            'type' => 'id',
            'comment' => 'Bean ID this schedule pertains to',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'Whether this schedule has already been processed',
        ],
    ],
    'indices' => [
        [
            'name' => 'time_aware_schedules_k',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
    ],
];
