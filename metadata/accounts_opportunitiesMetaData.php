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

$dictionary['accounts_opportunities'] = [
    'table' => 'accounts_opportunities',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
            'type' => 'id',
        ],
        'account_id' => [
            'name' => 'account_id',
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
            'name' => 'accounts_opportunitiespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_account_opportunity',
            'type' => 'alternate_key',
            'fields' => [
                'account_id',
                'opportunity_id',
            ],
        ],
        [
            'name' => 'idx_oppid_del_accid',
            'type' => 'index',
            'fields' => [
                'opportunity_id',
                'deleted',
                'account_id',
            ],
        ],
    ],
    'relationships' => [
        'accounts_opportunities' => [
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many',
            'join_table' => 'accounts_opportunities',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'opportunity_id',
            'true_relationship_type' => 'one-to-many',
        ],
    ],
];
