<?php 
 $GLOBALS["dictionary"]["CJ_WebHook"]=array (
  'table' => 'cj_web_hooks',
  'audited' => false,
  'unified_search' => false,
  'icon' => 'sicon-customer-journey-lg',
  'duplicate_merge' => true,
  'activity_enabled' => false,
  'comment' => 'CJ_WebHook',
  'optimistic_lock' => true,
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
      'relationship' => 'cj_webhooks_created_by',
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
      'relationship' => 'cj_webhooks_modified_user',
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
      'relationship' => 'cj_webhook_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'url' => 
    array (
      'name' => 'url',
      'vname' => 'LBL_URL',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
    ),
    'error_message_path' => 
    array (
      'name' => 'error_message_path',
      'vname' => 'LBL_ERROR_MESSAGE_PATH',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'default' => 'message',
    ),
    'sort_order' => 
    array (
      'name' => 'sort_order',
      'vname' => 'LBL_SORT_ORDER',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 1,
    ),
    'request_method' => 
    array (
      'name' => 'request_method',
      'vname' => 'LBL_REQUEST_METHOD',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'cj_webhooks_request_method_list',
      'type' => 'enum',
      'default' => 'GET',
    ),
    'request_format' => 
    array (
      'name' => 'request_format',
      'vname' => 'LBL_REQUEST_FORMAT',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'cj_webhooks_request_format_list',
      'type' => 'enum',
      'default' => 'json',
    ),
    'response_format' => 
    array (
      'name' => 'response_format',
      'vname' => 'LBL_RESPONSE_FORMAT',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'cj_webhooks_response_format_list',
      'type' => 'enum',
      'default' => 'json',
    ),
    'trigger_event' => 
    array (
      'name' => 'trigger_event',
      'vname' => 'LBL_TRIGGER_EVENT',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'cj_webhooks_trigger_event_list',
      'type' => 'enum',
      'default' => '',
      'visibility_grid' => 
      array (
        'trigger' => 'parent_type',
        'values' => 
        array (
          'DRI_Workflow_Task_Templates' => 
          array (
            0 => '',
            1 => 'before_create',
            2 => 'after_create',
            3 => 'before_in_progress',
            4 => 'after_in_progress',
            5 => 'before_completed',
            6 => 'after_completed',
            7 => 'before_not_applicable',
            8 => 'after_not_applicable',
          ),
          'DRI_SubWorkflow_Templates' => 
          array (
            0 => '',
            1 => 'before_create',
            2 => 'after_create',
            3 => 'before_in_progress',
            4 => 'after_in_progress',
            5 => 'before_completed',
            6 => 'after_completed',
          ),
          'DRI_Workflow_Templates' => 
          array (
            0 => '',
            1 => 'before_create',
            2 => 'after_create',
            3 => 'before_in_progress',
            4 => 'after_in_progress',
            5 => 'before_completed',
            6 => 'after_completed',
            7 => 'before_delete',
            8 => 'after_delete',
          ),
        ),
      ),
    ),
    'request_body' => 
    array (
      'name' => 'request_body',
      'vname' => 'LBL_REQUEST_BODY',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'cj_webhooks_request_body_list',
      'type' => 'enum',
      'default' => 'journey_body',
      'dependency' => 'not(or(equal($request_method, "GET"), equal($request_method, "DELETE")))',
    ),
    'headers' => 
    array (
      'name' => 'headers',
      'vname' => 'LBL_HEADERS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'text',
    ),
    'custom_post_body' => 
    array (
      'name' => 'custom_post_body',
      'vname' => 'LBL_CUSTOM_POST_BODY',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'text',
      'dependency' => 'and(equal($request_body, "custom_body"), or(equal($request_method, "POST"), equal($request_method, "PUT"), equal($request_method, "PATCH")))',
    ),
    'ignore_errors' => 
    array (
      'name' => 'ignore_errors',
      'vname' => 'LBL_IGNORE_ERRORS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
    ),
    'active' => 
    array (
      'name' => 'active',
      'vname' => 'LBL_ACTIVE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => true,
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'vname' => 'LBL_PARENT_NAME',
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'parent',
      'len' => 255,
      'source' => 'non-db',
      'options' => 'cj_webhooks_parent_type_list',
      'parent_type' => 'cj_webhooks_parent_type_list',
      'id_name' => 'parent_id',
      'type_name' => 'parent_type',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'default' => 'DRI_Workflow_Templates',
      'type' => 'parent_type',
      'len' => 255,
      'dbType' => 'varchar',
      'options' => 'cj_webhooks_parent_type_list',
      'parent_type' => 'cj_webhooks_parent_type_list',
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
      'relationship' => 'cj_webhooks_following',
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
      'relationship' => 'cj_webhooks_favorite',
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
      'relationship' => 'cj_webhooks_tags',
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
      'relationship' => 'cj_webhooks_commentlog',
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
      'relationship' => 'cj_webhooks_locked_fields',
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
      'relationship' => 'cj_webhooks_team',
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
      'relationship' => 'cj_webhooks_team_count_relationship',
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
      'relationship' => 'cj_webhooks_teams',
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
      'name' => 'idx_cj_webhooks_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_cj_web_hooks_del_d_m',
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
      'name' => 'idx_cj_web_hooks_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_cj_web_hooks_del_d_e',
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
      'name' => 'idx_cj_web_hooks_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'cj_webhooks_parent_id' => 
    array (
      'name' => 'cj_webhooks_parent_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_cj_web_hooks_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_cj_web_hooks' => 
    array (
      'name' => 'idx_cj_web_hooks_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_cj_web_hooks' => 
    array (
      'name' => 'idx_cj_web_hooks_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
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
    'SugarACLAdminOnly' => true,
    'SugarACLCustomerJourney' => true,
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
  ),
  'relationships' => 
  array (
    'cj_webhooks_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'cj_webhooks_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'cj_webhook_activities' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
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
      'relationship_role_column_value' => 'CJ_WebHooks',
    ),
    'cj_webhooks_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'CJ_WebHooks',
      'user_field' => 'created_by',
    ),
    'cj_webhooks_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'CJ_WebHooks',
      'user_field' => 'created_by',
    ),
    'cj_webhooks_tags' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'CJ_WebHooks',
      'dynamic_subpanel' => true,
    ),
    'cj_webhooks_commentlog' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'CJ_WebHooks',
    ),
    'cj_webhooks_locked_fields' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'CJ_WebHooks',
    ),
    'cj_webhooks_audit' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'cj_web_hooks_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'cj_webhooks_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'cj_webhooks_teams' => 
    array (
      'lhs_module' => 'CJ_WebHooks',
      'lhs_table' => 'cj_web_hooks',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'cj_webhooks_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
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
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);