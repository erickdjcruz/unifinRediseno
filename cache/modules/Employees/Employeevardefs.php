<?php 
 $GLOBALS["dictionary"]["Employee"]=array (
  'table' => 'users',
  'color' => 'teal',
  'icon' => 'sicon-user-group-lg',
  'archive' => false,
  'favorites' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
    ),
    'user_name' => 
    array (
      'name' => 'user_name',
      'vname' => 'LBL_USER_NAME',
      'type' => 'username',
      'dbType' => 'varchar',
      'len' => '60',
      'importable' => 'required',
      'required' => true,
      'studio' => 
      array (
        'no_duplicate' => true,
        'editview' => false,
        'detailview' => true,
        'quickcreate' => false,
        'basic_search' => false,
        'advanced_search' => false,
        'wirelesseditview' => false,
        'wirelessdetailview' => true,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
        'rollup' => false,
      ),
      'idm_mode_disabled' => true,
    ),
    'user_hash' => 
    array (
      'name' => 'user_hash',
      'vname' => 'LBL_USER_HASH',
      'type' => 'password',
      'dbType' => 'varchar',
      'len' => '255',
      'reportable' => false,
      'importable' => 'false',
      'sensitive' => true,
      'studio' => 
      array (
        'no_duplicate' => true,
        'listview' => false,
        'searchview' => false,
        'related' => false,
        'formula' => false,
        'rollup' => false,
        'recordview' => false,
        'recorddashletview' => false,
        'previewview' => false,
        'wirelessdetailview' => false,
        'wirelesseditview' => false,
      ),
      'idm_mode_disabled' => true,
      'massupdate' => false,
    ),
    'system_generated_password' => 
    array (
      'name' => 'system_generated_password',
      'vname' => 'LBL_SYSTEM_GENERATED_PASSWORD',
      'type' => 'bool',
      'required' => true,
      'reportable' => false,
      'massupdate' => false,
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'editview' => false,
        'quickcreate' => false,
        'wirelesseditview' => false,
        'related' => false,
        'formula' => false,
        'rollup' => false,
      ),
      'idm_mode_disabled' => true,
    ),
    'pwd_last_changed' => 
    array (
      'name' => 'pwd_last_changed',
      'vname' => 'LBL_PSW_MODIFIED',
      'type' => 'datetime',
      'required' => false,
      'massupdate' => false,
      'studio' => 
      array (
        'formula' => false,
      ),
      'idm_mode_disabled' => true,
    ),
    'authenticate_id' => 
    array (
      'name' => 'authenticate_id',
      'vname' => 'LBL_AUTHENTICATE_ID',
      'type' => 'varchar',
      'len' => '100',
      'reportable' => false,
      'importable' => 'false',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'related' => false,
      ),
      'massupdate' => false,
    ),
    'sugar_login' => 
    array (
      'name' => 'sugar_login',
      'vname' => 'LBL_SUGAR_LOGIN',
      'type' => 'bool',
      'default' => '1',
      'reportable' => false,
      'massupdate' => false,
      'importable' => false,
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
    ),
    'picture' => 
    array (
      'name' => 'picture',
      'vname' => 'LBL_PICTURE_FILE',
      'type' => 'image',
      'dbType' => 'varchar',
      'len' => '255',
      'width' => '42',
      'height' => '42',
      'border' => '',
    ),
    'first_name' => 
    array (
      'name' => 'first_name',
      'vname' => 'LBL_FIRST_NAME',
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => '30',
      'calculation_visible' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.71,
      ),
    ),
    'last_name' => 
    array (
      'name' => 'last_name',
      'vname' => 'LBL_LAST_NAME',
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => '30',
      'importable' => 'required',
      'required' => true,
      'calculation_visible' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.69,
      ),
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
      ),
      'source' => 'non-db',
      'sort_on' => 'last_name',
      'sort_on2' => 'first_name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'len' => '510',
      'studio' => 
      array (
        'formula' => false,
      ),
      'idm_mode_disabled' => true,
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
      ),
      'source' => 'non-db',
      'sort_on' => 'last_name',
      'sort_on2' => 'first_name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'len' => '510',
      'studio' => 
      array (
        'formula' => false,
      ),
      'idm_mode_disabled' => true,
    ),
    'is_admin' => 
    array (
      'name' => 'is_admin',
      'vname' => 'LBL_IS_ADMIN',
      'type' => 'bool',
      'default' => '0',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'related' => false,
      ),
      'processes' => false,
      'idm_mode_disabled' => true,
      'massupdate' => false,
    ),
    'external_auth_only' => 
    array (
      'name' => 'external_auth_only',
      'vname' => 'LBL_EXT_AUTHENTICATE',
      'type' => 'bool',
      'reportable' => false,
      'massupdate' => false,
      'default' => '0',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'related' => false,
      ),
      'idm_mode_disabled' => true,
    ),
    'receive_notifications' => 
    array (
      'name' => 'receive_notifications',
      'vname' => 'LBL_RECEIVE_NOTIFICATIONS',
      'type' => 'bool',
      'default' => '1',
      'massupdate' => false,
      'studio' => false,
    ),
    'send_email_on_mention' => 
    array (
      'name' => 'send_email_on_mention',
      'vname' => 'LBL_SEND_EMAIL_ON_MENTION',
      'type' => 'bool',
      'default' => '0',
      'massupdate' => false,
      'studio' => false,
      'user_preference' => true,
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'calculation_visible' => true,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
      'studio' => 
      array (
        'editview' => false,
        'quickcreate' => false,
        'wirelesseditview' => false,
      ),
      'readonly' => true,
      'calculation_visible' => true,
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
      'required' => true,
      'studio' => 
      array (
        'editview' => false,
        'quickcreate' => false,
        'wirelesseditview' => false,
      ),
      'readonly' => true,
      'calculation_visible' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'last_login' => 
    array (
      'name' => 'last_login',
      'vname' => 'LBL_LAST_LOGIN',
      'type' => 'datetime',
      'required' => false,
      'readonly' => true,
      'massupdate' => false,
      'calculation_visible' => true,
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED_BY_ID',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
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
    ),
    'modified_by_name' => 
    array (
      'name' => 'modified_by_name',
      'vname' => 'LBL_MODIFIED_BY',
      'type' => 'varchar',
      'source' => 'non-db',
      'studio' => false,
      'readonly' => true,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'studio' => false,
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
    ),
    'created_by_name' => 
    array (
      'name' => 'created_by_name',
      'vname' => 'LBL_CREATED_BY_NAME',
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
      'studio' => 
      array (
        'related' => false,
        'formula' => false,
        'rollup' => false,
      ),
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'employees_created_by',
      'vname' => 'LBL_CREATED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'title' => 
    array (
      'name' => 'title',
      'vname' => 'LBL_TITLE',
      'type' => 'varchar',
      'len' => '50',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'department' => 
    array (
      'name' => 'department',
      'vname' => 'LBL_DEPARTMENT',
      'type' => 'varchar',
      'len' => '50',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'phone_home' => 
    array (
      'name' => 'phone_home',
      'vname' => 'LBL_HOME_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => '50',
      'calculation_visible' => true,
    ),
    'phone_mobile' => 
    array (
      'name' => 'phone_mobile',
      'vname' => 'LBL_MOBILE_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => '50',
      'calculation_visible' => true,
    ),
    'phone_work' => 
    array (
      'name' => 'phone_work',
      'vname' => 'LBL_WORK_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => '50',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'phone_other' => 
    array (
      'name' => 'phone_other',
      'vname' => 'LBL_OTHER_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => '50',
      'calculation_visible' => true,
    ),
    'phone_fax' => 
    array (
      'name' => 'phone_fax',
      'vname' => 'LBL_FAX_PHONE',
      'type' => 'phone',
      'dbType' => 'varchar',
      'len' => '50',
      'calculation_visible' => true,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'len' => 100,
      'options' => 'user_status_dom',
      'importable' => 'required',
      'required' => false,
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
      'massupdate' => false,
      'studio' => false,
    ),
    'address_street' => 
    array (
      'name' => 'address_street',
      'vname' => 'LBL_ADDRESS_STREET',
      'type' => 'text',
      'dbType' => 'varchar',
      'len' => '150',
      'group' => 'address',
      'group_label' => 'LBL_ADDRESS',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'address_city' => 
    array (
      'name' => 'address_city',
      'vname' => 'LBL_ADDRESS_CITY',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'address',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'address_state' => 
    array (
      'name' => 'address_state',
      'vname' => 'LBL_ADDRESS_STATE',
      'type' => 'varchar',
      'len' => '100',
      'group' => 'address',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'address_country' => 
    array (
      'name' => 'address_country',
      'vname' => 'LBL_ADDRESS_COUNTRY',
      'type' => 'varchar',
      'len' => 100,
      'group' => 'address',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'address_postalcode' => 
    array (
      'name' => 'address_postalcode',
      'vname' => 'LBL_ADDRESS_POSTALCODE',
      'type' => 'varchar',
      'len' => '20',
      'group' => 'address',
      'idm_mode_disabled' => true,
      'calculation_visible' => true,
    ),
    'UserType' => 
    array (
      'name' => 'UserType',
      'vname' => 'LBL_USER_TYPE',
      'type' => 'enum',
      'len' => 50,
      'options' => 'user_type_dom',
      'source' => 'non-db',
      'import' => false,
      'reportable' => false,
      'studio' => 
      array (
        'formula' => false,
      ),
      'massupdate' => false,
    ),
    'license_type' => 
    array (
      'name' => 'license_type',
      'vname' => 'LBL_LICENSE_TYPE',
      'type' => 'json',
      'dbType' => 'text',
      'import' => false,
      'reportable' => false,
      'function_bean' => 'Users',
      'function' => 'getSystemLicenseTypesSelections',
      'isMultiSelect' => true,
      'showNullOption' => false,
      'studio' => 
      array (
        'formula' => false,
      ),
      'idm_mode_disabled' => true,
      'massupdate' => false,
    ),
    'portal_user_password' => 
    array (
      'name' => 'portal_user_password',
      'vname' => 'LBL_PASSWORD',
      'type' => 'varchar',
      'source' => 'non-db',
      'studio' => false,
      'massupdate' => false,
    ),
    'mail_credentials' => 
    array (
      'name' => 'mail_credentials',
      'vname' => 'LBL_MAIL_CREDENTIALS',
      'type' => 'json',
      'source' => 'non-db',
      'studio' => false,
      'dependency' => 'not(equal($mail_credentials, ""))',
    ),
    'email_link_type' => 
    array (
      'name' => 'email_link_type',
      'vname' => 'LBL_EMAIL_LINK_TYPE',
      'type' => 'enum',
      'function_bean' => 'Users',
      'function' => 'getEmailLinkDropdown',
      'importable' => false,
      'reportable' => false,
      'source' => 'non-db',
      'studio' => false,
      'user_preference' => true,
      'massupdate' => false,
    ),
    'export_delimiter' => 
    array (
      'name' => 'export_delimiter',
      'vname' => 'LBL_EXPORT_DELIMITER',
      'type' => 'varchar',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'default_export_charset' => 
    array (
      'name' => 'default_export_charset',
      'vname' => 'LBL_EXPORT_CHARSET',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getDefaultExportCharsetOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'use_real_names' => 
    array (
      'name' => 'use_real_names',
      'vname' => 'LBL_USE_REAL_NAMES',
      'type' => 'bool',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'reminder_time' => 
    array (
      'name' => 'reminder_time',
      'vname' => 'LBL_POPUP_REMINDER_TIME',
      'type' => 'enum',
      'source' => 'non-db',
      'options' => 'reminder_time_options',
      'default' => -1,
      'user_preference' => true,
      'studio' => false,
    ),
    'email_reminder_time' => 
    array (
      'name' => 'email_reminder_time',
      'vname' => 'LBL_EMAIL_REMINDER_TIME',
      'type' => 'enum',
      'source' => 'non-db',
      'options' => 'reminder_time_options',
      'default' => -1,
      'user_preference' => true,
      'studio' => false,
    ),
    'appearance' => 
    array (
      'name' => 'appearance',
      'vname' => 'LBL_APPEARANCE',
      'type' => 'enum',
      'source' => 'non-db',
      'options' => 'appearance_options',
      'user_preference' => true,
      'studio' => false,
    ),
    'site_url' => 
    array (
      'name' => 'site_url',
      'vname' => 'LBL_SETTINGS_URL',
      'type' => 'text',
      'source' => 'non-db',
      'readonly' => true,
      'user_preference' => true,
      'studio' => false,
    ),
    'field_name_placement' => 
    array (
      'name' => 'field_name_placement',
      'vname' => 'LBL_FIELD_NAME_PLACEMENT',
      'type' => 'enum',
      'source' => 'non-db',
      'options' => 'field_name_placement_dom',
      'user_preference' => true,
      'studio' => false,
    ),
    'user_tabs' => 
    array (
      'name' => 'user_tabs',
      'vname' => 'LBL_EDIT_TABS',
      'type' => 'json',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'number_pinned_modules' => 
    array (
      'name' => 'number_pinned_modules',
      'vname' => 'LBL_NUMBER_PINNED_MODULES',
      'type' => 'int',
      'min' => 1,
      'max' => 100,
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'datef' => 
    array (
      'name' => 'datef',
      'vname' => 'LBL_DATE_FORMAT',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getDateFormatOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'timef' => 
    array (
      'name' => 'timef',
      'vname' => 'LBL_TIME_FORMAT',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getTimeFormatOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'default_currency_significant_digits' => 
    array (
      'name' => 'default_currency_significant_digits',
      'vname' => 'LBL_SYSTEM_SIG_DIGITS',
      'type' => 'enum',
      'source' => 'non-db',
      'options' => 'default_currency_significant_digits_dom',
      'user_preference' => true,
      'studio' => false,
    ),
    'num_grp_sep' => 
    array (
      'name' => 'num_grp_sep',
      'vname' => 'LBL_NUMBER_GROUPING_SEP',
      'type' => 'varchar',
      'source' => 'non-db',
      'len' => 1,
      'user_preference' => true,
      'studio' => false,
    ),
    'timezone' => 
    array (
      'name' => 'timezone',
      'vname' => 'LBL_TIMEZONE',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getTimeZoneOptions',
      'readonly' => true,
      'user_preference' => true,
      'studio' => false,
    ),
    'dec_sep' => 
    array (
      'name' => 'dec_sep',
      'vname' => 'LBL_DECIMAL_SEP',
      'type' => 'varchar',
      'source' => 'non-db',
      'len' => 1,
      'user_preference' => true,
      'studio' => false,
    ),
    'currency' => 
    array (
      'name' => 'currency',
      'vname' => 'LBL_CURRENCY',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getCurrencyOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'default_locale_name_format' => 
    array (
      'name' => 'default_locale_name_format',
      'vname' => 'LBL_LOCALE_DEFAULT_NAME_FORMAT',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getNameFormatOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'currency_show_preferred' => 
    array (
      'name' => 'currency_show_preferred',
      'vname' => 'LBL_CURRENCY_SHOW_PREFERRED',
      'type' => 'bool',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'currency_create_in_preferred' => 
    array (
      'name' => 'currency_create_in_preferred',
      'vname' => 'LBL_CURRENCY_CREATE_IN_PREFERRED',
      'type' => 'bool',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'sugarpdf_pdf_font_name_main' => 
    array (
      'name' => 'sugarpdf_pdf_font_name_main',
      'vname' => 'LBL_PDF_FONT_NAME_MAIN',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getFontListOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'sugarpdf_pdf_font_size_main' => 
    array (
      'name' => 'sugarpdf_pdf_font_size_main',
      'vname' => 'LBL_PDF_FONT_SIZE_MAIN',
      'type' => 'int',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'sugarpdf_pdf_font_name_data' => 
    array (
      'name' => 'sugarpdf_pdf_font_name_data',
      'vname' => 'LBL_PDF_FONT_NAME_DATA',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getFontListOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'sugarpdf_pdf_font_size_data' => 
    array (
      'name' => 'sugarpdf_pdf_font_size_data',
      'vname' => 'LBL_PDF_FONT_SIZE_DATA',
      'type' => 'int',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'calendar_publish_key' => 
    array (
      'name' => 'calendar_publish_key',
      'vname' => 'LBL_PUBLISH_KEY',
      'type' => 'varchar',
      'source' => 'non-db',
      'user_preference' => true,
      'studio' => false,
    ),
    'calendar_publish_location' => 
    array (
      'name' => 'calendar_publish_location',
      'vname' => 'LBL_YOUR_PUBLISH_URL',
      'type' => 'text',
      'source' => 'non-db',
      'readonly' => true,
      'user_preference' => true,
      'studio' => false,
    ),
    'calendar_search_location' => 
    array (
      'name' => 'calendar_search_location',
      'vname' => 'LBL_SEARCH_URL',
      'type' => 'text',
      'source' => 'non-db',
      'readonly' => true,
      'user_preference' => true,
      'studio' => false,
    ),
    'calendar_ical_subscription_url' => 
    array (
      'name' => 'calendar_ical_subscription_url',
      'vname' => 'LBL_ICAL_PUB_URL',
      'type' => 'text',
      'source' => 'non-db',
      'readonly' => true,
      'user_preference' => true,
      'studio' => false,
    ),
    'fdow' => 
    array (
      'name' => 'fdow',
      'vname' => 'LBL_FDOW',
      'type' => 'enum',
      'source' => 'non-db',
      'function_bean' => 'Users',
      'function' => 'getFirstDayOfWeekOptions',
      'user_preference' => true,
      'studio' => false,
    ),
    'default_team' => 
    array (
      'name' => 'default_team',
      'vname' => 'LBL_DEFAULT_TEAM',
      'reportable' => false,
      'type' => 'id',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
        'wirelesslistview' => false,
        'wirelessdetailview' => false,
        'wirelesseditview' => false,
      ),
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'vname' => 'LBL_DEFAULT_TEAM',
      'reportable' => false,
      'source' => 'non-db',
      'type' => 'id',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
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
      'relationship' => 'business_center_users',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER',
    ),
    'shifts' => 
    array (
      'name' => 'shifts',
      'type' => 'link',
      'relationship' => 'shifts_users',
      'source' => 'non-db',
      'module' => 'Shifts',
      'bean_name' => 'Shift',
      'rel_fields' => 
      array (
      ),
      'vname' => 'LBL_SHIFTS',
      'populate_list' => 
      array (
      ),
    ),
    'shift_exceptions' => 
    array (
      'name' => 'shift_exceptions',
      'type' => 'link',
      'relationship' => 'shift_exceptions_users',
      'source' => 'non-db',
      'module' => 'ShiftExceptions',
      'bean_name' => 'ShiftException',
      'rel_fields' => 
      array (
      ),
      'vname' => 'LBL_SHIFT_EXCEPTIONS',
      'populate_list' => 
      array (
      ),
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
      'studio' => 'false',
    ),
    'team_name' => 
    array (
      'name' => 'team_name',
      'db_concat_fields' => 
      array (
        0 => 'name',
        1 => 'name_2',
      ),
      'rname' => 'name',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'type' => 'relate',
      'required' => true,
      'table' => 'teams',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_link',
      'massupdate' => false,
      'dbType' => 'varchar',
      'source' => 'non-db',
      'custom_type' => 'teamset',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'editview' => false,
        'quickcreate' => false,
        'wirelesslistview' => false,
        'wirelessdetailview' => false,
        'wirelesseditview' => false,
        'recordview' => false,
        'recorddashletview' => false,
        'previewview' => false,
      ),
      'exportable' => true,
      'fields' => 
      array (
        0 => 'acl_team_set_id',
      ),
    ),
    'team_link' => 
    array (
      'name' => 'team_link',
      'type' => 'link',
      'relationship' => 'users_team',
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
      'relationship' => 'users_team_count_relationship',
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
      'relationship' => 'users_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
    ),
    'team_memberships' => 
    array (
      'name' => 'team_memberships',
      'type' => 'link',
      'relationship' => 'team_memberships',
      'source' => 'non-db',
      'vname' => 'LBL_TEAM_MEMBERSHIP',
    ),
    'team_sets' => 
    array (
      'name' => 'team_sets',
      'type' => 'link',
      'relationship' => 'users_team_sets',
      'source' => 'non-db',
      'vname' => 'LBL_TEAM_SET',
    ),
    'users_signatures' => 
    array (
      'name' => 'users_signatures',
      'type' => 'link',
      'relationship' => 'users_users_signatures',
      'source' => 'non-db',
      'studio' => 'false',
      'reportable' => false,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => false,
      'reportable' => false,
    ),
    'portal_only' => 
    array (
      'name' => 'portal_only',
      'vname' => 'LBL_PORTAL_ONLY_USER',
      'type' => 'bool',
      'massupdate' => false,
      'default' => '0',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
    ),
    'show_on_employees' => 
    array (
      'name' => 'show_on_employees',
      'vname' => 'LBL_SHOW_ON_EMPLOYEES',
      'type' => 'bool',
      'massupdate' => true,
      'importable' => true,
      'default' => true,
      'studio' => 
      array (
        'formula' => false,
      ),
    ),
    'employee_status' => 
    array (
      'name' => 'employee_status',
      'vname' => 'LBL_EMPLOYEE_STATUS',
      'type' => 'enum',
      'options' => 'employee_status_dom',
      'len' => 100,
      'calculation_visible' => true,
    ),
    'messenger_id' => 
    array (
      'name' => 'messenger_id',
      'vname' => 'LBL_MESSENGER_ID',
      'type' => 'varchar',
      'len' => 100,
      'calculation_visible' => true,
    ),
    'messenger_type' => 
    array (
      'name' => 'messenger_type',
      'vname' => 'LBL_MESSENGER_TYPE',
      'type' => 'enum',
      'options' => 'messenger_type_dom',
      'len' => 100,
      'calculation_visible' => true,
      'massupdate' => false,
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'calls_users',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'message_invites' => 
    array (
      'name' => 'message_invites',
      'type' => 'link',
      'relationship' => 'messages_users',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'kbusefulness' => 
    array (
      'name' => 'kbusefulness',
      'type' => 'link',
      'reportable' => false,
      'relationship' => 'kbusefulness',
      'link_class' => 'UsefulnessLink',
      'source' => 'non-db',
      'vname' => 'LBL_USEFULNESS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'meetings_users',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'contacts_sync' => 
    array (
      'name' => 'contacts_sync',
      'type' => 'link',
      'relationship' => 'contacts_users',
      'source' => 'non-db',
      'vname' => 'LBL_CONTACTS_SYNC',
      'reportable' => false,
    ),
    'reports_to_id' => 
    array (
      'name' => 'reports_to_id',
      'vname' => 'LBL_REPORTS_TO_ID',
      'type' => 'id',
      'required' => false,
    ),
    'reports_to_name' => 
    array (
      'name' => 'reports_to_name',
      'rname' => 'name',
      'id_name' => 'reports_to_id',
      'vname' => 'LBL_REPORTS_TO_NAME',
      'type' => 'relate',
      'isnull' => 'true',
      'module' => 'Users',
      'table' => 'users',
      'link' => 'reports_to_link',
      'reportable' => false,
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'side' => 'right',
    ),
    'reports_to_link' => 
    array (
      'name' => 'reports_to_link',
      'type' => 'link',
      'relationship' => 'user_direct_reports',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_REPORTS_TO',
    ),
    'reportees' => 
    array (
      'name' => 'reportees',
      'type' => 'link',
      'relationship' => 'user_direct_reports',
      'link_type' => 'many',
      'side' => 'left',
      'source' => 'non-db',
      'vname' => 'LBL_REPORTS_TO',
      'reportable' => false,
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
      'group' => 'email1',
      'required' => true,
      'merge_filter' => 'enabled',
      'studio' => false,
      'exportable' => true,
      'idm_mode_disabled' => true,
      'use_formula' => 'email',
    ),
    'email' => 
    array (
      'name' => 'email',
      'type' => 'email',
      'function' => 
      array (
        'name' => 'getEmailAddressWidget',
        'returns' => 'html',
      ),
      'query_type' => 'default',
      'source' => 'non-db',
      'operator' => 'subquery',
      'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
      'db_field' => 
      array (
        0 => 'id',
      ),
      'link' => 'email_addresses_primary',
      'rname' => 'email_address',
      'module' => 'EmailAddresses',
      'vname' => 'LBL_ANY_EMAIL',
      'studio' => 
      array (
        'visible' => false,
        'searchview' => true,
        'editField' => true,
      ),
      'sort_on' => 'email_addresses',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.67,
      ),
      'idm_mode_disabled' => true,
      'required' => true,
    ),
    'email_addresses' => 
    array (
      'name' => 'email_addresses',
      'type' => 'link',
      'relationship' => 'users_email_addresses',
      'module' => 'EmailAddress',
      'bean_name' => 'EmailAddress',
      'source' => 'non-db',
      'vname' => 'LBL_EMAIL_ADDRESSES',
      'reportable' => false,
      'required' => false,
      'link' => 'email_addresses_primary',
      'rname' => 'email_address',
      'idm_mode_disabled' => true,
    ),
    'email_addresses_primary' => 
    array (
      'name' => 'email_addresses_primary',
      'type' => 'link',
      'relationship' => 'users_email_addresses_primary',
      'source' => 'non-db',
      'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
      'duplicate_merge' => 'disabled',
      'required' => false,
      'idm_mode_disabled' => true,
    ),
    'aclroles' => 
    array (
      'name' => 'aclroles',
      'type' => 'link',
      'relationship' => 'acl_roles_users',
      'source' => 'non-db',
      'side' => 'right',
      'vname' => 'LBL_ROLES',
    ),
    'is_group' => 
    array (
      'name' => 'is_group',
      'vname' => 'LBL_GROUP_USER',
      'type' => 'bool',
      'massupdate' => false,
      'studio' => 
      array (
        'formula' => false,
      ),
      'default' => '0',
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
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
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
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
    ),
    'accept_status_id' => 
    array (
      'name' => 'accept_status_id',
      'type' => 'varchar',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'importable' => 'false',
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
      'massupdate' => false,
    ),
    'accept_status_name' => 
    array (
      'name' => 'accept_status_name',
      'type' => 'enum',
      'source' => 'non-db',
      'vname' => 'LBL_LIST_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'massupdate' => false,
      'studio' => 
      array (
        'listview' => false,
        'searchview' => false,
        'formula' => false,
      ),
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
    'prospect_lists' => 
    array (
      'name' => 'prospect_lists',
      'type' => 'link',
      'relationship' => 'prospect_list_users',
      'module' => 'ProspectLists',
      'source' => 'non-db',
      'vname' => 'LBL_PROSPECT_LIST',
    ),
    'emails_users' => 
    array (
      'name' => 'emails_users',
      'type' => 'link',
      'relationship' => 'emails_users_rel',
      'module' => 'Emails',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'holidays' => 
    array (
      'name' => 'holidays',
      'type' => 'link',
      'relationship' => 'users_holidays',
      'source' => 'non-db',
      'side' => 'right',
      'vname' => 'LBL_HOLIDAYS',
    ),
    'eapm' => 
    array (
      'name' => 'eapm',
      'type' => 'link',
      'relationship' => 'eapm_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'source' => 'non-db',
    ),
    'oauth_tokens' => 
    array (
      'name' => 'oauth_tokens',
      'type' => 'link',
      'relationship' => 'oauthtokens_assigned_user',
      'vname' => 'LBL_OAUTH_TOKENS',
      'link_type' => 'one',
      'module' => 'OAuthTokens',
      'bean_name' => 'OAuthToken',
      'source' => 'non-db',
      'side' => 'left',
    ),
    'project_resource' => 
    array (
      'name' => 'project_resource',
      'type' => 'link',
      'relationship' => 'projects_users_resources',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'quotas' => 
    array (
      'name' => 'quotas',
      'type' => 'link',
      'relationship' => 'users_quotas',
      'source' => 'non-db',
      'link_type' => 'one',
      'vname' => 'LBL_QUOTAS',
    ),
    'forecasts' => 
    array (
      'name' => 'forecasts',
      'type' => 'link',
      'relationship' => 'users_forecasts',
      'source' => 'non-db',
      'link_type' => 'one',
      'vname' => 'LBL_FORECASTS',
    ),
    'reportschedules' => 
    array (
      'name' => 'reportschedules',
      'type' => 'link',
      'relationship' => 'reportschedules_users',
      'source' => 'non-db',
      'vname' => 'LBL_REPORTSCHEDULES',
    ),
    'preferred_language' => 
    array (
      'name' => 'preferred_language',
      'type' => 'enum',
      'vname' => 'LBL_PREFERRED_LANGUAGE',
      'options' => 'available_language_dom',
      'studio' => 
      array (
        'editview' => false,
        'detailview' => false,
        'listview' => false,
        'quickcreate' => false,
        'searchview' => false,
        'wirelesseditview' => false,
        'wirelessdetailview' => false,
        'wirelesslistview' => false,
      ),
      'calculation_visible' => true,
    ),
    'activities' => 
    array (
      'name' => 'activities',
      'type' => 'link',
      'relationship' => 'activities_users',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'acl_role_set_id' => 
    array (
      'name' => 'acl_role_set_id',
      'type' => 'id',
      'link' => 'acl_role_sets',
      'reportable' => false,
    ),
    'acl_role_sets' => 
    array (
      'name' => 'acl_role_sets',
      'type' => 'link',
      'relationship' => 'users_acl_role_sets',
      'source' => 'non-db',
    ),
    'site_user_id' => 
    array (
      'name' => 'site_user_id',
      'vname' => 'LBL_SITE_USER_ID',
      'type' => 'varchar',
      'len' => '64',
      'reportable' => false,
      'importable' => false,
      'studio' => 
      array (
        'editview' => false,
        'detailview' => false,
        'listview' => false,
        'searchview' => false,
        'related' => false,
        'recordview' => false,
        'recorddashletview' => false,
        'previewview' => false,
        'wirelessdetailview' => false,
        'wirelesseditview' => false,
      ),
      'readonly' => true,
    ),
    'cookie_consent' => 
    array (
      'name' => 'cookie_consent',
      'vname' => 'LBL_COOKIE_CONSENT',
      'type' => 'bool',
      'default' => '0',
      'audited' => true,
      'comment' => 'Indicator whether this user accepts cookies',
      'duplicate_on_record_copy' => 'no',
      'calculation_visible' => true,
      'massupdate' => false,
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
      'calculation_visible' => true,
      'massupdate' => false,
      'studio' => false,
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
      'calculation_visible' => true,
    ),
    'customer_journey_access' => 
    array (
      'name' => 'customer_journey_access',
      'vname' => 'LBL_CUSTOMER_JOURNEY_ACCESS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'studio' => false,
      'readonly' => true,
    ),
    'customer_journey_last_active' => 
    array (
      'name' => 'customer_journey_last_active',
      'vname' => 'LBL_CUSTOMER_JOURNEY_LAST_ACTIVE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'studio' => false,
      'readonly' => true,
    ),
  ),
  'name_format_map' => 
  array (
    'f' => 'first_name',
    'l' => 'last_name',
    't' => 'title',
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'userspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_user_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'user_name',
        1 => 'is_group',
        2 => 'status',
        3 => 'last_name',
        4 => 'first_name',
        5 => 'id',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_user_first_last',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
        2 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_users_reports_to_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'reports_to_id',
        1 => 'id',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_user_type_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
        1 => 'is_admin',
        2 => 'deleted',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_users_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    6 => 
    array (
      'name' => 'idx_users_del_businesscenter',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'business_center_id',
      ),
    ),
    'idx_user_cjp_access' => 
    array (
      'name' => 'idx_user_cjp_access',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'customer_journey_access',
        1 => 'deleted',
        2 => 'user_name',
        3 => 'is_group',
        4 => 'portal_only',
        5 => 'status',
      ),
    ),
    'idx_user_cjp_last_active' => 
    array (
      'name' => 'idx_user_cjp_last_active',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'customer_journey_access',
        1 => 'customer_journey_last_active',
        2 => 'deleted',
        3 => 'user_name',
        4 => 'is_group',
        5 => 'portal_only',
        6 => 'status',
      ),
    ),
  ),
  'required_import_indexes' => 
  array (
    0 => 'idx_user_name::user_name',
  ),
  'relationships' => 
  array (
    'user_direct_reports' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'reports_to_id',
      'relationship_type' => 'one-to-many',
    ),
    'users_users_signatures' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'UserSignatures',
      'rhs_table' => 'users_signatures',
      'rhs_key' => 'user_id',
      'relationship_type' => 'one-to-many',
    ),
    'users_email_addresses' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailAddresses',
      'rhs_table' => 'email_addresses',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'email_addr_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'email_address_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Users',
    ),
    'users_email_addresses_primary' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailAddresses',
      'rhs_table' => 'email_addresses',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'email_addr_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'email_address_id',
      'relationship_role_columns' => 
      array (
        'primary_address' => '1',
        'bean_module' => 'Users',
      ),
    ),
    'users_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'users_teams' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'users_forecasts' => 
    array (
      'rhs_module' => 'Forecasts',
      'rhs_table' => 'forecasts',
      'rhs_key' => 'user_id',
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'forecast_type',
      'relationship_role_column_value' => 'Rollup',
    ),
    'users_quotas' => 
    array (
      'rhs_module' => 'Quotas',
      'rhs_table' => 'quotas',
      'rhs_key' => 'user_id',
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'quota_type',
      'relationship_role_column_value' => 'Direct',
    ),
    'users_team_sets' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_id',
      'join_key_rhs' => 'team_set_id',
    ),
    'users_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'default_team',
      'relationship_type' => 'one-to-many',
    ),
    'users_acl_role_sets' => 
    array (
      'lhs_module' => 'ACLRoleSets',
      'lhs_table' => 'acl_role_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'acl_role_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'users_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'created_by',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'id',
      'relationship_type' => 'one-to-many',
    ),
    'employees_created_by' => 
    array (
      'lhs_module' => 'Employees',
      'lhs_table' => 'users',
      'lhs_key' => 'created_by',
      'rhs_module' => 'Users',
      'rhs_table' => 'users',
      'rhs_key' => 'id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'acls' => 
  array (
    'SugarACLUsers' => true,
    'SugarACLStatic' => true,
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
  'full_text_search' => true,
  'hidden_to_role_assignment' => true,
);