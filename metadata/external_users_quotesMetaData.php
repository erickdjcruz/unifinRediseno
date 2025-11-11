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

$dictionary['external_users_quotes'] = [
    'table' => 'external_users_quotes',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'external_user_id' => [
            'name' => 'external_user_id',
            'type' => 'id',
        ],
        'quote_id' => [
            'name' => 'quote_id',
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
            'name' => 'external_users_quotespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_ext_user_qt_user',
            'type' => 'index',
            'fields' => [
                'external_user_id',
                'quote_id',
            ],
        ],
        [
            'name' => 'idx_ext_user_qt_qt',
            'type' => 'index',
            'fields' => [
                'quote_id',
            ],
        ],
    ],
    'relationships' => [
        'external_users_quotes' => [
            'lhs_module' => 'ExternalUsers',
            'lhs_table' => 'external_users',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'external_users_quotes',
            'join_key_lhs' => 'external_user_id',
            'join_key_rhs' => 'quote_id',
        ],
    ],
];
