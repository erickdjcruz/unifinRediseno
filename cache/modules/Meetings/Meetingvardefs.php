<?php 
 $GLOBALS["dictionary"]["Meeting"]=array (
  'table' => 'meetings',
  'color' => 'teal',
  'icon' => 'sicon-meetings-lg',
  'audited' => true,
  'activity_enabled' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'comment' => 'Meeting activities',
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
      'required' => true,
      'type' => 'name',
      'dbType' => 'varchar',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.43,
      ),
      'len' => '255',
      'comment' => 'Meeting name',
      'importable' => 'required',
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
        'boost' => 0.55,
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
      'relationship' => 'meetings_created_by',
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
      'relationship' => 'meetings_modified_user',
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
      'relationship' => 'meeting_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'accept_status' => 
    array (
      'name' => 'accept_status',
      'vname' => 'LBL_ACCEPT_STATUS',
      'type' => 'varchar',
      'dbType' => 'varchar',
      'len' => '20',
      'source' => 'non-db',
    ),
    'set_accept_links' => 
    array (
      'name' => 'set_accept_links',
      'vname' => 'LBL_ACCEPT_LINK',
      'type' => 'varchar',
      'dbType' => 'varchar',
      'len' => '20',
      'source' => 'non-db',
    ),
    'location' => 
    array (
      'name' => 'location',
      'vname' => 'LBL_LOCATION',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'Meeting location',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.36,
      ),
    ),
    'password' => 
    array (
      'name' => 'password',
      'vname' => 'LBL_PASSWORD',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'Meeting password',
      'studio' => 
      array (
        'wirelesseditview' => false,
        'wirelessdetailview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
      ),
      'dependency' => 'isInEnum($type,getDD("extapi_meeting_password"))',
    ),
    'join_url' => 
    array (
      'name' => 'join_url',
      'vname' => 'LBL_URL',
      'type' => 'varchar',
      'len' => '600',
      'comment' => 'Join URL',
      'studio' => 'false',
      'reportable' => false,
    ),
    'host_url' => 
    array (
      'name' => 'host_url',
      'vname' => 'LBL_HOST_URL',
      'type' => 'varchar',
      'len' => '600',
      'comment' => 'Host URL',
      'studio' => 'false',
      'reportable' => false,
    ),
    'displayed_url' => 
    array (
      'name' => 'displayed_url',
      'vname' => 'LBL_DISPLAYED_URL',
      'type' => 'url',
      'len' => '400',
      'comment' => 'Meeting URL',
      'studio' => 
      array (
        'wirelesseditview' => false,
        'wirelessdetailview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
      ),
      'dependency' => 'and(isAlpha($type),not(equal($type,"Sugar")))',
    ),
    'creator' => 
    array (
      'name' => 'creator',
      'vname' => 'LBL_CREATOR',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'Meeting creator',
      'studio' => 'false',
    ),
    'external_id' => 
    array (
      'name' => 'external_id',
      'vname' => 'LBL_EXTERNALID',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'Meeting ID for external app API',
      'studio' => 'false',
    ),
    'duration_hours' => 
    array (
      'name' => 'duration_hours',
      'vname' => 'LBL_DURATION_HOURS',
      'type' => 'int',
      'comment' => 'Duration (hours)',
      'importable' => 'required',
      'required' => true,
      'massupdate' => false,
      'studio' => false,
      'processes' => true,
      'default' => 0,
      'group' => 'end_date',
      'group_label' => 'LBL_DATE_END',
    ),
    'duration_minutes' => 
    array (
      'name' => 'duration_minutes',
      'vname' => 'LBL_DURATION_MINUTES',
      'type' => 'enum',
      'dbType' => 'int',
      'options' => 'duration_intervals',
      'group' => 'end_date',
      'group_label' => 'LBL_DATE_END',
      'len' => '2',
      'comment' => 'Duration (minutes)',
      'required' => true,
      'massupdate' => false,
      'studio' => false,
      'processes' => true,
      'default' => 0,
    ),
    'date_start' => 
    array (
      'name' => 'date_start',
      'vname' => 'LBL_CALENDAR_START_DATE',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'comment' => 'Date of start of meeting',
      'importable' => 'required',
      'required' => true,
      'massupdate' => false,
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'date_end',
        'blank' => false,
      ),
      'studio' => 
      array (
        'recordview' => false,
        'wirelesseditview' => false,
      ),
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'date_end' => 
    array (
      'name' => 'date_end',
      'vname' => 'LBL_CALENDAR_END_DATE',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'massupdate' => false,
      'comment' => 'Date meeting ends',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'studio' => 
      array (
        'recordview' => false,
        'wirelesseditview' => false,
      ),
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
      'group' => 'end_date',
      'group_label' => 'LBL_DATE_END',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
      'type' => 'parent_type',
      'dbType' => 'varchar',
      'group' => 'parent_name',
      'options' => 'parent_type_display',
      'len' => 100,
      'comment' => 'Module meeting is associated with',
      'studio' => 
      array (
        'searchview' => false,
        'wirelesslistview' => false,
      ),
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'len' => 100,
      'options' => 'meeting_status_dom',
      'comment' => 'Meeting status (ex: Planned, Held, Not held)',
      'default' => 'Planned',
      'duplicate_on_record_copy' => 'no',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_TYPE',
      'type' => 'enum',
      'len' => 255,
      'function' => 'getMeetingsExternalApiDropDown',
      'comment' => 'Meeting type (ex: WebEx, Other)',
      'options' => 'eapm_list',
      'default' => 'Sugar',
      'massupdate' => false,
      'studio' => 
      array (
        'wireless_basic_search' => false,
      ),
    ),
    'direction' => 
    array (
      'name' => 'direction',
      'vname' => 'LBL_DIRECTION',
      'type' => 'enum',
      'len' => 100,
      'options' => 'call_direction_dom',
      'comment' => 'Indicates whether call is inbound or outbound',
      'source' => 'non-db',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'type' => 'id',
      'group' => 'parent_name',
      'reportable' => false,
      'comment' => 'ID of item indicated by parent_type',
      'studio' => 
      array (
        'searchview' => false,
      ),
    ),
    'reminder_checked' => 
    array (
      'name' => 'reminder_checked',
      'vname' => 'LBL_POPUP_REMINDER',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'checkbox indicating whether or not the reminder value is set (Meta-data only)',
      'massupdate' => false,
      'studio' => false,
    ),
    'reminder_time' => 
    array (
      'name' => 'reminder_time',
      'vname' => 'LBL_POPUP_REMINDER_TIME',
      'type' => 'enum',
      'dbType' => 'int',
      'options' => 'reminder_time_options',
      'reportable' => false,
      'massupdate' => false,
      'default' => -1,
      'comment' => 'Specifies when a reminder alert should be issued; -1 means no alert; otherwise the number of seconds prior to the start',
      'studio' => 
      array (
        'recordview' => false,
        'wirelesseditview' => false,
        'previewview' => false,
        'default' => false,
      ),
    ),
    'email_reminder_checked' => 
    array (
      'name' => 'email_reminder_checked',
      'vname' => 'LBL_EMAIL_REMINDER',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'checkbox indicating whether or not the email reminder value is set (Meta-data only)',
      'massupdate' => false,
      'studio' => false,
    ),
    'email_reminder_time' => 
    array (
      'name' => 'email_reminder_time',
      'vname' => 'LBL_EMAIL_REMINDER_TIME',
      'type' => 'enum',
      'dbType' => 'int',
      'options' => 'reminder_time_options',
      'reportable' => false,
      'massupdate' => false,
      'default' => -1,
      'comment' => 'Specifies when a email reminder alert should be issued; -1 means no alert; otherwise the number of seconds prior to the start',
      'studio' => 
      array (
        'previewview' => false,
        'default' => false,
      ),
    ),
    'email_reminder_sent' => 
    array (
      'name' => 'email_reminder_sent',
      'vname' => 'LBL_EMAIL_REMINDER_SENT',
      'default' => 0,
      'type' => 'bool',
      'comment' => 'Whether email reminder is already sent',
      'studio' => false,
      'massupdate' => false,
    ),
    'outlook_id' => 
    array (
      'name' => 'outlook_id',
      'vname' => 'LBL_OUTLOOK_ID',
      'type' => 'varchar',
      'len' => '255',
      'reportable' => false,
      'comment' => 'When the Sugar Plug-in for Microsoft Outlook syncs an Outlook appointment, this is the Outlook appointment item ID',
      'studio' => false,
    ),
    'sequence' => 
    array (
      'name' => 'sequence',
      'vname' => 'LBL_SEQUENCE',
      'type' => 'int',
      'len' => '11',
      'reportable' => false,
      'default' => 0,
      'comment' => 'Meeting update sequence for meetings as per iCalendar standards',
      'studio' => false,
    ),
    'contact_name' => 
    array (
      'name' => 'contact_name',
      'rname' => 'name',
      'db_concat_fields' => 
      array (
        0 => 'first_name',
        1 => 'last_name',
      ),
      'id_name' => 'contact_id',
      'massupdate' => false,
      'vname' => 'LBL_CONTACT_NAME',
      'type' => 'relate',
      'link' => 'contacts',
      'table' => 'contacts',
      'isnull' => 'true',
      'module' => 'Contacts',
      'join_name' => 'contacts',
      'dbType' => 'varchar',
      'source' => 'non-db',
      'len' => 36,
      'importable' => 'false',
      'studio' => false,
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'module' => 'Contacts',
      'relationship' => 'meetings_contacts',
      'source' => 'non-db',
      'vname' => 'LBL_CONTACTS',
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'parent_type' => 'record_type_display',
      'type_name' => 'parent_type',
      'id_name' => 'parent_id',
      'vname' => 'LBL_LIST_RELATED_TO',
      'type' => 'parent',
      'group' => 'parent_name',
      'source' => 'non-db',
      'options' => 'parent_type_display',
      'studio' => true,
    ),
    'users' => 
    array (
      'name' => 'users',
      'type' => 'link',
      'relationship' => 'meetings_users',
      'source' => 'non-db',
      'vname' => 'LBL_USERS',
    ),
    'accept_status_users' => 
    array (
      'massupdate' => false,
      'name' => 'accept_status_users',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'vname' => 'LBL_ACCEPT_STATUS',
      'options' => 'dom_meeting_accept_status',
      'importable' => 'false',
      'link' => 'users',
      'rname_link' => 'accept_status',
    ),
    'internal_notes' => 
    array (
      'name' => 'internal_notes',
      'vname' => 'LBL_INTERNAL_NOTES',
      'type' => 'text',
      'comment' => 'Internal notes for the meeting',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'account_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNT',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'revenuelineitem_meetings',
      'module' => 'RevenueLineItems',
      'bean_name' => 'RevenueLineItem',
      'source' => 'non-db',
      'vname' => 'LBL_REVENUELINEITEMS',
      'workflow' => true,
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'relationship' => 'product_meetings',
      'module' => 'Products',
      'bean_name' => 'Product',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCTS',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'type' => 'link',
      'relationship' => 'bug_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_BUGS',
      'module' => 'Bugs',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'type' => 'link',
      'relationship' => 'meetings_leads',
      'source' => 'non-db',
      'vname' => 'LBL_LEADS',
    ),
    'project' => 
    array (
      'name' => 'project',
      'type' => 'link',
      'relationship' => 'projects_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'opportunity' => 
    array (
      'name' => 'opportunity',
      'type' => 'link',
      'relationship' => 'opportunity_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITY',
    ),
    'prospects' => 
    array (
      'name' => 'prospects',
      'type' => 'link',
      'relationship' => 'prospect_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_PROSPECTS',
      'module' => 'Prospects',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quote_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_QUOTES',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'type' => 'link',
      'relationship' => 'case_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_CASE',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'meetings_notes',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'meetings_messages',
      'module' => 'Messages',
      'bean_name' => 'Message',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'escalation_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'contact_id' => 
    array (
      'name' => 'contact_id',
      'type' => 'relate',
      'link' => 'contacts',
      'rname' => 'id',
      'vname' => 'LBL_CONTACT_ID',
      'source' => 'non-db',
      'studio' => false,
    ),
    'repeat_type' => 
    array (
      'name' => 'repeat_type',
      'vname' => 'LBL_CALENDAR_REPEAT_TYPE',
      'type' => 'enum',
      'len' => 36,
      'options' => 'repeat_type_dom',
      'comment' => 'Type of recurrence',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_interval' => 
    array (
      'name' => 'repeat_interval',
      'vname' => 'LBL_CALENDAR_REPEAT_INTERVAL',
      'type' => 'int',
      'len' => 3,
      'default' => 1,
      'comment' => 'Interval of recurrence',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_dow' => 
    array (
      'name' => 'repeat_dow',
      'vname' => 'LBL_CALENDAR_REPEAT_ON',
      'type' => 'varchar',
      'len' => 7,
      'comment' => 'Days of week in recurrence',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_until' => 
    array (
      'name' => 'repeat_until',
      'vname' => 'LBL_CALENDAR_REPEAT_UNTIL_DATE',
      'type' => 'date',
      'comment' => 'Repeat until specified date',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_count' => 
    array (
      'name' => 'repeat_count',
      'vname' => 'LBL_CALENDAR_REPEAT_COUNT',
      'type' => 'int',
      'len' => 7,
      'comment' => 'Number of recurrence',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_selector' => 
    array (
      'name' => 'repeat_selector',
      'vname' => 'LBL_CALENDAR_REPEAT_SELECTOR',
      'type' => 'enum',
      'len' => 36,
      'options' => 'repeat_selector_dom',
      'comment' => 'Repeat selector',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
      'visibility_grid' => 
      array (
        'trigger' => 'repeat_type',
        'values' => 
        array (
          '' => 
          array (
            0 => 'None',
          ),
          'Daily' => 
          array (
            0 => 'None',
          ),
          'Weekly' => 
          array (
            0 => 'None',
          ),
          'Monthly' => 
          array (
            0 => 'None',
            1 => 'On',
            2 => 'Each',
          ),
          'Yearly' => 
          array (
            0 => 'None',
            1 => 'On',
          ),
        ),
      ),
    ),
    'repeat_days' => 
    array (
      'name' => 'repeat_days',
      'vname' => 'LBL_CALENDAR_REPEAT_DAYS',
      'type' => 'varchar',
      'len' => 128,
      'comment' => 'Days of month',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_ordinal' => 
    array (
      'name' => 'repeat_ordinal',
      'vname' => 'LBL_CALENDAR_REPEAT_ORDINAL',
      'type' => 'enum',
      'len' => 36,
      'options' => 'repeat_ordinal_dom',
      'comment' => 'Repeat ordinal value',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_unit' => 
    array (
      'name' => 'repeat_unit',
      'vname' => 'LBL_CALENDAR_REPEAT_UNIT',
      'type' => 'enum',
      'len' => 36,
      'options' => 'repeat_unit_dom',
      'comment' => 'Repeat unit value',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'repeat_parent_id' => 
    array (
      'name' => 'repeat_parent_id',
      'vname' => 'LBL_REPEAT_PARENT_ID',
      'type' => 'id',
      'comment' => 'Id of the first element of recurring records',
      'importable' => 'false',
      'massupdate' => false,
      'reportable' => false,
      'studio' => 'false',
    ),
    'recurrence_id' => 
    array (
      'name' => 'recurrence_id',
      'vname' => 'LBL_CALENDAR_RECURRENCE_ID',
      'type' => 'datetime',
      'dbType' => 'datetime',
      'comment' => 'Recurrence ID of meeting. Original meeting start date',
      'importable' => false,
      'exportable' => false,
      'massupdate' => false,
      'studio' => false,
      'processes' => false,
      'visible' => false,
      'reportable' => false,
      'hideacl' => true,
    ),
    'rset' => 
    array (
      'name' => 'rset',
      'vname' => 'LBL_RSET',
      'type' => 'text',
      'comment' => 'The RSet with RRule and EXDATE',
      'studio' => false,
      'reportable' => false,
      'hideacl' => true,
    ),
    'event_type' => 
    array (
      'name' => 'event_type',
      'vname' => 'LBL_EVENT_TYPE',
      'type' => 'varchar',
      'len' => 30,
      'comment' => 'Type of event(Master, Occurrence, Exception)',
      'studio' => false,
      'reportable' => false,
      'hideacl' => true,
    ),
    'ical_uid' => 
    array (
      'name' => 'ical_uid',
      'vname' => 'LBL_ICAL_UID',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Unique ID used by external calendars',
      'studio' => false,
      'reportable' => false,
      'hideacl' => true,
    ),
    'original_start_date' => 
    array (
      'name' => 'original_start_date',
      'vname' => 'LBL_ORIGINAL_DATE',
      'type' => 'datetimecombo',
      'dbType' => 'datetime',
      'comment' => ' Original Date of start of meeting',
      'studio' => false,
      'reportable' => false,
      'hideacl' => true,
    ),
    'recurring_source' => 
    array (
      'name' => 'recurring_source',
      'vname' => 'LBL_RECURRING_SOURCE',
      'type' => 'varchar',
      'len' => 36,
      'comment' => 'Source of recurring meeting',
      'importable' => false,
      'massupdate' => false,
      'reportable' => false,
      'studio' => false,
    ),
    'send_invites' => 
    array (
      'name' => 'send_invites',
      'vname' => 'LBL_SEND_INVITES',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'checkbox indicating whether or not to send out invites (Meta-data only)',
      'massupdate' => false,
    ),
    'invitees' => 
    array (
      'name' => 'invitees',
      'source' => 'non-db',
      'type' => 'collection',
      'vname' => 'LBL_INVITEES',
      'links' => 
      array (
        0 => 'contacts',
        1 => 'leads',
        2 => 'users',
      ),
      'order_by' => 'name:asc',
      'studio' => false,
      'hideacl' => true,
    ),
    'auto_invite_parent' => 
    array (
      'name' => 'auto_invite_parent',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Flag to allow for turning off auto invite of parent record -  (Meta-data only)',
      'massupdate' => false,
    ),
    'task_parent' => 
    array (
      'name' => 'task_parent',
      'type' => 'link',
      'relationship' => 'task_meetings_parent',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'contact_parent' => 
    array (
      'name' => 'contact_parent',
      'type' => 'link',
      'relationship' => 'contact_meetings_parent',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'lead_parent' => 
    array (
      'name' => 'lead_parent',
      'type' => 'link',
      'relationship' => 'lead_meetings',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'kbcontents_parent' => 
    array (
      'name' => 'kbcontents_parent',
      'type' => 'link',
      'relationship' => 'kbcontent_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_KBDOCUMENTS',
      'reportable' => false,
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
      'relationship' => 'meetings_following',
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
      'relationship' => 'meetings_favorite',
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
      'relationship' => 'meetings_tags',
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
      'relationship' => 'meetings_commentlog',
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
      'relationship' => 'meetings_locked_fields',
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
      'relationship' => 'meetings_assigned_user',
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
      'relationship' => 'meetings_team',
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
      'relationship' => 'meetings_team_count_relationship',
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
      'relationship' => 'meetings_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'dri_workflow_id' => 
    array (
      'name' => 'dri_workflow_id',
      'vname' => 'LBL_DRI_WORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_workflow_name' => 
    array (
      'name' => 'dri_workflow_name',
      'vname' => 'LBL_DRI_WORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflows',
      'id_name' => 'dri_workflow_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflows',
      'link' => 'dri_workflow_link',
      'processes' => false,
    ),
    'dri_workflow_link' => 
    array (
      'name' => 'dri_workflow_link',
      'vname' => 'LBL_DRI_WORKFLOW',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'meeting_dri_workflows',
      'module' => 'DRI_Workflows',
      'processes' => false,
    ),
    'current_cj_activity_at' => 
    array (
      'name' => 'current_cj_activity_at',
      'vname' => 'LBL_CURRENT_CJ_ACTIVITY_AT',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'meetings_flex_relate_dri_workflows',
      'module' => 'DRI_Workflows',
      'processes' => false,
    ),
    'dri_subworkflow_id' => 
    array (
      'name' => 'dri_subworkflow_id',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_subworkflow_name' => 
    array (
      'name' => 'dri_subworkflow_name',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_subworkflows',
      'id_name' => 'dri_subworkflow_id',
      'sort_on' => 'name',
      'module' => 'DRI_SubWorkflows',
      'link' => 'dri_subworkflow_link',
      'processes' => false,
    ),
    'dri_subworkflow_link' => 
    array (
      'name' => 'dri_subworkflow_link',
      'vname' => 'LBL_DRI_SUBWORKFLOW',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow',
      'relationship' => 'meeting_dri_subworkflows',
      'module' => 'DRI_SubWorkflows',
      'processes' => false,
    ),
    'dri_subworkflow_template_id' => 
    array (
      'name' => 'dri_subworkflow_template_id',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_subworkflow_template_name' => 
    array (
      'name' => 'dri_subworkflow_template_name',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_subworkflow_templates',
      'id_name' => 'dri_subworkflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_SubWorkflow_Templates',
      'link' => 'dri_subworkflow_template_link',
      'processes' => false,
    ),
    'dri_subworkflow_template_link' => 
    array (
      'name' => 'dri_subworkflow_template_link',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow_Template',
      'relationship' => 'meeting_dri_subworkflow_templates',
      'module' => 'DRI_SubWorkflow_Templates',
      'processes' => false,
    ),
    'dri_workflow_template_id' => 
    array (
      'name' => 'dri_workflow_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'dri_workflow_template_name' => 
    array (
      'name' => 'dri_workflow_template_name',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'dri_workflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'link' => 'dri_workflow_template_link',
      'processes' => false,
    ),
    'dri_workflow_template_link' => 
    array (
      'name' => 'dri_workflow_template_link',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'meeting_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
      'processes' => false,
    ),
    'dri_workflow_task_template_id' => 
    array (
      'name' => 'dri_workflow_task_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'cj_activity_tpl_name' => 
    array (
      'name' => 'cj_activity_tpl_name',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'dri_workflow_task_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'link' => 'cj_activity_tpl_link',
      'processes' => false,
    ),
    'cj_activity_tpl_link' => 
    array (
      'name' => 'cj_activity_tpl_link',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'meeting_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
      'processes' => false,
    ),
    'dri_workflow_sort_order' => 
    array (
      'name' => 'dri_workflow_sort_order',
      'vname' => 'LBL_DRI_WORKFLOW_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'default' => 1,
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'cj_actual_sort_order' => 
    array (
      'name' => 'cj_actual_sort_order',
      'vname' => 'LBL_CJ_ACTUAL_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'customer_journey_score' => 
    array (
      'name' => 'customer_journey_score',
      'vname' => 'LBL_CUSTOMER_JOURNEY_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_points' => 
    array (
      'name' => 'cj_momentum_points',
      'vname' => 'LBL_CJ_MOMENTUM_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_score' => 
    array (
      'name' => 'cj_momentum_score',
      'vname' => 'LBL_CJ_MOMENTUM_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_allow_activity_by' => 
    array (
      'name' => 'cj_allow_activity_by',
      'vname' => 'LBL_CJ_ALLOW_ACTIVITY_BY',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'text',
      'studio' => false,
      'processes' => false,
    ),
    'customer_journey_progress' => 
    array (
      'name' => 'customer_journey_progress',
      'vname' => 'LBL_CUSTOMER_JOURNEY_PROGRESS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'processes' => false,
    ),
    'cj_momentum_ratio' => 
    array (
      'name' => 'cj_momentum_ratio',
      'vname' => 'LBL_CJ_MOMENTUM_RATIO',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'processes' => false,
    ),
    'customer_journey_points' => 
    array (
      'name' => 'customer_journey_points',
      'vname' => 'LBL_CUSTOMER_JOURNEY_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_points_list',
      'type' => 'enum',
      'dependency' => 'not(equal($dri_subworkflow_id, ""))',
      'default' => 10,
      'dbType' => 'int',
      'len' => 3,
      'processes' => false,
    ),
    'cj_parent_activity_type' => 
    array (
      'name' => 'cj_parent_activity_type',
      'vname' => 'LBL_CJ_PARENT_ACTIVITY_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_cj_parent_activity_type_list',
      'type' => 'enum',
      'processes' => false,
    ),
    'customer_journey_blocked_by' => 
    array (
      'name' => 'customer_journey_blocked_by',
      'vname' => 'LBL_CUSTOMER_JOURNEY_BLOCKED_BY',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'processes' => false,
    ),
    'cj_blocked_by_stages' => 
    array (
      'name' => 'cj_blocked_by_stages',
      'vname' => 'LBL_CJ_BLOCKED_BY_STAGES',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'processes' => false,
    ),
    'is_cj_parent_activity' => 
    array (
      'name' => 'is_cj_parent_activity',
      'vname' => 'LBL_IS_CJ_PARENT_ACTIVITY',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'processes' => false,
    ),
    'is_customer_journey_activity' => 
    array (
      'name' => 'is_customer_journey_activity',
      'vname' => 'LBL_IS_CUSTOMER_JOURNEY_ACTIVITY',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'enforced' => true,
      'calculated' => true,
      'formula' => 'not(equal($dri_subworkflow_id, ""))',
      'processes' => false,
    ),
    'cj_momentum_start_date' => 
    array (
      'name' => 'cj_momentum_start_date',
      'vname' => 'LBL_CJ_MOMENTUM_START_DATE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_momentum_end_date' => 
    array (
      'name' => 'cj_momentum_end_date',
      'vname' => 'LBL_CJ_MOMENTUM_END_DATE',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'processes' => false,
    ),
    'cj_url' => 
    array (
      'name' => 'cj_url',
      'vname' => 'LBL_CJ_URL',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'url',
      'dbType' => 'varchar',
      'processes' => false,
    ),
    'cj_parent_activity_id' => 
    array (
      'name' => 'cj_parent_activity_id',
      'vname' => 'LBL_CJ_PARENT_ACTIVITY_ID',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'processes' => false,
    ),
    'start_next_journey_id' => 
    array (
      'name' => 'start_next_journey_id',
      'vname' => 'LBL_START_NEXT_JOURNEY_ID',
      'required' => false,
      'reportable' => true,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'parent_meeting_of_call_rel' => 
    array (
      'name' => 'parent_meeting_of_call_rel',
      'type' => 'link',
      'relationship' => 'parent_meeting_of_call_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_MEETING_OF_CALL_REL',
      'reportable' => true,
    ),
    'parent_meeting_of_meeting_rel' => 
    array (
      'name' => 'parent_meeting_of_meeting_rel',
      'type' => 'link',
      'relationship' => 'parent_meeting_of_meeting_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_MEETING_OF_MEETING_REL',
      'reportable' => true,
    ),
    'parent_meeting_of_task_rel' => 
    array (
      'name' => 'parent_meeting_of_task_rel',
      'type' => 'link',
      'relationship' => 'parent_meeting_of_task_rel',
      'source' => 'non-db',
      'vname' => 'LBL_PARENT_MEETING_OF_TASK_REL',
      'reportable' => true,
    ),
  ),
  'relationships' => 
  array (
    'meetings_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'meetings_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'meeting_activities' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
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
      'relationship_role_column_value' => 'Meetings',
    ),
    'meetings_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'meetings_notes' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Meetings',
    ),
    'meetings_messages' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Meetings',
    ),
    'meetings_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Meetings',
      'user_field' => 'created_by',
    ),
    'meetings_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Meetings',
      'user_field' => 'created_by',
    ),
    'meetings_tags' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Meetings',
      'dynamic_subpanel' => true,
    ),
    'meetings_commentlog' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Meetings',
    ),
    'meetings_locked_fields' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Meetings',
    ),
    'meetings_audit' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'meetings_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'meetings_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'meetings_teams' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'meetings_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'meeting_dri_workflows' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'dri_workflow_id',
    ),
    'meetings_flex_relate_dri_workflows' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'relationship_role_column_value' => 'Meetings',
      'relationship_role_column' => 'parent_type',
    ),
    'meeting_dri_subworkflows' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflows',
      'lhs_table' => 'dri_subworkflows',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'dri_subworkflow_id',
    ),
    'meeting_dri_subworkflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflow_Templates',
      'lhs_table' => 'dri_subworkflow_templates',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'dri_subworkflow_template_id',
    ),
    'meeting_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'dri_workflow_template_id',
    ),
    'meeting_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'dri_workflow_task_template_id',
    ),
    'parent_meeting_of_call_rel' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Meetings',
    ),
    'parent_meeting_of_meeting_rel' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Meetings',
    ),
    'parent_meeting_of_task_rel' => 
    array (
      'lhs_module' => 'Meetings',
      'lhs_table' => 'meetings',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'cj_parent_activity_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'cj_parent_activity_type',
      'relationship_role_column_value' => 'Meetings',
    ),
  ),
  'acls' => 
  array (
    'SugarACLOpi' => true,
    'SugarACLStatic' => true,
    'SugarACLLockedFields' => true,
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_meetings_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_meetings_del_d_m',
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
      'name' => 'idx_meetings_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_meetings_del_d_e',
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
      'name' => 'idx_meetings_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_meet_par_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
        1 => 'parent_type',
        2 => 'deleted',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_meet_stat_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'status',
        2 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_meet_date_start_end_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'date_start',
        1 => 'date_end',
        2 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_meet_repeat_parent_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'repeat_parent_id',
        1 => 'deleted',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_meet_date_start_reminder',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'date_start',
        1 => 'reminder_time',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_meetings_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_meetings_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_meetings' => 
    array (
      'name' => 'idx_meetings_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_meetings' => 
    array (
      'name' => 'idx_meetings_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'idx_del_wf_tpl_sub_subtpl_meeting_name' => 
    array (
      'name' => 'idx_del_wf_tpl_sub_subtpl_meeting_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'dri_workflow_id',
        2 => 'dri_workflow_template_id',
        3 => 'dri_subworkflow_id',
        4 => 'dri_subworkflow_template_id',
        5 => 'dri_workflow_task_template_id',
        6 => 'name',
      ),
    ),
    'idx_del_wf_tpl_sub_subtpl_meeting_status' => 
    array (
      'name' => 'idx_del_wf_tpl_sub_subtpl_meeting_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'dri_workflow_id',
        2 => 'dri_workflow_template_id',
        3 => 'dri_subworkflow_id',
        4 => 'dri_subworkflow_template_id',
        5 => 'dri_workflow_task_template_id',
        6 => 'status',
      ),
    ),
    'idx_meeting_cj_stage_id' => 
    array (
      'name' => 'idx_meeting_cj_stage_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_subworkflow_id',
      ),
    ),
    'idx_meeting_cj_stage_tpl_id' => 
    array (
      'name' => 'idx_meeting_cj_stage_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_subworkflow_template_id',
      ),
    ),
    'idx_meeting_cj_journey_tpl_id' => 
    array (
      'name' => 'idx_meeting_cj_journey_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
    'idx_meeting_cj_activity_tpl_id' => 
    array (
      'name' => 'idx_meeting_cj_activity_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_task_template_id',
      ),
    ),
    'idx_meeting_cj_parent_activity' => 
    array (
      'name' => 'idx_meeting_cj_parent_activity',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'cj_parent_activity_id',
        2 => 'cj_parent_activity_type',
      ),
    ),
  ),
  'optimistic_locking' => true,
  'duplicate_check' => 
  array (
    'enabled' => false,
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
  ),
  'favorites' => true,
  'templates' => 
  array (
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
    'customer_journey_activity' => 'customer_journey_activity',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);