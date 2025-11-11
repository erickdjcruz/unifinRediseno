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
$dictionary['Team'] = [
    'table' => 'teams',
    'color' => 'teal',
    'icon' => 'sicon-team-perm',
    'archive' => false,
    'fields' => [
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_PRIMARY_TEAM_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 128,
            'fields' => [0 => 'name', 1 => 'name_2'],
        ],
        'name_2' => [
            'name' => 'name_2',
            'vname' => 'LBL_NAME_2',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 128,
            'reportable' => false,
        ],
        'associated_user_id' => [
            'name' => 'associated_user_id',
            'type' => 'id',
            'reportable' => false,
        ],
        'private' => [
            'name' => 'private',
            'vname' => 'LBL_PRIVATE',
            'type' => 'bool',
            'default' => 0,
        ],
        'users' => [
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'team_memberships',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
        ],
        'teams_sets' => [
            'name' => 'teams_sets',
            'type' => 'link',
            'relationship' => 'team_sets_teams',
            'link_type' => 'many',
            'side' => 'right',
            'source' => 'non-db',
            'vname' => 'LBL_TEAMS',
            'studio' => false,
            'duplicate_merge' => 'disabled',
        ],
        'activities_teams' => [
            'name' => 'activities_teams',
            'type' => 'link',
            'relationship' => 'activities_teams',
            'link_type' => 'many',
            'module' => 'Activities',
            'bean_name' => 'Activity',
            'source' => 'non-db',
        ],
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'adminFor' => 'Users',
            'allowUserRead' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_team_del',
            'type' => 'index',
            'fields' => ['name'],
        ],
        [
            'name' => 'idx_team_del_name',
            'type' => 'index',
            'fields' => ['deleted', 'name'],
        ],
    ],
];

VardefManager::createVardef('Teams', 'Team', ['basic']);

$dictionary['TeamMembership'] = [
    'table' => 'team_memberships',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'team_id' => [
            'name' => 'team_id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'explicit_assign' => [
            'name' => 'explicit_assign',
            'type' => 'bool',
            'len' => 1,
            'default' => 0,
            'required' => true,
        ],
        'implicit_assign' => [
            'name' => 'implicit_assign',
            'type' => 'bool',
            'len' => 1,
            'default' => 0,
            'required' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => 1,
            'default' => 0,
        ],
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'adminFor' => 'Users',
            'allowUserRead' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'team_membershipspk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'idx_team_membership',
            'type' => 'index',
            'fields' => ['user_id', 'team_id'],
        ],
        [
            'name' => 'idx_del_team_user',
            'type' => 'index',
            'fields' => ['deleted', 'team_id', 'user_id'],
        ],
        [
            'name' => 'idx_teammemb_team_user',
            'type' => 'alternate_key',
            'fields' => ['team_id', 'user_id'],
        ],
    ],
    'relationships' => [
        'team_memberships' => [
            'lhs_module' => 'Teams',
            'lhs_table' => 'teams',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'team_memberships',
            'join_key_lhs' => 'team_id',
            'join_key_rhs' => 'user_id',
        ],
    ],
];

$dictionary['TeamSet'] = [
    'table' => 'team_sets',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 128,
        ],
        'team_md5' => [
            'name' => 'team_md5',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 32,
        ],
        'team_count' => [
            'name' => 'team_count',
            'type' => 'int',
            'default' => 0,
        ],
        'primary_team_id' => [
            'name' => 'primary_team_id',
            'type' => 'id',
            'required' => true,
            'source' => 'non-db',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => 1,
            'default' => 0,
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => true,
        ],
        'teams' => [
            'name' => 'teams',
            'type' => 'link',
            'relationship' => 'team_sets_teams',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNT',
            'duplicate_merge' => 'disabled',
        ],
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'adminFor' => 'Users',
            'allowUserRead' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'team_setspk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'idx_team_sets_md5',
            'type' => 'index',
            'fields' => ['team_md5'],
        ],
    ],
];

$dictionary['TeamSetModule'] = [
    'table' => 'team_sets_modules',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'team_set_id' => [
            'name' => 'team_set_id',
            'type' => 'id',
            'isnull' => false,
            'required' => true,
        ],
        'module_table_name' => [
            'name' => 'module_table_name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 128,
            'isnull' => false,
            'required' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => 1,
            'default' => 0,
        ],
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'adminFor' => 'Users',
            'allowUserRead' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'team_sets_modulespk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'idx_team_sets_modules',
            'type' => 'index',
            'fields' => ['team_set_id'],
        ],
    ],
];
