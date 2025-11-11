<?php 
 $GLOBALS["dictionary"]["TeamSetModule"]=array (
  'table' => 'team_sets_modules',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
      'required' => true,
    ),
    'team_set_id' => 
    array (
      'name' => 'team_set_id',
      'type' => 'id',
      'isnull' => false,
      'required' => true,
    ),
    'module_table_name' => 
    array (
      'name' => 'module_table_name',
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 128,
      'isnull' => false,
      'required' => true,
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
      'name' => 'team_sets_modulespk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_team_sets_modules',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);