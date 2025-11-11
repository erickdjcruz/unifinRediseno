<?php 
 $GLOBALS["dictionary"]["OutboundEmail"]=array (
  'table' => 'outbound_email',
  'color' => 'ocean',
  'icon' => 'sicon-email-lg',
  'archive' => false,
  'hidden_to_role_assignment' => true,
  'acls' => 
  array (
    'SugarACLOutboundEmail' => true,
    'SugarACLStatic' => true,
  ),
  'favorites' => true,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
      'mandatory_fetch' => true,
    ),
    'eapm_id' => 
    array (
      'name' => 'eapm_id',
      'vname' => 'LBL_EAPM_ID',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'mandatory_fetch' => true,
      'readonly' => true,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'required' => true,
      'reportable' => false,
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_TYPE',
      'type' => 'varchar',
      'len' => 15,
      'required' => true,
      'default' => 'user',
      'reportable' => false,
      'mandatory_fetch' => true,
      'readonly' => true,
    ),
    'user_id' => 
    array (
      'name' => 'user_id',
      'vname' => 'LBL_USER_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
      'mandatory_fetch' => true,
      'readonly' => true,
    ),
    'email_addresses' => 
    array (
      'name' => 'email_addresses',
      'relationship' => 'outbound_email_email_addresses',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_EMAIL_ADDRESSES',
    ),
    'email_address_id' => 
    array (
      'name' => 'email_address_id',
      'duplicate_merge' => 'disabled',
      'id_name' => 'email_address_id',
      'link' => 'email_addresses',
      'massupdate' => false,
      'module' => 'EmailAddresses',
      'reportable' => false,
      'rname' => 'id',
      'table' => 'email_addresses',
      'type' => 'id',
      'vname' => 'LBL_EMAIL_ADDRESS_ID',
    ),
    'email_address' => 
    array (
      'name' => 'email_address',
      'id_name' => 'email_address_id',
      'link' => 'email_addresses',
      'module' => 'EmailAddresses',
      'required' => true,
      'rname' => 'email_address',
      'source' => 'non-db',
      'table' => 'email_addresses',
      'type' => 'relate',
      'vname' => 'LBL_FROM_EMAIL_ADDRESS',
    ),
    'authorized_account' => 
    array (
      'name' => 'authorized_account',
      'vname' => 'LBL_AUTHORIZED_ACCOUNT',
      'type' => 'varchar',
      'readonly' => true,
    ),
    'mail_authtype' => 
    array (
      'name' => 'mail_authtype',
      'vname' => 'LBL_MAIL_AUTHTYPE',
      'type' => 'varchar',
      'len' => '10',
      'mandatory_fetch' => true,
      'readonly' => true,
    ),
    'reply_to_name' => 
    array (
      'name' => 'reply_to_name',
      'vname' => 'LBL_REPLY_TO_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'reportable' => false,
      'dependency' => 'not(equal($type, "system"))',
    ),
    'reply_to_email_addresses' => 
    array (
      'name' => 'reply_to_email_addresses',
      'relationship' => 'outbound_email_reply_to_email_addresses',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_REPLY_TO_EMAIL_ADDRESSES',
    ),
    'reply_to_email_address_id' => 
    array (
      'name' => 'reply_to_email_address_id',
      'duplicate_merge' => 'disabled',
      'id_name' => 'reply_to_email_address_id',
      'link' => 'reply_to_email_addresses',
      'massupdate' => false,
      'module' => 'EmailAddresses',
      'reportable' => false,
      'rname' => 'id',
      'table' => 'email_addresses',
      'type' => 'id',
      'vname' => 'LBL_REPLY_TO_EMAIL_ADDRESS_ID',
    ),
    'reply_to_email_address' => 
    array (
      'name' => 'reply_to_email_address',
      'id_name' => 'reply_to_email_address_id',
      'link' => 'reply_to_email_addresses',
      'module' => 'EmailAddresses',
      'rname' => 'email_address',
      'source' => 'non-db',
      'table' => 'email_addresses',
      'type' => 'relate',
      'vname' => 'LBL_REPLY_TO_EMAIL_ADDRESS',
      'dependency' => 'not(equal($type, "system"))',
    ),
    'mail_sendtype' => 
    array (
      'name' => 'mail_sendtype',
      'vname' => 'LBL_MAIL_SENDTYPE',
      'type' => 'varchar',
      'len' => 8,
      'required' => true,
      'default' => 'SMTP',
      'reportable' => false,
    ),
    'mail_smtptype' => 
    array (
      'name' => 'mail_smtptype',
      'vname' => 'LBL_EMAIL_PROVIDER',
      'type' => 'enum',
      'options' => 'mail_smtptype_options',
      'len' => 20,
      'required' => true,
      'default' => 'other',
      'reportable' => false,
    ),
    'mail_smtpserver' => 
    array (
      'name' => 'mail_smtpserver',
      'vname' => 'LBL_MAIL_SMTPSERVER',
      'type' => 'varchar',
      'len' => 100,
      'required' => false,
      'reportable' => false,
      'mandatory_fetch' => true,
    ),
    'mail_smtpport' => 
    array (
      'name' => 'mail_smtpport',
      'vname' => 'LBL_MAIL_SMTPPORT',
      'type' => 'int',
      'len' => 5,
      'default' => 465,
      'reportable' => false,
      'disable_num_format' => true,
    ),
    'mail_smtpuser' => 
    array (
      'name' => 'mail_smtpuser',
      'vname' => 'LBL_MAIL_SMTPUSER',
      'type' => 'varchar',
      'len' => 100,
      'reportable' => false,
      'mandatory_fetch' => true,
    ),
    'mail_smtppass' => 
    array (
      'name' => 'mail_smtppass',
      'vname' => 'LBL_MAIL_SMTPPASS',
      'type' => 'encrypt',
      'len' => 255,
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'skip_password_validation' => true,
      'mandatory_fetch' => true,
    ),
    'mail_smtpauth_req' => 
    array (
      'name' => 'mail_smtpauth_req',
      'vname' => 'LBL_MAIL_SMTPAUTH_REQ',
      'type' => 'bool',
      'default' => 0,
      'reportable' => false,
      'mandatory_fetch' => true,
    ),
    'mail_smtpssl' => 
    array (
      'name' => 'mail_smtpssl',
      'vname' => 'LBL_MAIL_SMTPSSL',
      'type' => 'enum',
      'dbType' => 'int',
      'options' => 'email_settings_for_ssl',
      'len' => 1,
      'default' => 1,
      'reportable' => false,
    ),
    'preferred_sending_account' => 
    array (
      'name' => 'preferred_sending_account',
      'vname' => 'LBL_PREFERRED_SENDING_ACCOUNT',
      'type' => 'bool',
      'default' => 0,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
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
      'relationship' => 'outboundemail_team',
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
      'relationship' => 'outboundemail_team_count_relationship',
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
      'relationship' => 'outboundemail_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
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
      'relationship' => 'outboundemail_favorite',
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
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'outbound_email_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oe_type_idx',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'type',
      ),
    ),
    'team_set_outbound_email' => 
    array (
      'name' => 'idx_outbound_email_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_outbound_email' => 
    array (
      'name' => 'idx_outbound_email_acl_tmst_id',
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
    'outbound_email_email_addresses' => 
    array (
      'lhs_module' => 'EmailAddresses',
      'lhs_table' => 'email_addresses',
      'lhs_key' => 'id',
      'rhs_module' => 'OutboundEmail',
      'rhs_table' => 'outbound_email',
      'rhs_key' => 'email_address_id',
      'relationship_type' => 'one-to-many',
    ),
    'outbound_email_reply_to_email_addresses' => 
    array (
      'lhs_module' => 'EmailAddresses',
      'lhs_table' => 'email_addresses',
      'lhs_key' => 'id',
      'rhs_module' => 'OutboundEmail',
      'rhs_table' => 'outbound_email',
      'rhs_key' => 'reply_to_email_address_id',
      'relationship_type' => 'one-to-many',
    ),
    'outboundemail_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'OutboundEmail',
      'rhs_table' => 'outbound_email',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'outboundemail_teams' => 
    array (
      'lhs_module' => 'OutboundEmail',
      'lhs_table' => 'outbound_email',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'outboundemail_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'OutboundEmail',
      'rhs_table' => 'outbound_email',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'outboundemail_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'OutboundEmail',
      'rhs_table' => 'outbound_email',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'OutboundEmail',
      'user_field' => 'created_by',
    ),
  ),
  'ignore_templates' => 
  array (
    0 => 'default',
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
    'favorite' => 'favorite',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);