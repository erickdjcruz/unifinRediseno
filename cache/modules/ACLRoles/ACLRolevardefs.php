<?php 
 $GLOBALS["dictionary"]["ACLRole"]=array (
  'table' => 'acl_roles',
  'color' => 'teal',
  'icon' => 'sicon-role-mgmt-lg',
  'archive' => false,
  'comment' => 'ACL Role definition',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'required' => true,
      'type' => 'id',
      'reportable' => false,
      'comment' => 'Unique identifier',
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
      'vname' => 'LBL_MODIFIED',
      'type' => 'assigned_user_name',
      'table' => 'modified_user_id_users',
      'isnull' => 'false',
      'dbType' => 'id',
      'required' => false,
      'len' => 36,
      'reportable' => true,
      'comment' => 'User who last modified record',
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'created_by',
      'vname' => 'LBL_CREATED',
      'type' => 'assigned_user_name',
      'table' => 'created_by_users',
      'isnull' => 'false',
      'dbType' => 'id',
      'len' => 36,
      'comment' => 'User who created record',
    ),
    'name' => 
    array (
      'name' => 'name',
      'type' => 'varchar',
      'vname' => 'LBL_NAME',
      'len' => 150,
      'comment' => 'The role name',
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'comment' => 'The role description',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'reportable' => false,
      'comment' => 'Record deletion indicator',
    ),
    'users' => 
    array (
      'name' => 'users',
      'type' => 'link',
      'relationship' => 'acl_roles_users',
      'link_class' => 'UserLink',
      'source' => 'non-db',
      'vname' => 'LBL_USERS',
    ),
    'actions' => 
    array (
      'name' => 'actions',
      'type' => 'link',
      'relationship' => 'acl_roles_actions',
      'source' => 'non-db',
      'module' => 'ACLActions',
      'bean_name' => 'ACLAction',
      'rel_fields' => 
      array (
        'access_override' => 
        array (
          'type' => 'int',
        ),
      ),
    ),
    'access_override' => 
    array (
      'name' => 'access_override',
      'type' => 'int',
      'studio' => 'false',
      'source' => 'non-db',
      'massupdate' => false,
      'link' => 'actions',
      'rname_link' => 'access_override',
      'link_type' => 'one',
    ),
    'acl_role_sets' => 
    array (
      'name' => 'acl_role_sets',
      'type' => 'link',
      'source' => 'non-db',
      'relationship' => 'acl_role_sets_acl_roles',
    ),
  ),
  'acls' => 
  array (
    'SugarACLDeveloperOrAdmin' => 
    array (
      'aclModule' => 'Users',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'aclrolespk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_aclrole_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_aclrole_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);