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

$dictionary['shift_exceptions_users'] = [
    'table' => 'shift_exceptions_users',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'shift_exception_id' => [
            'name' => 'shift_exception_id',
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
            'name' => 'shift_exceptions_users_id',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'shift_exceptions_idx_user_id',
            'type' => 'alternate_key',
            'fields' => [
                'shift_exception_id',
                'user_id',
            ],
        ],
    ],
    'relationships' => [
        'shift_exceptions_users' => [
            'lhs_module' => 'ShiftExceptions',
            'lhs_table' => 'shift_exceptions',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'shift_exceptions_users',
            'join_key_lhs' => 'shift_exception_id',
            'join_key_rhs' => 'user_id',
            'true_relationship_type' => 'many-to-many',
        ],
    ],
];
