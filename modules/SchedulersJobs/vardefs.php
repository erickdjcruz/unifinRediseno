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

$dictionary['SchedulersJob'] = [
    'table' => 'job_queue',
    'favorites' => true,
    'activity_enabled' => true,
    'comment' => 'Job queue keeps the list of the jobs executed by this instance',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
            'required' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => true,
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
            'vname' => 'LBL_SCHEDULER',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'execute_time' => [
            'name' => 'execute_time',
            'vname' => 'LBL_EXECUTE_TIME',
            'type' => 'datetime',
            'required' => false,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'schedulers_status_dom',
            'len' => 20,
            'required' => true,
            'reportable' => true,
            'readonly' => true,
        ],
        'resolution' => [
            'name' => 'resolution',
            'vname' => 'LBL_RESOLUTION',
            'type' => 'enum',
            'options' => 'schedulers_resolution_dom',
            'len' => 20,
            'required' => true,
            'reportable' => true,
            'readonly' => true,
        ],
        'message' => [
            'name' => 'message',
            'vname' => 'LBL_MESSAGE',
            'type' => 'text',
            'required' => false,
            'reportable' => false,
        ],
        'target' => [
            'name' => 'target',
            'vname' => 'LBL_TARGET_ACTION',
            'type' => 'varchar',
            'len' => 255,
            'required' => true,
            'reportable' => true,
        ],
        'data' => [
            'name' => 'data',
            'vname' => 'LBL_DATA',
            'type' => 'longtext',
            'required' => false,
            'reportable' => true,
        ],
        'requeue' => [
            'name' => 'requeue',
            'vname' => 'LBL_REQUEUE',
            'type' => 'bool',
            'default' => 0,
            'required' => false,
            'reportable' => true,
        ],
        'retry_count' => [
            'name' => 'retry_count',
            'vname' => 'LBL_RETRY_COUNT',
            'type' => 'tinyint',
            'required' => false,
            'reportable' => true,
            'readonly' => true,
        ],
        'failure_count' => [
            'name' => 'failure_count',
            'vname' => 'LBL_FAIL_COUNT',
            'type' => 'tinyint',
            'required' => false,
            'reportable' => true,
            'readonly' => true,
        ],
        'job_delay' => [
            'name' => 'job_delay',
            'vname' => 'LBL_INTERVAL',
            'type' => 'int',
            'required' => false,
            'reportable' => false,
        ],
        'client' => [
            'name' => 'client',
            'vname' => 'LBL_CLIENT',
            'type' => 'varchar',
            'len' => 255,
            'required' => true,
            'reportable' => true,
        ],
        'percent_complete' => [
            'name' => 'percent_complete',
            'vname' => 'LBL_PERCENT',
            'type' => 'int',
            'required' => false,
        ],
        'job_group' => [
            'name' => 'job_group',
            'vname' => 'LBL_JOB_GROUP',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
            'reportable' => true,
        ],
        'schedulers' => [
            'name' => 'schedulers',
            'vname' => 'LBL_SCHEDULER_ID',
            'type' => 'link',
            'relationship' => 'schedulers_jobs_rel',
            'source' => 'non-db',
            'link_type' => 'one',
        ],
        'module' => [
            'name' => 'module',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
            'reportable' => true,
        ],
        'fallible' => [
            'name' => 'fallible',
            'vname' => 'LBL_FALLIBLE',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'An indicator of whether parents failure depends on subtask.',
        ],
        'rerun' => [
            'name' => 'rerun',
            'vname' => 'LBL_RERUN',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'If a job can be rerun.',
        ],
        'interface' => [
            'name' => 'interface',
            'vname' => 'LBL_INTERFACE',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Mark the task as interface for a job in job server.',
        ],
        'notes' => [
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'link',
            'relationship' => 'schedulersjob_notes',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
        ],
    ],
    'relationships' => [
        'schedulersjob_notes' => [
            'lhs_module' => 'SchedulersJobs',
            'lhs_table' => 'job_queue',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
        ],
    ],
    'indices' => [
        [
            'name' => 'job_queuepk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_resolution_executetime',
            'type' => 'index',
            'fields' => [
                'resolution',
                'execute_time',
            ],
        ],
        [
            'name' => 'idx_target_jobgroup',
            'type' => 'index',
            'fields' => [
                'target',
                'job_group',
                'resolution',
            ],
        ],
        [
            'name' => 'idx_status_sched_id',
            'type' => 'index',
            'fields' => [
                'status',
                'scheduler_id',
            ],
        ],
        [
            'name' => 'idx_del_time_status',
            'type' => 'index',
            'fields' => [
                'deleted',
                'execute_time',
                'status',
            ],
        ],
    ],
    'acls' => [
        'SugarACLAdminOnly' => true,
    ],
    'uses' => [
        'following',
        'favorite',
    ],
];

VardefManager::createVardef('SchedulersJobs', 'SchedulersJob', ['assignable']);
