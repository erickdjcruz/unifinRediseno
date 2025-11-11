<?php 
 $GLOBALS["dictionary"]["pmse_BpmProcessDefinition"]=array (
  'table' => 'pmse_bpm_process_definition',
  'archive' => false,
  'audited' => false,
  'activity_enabled' => false,
  'duplicate_merge' => true,
  'reassignable' => false,
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
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.5,
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
      'relationship' => 'pmse_bpmprocessdefinition_created_by',
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
      'relationship' => 'pmse_bpmprocessdefinition_modified_user',
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
      'relationship' => 'pmse_bpmprocessdefinition_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'prj_id' => 
    array (
      'required' => true,
      'name' => 'prj_id',
      'vname' => 'Project Identifier',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'len' => '36',
      'size' => '36',
    ),
    'pro_module' => 
    array (
      'required' => true,
      'name' => 'pro_module',
      'vname' => 'The default Module Name for the whole process',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'len' => '255',
      'size' => '255',
    ),
    'pro_status' => 
    array (
      'required' => true,
      'name' => 'pro_status',
      'vname' => 'The process status, can be ACTIVE, INACTIVE',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => '',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'len' => '255',
      'size' => '255',
    ),
    'pro_locked_variables' => 
    array (
      'required' => true,
      'name' => 'pro_locked_variables',
      'vname' => 'array of locked variables, these variables are not able to be modified by SugarCrm Forms',
      'type' => 'text',
      'massupdate' => false,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'size' => '20',
      'rows' => '4',
      'cols' => '20',
    ),
    'pro_terminate_variables' => 
    array (
      'required' => true,
      'name' => 'pro_terminate_variables',
      'vname' => 'array of variables and their values used to halt (terminate) the case',
      'type' => 'text',
      'massupdate' => false,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'size' => '20',
      'rows' => '4',
      'cols' => '20',
    ),
    'execution_mode' => 
    array (
      'required' => true,
      'name' => 'execution_mode',
      'vname' => 'script to be executed',
      'type' => 'varchar',
      'massupdate' => false,
      'default' => 'SYNC',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'len' => '10',
      'size' => '10',
    ),
    'aclrolesets_locked_fields_link' => 
    array (
      'name' => 'aclrolesets_locked_fields_link',
      'vname' => 'ACLRoleSets',
      'type' => 'link',
      'relationship' => 'aclrolesets_locked_fields',
      'source' => 'non-db',
    ),
    'leads_locked_fields_link' => 
    array (
      'name' => 'leads_locked_fields_link',
      'vname' => 'Leads',
      'type' => 'link',
      'relationship' => 'leads_locked_fields',
      'source' => 'non-db',
    ),
    'cases_locked_fields_link' => 
    array (
      'name' => 'cases_locked_fields_link',
      'vname' => 'Cases',
      'type' => 'link',
      'relationship' => 'cases_locked_fields',
      'source' => 'non-db',
    ),
    'bugs_locked_fields_link' => 
    array (
      'name' => 'bugs_locked_fields_link',
      'vname' => 'Bugs',
      'type' => 'link',
      'relationship' => 'bugs_locked_fields',
      'source' => 'non-db',
    ),
    'prospects_locked_fields_link' => 
    array (
      'name' => 'prospects_locked_fields_link',
      'vname' => 'Prospects',
      'type' => 'link',
      'relationship' => 'prospects_locked_fields',
      'source' => 'non-db',
    ),
    'campaigns_locked_fields_link' => 
    array (
      'name' => 'campaigns_locked_fields_link',
      'vname' => 'Campaigns',
      'type' => 'link',
      'relationship' => 'campaigns_locked_fields',
      'source' => 'non-db',
    ),
    'contacts_locked_fields_link' => 
    array (
      'name' => 'contacts_locked_fields_link',
      'vname' => 'Contacts',
      'type' => 'link',
      'relationship' => 'contacts_locked_fields',
      'source' => 'non-db',
    ),
    'accounts_locked_fields_link' => 
    array (
      'name' => 'accounts_locked_fields_link',
      'vname' => 'Accounts',
      'type' => 'link',
      'relationship' => 'accounts_locked_fields',
      'source' => 'non-db',
    ),
    'opportunities_locked_fields_link' => 
    array (
      'name' => 'opportunities_locked_fields_link',
      'vname' => 'Opportunities',
      'type' => 'link',
      'relationship' => 'opportunities_locked_fields',
      'source' => 'non-db',
    ),
    'usersignatures_locked_fields_link' => 
    array (
      'name' => 'usersignatures_locked_fields_link',
      'vname' => 'UserSignatures',
      'type' => 'link',
      'relationship' => 'usersignatures_locked_fields',
      'source' => 'non-db',
    ),
    'notes_locked_fields_link' => 
    array (
      'name' => 'notes_locked_fields_link',
      'vname' => 'Notes',
      'type' => 'link',
      'relationship' => 'notes_locked_fields',
      'source' => 'non-db',
    ),
    'calls_locked_fields_link' => 
    array (
      'name' => 'calls_locked_fields_link',
      'vname' => 'Calls',
      'type' => 'link',
      'relationship' => 'calls_locked_fields',
      'source' => 'non-db',
    ),
    'meetings_locked_fields_link' => 
    array (
      'name' => 'meetings_locked_fields_link',
      'vname' => 'Meetings',
      'type' => 'link',
      'relationship' => 'meetings_locked_fields',
      'source' => 'non-db',
    ),
    'tasks_locked_fields_link' => 
    array (
      'name' => 'tasks_locked_fields_link',
      'vname' => 'Tasks',
      'type' => 'link',
      'relationship' => 'tasks_locked_fields',
      'source' => 'non-db',
    ),
    'documents_locked_fields_link' => 
    array (
      'name' => 'documents_locked_fields_link',
      'vname' => 'Documents',
      'type' => 'link',
      'relationship' => 'documents_locked_fields',
      'source' => 'non-db',
    ),
    'quotes_locked_fields_link' => 
    array (
      'name' => 'quotes_locked_fields_link',
      'vname' => 'Quotes',
      'type' => 'link',
      'relationship' => 'quotes_locked_fields',
      'source' => 'non-db',
    ),
    'revenuelineitems_locked_fields_link' => 
    array (
      'name' => 'revenuelineitems_locked_fields_link',
      'vname' => 'RevenueLineItems',
      'type' => 'link',
      'relationship' => 'revenuelineitems_locked_fields',
      'source' => 'non-db',
      'workflow' => true,
    ),
    'products_locked_fields_link' => 
    array (
      'name' => 'products_locked_fields_link',
      'vname' => 'Products',
      'type' => 'link',
      'relationship' => 'products_locked_fields',
      'source' => 'non-db',
    ),
    'producttemplates_locked_fields_link' => 
    array (
      'name' => 'producttemplates_locked_fields_link',
      'vname' => 'ProductTemplates',
      'type' => 'link',
      'relationship' => 'producttemplates_locked_fields',
      'source' => 'non-db',
    ),
    'contracts_locked_fields_link' => 
    array (
      'name' => 'contracts_locked_fields_link',
      'vname' => 'Contracts',
      'type' => 'link',
      'relationship' => 'contracts_locked_fields',
      'source' => 'non-db',
    ),
    'docusignenvelopes_locked_fields_link' => 
    array (
      'name' => 'docusignenvelopes_locked_fields_link',
      'vname' => 'DocuSignEnvelopes',
      'type' => 'link',
      'relationship' => 'docusignenvelopes_locked_fields',
      'source' => 'non-db',
    ),
    'businesscenters_locked_fields_link' => 
    array (
      'name' => 'businesscenters_locked_fields_link',
      'vname' => 'BusinessCenters',
      'type' => 'link',
      'relationship' => 'businesscenters_locked_fields',
      'source' => 'non-db',
    ),
    'shifts_locked_fields_link' => 
    array (
      'name' => 'shifts_locked_fields_link',
      'vname' => 'Shifts',
      'type' => 'link',
      'relationship' => 'shifts_locked_fields',
      'source' => 'non-db',
    ),
    'shiftexceptions_locked_fields_link' => 
    array (
      'name' => 'shiftexceptions_locked_fields_link',
      'vname' => 'ShiftExceptions',
      'type' => 'link',
      'relationship' => 'shiftexceptions_locked_fields',
      'source' => 'non-db',
    ),
    'messages_locked_fields_link' => 
    array (
      'name' => 'messages_locked_fields_link',
      'vname' => 'Messages',
      'type' => 'link',
      'relationship' => 'messages_locked_fields',
      'source' => 'non-db',
    ),
    'purchases_locked_fields_link' => 
    array (
      'name' => 'purchases_locked_fields_link',
      'vname' => 'Purchases',
      'type' => 'link',
      'relationship' => 'purchases_locked_fields',
      'source' => 'non-db',
    ),
    'purchasedlineitems_locked_fields_link' => 
    array (
      'name' => 'purchasedlineitems_locked_fields_link',
      'vname' => 'PurchasedLineItems',
      'type' => 'link',
      'relationship' => 'purchasedlineitems_locked_fields',
      'source' => 'non-db',
    ),
    'pushnotifications_locked_fields_link' => 
    array (
      'name' => 'pushnotifications_locked_fields_link',
      'vname' => 'PushNotifications',
      'type' => 'link',
      'relationship' => 'pushnotifications_locked_fields',
      'source' => 'non-db',
    ),
    'dataprivacy_locked_fields_link' => 
    array (
      'name' => 'dataprivacy_locked_fields_link',
      'vname' => 'DataPrivacy',
      'type' => 'link',
      'relationship' => 'dataprivacy_locked_fields',
      'source' => 'non-db',
    ),
    'calendar_locked_fields_link' => 
    array (
      'name' => 'calendar_locked_fields_link',
      'vname' => 'Calendar',
      'type' => 'link',
      'relationship' => 'calendar_locked_fields',
      'source' => 'non-db',
    ),
    'dashboards_locked_fields_link' => 
    array (
      'name' => 'dashboards_locked_fields_link',
      'vname' => 'Dashboards',
      'type' => 'link',
      'relationship' => 'dashboards_locked_fields',
      'source' => 'non-db',
    ),
    'tags_locked_fields_link' => 
    array (
      'name' => 'tags_locked_fields_link',
      'vname' => 'Tags',
      'type' => 'link',
      'relationship' => 'tags_locked_fields',
      'source' => 'non-db',
    ),
    'kbdocuments_locked_fields_link' => 
    array (
      'name' => 'kbdocuments_locked_fields_link',
      'vname' => 'KBDocuments',
      'type' => 'link',
      'relationship' => 'kbdocuments_locked_fields',
      'source' => 'non-db',
    ),
    'kbcontents_locked_fields_link' => 
    array (
      'name' => 'kbcontents_locked_fields_link',
      'vname' => 'KBContents',
      'type' => 'link',
      'relationship' => 'kbcontents_locked_fields',
      'source' => 'non-db',
    ),
    'escalations_locked_fields_link' => 
    array (
      'name' => 'escalations_locked_fields_link',
      'vname' => 'Escalations',
      'type' => 'link',
      'relationship' => 'escalations_locked_fields',
      'source' => 'non-db',
    ),
    'hintenrichfieldconfigs_locked_fields_link' => 
    array (
      'name' => 'hintenrichfieldconfigs_locked_fields_link',
      'vname' => 'HintEnrichFieldConfigs',
      'type' => 'link',
      'relationship' => 'hintenrichfieldconfigs_locked_fields',
      'source' => 'non-db',
    ),
    'hintaccountsets_locked_fields_link' => 
    array (
      'name' => 'hintaccountsets_locked_fields_link',
      'vname' => 'HintAccountsets',
      'type' => 'link',
      'relationship' => 'hintaccountsets_locked_fields',
      'source' => 'non-db',
    ),
    'hintnewsnotifications_locked_fields_link' => 
    array (
      'name' => 'hintnewsnotifications_locked_fields_link',
      'vname' => 'HintNewsNotifications',
      'type' => 'link',
      'relationship' => 'hintnewsnotifications_locked_fields',
      'source' => 'non-db',
    ),
    'hintnotificationtargets_locked_fields_link' => 
    array (
      'name' => 'hintnotificationtargets_locked_fields_link',
      'vname' => 'HintNotificationTargets',
      'type' => 'link',
      'relationship' => 'hintnotificationtargets_locked_fields',
      'source' => 'non-db',
    ),
    'documenttemplates_locked_fields_link' => 
    array (
      'name' => 'documenttemplates_locked_fields_link',
      'vname' => 'DocumentTemplates',
      'type' => 'link',
      'relationship' => 'documenttemplates_locked_fields',
      'source' => 'non-db',
    ),
    'clouddrivepaths_locked_fields_link' => 
    array (
      'name' => 'clouddrivepaths_locked_fields_link',
      'vname' => 'CloudDrivePaths',
      'type' => 'link',
      'relationship' => 'clouddrivepaths_locked_fields',
      'source' => 'non-db',
    ),
    'dri_workflows_locked_fields_link' => 
    array (
      'name' => 'dri_workflows_locked_fields_link',
      'vname' => 'DRI_Workflows',
      'type' => 'link',
      'relationship' => 'dri_workflows_locked_fields',
      'source' => 'non-db',
    ),
    'dri_workflow_task_templates_locked_fields_link' => 
    array (
      'name' => 'dri_workflow_task_templates_locked_fields_link',
      'vname' => 'DRI_Workflow_Task_Templates',
      'type' => 'link',
      'relationship' => 'dri_workflow_task_templates_locked_fields',
      'source' => 'non-db',
    ),
    'dri_subworkflows_locked_fields_link' => 
    array (
      'name' => 'dri_subworkflows_locked_fields_link',
      'vname' => 'DRI_SubWorkflows',
      'type' => 'link',
      'relationship' => 'dri_subworkflows_locked_fields',
      'source' => 'non-db',
    ),
    'dri_workflow_templates_locked_fields_link' => 
    array (
      'name' => 'dri_workflow_templates_locked_fields_link',
      'vname' => 'DRI_Workflow_Templates',
      'type' => 'link',
      'relationship' => 'dri_workflow_templates_locked_fields',
      'source' => 'non-db',
    ),
    'cj_webhooks_locked_fields_link' => 
    array (
      'name' => 'cj_webhooks_locked_fields_link',
      'vname' => 'CJ_WebHooks',
      'type' => 'link',
      'relationship' => 'cj_webhooks_locked_fields',
      'source' => 'non-db',
    ),
    'cj_forms_locked_fields_link' => 
    array (
      'name' => 'cj_forms_locked_fields_link',
      'vname' => 'CJ_Forms',
      'type' => 'link',
      'relationship' => 'cj_forms_locked_fields',
      'source' => 'non-db',
    ),
    'dri_subworkflow_templates_locked_fields_link' => 
    array (
      'name' => 'dri_subworkflow_templates_locked_fields_link',
      'vname' => 'DRI_SubWorkflow_Templates',
      'type' => 'link',
      'relationship' => 'dri_subworkflow_templates_locked_fields',
      'source' => 'non-db',
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
      'relationship' => 'pmse_bpmprocessdefinition_following',
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
      'relationship' => 'pmse_bpmprocessdefinition_favorite',
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
      'relationship' => 'pmse_bpmprocessdefinition_assigned_user',
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
  ),
  'optimistic_locking' => true,
  'unified_search' => true,
  'relationships' => 
  array (
    'pmse_bpmprocessdefinition_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'pmse_bpmprocessdefinition_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'pmse_bpmprocessdefinition_activities' => 
    array (
      'lhs_module' => 'pmse_BpmProcessDefinition',
      'lhs_table' => 'pmse_bpm_process_definition',
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
      'relationship_role_column_value' => 'pmse_BpmProcessDefinition',
    ),
    'pmse_bpmprocessdefinition_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'pmse_BpmProcessDefinition',
      'user_field' => 'created_by',
    ),
    'pmse_bpmprocessdefinition_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'pmse_BpmProcessDefinition',
      'user_field' => 'created_by',
    ),
    'pmse_bpmprocessdefinition_audit' => 
    array (
      'lhs_module' => 'pmse_BpmProcessDefinition',
      'lhs_table' => 'pmse_bpm_process_definition',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'pmse_bpm_process_definition_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'pmse_bpmprocessdefinition_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_pmse_bpmprocessdefinition_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_pmse_bpm_process_definition_del_d_m',
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
      'name' => 'idx_pmse_bpm_process_definition_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_pmse_bpm_process_definition_del_d_e',
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
      'name' => 'idx_pmse_bpm_process_definition_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'prj_id' => 
    array (
      'name' => 'idx_pd_prj_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'prj_id',
      ),
    ),
    'pro_status' => 
    array (
      'name' => 'idx_pd_pro_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'pro_status',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_pmse_bpm_process_definition_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_pmse_bpm_process_definition_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
  ),
  'load_fields' => 
  array (
    'class' => 'LockedFieldsRelatedModulesUtilities',
    'method' => 'getRelatedFields',
  ),
  'portal_visibility' => 
  array (
    'class' => 'PMSE',
  ),
  'ignore_templates' => 
  array (
    0 => 'taggable',
    1 => 'lockable_fields',
    2 => 'commentlog',
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
  ),
  'acls' => 
  array (
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
          'name' => 
          array (
            '$starts' => '$name',
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
      ),
    ),
  ),
  'favorites' => true,
  'templates' => 
  array (
    'basic' => 'basic',
    'following' => 'following',
    'favorite' => 'favorite',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
    'assignable' => 'assignable',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);