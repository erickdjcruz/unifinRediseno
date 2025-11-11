<?php 
 $GLOBALS["dictionary"]["BusinessCenter"]=array (
  'table' => 'business_centers',
  'audited' => true,
  'color' => 'ocean',
  'icon' => 'sicon-business-centers-lg',
  'activity_enabled' => false,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'duplicate_merge' => false,
  'comment' => 'Business operations center details',
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
      'relationship' => 'businesscenters_created_by',
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
      'relationship' => 'businesscenters_modified_user',
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
      'relationship' => 'businesscenter_activities',
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
      'comment' => 'Time Zone in which this Business Center operates',
      'required' => true,
      'audited' => true,
    ),
    'address_street' => 
    array (
      'name' => 'address_street',
      'vname' => 'LBL_ADDRESS_STREET',
      'type' => 'text',
      'dbType' => 'varchar',
      'len' => '150',
      'comment' => 'Address of this Business Center',
      'group' => 'address',
      'group_label' => 'LBL_ADDRESS',
      'merge_filter' => 'enabled',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.26,
      ),
      'audited' => true,
    ),
    'address_city' => 
    array (
      'name' => 'address_city',
      'vname' => 'LBL_ADDRESS_CITY',
      'type' => 'varchar',
      'len' => '100',
      'comment' => 'City of this Business Center',
      'group' => 'address',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'address_state' => 
    array (
      'name' => 'address_state',
      'vname' => 'LBL_ADDRESS_STATE',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'address',
      'comment' => 'State of this Business Center',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'address_postalcode' => 
    array (
      'name' => 'address_postalcode',
      'vname' => 'LBL_ADDRESS_POSTALCODE',
      'type' => 'varchar',
      'len' => '20',
      'group' => 'address',
      'comment' => 'Postal Code of this Business Center',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'address_country' => 
    array (
      'name' => 'address_country',
      'vname' => 'LBL_ADDRESS_COUNTRY',
      'type' => 'varchar',
      'group' => 'address',
      'comment' => 'Country of this Business Center',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'business_holidays' => 
    array (
      'name' => 'business_holidays',
      'type' => 'link',
      'relationship' => 'business_centers_holidays',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_HOLIDAYS',
      'module' => 'Holidays',
      'bean_name' => 'Holiday',
    ),
    'business_center_accounts' => 
    array (
      'name' => 'business_center_accounts',
      'type' => 'link',
      'relationship' => 'business_center_accounts',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER_ACCOUNTS_TITLE',
      'module' => 'Accounts',
      'bean_name' => 'Account',
    ),
    'business_center_cases' => 
    array (
      'name' => 'business_center_cases',
      'type' => 'link',
      'relationship' => 'business_center_cases',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER_CASES_TITLE',
      'module' => 'Cases',
      'bean_name' => 'aCase',
    ),
    'business_center_contacts' => 
    array (
      'name' => 'business_center_contacts',
      'type' => 'link',
      'relationship' => 'business_center_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER_CONTACTS_TITLE',
      'module' => 'Contacts',
      'bean_name' => 'Contact',
    ),
    'business_center_leads' => 
    array (
      'name' => 'business_center_leads',
      'type' => 'link',
      'relationship' => 'business_center_leads',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER_LEADS_TITLE',
      'module' => 'Leads',
      'bean_name' => 'Lead',
    ),
    'business_center_users' => 
    array (
      'name' => 'business_center_users',
      'type' => 'link',
      'relationship' => 'business_center_users',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER_USERS_TITLE',
      'module' => 'Users',
      'bean_name' => 'User',
    ),
    'geocode_status' => 
    array (
      'studio' => false,
      'name' => 'geocode_status',
      'vname' => 'LBL_MAPS_GEOCODE_STATUS',
      'label' => 'LBL_MAPS_GEOCODE_STATUS',
      'type' => 'geocodestatus',
      'len' => 255,
      'comment' => '',
      'dbType' => 'varchar',
      'licenseFilter' => 
      array (
        0 => 'MAPS',
      ),
      'readonly' => true,
      'reportable' => false,
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
      'relationship' => 'businesscenters_following',
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
      'relationship' => 'businesscenters_favorite',
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
      'relationship' => 'businesscenters_tags',
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
      'relationship' => 'businesscenters_commentlog',
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
      'relationship' => 'businesscenters_locked_fields',
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
      'relationship' => 'businesscenters_assigned_user',
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
      'relationship' => 'businesscenters_team',
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
      'relationship' => 'businesscenters_team_count_relationship',
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
      'relationship' => 'businesscenters_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'is_open_sunday' => 
    array (
      'name' => 'is_open_sunday',
      'vname' => 'LBL_SUNDAY_HOURS',
      'type' => 'bool',
      'default' => '0',
      'group' => 'sunday_hours',
      'comment' => 'Explicit marker for if this business center is open on Sunday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'sunday_open_hour' => 
    array (
      'name' => 'sunday_open_hour',
      'vname' => 'LBL_SUNDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'sunday_hours',
      'comment' => 'The hour portion of the time this business center is open on Sunday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'sunday_open_minutes' => 
    array (
      'name' => 'sunday_open_minutes',
      'vname' => 'LBL_SUNDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'sunday_hours',
      'comment' => 'The minute portion of the time this business center is open on Sunday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'sunday_close_hour' => 
    array (
      'name' => 'sunday_close_hour',
      'vname' => 'LBL_SUNDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'sunday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Sunday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'sunday_close_minutes' => 
    array (
      'name' => 'sunday_close_minutes',
      'vname' => 'LBL_SUNDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'sunday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Sunday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_monday' => 
    array (
      'name' => 'is_open_monday',
      'vname' => 'LBL_MONDAY_HOURS',
      'type' => 'bool',
      'default' => '1',
      'group' => 'monday_hours',
      'comment' => 'Explicit marker for if this business center is open on Monday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'monday_open_hour' => 
    array (
      'name' => 'monday_open_hour',
      'vname' => 'LBL_MONDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'monday_hours',
      'comment' => 'The hour portion of the time this business center is open on Monday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'monday_open_minutes' => 
    array (
      'name' => 'monday_open_minutes',
      'vname' => 'LBL_MONDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'monday_hours',
      'comment' => 'The minute portion of the time this business center is open on Monday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'monday_close_hour' => 
    array (
      'name' => 'monday_close_hour',
      'vname' => 'LBL_MONDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'monday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Monday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'monday_close_minutes' => 
    array (
      'name' => 'monday_close_minutes',
      'vname' => 'LBL_MONDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'monday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Monday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_tuesday' => 
    array (
      'name' => 'is_open_tuesday',
      'vname' => 'LBL_TUESDAY_HOURS',
      'type' => 'bool',
      'default' => '1',
      'group' => 'tuesday_hours',
      'comment' => 'Explicit marker for if this business center is open on Tuesday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'tuesday_open_hour' => 
    array (
      'name' => 'tuesday_open_hour',
      'vname' => 'LBL_TUESDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'tuesday_hours',
      'comment' => 'The hour portion of the time this business center is open on Tuesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'tuesday_open_minutes' => 
    array (
      'name' => 'tuesday_open_minutes',
      'vname' => 'LBL_TUESDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'tuesday_hours',
      'comment' => 'The minute portion of the time this business center is open on Tuesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'tuesday_close_hour' => 
    array (
      'name' => 'tuesday_close_hour',
      'vname' => 'LBL_TUESDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'tuesday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Tuesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'tuesday_close_minutes' => 
    array (
      'name' => 'tuesday_close_minutes',
      'vname' => 'LBL_TUESDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'tuesday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Tuesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_wednesday' => 
    array (
      'name' => 'is_open_wednesday',
      'vname' => 'LBL_WEDNESDAY_HOURS',
      'type' => 'bool',
      'default' => '1',
      'group' => 'wednesday_hours',
      'comment' => 'Explicit marker for if this business center is open on Wednesday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'wednesday_open_hour' => 
    array (
      'name' => 'wednesday_open_hour',
      'vname' => 'LBL_WEDNESDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'wednesday_hours',
      'comment' => 'The hour portion of the time this business center is open on Wednesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'wednesday_open_minutes' => 
    array (
      'name' => 'wednesday_open_minutes',
      'vname' => 'LBL_WEDNESDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'wednesday_hours',
      'comment' => 'The minute portion of the time this business center is open on Wednesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'wednesday_close_hour' => 
    array (
      'name' => 'wednesday_close_hour',
      'vname' => 'LBL_WEDNESDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'wednesday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Wednesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'wednesday_close_minutes' => 
    array (
      'name' => 'wednesday_close_minutes',
      'vname' => 'LBL_WEDNESDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'wednesday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Wednesday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_thursday' => 
    array (
      'name' => 'is_open_thursday',
      'vname' => 'LBL_THURSDAY_HOURS',
      'type' => 'bool',
      'default' => '1',
      'group' => 'thursday_hours',
      'comment' => 'Explicit marker for if this business center is open on Thursday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'thursday_open_hour' => 
    array (
      'name' => 'thursday_open_hour',
      'vname' => 'LBL_THURSDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'thursday_hours',
      'comment' => 'The hour portion of the time this business center is open on Thursday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'thursday_open_minutes' => 
    array (
      'name' => 'thursday_open_minutes',
      'vname' => 'LBL_THURSDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'thursday_hours',
      'comment' => 'The minute portion of the time this business center is open on Thursday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'thursday_close_hour' => 
    array (
      'name' => 'thursday_close_hour',
      'vname' => 'LBL_THURSDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'thursday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Thursday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'thursday_close_minutes' => 
    array (
      'name' => 'thursday_close_minutes',
      'vname' => 'LBL_THURSDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'thursday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Thursday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_friday' => 
    array (
      'name' => 'is_open_friday',
      'vname' => 'LBL_FRIDAY_HOURS',
      'type' => 'bool',
      'default' => '1',
      'group' => 'friday_hours',
      'comment' => 'Explicit marker for if this business center is open on Friday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'friday_open_hour' => 
    array (
      'name' => 'friday_open_hour',
      'vname' => 'LBL_FRIDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'friday_hours',
      'comment' => 'The hour portion of the time this business center is open on Friday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'friday_open_minutes' => 
    array (
      'name' => 'friday_open_minutes',
      'vname' => 'LBL_FRIDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'friday_hours',
      'comment' => 'The minute portion of the time this business center is open on Friday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'friday_close_hour' => 
    array (
      'name' => 'friday_close_hour',
      'vname' => 'LBL_FRIDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'friday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Friday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'friday_close_minutes' => 
    array (
      'name' => 'friday_close_minutes',
      'vname' => 'LBL_FRIDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'friday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Friday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'is_open_saturday' => 
    array (
      'name' => 'is_open_saturday',
      'vname' => 'LBL_SATURDAY_HOURS',
      'type' => 'bool',
      'default' => '0',
      'group' => 'saturday_hours',
      'comment' => 'Explicit marker for if this business center is open on Saturday',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
    ),
    'saturday_open_hour' => 
    array (
      'name' => 'saturday_open_hour',
      'vname' => 'LBL_SATURDAY_OPEN_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'saturday_hours',
      'comment' => 'The hour portion of the time this business center is open on Saturday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'saturday_open_minutes' => 
    array (
      'name' => 'saturday_open_minutes',
      'vname' => 'LBL_SATURDAY_OPEN_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'saturday_hours',
      'comment' => 'The minute portion of the time this business center is open on Saturday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'saturday_close_hour' => 
    array (
      'name' => 'saturday_close_hour',
      'vname' => 'LBL_SATURDAY_CLOSE_HOUR',
      'type' => 'enum',
      'function' => 'getHoursDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'saturday_hours',
      'comment' => 'The hour portion of the time this business center is closed on Saturday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
    'saturday_close_minutes' => 
    array (
      'name' => 'saturday_close_minutes',
      'vname' => 'LBL_SATURDAY_CLOSE_MINUTES',
      'type' => 'enum',
      'function' => 'getMinutesDropdown',
      'function_bean' => 'BusinessCenters',
      'len' => 2,
      'group' => 'saturday_hours',
      'comment' => 'The minute portion of the time this business center is closed on Saturday',
      'merge_filter' => 'enabled',
      'audited' => true,
    ),
  ),
  'relationships' => 
  array (
    'businesscenters_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenters_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenter_activities' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
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
      'relationship_role_column_value' => 'BusinessCenters',
    ),
    'business_center_accounts' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Accounts',
      'rhs_table' => 'accounts',
      'rhs_key' => 'business_center_id',
      'relationship_type' => 'one-to-many',
    ),
    'business_center_cases' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'business_center_id',
      'relationship_type' => 'one-to-many',
    ),
    'business_center_contacts' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'business_center_id',
      'relationship_type' => 'one-to-many',
    ),
    'business_center_leads' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Leads',
      'rhs_table' => 'leads',
      'rhs_key' => 'business_center_id',
      'relationship_type' => 'one-to-many',
    ),
    'business_center_users' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'business_center_id',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenters_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'BusinessCenters',
      'user_field' => 'created_by',
    ),
    'businesscenters_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'BusinessCenters',
      'user_field' => 'created_by',
    ),
    'businesscenters_tags' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'BusinessCenters',
      'dynamic_subpanel' => true,
    ),
    'businesscenters_commentlog' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'BusinessCenters',
    ),
    'businesscenters_locked_fields' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'BusinessCenters',
    ),
    'businesscenters_audit' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'business_centers_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenters_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenters_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'businesscenters_teams' => 
    array (
      'lhs_module' => 'BusinessCenters',
      'lhs_table' => 'business_centers',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'businesscenters_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'BusinessCenters',
      'rhs_table' => 'business_centers',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'acls' => 
  array (
    'SugarACLAdminOnly' => 
    array (
      'allowUserRead' => true,
    ),
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
  ),
  'portal_visibility' => 
  array (
    'class' => 'BusinessCenters',
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_businesscenters_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_business_centers_del_d_m',
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
      'name' => 'idx_business_centers_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_business_centers_del_d_e',
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
      'name' => 'idx_business_centers_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_business_centers_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_business_centers_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_business_centers' => 
    array (
      'name' => 'idx_business_centers_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_business_centers' => 
    array (
      'name' => 'idx_business_centers_acl_tmst_id',
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
    'business_hours' => 'business_hours',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);