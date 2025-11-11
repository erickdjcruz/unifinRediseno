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

$dictionary['users_holidays'] = [
    'table' => 'users_holidays',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'holiday_id' => [
            'name' => 'holiday_id',
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
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'users_holidays_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_user_holi_holi',
            'type' => 'index',
            'fields' => [
                'holiday_id',
            ],
        ],
        [
            'name' => 'users_quotes_alt',
            'type' => 'alternate_key',
            'fields' => [
                'user_id',
                'holiday_id',
            ],
        ],
    ],
    'relationships' => [
        'users_holidays' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Holidays',
            'rhs_table' => 'holidays',
            'rhs_key' => 'person_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'related_module',
            'relationship_role_column_value' => null,
        ],
    ],
];
