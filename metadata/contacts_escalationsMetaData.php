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

$dictionary['contacts_escalations'] = [
    'table' => 'contacts_escalations',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'escalation_id' => [
            'name' => 'escalation_id',
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
            'name' => 'contacts_escalationspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_con_escalation_escalation',
            'type' => 'index',
            'fields' => [
                'escalation_id',
            ],
        ],
        [
            'name' => 'idx_contacts_escalations',
            'type' => 'alternate_key',
            'fields' => [
                'contact_id',
                'escalation_id',
            ],
        ],
    ],
    'relationships' => [
        'contacts_escalations' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Escalations',
            'rhs_table' => 'escalations',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'contacts_escalations',
            'join_key_lhs' => 'contact_id',
            'join_key_rhs' => 'escalation_id',
        ],
    ],
];
