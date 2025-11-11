<?php 
 $GLOBALS["dictionary"]["ShiftException"]=array (
  'table' => 'shift_exceptions',
  'audited' => true,
  'activity_enabled' => false,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'duplicate_merge' => false,
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
      'relationship' => 'shiftexceptions_created_by',
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
      'relationship' => 'shiftexceptions_modified_user',
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
      'relationship' => 'shiftexception_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'timezone' => 
    array (
      'name' => 'timezone',
      'vname' => 'LBL_TIMEZONE',
      'type' => 'enum',
      'options' => 'timezone_dom',
      'comment' => 'Time Zone in which this Shift Exception operates',
      'required' => true,
      'audited' => true,
    ),
    'shift_exception_type' => 
    array (
      'name' => 'shift_exception_type',
      'vname' => 'LBL_TYPE',
      'type' => 'enum',
      'options' => 'shift_exception_type_dom',
      'len' => 50,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The Shift Exception is of this type',
      'required' => true,
      'audited' => true,
    ),
    'all_day' => 
    array (
      'name' => 'all_day',
      'vname' => 'LBL_ALL_DAY',
      'type' => 'bool',
      'default' => '1',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The Shift Exception is all day or not',
      'audited' => true,
    ),
    'start_date' => 
    array (
      'name' => 'start_date',
      'vname' => 'LBL_START_DATE',
      'type' => 'date',
      'comment' => 'The start date of the shift exception',
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'end_date',
        'blank' => false,
      ),
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'start_hour' => 
    array (
      'name' => 'start_hour',
      'vname' => 'LBL_START_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'star_hours',
      'comment' => 'The hour portion of the time this shift exception starts',
      'merge_filter' => 'enabled',
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'start_minutes' => 
    array (
      'name' => 'start_minutes',
      'vname' => 'LBL_START_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'start_hours',
      'comment' => 'The minute portion of the time this shift exception starts',
      'merge_filter' => 'enabled',
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'end_date' => 
    array (
      'name' => 'end_date',
      'vname' => 'LBL_END_DATE',
      'type' => 'date',
      'comment' => 'The end date of the shift exception',
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'end_hour' => 
    array (
      'name' => 'end_hour',
      'vname' => 'LBL_END_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'end_hours',
      'comment' => 'The hour portion of the time this shift exception ends',
      'merge_filter' => 'enabled',
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'end_minutes' => 
    array (
      'name' => 'end_minutes',
      'vname' => 'LBL_END_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'end_hours',
      'comment' => 'The minute portion of the time this shift exception ends',
      'merge_filter' => 'enabled',
      'required' => true,
      'audited' => true,
      'massupdate' => false,
    ),
    'enabled' => 
    array (
      'name' => 'enabled',
      'vname' => 'LBL_ENABLED',
      'type' => 'bool',
      'default' => '0',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'An indicator of whether shift exception is enabled',
    ),
    'shift_exceptions_users' => 
    array (
      'name' => 'shift_exceptions_users',
      'type' => 'link',
      'relationship' => 'shift_exceptions_users',
      'source' => 'non-db',
      'vname' => 'LBL_SHIFT_EXCEPTION_USERS_TITLE',
      'module' => 'Users',
      'bean_name' => 'User',
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
      'relationship' => 'shiftexceptions_following',
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
      'relationship' => 'shiftexceptions_favorite',
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
      'relationship' => 'shiftexceptions_tags',
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
      'relationship' => 'shiftexceptions_commentlog',
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
      'relationship' => 'shiftexceptions_locked_fields',
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
      'relationship' => 'shiftexceptions_assigned_user',
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
      'relationship' => 'shiftexceptions_team',
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
      'relationship' => 'shiftexceptions_team_count_relationship',
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
      'relationship' => 'shiftexceptions_teams',
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
  'relationships' => 
  array (
    'shiftexceptions_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'shiftexceptions_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'shiftexception_activities' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
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
      'relationship_role_column_value' => 'ShiftExceptions',
    ),
    'shiftexceptions_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'ShiftExceptions',
      'user_field' => 'created_by',
    ),
    'shiftexceptions_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'ShiftExceptions',
      'user_field' => 'created_by',
    ),
    'shiftexceptions_tags' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'ShiftExceptions',
      'dynamic_subpanel' => true,
    ),
    'shiftexceptions_commentlog' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'ShiftExceptions',
    ),
    'shiftexceptions_locked_fields' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'ShiftExceptions',
    ),
    'shiftexceptions_audit' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'shift_exceptions_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'shiftexceptions_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'shiftexceptions_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'shiftexceptions_teams' => 
    array (
      'lhs_module' => 'ShiftExceptions',
      'lhs_table' => 'shift_exceptions',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'shiftexceptions_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'ShiftExceptions',
      'rhs_table' => 'shift_exceptions',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_shiftexceptions_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_shift_exceptions_del_d_m',
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
      'name' => 'idx_shift_exceptions_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_shift_exceptions_del_d_e',
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
      'name' => 'idx_shift_exceptions_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_shift_exceptions_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_shift_exceptions_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_shift_exceptions' => 
    array (
      'name' => 'idx_shift_exceptions_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_shift_exceptions' => 
    array (
      'name' => 'idx_shift_exceptions_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
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