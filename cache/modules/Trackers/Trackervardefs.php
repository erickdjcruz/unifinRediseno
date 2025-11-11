<?php 
 $GLOBALS["dictionary"]["Tracker"]=array (
  'table' => 'tracker',
  'archive' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'int',
      'len' => '11',
      'isnull' => 'false',
      'auto_increment' => true,
      'readonly' => true,
      'reportable' => true,
    ),
    'monitor_id' => 
    array (
      'name' => 'monitor_id',
      'vname' => 'LBL_MONITOR_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
    ),
    'user_id' => 
    array (
      'name' => 'user_id',
      'vname' => 'LBL_USER_ID',
      'type' => 'id',
      'isnull' => 'false',
    ),
    'module_name' => 
    array (
      'name' => 'module_name',
      'vname' => 'LBL_MODULE_NAME',
      'type' => 'varchar',
      'len' => '255',
      'isnull' => 'false',
    ),
    'item_id' => 
    array (
      'name' => 'item_id',
      'vname' => 'LBL_ITEM_ID',
      'type' => 'id',
      'isnull' => 'false',
    ),
    'item_summary' => 
    array (
      'name' => 'item_summary',
      'vname' => 'LBL_ITEM_SUMMARY',
      'type' => 'varchar',
      'len' => '255',
      'isnull' => 'false',
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'vname' => 'LBL_TEAM_ID',
      'type' => 'id',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_LAST_ACTION',
      'type' => 'datetime',
      'isnull' => 'false',
    ),
    'action' => 
    array (
      'name' => 'action',
      'vname' => 'LBL_ACTION',
      'type' => 'varchar',
      'len' => '255',
      'isnull' => 'false',
    ),
    'session_id' => 
    array (
      'name' => 'session_id',
      'vname' => 'LBL_SESSION_ID',
      'type' => 'id',
      'isnull' => 'true',
      'exportable' => false,
    ),
    'visible' => 
    array (
      'name' => 'visible',
      'vname' => 'LBL_VISIBLE',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'comment' => 'Record deletion indicator',
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'tracker_user_id',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'monitor_id_link' => 
    array (
      'name' => 'monitor_id_link',
      'type' => 'link',
      'relationship' => 'tracker_monitor_id',
      'vname' => 'LBL_MONITOR_ID',
      'link_type' => 'one',
      'module' => 'TrackerPerfs',
      'bean_name' => 'TrackerPerf',
      'source' => 'non-db',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'tracker_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_tracker_iid',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'item_id',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_tracker_userid_vis_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_id',
        1 => 'visible',
        2 => 'id',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_tracker_userid_itemid_vis',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_id',
        1 => 'item_id',
        2 => 'visible',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_tracker_userid_del_vis',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_id',
        1 => 'deleted',
        2 => 'visible',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_tracker_monitor_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'monitor_id',
      ),
    ),
    6 => 
    array (
      'name' => 'idx_tracker_date_modified',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'date_modified',
      ),
    ),
    7 => 
    array (
      'name' => 'idx_trckr_mod_uid_dtmod_item',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'module_name',
        1 => 'user_id',
        2 => 'date_modified',
        3 => 'item_id',
      ),
    ),
    8 => 
    array (
      'name' => 'idx_userid_module_itemid_summary_datemodified',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_id',
        1 => 'module_name',
        2 => 'item_id',
        3 => 'item_summary',
        4 => 'date_modified',
      ),
    ),
  ),
  'relationships' => 
  array (
    'tracker_monitor_id' => 
    array (
      'lhs_module' => 'TrackerPerfs',
      'lhs_table' => 'tracker_perf',
      'lhs_key' => 'monitor_id',
      'rhs_module' => 'Trackers',
      'rhs_table' => 'tracker',
      'rhs_key' => 'monitor_id',
      'relationship_type' => 'one-to-one',
    ),
  ),
  'acls' => 
  array (
    'SugarACLStatic' => true,
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);