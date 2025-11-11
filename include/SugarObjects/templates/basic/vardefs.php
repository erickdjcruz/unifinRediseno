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
    'favorites' => true,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'duplicate_on_record_copy' => 'no',
            'comment' => 'Unique identifier',
            'mandatory_fetch' => true,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
            'unified_search' => true,
            'full_text_search' => ['enabled' => true, 'searchable' => true, 'boost' => 1.55],
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            //'duplicate_merge_dom_value' => '3',
            'merge_filter' => 'selected',
            'duplicate_on_record_copy' => 'always',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            'studio' => [
                'portaleditview' => false, // Bug58408 - hide from Portal edit layout
            ],
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'massupdate' => false,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                // Disabled until UX component is available
                //'aggregations' => array(
                //    'date_entered' => array(
                //        'type' => 'DateRange',
                //    ),
                //),
            ],
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                'sortable' => true,
                // Disabled until UX component is available
                //'aggregations' => array(
                //    'date_modified' => array(
                //        'type' => 'DateRange',
                //    ),
                //),
            ],
            'studio' => [
                'portaleditview' => false, // Bug58408 - hide from Portal edit layout
            ],
            'options' => 'date_range_search_dom',
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'massupdate' => false,
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => false,
            'group' => 'modified_by_name',
            'dbType' => 'id',
            'reportable' => true,
            'comment' => 'User who last modified record',
            'massupdate' => false,
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                'type' => 'id',
                'aggregations' => [
                    'modified_user_id' => [
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_MODIFIED_BY_ME',
                    ],
                ],
            ],
            'processes' => [
                'types' => [
                    'RR' => false,
                    'ALL' => true,
                ],
            ],
        ],
        'modified_by_name' => [
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'full_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'sort_on' => ['last_name'],
            'exportable' => true,
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => false,
            'dbType' => 'id',
            'group' => 'created_by_name',
            'comment' => 'User who created record',
            'massupdate' => false,
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                'type' => 'id',
                'aggregations' => [
                    'created_by' => [
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_CREATED_BY_ME',
                    ],
                ],
            ],
            'processes' => [
                'types' => [
                    'RR' => false,
                    'ALL' => true,
                ],
            ],
        ],
        'created_by_name' => [
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'full_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'sort_on' => ['last_name'],
            'exportable' => true,
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Full text of the note',
            'full_text_search' => ['enabled' => true, 'searchable' => true, 'boost' => 0.5],
            'rows' => 6,
            'cols' => 80,
            'duplicate_on_record_copy' => 'always',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
            'comment' => 'Record deletion indicator',
        ],
/////////////////RELATIONSHIP LINKS////////////////////////////
        'created_by_link' => [
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => strtolower($module) . '_created_by',
            'vname' => 'LBL_CREATED_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'side' => 'right',
        ],
        'modified_user_link' => [
            'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => strtolower($module) . '_modified_user',
            'vname' => 'LBL_MODIFIED_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'side' => 'right',
        ],
        'activities' => [
            'name' => 'activities',
            'type' => 'link',
            'relationship' => $_object_name . '_activities',
            'vname' => 'LBL_ACTIVITY_STREAM',
            'link_type' => 'many',
            'module' => 'Activities',
            'bean_name' => 'Activity',
            'source' => 'non-db',
        ],

    ],
    'indices' => [
        'id' => [
            'name' => 'idx_' . preg_replace('/[^a-z0-9_\-]/i', '', strtolower($module)) . '_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        'date_modified' => [
            'name' => 'idx_' . strtolower($table_name) . '_del_d_m',
            'type' => 'index',
            'fields' => ['deleted', 'date_modified', 'id'],
        ],
        'deleted' => [
            'name' => 'idx_' . strtolower($table_name) . '_id_del',
            'type' => 'index',
            'fields' => ['id', 'deleted'],
        ],
        'date_entered' => [
            'name' => 'idx_' . strtolower($table_name) . '_del_d_e',
            'type' => 'index',
            'fields' => ['deleted', 'date_entered', 'id'],
        ],
        'name_del' => [
            'name' => 'idx_' . strtolower($table_name) . '_name_del',
            'type' => 'index',
            'fields' => ['name', 'deleted'],
        ],
    ],
    'relationships' => [
        strtolower($module) . '_modified_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => $module,
            'rhs_table' => strtolower($table_name),
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many',
        ],
        strtolower($module) . '_created_by' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => $module,
            'rhs_table' => strtolower($table_name),
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many',
        ],
        $_object_name . '_activities' => [
            'lhs_module' => $module,
            'lhs_table' => strtolower($table_name),
            'lhs_key' => 'id',
            'rhs_module' => 'Activities',
            'rhs_table' => 'activities',
            'rhs_key' => 'id',
            'rhs_vname' => 'LBL_ACTIVITY_STREAM',
            'relationship_type' => 'many-to-many',
            'join_table' => 'activities_users',
            'join_key_lhs' => 'parent_id',
            'join_key_rhs' => 'activity_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => $module,
        ],
    ],
    'uses' => [
        'following',
        'favorite',
        'taggable',
        'commentlog',
        'lockable_fields',
        'integrate_fields',
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
