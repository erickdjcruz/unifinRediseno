<?php 
 $GLOBALS["dictionary"]["DataSet"]=array (
  'table' => 'data_sets',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_NAME',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => true,
      'default' => '0',
      'reportable' => false,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'required' => true,
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'reportable' => true,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'type' => 'id',
      'reportable' => false,
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'vname' => 'LBL_PARENT_DATASET',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'data_sets',
      'id_name' => 'parent_id',
      'rname' => 'name',
      'module' => 'DataSets',
      'duplicate_merge' => 'disabled',
      'comment' => 'Parent data sets for the data set (Meta-data only)',
    ),
    'report_id' => 
    array (
      'name' => 'report_id',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
    ),
    'report_name' => 
    array (
      'name' => 'report_name',
      'vname' => 'LBL_REPORT_NAME',
      'type' => 'varchar',
      'reportable' => false,
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'comment' => 'Custom Queries for the data sets (Meta-data only)',
    ),
    'query_id' => 
    array (
      'name' => 'query_id',
      'vname' => 'LBL_QUERY_NAME',
      'type' => 'id',
      'required' => true,
      'importable' => 'required',
    ),
    'query_name' => 
    array (
      'name' => 'query_name',
      'vname' => 'LBL_QUERY_NAME',
      'type' => 'relate',
      'reportable' => false,
      'required' => true,
      'source' => 'non-db',
      'table' => 'custom_queries',
      'id_name' => 'query_id',
      'rname' => 'name',
      'module' => 'CustomQueries',
      'duplicate_merge' => 'disabled',
      'comment' => 'Custom Queries for the data sets (Meta-data only)',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'varchar',
      'len' => '50',
      'importable' => 'required',
    ),
    'list_order_y' => 
    array (
      'name' => 'list_order_y',
      'vname' => 'LBL_LISTORDER_Y',
      'type' => 'int',
      'len' => '3',
      'default' => '0',
      'importable' => 'required',
    ),
    'exportable' => 
    array (
      'name' => 'exportable',
      'vname' => 'LBL_EXPORTABLE',
      'dbType' => 'varchar',
      'type' => 'bool',
      'len' => '3',
      'default' => '0',
    ),
    'header' => 
    array (
      'name' => 'header',
      'vname' => 'LBL_HEADER',
      'dbType' => 'varchar',
      'type' => 'bool',
      'len' => '3',
      'default' => '0',
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
    ),
    'table_width' => 
    array (
      'name' => 'table_width',
      'vname' => 'LBL_TABLE_WIDTH',
      'type' => 'varchar',
      'dbType' => 'varchar',
      'len' => '3',
      'default' => '0',
    ),
    'font_size' => 
    array (
      'name' => 'font_size',
      'vname' => 'LBL_FONT_SIZE',
      'type' => 'enum',
      'options' => 'font_size_dom',
      'len' => 8,
      'default' => '0',
    ),
    'output_default' => 
    array (
      'name' => 'output_default',
      'vname' => 'LBL_OUTPUT_DEFAULT',
      'type' => 'enum',
      'options' => 'dataset_output_default_dom',
      'len' => 100,
    ),
    'prespace_y' => 
    array (
      'name' => 'prespace_y',
      'vname' => 'LBL_PRESPACE_Y',
      'type' => 'bool',
      'dbType' => 'varchar',
      'len' => '3',
      'default' => '0',
    ),
    'use_prev_header' => 
    array (
      'name' => 'use_prev_header',
      'vname' => 'LBL_USE_PREV_HEADER',
      'type' => 'bool',
      'dbType' => 'varchar',
      'len' => '3',
      'default' => '0',
    ),
    'header_back_color' => 
    array (
      'name' => 'header_back_color',
      'vname' => 'LBL_HEADER_BACK_COLOR',
      'type' => 'enum',
      'options' => 'report_color_dom',
      'len' => 100,
    ),
    'body_back_color' => 
    array (
      'name' => 'body_back_color',
      'vname' => 'LBL_BODY_BACK_COLOR',
      'type' => 'enum',
      'options' => 'report_color_dom',
      'len' => 100,
    ),
    'header_text_color' => 
    array (
      'name' => 'header_text_color',
      'vname' => 'LBL_HEADER_TEXT_COLOR',
      'type' => 'enum',
      'options' => 'report_color_dom',
      'len' => 100,
    ),
    'body_text_color' => 
    array (
      'name' => 'body_text_color',
      'vname' => 'LBL_BODY_TEXT_COLOR',
      'type' => 'enum',
      'options' => 'report_color_dom',
      'len' => 100,
    ),
    'table_width_type' => 
    array (
      'name' => 'table_width_type',
      'vname' => 'LBL_TABLE_WIDTH_TYPE',
      'type' => 'enum',
      'options' => 'width_type_dom',
      'len' => 3,
    ),
    'custom_layout' => 
    array (
      'name' => 'custom_layout',
      'vname' => 'LBL_CUSTOM_LAYOUT',
      'type' => 'enum',
      'options' => 'custom_layout_dom',
      'len' => 10,
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
      'relationship' => 'datasets_team',
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
      'relationship' => 'datasets_team_count_relationship',
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
      'relationship' => 'datasets_teams',
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
  'acls' => 
  array (
    'SugarACLAdminOnly' => 
    array (
      'allowUserRead' => true,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'dataset_k',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_dataset',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'team_set_data_sets' => 
    array (
      'name' => 'idx_data_sets_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_data_sets' => 
    array (
      'name' => 'idx_data_sets_acl_tmst_id',
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
    'datasets_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'DataSets',
      'rhs_table' => 'data_sets',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'datasets_teams' => 
    array (
      'lhs_module' => 'DataSets',
      'lhs_table' => 'data_sets',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'datasets_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'DataSets',
      'rhs_table' => 'data_sets',
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