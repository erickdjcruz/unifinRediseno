<?php 
 $GLOBALS["dictionary"]["Project"]=array (
  'table' => 'project',
  'color' => 'army',
  'icon' => 'sicon-projects-lg',
  'favorites' => false,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => false,
  'comment' => 'Project',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'required' => true,
      'type' => 'id',
      'reportable' => true,
      'comment' => 'Unique identifier',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'comment' => 'Date record created',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'readonly' => true,
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
      'comment' => 'Date record last modified',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'type' => 'id',
      'vname' => 'LBL_ASSIGNED_USER_ID',
      'required' => false,
      'isnull' => false,
      'reportable' => false,
      'comment' => 'User assigned to this record',
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
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED_USER_ID',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
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
    ),
    'modified_by_name' => 
    array (
      'name' => 'modified_by_name',
      'vname' => 'LBL_MODIFIED_NAME',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'rname' => 'full_name',
      'table' => 'users',
      'id_name' => 'modified_user_id',
      'module' => 'Users',
      'link' => 'modified_user_link',
      'duplicate_merge' => 'disabled',
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_CREATED_BY',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'comment' => 'User who created record',
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
      'importable' => 'false',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'required' => true,
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => 50,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.84,
      ),
      'comment' => 'Project name',
      'importable' => 'required',
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'required' => false,
      'type' => 'text',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.46,
      ),
      'comment' => 'Project description',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => false,
      'reportable' => false,
      'default' => '0',
      'comment' => 'Record deletion indicator',
    ),
    'estimated_start_date' => 
    array (
      'name' => 'estimated_start_date',
      'vname' => 'LBL_DATE_START',
      'required' => true,
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'estimated_end_date',
        'blank' => true,
      ),
      'type' => 'date',
      'importable' => 'required',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
    ),
    'estimated_end_date' => 
    array (
      'name' => 'estimated_end_date',
      'vname' => 'LBL_DATE_END',
      'required' => true,
      'type' => 'date',
      'importable' => 'required',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'options' => 'project_status_dom',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'priority' => 
    array (
      'name' => 'priority',
      'vname' => 'LBL_PRIORITY',
      'type' => 'enum',
      'options' => 'projects_priority_options',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'is_template' => 
    array (
      'name' => 'is_template',
      'vname' => 'LBL_IS_TEMPLATE',
      'type' => 'bool',
      'required' => false,
      'default' => '0',
      'comment' => 'Should be checked if the project is a template',
      'massupdate' => false,
    ),
    'total_estimated_effort' => 
    array (
      'name' => 'total_estimated_effort',
      'type' => 'int',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_TOTAL_ESTIMATED_EFFORT',
    ),
    'total_actual_effort' => 
    array (
      'name' => 'total_actual_effort',
      'type' => 'int',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_TOTAL_ACTUAL_EFFORT',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'projects_accounts',
      'source' => 'non-db',
      'ignore_role' => true,
      'vname' => 'LBL_ACCOUNTS',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'projects_quotes',
      'source' => 'non-db',
      'ignore_role' => true,
      'vname' => 'LBL_QUOTES',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'projects_contacts',
      'source' => 'non-db',
      'ignore_role' => true,
      'vname' => 'LBL_CONTACTS',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'projects_opportunities',
      'source' => 'non-db',
      'ignore_role' => true,
      'vname' => 'LBL_OPPORTUNITIES',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'projects_notes',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'projects_messages',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'projects_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'projects_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'projects_calls',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_projects_rel',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'projecttask' => 
    array (
      'name' => 'projecttask',
      'type' => 'link',
      'relationship' => 'projects_project_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECT_TASKS',
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'projects_created_by',
      'vname' => 'LBL_CREATED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'modified_user_link' => 
    array (
      'name' => 'modified_user_link',
      'type' => 'link',
      'relationship' => 'projects_modified_user',
      'vname' => 'LBL_MODIFIED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'projects_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'assigned_user_name' => 
    array (
      'name' => 'assigned_user_name',
      'rname' => 'full_name',
      'id_name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_USER_NAME',
      'type' => 'relate',
      'table' => 'users',
      'module' => 'Users',
      'dbType' => 'varchar',
      'link' => 'assigned_user_link',
      'len' => '255',
      'source' => 'non-db',
      'exportable' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'projects_cases',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_CASES',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'projects_bugs',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'relationship' => 'projects_products',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCTS',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'projects_revenuelineitems',
      'source' => 'non-db',
      'vname' => 'LBL_REVENUELINEITEMS',
      'workflow' => true,
    ),
    'user_resources' => 
    array (
      'name' => 'user_resources',
      'type' => 'link',
      'relationship' => 'projects_users_resources',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_USER_RESOURCE',
    ),
    'contact_resources' => 
    array (
      'name' => 'contact_resources',
      'type' => 'link',
      'relationship' => 'projects_contacts_resources',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_CONTACTS_RESOURCE',
    ),
    'project_holidays' => 
    array (
      'name' => 'project_holidays',
      'type' => 'link',
      'relationship' => 'projects_holidays',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECT_HOLIDAYS',
      'module' => 'Holidays',
      'bean_name' => 'Holiday',
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
      'relationship' => 'project_team',
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
      'relationship' => 'project_team_count_relationship',
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
      'relationship' => 'project_teams',
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
    0 => 
    array (
      'name' => 'projects_primary_key_index',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_project_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_project_estimated_start_date',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'estimated_start_date',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_project_estimated_end_date',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'estimated_end_date',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_project_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
      ),
    ),
    'team_set_project' => 
    array (
      'name' => 'idx_project_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_project' => 
    array (
      'name' => 'idx_project_acl_tmst_id',
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
    'projects_notes' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_messages' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_tasks' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_meetings' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_calls' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_emails' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Project',
    ),
    'projects_project_tasks' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'ProjectTask',
      'rhs_table' => 'project_task',
      'rhs_key' => 'project_id',
      'relationship_type' => 'one-to-many',
    ),
    'projects_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Project',
      'rhs_table' => 'project',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'projects_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Project',
      'rhs_table' => 'project',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'projects_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Project',
      'rhs_table' => 'project',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'projects_users_resources' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'project_resources',
      'join_key_lhs' => 'project_id',
      'join_key_rhs' => 'resource_id',
      'relationship_role_column' => 'resource_type',
      'relationship_role_column_value' => 'Users',
    ),
    'projects_contacts_resources' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'project_resources',
      'join_key_lhs' => 'project_id',
      'join_key_rhs' => 'resource_id',
      'relationship_role_column' => 'resource_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'projects_holidays' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'id',
      'rhs_module' => 'Holidays',
      'rhs_table' => 'holidays',
      'rhs_key' => 'related_module_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'related_module',
      'relationship_role_column_value' => 'Project',
    ),
    'project_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Project',
      'rhs_table' => 'project',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'project_teams' => 
    array (
      'lhs_module' => 'Project',
      'lhs_table' => 'project',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'project_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Project',
      'rhs_table' => 'project',
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
    'SugarACLStatic' => true,
  ),
  'templates' => 
  array (
    'team_security' => 'team_security',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);