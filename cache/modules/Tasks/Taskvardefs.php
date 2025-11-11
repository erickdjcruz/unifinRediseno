<?php 
 $GLOBALS["dictionary"]["Task"]=array (
  'table' => 'tasks',
  'color' => 'purple',
  'icon' => 'sicon-task-lg',
  'audited' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'activity_enabled' => true,
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
      'vname' => 'LBL_SUBJECT',
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => '255',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.45,
      ),
      'importable' => 'required',
      'required' => true,
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
        'boost' => 0.56,
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
      'relationship' => 'tasks_created_by',
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
      'relationship' => 'tasks_modified_user',
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
      'relationship' => 'task_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'options' => 'task_status_dom',
      'len' => 100,
      'required' => true,
      'default' => 'Not Started',
      'duplicate_on_record_copy' => 'no',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'date_due_flag' => 
    array (
      'name' => 'date_due_flag',
      'vname' => 'LBL_DATE_DUE_FLAG',
      'type' => 'bool',
      'default' => 0,
      'group' => 'date_due',
      'studio' => false,
      'massupdate' => false,
    ),
    'date_due' => 
    array (
      'name' => 'date_due',
      'vname' => 'LBL_DUE_DATE',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'group' => 'date_due',
      'studio' => 
      array (
        'required' => true,
        'no_duplicate' => true,
      ),
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'full_text_search' => 
      array (
        'type' => 'datetime',
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'time_due' => 
    array (
      'name' => 'time_due',
      'vname' => 'LBL_DUE_TIME',
      'type' => 'datetime',
      'source' => 'non-db',
      'importable' => 'false',
      'massupdate' => false,
    ),
    'date_start_flag' => 
    array (
      'name' => 'date_start_flag',
      'vname' => 'LBL_DATE_START_FLAG',
      'type' => 'bool',
      'group' => 'date_start',
      'default' => 0,
      'studio' => false,
      'massupdate' => false,
    ),
    'date_start' => 
    array (
      'name' => 'date_start',
      'vname' => 'LBL_START_DATE',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'group' => 'date_start',
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'date_due',
        'blank' => false,
      ),
      'studio' => 
      array (
        'required' => true,
        'no_duplicate' => true,
      ),
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_NAME',
      'type' => 'parent_type',
      'dbType' => 'varchar',
      'group' => 'parent_name',
      'options' => 'parent_type_display',
      'required' => false,
      'len' => '255',
      'comment' => 'The Sugar object to which the call is related',
      'studio' => 
      array (
        'wirelesslistview' => false,
      ),
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'parent_type' => 'record_type_display',
      'type_name' => 'parent_type',
      'id_name' => 'parent_id',
      'vname' => 'LBL_LIST_RELATED_TO',
      'type' => 'parent',
      'related_fields' => 
      array (
        0 => 'parent_id',
        1 => 'parent_type',
      ),
      'group' => 'parent_name',
      'source' => 'non-db',
      'options' => 'parent_type_display',
      'studio' => true,
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'type' => 'id',
      'group' => 'parent_name',
      'reportable' => false,
      'vname' => 'LBL_PARENT_ID',
    ),
    'contact_id' => 
    array (
      'name' => 'contact_id',
      'type' => 'id',
      'group' => 'contact_name',
      'reportable' => false,
      'vname' => 'LBL_CONTACT_ID',
    ),
    'contact_name' => 
    array (
      'name' => 'contact_name',
      'rname' => 'name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'related_fields' => 
      array (
        0 => 'contact_id',
      ),
      'source' => 'non-db',
      'len' => '510',
      'group' => 'contact_name',
      'vname' => 'LBL_CONTACT_NAME',
      'reportable' => false,
      'id_name' => 'contact_id',
      'join_name' => 'contacts',
      'type' => 'relate',
      'module' => 'Contacts',
      'link' => 'contacts',
      'table' => 'contacts',
    ),
    'contact_phone' => 
    array (
      'name' => 'contact_phone',
      'type' => 'relate',
      'related_fields' => 
      array (
        0 => 'contact_id',
      ),
      'source' => 'non-db',
      'link' => 'contacts',
      'module' => 'Contacts',
      'table' => 'contacts',
      'id_name' => 'contact_id',
      'rname' => 'phone_work',
      'vname' => 'LBL_CONTACT_PHONE',
      'studio' => 
      array (
        'listview' => true,
      ),
      'readonly' => true,
    ),
    'contact_email' => 
    array (
      'name' => 'contact_email',
      'type' => 'varchar',
      'vname' => 'LBL_EMAIL_ADDRESS',
      'source' => 'non-db',
      'studio' => false,
    ),
    'priority' => 
    array (
      'name' => 'priority',
      'vname' => 'LBL_PRIORITY',
      'type' => 'enum',
      'options' => 'task_priority_dom',
      'len' => 100,
      'required' => true,
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'contact_tasks',
      'source' => 'non-db',
      'side' => 'right',
      'link_type' => 'one',
      'vname' => 'LBL_CONTACT',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'account_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNTS',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'opportunity_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITY',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'revenuelineitem_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_REVENUELINEITEMS',
      'workflow' => true,
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'case_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_CASE',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'bug_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_tasks_rel',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'type' => 'link',
      'relationship' => 'lead_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_LEADS',
    ),
    'projects' => 
    array (
      'name' => 'projects',
      'type' => 'link',
      'relationship' => 'projects_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'project_tasks' => 
    array (
      'name' => 'project_tasks',
      'type' => 'link',
      'relationship' => 'project_tasks_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECT_TASKS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'tasks_notes',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'tasks_messages',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'escalation_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quote_tasks',
      'vname' => 'LBL_QUOTES',
      'source' => 'non-db',
    ),
    'contact_parent' => 
    array (
      'name' => 'contact_parent',
      'type' => 'link',
      'relationship' => 'contact_tasks_parent',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'meetings_parent' => 
    array (
      'name' => 'meetings_parent',
      'type' => 'link',
      'relationship' => 'task_meetings_parent',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
      'reportable' => false,
    ),
    'calls_parent' => 
    array (
      'name' => 'calls_parent',
      'type' => 'link',
      'relationship' => 'task_calls_parent',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
      'reportable' => false,
    ),
    'project' => 
    array (
      'name' => 'project',
      'type' => 'link',
      'relationship' => 'projects_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
      'side' => 'right',
    ),
    'kbcontents' => 
    array (
      'name' => 'kbcontents',
      'type' => 'link',
      'relationship' => 'kbcontent_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_KBDOCUMENTS',
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
      'relationship' => 'tasks_following',
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
      'relationship' => 'tasks_favorite',
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
      'relationship' => 'tasks_tags',
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
      'relationship' => 'tasks_commentlog',
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
      'relationship' => 'tasks_locked_fields',
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
      'relationship' => 'tasks_assigned_user',
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
      'relationship' => 'tasks_team',
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
      'relationship' => 'tasks_team_count_relationship',
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
      'relationship' => 'tasks_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'dri_workflow_id' => 
    array (
      'name' => 'dri_workflow_id',
      'vname' => 'LBL_DRI_WORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_workflow_name' => 
    array (
      'name' => 'dri_workflow_name',
      'vname' => 'LBL_DRI_WORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflows',
      'id_name' => 'dri_workflow_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflows',
      'link' => 'dri_workflow_link',
      'processes' => false,
    ),
    'dri_workflow_link' => 
    array (
      'name' => 'dri_workflow_link',
      'vname' => 'LBL_DRI_WORKFLOW',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'task_dri_workflows',
      'module' => 'DRI_Workflows',
      'processes' => false,
    ),
    'current_cj_activity_at' => 
    array (
      'name' => 'current_cj_activity_at',
      'vname' => 'LBL_CURRENT_CJ_ACTIVITY_AT',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'tasks_flex_relate_dri_workflows',
      'module' => 'DRI_Workflows',
      'processes' => false,
    ),
    'dri_subworkflow_id' => 
    array (
      'name' => 'dri_subworkflow_id',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_subworkflow_name' => 
    array (
      'name' => 'dri_subworkflow_name',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_subworkflows',
      'id_name' => 'dri_subworkflow_id',
      'sort_on' => 'name',
      'module' => 'DRI_SubWorkflows',
      'link' => 'dri_subworkflow_link',
      'processes' => false,
    ),
    'dri_subworkflow_link' => 
    array (
      'name' => 'dri_subworkflow_link',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow',
      'relationship' => 'task_dri_subworkflows',
      'module' => 'DRI_SubWorkflows',
      'processes' => false,
    ),
    'dri_subworkflow_template_id' => 
    array (
      'name' => 'dri_subworkflow_template_id',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_subworkflow_template_name' => 
    array (
      'name' => 'dri_subworkflow_template_name',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_subworkflow_templates',
      'id_name' => 'dri_subworkflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_SubWorkflow_Templates',
      'link' => 'dri_subworkflow_template_link',
      'processes' => false,
    ),
    'dri_subworkflow_template_link' => 
    array (
      'name' => 'dri_subworkflow_template_link',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow_Template',
      'relationship' => 'task_dri_subworkflow_templates',
      'module' => 'DRI_SubWorkflow_Templates',
      'processes' => false,
    ),
    'dri_workflow_template_id' => 
    array (
      'name' => 'dri_workflow_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_workflow_template_name' => 
    array (
      'name' => 'dri_workflow_template_name',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'dri_workflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'link' => 'dri_workflow_template_link',
      'processes' => false,
    ),
    'dri_workflow_template_link' => 
    array (
      'name' => 'dri_workflow_template_link',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'task_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
      'processes' => false,
    ),
    'dri_workflow_task_template_id' => 
    array (
      'name' => 'dri_workflow_task_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'cj_activity_tpl_name' => 
    array (
      'name' => 'cj_activity_tpl_name',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'dri_workflow_task_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'link' => 'cj_activity_tpl_link',
      'processes' => false,
    ),
    'cj_activity_tpl_link' => 
    array (
      'name' => 'cj_activity_tpl_link',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'task_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
      'processes' => false,
    ),
    'dri_workflow_sort_order' => 
    array (
      'name' => 'dri_workflow_sort_order',
      'vname' => 'LBL_DRI_WORKFLOW_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'default' => 1,
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'cj_actual_sort_order' => 
    array (
      'name' => 'cj_actual_sort_order',
      'vname' => 'LBL_CJ_ACTUAL_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'customer_journey_score' => 
    array (
      'name' => 'customer_journey_score',
      'vname' => 'LBL_CUSTOMER_JOURNEY_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_points' => 
    array (
      'name' => 'cj_momentum_points',
      'vname' => 'LBL_CJ_MOMENTUM_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_score' => 
    array (
      'name' => 'cj_momentum_score',
      'vname' => 'LBL_CJ_MOMENTUM_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_allow_activity_by' => 
    array (
      'name' => 'cj_allow_activity_by',
      'vname' => 'LBL_CJ_ALLOW_ACTIVITY_BY',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'text',
      'studio' => false,
      'processes' => false,
    ),
    'customer_journey_progress' => 
    array (
      'name' => 'customer_journey_progress',
      'vname' => 'LBL_CUSTOMER_JOURNEY_PROGRESS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_ratio' => 
    array (
      'name' => 'cj_momentum_ratio',
      'vname' => 'LBL_CJ_MOMENTUM_RATIO',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'processes' => false,
    ),
    'customer_journey_points' => 
    array (
      'name' => 'customer_journey_points',
      'vname' => 'LBL_CUSTOMER_JOURNEY_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_points_list',
      'type' => 'enum',
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'default' => 10,
      'dbType' => 'int',
      'len' => 3,
      'processes' => false,
    ),
    'cj_parent_activity_type' => 
    array (
      'name' => 'cj_parent_activity_type',
      'vname' => 'LBL_CJ_PARENT_ACTIVITY_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_cj_parent_activity_type_list',
      'type' => 'enum',
      'processes' => false,
    ),
    'customer_journey_blocked_by' => 
    array (
      'name' => 'customer_journey_blocked_by',
      'vname' => 'LBL_CUSTOMER_JOURNEY_BLOCKED_BY',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'processes' => false,
    ),
    'cj_blocked_by_stages' => 
    array (
      'name' => 'cj_blocked_by_stages',
      'vname' => 'LBL_CJ_BLOCKED_BY_STAGES',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'processes' => false,
    ),
    'is_cj_parent_activity' => 
    array (
      'name' => 'is_cj_parent_activity',
      'vname' => 'LBL_IS_CJ_PARENT_ACTIVITY',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'processes' => false,
    ),
    'is_customer_journey_activity' => 
    array (
      'name' => 'is_customer_journey_activity',
      'vname' => 'LBL_IS_CUSTOMER_JOURNEY_ACTIVITY',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'enforced' => true,
      'calculated' => true,
      'formula' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'cj_momentum_start_date' => 
    array (
      'name' => 'cj_momentum_start_date',
      'vname' => 'LBL_CJ_MOMENTUM_START_DATE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_momentum_end_date' => 
    array (
      'name' => 'cj_momentum_end_date',
      'vname' => 'LBL_CJ_MOMENTUM_END_DATE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_url' => 
    array (
      'name' => 'cj_url',
      'vname' => 'LBL_CJ_URL',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'url',
      'dbType' => 'varchar',
      'processes' => false,
    ),
    'cj_parent_activity_id' => 
    array (
      'name' => 'cj_parent_activity_id',
      'vname' => 'LBL_CJ_PARENT_ACTIVITY_ID',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'start_next_journey_id' => 
    array (
      'name' => 'start_next_journey_id',
      'vname' => 'LBL_START_NEXT_JOURNEY_ID',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'parent_task_of_call_rel' => 
    array (
      'name' => 'parent_task_of_call_rel',
      'type' => 'link',
      'relationship' => 'parent_task_of_call_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_TASK_OF_CALL_REL',
      'reportable' => true,
    ),
    'parent_task_of_meeting_rel' => 
    array (
      'name' => 'parent_task_of_meeting_rel',
      'type' => 'link',
      'relationship' => 'parent_task_of_meeting_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_TASK_OF_MEETING_REL',
      'reportable' => true,
    ),
    'parent_task_of_task_rel' => 
    array (
      'name' => 'parent_task_of_task_rel',
      'type' => 'link',
      'relationship' => 'parent_task_of_task_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_TASK_OF_TASK_REL',
      'reportable' => true,
    ),
    'customer_journey_type' => 
    array (
      'name' => 'customer_journey_type',
      'vname' => 'LBL_CUSTOMER_JOURNEY_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_type_list',
      'type' => 'enum',
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'no_default' => true,
      'processes' => false,
    ),
    'cj_activity_start_date' => 
    array (
      'name' => 'cj_activity_start_date',
      'vname' => 'LBL_CJ_ACTIVITY_START_DATE',
      'required' => false,
      'readonly' => true,
      'reportable' => true,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_activity_completion_date' => 
    array (
      'name' => 'cj_activity_completion_date',
      'vname' => 'LBL_CJ_ACTIVITY_COMPLETION_DATE',
      'required' => false,
      'readonly' => true,
      'reportable' => true,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_days_to_complete' => 
    array (
      'name' => 'cj_days_to_complete',
      'vname' => 'LBL_CJ_DAYS_TO_COMPLETE',
      'required' => false,
      'readonly' => true,
      'reportable' => true,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'varchar',
      'processes' => false,
    ),
  ),
  'relationships' => 
  array (
    'tasks_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'tasks_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'task_activities' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
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
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_notes' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_messages' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'task_meetings_parent' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'task_calls_parent' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Tasks',
      'user_field' => 'created_by',
    ),
    'tasks_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Tasks',
      'user_field' => 'created_by',
    ),
    'tasks_tags' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Tasks',
      'dynamic_subpanel' => true,
    ),
    'tasks_commentlog' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_locked_fields' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Tasks',
    ),
    'tasks_audit' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'tasks_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'tasks_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'tasks_teams' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'tasks_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'task_dri_workflows' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'dri_workflow_id',
    ),
    'tasks_flex_relate_dri_workflows' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'relationship_role_column_value' => 'Tasks',
      'relationship_role_column' => 'parent_type',
    ),
    'task_dri_subworkflows' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflows',
      'lhs_table' => 'dri_subworkflows',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'dri_subworkflow_id',
    ),
    'task_dri_subworkflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflow_Templates',
      'lhs_table' => 'dri_subworkflow_templates',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'dri_subworkflow_template_id',
    ),
    'task_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'dri_workflow_template_id',
    ),
    'task_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'dri_workflow_task_template_id',
    ),
    'parent_task_of_call_rel' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'parent_task_of_meeting_rel' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Tasks',
    ),
    'parent_task_of_task_rel' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Tasks',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_tasks_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_tasks_del_d_m',
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
      'name' => 'idx_tasks_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_tasks_del_d_e',
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
      'name' => 'idx_tasks_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_tsk_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'name',
        2 => 'date_modified',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_task_con_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'contact_id',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_task_par_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
        1 => 'parent_type',
        2 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_task_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_task_date_due',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'date_due',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_tasks_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_tasks_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_tasks' => 
    array (
      'name' => 'idx_tasks_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_tasks' => 
    array (
      'name' => 'idx_tasks_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'idx_del_wf_tpl_sub_subtpl_task_name' => 
    array (
      'name' => 'idx_del_wf_tpl_sub_subtpl_task_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'dri_workflow_id',
        2 => 'dri_workflow_template_id',
        3 => 'dri_subworkflow_id',
        4 => 'dri_subworkflow_template_id',
        5 => 'dri_workflow_task_template_id',
        6 => 'name',
      ),
    ),
    'idx_del_wf_tpl_sub_subtpl_task_status' => 
    array (
      'name' => 'idx_del_wf_tpl_sub_subtpl_task_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'dri_workflow_id',
        2 => 'dri_workflow_template_id',
        3 => 'dri_subworkflow_id',
        4 => 'dri_subworkflow_template_id',
        5 => 'dri_workflow_task_template_id',
        6 => 'status',
      ),
    ),
    'idx_task_cj_stage_id' => 
    array (
      'name' => 'idx_task_cj_stage_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_subworkflow_id',
      ),
    ),
    'idx_task_cj_stage_tpl_id' => 
    array (
      'name' => 'idx_task_cj_stage_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_subworkflow_template_id',
      ),
    ),
    'idx_task_cj_journey_tpl_id' => 
    array (
      'name' => 'idx_task_cj_journey_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
    'idx_task_cj_activity_tpl_id' => 
    array (
      'name' => 'idx_task_cj_activity_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_task_template_id',
      ),
    ),
    'idx_task_cj_parent_activity' => 
    array (
      'name' => 'idx_task_cj_parent_activity',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'cj_parent_activity_id',
        2 => 'cj_parent_activity_type',
      ),
    ),
  ),
  'duplicate_check' => 
  array (
    'enabled' => false,
  ),
  'optimistic_locking' => true,
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
    'customer_journey_activity' => 'customer_journey_activity',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);