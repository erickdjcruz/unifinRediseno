<?php 
 $GLOBALS["dictionary"]["Administration"]=array (
  'table' => 'config',
  'archive' => false,
  'comment' => 'System table containing system-wide definitions',
  'color' => 'teal',
  'icon' => 'sicon-settings-lg',
  'hidden_to_role_assignment' => true,
  'fields' => 
  array (
    'category' => 
    array (
      'name' => 'category',
      'vname' => 'LBL_LIST_SYMBOL',
      'type' => 'varchar',
      'len' => '32',
      'comment' => 'Settings are grouped under this category; arbitraily defined based on requirements',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_LIST_NAME',
      'type' => 'varchar',
      'len' => '32',
      'comment' => 'The name given to the setting',
    ),
    'value' => 
    array (
      'name' => 'value',
      'vname' => 'LBL_LIST_RATE',
      'type' => 'text',
      'comment' => 'The value given to the setting',
    ),
    'platform' => 
    array (
      'name' => 'platform',
      'vname' => 'LBL_LIST_PLATFORM',
      'type' => 'varchar',
      'len' => '32',
      'comment' => 'Which platform to send this back with vai the api',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'idx_config_cat',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'category',
      ),
    ),
  ),
  'acls' => 
  array (
    'SugarACLDeveloperForTarget' => 
    array (
      'allowUserRead' => true,
    ),
    'SugarACLStatic' => false,
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);