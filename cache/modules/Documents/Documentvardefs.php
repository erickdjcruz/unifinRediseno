<?php 
 $GLOBALS["dictionary"]["Document"]=array (
  'table' => 'documents',
  'color' => 'coral',
  'icon' => 'sicon-document-lg',
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'audited' => true,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => true,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Unique identifier',
      'mandatory_fetch' => true,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'source' => 'non-db',
      'type' => 'varchar',
      'fields' => 
      array (
        0 => 'document_name',
      ),
      'sort_on' => 'name',
      'required' => true,
      'massupdate' => false,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'group' => 'created_by_name',
      'comment' => 'Date record created',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'group' => 'modified_by_name',
      'comment' => 'Date record last modified',
      'enable_range_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'sortable' => true,
      ),
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'options' => 'date_range_search_dom',
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'group' => 'modified_by_name',
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'modified_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_MODIFIED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
      ),
    ),
    'modified_by_name' => 
    array (
      'name' => 'modified_by_name',
      'vname' => 'LBL_MODIFIED',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'rname' => 'full_name',
      'table' => 'users',
      'id_name' => 'modified_user_id',
      'module' => 'Users',
      'link' => 'modified_user_link',
      'duplicate_merge' => 'disabled',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_CREATED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'group' => 'created_by_name',
      'comment' => 'User who created record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'created_by' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_CREATED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
      ),
    ),
    'created_by_name' => 
    array (
      'name' => 'created_by_name',
      'vname' => 'LBL_CREATED',
      'type' => 'relate',
      'reportable' => false,
      'link' => 'created_by_link',
      'rname' => 'full_name',
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'created_by',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'comment' => 'Full text of the note',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.61,
      ),
      'rows' => 6,
      'cols' => 80,
      'duplicate_on_record_copy' => 'always',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Record deletion indicator',
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'documents_created_by',
      'vname' => 'LBL_CREATED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'modified_user_link' => 
    array (
      'name' => 'modified_user_link',
      'type' => 'link',
      'relationship' => 'documents_modified_user',
      'vname' => 'LBL_MODIFIED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'activities' => 
    array (
      'name' => 'activities',
      'type' => 'link',
      'relationship' => 'document_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'document_name' => 
    array (
      'name' => 'document_name',
      'vname' => 'LBL_DOCUMENT_NAME',
      'type' => 'varchar',
      'len' => '255',
      'required' => true,
      'importable' => 'required',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.82,
      ),
    ),
    'doc_id' => 
    array (
      'name' => 'doc_id',
      'vname' => 'LBL_DOC_ID',
      'type' => 'varchar',
      'len' => '100',
      'comment' => 'Document ID from documents web server provider',
      'importable' => false,
      'studio' => 'false',
      'massupdate' => false,
    ),
    'doc_type' => 
    array (
      'name' => 'doc_type',
      'vname' => 'LBL_DOC_TYPE',
      'type' => 'enum',
      'function' => 'getDocumentsExternalApiDropDown',
      'len' => '100',
      'comment' => 'Document type (ex: Google, box.net, IBM SmartCloud)',
      'popupHelp' => 'LBL_DOC_TYPE_POPUP',
      'massupdate' => false,
      'default' => 'Sugar',
      'studio' => 
      array (
        'wirelesseditview' => false,
        'wirelessdetailview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
      ),
      'readonly' => true,
      'readonly_formula' => 'not(equal($filename, ""))',
    ),
    'doc_url' => 
    array (
      'name' => 'doc_url',
      'vname' => 'LBL_DOC_URL',
      'type' => 'varchar',
      'len' => '255',
      'comment' => 'Document URL from documents web server provider',
      'importable' => false,
      'massupdate' => false,
      'studio' => 'false',
    ),
    'filename' => 
    array (
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
      'sort_on' => 
      array (
        0 => 'document_name',
      ),
    ),
    'active_date' => 
    array (
      'name' => 'active_date',
      'vname' => 'LBL_DOC_ACTIVE_DATE',
      'type' => 'date',
      'importable' => 'required',
      'required' => true,
      'display_default' => 'now',
    ),
    'exp_date' => 
    array (
      'name' => 'exp_date',
      'vname' => 'LBL_DOC_EXP_DATE',
      'type' => 'date',
    ),
    'category_id' => 
    array (
      'name' => 'category_id',
      'vname' => 'LBL_SF_CATEGORY',
      'type' => 'enum',
      'len' => 100,
      'options' => 'document_category_dom',
      'reportable' => true,
    ),
    'subcategory_id' => 
    array (
      'name' => 'subcategory_id',
      'vname' => 'LBL_SF_SUBCATEGORY',
      'type' => 'enum',
      'len' => 100,
      'options' => 'document_subcategory_dom',
      'reportable' => true,
    ),
    'is_shared' => 
    array (
      'name' => 'is_shared',
      'vname' => 'LBL_IS_SHARED',
      'type' => 'bool',
      'audited' => true,
    ),
    'status_id' => 
    array (
      'name' => 'status_id',
      'vname' => 'LBL_DOC_STATUS',
      'type' => 'enum',
      'len' => 100,
      'options' => 'document_status_dom',
      'reportable' => false,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_DOC_STATUS',
      'type' => 'varchar',
      'source' => 'non-db',
      'comment' => 'Document status for Meta-Data framework',
      'studio' => 'false',
      'massupdate' => false,
    ),
    'document_revision_id' => 
    array (
      'name' => 'document_revision_id',
      'vname' => 'LBL_DOCUMENT_REVISION_ID',
      'type' => 'id',
      'reportable' => false,
    ),
    'revisions' => 
    array (
      'name' => 'revisions',
      'type' => 'link',
      'relationship' => 'document_revisions',
      'source' => 'non-db',
      'vname' => 'LBL_REVISIONS',
    ),
    'latest_document_revision_link' => 
    array (
      'name' => 'latest_document_revision_link',
      'type' => 'link',
      'relationship' => 'latest_document_revision',
      'source' => 'non-db',
      'vname' => 'LBL_LATEST_REVISION',
    ),
    'revision' => 
    array (
      'name' => 'revision',
      'vname' => 'LBL_DOC_VERSION',
      'type' => 'varchar',
      'reportable' => false,
      'required' => true,
      'source' => 'non-db',
      'importable' => 'required',
      'default' => '1',
      'readonly' => true,
    ),
    'last_rev_created_name' => 
    array (
      'name' => 'last_rev_created_name',
      'vname' => 'LBL_LAST_REV_CREATOR',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'last_rev_mime_type' => 
    array (
      'name' => 'last_rev_mime_type',
      'vname' => 'LBL_LAST_REV_MIME_TYPE',
      'type' => 'varchar',
      'reportable' => false,
      'studio' => 'false',
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'latest_revision' => 
    array (
      'name' => 'latest_revision',
      'vname' => 'LBL_LATEST_REVISION',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'latest_revision_file_mime_type' => 
    array (
      'name' => 'latest_revision_file_mime_type',
      'type' => 'relate',
      'link' => 'latest_document_revision_link',
      'id_name' => 'document_revision_id',
      'module' => 'DocumentRevisions',
      'rname' => 'file_mime_type',
      'source' => 'non-db',
      'vname' => 'LBL_MIME',
      'massupdate' => false,
    ),
    'latest_revision_file_size' => 
    array (
      'name' => 'latest_revision_file_size',
      'type' => 'relate',
      'link' => 'latest_document_revision_link',
      'id_name' => 'document_revision_id',
      'module' => 'DocumentRevisions',
      'rname' => 'file_size',
      'source' => 'non-db',
      'vname' => 'LBL_FILE_SIZE',
      'massupdate' => false,
    ),
    'latest_revision_file_ext' => 
    array (
      'name' => 'latest_revision_file_ext',
      'type' => 'relate',
      'link' => 'latest_document_revision_link',
      'id_name' => 'document_revision_id',
      'module' => 'DocumentRevisions',
      'rname' => 'file_ext',
      'source' => 'non-db',
      'vname' => 'LBL_FILE_EXTENSION',
      'massupdate' => false,
    ),
    'last_rev_create_date' => 
    array (
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
    ),
    'contracts' => 
    array (
      'name' => 'contracts',
      'type' => 'link',
      'relationship' => 'contracts_documents',
      'source' => 'non-db',
      'vname' => 'LBL_CONTRACTS',
    ),
    'contracttypes' => 
    array (
      'name' => 'contracttypes',
      'type' => 'link',
      'relationship' => 'contracttype_documents',
      'source' => 'non-db',
      'vname' => 'LBL_CONTRACTTYPES',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'documents_accounts',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'documents_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_CONTACTS_SUBPANEL_TITLE',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'documents_opportunities',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'documents_cases',
      'source' => 'non-db',
      'vname' => 'LBL_CASES_SUBPANEL_TITLE',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'documents_bugs',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS_SUBPANEL_TITLE',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'documents_quotes',
      'source' => 'non-db',
      'vname' => 'LBL_QUOTES_SUBPANEL_TITLE',
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'relationship' => 'documents_products',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCTS_SUBPANEL_TITLE',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'documents_revenuelineitems',
      'source' => 'non-db',
      'vname' => 'LBL_RLI_SUBPANEL_TITLE',
      'workflow' => true,
    ),
    'purchases' => 
    array (
      'name' => 'purchases',
      'type' => 'link',
      'relationship' => 'documents_purchases',
      'source' => 'non-db',
      'vname' => 'LBL_PURCHASES_SUBPANEL_TITLE',
    ),
    'purchasedlineitems' => 
    array (
      'name' => 'purchasedlineitems',
      'type' => 'link',
      'relationship' => 'documents_purchasedlineitems',
      'source' => 'non-db',
      'vname' => 'LBL_PLIS_SUBPANEL_TITLE',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'documents_escalations',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'related_doc_id' => 
    array (
      'name' => 'related_doc_id',
      'vname' => 'LBL_RELATED_DOCUMENT_ID',
      'reportable' => false,
      'type' => 'id',
    ),
    'related_doc_name' => 
    array (
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
    ),
    'related_doc_rev_id' => 
    array (
      'name' => 'related_doc_rev_id',
      'link' => 'related_docs',
      'rname' => 'document_revision_id',
      'id_name' => 'related_doc_id',
      'vname' => 'LBL_RELATED_DOCUMENT_REVISION_ID',
      'reportable' => false,
      'dbType' => 'id',
      'type' => 'id',
    ),
    'related_doc_rev_number' => 
    array (
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
      'module' => 'DocumentRevisions',
    ),
    'is_template' => 
    array (
      'name' => 'is_template',
      'vname' => 'LBL_IS_TEMPLATE',
      'type' => 'bool',
      'default' => 0,
      'reportable' => false,
    ),
    'template_type' => 
    array (
      'name' => 'template_type',
      'vname' => 'LBL_TEMPLATE_TYPE',
      'type' => 'enum',
      'len' => 100,
      'options' => 'document_template_type_dom',
      'reportable' => false,
    ),
    'latest_revision_name' => 
    array (
      'name' => 'latest_revision_name',
      'vname' => 'LBL_LASTEST_REVISION_NAME',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'selected_revision_name' => 
    array (
      'name' => 'selected_revision_name',
      'vname' => 'LBL_SELECTED_REVISION_NAME',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'contract_status' => 
    array (
      'name' => 'contract_status',
      'vname' => 'LBL_CONTRACT_STATUS',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'contract_name' => 
    array (
      'name' => 'contract_name',
      'vname' => 'LBL_CONTRACT_NAME',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'linked_id' => 
    array (
      'name' => 'linked_id',
      'vname' => 'LBL_LINKED_ID',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'selected_revision_id' => 
    array (
      'name' => 'selected_revision_id',
      'vname' => 'LBL_SELECTED_REVISION_ID',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'latest_revision_id' => 
    array (
      'name' => 'latest_revision_id',
      'vname' => 'LBL_LATEST_REVISION_ID',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'selected_revision_filename' => 
    array (
      'name' => 'selected_revision_filename',
      'vname' => 'LBL_SELECTED_REVISION_FILENAME',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
    ),
    'related_docs' => 
    array (
      'name' => 'related_docs',
      'type' => 'link',
      'link_type' => 'one',
      'relationship' => 'related_documents',
      'source' => 'non-db',
      'vname' => 'LBL_DET_RELATED_DOCUMENT',
    ),
    'following' => 
    array (
      'massupdate' => false,
      'name' => 'following',
      'vname' => 'LBL_FOLLOWING',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Is user following this record',
      'studio' => 'false',
      'link' => 'following_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'following_link' => 
    array (
      'name' => 'following_link',
      'type' => 'link',
      'relationship' => 'documents_following',
      'source' => 'non-db',
      'vname' => 'LBL_FOLLOWING',
      'reportable' => false,
    ),
    'my_favorite' => 
    array (
      'massupdate' => false,
      'name' => 'my_favorite',
      'vname' => 'LBL_FAVORITE',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Favorite for the user',
      'studio' => 
      array (
        'list' => false,
        'recordview' => false,
        'basic_search' => false,
        'advanced_search' => false,
      ),
      'link' => 'favorite_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'favorite_link' => 
    array (
      'name' => 'favorite_link',
      'type' => 'link',
      'relationship' => 'documents_favorite',
      'source' => 'non-db',
      'vname' => 'LBL_FAVORITE',
      'reportable' => false,
      'workflow' => false,
      'full_text_search' => 
      array (
        'type' => 'favorites',
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'favorite_link' => 
          array (
            'type' => 'MyItems',
            'options' => 
            array (
              'field' => 'user_favorites',
            ),
          ),
        ),
      ),
    ),
    'tag' => 
    array (
      'name' => 'tag',
      'vname' => 'LBL_TAGS',
      'type' => 'tag',
      'link' => 'tag_link',
      'source' => 'non-db',
      'module' => 'Tags',
      'relate_collection' => true,
      'studio' => 
      array (
        'portal' => false,
        'base' => 
        array (
          'popuplist' => false,
          'popupsearch' => false,
        ),
        'mobile' => 
        array (
          'wirelesseditview' => true,
          'wirelessdetailview' => true,
        ),
      ),
      'massupdate' => true,
      'exportable' => true,
      'sortable' => false,
      'rname' => 'name',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'tag_link' => 
    array (
      'name' => 'tag_link',
      'type' => 'link',
      'vname' => 'LBL_TAGS_LINK',
      'relationship' => 'documents_tags',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'commentlog' => 
    array (
      'name' => 'commentlog',
      'vname' => 'LBL_COMMENTLOG',
      'type' => 'collection',
      'displayParams' => 
      array (
        'type' => 'commentlog',
        'fields' => 
        array (
          0 => 'entry',
          1 => 'date_entered',
          2 => 'created_by_name',
        ),
        'max_num' => 100,
      ),
      'links' => 
      array (
        0 => 'commentlog_link',
      ),
      'order_by' => 'date_entered:asc',
      'source' => 'non-db',
      'module' => 'CommentLog',
      'studio' => 
      array (
        'listview' => false,
        'recordview' => true,
        'previewview' => true,
        'wirelesseditview' => false,
        'wirelessdetailview' => true,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
      ),
    ),
    'commentlog_link' => 
    array (
      'name' => 'commentlog_link',
      'type' => 'link',
      'vname' => 'LBL_COMMENTLOG_LINK',
      'relationship' => 'documents_commentlog',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'locked_fields' => 
    array (
      'name' => 'locked_fields',
      'vname' => 'LBL_LOCKED_FIELDS',
      'type' => 'locked_fields',
      'link' => 'locked_fields_link',
      'source' => 'non-db',
      'module' => 'pmse_BpmProcessDefinition',
      'relate_collection' => true,
      'studio' => false,
      'massupdate' => false,
      'exportable' => false,
      'sortable' => false,
      'rname' => 'pro_locked_variables',
      'collection_fields' => 
      array (
        0 => 'pro_locked_variables',
      ),
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
      ),
      'hideacl' => true,
    ),
    'locked_fields_link' => 
    array (
      'name' => 'locked_fields_link',
      'type' => 'link',
      'vname' => 'LBL_LOCKED_FIELDS_LINK',
      'relationship' => 'documents_locked_fields',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'sync_key' => 
    array (
      'is_sync_key' => true,
      'name' => 'sync_key',
      'vname' => 'LBL_SYNC_KEY',
      'type' => 'varchar',
      'enforced' => '',
      'required' => false,
      'massupdate' => false,
      'readonly' => true,
      'default' => NULL,
      'isnull' => true,
      'no_default' => false,
      'comments' => 'External default id of the remote integration record',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'merge_filter' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
      'reportable' => true,
      'unified_search' => false,
      'calculated' => false,
      'len' => '100',
      'size' => '20',
      'studio' => 
      array (
        'recordview' => true,
        'wirelessdetailview' => true,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
        'portallistview' => false,
        'portalrecordview' => false,
        'portaleditview' => false,
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO_ID',
      'group' => 'assigned_user_name',
      'type' => 'id',
      'reportable' => false,
      'isnull' => 'false',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'User ID assigned to record',
      'duplicate_merge' => 'disabled',
      'mandatory_fetch' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'assigned_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_ASSIGNED_TO_ME',
          ),
        ),
      ),
    ),
    'assigned_user_name' => 
    array (
      'name' => 'assigned_user_name',
      'link' => 'assigned_user_link',
      'vname' => 'LBL_ASSIGNED_TO',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'assigned_user_id',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
      'related_fields' => 
      array (
        0 => 'assigned_user_id',
      ),
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'documents_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'duplicate_merge' => 'enabled',
      'id_name' => 'assigned_user_id',
      'table' => 'users',
      'side' => 'right',
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'vname' => 'LBL_TEAM_ID',
      'group' => 'team_name',
      'reportable' => false,
      'dbType' => 'id',
      'type' => 'team_list',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'Team ID for the account',
    ),
    'team_set_id' => 
    array (
      'name' => 'team_set_id',
      'rname' => 'id',
      'id_name' => 'team_set_id',
      'vname' => 'LBL_TEAM_SET_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => 'false',
      'dbType' => 'id',
      'duplicate_on_record_copy' => 'always',
    ),
    'acl_team_set_id' => 
    array (
      'name' => 'acl_team_set_id',
      'vname' => 'LBL_TEAM_SET_SELECTED_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => false,
      'isnull' => true,
      'duplicate_on_record_copy' => 'always',
      'reportable' => false,
    ),
    'team_count' => 
    array (
      'name' => 'team_count',
      'rname' => 'team_count',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'join_name' => 'ts1',
      'table' => 'teams',
      'type' => 'relate',
      'required' => 'true',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_count_link',
      'massupdate' => false,
      'dbType' => 'int',
      'source' => 'non-db',
      'importable' => 'false',
      'reportable' => false,
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'studio' => 'false',
      'hideacl' => true,
    ),
    'team_name' => 
    array (
      'name' => 'team_name',
      'db_concat_fields' => 
      array (
        0 => 'name',
        1 => 'name_2',
      ),
      'sort_on' => 'tj.name',
      'join_name' => 'tj',
      'rname' => 'name',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'type' => 'relate',
      'required' => 'true',
      'table' => 'teams',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_link',
      'massupdate' => true,
      'dbType' => 'varchar',
      'source' => 'non-db',
      'custom_type' => 'teamset',
      'studio' => 
      array (
        'portallistview' => false,
        'portalrecordview' => false,
      ),
      'duplicate_on_record_copy' => 'always',
      'exportable' => true,
      'fields' => 
      array (
        0 => 'acl_team_set_id',
      ),
    ),
    'acl_team_names' => 
    array (
      'name' => 'acl_team_names',
      'table' => 'teams',
      'module' => 'Teams',
      'vname' => 'LBL_TEAM_SET_SELECTED_TEAMS',
      'rname' => 'name',
      'id_name' => 'acl_team_set_id',
      'source' => 'non-db',
      'link' => 'team_link',
      'type' => 'relate',
      'custom_type' => 'teamset',
      'exportable' => true,
      'studio' => false,
      'massupdate' => false,
      'hideacl' => true,
    ),
    'team_link' => 
    array (
      'name' => 'team_link',
      'type' => 'link',
      'relationship' => 'documents_team',
      'vname' => 'LBL_TEAMS_LINK',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'Team',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'studio' => 'false',
      'side' => 'right',
    ),
    'team_count_link' => 
    array (
      'name' => 'team_count_link',
      'type' => 'link',
      'relationship' => 'documents_team_count_relationship',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'TeamSet',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'reportable' => false,
      'studio' => 'false',
      'side' => 'right',
    ),
    'teams' => 
    array (
      'name' => 'teams',
      'type' => 'link',
      'relationship' => 'documents_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_documents_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_documents_del_d_m',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_modified',
        2 => 'id',
      ),
    ),
    'deleted' => 
    array (
      'name' => 'idx_documents_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_documents_del_d_e',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_entered',
        2 => 'id',
      ),
    ),
    'name_del' => 
    array (
      'name' => 'idx_documents_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_doc_cat',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'category_id',
        1 => 'subcategory_id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_document_doc_type',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'doc_type',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_document_exp_date',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'exp_date',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_documents_related_doc_id_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'related_doc_id',
        1 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_documents_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_documents_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_documents' => 
    array (
      'name' => 'idx_documents_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_documents' => 
    array (
      'name' => 'idx_documents_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
  ),
  'relationships' => 
  array (
    'documents_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'documents_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'document_activities' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'id',
      'rhs_vname' => 'LBL_ACTIVITY_STREAM',
      'relationship_type' => 'many-to-many',
      'join_table' => 'activities_users',
      'join_key_lhs' => 'parent_id',
      'join_key_rhs' => 'activity_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Documents',
    ),
    'related_documents' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'related_doc_id',
      'relationship_type' => 'one-to-many',
    ),
    'document_revisions' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'DocumentRevisions',
      'rhs_table' => 'document_revisions',
      'rhs_key' => 'document_id',
      'relationship_type' => 'one-to-many',
    ),
    'latest_document_revision' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'document_revision_id',
      'rhs_module' => 'DocumentRevisions',
      'rhs_table' => 'document_revisions',
      'rhs_key' => 'id',
      'relationship_type' => 'one-to-one',
    ),
    'documents_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Documents',
      'user_field' => 'created_by',
    ),
    'documents_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Documents',
      'user_field' => 'created_by',
    ),
    'documents_tags' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Documents',
      'dynamic_subpanel' => true,
    ),
    'documents_commentlog' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Documents',
    ),
    'documents_locked_fields' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Documents',
    ),
    'documents_audit' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'documents_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'documents_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'documents_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'documents_teams' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'documents_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
  ),
  'acls' => 
  array (
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
  ),
  'duplicate_check' => 
  array (
    'enabled' => true,
    'FilterDuplicateCheck' => 
    array (
      'filter_template' => 
      array (
        0 => 
        array (
          'name' => 
          array (
            '$starts' => '$name',
          ),
        ),
      ),
      'ranking_fields' => 
      array (
        0 => 
        array (
          'in_field_name' => 'name',
          'dupe_field_name' => 'name',
        ),
      ),
    ),
  ),
  'favorites' => true,
  'templates' => 
  array (
    'basic' => 'basic',
    'following' => 'following',
    'favorite' => 'favorite',
    'taggable' => 'taggable',
    'commentlog' => 'commentlog',
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
    'assignable' => 'assignable',
    'team_security' => 'team_security',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);