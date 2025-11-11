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

$dictionary['roles_users'] = [
    'table' => 'roles_users',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'role_id' => [
            'name' => 'role_id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
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
            'name' => 'roles_userspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_ru_role_id',
            'type' => 'index',
            'fields' => [
                'role_id',
            ],
        ],
        [
            'name' => 'idx_ru_user_id',
            'type' => 'index',
            'fields' => [
                'user_id',
            ],
        ],
    ],
    'relationships' => [
        'roles_users' => [
            'lhs_module' => 'Roles',
            'lhs_table' => 'roles',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'roles_users',
            'join_key_lhs' => 'role_id',
            'join_key_rhs' => 'user_id',
        ],
    ],
];
