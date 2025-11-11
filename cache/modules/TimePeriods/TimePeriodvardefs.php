<?php 
 $GLOBALS["dictionary"]["TimePeriod"]=array (
  'table' => 'timeperiods',
  'archive' => false,
  'favorites' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_NAME',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_TP_NAME',
      'dbType' => 'varchar',
      'type' => 'timeperiod',
      'function' => 'get_timeperiods_dom',
      'len' => '36',
      'isnull' => false,
      'importable' => 'required',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'type' => 'id',
      'id_name' => 'id',
      'table' => 'timeperiods',
      'reportable' => false,
    ),
    'start_date' => 
    array (
      'name' => 'start_date',
      'vname' => 'LBL_TP_START_DATE',
      'type' => 'date',
      'isnull' => false,
      'importable' => 'required',
    ),
    'start_date_timestamp' => 
    array (
      'name' => 'start_date_timestamp',
      'vname' => 'LBL_TP_START_DATE_TIMESTAMP',
      'type' => 'int',
      'len' => '11',
      'required' => true,
      'enable_range_search' => true,
      'studio' => false,
      'reportable' => false,
    ),
    'end_date' => 
    array (
      'name' => 'end_date',
      'vname' => 'LBL_TP_END_DATE',
      'type' => 'date',
      'isnull' => false,
      'importable' => 'required',
    ),
    'end_date_timestamp' => 
    array (
      'name' => 'end_date_timestamp',
      'vname' => 'LBL_TP_END_DATE_TIMESTAMP',
      'type' => 'int',
      'len' => '11',
      'required' => true,
      'enable_range_search' => true,
      'studio' => false,
      'reportable' => false,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'vname' => 'LBL_CREATED_BY',
      'type' => 'id',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'reportable' => false,
    ),
    'is_fiscal' => 
    array (
      'name' => 'is_fiscal',
      'default' => 0,
      'vname' => 'LBL_TP_IS_FISCAL',
      'type' => 'bool',
    ),
    'is_fiscal_year' => 
    array (
      'name' => 'is_fiscal_year',
      'default' => 0,
      'vname' => 'LBL_TP_IS_FISCAL_YEAR',
      'type' => 'bool',
    ),
    'leaf_cycle' => 
    array (
      'name' => 'leaf_cycle',
      'vname' => 'LBL_LEAF_CYCLE',
      'type' => 'int',
      'len' => '2',
      'studio' => false,
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_TP_TYPE',
      'type' => 'enum',
      'options' => 'time_period_dom',
      'len' => '255',
      'audited' => true,
      'comment' => 'Time Period to be Forecast over',
      'merge_filter' => 'enabled',
      'importable' => 'required',
      'required' => true,
      'default' => '',
    ),
    'related_timeperiods' => 
    array (
      'name' => 'related_timeperiods',
      'type' => 'link',
      'relationship' => 'related_timeperiods',
      'link_type' => 'many',
      'side' => 'left',
      'source' => 'non-db',
    ),
  ),
  'acls' => 
  array (
    'SugarACLAdminOnly' => 
    array (
      'adminFor' => 'Forecasts',
      'allowUserRead' => true,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'timeperiodspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_del_type_startdate_enddate',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'type',
        2 => 'start_date',
        3 => 'end_date',
      ),
    ),
  ),
  'relationships' => 
  array (
    'related_timeperiods' => 
    array (
      'lhs_module' => 'TimePeriods',
      'lhs_table' => 'timeperiods',
      'lhs_key' => 'id',
      'rhs_module' => 'TimePeriods',
      'rhs_table' => 'timeperiods',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);