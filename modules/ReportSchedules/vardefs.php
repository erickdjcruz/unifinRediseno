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

$dictionary['ReportSchedule'] = [
    'table' => 'report_schedules',
    'archive' => false,
    'audited' => true,
    'fields' => [
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'report_id' => [
            'name' => 'report_id',
            'type' => 'id',
            'required' => true,
            'massupdate' => false,
        ],
        'report_name' => [
            'name' => 'report_name',
            'rname' => 'name',
            'id_name' => 'report_id',
            'vname' => 'LBL_REPORT_NAME',
            'type' => 'relate',
            'table' => 'saved_reports',
            'isnull' => false,
            'module' => 'Reports',
            'dbType' => 'varchar',
            'link' => 'report',
            'len' => '255',
            'source' => 'non-db',
            'required' => true,
            'massupdate' => false,
        ],
        'report' => [
            'name' => 'report',
            'type' => 'link',
            'relationship' => 'report_reportschedules',
            'source' => 'non-db',
            'vname' => 'LBL_REPORTS',
            'massupdate' => false,
        ],
        'date_start' => [
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'datetime',
            'required' => true,
            'audited' => true,
        ],
        'time_interval' => [
            'name' => 'time_interval',
            'type' => 'enum',
            'dbType' => 'int',
            'len' => 11,
            'vname' => 'LBL_TIME_INTERVAL',
            'options' => 'reportschedule_time_interval_dom',
            'required' => true,
            'default' => '604800',
            'audited' => true,
        ],
        'next_run' => [
            'name' => 'next_run',
            'type' => 'datetime',
            'vname' => 'LBL_NEXT_RUN',
            'massupdate' => false,
        ],
        'active' => [
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
            'len' => '1',
            'default' => '1',
            'audited' => true,
        ],
        'schedule_type' => [
            'name' => 'schedule_type',
            'type' => 'varchar',
            'len' => 3,
            'vname' => 'LBL_SCHEDULE_TYPE',
            'required' => true,
            'comment' => 'Legacy field. ent for advanced reports, pro for regular reports',
        ],
        'users' => [
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'reportschedules_users',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
        ],
        'file_type' => [
            'name' => 'file_type',
            'vname' => 'LBL_FILE_TYPE',
            'type' => 'enum',
            'options' => 'file_type_dom',
            'len' => 32,
            'default' => 'PDF',
            'comment' => 'File type for scheduled report attachment',
            'dependency' => 'equal($embed_report, "")',
        ],
        'embed_report' => [
            'name' => 'embed_report',
            'vname' => 'LBL_EMBED_REPORT',
            'type' => 'enum',
            'options' => 'embed_report_dom',
            'len' => 32,
            'default' => '',
            'comment' => 'Embed report in email',
        ],
    ],
    'relationships' => [
        'report_reportschedules' => [
            'lhs_module' => 'Reports',
            'lhs_table' => 'saved_reports',
            'lhs_key' => 'id',
            'rhs_module' => 'ReportSchedules',
            'rhs_table' => 'report_schedules',
            'rhs_key' => 'report_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_del_active',
            'type' => 'index',
            'fields' => [
                'deleted',
                'active',
            ],
        ],
    ],

    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
    'acls' => ['SugarACLStatic' => true],
];

VardefManager::createVardef(
    'ReportSchedules',
    'ReportSchedule',
    [
        'basic',
        'assignable',
        'team_security',
    ]
);

$dictionary['ReportSchedule']['fields']['name']['audited'] = true;
