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

$dictionary['acl_roles_actions'] = [
    'table' => 'acl_roles_actions',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'role_id' => [
            'name' => 'role_id',
            'type' => 'id',
        ],
        'action_id' => [
            'name' => 'action_id',
            'type' => 'id',
        ],
        'access_override' => [
            'name' => 'access_override',
            'type' => 'int',
            'len' => '3',
            'required' => false,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'acl_roles_actionspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_acl_role_id',
            'type' => 'index',
            'fields' => [
                'role_id',
            ],
        ],
        [
            'name' => 'idx_acl_action_id',
            'type' => 'index',
            'fields' => [
                'action_id',
            ],
        ],
        [
            'name' => 'idx_del_override',
            'type' => 'index',
            'fields' => [
                'role_id',
                'deleted',
                'action_id',
                'access_override',
            ],
        ],
    ],
    'relationships' => [
        'acl_roles_actions' => [
            'lhs_module' => 'ACLRoles',
            'lhs_table' => 'acl_roles',
            'lhs_key' => 'id',
            'rhs_module' => 'ACLActions',
            'rhs_table' => 'acl_actions',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'acl_roles_actions',
            'join_key_lhs' => 'role_id',
            'join_key_rhs' => 'action_id',
        ],
    ],
];
