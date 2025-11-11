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

$dictionary['contracts_opportunities'] = [
    'table' => 'contracts_opportunities',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
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
            'name' => 'contracts_opp_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'contracts_opp_alt',
            'type' => 'alternate_key',
            'fields' => [
                'contract_id',
            ],
        ],
    ],
    'relationships' => [
        'contracts_opportunities' => [
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Contracts',
            'rhs_table' => 'contracts',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'join_table' => 'contracts_opportunities',
            'join_key_lhs' => 'opportunity_id',
            'join_key_rhs' => 'contract_id',
            'true_relationship_type' => 'one-to-many',
        ],
    ],
];
