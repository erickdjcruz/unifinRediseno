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

$dictionary['accounts_escalations'] = [
    'true_relationship_type' => 'many-to-many',
    'table' => 'accounts_escalations',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'account_id' => [
            'name' => 'account_id',
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
            'name' => 'accounts_escalationspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
    ],
    'relationships' => [
        'accounts_escalations' => [
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'Escalations',
            'rhs_table' => 'escalations',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'accounts_escalations',
            'join_key_lhs' => 'account_id',
            'join_key_rhs' => 'escalation_id',
        ],
    ],
];
