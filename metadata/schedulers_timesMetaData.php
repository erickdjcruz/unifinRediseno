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

$dictionary['SchedulersTimes'] = ['table' => 'schedulers_times',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'default' => '0',
            'reportable' => false,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
        ],
        'scheduler_id' => [
            'name' => 'scheduler_id',
            'vname' => 'LBL_SCHEDULER_ID',
            'type' => 'id',
            'required' => true,
            'isnull' => false,
            'reportable' => false,
        ],
        'execute_time' => [
            'name' => 'execute_time',
            'vname' => 'LBL_EXECUTE_TIME',
            'type' => 'datetime',
            'required' => true,
            'reportable' => true,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'varchar',
            'len' => '25',
            'required' => true,
            'reportable' => true,
            'default' => 'ready',
        ],
    ],
    'indices' => [
        [
            'name' => 'schedulers_timespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_scheduler_id',
            'type' => 'index',
            'fields' => [
                'scheduler_id',
                'execute_time',
            ],
        ],
    ],
];
