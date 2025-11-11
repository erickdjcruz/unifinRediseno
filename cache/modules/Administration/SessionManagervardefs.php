<?php 
 $GLOBALS["dictionary"]["SessionManager"]=array (
  'table' => 'session_active',
  'archive' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
    ),
    'session_id' => 
    array (
      'name' => 'session_id',
      'type' => 'varchar',
      'len' => '100',
    ),
    'last_request_time' => 
    array (
      'name' => 'last_request_time',
      'type' => 'datetime',
    ),
    'session_type' => 
    array (
      'name' => 'session_type',
      'type' => 'varchar',
      'len' => '100',
    ),
    'is_violation' => 
    array (
      'name' => 'is_violation',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
    ),
    'num_active_sessions' => 
    array (
      'name' => 'num_active_sessions',
      'type' => 'int',
      'default' => '0',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'type' => 'datetime',
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
      'len' => '1',
      'default' => '0',
      'required' => false,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'session_active_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_session_id',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'session_id',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);