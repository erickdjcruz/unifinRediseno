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

$dictionary['quotes_opportunities'] = [
    'table' => 'quotes_opportunities',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
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
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'quotes_opportunitiespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_opp_qte_opp',
            'type' => 'index',
            'fields' => [
                'opportunity_id',
            ],
        ],
        [
            'name' => 'idx_quote_oportunities',
            'type' => 'alternate_key',
            'fields' => [
                'quote_id',
            ],
        ],
    ],
    'relationships' => [
        'quotes_opportunities' => [
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'true_relationship_type' => 'one-to-many',
            'join_table' => 'quotes_opportunities',
            'join_key_lhs' => 'opportunity_id',
            'join_key_rhs' => 'quote_id',
        ],
    ],
];
