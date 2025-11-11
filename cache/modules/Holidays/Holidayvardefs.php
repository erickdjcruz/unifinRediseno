<?php 
 $GLOBALS["dictionary"]["Holiday"]=array (
  'table' => 'holidays',
  'archive' => false,
  'favorites' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'required' => true,
      'type' => 'id',
      'reportable' => false,
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
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => false,
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
        'enabled' => false,
        'searchable' => false,
      ),
      'options' => 'date_range_search_dom',
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED',
      'type' => 'assigned_user_name',
      'table' => 'modified_user_id_users',
      'isnull' => false,
      'group' => 'modified_by_name',
      'massupdate' => false,
      'dbType' => 'id',
      'required' => false,
      'len' => 36,
      'reportable' => true,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
    ),
    'modified_by_name' => 
    array (
      'name' => 'modified_by_name',
      'vname' => 'LBL_MODIFIED',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'rname' => 'full_name',
      'table' => 'users',
      'id_name' => 'modified_user_id',
      'module' => 'Users',
      'link' => 'modified_user_link',
      'duplicate_merge' => 'disabled',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'created_by',
      'vname' => 'LBL_CREATED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'len' => 36,
      'group' => 'created_by_name',
      'comment' => 'User who created record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
      ),
    ),
    'created_by_name' => 
    array (
      'name' => 'created_by_name',
      'vname' => 'LBL_CREATED',
      'type' => 'relate',
      'reportable' => false,
      'link' => 'created_by_link',
      'rname' => 'full_name',
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'created_by',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'importable' => false,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'holidays_created_by',
      'vname' => 'LBL_CREATED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'modified_user_link' => 
    array (
      'name' => 'modified_user_link',
      'type' => 'link',
      'relationship' => 'holidays_modified_user',
      'vname' => 'LBL_MODIFIED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'holiday_date' => 
    array (
      'name' => 'holiday_date',
      'type' => 'date',
      'vname' => 'LBL_HOLIDAY_DATE',
      'required' => true,
      'importable' => 'required',
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'reportable' => false,
    ),
    'person_id' => 
    array (
      'name' => 'person_id',
      'type' => 'id',
      'vname' => 'LBL_PERSON_ID',
    ),
    'person_type' => 
    array (
      'name' => 'person_type',
      'type' => 'varchar',
      'vname' => 'LBL_PERSON_TYPE',
    ),
    'related_module' => 
    array (
      'name' => 'related_module',
      'type' => 'varchar',
      'vname' => 'LBL_RELATED_MODULE',
    ),
    'related_module_id' => 
    array (
      'name' => 'related_module_id',
      'type' => 'id',
      'vname' => 'LBL_RELATED_MODULE_ID',
    ),
    'resource_name' => 
    array (
      'name' => 'resource_name',
      'type' => 'varchar',
      'vname' => 'LBL_RESOURCE_NAME',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.55,
      ),
      'required' => true,
      'importable' => 'required',
      'duplicate_merge' => 'enabled',
      'merge_filter' => 'selected',
      'duplicate_on_record_copy' => 'always',
    ),
    'business_holidays' => 
    array (
      'name' => 'business_holidays',
      'type' => 'link',
      'relationship' => 'business_centers_holidays',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_HOLIDAYS',
      'module' => 'BusinessCenters',
      'bean_name' => 'BusinessCenter',
    ),
    'users' => 
    array (
      'name' => 'users',
      'type' => 'link',
      'relationship' => 'users_holidays',
      'source' => 'non-db',
      'vname' => 'LBL_USERS',
    ),
  ),
  'relationships' => 
  array (
    'holidays_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Holidays',
      'rhs_table' => 'holidays',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'holidays_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Holidays',
      'rhs_table' => 'holidays',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
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
      'name' => 'holidayspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_holiday_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_holiday_id_rel',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'related_module_id',
        1 => 'related_module',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_holiday_holiday_date',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'holiday_date',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);