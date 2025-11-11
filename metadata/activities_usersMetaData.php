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

$dictionary['activities_users'] = [
    'table' => 'activities_users',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],

        'activity_id' => [
            'name' => 'activity_id',
            'type' => 'id',
            'required' => true,
        ],

        'parent_type' => [
            'name' => 'parent_type',
            'type' => 'varchar',
            'len' => 100,
        ],

        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
        ],

        'fields' => [
            'name' => 'fields',
            'type' => 'json',
            'dbType' => 'longtext',
            'required' => true,
        ],

        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],

        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'activities_users_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'activities_records',
            'type' => 'index',
            'fields' => ['parent_type', 'parent_id'],
        ],
        [
            'name' => 'activities_users_parent',
            'type' => 'index',
            'fields' => ['activity_id', 'parent_id', 'parent_type'],
        ],
    ],

    'relationships' => [
        'activities_users' => [
            'lhs_module' => 'Activities',
            'lhs_table' => 'activities',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'activities_users',
            'join_key_lhs' => 'activity_id',
            'join_key_rhs' => 'parent_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Users',
        ],
        'activities_teams' => [
            'lhs_module' => 'Activities',
            'lhs_table' => 'activities',
            'lhs_key' => 'id',
            'rhs_module' => 'Teams',
            'rhs_table' => 'teams',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'activities_users',
            'join_key_lhs' => 'activity_id',
            'join_key_rhs' => 'parent_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Teams',
        ],
    ],
];
