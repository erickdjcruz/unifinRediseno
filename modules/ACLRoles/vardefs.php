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


$dictionary['ACLRole'] = [
    'table' => 'acl_roles',
    'color' => 'teal',
    'icon' => 'sicon-role-mgmt-lg',
    'archive' => false,
    'comment' => 'ACL Role definition',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'required' => true,
            'type' => 'id',
            'reportable' => false,
            'comment' => 'Unique identifier',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'modified_user_id_users',
            'isnull' => 'false',
            'dbType' => 'id',
            'required' => false,
            'len' => 36,
            'reportable' => true,
            'comment' => 'User who last modified record',
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'created_by_users',
            'isnull' => 'false',
            'dbType' => 'id',
            'len' => 36,
            'comment' => 'User who created record',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'vname' => 'LBL_NAME',
            'len' => 150,
            'comment' => 'The role name',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'The role description',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ],
        'users' => [
            'name' => 'users',
            'type' => 'link',
            'relationship' => 'acl_roles_users',
            'link_class' => 'UserLink',
            'source' => 'non-db',
            'vname' => 'LBL_USERS',
        ],
        'actions' => [
            'name' => 'actions',
            'type' => 'link',
            'relationship' => 'acl_roles_actions',
            'source' => 'non-db',
            'module' => 'ACLActions',
            'bean_name' => 'ACLAction',
            'rel_fields' => [
                'access_override' => [
                    'type' => 'int',
                ],
            ],
        ],
        'access_override' => [
            'name' => 'access_override',
            'type' => 'int',
            'studio' => 'false',
            'source' => 'non-db',
            'massupdate' => false,
            'link' => 'actions',
            'rname_link' => 'access_override',
            'link_type' => 'one',
        ],
        'acl_role_sets' => [
            'name' => 'acl_role_sets',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'acl_role_sets_acl_roles',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => ['aclModule' => 'Users']],
    'indices' => [
        ['name' => 'aclrolespk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_aclrole_id_del', 'type' => 'index', 'fields' => ['id', 'deleted']],
        ['name' => 'idx_aclrole_name', 'type' => 'index', 'fields' => ['name']],
    ],
];

$dictionary['ACLRoleSet'] = [
    'table' => 'acl_role_sets',
    'archive' => false,
    'fields' => [
        'hash' => [
            'name' => 'hash',
            'type' => 'varchar',
            'len' => 32,
            'isnull' => false,
        ],
        'acl_roles' => [
            'name' => 'acl_roles',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'acl_role_sets_acl_roles',
            'duplicate_merge' => 'disabled',
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_acl_role_sets_hash',
            'type' => 'unique',
            'fields' => ['hash'],
        ],
    ],
];

VardefManager::createVardef('ACLRoleSets', 'ACLRoleSet');
