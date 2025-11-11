<?php 
 $GLOBALS["dictionary"]["DRI_Workflow_Template"]=array (
  'table' => 'dri_workflow_templates',
  'audited' => false,
  'unified_search' => false,
  'icon' => 'sicon-customer-journey-lg',
  'duplicate_merge' => true,
  'comment' => 'DRI_Workflow_Template',
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
      'relationship' => 'dri_workflow_templates_created_by',
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
      'relationship' => 'dri_workflow_templates_modified_user',
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
      'relationship' => 'dri_workflow_template_activities',
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
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => NULL,
      'type' => 'multienum',
      'isMultiSelect' => true,
      'help' => 'LBL_SMART_GUIDE_ACCESSIBLE',
      'function' => 
      array (
        'name' => 'listTemplateAvailableModulesEnumOptions',
      ),
    ),
    'disabled_stage_actions' => 
    array (
      'name' => 'disabled_stage_actions',
      'vname' => 'LBL_DISABLED_STAGE_ACTIONS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_disabled_stage_actions_list',
      'type' => 'multienum',
      'isMultiSelect' => true,
      'help' => 'LBL_SMART_GUIDE_MODIFY_ACTIONS',
    ),
    'disabled_activity_actions' => 
    array (
      'name' => 'disabled_activity_actions',
      'vname' => 'LBL_DISABLED_ACTIVITY_ACTIONS',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_disabled_activity_actions_list',
      'type' => 'multienum',
      'isMultiSelect' => true,
      'help' => 'LBL_SMART_GUIDE_DISABLE_ACTIONS',
    ),
    'active_limit' => 
    array (
      'name' => 'active_limit',
      'vname' => 'LBL_ACTIVE_LIMIT',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'int',
      'len' => 8,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'help' => 'LBL_SMART_GUIDE_ACTIVATES',
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
    'related_activities' => 
    array (
      'name' => 'related_activities',
      'vname' => 'LBL_RELATED_ACTIVITIES',
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
    'active' => 
    array (
      'name' => 'active',
      'vname' => 'LBL_ACTIVE',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => true,
      'type' => 'bool',
      'default' => true,
    ),
    'update_assignees' => 
    array (
      'name' => 'update_assignees',
      'vname' => 'LBL_UPDATE_ASSIGNEES',
      'required' => false,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => true,
      'type' => 'bool',
      'default' => false,
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
      'options' => 'dri_workflow_templates_assignee_rule_list',
      'type' => 'enum',
      'default' => 'stage_start',
      'help' => 'LBL_SMART_GUIDE_USER_ASSIGNED',
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
      'help' => 'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED',
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
      'help' => 'LBL_SMART_GUIDE_STAGE_NUMBERS',
      'label_right' => 'LBL_CUSTOMER_JOURNEY_STAGE_NUMBER_SHOW',
      'label_left' => 'LBL_CUSTOMER_JOURNEY_STAGE_NUMBER_HIDE',
    ),
    'cancel_action' => 
    array (
      'name' => 'cancel_action',
      'vname' => 'LBL_CANCEL_ACTION',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_cancel_action_list',
      'type' => 'enum',
      'default' => 'set_not_applicable',
    ),
    'not_applicable_action' => 
    array (
      'name' => 'not_applicable_action',
      'vname' => 'LBL_NOT_APPLICABLE_ACTION',
      'required' => true,
      'reportable' => true,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'options' => 'dri_workflow_templates_not_applicable_action_list',
      'type' => 'enum',
      'default' => 'default',
    ),
    'dri_workflows' => 
    array (
      'name' => 'dri_workflows',
      'vname' => 'LBL_DRI_WORKFLOWS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'dri_workflow_dri_workflow_templates',
      'module' => 'DRI_Workflows',
    ),
    'dri_subworkflow_templates' => 
    array (
      'name' => 'dri_subworkflow_templates',
      'vname' => 'LBL_DRI_SUBWORKFLOW_TEMPLATES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_SubWorkflow_Template',
      'relationship' => 'dri_subworkflow_template_dri_workflow_templates',
      'module' => 'DRI_SubWorkflow_Templates',
    ),
    'dri_workflow_task_templates' => 
    array (
      'name' => 'dri_workflow_task_templates',
      'vname' => 'LBL_DRI_WORKFLOW_TASK_TEMPLATES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_dri_workflow_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'copies' => 
    array (
      'name' => 'copies',
      'vname' => 'LBL_COPIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'dri_workflow_template_copied_template_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'vname' => 'LBL_TASKS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Task',
      'relationship' => 'task_dri_workflow_templates',
      'module' => 'Tasks',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'vname' => 'LBL_MEETINGS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Meeting',
      'relationship' => 'meeting_dri_workflow_templates',
      'module' => 'Meetings',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'vname' => 'LBL_CALLS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Call',
      'relationship' => 'call_dri_workflow_templates',
      'module' => 'Calls',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'vname' => 'LBL_ACCOUNTS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Account',
      'relationship' => 'account_dri_workflow_templates',
      'module' => 'Accounts',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'vname' => 'LBL_CONTACTS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Contact',
      'relationship' => 'contact_dri_workflow_templates',
      'module' => 'Contacts',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'vname' => 'LBL_LEADS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Lead',
      'relationship' => 'lead_dri_workflow_templates',
      'module' => 'Leads',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'vname' => 'LBL_CASES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'aCase',
      'relationship' => 'case_dri_workflow_templates',
      'module' => 'Cases',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'vname' => 'LBL_OPPORTUNITIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'Opportunity',
      'relationship' => 'opportunity_dri_workflow_templates',
      'module' => 'Opportunities',
    ),
    'start_next_journey_activities' => 
    array (
      'name' => 'start_next_journey_activities',
      'vname' => 'LBL_START_NEXT_JOURNEY_ACTIVITIES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow_Task_Template',
      'relationship' => 'dri_workflow_task_template_start_next_journey_dri_workflow_templates',
      'module' => 'DRI_Workflow_Task_Templates',
    ),
    'start_next_journey_stages' => 
    array (
      'name' => 'start_next_journey_stages',
      'vname' => 'LBL_START_NEXT_JOURNEY_STAGES',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_SubWorkflow_Template',
      'relationship' => 'dri_subworkflow_template_start_next_journey_dri_workflow_templates',
      'module' => 'DRI_SubWorkflow_Templates',
    ),
    'web_hooks' => 
    array (
      'name' => 'web_hooks',
      'vname' => 'LBL_WEB_HOOKS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'CJ_WebHook',
      'relationship' => 'dri_workflow_templates_flex_relate_cj_web_hooks',
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
      'relationship' => 'dri_workflow_templates_flex_relate_cj_forms',
      'module' => 'CJ_Forms',
    ),
    'copied_template_id' => 
    array (
      'name' => 'copied_template_id',
      'vname' => 'LBL_COPIED_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'type' => 'id',
    ),
    'copied_template_name' => 
    array (
      'name' => 'copied_template_name',
      'vname' => 'LBL_COPIED_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'copied_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'dependency' => 'not(equal($copied_template_name, ""))',
      'link' => 'copied_template_link',
    ),
    'copied_template_link' => 
    array (
      'name' => 'copied_template_link',
      'vname' => 'LBL_COPIED_TEMPLATE',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'dri_workflow_template_copied_template_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
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
      'relationship' => 'dri_workflow_templates_following',
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
      'relationship' => 'dri_workflow_templates_favorite',
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
      'relationship' => 'dri_workflow_templates_tags',
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
      'relationship' => 'dri_workflow_templates_commentlog',
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
      'relationship' => 'dri_workflow_templates_locked_fields',
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
      'relationship' => 'dri_workflow_templates_team',
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
      'relationship' => 'dri_workflow_templates_team_count_relationship',
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
      'relationship' => 'dri_workflow_templates_teams',
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
    'dri_workflow_templates_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_templates_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_template_activities' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
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
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
    ),
    'dri_workflow_templates_flex_relate_cj_web_hooks' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'CJ_WebHooks',
      'rhs_table' => 'cj_web_hooks',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
      'relationship_role_column' => 'parent_type',
    ),
    'dri_workflow_templates_flex_relate_cj_forms' => 
    array (
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'parent_id',
      'rhs_module' => 'CJ_Forms',
      'rhs_table' => 'cj_forms',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
      'relationship_role_column' => 'parent_type',
    ),
    'dri_workflow_template_copied_template_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'copied_template_id',
    ),
    'dri_workflow_templates_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
      'user_field' => 'created_by',
    ),
    'dri_workflow_templates_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
      'user_field' => 'created_by',
    ),
    'dri_workflow_templates_tags' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
      'dynamic_subpanel' => true,
    ),
    'dri_workflow_templates_commentlog' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
    ),
    'dri_workflow_templates_locked_fields' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'DRI_Workflow_Templates',
    ),
    'dri_workflow_templates_audit' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'dri_workflow_templates_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_templates_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'dri_workflow_templates_teams' => 
    array (
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'dri_workflow_templates_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'DRI_Workflow_Templates',
      'rhs_table' => 'dri_workflow_templates',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_dri_workflow_templates_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_dri_workflow_templates_del_d_m',
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
      'name' => 'idx_dri_workflow_templates_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_dri_workflow_templates_del_d_e',
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
      'name' => 'idx_dri_workflow_templates_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'idx_cj_jry_tpl_copied_tpl_id' => 
    array (
      'name' => 'idx_cj_jry_tpl_copied_tpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'copied_template_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_dri_workflow_templates_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_dri_workflow_templates' => 
    array (
      'name' => 'idx_dri_workflow_templates_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_dri_workflow_templates' => 
    array (
      'name' => 'idx_dri_workflow_templates_acl_tmst_id',
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
    'team_security' => 'team_security',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
    0 => 'forms',
  ),
);