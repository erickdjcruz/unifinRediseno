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

$dictionary['documents_opportunities'] = [
    'true_relationship_type' => 'many-to-many',
    'relationships' => [
        'documents_opportunities' => [
            'lhs_module' => 'Documents',
            'lhs_table' => 'documents',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'documents_opportunities',
            'join_key_lhs' => 'document_id',
            'join_key_rhs' => 'opportunity_id',
        ],
    ],
    'table' => 'documents_opportunities',
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
        'document_id' => [
            'name' => 'document_id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
            'type' => 'id',
        ],
    ],
    'indices' => [
        [
            'name' => 'documents_opportunitiesspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_docu_opps_oppo_id',
            'type' => 'alternate_key',
            'fields' => [
                'opportunity_id',
                'document_id',
            ],
        ],
        [
            'name' => 'idx_docu_oppo_docu_id',
            'type' => 'alternate_key',
            'fields' => [
                'document_id',
                'opportunity_id',
            ],
        ],
    ],
];
