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
$vardefs = [
    'fields' => [
        $_object_name . '_number' => [
            'name' => $_object_name . '_number',
            'vname' => 'LBL_NUMBER',
            'type' => 'int',
            'readonly' => true,
            'len' => 11,
            'required' => true,
            'auto_increment' => true,
            'unified_search' => true,
            'full_text_search' => ['enabled' => true, 'searchable' => true, 'boost' => 1.25],
            'comment' => 'Visual unique identifier',
            'duplicate_merge' => 'disabled',
            'disable_num_format' => true,
            'studio' => ['quickcreate' => false],
            'duplicate_on_record_copy' => 'no',
        ],

        'name' => [
            'name' => 'name',
            'vname' => 'LBL_SUBJECT',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
            'audited' => true,
            'unified_search' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
                'boost' => 1.47,
            ],
            'comment' => 'The short description of the bug',
            'merge_filter' => 'selected',
            'required' => true,
            'importable' => 'required',
            'duplicate_on_record_copy' => 'always',
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => strtolower($object_name) . '_type_dom',
            'len' => 255,
            'comment' => 'The type of issue (ex: issue, feature)',
            'merge_filter' => 'enabled',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
        ],

        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => strtolower($object_name) . '_status_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'The status of the issue',
            'merge_filter' => 'enabled',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
            ],
        ],

        'priority' => [
            'name' => 'priority',
            'vname' => 'LBL_PRIORITY',
            'type' => 'enum',
            'options' => strtolower($object_name) . '_priority_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'An indication of the priorty of the issue',
            'merge_filter' => 'enabled',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
        ],

        'resolution' => [
            'name' => 'resolution',
            'vname' => 'LBL_RESOLUTION',
            'type' => 'enum',
            'options' => strtolower($object_name) . '_resolution_dom',
            'len' => 255,
            'audited' => true,
            'comment' => 'An indication of how the issue was resolved',
            'merge_filter' => 'enabled',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',

        ],
        //not in cases.
        'work_log' => [
            'name' => 'work_log',
            'vname' => 'LBL_WORK_LOG',
            'type' => 'text',
            'full_text_search' => ['enabled' => true, 'searchable' => true, 'boost' => 0.51],
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Free-form text used to denote activities of interest',
        ],
        'follow_up_datetime' => [
            'name' => 'follow_up_datetime',
            'vname' => 'LBL_FOLLOW_UP_DATETIME',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'comment' => 'Deadline for following up on an issue',
            'audited' => true,
        ],
        'widget_follow_up_datetime' => [
            'name' => 'widget_follow_up_datetime',
            'vname' => 'LBL_WIDGET_FOLLOW_UP_DATETIME',
            'type' => 'widget',
            'multiline' => false,
            'studio' => false,
            'workflow' => false,
            'reportable' => false,
            'importable' => false,
            'source' => 'non-db',
            'console' => [
                'name' => 'follow_up_datetime',
                'label' => 'LBL_WIDGET_FOLLOW_UP_DATETIME',
                'type' => 'follow-up-datetime-colorcoded',
                'color_code_classes' => [
                    'overdue' => 'expired',
                    'in_a_day' => 'soon-expired',
                    'more_than_a_day' => 'white black-text',
                ],
            ],
        ],
        'resolved_datetime' => [
            'name' => 'resolved_datetime',
            'vname' => 'LBL_RESOLVED_DATETIME',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'comment' => 'Date when an issue is resolved',
            'audited' => true,
        ],
        'hours_to_resolution' => [
            'name' => 'hours_to_resolution',
            'vname' => 'LBL_HOURS_TO_RESOLUTION',
            'type' => 'decimal',
            'len' => '12,2',
            'precision' => 2,
            'comment' => 'How long it took to resolve this issue, in decimal calendar hours',
            'audited' => true,
            'readonly' => true,
        ],
        'business_hours_to_resolution' => [
            'name' => 'business_hours_to_resolution',
            'vname' => 'LBL_BUSINESS_HOURS_TO_RESOLUTION',
            'type' => 'decimal',
            'len' => '12,2',
            'precision' => 2,
            'comment' => 'How long it took to resolve this issue, in decimal business hours',
            'audited' => true,
            'readonly' => true,
        ],
        'pending_processing' => [
            'name' => 'pending_processing',
            'vname' => 'LBL_PENDING_PROCESSING',
            'type' => 'bool',
            'default' => 0,
            'reportable' => false,
            'readonly' => true,
            'studio' => false,
            'processes' => true,
        ],
    ],
    'indices' => [
        'number' => ['name' => strtolower($module) . 'numk', 'type' => 'unique', 'fields' => [$_object_name . '_number']],
    ],
    'relationships' => [
        strtolower($module) . '_changetimers' => [
            'lhs_module' => $module,
            'lhs_table' => strtolower($table_name),
            'lhs_key' => 'id',
            'rhs_module' => 'ChangeTimers',
            'rhs_table' => 'changetimers',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => $module,
        ],
    ],
    'uses' => [
        'taggable',
        'audit',
    ],
    'duplicate_check' => [
        'enabled' => true,
        'FilterDuplicateCheck' => [
            'filter_template' => [
                ['name' => ['$starts' => '$name']],
            ],
            'ranking_fields' => [
                ['in_field_name' => 'name', 'dupe_field_name' => 'name'],
            ],
        ],
    ],
];
