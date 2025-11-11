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

$dictionary['contacts_bugs'] = [
    'table' => 'contacts_bugs',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'bug_id' => [
            'name' => 'bug_id',
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
            'name' => 'contacts_bugspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_con_bug_bug',
            'type' => 'index',
            'fields' => [
                'bug_id',
            ],
        ],
        [
            'name' => 'idx_contact_bug',
            'type' => 'alternate_key',
            'fields' => [
                'contact_id',
                'bug_id',
            ],
        ],
    ],
    'relationships' => [
        'contacts_bugs' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Bugs',
            'rhs_table' => 'bugs',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_bugs',
            'join_key_lhs' => 'contact_id',
            'join_key_rhs' => 'bug_id',
        ],
    ],
];
