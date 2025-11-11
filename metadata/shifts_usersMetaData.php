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

$dictionary['shifts_users'] = [
    'table' => 'shifts_users',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'shift_id' => [
            'name' => 'shift_id',
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
            'name' => 'shifts_users_id',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'shift_idx_user_id',
            'type' => 'alternate_key',
            'fields' => [
                'shift_id',
                'user_id',
            ],
        ],
    ],
    'relationships' => [
        'shifts_users' => [
            'lhs_module' => 'Shifts',
            'lhs_table' => 'shifts',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'shifts_users',
            'join_key_lhs' => 'shift_id',
            'join_key_rhs' => 'user_id',
            'true_relationship_type' => 'many-to-many',
        ],
    ],
];
