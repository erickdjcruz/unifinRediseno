<?php 
 $GLOBALS["dictionary"]["GeocodeJob"]=array (
  'table' => 'geocode_job',
  'archive' => false,
  'audited' => false,
  'activity_enabled' => false,
  'reassignable' => false,
  'duplicate_merge' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Unique identifier',
      'mandatory_fetch' => true,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'group' => 'created_by_name',
      'comment' => 'Date record created',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'group' => 'modified_by_name',
      'comment' => 'Date record last modified',
      'enable_range_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'sortable' => true,
      ),
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'options' => 'date_range_search_dom',
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Record deletion indicator',
    ),
    'status' => 
    array (
      'required' => true,
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => '64',
      'size' => '64',
    ),
    'processed_entity_success_count' => 
    array (
      'required' => true,
      'name' => 'processed_entity_success_count',
      'vname' => 'LBL_PROCESSED_ENTITY_SUCCESS',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => '64',
      'size' => '64',
    ),
    'processed_entity_failed_count' => 
    array (
      'required' => true,
      'name' => 'processed_entity_failed_count',
      'vname' => 'LBL_PROCESSED_ENTITY_FAILED',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => '64',
      'size' => '64',
    ),
    'addresses_data' => 
    array (
      'name' => 'addresses_data',
      'vname' => 'LBL_ADDRESSES_DATA',
      'type' => 'json',
      'dbType' => 'longtext',
      'comment' => '',
    ),
    'geocode_result' => 
    array (
      'name' => 'geocode_result',
      'vname' => 'LBL_GEOCODE_RESULT',
      'type' => 'json',
      'dbType' => 'longtext',
      'comment' => '',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_geocode_job_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_geocode_job_del_d_m',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_modified',
        2 => 'id',
      ),
    ),
    'deleted' => 
    array (
      'name' => 'idx_geocode_job_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_geocode_job_del_d_e',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_entered',
        2 => 'id',
      ),
    ),
    'status' => 
    array (
      'name' => 'idx_geocode_job_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
        1 => 'id',
      ),
    ),
  ),
  'relationships' => 
  array (
  ),
  'optimistic_locking' => true,
  'ignore_templates' => 
  array (
    0 => 'integrate_fields',
    1 => 'default',
  ),
  'portal_visibility' => 
  array (
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);