<?php 
 $GLOBALS["dictionary"]["DRI_Workflow_Task_Template"]=array (
  'table' => 'dri_workflow_task_templates',
  'audited' => false,
  'unified_search' => false,
  'icon' => 'sicon-customer-journey-lg',
  'duplicate_merge' => true,
  'comment' => 'DRI_Workflow_Task_Template',
  'optimistic_lock' => true,
  'acls' => 
  array (
    'SugarACLDeveloperOrAdmin' => 
    array (
      'adminFor' => 'DRI_Workflow_Templates',
      'allowUserRead' => true,
    ),
    'SugarACLCustomerJourney' => true,
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
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
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 50,
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
        'enabled' => false,
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
      'relationship' => 'dri_workflow_task_templates_created_by',
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
      'relationship' => 'dri_workflow_task_templates_modified_user',
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
      'relationship' => 'dri_workflow_task_template_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'task_start_date_type' => 
    array (
      'name' => 'task_start_date_type',
      'vname' => 'LBL_TASK_START_DATE_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_task_due_date_type_list',
      'type' => 'enum',
      'help' => 'LBL_SMART_GUIDE_START_DATE',
      'visibility_grid' => 
      array (
        'trigger' => 'activity_type',
        'values' => 
        array (
          'Tasks' => 
          array (
            0 => '',
            1 => 'days_from_created',
            2 => 'days_from_stage_started',
            3 => 'days_from_previous_activity_completed',
            4 => 'days_from_parent_date_field',
            5 => 'days_from_specific_activity_completed',
          ),
          'Meetings' => 
          array (
            0 => 'days_from_created',
            1 => 'days_from_stage_started',
            2 => 'days_from_previous_activity_completed',
            3 => 'days_from_parent_date_field',
            4 => 'days_from_specific_activity_completed',
          ),
          'Calls' => 
          array (
            0 => 'days_from_created',
            1 => 'days_from_stage_started',
            2 => 'days_from_previous_activity_completed',
            3 => 'days_from_parent_date_field',
            4 => 'days_from_specific_activity_completed',
          ),
        ),
      ),
    ),
    'task_due_date_type' => 
    array (
      'name' => 'task_due_date_type',
      'vname' => 'LBL_TASK_DUE_DATE_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_task_due_date_type_list',
      'type' => 'enum',
      'help' => 'LBL_SMART_GUIDE_TASK_DUE_DATE_TYPE',
      'visibility_grid' => 
      array (
        'trigger' => 'activity_type',
        'values' => 
        array (
          'Tasks' => 
          array (
            0 => '',
            1 => 'days_from_created',
            2 => 'days_from_stage_started',
            3 => 'days_from_previous_activity_completed',
            4 => 'days_from_parent_date_field',
            5 => 'days_from_specific_activity_completed',
          ),
          'Meetings' => 
          array (
            0 => 'days_from_created',
            1 => 'days_from_stage_started',
            2 => 'days_from_previous_activity_completed',
            3 => 'days_from_parent_date_field',
            4 => 'days_from_specific_activity_completed',
          ),
          'Calls' => 
          array (
            0 => 'days_from_created',
            1 => 'days_from_stage_started',
            2 => 'days_from_previous_activity_completed',
            3 => 'days_from_parent_date_field',
            4 => 'days_from_specific_activity_completed',
          ),
        ),
      ),
    ),
    'momentum_start_type' => 
    array (
      'name' => 'momentum_start_type',
      'vname' => 'LBL_MOMENTUM_START_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_momentum_start_type_list',
      'type' => 'enum',
      'help' => 'LBL_SMART_GUIDE_MOMENTUM_START_TYPE',
    ),
    'start_date_module' => 
    array (
      'name' => 'start_date_module',
      'vname' => 'LBL_START_DATE_MODULE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_available_modules_list',
      'type' => 'enum',
      'dependency' => 'equal($task_start_date_type, "days_from_parent_date_field")',
      'help' => 'LBL_SMART_GUIDE_MODULE_DATE',
    ),
    'due_date_module' => 
    array (
      'name' => 'due_date_module',
      'vname' => 'LBL_DUE_DATE_MODULE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_available_modules_list',
      'type' => 'enum',
      'dependency' => 'equal($task_due_date_type, "days_from_parent_date_field")',
      'help' => 'LBL_SMART_GUIDE_MODULE_DUE_DATE',
    ),
    'start_date_field' => 
    array (
      'name' => 'start_date_field',
      'vname' => 'LBL_START_DATE_FIELD',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_start_date_field_list',
      'type' => 'enum',
      'dependency' => 'equal($task_start_date_type, "days_from_parent_date_field")',
      'help' => 'LBL_SMART_GUIDE_START_DATE_FIELD',
    ),
    'due_date_field' => 
    array (
      'name' => 'due_date_field',
      'vname' => 'LBL_DUE_DATE_FIELD',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_due_date_field_list',
      'type' => 'enum',
      'dependency' => 'equal($task_due_date_type, "days_from_parent_date_field")',
      'help' => 'LBL_SMART_GUIDE_DUE_DATE_FIELD',
    ),
    'due_date_criteria' => 
    array (
      'name' => 'due_date_criteria',
      'vname' => 'LBL_DUE_DATE_CRITERIA',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_due_date_criteria_list',
      'type' => 'enum',
      'default' => 'Absolute',
      'dependency' => 'equal($task_due_date_type, "days_from_parent_date_field")',
      'help' => 'LBL_SMART_GUIDE_DUE_DATE_CRITERIA',
    ),
    'momentum_start_module' => 
    array (
      'name' => 'momentum_start_module',
      'vname' => 'LBL_MOMENTUM_START_MODULE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_available_modules_list',
      'type' => 'enum',
      'help' => 'LBL_SMART_MOMENTUM_START_MODULE',
      'dependency' => 'equal($momentum_start_type, "parent_date_field")',
    ),
    'momentum_start_field' => 
    array (
      'name' => 'momentum_start_field',
      'vname' => 'LBL_MOMENTUM_START_FIELD',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_momentum_start_field_list',
      'type' => 'enum',
      'help' => 'LBL_SMART_GUIDE_MOMENTUM_START_FIELDv',
      'dependency' => 'equal($momentum_start_type, "parent_date_field")',
    ),
    'priority' => 
    array (
      'name' => 'priority',
      'vname' => 'LBL_PRIORITY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'task_priority_dom',
      'type' => 'enum',
      'default' => 'Medium',
      'len' => 100,
      'dependency' => 'equal($activity_type, "Tasks")',
      'help' => 'LBL_SMART_GUIDE_PRIORITY',
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_TYPE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_type_list',
      'type' => 'enum',
      'default' => 'customer_task',
      'dependency' => 'equal($activity_type, "Tasks")',
      'help' => 'LBL_SMART_GUIDE_TYPE',
    ),
    'activity_type' => 
    array (
      'name' => 'activity_type',
      'vname' => 'LBL_ACTIVITY_TYPE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_activity_type_list',
      'type' => 'enum',
      'default' => 'Tasks',
      'help' => 'LBL_SMART_GUIDE_ACTIVITY_TYPE',
    ),
    'duration_minutes' => 
    array (
      'name' => 'duration_minutes',
      'vname' => 'LBL_DURATION_MINUTES',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'duration_intervals',
      'type' => 'enum',
      'len' => 2,
      'default' => 0,
    ),
    'direction' => 
    array (
      'name' => 'direction',
      'vname' => 'LBL_DIRECTION',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'call_direction_dom',
      'type' => 'enum',
      'len' => 100,
      'default' => 'Outbound',
      'dependency' => 'equal($activity_type, "Calls")',
    ),
    'points' => 
    array (
      'name' => 'points',
      'vname' => 'LBL_POINTS',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_points_list',
      'type' => 'enum',
      'default' => 10,
      'dependency' => 'not($is_parent)',
      'dbType' => 'int',
      'len' => 3,
      'help' => 'LBL_SMART_GUIDE_POINTS',
    ),
    'momentum_points' => 
    array (
      'name' => 'momentum_points',
      'vname' => 'LBL_MOMENTUM_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_momentum_points_list',
      'type' => 'enum',
      'default' => 100,
      'dependency' => 'and(not($is_parent), not(equal($momentum_start_type, "")))',
      'dbType' => 'int',
      'len' => 8,
      'help' => 'LBL_SMART_GUIDE_MOMENTUM_POINTS',
    ),
    'send_invite_type' => 
    array (
      'name' => 'send_invite_type',
      'vname' => 'LBL_SEND_INVITES',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_send_invites_list',
      'type' => 'enum',
      'default' => 'none',
      'dependency' => 'or(equal($activity_type, "Meetings"), equal($activity_type, "Calls"))',
    ),
    'target_assignee' => 
    array (
      'name' => 'target_assignee',
      'vname' => 'LBL_TARGET_ASSIGNEE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_target_assignee_list',
      'type' => 'enum',
      'default' => 'inherit',
      'help' => 'LBL_SMART_GUIDE_TARGET_ASSIGNEE',
    ),
    'assignee_rule' => 
    array (
      'name' => 'assignee_rule',
      'vname' => 'LBL_ASSIGNEE_RULE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_task_templates_assignee_rule_list',
      'type' => 'enum',
      'default' => 'inherit',
    ),
    'populate_fields' => 
    array (
      'name' => 'populate_fields',
      'vname' => 'LBL_POPULATE_FIELDS',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'text',
      'studio' => false,
    ),
    'select_to_guests' => 
    array (
      'name' => 'select_to_guests',
      'vname' => 'LBL_SELECT_TO_GUESTS',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'text',
      'studio' => false,
    ),
    'allow_activity_by' => 
    array (
      'name' => 'allow_activity_by',
      'vname' => 'LBL_ALLOW_ACTIVITY_BY',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => false,
      'massupdate' => false,
      'type' => 'text',
      'studio' => false,
    ),
    'time_of_day' => 
    array (
      'name' => 'time_of_day',
      'vname' => 'LBL_TIME_OF_DAY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'dependency' => 'and(or(equal($activity_type, "Meetings"), equal($activity_type, "Calls")), not(equal($task_due_date_type, "")))',
      'default' => '12:00',
    ),
    'sort_order' => 
    array (
      'name' => 'sort_order',
      'vname' => 'LBL_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'varchar',
      'len' => 255,
      'help' => 'LBL_SMART_GUIDE_SORT_ORDER',
    ),
    'task_start_days' => 
    array (
      'name' => 'task_start_days',
      'vname' => 'LBL_TASK_START_DAYS',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'dependency' => 'not(equal($task_start_date_type, ""))',
      'help' => 'LBL_SMART_GUIDE_TASK_START_DAYS',
    ),
    'task_due_days' => 
    array (
      'name' => 'task_due_days',
      'vname' => 'LBL_TASK_DUE_DAYS',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'dependency' => 'not(equal($task_due_date_type, ""))',
      'help' => 'LBL_SMART_GUIDE_TASK_DUE_DAYS',
    ),
    'momentum_due_days' => 
    array (
      'name' => 'momentum_due_days',
      'vname' => 'LBL_MOMENTUM_DUE_DAYS',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'help' => 'LBL_SMART_GUIDE_MOMENTUM_DUE_DAYS',
      'dependency' => 'not(equal($momentum_start_type, ""))',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
      ),
    ),
    'momentum_due_hours' => 
    array (
      'name' => 'momentum_due_hours',
      'vname' => 'LBL_MOMENTUM_DUE_HOURS',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'help' => 'LBL_SMART_GUIDE_MOMENTUM_DUE_HOURS',
      'dependency' => 'not(equal($momentum_start_type, ""))',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
      ),
    ),
    'duration_hours' => 
    array (
      'name' => 'duration_hours',
      'vname' => 'LBL_DURATION_HOURS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 3,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 1,
    ),
    'duration' => 
    array (
      'name' => 'duration',
      'vname' => 'LBL_DURATION',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'source' => 'non-db',
      'dependency' => 'and(or(equal($activity_type, "Meetings"), equal($activity_type, "Calls")), not(equal($task_due_date_type, "")))',
    ),
    'is_parent' => 
    array (
      'name' => 'is_parent',
      'vname' => 'LBL_IS_PARENT',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => false,
      'readonly' => true,
    ),
    'blocked_by' => 
    array (
      'name' => 'blocked_by',
      'vname' => 'LBL_BLOCKED_BY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'help' => 'LBL_SMART_GUIDE_BLOCKED_BY',
    ),
    'blocked_by_stages' => 
    array (
      'name' => 'blocked_by_stages',
      'vname' => 'LBL_BLOCKED_BY_STAGES',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'json',
      'dbType' => 'text',
      'isMultiSelect' => true,
      'help' => 'LBL_SMART_GUIDE_BLOCKED_BY_STAGES',
    ),
    'url' => 
    array (
      'name' => 'url',
      'vname' => 'LBL_URL',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'url',
      'dbType' => 'varchar',
      'help' => 'LBL_SMART_GUIDE_URL',
    ),
    'children' => 
    array (
      'name' => 'children',
      'vname' => 'LBL_CHILDREN',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_parent_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'assignee_rule_activities' => 
    array (
      'name' => 'assignee_rule_activities',
      'vname' => 'LBL_ASSIGNEE_RULE_ACTIVITIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_assignee_rule_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'due_date_activities' => 
    array (
      'name' => 'due_date_activities',
      'vname' => 'LBL_DUE_DATE_ACTIVITIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_due_date_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'momentum_start_activities' => 
    array (
      'name' => 'momentum_start_activities',
      'vname' => 'LBL_MOMENTUM_START_ACTIVITIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_momentum_start_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'stage_template_label' => 
    array (
      'name' => 'stage_template_label',
      'vname' => 'LBL_STAGE_TEMPLATE_LABEL',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'label',
      'table' => 'dri_subworkflow_templates',
      'id_name' => 'dri_subworkflow_template_id',
      'sort_on' => 'stage_template_label',
      'module' => 'DRI_SubWorkflow_Templates',
      'link' => 'dri_subworkflow_template_link',
    ),
    'stage_template_sort_order' => 
    array (
      'name' => 'stage_template_sort_order',
      'vname' => 'LBL_STAGE_TEMPLATE_SORT_ORDER',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'sort_order',
      'table' => 'dri_subworkflow_templates',
      'id_name' => 'dri_subworkflow_template_id',
      'sort_on' => 'stage_template_sort_order',
      'module' => 'DRI_SubWorkflow_Templates',
      'link' => 'dri_subworkflow_template_link',
    ),
    'web_hooks' => 
    array (
      'name' => 'web_hooks',
      'vname' => 'LBL_WEB_HOOKS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'CJ_WebHook',
      'relationship' => 'dri_workflow_task_templates_flex_relate_cj_web_hooks',
      'module' => 'CJ_WebHooks',
    ),
    'forms' => 
    array (
      'name' => 'forms',
      'vname' => 'LBL_FORMS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'CJ_Form',
      'relationship' => 'dri_workflow_task_templates_flex_relate_cj_forms',
      'module' => 'CJ_Forms',
    ),
    'assignee_rule_activity_id' => 
    array (
      'name' => 'assignee_rule_activity_id',
      'vname' => 'LBL_ASSIGNEE_RULE_ACTIVITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'assignee_rule_activity_name' => 
    array (
      'name' => 'assignee_rule_activity_name',
      'vname' => 'LBL_ASSIGNEE_RULE_ACTIVITY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'assignee_rule_activity_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'dependency' => 'equal($assignee_rule, "specific_activity_completed")',
      'link' => 'assignee_rule_activity_link',
    ),
    'assignee_rule_activity_link' => 
    array (
      'name' => 'assignee_rule_activity_link',
      'vname' => 'LBL_ASSIGNEE_RULE_ACTIVITY',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_assignee_rule_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'start_date_activity_id' => 
    array (
      'name' => 'start_date_activity_id',
      'vname' => 'LBL_START_DATE_ACTIVITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'start_date_activity_name' => 
    array (
      'name' => 'start_date_activity_name',
      'vname' => 'LBL_START_DATE_ACTIVITY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'start_date_activity_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'dependency' => 'equal($task_start_date_type, "days_from_specific_activity_completed")',
      'link' => 'start_date_activity_link',
    ),
    'start_date_activity_link' => 
    array (
      'name' => 'start_date_activity_link',
      'vname' => 'LBL_START_DATE_ACTIVITY',
      'source' => 'non-db',
      'type' => 'link',
      'primary_only' => true,
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_start_date_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'due_date_activity_id' => 
    array (
      'name' => 'due_date_activity_id',
      'vname' => 'LBL_DUE_DATE_ACTIVITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'due_date_activity_name' => 
    array (
      'name' => 'due_date_activity_name',
      'vname' => 'LBL_DUE_DATE_ACTIVITY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'due_date_activity_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'dependency' => 'equal($task_due_date_type, "days_from_specific_activity_completed")',
      'link' => 'due_date_activity_link',
    ),
    'due_date_activity_link' => 
    array (
      'name' => 'due_date_activity_link',
      'vname' => 'LBL_DUE_DATE_ACTIVITY',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_due_date_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'momentum_start_activity_id' => 
    array (
      'name' => 'momentum_start_activity_id',
      'vname' => 'LBL_MOMENTUM_START_ACTIVITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'momentum_start_activity_name' => 
    array (
      'name' => 'momentum_start_activity_name',
      'vname' => 'LBL_MOMENTUM_START_ACTIVITY',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'momentum_start_activity_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'dependency' => 'equal($momentum_start_type, "specific_activity_completed")',
      'link' => 'momentum_start_activity_link',
    ),
    'momentum_start_activity_link' => 
    array (
      'name' => 'momentum_start_activity_link',
      'vname' => 'LBL_MOMENTUM_START_ACTIVITY',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_momentum_start_activity_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'vname' => 'LBL_PARENT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_task_templates',
      'id_name' => 'parent_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Task_Templates',
      'readonly' => true,
      'dependency' => 'not(equal($parent_id, ""))',
      'link' => 'parent_link',
    ),
    'parent_link' => 
    array (
      'name' => 'parent_link',
      'vname' => 'LBL_PARENT',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_parent_dri_workflow_task_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'dri_subworkflow_template_id' => 
    array (
      'name' => 'dri_subworkflow_template_id',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'dri_subworkflow_template_name' => 
    array (
      'name' => 'dri_subworkflow_template_name',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'required' => true,
      'reportable' => false,
      'audited' => true,
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
    ),
    'dri_subworkflow_template_link' => 
    array (
      'name' => 'dri_subworkflow_template_link',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow_Template',
      'relationship' => 'dri_workflow_task_template_dri_subworkflow_templates',
      'module' => 'DRI_SubWorkflow_Templates',
    ),
    'dri_workflow_template_id' => 
    array (
      'name' => 'dri_workflow_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'dri_workflow_template_name' => 
    array (
      'name' => 'dri_workflow_template_name',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'dri_workflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'readonly' => true,
      'link' => 'dri_workflow_template_link',
    ),
    'dri_workflow_template_link' => 
    array (
      'name' => 'dri_workflow_template_link',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'dri_workflow_task_template_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'start_next_journey_id' => 
    array (
      'name' => 'start_next_journey_id',
      'vname' => 'LBL_START_NEXT_JOURNEY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'start_next_journey_name' => 
    array (
      'name' => 'start_next_journey_name',
      'vname' => 'LBL_START_NEXT_JOURNEY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'start_next_journey_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'link' => 'start_next_journey_link',
    ),
    'start_next_journey_link' => 
    array (
      'name' => 'start_next_journey_link',
      'vname' => 'LBL_START_NEXT_JOURNEY',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'dri_workflow_task_template_start_next_journey_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'target_assignee_user_id' => 
    array (
      'name' => 'target_assignee_user_id',
      'vname' => 'LBL_TARGET_ASSIGNEE_USER',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'target_assignee_user_name' => 
    array (
      'name' => 'target_assignee_user_name',
      'vname' => 'LBL_TARGET_ASSIGNEE_USER',
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'users',
      'id_name' => 'target_assignee_user_id',
      'sort_on' => 'name',
      'module' => 'Users',
      'dependency' => 'or(equal($target_assignee, "user"), equal($target_assignee, "user_team"))',
      'link' => 'target_assignee_user_link',
    ),
    'target_assignee_user_link' => 
    array (
      'name' => 'target_assignee_user_link',
      'vname' => 'LBL_TARGET_ASSIGNEE_USER',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'User',
      'relationship' => 'dri_workflow_task_template_target_assignee_user_users',
      'module' => 'Users',
    ),
    'target_assignee_team_id' => 
    array (
      'name' => 'target_assignee_team_id',
      'vname' => 'LBL_TARGET_ASSIGNEE_TEAM',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'target_assignee_team_name' => 
    array (
      'name' => 'target_assignee_team_name',
      'vname' => 'LBL_TARGET_ASSIGNEE_TEAM',
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'teams',
      'id_name' => 'target_assignee_team_id',
      'sort_on' => 'name',
      'module' => 'Teams',
      'dependency' => 'or(equal($target_assignee, "team"), equal($target_assignee, "user_team"))',
      'link' => 'target_assignee_team_link',
    ),
    'target_assignee_team_link' => 
    array (
      'name' => 'target_assignee_team_link',
      'vname' => 'LBL_TARGET_ASSIGNEE_TEAM',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Team',
      'relationship' => 'dri_workflow_task_template_target_assignee_team_teams',
      'module' => 'Teams',
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
      'relationship' => 'dri_workflow_task_templates_following',
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
      'relationship' => 'dri_workflow_task_templates_favorite',
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
      'relationship' => 'dri_workflow_task_templates_tags',
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
      'relationship' => 'dri_workflow_task_templates_commentlog',
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
      'relationship' => 'dri_workflow_task_templates_locked_fields',
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
      'relationship' => 'dri_workflow_task_templates_team',
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
      'relationship' => 'dri_workflow_task_templates_team_count_relationship',
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
      'relationship' => 'dri_workflow_task_templates_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
  ),
  'relationships' => 
  array (
    'dri_workflow_task_templates_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_task_templates_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_task_template_activities' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
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
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
    ),
    'dri_workflow_task_templates_flex_relate_cj_forms' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'CJ_Forms',
      'rhs_table' => 'cj_forms',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
      'relationship_role_column' => 'parent_type',
    ),
    'dri_workflow_task_template_parent_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'parent_id',
    ),
    'dri_workflow_task_template_assignee_rule_activity_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'assignee_rule_activity_id',
    ),
    'dri_workflow_task_template_due_date_activity_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'due_date_activity_id',
    ),
    'dri_workflow_task_template_momentum_start_activity_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'momentum_start_activity_id',
    ),
    'dri_workflow_task_templates_flex_relate_cj_web_hooks' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
      'relationship_role_column' => 'parent_type',
    ),
    'dri_workflow_task_template_dri_subworkflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflow_Templates',
      'lhs_table' => 'dri_subworkflow_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'dri_subworkflow_template_id',
    ),
    'dri_workflow_task_template_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'dri_workflow_template_id',
    ),
    'dri_workflow_task_template_start_next_journey_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'start_next_journey_id',
    ),
    'dri_workflow_task_template_target_assignee_user_users' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'target_assignee_user_id',
    ),
    'dri_workflow_task_template_target_assignee_team_teams' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'target_assignee_team_id',
    ),
    'dri_workflow_task_template_start_date_activity_dri_workflow_task_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'start_date_activity_id',
    ),
    'dri_workflow_task_templates_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
      'user_field' => 'created_by',
    ),
    'dri_workflow_task_templates_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
      'user_field' => 'created_by',
    ),
    'dri_workflow_task_templates_tags' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
      'dynamic_subpanel' => true,
    ),
    'dri_workflow_task_templates_commentlog' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
    ),
    'dri_workflow_task_templates_locked_fields' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflow_Task_Templates',
    ),
    'dri_workflow_task_templates_audit' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'dri_workflow_task_templates_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_task_templates_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_task_templates_teams' => 
    array (
      'lhs_module' => 'DRI_Workflow_Task_Templates',
      'lhs_table' => 'dri_workflow_task_templates',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'dri_workflow_task_templates_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Task_Templates',
      'rhs_table' => 'dri_workflow_task_templates',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_del_d_m',
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
      'name' => 'idx_dri_workflow_task_templates_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_del_d_e',
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
      'name' => 'idx_dri_workflow_task_templates_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'idx_cj_act_tpl_stage_tpl_id' => 
    array (
      'name' => 'idx_cj_act_tpl_stage_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_subworkflow_template_id',
      ),
    ),
    'idx_cj_act_tpl_jry_tpl_id' => 
    array (
      'name' => 'idx_cj_act_tpl_jry_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
    'idx_cj_act_tpl_nxt_jry_tpl_id' => 
    array (
      'name' => 'idx_cj_act_tpl_nxt_jry_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'start_next_journey_id',
      ),
    ),
    'idx_cj_act_tpl_trgt_asgn_us_id' => 
    array (
      'name' => 'idx_cj_act_tpl_trgt_asgn_us_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'target_assignee_user_id',
      ),
    ),
    'idx_cj_act_tpl_trgt_asgn_te_id' => 
    array (
      'name' => 'idx_cj_act_tpl_trgt_asgn_te_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'target_assignee_team_id',
      ),
    ),
    'idx_cj_act_tpl_parent_id' => 
    array (
      'name' => 'idx_cj_act_tpl_parent_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
      ),
    ),
    'idx_cj_act_tpl_ass_rl_act_id' => 
    array (
      'name' => 'idx_cj_act_tpl_ass_rl_act_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assignee_rule_activity_id',
      ),
    ),
    'idx_cj_act_tpl_start_dt_act_id' => 
    array (
      'name' => 'idx_cj_act_tpl_start_dt_act_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'start_date_activity_id',
      ),
    ),
    'idx_cj_act_tpl_due_dt_act_id' => 
    array (
      'name' => 'idx_cj_act_tpl_due_dt_act_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'due_date_activity_id',
      ),
    ),
    'idx_cj_act_tpl_mmt_strt_act_id' => 
    array (
      'name' => 'idx_cj_act_tpl_mmt_strt_act_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'momentum_start_activity_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_dri_workflow_task_templates' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_dri_workflow_task_templates' => 
    array (
      'name' => 'idx_dri_workflow_task_templates_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
  ),
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
    'team_security' => 'team_security',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
    0 => 'forms',
  ),
);