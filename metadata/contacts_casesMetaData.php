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

$dictionary['contacts_cases'] = [
    'table' => 'contacts_cases',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'case_id' => [
            'name' => 'case_id',
            'type' => 'id',
        ],
        'contact_role' => [
            'name' => 'contact_role',
            'type' => 'varchar',
            'len' => '50',
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
            'name' => 'contacts_casespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_con_case_case',
            'type' => 'index',
            'fields' => [
                'case_id',
            ],
        ],
        [
            'name' => 'idx_contacts_cases',
            'type' => 'alternate_key',
            'fields' => [
                'contact_id',
                'case_id',
            ],
        ],
    ],
    'relationships' => [
        'contacts_cases' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_cases',
            'join_key_lhs' => 'contact_id',
            'join_key_rhs' => 'case_id',
        ],
    ],
];
