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

$dictionary['cases_purchases'] = [
    'table' => 'cases_purchases',
    'true_relationship_type' => 'many-to-many',
    'relationships' => [
        'cases_purchases' => [
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Purchases',
            'rhs_table' => 'purchases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'cases_purchases',
            'join_key_lhs' => 'case_id',
            'join_key_rhs' => 'purchase_id',
        ],
    ],
    'fields' => [
        'id' => [
            'name' => 'id',
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
            'required' => true,
        ],
        'case_id' => [
            'name' => 'case_id',
            'type' => 'id',
        ],
        'purchase_id' => [
            'name' => 'purchase_id',
            'type' => 'id',
        ],
    ],
    'indices' => [
        [
            'name' => 'cases_purchasesspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'cases_purchases_purchase_id',
            'type' => 'alternate_key',
            'fields' => [
                'purchase_id',
                'case_id',
            ],
        ],
        [
            'name' => 'cases_purchases_case_id',
            'type' => 'alternate_key',
            'fields' => [
                'case_id',
                'purchase_id',
            ],
        ],
    ],
];
