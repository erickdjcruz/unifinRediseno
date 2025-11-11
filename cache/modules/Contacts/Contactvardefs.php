<?php 
 $GLOBALS["dictionary"]["Contact"]=array (
  'table' => 'contacts',
  'audited' => true,
  'activity_enabled' => true,
  'color' => 'pink',
  'icon' => 'sicon-contact-lg',
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'duplicate_merge' => true,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => true,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Unique identifier',
      'mandatory_fetch' => true,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'fullname',
      'fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
        2 => 'salutation',
        3 => 'title',
      ),
      'sort_on' => 'last_name',
      'source' => 'non-db',
      'group' => 'last_name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'importable' => 'false',
      'duplicate_on_record_copy' => 'always',
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
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'group' => 'modified_by_name',
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'modified_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_MODIFIED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
      ),
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
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_CREATED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'group' => 'created_by_name',
      'comment' => 'User who created record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'created_by' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_CREATED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
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
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'comment' => 'Full text of the note',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.71,
      ),
      'rows' => 6,
      'cols' => 80,
      'duplicate_on_record_copy' => 'always',
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
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'contacts_created_by',
      'vname' => 'LBL_CREATED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'modified_user_link' => 
    array (
      'name' => 'modified_user_link',
      'type' => 'link',
      'relationship' => 'contacts_modified_user',
      'vname' => 'LBL_MODIFIED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'activities' => 
    array (
      'name' => 'activities',
      'type' => 'link',
      'relationship' => 'contact_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'salutation' => 
    array (
      'name' => 'salutation',
      'vname' => 'LBL_SALUTATION',
      'type' => 'enum',
      'options' => 'salutation_dom',
      'massupdate' => false,
      'len' => '255',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'Contact salutation (e.g., Mr, Ms)',
      'audited' => true,
      'pii' => true,
    ),
    'first_name' => 
    array (
      'name' => 'first_name',
      'vname' => 'LBL_FIRST_NAME',
      'type' => 'varchar',
      'len' => '255',
      'unified_search' => true,
      'duplicate_on_record_copy' => 'always',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.99,
      ),
      'comment' => 'First name of the contact',
      'merge_filter' => 'selected',
      'audited' => true,
      'pii' => true,
    ),
    'last_name' => 
    array (
      'name' => 'last_name',
      'vname' => 'LBL_LAST_NAME',
      'type' => 'varchar',
      'len' => '255',
      'unified_search' => true,
      'duplicate_on_record_copy' => 'always',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.97,
      ),
      'comment' => 'Last name of the contact',
      'merge_filter' => 'selected',
      'required' => true,
      'importable' => 'required',
      'audited' => true,
      'pii' => true,
    ),
    'full_name' => 
    array (
      'name' => 'full_name',
      'vname' => 'LBL_NAME',
      'type' => 'fullname',
      'fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
        2 => 'salutation',
        3 => 'title',
      ),
      'sort_on' => 'last_name',
      'source' => 'non-db',
      'group' => 'last_name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'studio' => 
      array (
        'listview' => false,
      ),
      'duplicate_on_record_copy' => 'always',
    ),
    'title' => 
    array (
      'name' => 'title',
      'vname' => 'LBL_TITLE',
      'type' => 'varchar',
      'len' => '100',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The title of the contact',
      'audited' => true,
      'pii' => true,
    ),
    'facebook' => 
    array (
      'name' => 'facebook',
      'vname' => 'LBL_FACEBOOK',
      'type' => 'varchar',
      'len' => '100',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The facebook name of the user',
      'audited' => true,
      'pii' => true,
    ),
    'twitter' => 
    array (
      'name' => 'twitter',
      'vname' => 'LBL_TWITTER',
      'type' => 'varchar',
      'len' => '100',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The twitter name of the user',
      'audited' => true,
      'pii' => true,
    ),
    'googleplus' => 
    array (
      'name' => 'googleplus',
      'vname' => 'LBL_GOOGLEPLUS',
      'type' => 'varchar',
      'len' => '100',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The google plus id of the user',
      'audited' => true,
      'pii' => true,
    ),
    'department' => 
    array (
      'name' => 'department',
      'vname' => 'LBL_DEPARTMENT',
      'type' => 'varchar',
      'len' => '255',
      'duplicate_on_record_copy' => 'always',
      'comment' => 'The department of the contact',
      'merge_filter' => 'enabled',
    ),
    'do_not_call' => 
    array (
      'name' => 'do_not_call',
      'vname' => 'LBL_DO_NOT_CALL',
      'type' => 'bool',
      'default' => '0',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'An indicator of whether contact can be called',
    ),
    'phone_home' => 
    array (
      'name' => 'phone_home',
      'vname' => 'LBL_HOME_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'duplicate_on_record_copy' => 'always',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.1,
      ),
      'comment' => 'Home phone number of the contact',
      'merge_filter' => 'enabled',
      'audited' => true,
      'pii' => true,
    ),
    'phone_mobile' => 
    array (
      'name' => 'phone_mobile',
      'vname' => 'LBL_MOBILE_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.09,
      ),
      'comment' => 'Mobile phone number of the contact',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'phone_work' => 
    array (
      'name' => 'phone_work',
      'vname' => 'LBL_OFFICE_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'audited' => true,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.08,
      ),
      'comment' => 'Work phone number of the contact',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'pii' => true,
    ),
    'phone_other' => 
    array (
      'name' => 'phone_other',
      'vname' => 'LBL_OTHER_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.07,
      ),
      'comment' => 'Other phone number for the contact',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'phone_fax' => 
    array (
      'name' => 'phone_fax',
      'vname' => 'LBL_FAX_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.06,
      ),
      'comment' => 'Contact fax number',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'primary_address_street' => 
    array (
      'name' => 'primary_address_street',
      'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
      'type' => 'text',
      'dbType' => 'varchar',
      'len' => '150',
      'comment' => 'The street address used for primary address',
      'group' => 'primary_address',
      'group_label' => 'LBL_PRIMARY_ADDRESS',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.33,
      ),
      'audited' => true,
      'pii' => true,
      'rows' => 2,
      'cols' => 20,
    ),
    'primary_address_street_2' => 
    array (
      'name' => 'primary_address_street_2',
      'vname' => 'LBL_PRIMARY_ADDRESS_STREET_2',
      'type' => 'varchar',
      'len' => '150',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
      'massupdate' => false,
    ),
    'primary_address_street_3' => 
    array (
      'name' => 'primary_address_street_3',
      'vname' => 'LBL_PRIMARY_ADDRESS_STREET_3',
      'type' => 'varchar',
      'len' => '150',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
      'massupdate' => false,
    ),
    'primary_address_city' => 
    array (
      'name' => 'primary_address_city',
      'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'primary_address',
      'comment' => 'City for primary address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'primary_address_state' => 
    array (
      'name' => 'primary_address_state',
      'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'primary_address',
      'comment' => 'State for primary address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'primary_address_postalcode' => 
    array (
      'name' => 'primary_address_postalcode',
      'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
      'type' => 'varchar',
      'len' => '20',
      'group' => 'primary_address',
      'comment' => 'Postal code for primary address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'primary_address_country' => 
    array (
      'name' => 'primary_address_country',
      'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
      'type' => 'varchar',
      'group' => 'primary_address',
      'comment' => 'Country for primary address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'alt_address_street' => 
    array (
      'name' => 'alt_address_street',
      'vname' => 'LBL_ALT_ADDRESS_STREET',
      'type' => 'text',
      'dbType' => 'varchar',
      'len' => '150',
      'group' => 'alt_address',
      'group_label' => 'LBL_ALT_ADDRESS',
      'comment' => 'Street address for alternate address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.32,
      ),
      'audited' => true,
      'pii' => true,
      'rows' => 2,
      'cols' => 20,
    ),
    'alt_address_street_2' => 
    array (
      'name' => 'alt_address_street_2',
      'vname' => 'LBL_ALT_ADDRESS_STREET_2',
      'type' => 'varchar',
      'len' => '150',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
      'massupdate' => false,
    ),
    'alt_address_street_3' => 
    array (
      'name' => 'alt_address_street_3',
      'vname' => 'LBL_ALT_ADDRESS_STREET_3',
      'type' => 'varchar',
      'len' => '150',
      'source' => 'non-db',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
      'massupdate' => false,
    ),
    'alt_address_city' => 
    array (
      'name' => 'alt_address_city',
      'vname' => 'LBL_ALT_ADDRESS_CITY',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'alt_address',
      'comment' => 'City for alternate address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'alt_address_state' => 
    array (
      'name' => 'alt_address_state',
      'vname' => 'LBL_ALT_ADDRESS_STATE',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'alt_address',
      'comment' => 'State for alternate address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'alt_address_postalcode' => 
    array (
      'name' => 'alt_address_postalcode',
      'vname' => 'LBL_ALT_ADDRESS_POSTALCODE',
      'type' => 'varchar',
      'len' => '20',
      'group' => 'alt_address',
      'comment' => 'Postal code for alternate address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'alt_address_country' => 
    array (
      'name' => 'alt_address_country',
      'vname' => 'LBL_ALT_ADDRESS_COUNTRY',
      'type' => 'varchar',
      'group' => 'alt_address',
      'comment' => 'Country for alternate address',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'assistant' => 
    array (
      'name' => 'assistant',
      'vname' => 'LBL_ASSISTANT',
      'type' => 'varchar',
      'len' => '75',
      'unified_search' => true,
      'comment' => 'Name of the assistant of the contact',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
    ),
    'assistant_phone' => 
    array (
      'name' => 'assistant_phone',
      'vname' => 'LBL_ASSISTANT_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => 100,
      'group' => 'assistant',
      'unified_search' => true,
      'comment' => 'Phone number of the assistant of the contact',
      'merge_filter' => 'enabled',
      'duplicate_on_record_copy' => 'always',
      'audited' => true,
      'pii' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.05,
      ),
    ),
    'picture' => 
    array (
      'name' => 'picture',
      'vname' => 'LBL_PICTURE_FILE',
      'type' => 'image',
      'dbtype' => 'varchar',
      'massupdate' => false,
      'reportable' => false,
      'comment' => 'Avatar',
      'len' => '255',
      'width' => '42',
      'height' => '42',
      'border' => '',
      'duplicate_on_record_copy' => 'always',
    ),
    'email_and_name1' => 
    array (
      'name' => 'email_and_name1',
      'vname' => 'LBL_NAME',
      'type' => 'varchar',
      'source' => 'non-db',
      'len' => '510',
      'importable' => 'false',
      'massupdate' => false,
      'studio' => 
      array (
        'formula' => false,
      ),
    ),
    'lead_source' => 
    array (
      'name' => 'lead_source',
      'vname' => 'LBL_LEAD_SOURCE',
      'type' => 'enum',
      'options' => 'lead_source_dom',
      'len' => '255',
      'comment' => 'How did the contact come about',
      'merge_filter' => 'enabled',
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'rname' => 'name',
      'id_name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_NAME',
      'join_name' => 'accounts',
      'type' => 'relate',
      'link' => 'accounts',
      'table' => 'accounts',
      'isnull' => 'true',
      'module' => 'Accounts',
      'dbType' => 'varchar',
      'len' => '255',
      'source' => 'non-db',
      'unified_search' => true,
      'populate_list' => 
      array (
        'billing_address_street' => 'primary_address_street',
        'billing_address_city' => 'primary_address_city',
        'billing_address_state' => 'primary_address_state',
        'billing_address_postalcode' => 'primary_address_postalcode',
        'billing_address_country' => 'primary_address_country',
        'phone_office' => 'phone_work',
      ),
      'populate_confirm_label' => 'TPL_OVERWRITE_POPULATED_DATA_CONFIRM_WITH_MODULE_SINGULAR',
      'importable' => 'true',
      'exportable' => true,
      'export_link_type' => 'one',
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'rname' => 'id',
      'id_name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_ID',
      'type' => 'relate',
      'table' => 'accounts',
      'isnull' => 'true',
      'module' => 'Accounts',
      'dbType' => 'id',
      'reportable' => false,
      'source' => 'non-db',
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'hideacl' => true,
      'link' => 'accounts',
    ),
    'dnb_principal_id' => 
    array (
      'name' => 'dnb_principal_id',
      'vname' => 'LBL_DNB_PRINCIPAL_ID',
      'type' => 'varchar',
      'len' => 30,
      'comment' => 'Unique Id For D&B Contact',
    ),
    'opportunity_role_fields' => 
    array (
      'name' => 'opportunity_role_fields',
      'rname' => 'id',
      'relationship_fields' => 
      array (
        'id' => 'opportunity_role_id',
        'contact_role' => 'opportunity_role',
      ),
      'vname' => 'LBL_ACCOUNT_NAME',
      'type' => 'relate',
      'link' => 'opportunities',
      'link_type' => 'relationship_info',
      'join_link_name' => 'opportunities_contacts',
      'source' => 'non-db',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'opportunity_role_id' => 
    array (
      'name' => 'opportunity_role_id',
      'type' => 'varchar',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITY_ROLE_ID',
      'studio' => 
      array (
        'listview' => false,
      ),
    ),
    'opportunity_role' => 
    array (
      'name' => 'opportunity_role',
      'type' => 'enum',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITY_ROLE',
      'options' => 'opportunity_relationship_type_dom',
      'link' => 'opportunities',
      'rname_link' => 'contact_role',
      'massupdate' => false,
    ),
    'reports_to_id' => 
    array (
      'name' => 'reports_to_id',
      'vname' => 'LBL_REPORTS_TO_ID',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'comment' => 'The contact this contact reports to',
    ),
    'report_to_name' => 
    array (
      'name' => 'report_to_name',
      'rname' => 'name',
      'id_name' => 'reports_to_id',
      'vname' => 'LBL_REPORTS_TO',
      'type' => 'relate',
      'link' => 'reports_to_link',
      'table' => 'contacts',
      'isnull' => 'true',
      'module' => 'Contacts',
      'dbType' => 'varchar',
      'len' => 'id',
      'reportable' => false,
      'source' => 'non-db',
      'populate_list' => 
      array (
        'account_id' => 'account_id',
        'account_name' => 'account_name',
      ),
    ),
    'birthdate' => 
    array (
      'name' => 'birthdate',
      'vname' => 'LBL_BIRTHDATE',
      'massupdate' => false,
      'type' => 'date',
      'comment' => 'The birthdate of the contact',
      'audited' => true,
      'pii' => true,
    ),
    'portal_name' => 
    array (
      'name' => 'portal_name',
      'vname' => 'LBL_PORTAL_NAME',
      'type' => 'username',
      'dbType' => 'varchar',
      'len' => '255',
      'group' => 'portal',
      'group_label' => 'LBL_PORTAL',
      'comment' => 'Name as it appears in the portal',
      'studio' => 
      array (
        'portalrecordview' => false,
        'portallistview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.93,
        'type' => 'exact',
      ),
    ),
    'portal_active' => 
    array (
      'name' => 'portal_active',
      'vname' => 'LBL_PORTAL_ACTIVE',
      'type' => 'bool',
      'default' => '0',
      'group' => 'portal',
      'comment' => 'Indicator whether this contact is a portal user',
      'duplicate_on_record_copy' => 'no',
    ),
    'portal_password' => 
    array (
      'name' => 'portal_password',
      'vname' => 'LBL_USER_PASSWORD',
      'type' => 'password',
      'dbType' => 'varchar',
      'len' => '255',
      'group' => 'portal',
      'reportable' => false,
      'studio' => 
      array (
        'listview' => false,
        'portalrecordview' => false,
        'portallistview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
    ),
    'portal_password1' => 
    array (
      'name' => 'portal_password1',
      'vname' => 'LBL_USER_PASSWORD',
      'type' => 'password',
      'source' => 'non-db',
      'len' => '255',
      'group' => 'portal',
      'reportable' => false,
      'importable' => 'false',
      'studio' => 
      array (
        'listview' => false,
        'portalrecordview' => false,
        'portallistview' => false,
      ),
    ),
    'portal_app' => 
    array (
      'name' => 'portal_app',
      'vname' => 'LBL_PORTAL_APP',
      'type' => 'varchar',
      'group' => 'portal',
      'len' => '255',
      'comment' => 'Reference to the portal',
      'duplicate_on_record_copy' => 'no',
    ),
    'portal_user_company_name' => 
    array (
      'name' => 'portal_user_company_name',
      'vname' => 'LBL_PORTAL_USER_COMPANY_NAME',
      'type' => 'varchar',
      'len' => '255',
      'group' => 'portal',
      'comment' => 'User company name in the portal',
      'studio' => 
      array (
        'portalrecordview' => false,
        'portallistview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
    ),
    'preferred_language' => 
    array (
      'name' => 'preferred_language',
      'type' => 'enum',
      'vname' => 'LBL_PREFERRED_LANGUAGE',
      'options' => 'available_language_dom',
      'popupHelp' => 'LBL_LANG_PREF_TOOLTIP',
    ),
    'cookie_consent' => 
    array (
      'name' => 'cookie_consent',
      'vname' => 'LBL_COOKIE_CONSENT',
      'type' => 'bool',
      'default' => '0',
      'audited' => true,
      'comment' => 'Indicator whether this portal user accepts cookies',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
    ),
    'cookie_consent_received_on' => 
    array (
      'name' => 'cookie_consent_received_on',
      'vname' => 'LBL_COOKIE_CONSENT_RECEIVED_ON',
      'type' => 'datetime',
      'audited' => true,
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'comment' => 'Date cookie consent received on',
      'duplicate_on_record_copy' => 'no',
      'studio' => false,
    ),
    'business_center_name' => 
    array (
      'name' => 'business_center_name',
      'rname' => 'name',
      'id_name' => 'business_center_id',
      'vname' => 'LBL_BUSINESS_CENTER_NAME',
      'type' => 'relate',
      'link' => 'business_centers',
      'table' => 'business_centers',
      'join_name' => 'business_centers',
      'isnull' => 'true',
      'module' => 'BusinessCenters',
      'dbType' => 'varchar',
      'len' => 255,
      'source' => 'non-db',
      'unified_search' => true,
      'comment' => 'The name of the business center represented by the business_center_id field',
      'required' => false,
    ),
    'business_center_id' => 
    array (
      'name' => 'business_center_id',
      'type' => 'relate',
      'dbType' => 'id',
      'rname' => 'id',
      'module' => 'BusinessCenters',
      'id_name' => 'business_center_id',
      'reportable' => false,
      'vname' => 'LBL_BUSINESS_CENTER_ID',
      'audited' => true,
      'massupdate' => false,
      'comment' => 'The business center to which the case is associated',
    ),
    'business_centers' => 
    array (
      'name' => 'business_centers',
      'type' => 'link',
      'relationship' => 'business_center_contacts',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER',
    ),
    'purchases' => 
    array (
      'name' => 'purchases',
      'type' => 'link',
      'relationship' => 'contacts_purchases',
      'source' => 'non-db',
      'vname' => 'LBL_PURCHASES_SUBPANEL_TITLE',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'contacts_escalations',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'accounts_contacts',
      'link_type' => 'one',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNT',
      'duplicate_merge' => 'disabled',
      'primary_only' => true,
    ),
    'reports_to_link' => 
    array (
      'name' => 'reports_to_link',
      'type' => 'link',
      'relationship' => 'contact_direct_reports',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_REPORTS_TO',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'opportunities_contacts',
      'source' => 'non-db',
      'module' => 'Opportunities',
      'bean_name' => 'Opportunity',
      'vname' => 'LBL_OPPORTUNITIES',
      'populate_list' => 
      array (
        'account_id' => 'account_id',
        'account_name' => 'account_name',
      ),
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'contacts_bugs',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'calls_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'contacts_cases',
      'source' => 'non-db',
      'vname' => 'LBL_CASES',
      'populate_list' => 
      array (
        0 => 'account_id',
        1 => 'account_name',
      ),
    ),
    'case_contact' => 
    array (
      'name' => 'case_contact',
      'type' => 'link',
      'relationship' => 'contact_cases',
      'source' => 'non-db',
      'side' => 'right',
      'vname' => 'LBL_CONTACT',
      'module' => 'Cases',
      'bean_name' => 'aCase',
      'id_name' => 'primary_contact_id',
      'link_type' => 'one',
      'populate_list' => 
      array (
        0 => 'account_id',
        1 => 'account_name',
      ),
    ),
    'dataprivacy' => 
    array (
      'name' => 'dataprivacy',
      'type' => 'link',
      'relationship' => 'contacts_dataprivacy',
      'source' => 'non-db',
      'vname' => 'LBL_DATAPRIVACY',
    ),
    'dp_business_purpose' => 
    array (
      'name' => 'dp_business_purpose',
      'vname' => 'LBL_DATAPRIVACY_BUSINESS_PURPOSE',
      'type' => 'multienum',
      'isMultiSelect' => true,
      'audited' => true,
      'options' => 'dataprivacy_business_purpose_dom',
      'default' => '',
      'len' => 255,
      'comment' => 'Business purposes consented for',
    ),
    'dp_consent_last_updated' => 
    array (
      'name' => 'dp_consent_last_updated',
      'vname' => 'LBL_DATAPRIVACY_CONSENT_LAST_UPDATED',
      'type' => 'date',
      'display_default' => 'now',
      'audited' => true,
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'comment' => 'Date consent last updated',
      'studio' => false,
    ),
    'direct_reports' => 
    array (
      'name' => 'direct_reports',
      'type' => 'link',
      'relationship' => 'contact_direct_reports',
      'source' => 'non-db',
      'vname' => 'LBL_DIRECT_REPORTS',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_contacts_rel',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'archived_emails' => 
    array (
      'name' => 'archived_emails',
      'type' => 'link',
      'link_class' => 'ArchivedEmailsLink',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
      'module' => 'Emails',
      'link_type' => 'many',
      'relationship' => '',
      'readonly' => true,
    ),
    'documents' => 
    array (
      'name' => 'documents',
      'type' => 'link',
      'relationship' => 'documents_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'type' => 'link',
      'relationship' => 'contact_leads',
      'source' => 'non-db',
      'vname' => 'LBL_LEADS',
      'populate_list' => 
      array (
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'account_name' => 'account_name',
        'phone_work' => 'phone_work',
        'id' => 'contact_id',
        'account_id' => 'account_id',
      ),
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'rname' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'relationship' => 'contact_products',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCTS_TITLE',
      'populate_list' => 
      array (
        0 => 'account_id',
        1 => 'account_name',
      ),
    ),
    'contracts' => 
    array (
      'name' => 'contracts',
      'type' => 'link',
      'vname' => 'LBL_CONTRACTS',
      'relationship' => 'contracts_contacts',
      'source' => 'non-db',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'meetings_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'contact_notes',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'contact_messages',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'message_invites' => 
    array (
      'name' => 'message_invites',
      'type' => 'link',
      'relationship' => 'messages_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'project' => 
    array (
      'name' => 'project',
      'type' => 'link',
      'relationship' => 'projects_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'project_resource' => 
    array (
      'name' => 'project_resource',
      'type' => 'link',
      'relationship' => 'projects_contacts_resources',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS_RESOURCES',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quotes_contacts_shipto',
      'source' => 'non-db',
      'ignore_role' => 'true',
      'module' => 'Quotes',
      'bean_name' => 'Quote',
      'vname' => 'LBL_QUOTES_SHIP_TO',
    ),
    'billing_quotes' => 
    array (
      'name' => 'billing_quotes',
      'type' => 'link',
      'relationship' => 'quotes_contacts_billto',
      'source' => 'non-db',
      'ignore_role' => 'true',
      'module' => 'Quotes',
      'bean_name' => 'Quote',
      'vname' => 'LBL_QUOTES_BILL_TO',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'contact_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'tasks_parent' => 
    array (
      'name' => 'tasks_parent',
      'type' => 'link',
      'relationship' => 'contact_tasks_parent',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
      'reportable' => false,
    ),
    'notes_parent' => 
    array (
      'name' => 'notes_parent',
      'type' => 'link',
      'relationship' => 'contact_notes_parent',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
      'reportable' => false,
    ),
    'calls_parent' => 
    array (
      'name' => 'calls_parent',
      'type' => 'link',
      'relationship' => 'contact_calls_parent',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
      'reportable' => false,
    ),
    'meetings_parent' => 
    array (
      'name' => 'meetings_parent',
      'type' => 'link',
      'relationship' => 'contact_meetings_parent',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
      'reportable' => false,
    ),
    'all_tasks' => 
    array (
      'name' => 'all_tasks',
      'type' => 'link',
      'link_class' => 'FlexRelateChildrenLink',
      'relationship' => 'contact_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'user_sync' => 
    array (
      'name' => 'user_sync',
      'type' => 'link',
      'relationship' => 'contacts_users',
      'source' => 'non-db',
      'vname' => 'LBL_USER_SYNC',
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'contacts_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'duplicate_merge' => 'enabled',
      'id_name' => 'assigned_user_id',
      'table' => 'users',
      'side' => 'right',
    ),
    'campaign_id' => 
    array (
      'name' => 'campaign_id',
      'comment' => 'Campaign that generated lead',
      'vname' => 'LBL_CAMPAIGN_ID',
      'rname' => 'id',
      'id_name' => 'campaign_id',
      'type' => 'id',
      'isnull' => 'true',
      'module' => 'Campaigns',
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
    ),
    'campaign_name' => 
    array (
      'name' => 'campaign_name',
      'rname' => 'name',
      'vname' => 'LBL_CAMPAIGN',
      'type' => 'relate',
      'link' => 'campaign_contacts',
      'isnull' => 'true',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'campaigns',
      'id_name' => 'campaign_id',
      'module' => 'Campaigns',
      'duplicate_merge' => 'disabled',
      'comment' => 'The first campaign name for Contact (Meta-data only)',
      'studio' => 
      array (
        'mobile' => false,
      ),
    ),
    'campaigns' => 
    array (
      'name' => 'campaigns',
      'type' => 'link',
      'relationship' => 'contact_campaign_log',
      'module' => 'CampaignLog',
      'bean_name' => 'CampaignLog',
      'source' => 'non-db',
      'vname' => 'LBL_CAMPAIGNLOG',
    ),
    'campaign_contacts' => 
    array (
      'name' => 'campaign_contacts',
      'type' => 'link',
      'vname' => 'LBL_CAMPAIGN_CONTACT',
      'relationship' => 'campaign_contacts',
      'source' => 'non-db',
    ),
    'c_accept_status_fields' => 
    array (
      'name' => 'c_accept_status_fields',
      'rname' => 'id',
      'relationship_fields' => 
      array (
        'id' => 'accept_status_id',
        'accept_status' => 'accept_status_name',
      ),
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'type' => 'relate',
      'link' => 'calls',
      'link_type' => 'relationship_info',
      'source' => 'non-db',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'm_accept_status_fields' => 
    array (
      'name' => 'm_accept_status_fields',
      'rname' => 'id',
      'relationship_fields' => 
      array (
        'id' => 'accept_status_id',
        'accept_status' => 'accept_status_name',
      ),
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'type' => 'relate',
      'link' => 'meetings',
      'link_type' => 'relationship_info',
      'source' => 'non-db',
      'importable' => 'false',
      'hideacl' => true,
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'accept_status_id' => 
    array (
      'name' => 'accept_status_id',
      'type' => 'varchar',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'studio' => 
      array (
        'listview' => false,
      ),
    ),
    'accept_status_name' => 
    array (
      'massupdate' => false,
      'name' => 'accept_status_name',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'importable' => 'false',
    ),
    'accept_status_calls' => 
    array (
      'massupdate' => false,
      'name' => 'accept_status_calls',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'importable' => 'false',
      'link' => 'calls',
      'rname_link' => 'accept_status',
    ),
    'accept_status_meetings' => 
    array (
      'massupdate' => false,
      'name' => 'accept_status_meetings',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'importable' => 'false',
      'link' => 'meetings',
      'rname_link' => 'accept_status',
    ),
    'accept_status_messages' => 
    array (
      'massupdate' => false,
      'name' => 'accept_status_messages',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'importable' => 'false',
      'link' => 'meetings',
      'rname_link' => 'accept_status',
    ),
    'hint_account_size' => 
    array (
      'studio' => false,
      'name' => 'hint_account_size',
      'vname' => 'LBL_HINT_COMPANY_SIZE',
      'label' => 'LBL_HINT_COMPANY_SIZE',
      'type' => 'varchar',
      'len' => 20,
      'comment' => 'Company Size',
      'reportable' => false,
    ),
    'hint_account_industry' => 
    array (
      'studio' => false,
      'name' => 'hint_account_industry',
      'vname' => 'LBL_HINT_COMPANY_INDUSTRY',
      'label' => 'LBL_HINT_COMPANY_INDUSTRY',
      'type' => 'varchar',
      'len' => 120,
      'comment' => 'Company Industry',
      'reportable' => false,
    ),
    'hint_account_location' => 
    array (
      'studio' => false,
      'name' => 'hint_account_location',
      'vname' => 'LBL_HINT_COMPANY_LOCATION',
      'label' => 'LBL_HINT_COMPANY_LOCATION',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Company Location',
      'reportable' => false,
    ),
    'hint_account_description' => 
    array (
      'studio' => false,
      'name' => 'hint_account_description',
      'vname' => 'LBL_HINT_COMPANY_DESCRIPTION',
      'label' => 'LBL_HINT_COMPANY_DESCRIPTION',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Company Description',
      'reportable' => false,
    ),
    'hint_job_2' => 
    array (
      'studio' => false,
      'name' => 'hint_job_2',
      'vname' => 'LBL_HINT_JOB_2',
      'label' => 'LBL_HINT_JOB_2',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Job 2',
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_education' => 
    array (
      'studio' => false,
      'name' => 'hint_education',
      'vname' => 'LBL_HINT_EDUCATION',
      'label' => 'LBL_HINT_EDUCATION',
      'type' => 'varchar',
      'len' => 225,
      'comment' => 'Education',
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_education_2' => 
    array (
      'studio' => false,
      'name' => 'hint_education_2',
      'vname' => 'LBL_HINT_EDUCATION_2',
      'label' => 'LBL_HINT_EDUCATION_2',
      'type' => 'varchar',
      'len' => 225,
      'comment' => 'Education 2',
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_facebook' => 
    array (
      'studio' => false,
      'name' => 'hint_facebook',
      'vname' => 'LBL_HINT_FACEBOOK',
      'label' => 'LBL_HINT_FACEBOOK',
      'type' => 'url',
      'len' => 120,
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_twitter' => 
    array (
      'studio' => false,
      'name' => 'hint_twitter',
      'vname' => 'LBL_HINT_TWITTER',
      'label' => 'LBL_HINT_TWITTER',
      'type' => 'url',
      'len' => 120,
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_industry_tags' => 
    array (
      'studio' => false,
      'name' => 'hint_industry_tags',
      'vname' => 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
      'label' => 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
      'type' => 'varchar',
      'len' => 225,
      'comment' => 'industry tags',
      'reportable' => false,
    ),
    'hint_account_founded_year' => 
    array (
      'studio' => false,
      'name' => 'hint_account_founded_year',
      'vname' => 'LBL_HINT_COMPANY_FOUNDED_YEAR',
      'label' => 'LBL_HINT_COMPANY_FOUNDED_YEAR',
      'type' => 'varchar',
      'len' => 5,
      'comment' => 'company founded year',
      'reportable' => false,
    ),
    'hint_account_facebook_handle' => 
    array (
      'studio' => false,
      'name' => 'hint_account_facebook_handle',
      'vname' => 'LBL_HINT_COMPANY_FACEBOOK',
      'label' => 'LBL_HINT_COMPANY_FACEBOOK',
      'type' => 'url',
      'len' => 120,
      'comment' => 'company facebook',
      'reportable' => false,
    ),
    'hint_account_twitter_handle' => 
    array (
      'studio' => false,
      'name' => 'hint_account_twitter_handle',
      'vname' => 'LBL_HINT_COMPANY_TWITTER',
      'label' => 'LBL_HINT_COMPANY_TWITTER',
      'type' => 'url',
      'len' => 120,
      'comment' => 'company twitter',
      'reportable' => false,
    ),
    'hint_account_logo' => 
    array (
      'name' => 'hint_account_logo',
      'vname' => 'LBL_HINT_COMPANY_LOGO',
      'label' => 'LBL_HINT_COMPANY_LOGO',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'company logo',
      'studio' => false,
      'reportable' => false,
    ),
    'hint_contact_pic' => 
    array (
      'name' => 'hint_contact_pic',
      'vname' => 'LBL_HINT_CONTACT_PIC',
      'label' => 'LBL_HINT_CONTACT_PIC',
      'type' => 'text',
      'comment' => 'Hint Contact logo',
      'studio' => false,
      'reportable' => false,
    ),
    'hint_photo' => 
    array (
      'name' => 'hint_photo',
      'vname' => 'LBL_HINT_PHOTO',
      'label' => 'LBL_HINT_PHOTO',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'person photo',
      'studio' => false,
      'reportable' => false,
    ),
    'hint_phone_1' => 
    array (
      'studio' => false,
      'name' => 'hint_phone_1',
      'vname' => 'LBL_HINT_PHONE_1',
      'label' => 'LBL_HINT_PHONE_1',
      'type' => 'varchar',
      'len' => 15,
      'comment' => 'extra phone field',
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_phone_2' => 
    array (
      'studio' => false,
      'name' => 'hint_phone_2',
      'vname' => 'LBL_HINT_PHONE_2',
      'label' => 'LBL_HINT_PHONE_2',
      'type' => 'varchar',
      'len' => 15,
      'comment' => 'extra phone field',
      'pii' => true,
      'audited' => true,
      'reportable' => false,
    ),
    'hint_account_website' => 
    array (
      'studio' => false,
      'name' => 'hint_account_website',
      'vname' => 'LBL_HINT_COMPANY_WEBSITE',
      'label' => 'LBL_HINT_COMPANY_WEBSITE',
      'type' => 'url',
      'len' => 255,
      'comment' => 'company website',
      'reportable' => false,
    ),
    'hint_account_naics_code_lbl' => 
    array (
      'studio' => false,
      'name' => 'hint_account_naics_code_lbl',
      'vname' => 'LBL_HINT_COMPANY_NAICS_CODE_LABEL',
      'label' => 'LBL_HINT_COMPANY_NAICS_CODE_LABEL',
      'type' => 'varchar',
      'len' => 170,
      'comment' => 'NAICS Code',
      'reportable' => false,
    ),
    'hint_account_sic_code_label' => 
    array (
      'studio' => false,
      'name' => 'hint_account_sic_code_label',
      'vname' => 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
      'label' => 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
      'type' => 'varchar',
      'len' => 120,
      'comment' => 'SIC Code',
      'reportable' => false,
    ),
    'hint_account_fiscal_year_end' => 
    array (
      'studio' => false,
      'name' => 'hint_account_fiscal_year_end',
      'vname' => 'LBL_HINT_COMPANY_FISCAL_YEAR_END',
      'label' => 'LBL_HINT_COMPANY_FISCAL_YEAR_END',
      'type' => 'varchar',
      'len' => 5,
      'comment' => 'FY End',
      'reportable' => false,
    ),
    'hint_account_annual_revenue' => 
    array (
      'studio' => false,
      'name' => 'hint_account_annual_revenue',
      'vname' => 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
      'label' => 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
      'type' => 'varchar',
      'len' => 25,
      'comment' => 'Annual Rev',
      'reportable' => false,
    ),
    'geocode_status' => 
    array (
      'studio' => false,
      'name' => 'geocode_status',
      'vname' => 'LBL_MAPS_GEOCODE_STATUS',
      'label' => 'LBL_MAPS_GEOCODE_STATUS',
      'type' => 'geocodestatus',
      'len' => 255,
      'comment' => '',
      'dbType' => 'varchar',
      'licenseFilter' => 
      array (
        0 => 'MAPS',
      ),
      'readonly' => true,
      'reportable' => false,
    ),
    'prospect_lists' => 
    array (
      'name' => 'prospect_lists',
      'type' => 'link',
      'relationship' => 'prospect_list_contacts',
      'module' => 'ProspectLists',
      'source' => 'non-db',
      'vname' => 'LBL_PROSPECT_LIST',
    ),
    'sync_contact' => 
    array (
      'massupdate' => false,
      'name' => 'sync_contact',
      'vname' => 'LBL_SYNC_CONTACT',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Synch to outlook?  (Meta-Data only)',
      'studio' => 'true',
      'link' => 'user_sync',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'market_interest_prediction_score' => 
    array (
      'name' => 'market_interest_prediction_score',
      'vname' => 'LBL_MARKET_INTEREST_PREDICTION',
      'type' => 'enum',
      'help' => 'Sugar Predict score',
      'options' => 'market_interest_prediction_dom',
      'default' => '',
      'readonly' => true,
      'massupdate' => false,
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => true,
      'duplicate_merge' => true,
    ),
    'market_score' => 
    array (
      'name' => 'market_score',
      'vname' => 'LBL_MARKET_SCORE',
      'type' => 'int',
      'help' => 'Score based on Market profile',
      'readonly' => true,
      'massupdate' => false,
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => true,
      'duplicate_merge' => true,
      'enable_range_search' => true,
    ),
    'mkto_sync' => 
    array (
      'name' => 'mkto_sync',
      'vname' => 'LBL_MKTO_SYNC',
      'type' => 'bool',
      'default' => '0',
      'comment' => 'Should the Lead be synced to Marketo',
      'massupdate' => true,
      'audited' => true,
      'duplicate_merge' => true,
      'reportable' => true,
      'importable' => 'true',
    ),
    'mkto_id' => 
    array (
      'name' => 'mkto_id',
      'vname' => 'LBL_MKTO_ID',
      'comment' => 'Associated Marketo Lead ID',
      'type' => 'int',
      'default' => NULL,
      'audited' => true,
      'mass_update' => false,
      'duplicate_merge' => true,
      'reportable' => true,
      'importable' => 'false',
    ),
    'mkto_lead_score' => 
    array (
      'name' => 'mkto_lead_score',
      'vname' => 'LBL_MKTO_LEAD_SCORE',
      'comment' => NULL,
      'type' => 'int',
      'default_value' => NULL,
      'audited' => true,
      'mass_update' => false,
      'duplicate_merge' => true,
      'reportable' => true,
      'importable' => 'true',
    ),
    'entry_source' => 
    array (
      'name' => 'entry_source',
      'vname' => 'LBL_ENTRY_SOURCE',
      'type' => 'enum',
      'function' => 'getSourceTypes',
      'function_bean' => 'Contacts',
      'len' => '255',
      'default' => 'internal',
      'comment' => 'Determines if a record was created internal to the system or external to the system',
      'readonly' => true,
      'studio' => false,
      'processes' => true,
      'reportable' => true,
    ),
    'site_user_id' => 
    array (
      'name' => 'site_user_id',
      'vname' => 'LBL_SITE_USER_ID',
      'type' => 'varchar',
      'len' => '64',
      'reportable' => false,
      'importable' => false,
      'studio' => false,
      'readonly' => true,
    ),
    'following' => 
    array (
      'massupdate' => false,
      'name' => 'following',
      'vname' => 'LBL_FOLLOWING',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Is user following this record',
      'studio' => 'false',
      'link' => 'following_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'following_link' => 
    array (
      'name' => 'following_link',
      'type' => 'link',
      'relationship' => 'contacts_following',
      'source' => 'non-db',
      'vname' => 'LBL_FOLLOWING',
      'reportable' => false,
    ),
    'my_favorite' => 
    array (
      'massupdate' => false,
      'name' => 'my_favorite',
      'vname' => 'LBL_FAVORITE',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Favorite for the user',
      'studio' => 
      array (
        'list' => false,
        'recordview' => false,
        'basic_search' => false,
        'advanced_search' => false,
      ),
      'link' => 'favorite_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'favorite_link' => 
    array (
      'name' => 'favorite_link',
      'type' => 'link',
      'relationship' => 'contacts_favorite',
      'source' => 'non-db',
      'vname' => 'LBL_FAVORITE',
      'reportable' => false,
      'workflow' => false,
      'full_text_search' => 
      array (
        'type' => 'favorites',
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'favorite_link' => 
          array (
            'type' => 'MyItems',
            'options' => 
            array (
              'field' => 'user_favorites',
            ),
          ),
        ),
      ),
    ),
    'tag' => 
    array (
      'name' => 'tag',
      'vname' => 'LBL_TAGS',
      'type' => 'tag',
      'link' => 'tag_link',
      'source' => 'non-db',
      'module' => 'Tags',
      'relate_collection' => true,
      'studio' => 
      array (
        'portal' => false,
        'base' => 
        array (
          'popuplist' => false,
          'popupsearch' => false,
        ),
        'mobile' => 
        array (
          'wirelesseditview' => true,
          'wirelessdetailview' => true,
        ),
      ),
      'massupdate' => true,
      'exportable' => true,
      'sortable' => false,
      'rname' => 'name',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'tag_link' => 
    array (
      'name' => 'tag_link',
      'type' => 'link',
      'vname' => 'LBL_TAGS_LINK',
      'relationship' => 'contacts_tags',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'commentlog' => 
    array (
      'name' => 'commentlog',
      'vname' => 'LBL_COMMENTLOG',
      'type' => 'collection',
      'displayParams' => 
      array (
        'type' => 'commentlog',
        'fields' => 
        array (
          0 => 'entry',
          1 => 'date_entered',
          2 => 'created_by_name',
        ),
        'max_num' => 100,
      ),
      'links' => 
      array (
        0 => 'commentlog_link',
      ),
      'order_by' => 'date_entered:asc',
      'source' => 'non-db',
      'module' => 'CommentLog',
      'studio' => 
      array (
        'listview' => false,
        'recordview' => true,
        'previewview' => true,
        'wirelesseditview' => false,
        'wirelessdetailview' => true,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
      ),
    ),
    'commentlog_link' => 
    array (
      'name' => 'commentlog_link',
      'type' => 'link',
      'vname' => 'LBL_COMMENTLOG_LINK',
      'relationship' => 'contacts_commentlog',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'locked_fields' => 
    array (
      'name' => 'locked_fields',
      'vname' => 'LBL_LOCKED_FIELDS',
      'type' => 'locked_fields',
      'link' => 'locked_fields_link',
      'source' => 'non-db',
      'module' => 'pmse_BpmProcessDefinition',
      'relate_collection' => true,
      'studio' => false,
      'massupdate' => false,
      'exportable' => false,
      'sortable' => false,
      'rname' => 'pro_locked_variables',
      'collection_fields' => 
      array (
        0 => 'pro_locked_variables',
      ),
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
      ),
      'hideacl' => true,
    ),
    'locked_fields_link' => 
    array (
      'name' => 'locked_fields_link',
      'type' => 'link',
      'vname' => 'LBL_LOCKED_FIELDS_LINK',
      'relationship' => 'contacts_locked_fields',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'sync_key' => 
    array (
      'is_sync_key' => true,
      'name' => 'sync_key',
      'vname' => 'LBL_SYNC_KEY',
      'type' => 'varchar',
      'enforced' => '',
      'required' => false,
      'massupdate' => false,
      'readonly' => true,
      'default' => NULL,
      'isnull' => true,
      'no_default' => false,
      'comments' => 'External default id of the remote integration record',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'merge_filter' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
      'reportable' => true,
      'unified_search' => false,
      'calculated' => false,
      'len' => '100',
      'size' => '20',
      'studio' => 
      array (
        'recordview' => true,
        'wirelessdetailview' => true,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
        'portallistview' => false,
        'portalrecordview' => false,
        'portaleditview' => false,
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO_ID',
      'group' => 'assigned_user_name',
      'type' => 'id',
      'reportable' => false,
      'isnull' => 'false',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'User ID assigned to record',
      'duplicate_merge' => 'disabled',
      'mandatory_fetch' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'assigned_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_ASSIGNED_TO_ME',
          ),
        ),
      ),
    ),
    'assigned_user_name' => 
    array (
      'name' => 'assigned_user_name',
      'link' => 'assigned_user_link',
      'vname' => 'LBL_ASSIGNED_TO',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'assigned_user_id',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
      'related_fields' => 
      array (
        0 => 'assigned_user_id',
      ),
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'vname' => 'LBL_TEAM_ID',
      'group' => 'team_name',
      'reportable' => false,
      'dbType' => 'id',
      'type' => 'team_list',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'Team ID for the account',
    ),
    'team_set_id' => 
    array (
      'name' => 'team_set_id',
      'rname' => 'id',
      'id_name' => 'team_set_id',
      'vname' => 'LBL_TEAM_SET_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => 'false',
      'dbType' => 'id',
      'duplicate_on_record_copy' => 'always',
    ),
    'acl_team_set_id' => 
    array (
      'name' => 'acl_team_set_id',
      'vname' => 'LBL_TEAM_SET_SELECTED_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => false,
      'isnull' => true,
      'duplicate_on_record_copy' => 'always',
      'reportable' => false,
    ),
    'team_count' => 
    array (
      'name' => 'team_count',
      'rname' => 'team_count',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'join_name' => 'ts1',
      'table' => 'teams',
      'type' => 'relate',
      'required' => 'true',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_count_link',
      'massupdate' => false,
      'dbType' => 'int',
      'source' => 'non-db',
      'importable' => 'false',
      'reportable' => false,
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'studio' => 'false',
      'hideacl' => true,
    ),
    'team_name' => 
    array (
      'name' => 'team_name',
      'db_concat_fields' => 
      array (
        0 => 'name',
        1 => 'name_2',
      ),
      'sort_on' => 'tj.name',
      'join_name' => 'tj',
      'rname' => 'name',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'type' => 'relate',
      'required' => 'true',
      'table' => 'teams',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_link',
      'massupdate' => true,
      'dbType' => 'varchar',
      'source' => 'non-db',
      'custom_type' => 'teamset',
      'studio' => 
      array (
        'portallistview' => false,
        'portalrecordview' => false,
      ),
      'duplicate_on_record_copy' => 'always',
      'exportable' => true,
      'fields' => 
      array (
        0 => 'acl_team_set_id',
      ),
    ),
    'acl_team_names' => 
    array (
      'name' => 'acl_team_names',
      'table' => 'teams',
      'module' => 'Teams',
      'vname' => 'LBL_TEAM_SET_SELECTED_TEAMS',
      'rname' => 'name',
      'id_name' => 'acl_team_set_id',
      'source' => 'non-db',
      'link' => 'team_link',
      'type' => 'relate',
      'custom_type' => 'teamset',
      'exportable' => true,
      'studio' => false,
      'massupdate' => false,
      'hideacl' => true,
    ),
    'team_link' => 
    array (
      'name' => 'team_link',
      'type' => 'link',
      'relationship' => 'contacts_team',
      'vname' => 'LBL_TEAMS_LINK',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'Team',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'studio' => 'false',
      'side' => 'right',
    ),
    'team_count_link' => 
    array (
      'name' => 'team_count_link',
      'type' => 'link',
      'relationship' => 'contacts_team_count_relationship',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'TeamSet',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'reportable' => false,
      'studio' => 'false',
      'side' => 'right',
    ),
    'teams' => 
    array (
      'name' => 'teams',
      'type' => 'link',
      'relationship' => 'contacts_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'email' => 
    array (
      'name' => 'email',
      'type' => 'email',
      'query_type' => 'default',
      'source' => 'non-db',
      'operator' => 'subquery',
      'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
      'db_field' => 
      array (
        0 => 'id',
      ),
      'vname' => 'LBL_EMAIL_ADDRESS',
      'studio' => 
      array (
        'visible' => true,
        'searchview' => true,
        'editview' => true,
        'editField' => true,
      ),
      'duplicate_on_record_copy' => 'always',
      'len' => 100,
      'link' => 'email_addresses_primary',
      'rname' => 'email_address',
      'module' => 'EmailAddresses',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.95,
      ),
      'audited' => true,
      'pii' => true,
    ),
    'email1' => 
    array (
      'name' => 'email1',
      'vname' => 'LBL_EMAIL_ADDRESS',
      'type' => 'varchar',
      'function' => 
      array (
        'name' => 'getEmailAddressWidget',
        'returns' => 'html',
      ),
      'source' => 'non-db',
      'link' => 'email_addresses_primary',
      'rname' => 'email_address',
      'group' => 'email1',
      'merge_filter' => 'enabled',
      'module' => 'EmailAddresses',
      'studio' => false,
      'duplicate_on_record_copy' => 'always',
      'importable' => false,
    ),
    'email2' => 
    array (
      'name' => 'email2',
      'vname' => 'LBL_OTHER_EMAIL_ADDRESS',
      'type' => 'varchar',
      'function' => 
      array (
        'name' => 'getEmailAddressWidget',
        'returns' => 'html',
      ),
      'source' => 'non-db',
      'group' => 'email2',
      'merge_filter' => 'enabled',
      'studio' => 'false',
      'duplicate_on_record_copy' => 'always',
      'importable' => false,
      'workflow' => false,
    ),
    'invalid_email' => 
    array (
      'name' => 'invalid_email',
      'vname' => 'LBL_INVALID_EMAIL',
      'source' => 'non-db',
      'type' => 'bool',
      'link' => 'email_addresses_primary',
      'rname' => 'invalid_email',
      'massupdate' => false,
      'studio' => 'false',
      'duplicate_on_record_copy' => 'always',
    ),
    'email_opt_out' => 
    array (
      'name' => 'email_opt_out',
      'vname' => 'LBL_EMAIL_OPT_OUT',
      'source' => 'non-db',
      'type' => 'bool',
      'link' => 'email_addresses_primary',
      'rname' => 'opt_out',
      'massupdate' => false,
      'studio' => 'false',
      'duplicate_on_record_copy' => 'always',
    ),
    'email_addresses_primary' => 
    array (
      'name' => 'email_addresses_primary',
      'type' => 'link',
      'relationship' => 'contacts_email_addresses_primary',
      'source' => 'non-db',
      'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
      'duplicate_merge' => 'disabled',
      'primary_only' => true,
    ),
    'email_addresses' => 
    array (
      'name' => 'email_addresses',
      'type' => 'link',
      'relationship' => 'contacts_email_addresses',
      'source' => 'non-db',
      'vname' => 'LBL_EMAIL_ADDRESSES',
      'reportable' => false,
      'unified_search' => true,
      'rel_fields' => 
      array (
        'primary_address' => 
        array (
          'type' => 'bool',
        ),
      ),
    ),
    'email_addresses_non_primary' => 
    array (
      'name' => 'email_addresses_non_primary',
      'type' => 'varchar',
      'source' => 'non-db',
      'vname' => 'LBL_EMAIL_NON_PRIMARY',
      'studio' => false,
      'reportable' => false,
      'massupdate' => false,
    ),
    'external_user_id' => 
    array (
      'name' => 'external_user_id',
      'vname' => 'LBL_EXTERNAL_USER_ID',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'comment' => 'The external user this person is associated with',
    ),
    'source_id' => 
    array (
      'name' => 'source_id',
      'vname' => 'LBL_SOURCE_ID',
      'type' => 'varchar',
      'comment' => '',
    ),
    'source_type' => 
    array (
      'name' => 'source_type',
      'vname' => 'LBL_SOURCE_TYPE',
      'type' => 'varchar',
      'comment' => '',
    ),
    'source_meta' => 
    array (
      'name' => 'source_meta',
      'vname' => 'LBL_SOURCE_META',
      'type' => 'text',
      'comment' => '',
    ),
    'dri_workflows' => 
    array (
      'name' => 'dri_workflows',
      'vname' => 'LBL_DRI_WORKFLOWS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'dri_workflow_contacts',
      'module' => 'DRI_Workflows',
    ),
    'dri_workflow_template_id' => 
    array (
      'name' => 'dri_workflow_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => true,
      'type' => 'enum',
      'placeholder' => 'LBL_SELECT_SMART_GUIDE_TEMPLATE_PLACEHOLDER',
      'options' => NULL,
      'dbType' => 'id',
      'processes' => true,
      'studio' => false,
      'function' => 
      array (
        'name' => 'listSmartGuideTemplatesByModule',
        'params' => 
        array (
          0 => 'Contacts',
        ),
      ),
    ),
    'dri_workflow_template_name' => 
    array (
      'name' => 'dri_workflow_template_name',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'studio' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'dri_workflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'link' => 'dri_workflow_template_link',
    ),
    'dri_workflow_template_link' => 
    array (
      'name' => 'dri_workflow_template_link',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'reportable' => false,
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'contact_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'perform_sugar_action' => 
    array (
      'name' => 'perform_sugar_action',
      'vname' => 'LBL_PERFORM_SUGAR_ACTION',
      'type' => 'bool',
      'studio' => false,
      'massupdate' => false,
      'reportable' => false,
      'default' => 0,
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_contacts_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_contacts_del_d_m',
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
      'name' => 'idx_contacts_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_contacts_del_d_e',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_entered',
        2 => 'id',
      ),
    ),
    'name_del' => 
    array (
      'name' => 'idx_contacts_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_contacts_last_first',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'last_name',
        1 => 'first_name',
        2 => 'deleted',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_contacts_first_last',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
        2 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_contacts_del_last',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'last_name',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_cont_del_last_dm',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'last_name',
        2 => 'date_modified',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_cont_del_reports',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'reports_to_id',
        2 => 'last_name',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_reports_to_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'reports_to_id',
        2 => 'id',
      ),
    ),
    6 => 
    array (
      'name' => 'idx_del_id_user',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'id',
        2 => 'assigned_user_id',
      ),
    ),
    7 => 
    array (
      'name' => 'idx_contact_title',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'title',
      ),
    ),
    8 => 
    array (
      'name' => 'idx_contact_mkto_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'mkto_id',
      ),
    ),
    9 => 
    array (
      'name' => 'idx_contacts_del_businesscenter',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'business_center_id',
      ),
    ),
    10 => 
    array (
      'name' => 'idx_cont_portal_active',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'portal_name',
        1 => 'portal_active',
        2 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_contacts_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_contacts_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_contacts' => 
    array (
      'name' => 'idx_contacts_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_contacts' => 
    array (
      'name' => 'idx_contacts_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'external_user_id' => 
    array (
      'name' => 'idx_contacts_external_user_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'external_user_id',
      ),
    ),
    'idx_contacts_cjtpl_id' => 
    array (
      'name' => 'idx_contacts_cjtpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
  ),
  'relationships' => 
  array (
    'contacts_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'contacts_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'contact_activities' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'id',
      'rhs_vname' => 'LBL_ACTIVITY_STREAM',
      'relationship_type' => 'many-to-many',
      'join_table' => 'activities_users',
      'join_key_lhs' => 'parent_id',
      'join_key_rhs' => 'activity_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contact_direct_reports' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'reports_to_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_leads' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Leads',
      'rhs_table' => 'leads',
      'rhs_key' => 'contact_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_notes' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'contact_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'ContactNotesRelationship',
      'relationship_file' => 'data/Relationships/ContactNotesRelationship.php',
    ),
    'contact_messages' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'contact_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_notes_parent' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contact_calls_parent' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contact_meetings_parent' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contact_tasks' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'contact_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_tasks_parent' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contacts_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_products' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'contact_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_campaign_log' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'CampaignLog',
      'rhs_table' => 'campaign_log',
      'rhs_key' => 'target_id',
      'relationship_type' => 'one-to-many',
    ),
    'contacts_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Contacts',
      'user_field' => 'created_by',
    ),
    'contacts_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Contacts',
      'user_field' => 'created_by',
    ),
    'contacts_tags' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Contacts',
      'dynamic_subpanel' => true,
    ),
    'contacts_commentlog' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contacts_locked_fields' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contacts_audit' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'contacts_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'contacts_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'contacts_teams' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'contacts_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'contacts_email_addresses' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailAddresses',
      'rhs_table' => 'email_addresses',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'email_addr_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'email_address_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Contacts',
    ),
    'contacts_email_addresses_primary' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailAddresses',
      'rhs_table' => 'email_addresses',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'email_addr_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'email_address_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Contacts',
      'primary_flag_column' => 'primary_address',
    ),
    'contact_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'dri_workflow_template_id',
    ),
  ),
  'duplicate_check' => 
  array (
    'enabled' => true,
    'FilterDuplicateCheck' => 
    array (
      'filter_template' => 
      array (
        0 => 
        array (
          '$and' => 
          array (
            0 => 
            array (
              'first_name' => 
              array (
                '$starts' => '$first_name',
              ),
            ),
            1 => 
            array (
              'last_name' => 
              array (
                '$starts' => '$last_name',
              ),
            ),
            2 => 
            array (
              'accounts.id' => 
              array (
                '$equals' => '$account_id',
              ),
            ),
            3 => 
            array (
              'dnb_principal_id' => 
              array (
                '$equals' => '$dnb_principal_id',
              ),
            ),
          ),
        ),
      ),
      'ranking_fields' => 
      array (
        0 => 
        array (
          'in_field_name' => 'account_id',
          'dupe_field_name' => 'account_id',
        ),
        1 => 
        array (
          'in_field_name' => 'last_name',
          'dupe_field_name' => 'last_name',
        ),
        2 => 
        array (
          'in_field_name' => 'first_name',
          'dupe_field_name' => 'first_name',
        ),
      ),
    ),
  ),
  'optimistic_locking' => true,
  'portal_visibility' => 
  array (
    'class' => 'Contacts',
    'links' => 
    array (
      'Accounts' => 'accounts',
    ),
  ),
  'name_format_map' => 
  array (
    'f' => 'first_name',
    'l' => 'last_name',
    's' => 'salutation',
    't' => 'title',
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
  ),
  'acls' => 
  array (
    'SugarACLLockedFields' => true,
    'SugarACLEmailAddress' => true,
    'SugarACLStatic' => true,
  ),
  'templates' => 
  array (
    'person' => 'person',
    'basic' => 'basic',
    'following' => 'following',
    'favorite' => 'favorite',
    'taggable' => 'taggable',
    'commentlog' => 'commentlog',
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
    'assignable' => 'assignable',
    'team_security' => 'team_security',
    'email_address' => 'email_address',
    'external_user' => 'external_user',
    'external_source' => 'external_source',
    'customer_journey_parent' => 'customer_journey_parent',
  ),
  'favorites' => true,
  'custom_fields' => false,
  'has_pii_fields' => true,
  'related_calc_fields' => 
  array (
  ),
);