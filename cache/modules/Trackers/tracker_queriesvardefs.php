<?php 
 $GLOBALS["dictionary"]["tracker_queries"]=array (
  'table' => 'tracker_queries',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'int',
      'len' => '11',
      'reportable' => true,
      'isnull' => 'false',
      'auto_increment' => true,
    ),
    'query_id' => 
    array (
      'name' => 'query_id',
      'vname' => 'LBL_QUERY_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
    ),
    'text' => 
    array (
      'name' => 'text',
      'vname' => 'LBL_SQL_TEXT',
      'type' => 'text',
      'isnull' => 'false',
    ),
    'query_hash' => 
    array (
      'name' => 'query_hash',
      'vname' => 'LBL_QUERY_HASH',
      'type' => 'varchar',
      'len' => '36',
      'reportable' => false,
      'isnull' => 'false',
    ),
    'sec_total' => 
    array (
      'name' => 'sec_total',
      'vname' => 'LBL_SEC_TOTAL',
      'type' => 'float',
      'dbType' => 'double',
      'isnull' => 'false',
    ),
    'sec_avg' => 
    array (
      'name' => 'sec_avg',
      'vname' => 'LBL_SEC_AVG',
      'type' => 'float',
      'dbType' => 'double',
      'isnull' => 'false',
    ),
    'run_count' => 
    array (
      'name' => 'run_count',
      'vname' => 'LBL_RUN_COUNT',
      'type' => 'int',
      'len' => '6',
      'isnull' => 'false',
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
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'isnull' => 'false',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'tracker_queries_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_tracker_queries_query_hash',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'query_hash',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_tracker_queries_query_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'query_id',
      ),
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