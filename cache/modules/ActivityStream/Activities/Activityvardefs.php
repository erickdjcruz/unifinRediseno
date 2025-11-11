<?php 
 $GLOBALS["dictionary"]["Activity"]=array (
  'table' => 'activities',
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
      'type' => 'varchar',
      'required' => false,
      'source' => 'non-db',
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
      'type' => 'varchar',
      'required' => false,
      'source' => 'non-db',
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
      'relationship' => 'activities_created_by',
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
      'relationship' => 'activities_modified_user',
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
      'relationship' => 'activity_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'comments' => 
    array (
      'name' => 'comments',
      'type' => 'link',
      'relationship' => 'comments',
      'link_type' => 'many',
      'module' => 'Comments',
      'bean_name' => 'Comment',
      'source' => 'non-db',
    ),
    'activities_users' => 
    array (
      'name' => 'activities_users',
      'type' => 'link',
      'relationship' => 'activities_users',
      'link_type' => 'many',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'activities_teams' => 
    array (
      'name' => 'activities_teams',
      'type' => 'link',
      'relationship' => 'activities_teams',
      'link_type' => 'many',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'contact_activities',
      'vname' => 'LBL_LIST_CONTACT_NAME',
      'source' => 'non-db',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'case_activities',
      'vname' => 'LBL_CASES',
      'source' => 'non-db',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'account_activities',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNTS',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'opportunity_activities',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITIES',
    ),
    'quotas' => 
    array (
      'name' => 'quotas',
      'type' => 'link',
      'relationship' => 'quota_activities',
      'source' => 'non-db',
      'vname' => 'LBL_QUOTAS',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'type' => 'link',
      'relationship' => 'lead_activities',
      'source' => 'non-db',
      'vname' => 'LBL_LEADS',
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'relationship' => 'product_activities',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCTS',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'revenuelineitem_activities',
      'source' => 'non-db',
      'vname' => 'LBL_REVENUELINEITEMS',
      'workflow' => true,
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quote_activities',
      'vname' => 'LBL_QUOTES',
      'source' => 'non-db',
    ),
    'contracts' => 
    array (
      'name' => 'contracts',
      'type' => 'link',
      'relationship' => 'contract_activities',
      'source' => 'non-db',
      'vname' => 'LBL_CONTRACTS',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'bug_activities',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'meeting_activities',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'call_activities',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'task_activities',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'note_activities',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'kbcontents' => 
    array (
      'name' => 'kbcontents',
      'type' => 'link',
      'relationship' => 'kbcontent_activities',
      'source' => 'non-db',
      'vname' => 'LBL_KBCONTENTS',
    ),
    'kbtemplates' => 
    array (
      'name' => 'kbtemplates',
      'type' => 'link',
      'relationship' => 'kbcontenttemplate_activities',
      'source' => 'non-db',
      'vname' => 'LBL_KBTEMPLATES',
    ),
    'campaigns' => 
    array (
      'name' => 'campaigns',
      'type' => 'link',
      'relationship' => 'campaign_activities',
      'source' => 'non-db',
      'vname' => 'LBL_CAMPAIGN',
    ),
    'pmse_Project' => 
    array (
      'name' => 'pmse_Project',
      'type' => 'link',
      'relationship' => 'pmse_project_activities',
      'source' => 'non-db',
      'vname' => 'LBL_PMSE_PROJECT_ACTIVITIES_TITLE',
    ),
    'pmse_Business_Rules' => 
    array (
      'name' => 'pmse_Business_Rules',
      'type' => 'link',
      'relationship' => 'pmse_business_rules_activities',
      'source' => 'non-db',
      'vname' => 'LBL_PMSE_BUSINESS_RULES_ACTIVITIES_TITLE',
    ),
    'pmse_Emails_Templates' => 
    array (
      'name' => 'pmse_Emails_Templates',
      'type' => 'link',
      'relationship' => 'pmse_emails_templates_activities',
      'source' => 'non-db',
      'vname' => 'LBL_PMSE_EMAILS_TEMPLATES_ACTIVITIES_TITLE',
    ),
    'purchasedlineitems' => 
    array (
      'name' => 'purchasedlineitems',
      'type' => 'link',
      'relationship' => 'purchasedlineitem_activities',
      'source' => 'non-db',
      'vname' => 'LBL_PLIS_ACTIVITIES',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'type' => 'id',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'type' => 'varchar',
      'len' => 100,
    ),
    'activity_type' => 
    array (
      'name' => 'activity_type',
      'type' => 'varchar',
      'len' => 100,
      'required' => true,
    ),
    'data' => 
    array (
      'name' => 'data',
      'type' => 'json',
      'dbType' => 'longtext',
      'required' => true,
    ),
    'comment_count' => 
    array (
      'name' => 'comment_count',
      'type' => 'int',
      'required' => true,
      'default' => 0,
    ),
    'last_comment' => 
    array (
      'name' => 'last_comment',
      'type' => 'json',
      'dbType' => 'longtext',
      'required' => true,
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
      'relationship' => 'activities_following',
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
      'relationship' => 'activities_favorite',
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
      'relationship' => 'activities_locked_fields',
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
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_activities_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_activities_del_d_m',
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
      'name' => 'idx_activities_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_activities_del_d_e',
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
      'name' => 'idx_activities_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'activity_records',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_type',
        1 => 'parent_id',
      ),
    ),
    1 => 
    array (
      'name' => 'activity_type_date',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'activity_type',
        1 => 'date_entered',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_activities_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
  ),
  'relationships' => 
  array (
    'activities_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'activities_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'activity_activities' => 
    array (
      'lhs_module' => 'Activities',
      'lhs_table' => 'activities',
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
      'relationship_role_column_value' => 'Activities',
    ),
    'comments' => 
    array (
      'lhs_module' => 'Activities',
      'lhs_table' => 'activities',
      'lhs_key' => 'id',
      'rhs_module' => 'Comments',
      'rhs_table' => 'comments',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'activities_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Activities',
      'user_field' => 'created_by',
    ),
    'activities_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Activities',
      'user_field' => 'created_by',
    ),
    'activities_locked_fields' => 
    array (
      'lhs_module' => 'Activities',
      'lhs_table' => 'activities',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Activities',
    ),
    'activities_audit' => 
    array (
      'lhs_module' => 'Activities',
      'lhs_table' => 'activities',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'activities_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'ignore_templates' => 
  array (
    0 => 'taggable',
    1 => 'commentlog',
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
  ),
  'acls' => 
  array (
    'SugarACLLockedFields' => true,
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
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);