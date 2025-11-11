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

$dictionary['calls_users'] = [
    'table' => 'calls_users',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'call_id' => [
            'name' => 'call_id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'required' => [
            'name' => 'required',
            'type' => 'varchar',
            'len' => '1',
            'default' => '1',
        ],
        'accept_status' => [
            'name' => 'accept_status',
            'type' => 'varchar',
            'len' => '25',
            'default' => 'none',
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
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'calls_userspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_usr_call_usr',
            'type' => 'index',
            'fields' => [
                'user_id',
            ],
        ],
        [
            'name' => 'idx_call_users_del',
            'type' => 'alternate_key',
            'fields' => [
                'call_id',
                'user_id',
                'deleted',
            ],
        ],
    ],
    'relationships' => [
        'calls_users' => [
            'lhs_module' => 'Calls',
            'lhs_table' => 'calls',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'calls_users',
            'join_key_lhs' => 'call_id',
            'join_key_rhs' => 'user_id',
        ],
    ],
];
