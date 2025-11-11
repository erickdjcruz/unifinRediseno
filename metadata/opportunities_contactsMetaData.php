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

$dictionary['opportunities_contacts'] = [
    'table' => 'opportunities_contacts',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
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
            'name' => 'opportunities_contactspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_con_opp_con',
            'type' => 'index',
            'fields' => [
                'contact_id',
            ],
        ],
        [
            'name' => 'idx_opportunities_contacts',
            'type' => 'alternate_key',
            'fields' => [
                'opportunity_id',
                'contact_id',
            ],
        ],
        [
            'name' => 'idx_del_opp_con',
            'type' => 'alternate_key',
            'fields' => [
                'deleted',
                'opportunity_id',
                'contact_id',
            ],
        ],
    ],
    'relationships' => [
        'opportunities_contacts' => [
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'opportunities_contacts',
            'join_key_lhs' => 'opportunity_id',
            'join_key_rhs' => 'contact_id',
        ],
    ],
];
