<?php 
 $GLOBALS["dictionary"]["Geocode"]=array (
  'table' => 'geocode',
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
    'parent_id' => 
    array (
      'required' => true,
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
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
      'len' => '36',
      'size' => '36',
    ),
    'parent_type' => 
    array (
      'required' => true,
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
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
    'parent_name' => 
    array (
      'required' => true,
      'name' => 'parent_name',
      'vname' => 'LBL_PARENT_NAME',
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
      'len' => '256',
      'size' => '256',
    ),
    'parent_user_name' => 
    array (
      'required' => true,
      'name' => 'parent_user_name',
      'vname' => 'LBL_PARENT_USER_NAME',
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
      'len' => '256',
      'size' => '256',
    ),
    'address' => 
    array (
      'name' => 'address',
      'vname' => 'LBL_ADDRESS',
      'type' => 'text',
      'help' => 'Geocode Address',
    ),
    'status' => 
    array (
      'duplicate_merge_dom_value' => 0,
      'required' => false,
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'massupdate' => true,
      'mandatory_fetch' => true,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => 100,
      'size' => '20',
      'options' => 'gc_status_list',
      'studio' => 'visible',
      'dependency' => false,
    ),
    'postalcode' => 
    array (
      'required' => true,
      'name' => 'postalcode',
      'vname' => 'LBL_POSTALCODE',
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
    'country' => 
    array (
      'required' => true,
      'name' => 'country',
      'vname' => 'LBL_COUNTRY',
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
    'latitude' => 
    array (
      'duplicate_merge_dom_value' => 0,
      'required' => false,
      'name' => 'latitude',
      'vname' => 'LBL_LATITUDE',
      'type' => 'decimal',
      'mandatory_fetch' => true,
      'massupdate' => true,
      'default' => 0.0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => '18',
      'size' => '20',
      'enable_range_search' => false,
      'precision' => '10',
      'readonly' => true,
    ),
    'longitude' => 
    array (
      'duplicate_merge_dom_value' => 0,
      'required' => false,
      'name' => 'longitude',
      'vname' => 'LBL_LONGITUDE',
      'type' => 'decimal',
      'mandatory_fetch' => true,
      'massupdate' => true,
      'default' => 0.0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'audited' => false,
      'reportable' => false,
      'merge_filter' => 'disabled',
      'len' => '18',
      'size' => '20',
      'enable_range_search' => false,
      'precision' => '10',
      'readonly' => true,
    ),
    'geocoded' => 
    array (
      'name' => 'geocoded',
      'vname' => 'LBL_GEOCODED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'yes',
      'comment' => '',
    ),
    'error_message' => 
    array (
      'name' => 'error_message',
      'vname' => 'LBL_ERROR_MESSAGE',
      'type' => 'text',
      'help' => 'Geocode Error Message',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_geocode_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_geocode_del_d_m',
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
      'name' => 'idx_geocode_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_geocode_del_d_e',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_entered',
        2 => 'id',
      ),
    ),
    'geocoded' => 
    array (
      'name' => 'idx_geocode_geocoded',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'geocoded',
        1 => 'id',
      ),
    ),
    'parent_id' => 
    array (
      'name' => 'idx_geocode_p_i_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
        1 => 'id',
      ),
    ),
    'parent_type' => 
    array (
      'name' => 'idx_geocode_p_t_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_type',
        1 => 'id',
      ),
    ),
    'country' => 
    array (
      'name' => 'idx_geocode_country',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'country',
      ),
    ),
    'postalcode' => 
    array (
      'name' => 'idx_geocode_postal_code',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'postalcode',
      ),
    ),
    'status' => 
    array (
      'name' => 'idx_geocode_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
      ),
    ),
    'coords_from_zip' => 
    array (
      'name' => 'idx_coords_from_zip',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'postalcode',
        1 => 'country',
        2 => 'geocoded',
        3 => 'status',
      ),
    ),
    'coords_from_record' => 
    array (
      'name' => 'idx_coords_from_record',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_type',
        1 => 'parent_id',
        2 => 'geocoded',
        3 => 'status',
      ),
    ),
  ),
  'relationships' => 
  array (
  ),
  'optimistic_locking' => true,
  'portal_visibility' => 
  array (
  ),
  'ignore_templates' => 
  array (
    0 => 'integrate_fields',
    1 => 'default',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);