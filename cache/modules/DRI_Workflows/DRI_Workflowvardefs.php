<?php 
 $GLOBALS["dictionary"]["DRI_Workflow"]=array (
  'table' => 'dri_workflows',
  'audited' => true,
  'unified_search' => false,
  'icon' => 'sicon-customer-journey-lg',
  'duplicate_merge' => true,
  'comment' => 'DRI_Workflow',
  'optimistic_lock' => true,
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
      'readonly' => true,
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
      'readonly' => true,
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
      'relationship' => 'dri_workflows_created_by',
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
      'relationship' => 'dri_workflows_modified_user',
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
      'relationship' => 'dri_workflow_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'available_modules' => 
    array (
      'name' => 'available_modules',
      'vname' => 'LBL_AVAILABLE_MODULES',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_available_modules_list',
      'type' => 'multienum',
      'isMultiSelect' => true,
    ),
    'enabled_modules' => 
    array (
      'name' => 'enabled_modules',
      'vname' => 'LBL_ENABLED_MODULES',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => NULL,
      'type' => 'multienum',
      'isMultiSelect' => true,
      'source' => 'non-db',
      'function' => 
      array (
        'name' => 'listAutomateEnabledModulesEnumOptions',
      ),
    ),
    'state' => 
    array (
      'name' => 'state',
      'vname' => 'LBL_STATE',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflows_state_list',
      'dbType' => 'varchar',
      'type' => 'enum',
      'default' => 'not_started',
      'readonly' => true,
      'related_fields' => 
      array (
        0 => 'archived',
      ),
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
      'options' => 'dri_workflow_templates_assignee_rule_list',
      'type' => 'enum',
      'default' => 'stage_start',
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
      'options' => 'dri_workflow_templates_target_assignee_list',
      'type' => 'enum',
      'default' => 'current_user',
    ),
    'stage_numbering' => 
    array (
      'name' => 'stage_numbering',
      'vname' => 'LBL_STAGE_NUMBERS',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'importable' => 'true',
      'massupdate' => true,
      'type' => 'toggle',
      'dbType' => 'bool',
      'default' => '0',
      'studio' => true,
      'label_right' => 'LBL_CUSTOMER_JOURNEY_STAGE_NUMBER_SHOW',
      'label_left' => 'LBL_CUSTOMER_JOURNEY_STAGE_NUMBER_HIDE',
    ),
    'progress' => 
    array (
      'name' => 'progress',
      'vname' => 'LBL_PROGRESS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
        'max' => 100,
      ),
    ),
    'momentum_ratio' => 
    array (
      'name' => 'momentum_ratio',
      'vname' => 'LBL_MOMENTUM_RATIO',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'float',
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 1,
      'readonly' => true,
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
        'max' => 100,
      ),
    ),
    'score' => 
    array (
      'name' => 'score',
      'vname' => 'LBL_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'disable_num_format' => true,
    ),
    'points' => 
    array (
      'name' => 'points',
      'vname' => 'LBL_POINTS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'readonly' => true,
      'disable_num_format' => true,
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
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'disable_num_format' => true,
    ),
    'momentum_score' => 
    array (
      'name' => 'momentum_score',
      'vname' => 'LBL_MOMENTUM_SCORE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'default' => 0,
      'readonly' => true,
      'disable_num_format' => true,
    ),
    'date_started' => 
    array (
      'name' => 'date_started',
      'vname' => 'LBL_DATE_STARTED',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'readonly' => true,
    ),
    'date_completed' => 
    array (
      'name' => 'date_completed',
      'vname' => 'LBL_DATE_COMPLETED',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'datetime',
      'readonly' => true,
    ),
    'archived' => 
    array (
      'name' => 'archived',
      'vname' => 'LBL_ARCHIVED',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => '0',
    ),
    'is_cancelled' => 
    array (
      'name' => 'is_cancelled',
      'vname' => 'LBL_IS_CANCELLED',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => '0',
    ),
    'is_deleted' => 
    array (
      'name' => 'is_deleted',
      'vname' => 'LBL_IS_DELETED',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'bool',
      'default' => '0',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'vname' => 'LBL_TASKS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Task',
      'relationship' => 'task_dri_workflows',
      'module' => 'Tasks',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'vname' => 'LBL_CALLS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Call',
      'relationship' => 'call_dri_workflows',
      'module' => 'Calls',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'vname' => 'LBL_MEETINGS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Meeting',
      'relationship' => 'meeting_dri_workflows',
      'module' => 'Meetings',
    ),
    'dri_subworkflows' => 
    array (
      'name' => 'dri_subworkflows',
      'vname' => 'LBL_DRI_SUBWORKFLOWS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_SubWorkflow',
      'relationship' => 'dri_subworkflow_dri_workflows',
      'module' => 'DRI_SubWorkflows',
    ),
    'current_stage_id' => 
    array (
      'name' => 'current_stage_id',
      'vname' => 'LBL_CURRENT_STAGE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'current_stage_name' => 
    array (
      'name' => 'current_stage_name',
      'vname' => 'LBL_CURRENT_STAGE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_subworkflows',
      'id_name' => 'current_stage_id',
      'sort_on' => 'name',
      'module' => 'DRI_SubWorkflows',
      'readonly' => true,
      'link' => 'current_stage_link',
    ),
    'current_stage_link' => 
    array (
      'name' => 'current_stage_link',
      'vname' => 'LBL_CURRENT_STAGE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_SubWorkflow',
      'relationship' => 'dri_workflow_current_stage_dri_subworkflows',
      'module' => 'DRI_SubWorkflows',
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
      'relationship' => 'dri_workflow_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
      'readonly' => true,
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'vname' => 'LBL_PARENT_NAME',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'parent',
      'len' => 255,
      'source' => 'non-db',
      'options' => 'dri_workflows_parent_type_list',
      'parent_type' => 'dri_workflows_parent_type_list',
      'readonly' => true,
      'id_name' => 'parent_id',
      'type_name' => 'parent_type',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'parent_type',
      'len' => 255,
      'dbType' => 'varchar',
      'options' => 'dri_workflows_parent_type_list',
      'parent_type' => 'dri_workflows_parent_type_list',
      'readonly' => true,
    ),
    'current_activity_task' => 
    array (
      'name' => 'current_activity_task',
      'vname' => 'LBL_CURRENT_ACTIVITY_TASK',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Task',
      'relationship' => 'tasks_flex_relate_dri_workflows',
      'module' => 'Tasks',
    ),
    'current_activity_call' => 
    array (
      'name' => 'current_activity_call',
      'vname' => 'LBL_CURRENT_ACTIVITY_CALL',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Call',
      'relationship' => 'calls_flex_relate_dri_workflows',
      'module' => 'Calls',
    ),
    'current_activity_meeting' => 
    array (
      'name' => 'current_activity_meeting',
      'vname' => 'LBL_CURRENT_ACTIVITY_MEETING',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Meeting',
      'relationship' => 'meetings_flex_relate_dri_workflows',
      'module' => 'Meetings',
    ),
    'assigned_user_picture' => 
    array (
      'name' => 'assigned_user_picture',
      'link' => 'assigned_user_link',
      'vname' => 'LBL_ASSIGNED_TO',
      'rname' => 'picture',
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
      'relationship' => 'dri_workflows_following',
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
      'relationship' => 'dri_workflows_favorite',
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
      'relationship' => 'dri_workflows_tags',
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
      'relationship' => 'dri_workflows_commentlog',
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
      'relationship' => 'dri_workflows_locked_fields',
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
      'relationship' => 'dri_workflows_assigned_user',
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
      'relationship' => 'dri_workflows_team',
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
      'relationship' => 'dri_workflows_team_count_relationship',
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
      'relationship' => 'dri_workflows_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'vname' => 'LBL_ACCOUNT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'vname' => 'LBL_ACCOUNT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'accounts',
      'id_name' => 'account_id',
      'sort_on' => 'name',
      'module' => 'Accounts',
      'link' => 'account_link',
      'customer_journey_parent' => 
      array (
        'enabled' => true,
        'rank' => 10,
      ),
    ),
    'account_link' => 
    array (
      'name' => 'account_link',
      'vname' => 'LBL_ACCOUNT',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Account',
      'relationship' => 'dri_workflow_accounts',
      'module' => 'Accounts',
    ),
    'contact_id' => 
    array (
      'name' => 'contact_id',
      'vname' => 'LBL_CONTACT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'contact_name' => 
    array (
      'name' => 'contact_name',
      'vname' => 'LBL_CONTACT',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'contacts',
      'id_name' => 'contact_id',
      'sort_on' => 'name',
      'module' => 'Contacts',
      'link' => 'contact_link',
      'customer_journey_parent' => 
      array (
        'enabled' => true,
        'rank' => 10,
      ),
    ),
    'contact_link' => 
    array (
      'name' => 'contact_link',
      'vname' => 'LBL_CONTACT',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Contact',
      'relationship' => 'dri_workflow_contacts',
      'module' => 'Contacts',
    ),
    'lead_id' => 
    array (
      'name' => 'lead_id',
      'vname' => 'LBL_LEAD',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'lead_name' => 
    array (
      'name' => 'lead_name',
      'vname' => 'LBL_LEAD',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'leads',
      'id_name' => 'lead_id',
      'sort_on' => 'name',
      'module' => 'Leads',
      'link' => 'lead_link',
      'customer_journey_parent' => 
      array (
        'enabled' => true,
        'rank' => 10,
      ),
    ),
    'lead_link' => 
    array (
      'name' => 'lead_link',
      'vname' => 'LBL_LEAD',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Lead',
      'relationship' => 'dri_workflow_leads',
      'module' => 'Leads',
    ),
    'opportunity_id' => 
    array (
      'name' => 'opportunity_id',
      'vname' => 'LBL_OPPORTUNITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'opportunity_name' => 
    array (
      'name' => 'opportunity_name',
      'vname' => 'LBL_OPPORTUNITY',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'opportunities',
      'id_name' => 'opportunity_id',
      'sort_on' => 'name',
      'module' => 'Opportunities',
      'link' => 'opportunity_link',
      'customer_journey_parent' => 
      array (
        'enabled' => true,
        'rank' => 10,
      ),
    ),
    'opportunity_link' => 
    array (
      'name' => 'opportunity_link',
      'vname' => 'LBL_OPPORTUNITY',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Opportunity',
      'relationship' => 'dri_workflow_opportunities',
      'module' => 'Opportunities',
    ),
    'case_id' => 
    array (
      'name' => 'case_id',
      'vname' => 'LBL_CASE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'case_name' => 
    array (
      'name' => 'case_name',
      'vname' => 'LBL_CASE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'cases',
      'id_name' => 'case_id',
      'sort_on' => 'name',
      'module' => 'Cases',
      'link' => 'case_link',
      'customer_journey_parent' => 
      array (
        'enabled' => true,
        'rank' => 10,
      ),
    ),
    'case_link' => 
    array (
      'name' => 'case_link',
      'vname' => 'LBL_CASE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'Case',
      'relationship' => 'dri_workflow_cases',
      'module' => 'Cases',
    ),
  ),
  'relationships' => 
  array (
    'dri_workflows_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflows_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_activities' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
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
      'relationship_role_column_value' => 'DRI_Workflows',
    ),
    'dri_workflow_current_stage_dri_subworkflows' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_SubWorkflows',
      'lhs_table' => 'dri_subworkflows',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'current_stage_id',
    ),
    'dri_workflow_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'dri_workflow_template_id',
    ),
    'dri_workflows_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'DRI_Workflows',
      'user_field' => 'created_by',
    ),
    'dri_workflows_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflows',
      'user_field' => 'created_by',
    ),
    'dri_workflows_tags' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflows',
      'dynamic_subpanel' => true,
    ),
    'dri_workflows_commentlog' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflows',
    ),
    'dri_workflows_locked_fields' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflows',
    ),
    'dri_workflows_audit' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'dri_workflows_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflows_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflows_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflows_teams' => 
    array (
      'lhs_module' => 'DRI_Workflows',
      'lhs_table' => 'dri_workflows',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'dri_workflows_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_accounts' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Accounts',
      'lhs_table' => 'accounts',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'account_id',
    ),
    'dri_workflow_contacts' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'contact_id',
    ),
    'dri_workflow_leads' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Leads',
      'lhs_table' => 'leads',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'lead_id',
    ),
    'dri_workflow_opportunities' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'opportunity_id',
    ),
    'dri_workflow_cases' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'Cases',
      'lhs_table' => 'cases',
      'rhs_module' => 'DRI_Workflows',
      'rhs_table' => 'dri_workflows',
      'rhs_key' => 'case_id',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_dri_workflows_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_dri_workflows_del_d_m',
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
      'name' => 'idx_dri_workflows_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_dri_workflows_del_d_e',
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
      'name' => 'idx_dri_workflows_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'idx_cj_jry_current_stage_id' => 
    array (
      'name' => 'idx_cj_jry_current_stage_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'current_stage_id',
      ),
    ),
    'idx_cj_journey_tpl_id' => 
    array (
      'name' => 'idx_cj_journey_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
    'idx_cj_jry_parent_id' => 
    array (
      'name' => 'idx_cj_jry_parent_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_dri_workflows_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_dri_workflows_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_dri_workflows' => 
    array (
      'name' => 'idx_dri_workflows_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_dri_workflows' => 
    array (
      'name' => 'idx_dri_workflows_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'idx_cj_jry_account_id' => 
    array (
      'name' => 'idx_cj_jry_account_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'account_id',
      ),
    ),
    'idx_cj_jry_contact_id' => 
    array (
      'name' => 'idx_cj_jry_contact_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'contact_id',
      ),
    ),
    'idx_cj_jry_lead_id' => 
    array (
      'name' => 'idx_cj_jry_lead_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'lead_id',
      ),
    ),
    'idx_cj_jry_opportunity_id' => 
    array (
      'name' => 'idx_cj_jry_opportunity_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'opportunity_id',
      ),
    ),
    'idx_cj_jry_case_id' => 
    array (
      'name' => 'idx_cj_jry_case_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'case_id',
      ),
    ),
  ),
  'duplicate_check' => 
  array (
    'enabled' => false,
  ),
  'acls' => 
  array (
    'SugarACLCustomerJourney' => true,
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
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
    'customer_journey_enabled_modules' => 'customer_journey_enabled_modules',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);