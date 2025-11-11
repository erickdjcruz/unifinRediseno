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
$dictionary['SavedReport'] = [
    'table' => 'saved_reports',
    'color' => 'orange',
    'icon' => 'sicon-add-dashlet-lg',
    'visibility' => ['ReportVisibility' => true],
    'favorites' => true,
    'full_text_search' => false,
    'template_restricted_actions' => ['edit', 'delete'],
    'template_editable_fields' => ['assigned_user_name', 'team_name'],
    'template_specific_actions' => ['copy'],
    'fields' => [
        'module' => [
            'name' => 'module',
            'vname' => 'LBL_MODULE',
            'type' => 'enum',
            'function' => 'getModulesDropdown',
            'required' => true,
            'massupdate' => false,
        ],
        'report_type' => [
            'name' => 'report_type',
            'vname' => 'LBL_REPORT_TYPE',
            'type' => 'enum',
            'options' => 'dom_report_types',
            'required' => true,
            'massupdate' => false,
        ],
        'content' => [
            'name' => 'content',
            'vname' => 'LBL_CONTENT',
            'type' => 'longtext',
        ],
        'is_published' => [
            'name' => 'is_published',
            'vname' => 'LBL_IS_PUBLISHED',
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'massupdate' => false,
        ],
        'is_template' => [
            'name' => 'is_template',
            'vname' => 'LBL_TEMPLATE',
            'type' => 'bool',
            'default' => false,
            'readonly' => true,
            'reportable' => true,
            'importable' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
            ],
        ],
        'last_run_date' => [
            'name' => 'last_run_date',
            'id_name' => 'report_cache_id',
            'vname' => 'LBL_REPORT_LAST_RUN_DATE',
            'type' => 'datetime',
            'table' => 'report_cache',
            'isnull' => 'true',
            'module' => 'Reports',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
            'width' => '15',
            'link' => 'last_run_date_link',
            'rname_link' => 'date_modified',
        ],
        'last_run_date_link' => [
            'name' => 'last_run_date_link',
            'type' => 'link',
            'relationship' => 'reports_last_run_date',
            'source' => 'non-db',
            'vname' => 'LBL_REPORT_LAST_RUN_DATE',
            'reportable' => false,
            'primary_only' => true,
            'link_type' => 'one',
        ],
        'report_cache_id' => [
            'name' => 'report_cache_id',
            'rname' => 'id',
            'id_name' => 'report_cache_id',
            'vname' => 'LBL_REPORT_CACHE_ID',
            'type' => 'relate',
            'dbType' => 'id',
            'table' => 'report_cache',
            'isnull' => 'true',
            'module' => 'Reports',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'hideacl' => true,
            'studio' => false,
        ],
        'chart_type' => [
            'name' => 'chart_type',
            'vname' => 'LBL_CHART_TYPE',
            'type' => 'varchar',
            'required' => true,
            'default' => 'none',
            'len' => 36,
            'massupdate' => false,
        ],
        'schedule_type' => [
            'name' => 'schedule_type',
            'vname' => 'LBL_SCHEDULE_TYPE',
            'type' => 'varchar',
            'len' => '3',
            'default' => 'pro',
            'massupdate' => false,
        ],
        'favorite' => [
            'name' => 'favorite',
            'vname' => 'LBL_FAVORITE',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
            'massupdate' => false,
        ],
        'reportschedules' => [
            'name' => 'reportschedules',
            'type' => 'link',
            'relationship' => 'reports_reportschedules',
            'source' => 'non-db',
            'workflow' => false,
        ],
    ],
    'indices' => [
        'idx_savedreport_module' => [
            'name' => 'idx_savedreport_module',
            'type' => 'index',
            'fields' => ['module'],
        ],
    ],
    'relationships' => [
        'reports_last_run_date' => [
            'lhs_module' => 'Reports',
            'lhs_table' => 'saved_reports',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'join_table' => 'report_cache',
            'join_key_lhs' => 'id',
            'join_key_rhs' => 'assigned_user_id',
            'relationship_type' => 'user-based',
            'user_field' => 'assigned_user_id',
        ],
        'reports_reportschedules' => [
            'lhs_module' => 'Reports',
            'lhs_table' => 'saved_reports',
            'lhs_key' => 'id',
            'rhs_module' => 'ReportSchedules',
            'rhs_table' => 'report_schedules',
            'rhs_key' => 'report_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'uses' => [
        'basic',
        'assignable',
        'team_security',
    ],
    'ignore_templates' => [
        'following',
        'lockable_fields',
        'commentlog',
    ],
];

VardefManager::createVardef('Reports', 'SavedReport');

// to override field attributes
$dictionary['SavedReport']['fields']['id']['reportable'] = false;
$dictionary['SavedReport']['fields']['modified_user_id']['reportable'] = false;
$dictionary['SavedReport']['fields']['date_entered']['required'] = true;
$dictionary['SavedReport']['fields']['date_modified']['required'] = true;
$dictionary['SavedReport']['fields']['assigned_user_id']['reportable'] = false;
