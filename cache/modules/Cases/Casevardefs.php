<?php 
 $GLOBALS["dictionary"]["Case"]=array (
  'table' => 'cases',
  'audited' => true,
  'escalatable' => true,
  'color' => 'orange',
  'icon' => 'sicon-case-lg',
  'activity_enabled' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'duplicate_merge' => true,
  'console_tab_badges' => true,
  'comment' => 'Cases are issues or problems that a customer asks a support representative to resolve',
  'change_timer_fields' => 
  array (
    0 => 'status',
    1 => 'assigned_user_id',
  ),
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
      'vname' => 'LBL_SUBJECT',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'audited' => true,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.53,
      ),
      'comment' => 'The short description of the bug',
      'merge_filter' => 'selected',
      'required' => true,
      'importable' => 'required',
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
        'boost' => 0.66,
      ),
      'rows' => 6,
      'cols' => 80,
      'duplicate_on_record_copy' => 'always',
      'dbtype' => 'longtext',
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
      'relationship' => 'cases_created_by',
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
      'relationship' => 'cases_modified_user',
      'vname' => 'LBL_MODIFIED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'activities' => 
    array (
      'name' => 'activities',
      'type' => 'link',
      'relationship' => 'case_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'case_number' => 
    array (
      'name' => 'case_number',
      'vname' => 'LBL_NUMBER',
      'type' => 'int',
      'readonly' => true,
      'len' => 11,
      'required' => true,
      'auto_increment' => true,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.29,
      ),
      'comment' => 'Visual unique identifier',
      'duplicate_merge' => 'disabled',
      'disable_num_format' => true,
      'studio' => 
      array (
        'quickcreate' => false,
      ),
      'duplicate_on_record_copy' => 'no',
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_TYPE',
      'type' => 'enum',
      'options' => 'case_type_dom',
      'len' => 255,
      'comment' => 'The type of issue (ex: issue, feature)',
      'merge_filter' => 'enabled',
      'sortable' => true,
      'duplicate_on_record_copy' => 'always',
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'options' => 'case_status_dom',
      'len' => 100,
      'audited' => true,
      'comment' => 'The status of the case',
      'merge_filter' => 'enabled',
      'sortable' => true,
    ),
    'priority' => 
    array (
      'name' => 'priority',
      'vname' => 'LBL_PRIORITY',
      'type' => 'enum',
      'options' => 'case_priority_dom',
      'len' => 100,
      'audited' => true,
      'comment' => 'The priority of the case',
      'merge_filter' => 'enabled',
      'sortable' => true,
    ),
    'resolution' => 
    array (
      'name' => 'resolution',
      'vname' => 'LBL_RESOLUTION',
      'type' => 'text',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.65,
      ),
      'comment' => 'The resolution of the case',
    ),
    'work_log' => 
    array (
      'name' => 'work_log',
      'vname' => 'LBL_WORK_LOG',
      'type' => 'text',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.64,
      ),
      'duplicate_on_record_copy' => 'always',
      'comment' => 'Free-form text used to denote activities of interest',
    ),
    'follow_up_datetime' => 
    array (
      'name' => 'follow_up_datetime',
      'vname' => 'LBL_FOLLOW_UP_DATETIME',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'comment' => 'Deadline for following up on an issue',
      'audited' => true,
    ),
    'widget_follow_up_datetime' => 
    array (
      'name' => 'widget_follow_up_datetime',
      'vname' => 'LBL_WIDGET_FOLLOW_UP_DATETIME',
      'type' => 'widget',
      'multiline' => false,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'source' => 'non-db',
      'console' => 
      array (
        'name' => 'follow_up_datetime',
        'label' => 'LBL_WIDGET_FOLLOW_UP_DATETIME',
        'type' => 'follow-up-datetime-colorcoded',
        'color_code_classes' => 
        array (
          'overdue' => 'expired',
          'in_a_day' => 'soon-expired',
          'more_than_a_day' => 'white black-text',
        ),
      ),
    ),
    'resolved_datetime' => 
    array (
      'name' => 'resolved_datetime',
      'vname' => 'LBL_RESOLVED_DATETIME',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'comment' => 'Date when an issue is resolved',
      'audited' => true,
    ),
    'hours_to_resolution' => 
    array (
      'name' => 'hours_to_resolution',
      'vname' => 'LBL_HOURS_TO_RESOLUTION',
      'type' => 'decimal',
      'len' => '12,2',
      'precision' => 2,
      'comment' => 'How long it took to resolve this issue, in decimal calendar hours',
      'audited' => true,
      'readonly' => true,
    ),
    'business_hours_to_resolution' => 
    array (
      'name' => 'business_hours_to_resolution',
      'vname' => 'LBL_BUSINESS_HOURS_TO_RESOLUTION',
      'type' => 'decimal',
      'len' => '12,2',
      'precision' => 2,
      'comment' => 'How long it took to resolve this issue, in decimal business hours',
      'audited' => true,
      'readonly' => true,
    ),
    'pending_processing' => 
    array (
      'name' => 'pending_processing',
      'vname' => 'LBL_PENDING_PROCESSING',
      'type' => 'bool',
      'default' => 0,
      'reportable' => false,
      'readonly' => true,
      'studio' => false,
      'processes' => true,
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'rname' => 'name',
      'id_name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_NAME',
      'type' => 'relate',
      'related_fields' => 
      array (
        0 => 'account_id',
      ),
      'link' => 'accounts',
      'table' => 'accounts',
      'join_name' => 'accounts',
      'isnull' => 'true',
      'module' => 'Accounts',
      'dbType' => 'varchar',
      'len' => 100,
      'source' => 'non-db',
      'unified_search' => true,
      'comment' => 'The name of the account represented by the account_id field',
      'required' => true,
      'importable' => 'required',
      'exportable' => true,
      'studio' => 
      array (
        'portalrecordview' => false,
        'portallistview' => false,
      ),
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'type' => 'relate',
      'dbType' => 'id',
      'rname' => 'id',
      'module' => 'Accounts',
      'id_name' => 'account_id',
      'reportable' => false,
      'vname' => 'LBL_ACCOUNT_ID',
      'audited' => true,
      'massupdate' => false,
      'comment' => 'The account to which the case is associated',
    ),
    'service_level' => 
    array (
      'name' => 'service_level',
      'rname' => 'service_level',
      'id_name' => 'account_id',
      'vname' => 'LBL_SERVICE_LEVEL',
      'type' => 'relate',
      'link' => 'accounts',
      'table' => 'accounts',
      'join_name' => 'accounts',
      'isnull' => 'true',
      'module' => 'Accounts',
      'source' => 'non-db',
      'reportable' => false,
      'massupdate' => false,
      'comment' => 'Service level of the associated account of case',
      'readonly' => true,
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
      'relationship' => 'business_center_cases',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_BUSINESS_CENTER',
    ),
    'source' => 
    array (
      'name' => 'source',
      'vname' => 'LBL_SOURCE',
      'type' => 'enum',
      'options' => 'cases_source_dom',
      'len' => 255,
      'comment' => 'An indicator of how the case was entered (ex: via web, email, etc.)',
    ),
    'request_close' => 
    array (
      'name' => 'request_close',
      'type' => 'bool',
      'default' => NULL,
      'readonly' => true,
      'audited' => true,
      'reportable' => true,
      'vname' => 'LBL_REQUEST_CLOSE',
      'comment' => 'The request close status of the case',
    ),
    'request_close_date' => 
    array (
      'name' => 'request_close_date',
      'type' => 'datetimecombo',
      'default' => NULL,
      'readonly' => true,
      'audited' => true,
      'reportable' => true,
      'vname' => 'LBL_REQUEST_CLOSE_DATE',
      'comment' => 'The date/time that the request close was initiated',
    ),
    'portal_viewable' => 
    array (
      'name' => 'portal_viewable',
      'vname' => 'LBL_SHOW_IN_PORTAL',
      'type' => 'bool',
      'default' => 1,
      'reportable' => false,
    ),
    'changetimers' => 
    array (
      'name' => 'changetimers',
      'type' => 'link',
      'relationship' => 'cases_changetimers',
      'source' => 'non-db',
      'vname' => 'LBL_CHANGETIMERS',
    ),
    'widget_status' => 
    array (
      'name' => 'widget_status',
      'vname' => 'LBL_WIDGET_STATUS',
      'type' => 'widget',
      'multiline' => false,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'source' => 'non-db',
      'console' => 
      array (
        'name' => 'status',
        'label' => 'LBL_WIDGET_STATUS',
        'type' => 'case-status',
      ),
    ),
    'purchases' => 
    array (
      'name' => 'purchases',
      'type' => 'link',
      'relationship' => 'cases_purchases',
      'source' => 'non-db',
      'vname' => 'LBL_PURCHASES_SUBPANEL_TITLE',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'case_messages',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'case_escalations',
      'module' => 'Escalations',
      'bean_name' => 'Escalation',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'external_users' => 
    array (
      'name' => 'external_users',
      'type' => 'link',
      'relationship' => 'external_users_cases',
      'source' => 'non-db',
      'vname' => 'LBL_EXTERNAL_USERS',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'case_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'case_notes',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'case_meetings',
      'bean_name' => 'Meeting',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_cases_rel',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'archived_emails' => 
    array (
      'name' => 'archived_emails',
      'type' => 'link',
      'link_class' => 'CaseEmailsLink',
      'link' => 'contacts',
      'module' => 'Emails',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
      'link_type' => 'many',
      'relationship' => '',
      'hideacl' => true,
      'readonly' => true,
    ),
    'documents' => 
    array (
      'name' => 'documents',
      'type' => 'link',
      'relationship' => 'documents_cases',
      'source' => 'non-db',
      'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'case_calls',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'cases_bugs',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'contacts_cases',
      'source' => 'non-db',
      'vname' => 'LBL_CONTACTS',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'account_cases',
      'link_type' => 'one',
      'side' => 'right',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNT',
    ),
    'project' => 
    array (
      'name' => 'project',
      'type' => 'link',
      'relationship' => 'projects_cases',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'kbcontents' => 
    array (
      'name' => 'kbcontents',
      'type' => 'link',
      'vname' => 'LBL_KBCONTENTS_SUBPANEL_TITLE',
      'relationship' => 'relcases_kbcontents',
      'source' => 'non-db',
      'link_type' => 'many',
      'side' => 'right',
    ),
    'primary_contact_name' => 
    array (
      'name' => 'primary_contact_name',
      'rname' => 'name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'related_fields' => 
      array (
        0 => 'primary_contact_id',
      ),
      'source' => 'non-db',
      'len' => '255',
      'group' => 'primary_contact_name',
      'vname' => 'LBL_PRIMARY_CONTACT_NAME',
      'reportable' => true,
      'id_name' => 'primary_contact_id',
      'join_name' => 'case_contact',
      'type' => 'relate',
      'module' => 'Contacts',
      'link' => 'case_contact',
      'table' => 'contacts',
      'audited' => true,
      'studio' => 
      array (
        'portal' => 
        array (
          'portalrecordview' => true,
          'portallistview' => false,
        ),
      ),
    ),
    'primary_contact_id' => 
    array (
      'name' => 'primary_contact_id',
      'type' => 'id',
      'group' => 'primary_contact_name',
      'reportable' => true,
      'vname' => 'LBL_PRIMARY_CONTACT_ID',
      'audited' => true,
    ),
    'case_contact' => 
    array (
      'name' => 'case_contact',
      'type' => 'link',
      'relationship' => 'contact_cases',
      'source' => 'non-db',
      'side' => 'right',
      'vname' => 'LBL_CONTACT',
      'module' => 'Contacts',
      'bean_name' => 'Contact',
      'id_name' => 'primary_contact_id',
      'link_type' => 'one',
      'audited' => true,
      'populate_list' => 
      array (
        0 => 'account_id',
        1 => 'account_name',
      ),
    ),
    'attachments' => 
    array (
      'name' => 'attachments',
      'vname' => 'LBL_ATTACHMENTS',
      'type' => 'link',
      'relationship' => 'case_attachments',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
    ),
    'attachment_list' => 
    array (
      'name' => 'attachment_list',
      'links' => 
      array (
        0 => 'attachments',
      ),
      'order_by' => 'name:asc',
      'source' => 'non-db',
      'studio' => 
      array (
        'recordview' => true,
        'previewview' => true,
        'recorddashletview' => true,
        'visible' => false,
      ),
      'type' => 'collection',
      'vname' => 'LBL_ATTACHMENTS',
      'reportable' => false,
      'hideacl' => true,
      'filter' => 
      array (
        0 => 
        array (
          'attachment_flag' => '1',
        ),
      ),
    ),
    'action_claim' => 
    array (
      'name' => 'action_claim',
      'source' => 'non-db',
      'vname' => 'LBL_ACTION_CLAIM',
      'type' => 'actionbutton',
      'massupdate' => false,
      'hidemassupdate' => false,
      'no_default' => false,
      'importable' => true,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'audited' => false,
      'reportable' => false,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'studio' => 
      array (
        'editField' => true,
        'recordview' => false,
        'previewview' => false,
        'recorddashletview' => false,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wirelessdetailview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'options' => '{"settings":{"type":"YnV0dG9u","size":"ZGVmYXVsdA==","showFieldLabel":false,"showInRecordHeader":true,"hideOnEdit":false,"displayOnFocusDashboard":true},"actionMenu":{"orderNumber":1,"listView":true,"recordView":true,"recordViewDashlet":true,"focusDashboardHeader":true,"subpanels":true},"buttons":{"52d8b90b-9ae1-4dd5-a2fc-bfb3dec0782c":{"active":true,"buttonId":"NTJkOGI5MGItOWFlMS00ZGQ1LWEyZmMtYmZiM2RlYzA3ODJj","orderNumber":0,"properties":{"label":"TEJMX0NMQUlNX0JVVFRPTl9URVhU","description":"","showLabel":true,"showIcon":false,"colorScheme":"cHJpbWFyeQ==","icon":"c2ljb24tc2V0dGluZ3M=","isDependent":true,"stopOnError":false,"formula":"bm90KGlzT3duZXIoKSk="},"actions":{"23cea3f5-9b41-499c-bbd2-e0d6e467f25a":{"actionType":"YXNzaWduLXJlY29yZA==","orderNumber":0,"properties":{"id":"","name":"","assignToCurrentUser":true}}}}}}',
    ),
    'action_close' => 
    array (
      'name' => 'action_close',
      'source' => 'non-db',
      'vname' => 'LBL_ACTION_CLOSE',
      'type' => 'actionbutton',
      'massupdate' => false,
      'hidemassupdate' => false,
      'no_default' => false,
      'importable' => true,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'audited' => false,
      'reportable' => false,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'studio' => 
      array (
        'editField' => true,
        'recordview' => false,
        'previewview' => false,
        'recorddashletview' => false,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wirelessdetailview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'options' => '{"settings":{"type":"YnV0dG9u","size":"ZGVmYXVsdA==","showFieldLabel":false,"showInRecordHeader":true,"hideOnEdit":false,"displayOnFocusDashboard":true},"actionMenu":{"orderNumber":2,"listView":true,"recordView":true,"recordViewDashlet":true,"focusDashboardHeader":true,"subpanels":true},"buttons":{"2f7d92f4-133f-4f66-aebf-c84e34575bd3":{"active":true,"buttonId":"MmY3ZDkyZjQtMTMzZi00ZjY2LWFlYmYtYzg0ZTM0NTc1YmQz","orderNumber":0,"properties":{"label":"TEJMX0NMT1NFX0JVVFRPTl9URVhU","description":"","showLabel":true,"showIcon":false,"colorScheme":"cHJpbWFyeQ==","icon":"","isDependent":true,"stopOnError":false,"formula":"bm90KGVxdWFsKCRzdGF0dXMsICJDbG9zZWQiKSk="},"actions":{"85baf0f5-ad48-4159-b22e-796a206e934e":{"actionType":"dXBkYXRlLXJlY29yZA==","orderNumber":0,"properties":{"fieldsToBeUpdated":{"status":{"fieldName":"c3RhdHVz","isCalculated":false,"formula":"","value":{"status":"Q2xvc2Vk"}}},"autoSave":true}}}}}}',
    ),
    'action_unassign' => 
    array (
      'name' => 'action_unassign',
      'source' => 'non-db',
      'vname' => 'LBL_ACTION_UNASSIGN',
      'type' => 'actionbutton',
      'massupdate' => false,
      'hidemassupdate' => false,
      'no_default' => false,
      'importable' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'audited' => false,
      'reportable' => false,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'studio' => 
      array (
        'editField' => true,
        'recordview' => false,
        'previewview' => false,
        'recorddashletview' => false,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wirelessdetailview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'options' => '{"settings":{"type":"YnV0dG9u","size":"ZGVmYXVsdA==","showFieldLabel":false,"showInRecordHeader":false,"hideOnEdit":false,"displayOnFocusDashboard":false},"actionMenu":{"orderNumber":"MQ==","listView":true,"recordView":true,"recordViewDashlet":true,"focusDashboardHeader":true,"subpanels":true},"buttons":{"f2b0c260-fd7c-4130-a5d5-de9b9bc9717b":{"active":true,"buttonId":"ZjJiMGMyNjAtZmQ3Yy00MTMwLWE1ZDUtZGU5YjliYzk3MTdi","orderNumber":0,"properties":{"label":"TEJMX1VOQVNTSUdOX0JVVFRPTl9URVhU","description":"","showLabel":true,"showIcon":false,"colorScheme":"cHJpbWFyeQ==","icon":"c2ljb24tc2V0dGluZ3M=","isDependent":true,"stopOnError":false,"formula":"aXNBc3NpZ25lZCgp"},"actions":{"9d083eae-199a-43b7-b206-b36e474d1c94":{"actionType":"YXNzaWduLXJlY29yZA==","orderNumber":0,"properties":{"id":"","name":"","assignToCurrentUser":false},"id":"OWQwODNlYWUtMTk5YS00M2I3LWIyMDYtYjM2ZTQ3NGQxYzk0"}}}}}',
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
      'relationship' => 'cases_following',
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
      'relationship' => 'cases_favorite',
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
      'relationship' => 'cases_tags',
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
      'relationship' => 'cases_commentlog',
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
      'relationship' => 'cases_locked_fields',
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
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'cases_assigned_user',
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
      'relationship' => 'cases_team',
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
      'relationship' => 'cases_team_count_relationship',
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
      'relationship' => 'cases_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
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
    'first_response_target_datetime' => 
    array (
      'name' => 'first_response_target_datetime',
      'vname' => 'LBL_FIRST_RESPONSE_TARGET_DATETIME',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'readonly' => true,
    ),
    'first_response_actual_datetime' => 
    array (
      'name' => 'first_response_actual_datetime',
      'vname' => 'LBL_FIRST_RESPONSE_ACTUAL_DATETIME',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'readonly' => true,
    ),
    'hours_to_first_response' => 
    array (
      'name' => 'hours_to_first_response',
      'vname' => 'LBL_HOURS_TO_FIRST_RESPONSE',
      'type' => 'decimal',
      'len' => '12',
      'precision' => '2',
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'readonly' => true,
    ),
    'business_hrs_to_first_response' => 
    array (
      'name' => 'business_hrs_to_first_response',
      'vname' => 'LBL_BUSINESS_HOURS_TO_FIRST_RESPONSE',
      'type' => 'decimal',
      'len' => '12',
      'precision' => '2',
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'readonly' => true,
    ),
    'first_response_var_from_target' => 
    array (
      'name' => 'first_response_var_from_target',
      'vname' => 'LBL_FIRST_RESPONSE_VARIANCE_FROM_TARGET',
      'type' => 'decimal',
      'len' => '12',
      'precision' => '2',
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'readonly' => true,
    ),
    'first_response_sla_met' => 
    array (
      'name' => 'first_response_sla_met',
      'vname' => 'LBL_FIRST_RESPONSE_SLA_MET',
      'type' => 'enum',
      'options' => 'first_response_met_sla_dom',
      'len' => 100,
      'audited' => false,
      'reportable' => true,
      'massupdate' => false,
      'processes' => true,
    ),
    'first_response_user_id' => 
    array (
      'name' => 'first_response_user_id',
      'vname' => 'LBL_FIRST_RESPONSE_USER_ID',
      'group' => 'first_response_user_name',
      'type' => 'id',
      'reportable' => false,
      'audited' => false,
      'massupdate' => false,
    ),
    'first_response_user_name' => 
    array (
      'name' => 'first_response_user_name',
      'link' => 'first_response_user_link',
      'vname' => 'LBL_FIRST_RESPONSE_USER_NAME',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => true,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'first_response_user_id',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'readonly' => true,
      'related_fields' => 
      array (
        0 => 'first_response_user_id',
      ),
    ),
    'first_response_user_link' => 
    array (
      'name' => 'first_response_user_link',
      'type' => 'link',
      'relationship' => 'cases_first_response_user',
      'vname' => 'LBL_FIRST_RESPONSE_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'id_name' => 'first_response_user_id',
      'table' => 'users',
      'side' => 'right',
    ),
    'first_response_sent' => 
    array (
      'name' => 'first_response_sent',
      'vname' => 'LBL_FIRST_RESPONSE_SENT',
      'type' => 'bool',
      'audited' => true,
      'reportable' => true,
      'massupdate' => false,
      'processes' => true,
    ),
    'is_escalated' => 
    array (
      'name' => 'is_escalated',
      'vname' => 'LBL_IS_ESCALATED',
      'type' => 'bool',
      'default' => '0',
      'readonly' => true,
      'displayParams' => 
      array (
        'type' => 'badge',
        'badge_label' => 'LBL_ESCALATED',
        'warning_level' => 'important',
      ),
      'comment' => 'Is this escalated?',
      'studio' => 
      array (
        'previewview' => false,
        'recorddashletview' => false,
        'portalrecordview' => false,
        'portallistview' => false,
        'mobile' => 
        array (
          'wirelesseditview' => false,
          'wirelessdetailview' => false,
        ),
      ),
    ),
    'dri_workflows' => 
    array (
      'name' => 'dri_workflows',
      'vname' => 'LBL_DRI_WORKFLOWS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'dri_workflow_cases',
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
          0 => 'Cases',
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
      'relationship' => 'case_dri_workflow_templates',
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
      'name' => 'idx_cases_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_cases_del_d_m',
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
      'name' => 'idx_cases_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_cases_del_d_e',
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
      'name' => 'idx_cases_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'number' => 
    array (
      'name' => 'casesnumk',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'case_number',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_case_del_nam_dm',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'name',
        2 => 'date_modified',
        3 => 'id',
        4 => 'team_set_id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_account_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'account_id',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_cases_stat_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'status',
        2 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_cases_del_prim_contact_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'primary_contact_id',
        1 => 'deleted',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_cases_del_businesscenter',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'business_center_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_cases_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_cases_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_cases' => 
    array (
      'name' => 'idx_cases_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_cases' => 
    array (
      'name' => 'idx_cases_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'first_response_user_id' => 
    array (
      'name' => 'idx_cases_first_response_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'first_response_user_id',
        1 => 'deleted',
      ),
    ),
    'idx_cases_cjtpl_id' => 
    array (
      'name' => 'idx_cases_cjtpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
  ),
  'relationships' => 
  array (
    'cases_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'cases_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'case_activities' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
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
      'relationship_role_column_value' => 'Cases',
    ),
    'cases_changetimers' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'ChangeTimers',
      'rhs_table' => 'changetimers',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_calls' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_tasks' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_notes' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_columns' => 
      array (
        'parent_type' => 'Cases',
        'attachment_flag' => 0,
      ),
    ),
    'case_messages' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_escalations' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Escalations',
      'rhs_table' => 'escalations',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_meetings' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'case_emails' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
    ),
    'cases_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'contact_cases' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'primary_contact_id',
      'relationship_type' => 'one-to-many',
    ),
    'case_attachments' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'AttachmentRelationship',
      'relationship_file' => 'data/Relationships/AttachmentRelationship.php',
      'relationship_role_columns' => 
      array (
        'parent_type' => 'Cases',
        'attachment_flag' => 1,
      ),
    ),
    'cases_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Cases',
      'user_field' => 'created_by',
    ),
    'cases_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Cases',
      'user_field' => 'created_by',
    ),
    'cases_tags' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Cases',
      'dynamic_subpanel' => true,
    ),
    'cases_commentlog' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Cases',
    ),
    'cases_locked_fields' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Cases',
    ),
    'cases_audit' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'cases_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'cases_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'cases_teams' => 
    array (
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'cases_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'cases_first_response_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'first_response_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'case_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'dri_workflow_template_id',
    ),
  ),
  'acls' => 
  array (
    'SugarACLStatic' => true,
    'SugarACLLockedFields' => true,
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
              'name' => 
              array (
                '$starts' => '$name',
              ),
            ),
            1 => 
            array (
              'status' => 
              array (
                '$not_equals' => 'Closed',
              ),
            ),
            2 => 
            array (
              'account_id' => 
              array (
                '$equals' => '$account_id',
              ),
            ),
          ),
        ),
      ),
      'ranking_fields' => 
      array (
        0 => 
        array (
          'in_field_name' => 'name',
          'dupe_field_name' => 'name',
        ),
        1 => 
        array (
          'in_field_name' => 'account_id',
          'dupe_field_name' => 'account_id',
        ),
      ),
    ),
  ),
  'optimistic_locking' => true,
  'portal_visibility' => 
  array (
    'class' => 'Cases',
    'links' => 
    array (
      'Accounts' => 'accounts',
      'Contacts' => 'contacts',
    ),
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
  ),
  'templates' => 
  array (
    'issue' => 'issue',
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
    'external_source' => 'external_source',
    'sla_fields' => 'sla_fields',
    'escalatable' => 'escalatable',
    'customer_journey_parent' => 'customer_journey_parent',
  ),
  'favorites' => true,
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);