<?php 
 $GLOBALS["dictionary"]["KBContent"]=array (
  'optimistic_locking' => true,
  'table' => 'kbcontents',
  'color' => 'coral',
  'icon' => 'sicon-knowledgebase-xl',
  'audited' => true,
  'activity_enabled' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'generic_search' => 
  array (
    'Elastic' => 
    array (
      'mapping' => 
      array (
        'name' => 'name',
        'description' => 'kbdocument_body',
      ),
    ),
  ),
  'unified_search_default_enabled' => true,
  'comment' => 'A content represents information about document',
  'duplicate_merge' => true,
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
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.55,
      ),
      'required' => true,
      'importable' => 'required',
      'duplicate_merge' => 'enabled',
      'merge_filter' => 'selected',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
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
        'boost' => 0.5,
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
      'relationship' => 'kbcontents_created_by',
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
      'relationship' => 'kbcontents_modified_user',
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
      'relationship' => 'kbcontent_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'kbdocument_body' => 
    array (
      'name' => 'kbdocument_body',
      'vname' => 'LBL_TEXT_BODY',
      'dbType' => 'longtext',
      'type' => 'htmleditable_tinymce',
      'comment' => 'Article body',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.6,
      ),
      'audited' => false,
      'duplicate_on_record_copy' => 'always',
      'sortable' => false,
    ),
    'language' => 
    array (
      'name' => 'language',
      'type' => 'enum',
      'function_bean' => 'KBContents',
      'function' => 
      array (
        'returns' => 'array',
        'name' => 'getLanguageOptions',
      ),
      'len' => '2',
      'required' => true,
      'vname' => 'LBL_LANG',
      'sortable' => false,
      'audited' => false,
      'studio' => false,
      'duplicate_on_record_copy' => 'always',
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'active_date' => 
    array (
      'name' => 'active_date',
      'vname' => 'LBL_PUBLISH_DATE',
      'type' => 'date',
      'sortable' => true,
      'studio' => true,
      'duplicate_on_record_copy' => 'no',
      'massupdate' => false,
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'exp_date',
        'blank' => false,
      ),
    ),
    'exp_date' => 
    array (
      'name' => 'exp_date',
      'vname' => 'LBL_EXP_DATE',
      'type' => 'date',
      'sortable' => true,
      'duplicate_on_record_copy' => 'no',
      'studio' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'approved' => 
    array (
      'name' => 'approved',
      'vname' => 'LBL_APPROVED',
      'type' => 'bool',
      'sortable' => true,
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'massupdate' => false,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'len' => 100,
      'options' => 'kbdocument_status_dom',
      'default' => 'draft',
      'reportable' => true,
      'audited' => true,
      'studio' => true,
      'duplicate_on_record_copy' => 'no',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'viewcount' => 
    array (
      'name' => 'viewcount',
      'vname' => 'LBL_VIEWED_COUNT',
      'type' => 'int',
      'importable' => 'required',
      'default' => 0,
      'sortable' => true,
      'duplicate_on_record_copy' => 'no',
      'studio' => true,
      'readonly' => true,
      'duplicate_merge' => 'disabled',
    ),
    'revision' => 
    array (
      'name' => 'revision',
      'vname' => 'LBL_REVISION',
      'type' => 'int',
      'default' => '0',
      'duplicate_on_record_copy' => 'no',
      'studio' => true,
      'duplicate_merge' => 'disabled',
    ),
    'useful' => 
    array (
      'name' => 'useful',
      'vname' => 'LBL_USEFUL',
      'type' => 'int',
      'default' => '0',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'group' => 'usefulness',
      'hideacl' => true,
    ),
    'notuseful' => 
    array (
      'name' => 'notuseful',
      'vname' => 'LBL_NOT_USEFUL',
      'type' => 'int',
      'default' => '0',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'group' => 'usefulness',
      'hideacl' => true,
    ),
    'attachment_list' => 
    array (
      'name' => 'attachment_list',
      'links' => 
      array (
        0 => 'attachments',
      ),
      'order_by' => 'name:asc',
      'source' => 'non-db',
      'studio' => 
      array (
        'recordview' => true,
        'previewview' => true,
        'recorddashletview' => true,
        'visible' => false,
      ),
      'type' => 'collection',
      'vname' => 'LBL_ATTACHMENTS',
      'reportable' => false,
      'hideacl' => true,
      'filter' => 
      array (
        0 => 
        array (
          'attachment_flag' => '1',
        ),
      ),
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'vname' => 'LBL_NOTES',
      'type' => 'link',
      'relationship' => 'kbcontent_notes',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'vname' => 'LBL_MESSAGES',
      'type' => 'link',
      'relationship' => 'kbcontent_messages',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
    ),
    'attachments' => 
    array (
      'name' => 'attachments',
      'vname' => 'LBL_ATTACHMENTS',
      'type' => 'link',
      'relationship' => 'kbcontent_attachments',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
      'group' => 'attachments',
    ),
    'kbdocuments_kbcontents' => 
    array (
      'name' => 'kbdocuments_kbcontents',
      'type' => 'link',
      'vname' => 'LBL_KBDOCUMENTS',
      'relationship' => 'kbdocuments_kbcontents',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'no',
    ),
    'kbdocument_id' => 
    array (
      'name' => 'kbdocument_id',
      'id_name' => 'kbdocument_id',
      'vname' => 'LBL_KBDOCUMENT_ID',
      'rname' => 'id',
      'type' => 'id',
      'table' => 'kbdocuments',
      'isnull' => 'true',
      'module' => 'KBDocuments',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
    ),
    'kbdocument_name' => 
    array (
      'name' => 'kbdocument_name',
      'rname' => 'name',
      'vname' => 'LBL_KBDOCUMENT',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'kbdocuments',
      'id_name' => 'kbdocument_id',
      'link' => 'kbdocuments_kbcontents',
      'module' => 'KBDocuments',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'studio' => false,
      'massupdate' => false,
    ),
    'active_rev' => 
    array (
      'name' => 'active_rev',
      'vname' => 'LBL_ACTIVE_REV',
      'type' => 'tinyint',
      'isnull' => 'true',
      'comment' => 'Active revision flag',
      'default' => 0,
      'duplicate_on_record_copy' => 'no',
      'studio' => 
      array (
        'list' => false,
        'quickcreate' => false,
        'basic_search' => false,
        'advanced_search' => false,
      ),
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'int',
      ),
    ),
    'is_external' => 
    array (
      'name' => 'is_external',
      'vname' => 'LBL_IS_EXTERNAL',
      'type' => 'tinyint',
      'displayParams' => 
      array (
        'reports_type' => 'bool',
      ),
      'isnull' => 'true',
      'comment' => 'External article flag',
      'default' => 0,
      'studio' => true,
      'duplicate_on_record_copy' => 'always',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'int',
      ),
    ),
    'kbarticles_kbcontents' => 
    array (
      'name' => 'kbarticles_kbcontents',
      'type' => 'link',
      'vname' => 'LBL_KBARTICLES',
      'relationship' => 'kbarticles_kbcontents',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'no',
    ),
    'kbarticle_id' => 
    array (
      'name' => 'kbarticle_id',
      'id_name' => 'kbarticle_id',
      'vname' => 'LBL_KBARTICLE_ID',
      'rname' => 'id',
      'type' => 'id',
      'table' => 'kbarticles',
      'isnull' => 'true',
      'module' => 'KBArticles',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'importable' => false,
      'audited' => true,
      'studio' => false,
    ),
    'kbarticle_name' => 
    array (
      'name' => 'kbarticle_name',
      'rname' => 'name',
      'vname' => 'LBL_KBARTICLE',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'kbarticles',
      'id_name' => 'kbarticle_id',
      'link' => 'kbarticles_kbcontents',
      'module' => 'KBArticles',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'massupdate' => false,
    ),
    'localizations' => 
    array (
      'name' => 'localizations',
      'type' => 'link',
      'link_class' => 'LocalizationsLink',
      'source' => 'non-db',
      'vname' => 'LBL_KBSLOCALIZATIONS',
      'relationship' => 'localizations',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'massupdate' => false,
    ),
    'revisions' => 
    array (
      'name' => 'revisions',
      'type' => 'link',
      'link_class' => 'RevisionsLink',
      'source' => 'non-db',
      'vname' => 'LBL_KBSREVISIONS',
      'relationship' => 'revisions',
      'studio' => false,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
    ),
    'related_languages' => 
    array (
      'name' => 'related_languages',
      'type' => 'enum',
      'function' => 'getLanguages',
      'function_bean' => 'KBContents',
      'source' => 'non-db',
      'vname' => 'LBL_KBSLOCALIZATIONS',
      'studio' => false,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
    ),
    'kbsapprovers_kbcontents' => 
    array (
      'name' => 'kbsapprovers_kbcontents',
      'type' => 'link',
      'vname' => 'LBL_KBSAPPROVERS',
      'relationship' => 'kbsapprovers_kbcontents',
      'source' => 'non-db',
    ),
    'kbsapprover_id' => 
    array (
      'name' => 'kbsapprover_id',
      'id_name' => 'kbsapprover_id',
      'vname' => 'LBL_KBSAPPROVER_ID',
      'rname' => 'id',
      'type' => 'id',
      'table' => 'users',
      'isnull' => 'true',
      'module' => 'Users',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'audited' => true,
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'comment' => 'User who approved article',
    ),
    'kbsapprover_name' => 
    array (
      'name' => 'kbsapprover_name',
      'rname' => 'full_name',
      'vname' => 'LBL_KBSAPPROVER',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'kbsapprover_id',
      'link' => 'kbsapprovers_kbcontents',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'studio' => true,
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'relcases_kbcontents',
      'source' => 'non-db',
      'vname' => 'LBL_KBSCASES',
    ),
    'kbscase_id' => 
    array (
      'name' => 'kbscase_id',
      'id_name' => 'kbscase_id',
      'vname' => 'LBL_KBSCASE_ID',
      'rname' => 'id',
      'type' => 'id',
      'link' => 'cases',
      'table' => 'cases',
      'isnull' => 'true',
      'module' => 'Cases',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'audited' => true,
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
      'comment' => 'Related case',
      'importable' => true,
    ),
    'kbscase_name' => 
    array (
      'name' => 'kbscase_name',
      'rname' => 'name',
      'vname' => 'LBL_KBSCASE',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'cases',
      'id_name' => 'kbscase_id',
      'link' => 'cases',
      'module' => 'Cases',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'studio' => true,
      'importable' => false,
    ),
    'category_id' => 
    array (
      'name' => 'category_id',
      'vname' => 'LBL_CATEGORY_ID',
      'type' => 'id',
      'isnull' => 'true',
      'comment' => 'Category ID',
      'audited' => true,
      'studio' => false,
      'duplicate_on_record_copy' => 'always',
    ),
    'category_name' => 
    array (
      'name' => 'category_name',
      'rname' => 'name',
      'id_name' => 'category_id',
      'vname' => 'LBL_CATEGORY_NAME',
      'type' => 'nestedset',
      'isnull' => 'true',
      'config_provider' => 'KBContents',
      'category_provider' => 'Categories',
      'module' => 'Categories',
      'table' => 'categories',
      'massupdate' => false,
      'source' => 'non-db',
      'studio' => 'visible',
      'duplicate_on_record_copy' => 'always',
    ),
    'usefulness' => 
    array (
      'name' => 'usefulness',
      'type' => 'link',
      'module' => 'Users',
      'bean_name' => 'User',
      'link_class' => 'UsefulnessLink',
      'source' => 'non-db',
      'vname' => 'LBL_USEFULNESS',
      'relationship' => 'kbusefulness',
      'studio' => false,
      'massupdate' => false,
      'reportable' => false,
      'side' => 'right',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'kbcontent_calls',
      'module' => 'Calls',
      'bean_name' => 'Call',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'kbcontent_meetings',
      'module' => 'Meetings',
      'bean_name' => 'Meeting',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'usefulness_user_vote' => 
    array (
      'name' => 'usefulness_user_vote',
      'type' => 'smallint',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'kbcontent_tasks',
      'module' => 'Tasks',
      'bean_name' => 'Task',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
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
      'relationship' => 'kbcontents_following',
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
      'relationship' => 'kbcontents_favorite',
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
      'duplicate_on_record_copy' => 'no',
    ),
    'tag_link' => 
    array (
      'name' => 'tag_link',
      'type' => 'link',
      'vname' => 'LBL_TAGS_LINK',
      'relationship' => 'kbcontents_tags',
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
      'relationship' => 'kbcontents_commentlog',
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
      'relationship' => 'kbcontents_locked_fields',
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
      'relationship' => 'kbcontents_team',
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
      'relationship' => 'kbcontents_team_count_relationship',
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
      'relationship' => 'kbcontents_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
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
      'relationship' => 'kbcontents_assigned_user',
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
  ),
  'relationships' => 
  array (
    'kbcontents_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontents_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontent_activities' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
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
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontent_messages' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontent_notes' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontent_attachments' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'AttachmentRelationship',
      'relationship_file' => 'data/Relationships/AttachmentRelationship.php',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbdocuments_kbcontents' => 
    array (
      'lhs_module' => 'KBDocuments',
      'lhs_table' => 'kbdocuments',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbdocument_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbarticles_kbcontents' => 
    array (
      'lhs_module' => 'KBArticles',
      'lhs_table' => 'kbarticles',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbarticle_id',
      'relationship_type' => 'one-to-many',
    ),
    'localizations' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'kbdocument_id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbdocument_id',
      'join_table' => 'kbcontents',
      'join_key_lhs' => 'kbdocument_id',
      'join_key_rhs' => 'kbdocument_id',
      'relationship_type' => 'one-to-many',
    ),
    'revisions' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'kbarticle_id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbarticle_id',
      'join_table' => 'kbcontents',
      'join_key_lhs' => 'kbarticle_id',
      'join_key_rhs' => 'kbarticle_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbsapprovers_kbcontents' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbsapprover_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontent_calls' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontent_meetings' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'relcases_kbcontents' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'kbscase_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontent_tasks' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontents_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'KBContents',
      'user_field' => 'created_by',
    ),
    'kbcontents_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'KBContents',
      'user_field' => 'created_by',
    ),
    'kbcontents_tags' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'KBContents',
      'dynamic_subpanel' => true,
    ),
    'kbcontents_commentlog' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontents_locked_fields' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'KBContents',
    ),
    'kbcontents_audit' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'kbcontents_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontents_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontents_teams' => 
    array (
      'lhs_module' => 'KBContents',
      'lhs_table' => 'kbcontents',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'kbcontents_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'kbcontents_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'KBContents',
      'rhs_table' => 'kbcontents',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_kbcontents_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_kbcontents_del_d_m',
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
      'name' => 'idx_kbcontents_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_kbcontents_del_d_e',
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
      'name' => 'idx_kbcontents_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_kbcontent_del_doc_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'kbdocument_id',
        1 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_kbcontents_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_kbcontents' => 
    array (
      'name' => 'idx_kbcontents_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_kbcontents' => 
    array (
      'name' => 'idx_kbcontents_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_kbcontents_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
  ),
  'duplicate_check' => 
  array (
    'enabled' => false,
  ),
  'acls' => 
  array (
    'SugarACLStatic' => true,
    'SugarACLKB' => true,
    'SugarACLLockedFields' => true,
  ),
  'visibility' => 
  array (
    'KBVisibility' => true,
    'TeamSecurity' => true,
  ),
  'portal_visibility' => 
  array (
    'class' => 'KBContents',
  ),
  'name_format_map' => 
  array (
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
    'team_security' => 'team_security',
    'assignable' => 'assignable',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);