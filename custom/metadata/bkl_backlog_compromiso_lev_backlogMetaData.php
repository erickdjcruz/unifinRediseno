<?php
// created: 2025-11-04 19:20:40
$dictionary["bkl_backlog_compromiso_lev_backlog"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'bkl_backlog_compromiso_lev_backlog' => 
    array (
      'lhs_module' => 'lev_Backlog',
      'lhs_table' => 'lev_backlog',
      'lhs_key' => 'id',
      'rhs_module' => 'Bkl_Backlog_Compromiso',
      'rhs_table' => 'bkl_backlog_compromiso',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'bkl_backlog_compromiso_lev_backlog_c',
      'join_key_lhs' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
      'join_key_rhs' => 'bkl_backlog_compromiso_lev_backlogbkl_backlog_compromiso_idb',
    ),
  ),
  'table' => 'bkl_backlog_compromiso_lev_backlog_c',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
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
      'default' => 0,
    ),
    'bkl_backlog_compromiso_lev_backloglev_backlog_ida' => 
    array (
      'name' => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
      'type' => 'id',
    ),
    'bkl_backlog_compromiso_lev_backlogbkl_backlog_compromiso_idb' => 
    array (
      'name' => 'bkl_backlog_compromiso_lev_backlogbkl_backlog_compromiso_idb',
      'type' => 'id',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'idx_bkl_backlog_compromiso_lev_backlog_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_bkl_backlog_compromiso_lev_backlog_ida1_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'bkl_backlog_compromiso_lev_backloglev_backlog_ida',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_bkl_backlog_compromiso_lev_backlog_idb2_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'bkl_backlog_compromiso_lev_backlogbkl_backlog_compromiso_idb',
        1 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'bkl_backlog_compromiso_lev_backlog_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'bkl_backlog_compromiso_lev_backlogbkl_backlog_compromiso_idb',
      ),
    ),
  ),
);