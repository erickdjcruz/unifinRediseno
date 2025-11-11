<?php 
 $GLOBALS["dictionary"]["CommentLog"]=array (
  'table' => 'commentlog',
  'audited' => false,
  'comment' => 'CommentLog entries that are related to a record',
  'duplicate_merge' => false,
  'unified_search' => true,
  'full_text_search' => false,
  'unified_search_default_enabled' => true,
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
      'relationship' => 'commentlog_created_by',
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
      'relationship' => 'commentlog_modified_user',
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
      'relationship' => 'commentlog_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'entry' => 
    array (
      'name' => 'entry',
      'vname' => 'LBL_ENTRY',
      'type' => 'text',
    ),
    'aclrolesets_link' => 
    array (
      'name' => 'aclrolesets_link',
      'vname' => 'ACLRoleSets',
      'type' => 'link',
      'relationship' => 'aclrolesets_commentlog',
      'source' => 'non-db',
    ),
    'leads_link' => 
    array (
      'name' => 'leads_link',
      'vname' => 'Leads',
      'type' => 'link',
      'relationship' => 'leads_commentlog',
      'source' => 'non-db',
    ),
    'cases_link' => 
    array (
      'name' => 'cases_link',
      'vname' => 'Cases',
      'type' => 'link',
      'relationship' => 'cases_commentlog',
      'source' => 'non-db',
    ),
    'bugs_link' => 
    array (
      'name' => 'bugs_link',
      'vname' => 'Bugs',
      'type' => 'link',
      'relationship' => 'bugs_commentlog',
      'source' => 'non-db',
    ),
    'prospectlists_link' => 
    array (
      'name' => 'prospectlists_link',
      'vname' => 'ProspectLists',
      'type' => 'link',
      'relationship' => 'prospectlists_commentlog',
      'source' => 'non-db',
    ),
    'prospects_link' => 
    array (
      'name' => 'prospects_link',
      'vname' => 'Prospects',
      'type' => 'link',
      'relationship' => 'prospects_commentlog',
      'source' => 'non-db',
    ),
    'campaigns_link' => 
    array (
      'name' => 'campaigns_link',
      'vname' => 'Campaigns',
      'type' => 'link',
      'relationship' => 'campaigns_commentlog',
      'source' => 'non-db',
    ),
    'schedulers_link' => 
    array (
      'name' => 'schedulers_link',
      'vname' => 'Schedulers',
      'type' => 'link',
      'relationship' => 'schedulers_commentlog',
      'source' => 'non-db',
    ),
    'contacts_link' => 
    array (
      'name' => 'contacts_link',
      'vname' => 'Contacts',
      'type' => 'link',
      'relationship' => 'contacts_commentlog',
      'source' => 'non-db',
    ),
    'accounts_link' => 
    array (
      'name' => 'accounts_link',
      'vname' => 'Accounts',
      'type' => 'link',
      'relationship' => 'accounts_commentlog',
      'source' => 'non-db',
    ),
    'opportunities_link' => 
    array (
      'name' => 'opportunities_link',
      'vname' => 'Opportunities',
      'type' => 'link',
      'relationship' => 'opportunities_commentlog',
      'source' => 'non-db',
    ),
    'usersignatures_link' => 
    array (
      'name' => 'usersignatures_link',
      'vname' => 'UserSignatures',
      'type' => 'link',
      'relationship' => 'usersignatures_commentlog',
      'source' => 'non-db',
    ),
    'notes_link' => 
    array (
      'name' => 'notes_link',
      'vname' => 'Notes',
      'type' => 'link',
      'relationship' => 'notes_commentlog',
      'source' => 'non-db',
    ),
    'calls_link' => 
    array (
      'name' => 'calls_link',
      'vname' => 'Calls',
      'type' => 'link',
      'relationship' => 'calls_commentlog',
      'source' => 'non-db',
    ),
    'meetings_link' => 
    array (
      'name' => 'meetings_link',
      'vname' => 'Meetings',
      'type' => 'link',
      'relationship' => 'meetings_commentlog',
      'source' => 'non-db',
    ),
    'tasks_link' => 
    array (
      'name' => 'tasks_link',
      'vname' => 'Tasks',
      'type' => 'link',
      'relationship' => 'tasks_commentlog',
      'source' => 'non-db',
    ),
    'documents_link' => 
    array (
      'name' => 'documents_link',
      'vname' => 'Documents',
      'type' => 'link',
      'relationship' => 'documents_commentlog',
      'source' => 'non-db',
    ),
    'teams_link' => 
    array (
      'name' => 'teams_link',
      'vname' => 'Teams',
      'type' => 'link',
      'relationship' => 'teams_commentlog',
      'source' => 'non-db',
    ),
    'quotes_link' => 
    array (
      'name' => 'quotes_link',
      'vname' => 'Quotes',
      'type' => 'link',
      'relationship' => 'quotes_commentlog',
      'source' => 'non-db',
    ),
    'revenuelineitems_link' => 
    array (
      'name' => 'revenuelineitems_link',
      'vname' => 'RevenueLineItems',
      'type' => 'link',
      'relationship' => 'revenuelineitems_commentlog',
      'source' => 'non-db',
      'workflow' => true,
    ),
    'products_link' => 
    array (
      'name' => 'products_link',
      'vname' => 'Products',
      'type' => 'link',
      'relationship' => 'products_commentlog',
      'source' => 'non-db',
    ),
    'producttemplates_link' => 
    array (
      'name' => 'producttemplates_link',
      'vname' => 'ProductTemplates',
      'type' => 'link',
      'relationship' => 'producttemplates_commentlog',
      'source' => 'non-db',
    ),
    'producttypes_link' => 
    array (
      'name' => 'producttypes_link',
      'vname' => 'ProductTypes',
      'type' => 'link',
      'relationship' => 'producttypes_commentlog',
      'source' => 'non-db',
    ),
    'manufacturers_link' => 
    array (
      'name' => 'manufacturers_link',
      'vname' => 'Manufacturers',
      'type' => 'link',
      'relationship' => 'manufacturers_commentlog',
      'source' => 'non-db',
    ),
    'shippers_link' => 
    array (
      'name' => 'shippers_link',
      'vname' => 'Shippers',
      'type' => 'link',
      'relationship' => 'shippers_commentlog',
      'source' => 'non-db',
    ),
    'taxrates_link' => 
    array (
      'name' => 'taxrates_link',
      'vname' => 'TaxRates',
      'type' => 'link',
      'relationship' => 'taxrates_commentlog',
      'source' => 'non-db',
    ),
    'quotas_link' => 
    array (
      'name' => 'quotas_link',
      'vname' => 'Quotas',
      'type' => 'link',
      'relationship' => 'quotas_commentlog',
      'source' => 'non-db',
    ),
    'contracts_link' => 
    array (
      'name' => 'contracts_link',
      'vname' => 'Contracts',
      'type' => 'link',
      'relationship' => 'contracts_commentlog',
      'source' => 'non-db',
    ),
    'contracttypes_link' => 
    array (
      'name' => 'contracttypes_link',
      'vname' => 'ContractTypes',
      'type' => 'link',
      'relationship' => 'contracttypes_commentlog',
      'source' => 'non-db',
    ),
    'docusignenvelopes_link' => 
    array (
      'name' => 'docusignenvelopes_link',
      'vname' => 'DocuSignEnvelopes',
      'type' => 'link',
      'relationship' => 'docusignenvelopes_commentlog',
      'source' => 'non-db',
    ),
    'pmse_project_link' => 
    array (
      'name' => 'pmse_project_link',
      'vname' => 'pmse_Project',
      'type' => 'link',
      'relationship' => 'pmse_project_commentlog',
      'source' => 'non-db',
    ),
    'pmse_business_rules_link' => 
    array (
      'name' => 'pmse_business_rules_link',
      'vname' => 'pmse_Business_Rules',
      'type' => 'link',
      'relationship' => 'pmse_business_rules_commentlog',
      'source' => 'non-db',
    ),
    'pmse_emails_templates_link' => 
    array (
      'name' => 'pmse_emails_templates_link',
      'vname' => 'pmse_Emails_Templates',
      'type' => 'link',
      'relationship' => 'pmse_emails_templates_commentlog',
      'source' => 'non-db',
    ),
    'businesscenters_link' => 
    array (
      'name' => 'businesscenters_link',
      'vname' => 'BusinessCenters',
      'type' => 'link',
      'relationship' => 'businesscenters_commentlog',
      'source' => 'non-db',
    ),
    'shifts_link' => 
    array (
      'name' => 'shifts_link',
      'vname' => 'Shifts',
      'type' => 'link',
      'relationship' => 'shifts_commentlog',
      'source' => 'non-db',
    ),
    'shiftexceptions_link' => 
    array (
      'name' => 'shiftexceptions_link',
      'vname' => 'ShiftExceptions',
      'type' => 'link',
      'relationship' => 'shiftexceptions_commentlog',
      'source' => 'non-db',
    ),
    'messages_link' => 
    array (
      'name' => 'messages_link',
      'vname' => 'Messages',
      'type' => 'link',
      'relationship' => 'messages_commentlog',
      'source' => 'non-db',
    ),
    'purchases_link' => 
    array (
      'name' => 'purchases_link',
      'vname' => 'Purchases',
      'type' => 'link',
      'relationship' => 'purchases_commentlog',
      'source' => 'non-db',
    ),
    'purchasedlineitems_link' => 
    array (
      'name' => 'purchasedlineitems_link',
      'vname' => 'PurchasedLineItems',
      'type' => 'link',
      'relationship' => 'purchasedlineitems_commentlog',
      'source' => 'non-db',
    ),
    'externalusers_link' => 
    array (
      'name' => 'externalusers_link',
      'vname' => 'ExternalUsers',
      'type' => 'link',
      'relationship' => 'externalusers_commentlog',
      'source' => 'non-db',
    ),
    'dataprivacy_link' => 
    array (
      'name' => 'dataprivacy_link',
      'vname' => 'DataPrivacy',
      'type' => 'link',
      'relationship' => 'dataprivacy_commentlog',
      'source' => 'non-db',
    ),
    'calendar_link' => 
    array (
      'name' => 'calendar_link',
      'vname' => 'Calendar',
      'type' => 'link',
      'relationship' => 'calendar_commentlog',
      'source' => 'non-db',
    ),
    'eapm_link' => 
    array (
      'name' => 'eapm_link',
      'vname' => 'EAPM',
      'type' => 'link',
      'relationship' => 'eapm_commentlog',
      'source' => 'non-db',
    ),
    'oauthkeys_link' => 
    array (
      'name' => 'oauthkeys_link',
      'vname' => 'OAuthKeys',
      'type' => 'link',
      'relationship' => 'oauthkeys_commentlog',
      'source' => 'non-db',
    ),
    'sugarfavorites_link' => 
    array (
      'name' => 'sugarfavorites_link',
      'vname' => 'SugarFavorites',
      'type' => 'link',
      'relationship' => 'sugarfavorites_commentlog',
      'source' => 'non-db',
    ),
    'pubsub_moduleevent_pushsubs_link' => 
    array (
      'name' => 'pubsub_moduleevent_pushsubs_link',
      'vname' => 'PubSub_ModuleEvent_PushSubs',
      'type' => 'link',
      'relationship' => 'pubsub_moduleevent_pushsubs_commentlog',
      'source' => 'non-db',
    ),
    'pdfmanager_link' => 
    array (
      'name' => 'pdfmanager_link',
      'vname' => 'PdfManager',
      'type' => 'link',
      'relationship' => 'pdfmanager_commentlog',
      'source' => 'non-db',
    ),
    'kbcontents_link' => 
    array (
      'name' => 'kbcontents_link',
      'vname' => 'KBContents',
      'type' => 'link',
      'relationship' => 'kbcontents_commentlog',
      'source' => 'non-db',
    ),
    'visualpipeline_link' => 
    array (
      'name' => 'visualpipeline_link',
      'vname' => 'VisualPipeline',
      'type' => 'link',
      'relationship' => 'visualpipeline_commentlog',
      'source' => 'non-db',
    ),
    'consoleconfiguration_link' => 
    array (
      'name' => 'consoleconfiguration_link',
      'vname' => 'ConsoleConfiguration',
      'type' => 'link',
      'relationship' => 'consoleconfiguration_commentlog',
      'source' => 'non-db',
    ),
    'sugarlive_link' => 
    array (
      'name' => 'sugarlive_link',
      'vname' => 'SugarLive',
      'type' => 'link',
      'relationship' => 'sugarlive_commentlog',
      'source' => 'non-db',
    ),
    'escalations_link' => 
    array (
      'name' => 'escalations_link',
      'vname' => 'Escalations',
      'type' => 'link',
      'relationship' => 'escalations_commentlog',
      'source' => 'non-db',
    ),
    'dataarchiver_link' => 
    array (
      'name' => 'dataarchiver_link',
      'vname' => 'DataArchiver',
      'type' => 'link',
      'relationship' => 'dataarchiver_commentlog',
      'source' => 'non-db',
    ),
    'archiveruns_link' => 
    array (
      'name' => 'archiveruns_link',
      'vname' => 'ArchiveRuns',
      'type' => 'link',
      'relationship' => 'archiveruns_commentlog',
      'source' => 'non-db',
    ),
    'hintenrichfieldconfigs_link' => 
    array (
      'name' => 'hintenrichfieldconfigs_link',
      'vname' => 'HintEnrichFieldConfigs',
      'type' => 'link',
      'relationship' => 'hintenrichfieldconfigs_commentlog',
      'source' => 'non-db',
    ),
    'hintaccountsets_link' => 
    array (
      'name' => 'hintaccountsets_link',
      'vname' => 'HintAccountsets',
      'type' => 'link',
      'relationship' => 'hintaccountsets_commentlog',
      'source' => 'non-db',
    ),
    'hintnewsnotifications_link' => 
    array (
      'name' => 'hintnewsnotifications_link',
      'vname' => 'HintNewsNotifications',
      'type' => 'link',
      'relationship' => 'hintnewsnotifications_commentlog',
      'source' => 'non-db',
    ),
    'hintnotificationtargets_link' => 
    array (
      'name' => 'hintnotificationtargets_link',
      'vname' => 'HintNotificationTargets',
      'type' => 'link',
      'relationship' => 'hintnotificationtargets_commentlog',
      'source' => 'non-db',
    ),
    'documenttemplates_link' => 
    array (
      'name' => 'documenttemplates_link',
      'vname' => 'DocumentTemplates',
      'type' => 'link',
      'relationship' => 'documenttemplates_commentlog',
      'source' => 'non-db',
    ),
    'documentmerges_link' => 
    array (
      'name' => 'documentmerges_link',
      'vname' => 'DocumentMerges',
      'type' => 'link',
      'relationship' => 'documentmerges_commentlog',
      'source' => 'non-db',
    ),
    'clouddrivepaths_link' => 
    array (
      'name' => 'clouddrivepaths_link',
      'vname' => 'CloudDrivePaths',
      'type' => 'link',
      'relationship' => 'clouddrivepaths_commentlog',
      'source' => 'non-db',
    ),
    'dri_workflows_link' => 
    array (
      'name' => 'dri_workflows_link',
      'vname' => 'DRI_Workflows',
      'type' => 'link',
      'relationship' => 'dri_workflows_commentlog',
      'source' => 'non-db',
    ),
    'dri_workflow_task_templates_link' => 
    array (
      'name' => 'dri_workflow_task_templates_link',
      'vname' => 'DRI_Workflow_Task_Templates',
      'type' => 'link',
      'relationship' => 'dri_workflow_task_templates_commentlog',
      'source' => 'non-db',
    ),
    'dri_subworkflows_link' => 
    array (
      'name' => 'dri_subworkflows_link',
      'vname' => 'DRI_SubWorkflows',
      'type' => 'link',
      'relationship' => 'dri_subworkflows_commentlog',
      'source' => 'non-db',
    ),
    'dri_workflow_templates_link' => 
    array (
      'name' => 'dri_workflow_templates_link',
      'vname' => 'DRI_Workflow_Templates',
      'type' => 'link',
      'relationship' => 'dri_workflow_templates_commentlog',
      'source' => 'non-db',
    ),
    'cj_webhooks_link' => 
    array (
      'name' => 'cj_webhooks_link',
      'vname' => 'CJ_WebHooks',
      'type' => 'link',
      'relationship' => 'cj_webhooks_commentlog',
      'source' => 'non-db',
    ),
    'cj_forms_link' => 
    array (
      'name' => 'cj_forms_link',
      'vname' => 'CJ_Forms',
      'type' => 'link',
      'relationship' => 'cj_forms_commentlog',
      'source' => 'non-db',
    ),
    'dri_subworkflow_templates_link' => 
    array (
      'name' => 'dri_subworkflow_templates_link',
      'vname' => 'DRI_SubWorkflow_Templates',
      'type' => 'link',
      'relationship' => 'dri_subworkflow_templates_commentlog',
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
      'relationship' => 'commentlog_following',
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
      'relationship' => 'commentlog_favorite',
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
      'relationship' => 'commentlog_tags',
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
      'relationship' => 'commentlog_locked_fields',
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
  ),
  'ignore_templates' => 
  array (
    0 => 'commentlog',
  ),
  'load_fields' => 
  array (
    'class' => 'CommentLogRelatedModulesUtilities',
    'method' => 'getRelatedFields',
  ),
  'relationships' => 
  array (
    'commentlog_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'commentlog_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'commentlog_activities' => 
    array (
      'lhs_module' => 'CommentLog',
      'lhs_table' => 'commentlog',
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
      'relationship_role_column_value' => 'CommentLog',
    ),
    'commentlog_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'CommentLog',
      'user_field' => 'created_by',
    ),
    'commentlog_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'CommentLog',
      'user_field' => 'created_by',
    ),
    'commentlog_tags' => 
    array (
      'lhs_module' => 'CommentLog',
      'lhs_table' => 'commentlog',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'CommentLog',
      'dynamic_subpanel' => true,
    ),
    'commentlog_locked_fields' => 
    array (
      'lhs_module' => 'CommentLog',
      'lhs_table' => 'commentlog',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'CommentLog',
    ),
    'commentlog_audit' => 
    array (
      'lhs_module' => 'CommentLog',
      'lhs_table' => 'commentlog',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'commentlog_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_commentlog_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_commentlog_del_d_m',
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
      'name' => 'idx_commentlog_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_commentlog_del_d_e',
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
      'name' => 'idx_commentlog_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_commentlog_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
  ),
  'acls' => 
  array (
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
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
    'taggable' => 'taggable',
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);