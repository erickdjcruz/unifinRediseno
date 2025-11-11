<?php 
 $GLOBALS["dictionary"]["TeamMembership"]=array (
  'table' => 'team_memberships',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
      'required' => true,
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'type' => 'id',
    ),
    'user_id' => 
    array (
      'name' => 'user_id',
      'type' => 'id',
    ),
    'explicit_assign' => 
    array (
      'name' => 'explicit_assign',
      'type' => 'bool',
      'len' => 1,
      'default' => 0,
      'required' => true,
    ),
    'implicit_assign' => 
    array (
      'name' => 'implicit_assign',
      'type' => 'bool',
      'len' => 1,
      'default' => 0,
      'required' => true,
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => 1,
      'default' => 0,
    ),
  ),
  'acls' => 
  array (
    'SugarACLAdminOnly' => 
    array (
      'adminFor' => 'Users',
      'allowUserRead' => true,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'team_membershipspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_team_membership',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_id',
        1 => 'team_id',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_del_team_user',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'team_id',
        2 => 'user_id',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_teammemb_team_user',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'team_id',
        1 => 'user_id',
      ),
    ),
  ),
  'relationships' => 
  array (
    'team_memberships' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_memberships',
      'join_key_lhs' => 'team_id',
      'join_key_rhs' => 'user_id',
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);