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

$dictionary['projects_quotes'] = [
    'table' => 'projects_quotes',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'quote_id' => [
            'name' => 'quote_id',
            'type' => 'id',
        ],
        'project_id' => [
            'name' => 'project_id',
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
            'name' => 'projects_quotes_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_proj_quote_quote',
            'type' => 'index',
            'fields' => [
                'quote_id',
            ],
        ],
        [
            'name' => 'projects_quotes_alt',
            'type' => 'alternate_key',
            'fields' => [
                'project_id',
                'quote_id',
            ],
        ],
    ],
    'relationships' => [
        'projects_quotes' => [
            'lhs_module' => 'Project',
            'lhs_table' => 'project',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_quotes',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'quote_id',
        ],
    ],
];
