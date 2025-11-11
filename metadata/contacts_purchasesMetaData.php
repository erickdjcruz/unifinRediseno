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

$dictionary['contacts_purchases'] = [
    'table' => 'contacts_purchases',
    'true_relationship_type' => 'many-to-many',
    'relationships' => [
        'contacts_purchases' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Purchases',
            'rhs_table' => 'purchases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_purchases',
            'join_key_lhs' => 'contact_id',
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
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'purchase_id' => [
            'name' => 'purchase_id',
            'type' => 'id',
        ],
    ],
    'indices' => [
        [
            'name' => 'contacts_purchasesspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'contacts_purchases_purchase_id',
            'type' => 'alternate_key',
            'fields' => [
                'purchase_id',
                'contact_id',
            ],
        ],
        [
            'name' => 'contacts_purchases_contact_id',
            'type' => 'alternate_key',
            'fields' => [
                'contact_id',
                'purchase_id',
            ],
        ],
    ],
];
