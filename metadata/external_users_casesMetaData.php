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

$dictionary['external_users_cases'] = [
    'table' => 'external_users_cases',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'external_user_id' => [
            'name' => 'external_user_id',
            'type' => 'id',
        ],
        'case_id' => [
            'name' => 'case_id',
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
            'required' => false,
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'external_users_casespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_ext_user_case_user',
            'type' => 'index',
            'fields' => [
                'external_user_id',
                'case_id',
            ],
        ],
        [
            'name' => 'idx_ext_user_case_case',
            'type' => 'index',
            'fields' => [
                'case_id',
            ],
        ],
    ],
    'relationships' => [
        'external_users_cases' => [
            'lhs_module' => 'ExternalUsers',
            'lhs_table' => 'external_users',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'external_users_cases',
            'join_key_lhs' => 'external_user_id',
            'join_key_rhs' => 'case_id',
        ],
    ],
];
