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

$dictionary['meetings_contacts'] = [
    'table' => 'meetings_contacts',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'meeting_id' => [
            'name' => 'meeting_id',
            'type' => 'id',
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'required' => [
            'name' => 'required',
            'type' => 'varchar',
            'len' => '1',
            'default' => '1',
        ],
        'accept_status' => [
            'name' => 'accept_status',
            'type' => 'varchar',
            'len' => '25',
            'default' => 'none',
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
            'name' => 'meetings_contactspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_con_mtg_con',
            'type' => 'index',
            'fields' => [
                'contact_id',
            ],
        ],
        [
            'name' => 'idx_meeting_contact',
            'type' => 'alternate_key',
            'fields' => [
                'meeting_id',
                'contact_id',
            ],
        ],
    ],
    'relationships' => [
        'meetings_contacts' => [
            'lhs_module' => 'Meetings',
            'lhs_table' => 'meetings',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'meetings_contacts',
            'join_key_lhs' => 'meeting_id',
            'join_key_rhs' => 'contact_id',
        ],
    ],
];
