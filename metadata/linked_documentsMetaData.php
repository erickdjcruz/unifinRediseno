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

$dictionary['linked_documents'] = [
    'table' => 'linked_documents',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
        ],
        'parent_type' => [
            'name' => 'parent_type',
            'type' => 'varchar',
            'len' => '25',
        ],
        'document_id' => [
            'name' => 'document_id',
            'type' => 'id',
        ],
        'document_revision_id' => [
            'name' => 'document_revision_id',
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
            'name' => 'linked_documentspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_parent_document',
            'type' => 'alternate_key',
            'fields' => [
                'parent_type',
                'parent_id',
                'document_id',
            ],
        ],
    ],
    'relationships' => [
        'contracts_documents' => [
            'lhs_module' => 'Contracts',
            'lhs_table' => 'contracts',
            'lhs_key' => 'id',
            'rhs_module' => 'Documents',
            'rhs_table' => 'documents',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'linked_documents',
            'join_key_lhs' => 'parent_id',
            'join_key_rhs' => 'document_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contracts',
        ],
        'contracttype_documents' => [
            'lhs_module' => 'ContractTypes',
            'lhs_table' => 'contract_types',
            'lhs_key' => 'id',
            'rhs_module' => 'Documents',
            'rhs_table' => 'documents',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'linked_documents',
            'join_key_lhs' => 'parent_id',
            'join_key_rhs' => 'document_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'ContracTemplates',
        ],
    ],
];
