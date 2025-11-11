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
$dictionary['Document'] = [
    'table' => 'documents',
    'color' => 'coral',
    'icon' => 'sicon-document-lg',
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'audited' => true,
    'fields' => [

        'document_name' => [
            'name' => 'document_name',
            'vname' => 'LBL_DOCUMENT_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
            'importable' => 'required',
            'unified_search' => true,
            'full_text_search' => ['enabled' => true, 'searchable' => true, 'boost' => 0.82],
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'source' => 'non-db',
            'type' => 'varchar',
            'fields' => ['document_name'],
            'sort_on' => 'name',
            'required' => true,
            'massupdate' => false,
        ],
        'doc_id' => [
            'name' => 'doc_id',
            'vname' => 'LBL_DOC_ID',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Document ID from documents web server provider',
            'importable' => false,
            'studio' => 'false',
            'massupdate' => false,
        ],
        'doc_type' => [
            'name' => 'doc_type',
            'vname' => 'LBL_DOC_TYPE',
            'type' => 'enum',
            'function' => 'getDocumentsExternalApiDropDown',
            'len' => '100',
            'comment' => 'Document type (ex: Google, box.net, IBM SmartCloud)',
            'popupHelp' => 'LBL_DOC_TYPE_POPUP',
            'massupdate' => false,
            'default' => 'Sugar',
            'studio' => ['wirelesseditview' => false, 'wirelessdetailview' => false, 'wirelesslistview' => false, 'wireless_basic_search' => false],
            'readonly' => true,
            'readonly_formula' => 'not(equal($filename, ""))',
        ],
        'doc_url' => [
            'name' => 'doc_url',
            'vname' => 'LBL_DOC_URL',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Document URL from documents web server provider',
            'importable' => false,
            'massupdate' => false,
            'studio' => 'false',
        ],
        'filename' => [
            'name' => 'filename',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'source' => 'non-db',
            'comment' => 'The filename of the document attachment',
            'required' => true,
            'noChange' => true,
            'allowEapm' => true,
            'fileId' => 'document_revision_id',
            'docType' => 'doc_type',
            'docUrl' => 'doc_url',
            'docId' => 'doc_id',
            'sort_on' => ['document_name'],
        ],

        'active_date' => [
            'name' => 'active_date',
            'vname' => 'LBL_DOC_ACTIVE_DATE',
            'type' => 'date',
            'importable' => 'required',
            'required' => true,
            'display_default' => 'now',
        ],

        'exp_date' => [
            'name' => 'exp_date',
            'vname' => 'LBL_DOC_EXP_DATE',
            'type' => 'date',
        ],

        'category_id' => [
            'name' => 'category_id',
            'vname' => 'LBL_SF_CATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_category_dom',
            'reportable' => true,
        ],

        'subcategory_id' => [
            'name' => 'subcategory_id',
            'vname' => 'LBL_SF_SUBCATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_subcategory_dom',
            'reportable' => true,
        ],

        'is_shared' => [
            'name' => 'is_shared',
            'vname' => 'LBL_IS_SHARED',
            'type' => 'bool',
            'audited' => true,
        ],

        'status_id' => [
            'name' => 'status_id',
            'vname' => 'LBL_DOC_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_status_dom',
            'reportable' => false,
        ],

        'status' => [
            'name' => 'status',
            'vname' => 'LBL_DOC_STATUS',
            'type' => 'varchar',
            'source' => 'non-db',
            'comment' => 'Document status for Meta-Data framework',
            'studio' => 'false',
            'massupdate' => false,
        ],

        'document_revision_id' => [
            'name' => 'document_revision_id',
            'vname' => 'LBL_DOCUMENT_REVISION_ID',
            'type' => 'id',
            'reportable' => false,
        ],

        'revisions' => [
            'name' => 'revisions',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'source' => 'non-db',
            'vname' => 'LBL_REVISIONS',
        ],

        'latest_document_revision_link' => [
            'name' => 'latest_document_revision_link',
            'type' => 'link',
            'relationship' => 'latest_document_revision',
            'source' => 'non-db',
            'vname' => 'LBL_LATEST_REVISION',
        ],

        'revision' => [
            'name' => 'revision',
            'vname' => 'LBL_DOC_VERSION',
            'type' => 'varchar',
            'reportable' => false,
            'required' => true,
            'source' => 'non-db',
            'importable' => 'required',
            'default' => '1',
            'readonly' => true,
        ],

        'last_rev_created_name' => [
            'name' => 'last_rev_created_name',
            'vname' => 'LBL_LAST_REV_CREATOR',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'last_rev_mime_type' => [
            'name' => 'last_rev_mime_type',
            'vname' => 'LBL_LAST_REV_MIME_TYPE',
            'type' => 'varchar',
            'reportable' => false,
            'studio' => 'false',
            'source' => 'non-db',
            'massupdate' => false,
        ],
        'latest_revision' => [
            'name' => 'latest_revision',
            'vname' => 'LBL_LATEST_REVISION',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'latest_revision_file_mime_type' => [
            'name' => 'latest_revision_file_mime_type',
            'type' => 'relate',
            'link' => 'latest_document_revision_link',
            'id_name' => 'document_revision_id',
            'module' => 'DocumentRevisions',
            'rname' => 'file_mime_type',
            'source' => 'non-db',
            'vname' => 'LBL_MIME',
            'massupdate' => false,
        ],
        'latest_revision_file_size' => [
            'name' => 'latest_revision_file_size',
            'type' => 'relate',
            'link' => 'latest_document_revision_link',
            'id_name' => 'document_revision_id',
            'module' => 'DocumentRevisions',
            'rname' => 'file_size',
            'source' => 'non-db',
            'vname' => 'LBL_FILE_SIZE',
            'massupdate' => false,
        ],
        'latest_revision_file_ext' => [
            'name' => 'latest_revision_file_ext',
            'type' => 'relate',
            'link' => 'latest_document_revision_link',
            'id_name' => 'document_revision_id',
            'module' => 'DocumentRevisions',
            'rname' => 'file_ext',
            'source' => 'non-db',
            'vname' => 'LBL_FILE_EXTENSION',
            'massupdate' => false,
        ],
        'last_rev_create_date' => [
            'name' => 'last_rev_create_date',
            'type' => 'relate',
            'table' => 'document_revisions',
            'link' => 'latest_document_revision_link',
            'id_name' => 'document_revision_id',
            'join_name' => 'document_revisions',
            'vname' => 'LBL_LAST_REV_CREATE_DATE',
            'rname' => 'date_entered',
            'module' => 'DocumentRevisions',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'contracts' => [
            'name' => 'contracts',
            'type' => 'link',
            'relationship' => 'contracts_documents',
            'source' => 'non-db',
            'vname' => 'LBL_CONTRACTS',
        ],
        'contracttypes' => [
            'name' => 'contracttypes',
            'type' => 'link',
            'relationship' => 'contracttype_documents',
            'source' => 'non-db',
            'vname' => 'LBL_CONTRACTTYPES',
        ],
        // Links around the world
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'documents_accounts',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'documents_contacts',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS_SUBPANEL_TITLE',
        ],
        'opportunities' => [
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'documents_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
        ],
        'cases' => [
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'documents_cases',
            'source' => 'non-db',
            'vname' => 'LBL_CASES_SUBPANEL_TITLE',
        ],
        'bugs' => [
            'name' => 'bugs',
            'type' => 'link',
            'relationship' => 'documents_bugs',
            'source' => 'non-db',
            'vname' => 'LBL_BUGS_SUBPANEL_TITLE',
        ],
        'quotes' => [
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'documents_quotes',
            'source' => 'non-db',
            'vname' => 'LBL_QUOTES_SUBPANEL_TITLE',
        ],
        'products' => [
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'documents_products',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTS_SUBPANEL_TITLE',
        ],
        'revenuelineitems' => [
            'name' => 'revenuelineitems',
            'type' => 'link',
            'relationship' => 'documents_revenuelineitems',
            'source' => 'non-db',
            'vname' => 'LBL_RLI_SUBPANEL_TITLE',
            'workflow' => false,
        ],
        'purchases' => [
            'name' => 'purchases',
            'type' => 'link',
            'relationship' => 'documents_purchases',
            'source' => 'non-db',
            'vname' => 'LBL_PURCHASES_SUBPANEL_TITLE',
        ],
        'purchasedlineitems' => [
            'name' => 'purchasedlineitems',
            'type' => 'link',
            'relationship' => 'documents_purchasedlineitems',
            'source' => 'non-db',
            'vname' => 'LBL_PLIS_SUBPANEL_TITLE',
        ],
        'escalations' => [
            'name' => 'escalations',
            'type' => 'link',
            'relationship' => 'documents_escalations',
            'source' => 'non-db',
            'vname' => 'LBL_ESCALATIONS',
        ],
        'related_doc_id' => [
            'name' => 'related_doc_id',
            'vname' => 'LBL_RELATED_DOCUMENT_ID',
            'reportable' => false,
            'type' => 'id',
        ],

        'related_doc_name' => [
            'name' => 'related_doc_name',
            'vname' => 'LBL_DET_RELATED_DOCUMENT',
            'type' => 'relate',
            'table' => 'documents',
            'link' => 'related_docs',
            'id_name' => 'related_doc_id',
            'rname' => 'document_name',
            'module' => 'Documents',
            'source' => 'non-db',
            'comment' => 'The related document name for Meta-Data framework',
        ],

        'related_doc_rev_id' => [
            'name' => 'related_doc_rev_id',
            'link' => 'related_docs',
            'rname' => 'document_revision_id',
            'id_name' => 'related_doc_id',
            'vname' => 'LBL_RELATED_DOCUMENT_REVISION_ID',
            'reportable' => false,
            'dbType' => 'id',
            'type' => 'id',
        ],

        'related_doc_rev_number' => [
            'name' => 'related_doc_rev_number',
            'type' => 'relate',
            'link' => 'revisions',
            'rname' => 'revision',
            'id_name' => 'related_doc_rev_id',
            'table' => 'document_revisions',
            'join_name' => 'document_revisions',
            'vname' => 'LBL_DET_RELATED_DOCUMENT_VERSION',
            'source' => 'non-db',
            'readonly' => true,
            'readonly_formula' => 'equal($related_doc_id, "")',
            'comment' => 'The related document version number for Meta-Data framework',
            'module' => 'DocumentRevisions', // If the module is not set, sidecar should look at the link to determine the module. This is just temp solution.
        ],

        'is_template' => [
            'name' => 'is_template',
            'vname' => 'LBL_IS_TEMPLATE',
            'type' => 'bool',
            'default' => 0,
            'reportable' => false,
        ],
        'template_type' => [
            'name' => 'template_type',
            'vname' => 'LBL_TEMPLATE_TYPE',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_template_type_dom',
            'reportable' => false,
        ],
//BEGIN field used for contract document subpanel.
        'latest_revision_name' => [
            'name' => 'latest_revision_name',
            'vname' => 'LBL_LASTEST_REVISION_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],

        'selected_revision_name' => [
            'name' => 'selected_revision_name',
            'vname' => 'LBL_SELECTED_REVISION_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'contract_status' => [
            'name' => 'contract_status',
            'vname' => 'LBL_CONTRACT_STATUS',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'contract_name' => [
            'name' => 'contract_name',
            'vname' => 'LBL_CONTRACT_NAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'linked_id' => [
            'name' => 'linked_id',
            'vname' => 'LBL_LINKED_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
        ],
        'selected_revision_id' => [
            'name' => 'selected_revision_id',
            'vname' => 'LBL_SELECTED_REVISION_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
        ],
        'latest_revision_id' => [
            'name' => 'latest_revision_id',
            'vname' => 'LBL_LATEST_REVISION_ID',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
            'massupdate' => false,
        ],
        'selected_revision_filename' => [
            'name' => 'selected_revision_filename',
            'vname' => 'LBL_SELECTED_REVISION_FILENAME',
            'type' => 'varchar',
            'reportable' => false,
            'source' => 'non-db',
        ],
        'related_docs' => [
            'name' => 'related_docs',
            'type' => 'link',
            'link_type' => 'one',
            'relationship' => 'related_documents',
            'source' => 'non-db',
            'vname' => 'LBL_DET_RELATED_DOCUMENT',
        ],
//END fields used for contract documents subpanel.

    ],
    'indices' => [
        ['name' => 'idx_doc_cat', 'type' => 'index', 'fields' => ['category_id', 'subcategory_id']],
        ['name' => 'idx_document_doc_type', 'type' => 'index', 'fields' => ['doc_type']],
        ['name' => 'idx_document_exp_date', 'type' => 'index', 'fields' => ['exp_date']],
        ['name' => 'idx_documents_related_doc_id_deleted', 'type' => 'index', 'fields' => [
            'related_doc_id',
            'deleted',
        ]],
    ],
    'relationships' => [
        'related_documents' => ['lhs_module' => 'Documents', 'lhs_table' => 'documents', 'lhs_key' => 'id',
            'rhs_module' => 'Documents', 'rhs_table' => 'documents', 'rhs_key' => 'related_doc_id',
            'relationship_type' => 'one-to-many']
        , 'document_revisions' => ['lhs_module' => 'Documents', 'lhs_table' => 'documents', 'lhs_key' => 'id',
            'rhs_module' => 'DocumentRevisions', 'rhs_table' => 'document_revisions', 'rhs_key' => 'document_id',
            'relationship_type' => 'one-to-many']
        , 'latest_document_revision' => [
            'lhs_module' => 'Documents', 'lhs_table' => 'documents', 'lhs_key' => 'document_revision_id',
            'rhs_module' => 'DocumentRevisions', 'rhs_table' => 'document_revisions', 'rhs_key' => 'id',
            'relationship_type' => 'one-to-one',
        ]

        , 'documents_modified_user' => ['lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Documents', 'rhs_table' => 'documents', 'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many']

        , 'documents_created_by' => ['lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Documents', 'rhs_table' => 'documents', 'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'],
    ],

];
VardefManager::createVardef('Documents', 'Document', ['default', 'assignable',
    'team_security', 'audit',
]);

//boost value for full text search
$dictionary['Document']['fields']['description']['full_text_search']['boost'] = 0.61;
