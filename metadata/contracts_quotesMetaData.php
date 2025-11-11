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

$dictionary['contracts_quotes'] = [
    'table' => 'contracts_quotes',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'quote_id' => [
            'name' => 'quote_id',
            'type' => 'id',
        ],
        'contract_id' => [
            'name' => 'contract_id',
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
            'name' => 'contracts_quot_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'contracts_quot_alt',
            'type' => 'alternate_key',
            'fields' => [
                'contract_id',
                'quote_id',
            ],
        ],
    ],
    'relationships' => [
        'contracts_quotes' => [
            'lhs_module' => 'Contracts',
            'lhs_table' => 'contracts',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contracts_quotes',
            'join_key_lhs' => 'contract_id',
            'join_key_rhs' => 'quote_id',
        ],
    ],
];
