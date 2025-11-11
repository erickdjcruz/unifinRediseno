<?php 
 $GLOBALS["dictionary"]["InboundEmail"]=array (
  'table' => 'inbound_email',
  'archive' => false,
  'comment' => 'Inbound email parameters',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'dbType' => 'varchar',
      'required' => true,
      'reportable' => false,
      'comment' => 'Unique identifier',
    ),
    'eapm_id' => 
    array (
      'name' => 'eapm_id',
      'vname' => 'LBL_EAPM_ID',
      'type' => 'id',
      'readonly' => true,
    ),
    'authorized_account' => 
    array (
      'name' => 'authorized_account',
      'vname' => 'LBL_AUTHORIZED_ACCOUNT',
      'type' => 'varchar',
      'readonly' => true,
    ),
    'auth_type' => 
    array (
      'name' => 'auth_type',
      'vname' => 'LBL_MAIL_AUTHTYPE',
      'type' => 'varchar',
      'len' => '10',
      'readonly' => true,
    ),
    'email_provider' => 
    array (
      'name' => 'email_provider',
      'vname' => 'LBL_EMAIL_PROVIDER',
      'type' => 'enum',
      'options' => 'mail_imaptype_options',
      'len' => 20,
      'default' => 'other',
      'required' => true,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => false,
      'default' => '0',
      'reportable' => false,
      'comment' => 'Record deltion indicator',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date record created',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date record last modified',
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED_BY',
      'type' => 'modified_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
    ),
    'modified_user_id_link' => 
    array (
      'name' => 'modified_user_id_link',
      'type' => 'link',
      'relationship' => 'inbound_email_modified_user_id',
      'vname' => 'LBL_MODIFIED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'comment' => 'User who created record',
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'inbound_email_created_by',
      'vname' => 'LBL_CREATED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'varchar',
      'len' => '255',
      'required' => false,
      'reportable' => false,
      'comment' => 'Name given to the inbound email mailbox',
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'varchar',
      'len' => 100,
      'default' => 'Active',
      'required' => true,
      'reportable' => false,
      'comment' => 'Status of the inbound email mailbox (ex: Active or Inactive)',
    ),
    'server_url' => 
    array (
      'name' => 'server_url',
      'vname' => 'LBL_SERVER_URL',
      'type' => 'varchar',
      'len' => '100',
      'required' => true,
      'reportable' => false,
      'comment' => 'Mail server URL',
      'importable' => 'required',
    ),
    'email_user' => 
    array (
      'name' => 'email_user',
      'vname' => 'LBL_LOGIN',
      'type' => 'varchar',
      'len' => '100',
      'required' => true,
      'reportable' => false,
      'comment' => 'User name allowed access to mail server',
    ),
    'email_password' => 
    array (
      'name' => 'email_password',
      'vname' => 'LBL_PASSWORD',
      'type' => 'encrypt',
      'len' => '255',
      'required' => true,
      'reportable' => false,
      'write_only' => true,
      'comment' => 'Password of user identified by email_user',
    ),
    'port' => 
    array (
      'name' => 'port',
      'vname' => 'LBL_SERVER_TYPE',
      'type' => 'int',
      'len' => '5',
      'required' => true,
      'reportable' => false,
      'validation' => 
      array (
        'type' => 'range',
        'min' => '110',
        'max' => '65535',
      ),
      'comment' => 'Port used to access mail server',
    ),
    'service' => 
    array (
      'name' => 'service',
      'vname' => 'LBL_SERVICE',
      'type' => 'varchar',
      'len' => '50',
      'required' => true,
      'reportable' => false,
      'comment' => '',
      'importable' => 'required',
    ),
    'mailbox' => 
    array (
      'name' => 'mailbox',
      'vname' => 'LBL_MAILBOX',
      'type' => 'text',
      'required' => true,
      'reportable' => false,
      'comment' => '',
    ),
    'delete_seen' => 
    array (
      'name' => 'delete_seen',
      'vname' => 'LBL_DELETE_SEEN',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'massupdate' => '',
      'comment' => 'Delete email from server once read (seen)',
    ),
    'mailbox_type' => 
    array (
      'name' => 'mailbox_type',
      'vname' => 'LBL_MAILBOX_TYPE',
      'type' => 'varchar',
      'len' => '10',
      'reportable' => false,
      'comment' => '',
    ),
    'template_id' => 
    array (
      'name' => 'template_id',
      'vname' => 'LBL_AUTOREPLY',
      'type' => 'id',
      'reportable' => false,
      'comment' => 'Template used for auto-reply',
    ),
    'stored_options' => 
    array (
      'name' => 'stored_options',
      'vname' => 'LBL_STORED_OPTIONS',
      'type' => 'text',
      'reportable' => false,
      'comment' => '',
    ),
    'group_id' => 
    array (
      'name' => 'group_id',
      'vname' => 'LBL_GROUP_ID',
      'type' => 'id',
      'reportable' => false,
      'comment' => 'Group ID (unused)',
    ),
    'is_personal' => 
    array (
      'name' => 'is_personal',
      'vname' => 'LBL_IS_PERSONAL',
      'type' => 'bool',
      'required' => true,
      'default' => '0',
      'reportable' => false,
      'massupdate' => '',
      'comment' => 'Personal account flag',
    ),
    'groupfolder_id' => 
    array (
      'name' => 'groupfolder_id',
      'vname' => 'LBL_GROUPFOLDER_ID',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'comment' => 'Unique identifier',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'inbound_email_emails',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
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
      'relationship' => 'inboundemail_team',
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
      'relationship' => 'inboundemail_team_count_relationship',
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
      'relationship' => 'inboundemail_teams',
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
      'name' => 'inbound_emailpk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
      ),
    ),
    'team_set_inbound_email' => 
    array (
      'name' => 'idx_inbound_email_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_inbound_email' => 
    array (
      'name' => 'idx_inbound_email_acl_tmst_id',
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
    'inbound_email_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'InboundEmail',
      'rhs_table' => 'inbound_email',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-one',
    ),
    'inbound_email_modified_user_id' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'InboundEmail',
      'rhs_table' => 'inbound_email',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-one',
    ),
    'inbound_email_emails' => 
    array (
      'lhs_module' => 'InboundEmail',
      'lhs_table' => 'inbound_email',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'mailbox_id',
      'relationship_type' => 'one-to-many',
    ),
    'inboundemail_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'InboundEmail',
      'rhs_table' => 'inbound_email',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'inboundemail_teams' => 
    array (
      'lhs_module' => 'InboundEmail',
      'lhs_table' => 'inbound_email',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'inboundemail_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'InboundEmail',
      'rhs_table' => 'inbound_email',
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