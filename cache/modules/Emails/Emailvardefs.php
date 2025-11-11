<?php 
 $GLOBALS["dictionary"]["Email"]=array (
  'favorites' => true,
  'table' => 'emails',
  'color' => 'ocean',
  'icon' => 'sicon-email-lg',
  'acls' => 
  array (
    'SugarACLEmails' => true,
    'SugarACLDraftEmails' => true,
    'SugarACLArchivedEmails' => true,
    'SugarACLStatic' => true,
  ),
  'full_text_search' => true,
  'activity_enabled' => true,
  'comment' => 'Contains a record of emails sent to and from the Sugar application',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => true,
      'comment' => 'Unique identifier',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date record created',
      'readonly' => true,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
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
      'comment' => 'Date record last modified',
      'readonly' => true,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'id',
      'isnull' => false,
      'reportable' => false,
      'comment' => 'User ID that last modified record',
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
      'id_name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'link' => 'assigned_user_link',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'module' => 'Users',
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED_BY',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'reportable' => true,
      'dbType' => 'id',
      'comment' => 'User ID that last modified record',
      'readonly' => true,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
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
      'vname' => 'LBL_MODIFIED_NAME',
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
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'vname' => 'LBL_CREATED_BY',
      'type' => 'id',
      'reportable' => false,
      'comment' => 'User name who created record',
      'readonly' => true,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
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
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => false,
      'reportable' => false,
      'comment' => 'Record deletion indicator',
    ),
    'from_addr_name' => 
    array (
      'name' => 'from_addr_name',
      'type' => 'varchar',
      'vname' => 'LBL_FROM',
      'source' => 'non-db',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
      'massupdate' => false,
    ),
    'reply_to_addr' => 
    array (
      'name' => 'reply_to_addr',
      'type' => 'varchar',
      'vname' => 'reply_to_addr',
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'to_addrs_names' => 
    array (
      'name' => 'to_addrs_names',
      'type' => 'varchar',
      'vname' => 'LBL_TO_ADDRS',
      'source' => 'non-db',
      'reportable' => false,
      'massupdate' => false,
    ),
    'cc_addrs_names' => 
    array (
      'name' => 'cc_addrs_names',
      'type' => 'varchar',
      'vname' => 'LBL_CC',
      'source' => 'non-db',
      'reportable' => false,
      'massupdate' => false,
    ),
    'bcc_addrs_names' => 
    array (
      'name' => 'bcc_addrs_names',
      'type' => 'varchar',
      'vname' => 'LBL_BCC',
      'source' => 'non-db',
      'reportable' => false,
      'massupdate' => false,
    ),
    'raw_source' => 
    array (
      'name' => 'raw_source',
      'type' => 'varchar',
      'vname' => 'raw_source',
      'source' => 'non-db',
      'massupdate' => false,
    ),
    'description_html' => 
    array (
      'name' => 'description_html',
      'type' => 'htmleditable_tinymce',
      'vname' => 'description_html',
      'source' => 'non-db',
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
        'type' => 'text',
      ),
    ),
    'description' => 
    array (
      'name' => 'description',
      'type' => 'varchar',
      'vname' => 'LBL_TEXT_BODY',
      'source' => 'non-db',
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
        'type' => 'text',
      ),
      'massupdate' => false,
    ),
    'date_sent' => 
    array (
      'name' => 'date_sent',
      'vname' => 'LBL_DATE_SENT',
      'type' => 'datetime',
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
      'hideacl' => true,
    ),
    'message_id' => 
    array (
      'name' => 'message_id',
      'vname' => 'LBL_MESSAGE_ID',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'ID of the email item obtained from the email transport system',
      'hideacl' => true,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
    ),
    'message_uid' => 
    array (
      'name' => 'message_uid',
      'vname' => 'LBL_MESSAGE_UID',
      'type' => 'varchar',
      'len' => 64,
      'comment' => 'UID of the email item obtained from the email transport system',
      'hideacl' => true,
      'massupdate' => false,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_SUBJECT',
      'type' => 'name',
      'dbType' => 'varchar',
      'required' => false,
      'len' => '255',
      'comment' => 'The subject of the email',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
      ),
      'hideacl' => true,
      'massupdate' => false,
    ),
    'type' => 
    array (
      'name' => 'type',
      'vname' => 'LBL_LIST_TYPE',
      'type' => 'enum',
      'options' => 'dom_email_types',
      'len' => 100,
      'massupdate' => false,
      'comment' => 'Type of email (ex: draft)',
      'hideacl' => true,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'len' => 100,
      'options' => 'dom_email_status',
      'massupdate' => false,
      'hideacl' => true,
    ),
    'flagged' => 
    array (
      'name' => 'flagged',
      'vname' => 'LBL_EMAIL_FLAGGED',
      'type' => 'bool',
      'required' => false,
      'reportable' => false,
      'comment' => 'flagged status',
      'massupdate' => false,
    ),
    'reply_to_status' => 
    array (
      'name' => 'reply_to_status',
      'vname' => 'LBL_EMAIL_REPLY_TO_STATUS',
      'type' => 'bool',
      'required' => false,
      'reportable' => false,
      'comment' => 'If you reply to an email then reply to status of original email is set',
      'massupdate' => false,
      'hideacl' => true,
      'duplicate_on_record_copy' => 'no',
    ),
    'intent' => 
    array (
      'name' => 'intent',
      'vname' => 'LBL_INTENT',
      'type' => 'varchar',
      'len' => 100,
      'default' => 'pick',
      'comment' => 'Target of action used in Inbound Email assignment',
      'hideacl' => true,
    ),
    'mailbox_id' => 
    array (
      'name' => 'mailbox_id',
      'vname' => 'LBL_MAILBOX_ID',
      'type' => 'id',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
    ),
    'mailbox_name' => 
    array (
      'name' => 'mailbox_name',
      'rname' => 'name',
      'type' => 'relate',
      'source' => 'non-db',
      'vname' => 'LBL_MAILBOX',
      'reportable' => false,
      'id_name' => 'mailbox_id',
      'link' => 'mailbox',
      'module' => 'InboundEmail',
      'readonly' => true,
      'studio' => false,
    ),
    'mailbox' => 
    array (
      'name' => 'mailbox',
      'type' => 'link',
      'relationship' => 'inbound_email_emails',
      'source' => 'non-db',
      'vname' => 'LBL_MAILBOX',
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'emails_created_by',
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
      'relationship' => 'emails_modified_user',
      'vname' => 'LBL_MODIFIED_BY_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'emails_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'state' => 
    array (
      'name' => 'state',
      'vname' => 'LBL_EMAIL_STATE',
      'type' => 'enum',
      'options' => 'dom_email_states',
      'len' => 100,
      'required' => true,
      'isnull' => false,
      'default' => 'Archived',
      'massupdate' => false,
      'comment' => 'An email is either a draft or archived',
      'reportable' => false,
      'hideacl' => true,
      'mandatory_fetch' => true,
    ),
    'reply_to_id' => 
    array (
      'name' => 'reply_to_id',
      'vname' => 'LBL_EMAIL_REPLY_TO_ID',
      'type' => 'id',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'importable' => false,
      'comment' => 'Identifier of email record that this email was a reply to',
      'massupdate' => false,
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
      'reportable' => false,
      'source' => 'non-db',
      'options' => 'parent_type_display',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
      'type' => 'parent_type',
      'dbType' => 'varchar',
      'group' => 'parent_name',
      'options' => 'parent_type_display',
      'reportable' => false,
      'comment' => 'Identifier of Sugar module to which this email is associated',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'type' => 'id',
      'group' => 'parent_name',
      'reportable' => false,
      'comment' => 'ID of Sugar object referenced by parent_type',
    ),
    'direction' => 
    array (
      'name' => 'direction',
      'vname' => 'LBL_EMAIL_DIRECTION',
      'type' => 'enum',
      'options' => 'dom_email_direction',
      'len' => 20,
      'required' => true,
      'isnull' => false,
      'default' => 'Unknown',
      'massupdate' => false,
      'comment' => 'Email direction is one of Unknown, Outbound, Inbound, Internal',
      'reportable' => true,
      'mandatory_fetch' => true,
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'vname' => 'LBL_EMAILS_ACCOUNTS_REL',
      'type' => 'link',
      'relationship' => 'emails_accounts_rel',
      'module' => 'Accounts',
      'bean_name' => 'Account',
      'source' => 'non-db',
    ),
    'bugs' => 
    array (
      'name' => 'bugs',
      'vname' => 'LBL_EMAILS_BUGS_REL',
      'type' => 'link',
      'relationship' => 'emails_bugs_rel',
      'module' => 'Bugs',
      'bean_name' => 'Bug',
      'source' => 'non-db',
    ),
    'cases' => 
    array (
      'name' => 'cases',
      'vname' => 'LBL_EMAILS_CASES_REL',
      'type' => 'link',
      'relationship' => 'emails_cases_rel',
      'module' => 'Cases',
      'bean_name' => 'Case',
      'source' => 'non-db',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'vname' => 'LBL_EMAILS_CONTACTS_REL',
      'type' => 'link',
      'relationship' => 'emails_contacts_rel',
      'module' => 'Contacts',
      'bean_name' => 'Contact',
      'source' => 'non-db',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'vname' => 'LBL_EMAILS_ESCALATIONS_REL',
      'type' => 'link',
      'relationship' => 'emails_escalations_rel',
      'module' => 'Escalations',
      'bean_name' => 'Escalation',
      'source' => 'non-db',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'vname' => 'LBL_EMAILS_LEADS_REL',
      'type' => 'link',
      'relationship' => 'emails_leads_rel',
      'module' => 'Leads',
      'bean_name' => 'Lead',
      'source' => 'non-db',
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'vname' => 'LBL_EMAILS_OPPORTUNITIES_REL',
      'type' => 'link',
      'relationship' => 'emails_opportunities_rel',
      'module' => 'Opportunities',
      'bean_name' => 'Opportunity',
      'source' => 'non-db',
    ),
    'purchases' => 
    array (
      'name' => 'purchases',
      'vname' => 'LBL_EMAILS_PURCHASES_REL',
      'type' => 'link',
      'relationship' => 'emails_purchases_rel',
      'module' => 'Purchases',
      'bean_name' => 'Purchase',
      'source' => 'non-db',
    ),
    'purchasedlineitems' => 
    array (
      'name' => 'purchasedlineitems',
      'vname' => 'LBL_EMAILS_PURCHASEDLINEITEMS_REL',
      'type' => 'link',
      'relationship' => 'emails_purchasedlineitems_rel',
      'module' => 'Emails',
      'bean_name' => 'Email',
      'source' => 'non-db',
    ),
    'project' => 
    array (
      'name' => 'project',
      'vname' => 'LBL_EMAILS_PROJECT_REL',
      'type' => 'link',
      'relationship' => 'emails_projects_rel',
      'module' => 'Project',
      'bean_name' => 'Project',
      'source' => 'non-db',
    ),
    'projecttask' => 
    array (
      'name' => 'projecttask',
      'vname' => 'LBL_EMAILS_PROJECT_TASK_REL',
      'type' => 'link',
      'relationship' => 'emails_project_task_rel',
      'module' => 'ProjectTask',
      'bean_name' => 'ProjectTask',
      'source' => 'non-db',
    ),
    'prospects' => 
    array (
      'name' => 'prospects',
      'vname' => 'LBL_EMAILS_PROSPECT_REL',
      'type' => 'link',
      'relationship' => 'emails_prospects_rel',
      'module' => 'Prospects',
      'bean_name' => 'Prospect',
      'source' => 'non-db',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'vname' => 'LBL_EMAILS_QUOTES_REL',
      'type' => 'link',
      'relationship' => 'emails_quotes',
      'module' => 'Quotes',
      'bean_name' => 'Quote',
      'source' => 'non-db',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'vname' => 'LBL_EMAILS_REVENUELINEITEMS_REL',
      'type' => 'link',
      'relationship' => 'emails_revenuelineitems_rel',
      'module' => 'RevenueLineItems',
      'bean_name' => 'RevenueLineItem',
      'source' => 'non-db',
      'workflow' => true,
    ),
    'products' => 
    array (
      'name' => 'products',
      'vname' => 'LBL_EMAILS_PRODUCTS_REL',
      'type' => 'link',
      'relationship' => 'emails_products_rel',
      'module' => 'Products',
      'bean_name' => 'Product',
      'source' => 'non-db',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'vname' => 'LBL_EMAILS_TASKS_REL',
      'type' => 'link',
      'relationship' => 'emails_tasks_rel',
      'module' => 'Tasks',
      'bean_name' => 'Task',
      'source' => 'non-db',
    ),
    'users' => 
    array (
      'name' => 'users',
      'vname' => 'LBL_EMAILS_USERS_REL',
      'type' => 'link',
      'relationship' => 'emails_users_rel',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'vname' => 'LBL_EMAILS_NOTES_REL',
      'type' => 'link',
      'relationship' => 'emails_notes_rel',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'vname' => 'LBL_EMAILS_MESSAGES_REL',
      'type' => 'link',
      'relationship' => 'emails_messages_rel',
      'module' => 'Messages',
      'bean_name' => 'Message',
      'source' => 'non-db',
    ),
    'attachments' => 
    array (
      'bean_name' => 'Note',
      'module' => 'Notes',
      'name' => 'attachments',
      'relationship' => 'emails_attachments',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_ATTACHMENTS',
      'reportable' => false,
      'readonly' => true,
    ),
    'attachments_collection' => 
    array (
      'name' => 'attachments_collection',
      'links' => 
      array (
        0 => 'attachments',
      ),
      'order_by' => 'name:asc',
      'source' => 'non-db',
      'studio' => false,
      'type' => 'collection',
      'vname' => 'LBL_ATTACHMENTS',
      'reportable' => false,
      'hideacl' => true,
    ),
    'total_attachments' => 
    array (
      'name' => 'total_attachments',
      'vname' => 'LBL_TOTAL_ATTACHMENTS',
      'type' => 'int',
      'formula' => 'count($attachments)',
      'calculated' => true,
      'enforced' => true,
      'studio' => false,
      'workflow' => false,
      'importable' => false,
      'reportable' => false,
      'massupdate' => false,
      'hideacl' => true,
    ),
    'outbound_email_id' => 
    array (
      'name' => 'outbound_email_id',
      'comment' => 'The configuration used to send an email, only used for emails sent using SugarCRM',
      'type' => 'enum',
      'dbType' => 'id',
      'required' => false,
      'vname' => 'LBL_OUTBOUND_EMAIL_ID',
      'function' => 'getOutboundEmailDropdown',
      'function_bean' => 'Emails',
      'reportable' => false,
      'massupdate' => false,
    ),
    'from_collection' => 
    array (
      'name' => 'from_collection',
      'links' => 
      array (
        0 => 'from',
      ),
      'order_by' => 'parent_name:asc',
      'source' => 'non-db',
      'studio' => false,
      'type' => 'collection',
      'vname' => 'LBL_FROM',
      'reportable' => false,
      'hideacl' => true,
      'displayParams' => 
      array (
        'fields' => 
        array (
          0 => 'email_address_id',
          1 => 'email_address',
          2 => 'parent_type',
          3 => 'parent_id',
          4 => 'parent_name',
          5 => 'invalid_email',
          6 => 'opt_out',
        ),
      ),
    ),
    'to_collection' => 
    array (
      'name' => 'to_collection',
      'links' => 
      array (
        0 => 'to',
      ),
      'order_by' => 'parent_name:asc',
      'source' => 'non-db',
      'studio' => false,
      'type' => 'collection',
      'vname' => 'LBL_TO_ADDRS',
      'reportable' => false,
      'hideacl' => true,
    ),
    'cc_collection' => 
    array (
      'name' => 'cc_collection',
      'links' => 
      array (
        0 => 'cc',
      ),
      'order_by' => 'parent_name:asc',
      'source' => 'non-db',
      'studio' => false,
      'type' => 'collection',
      'vname' => 'LBL_CC',
      'reportable' => false,
      'hideacl' => true,
    ),
    'bcc_collection' => 
    array (
      'name' => 'bcc_collection',
      'links' => 
      array (
        0 => 'bcc',
      ),
      'order_by' => 'parent_name:asc',
      'source' => 'non-db',
      'studio' => false,
      'type' => 'collection',
      'vname' => 'LBL_BCC',
      'reportable' => false,
      'hideacl' => true,
    ),
    'from' => 
    array (
      'name' => 'from',
      'relationship' => 'emails_from',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_FROM',
      'reportable' => false,
      'readonly' => true,
    ),
    'to' => 
    array (
      'name' => 'to',
      'relationship' => 'emails_to',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_TO_ADDRS',
      'reportable' => false,
      'readonly' => true,
    ),
    'cc' => 
    array (
      'name' => 'cc',
      'relationship' => 'emails_cc',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_CC',
      'reportable' => false,
      'readonly' => true,
    ),
    'bcc' => 
    array (
      'name' => 'bcc',
      'relationship' => 'emails_bcc',
      'source' => 'non-db',
      'type' => 'link',
      'vname' => 'LBL_BCC',
      'reportable' => false,
      'readonly' => true,
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
      'relationship' => 'emails_favorite',
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
      'relationship' => 'emails_following',
      'source' => 'non-db',
      'vname' => 'LBL_FOLLOWING',
      'reportable' => false,
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
      'relationship' => 'emails_tags',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
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
      'relationship' => 'emails_team',
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
      'relationship' => 'emails_team_count_relationship',
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
      'relationship' => 'emails_teams',
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
    'emails_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'emails_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'emails_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'emails_attachments' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'email_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'EmailAttachmentRelationship',
      'relationship_file' => 'modules/Emails/EmailAttachmentRelationship.php',
      'relationship_role_column' => 'email_type',
      'relationship_role_column_value' => 'Emails',
    ),
    'emails_notes_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Notes',
    ),
    'emails_messages_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Messages',
    ),
    'emails_revenuelineitems_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'RevenueLineItems',
      'rhs_table' => 'revenue_line_items',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'RevenueLineItems',
    ),
    'emails_purchases_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Purchases',
      'rhs_table' => 'purchases',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Purchases',
    ),
    'emails_purchasedlineitems_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'PurchasedLineItems',
      'rhs_table' => 'purchased_line_items',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'PurchasedLineItems',
    ),
    'emails_products_rel' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'emails_beans',
      'join_key_lhs' => 'email_id',
      'join_key_rhs' => 'bean_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Products',
    ),
    'emails_from' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailParticipants',
      'rhs_table' => 'emails_email_addr_rel',
      'rhs_key' => 'email_id',
      'relationship_type' => 'one-to-one',
      'relationship_class' => 'EmailSenderRelationship',
      'relationship_file' => 'modules/Emails/EmailSenderRelationship.php',
      'relationship_role_columns' => 
      array (
        'address_type' => 'from',
      ),
    ),
    'emails_to' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailParticipants',
      'rhs_table' => 'emails_email_addr_rel',
      'rhs_key' => 'email_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'EmailRecipientRelationship',
      'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
      'relationship_role_columns' => 
      array (
        'address_type' => 'to',
      ),
    ),
    'emails_cc' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailParticipants',
      'rhs_table' => 'emails_email_addr_rel',
      'rhs_key' => 'email_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'EmailRecipientRelationship',
      'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
      'relationship_role_columns' => 
      array (
        'address_type' => 'cc',
      ),
    ),
    'emails_bcc' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailParticipants',
      'rhs_table' => 'emails_email_addr_rel',
      'rhs_key' => 'email_id',
      'relationship_type' => 'one-to-many',
      'relationship_class' => 'EmailRecipientRelationship',
      'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
      'relationship_role_columns' => 
      array (
        'address_type' => 'bcc',
      ),
    ),
    'emails_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Emails',
      'user_field' => 'created_by',
    ),
    'emails_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Emails',
      'user_field' => 'created_by',
    ),
    'emails_tags' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Emails',
      'dynamic_subpanel' => true,
    ),
    'emails_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'emails_teams' => 
    array (
      'lhs_module' => 'Emails',
      'lhs_table' => 'emails',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'emails_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'emailspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_email_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_message_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'message_id',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_email_parent_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_email_assigned',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'type',
        2 => 'status',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_date_modified',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'date_modified',
      ),
    ),
    6 => 
    array (
      'name' => 'idx_state',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'state',
        1 => 'id',
      ),
    ),
    7 => 
    array (
      'name' => 'idx_mailbox_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'mailbox_id',
      ),
    ),
    8 => 
    array (
      'name' => 'idx_emails_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_emails' => 
    array (
      'name' => 'idx_emails_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_emails' => 
    array (
      'name' => 'idx_emails_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
  ),
  'processes' => 
  array (
    'enabled' => true,
    'types' => 
    array (
      'CF' => 
      array (
        0 => 'teams',
        1 => 'assigned_user_id',
      ),
      'BR' => 
      array (
        0 => 'assigned_user_id',
      ),
      'AC' => 
      array (
      ),
      'RR' => 
      array (
      ),
      'RQF' => 
      array (
      ),
      'ROF' => 
      array (
      ),
      'PD' => 
      array (
        0 => 'created_by_name',
        1 => 'date_entered',
        2 => 'date_modified',
        3 => 'date_sent',
        4 => 'direction',
        5 => 'modified_by_name',
        6 => 'reply_to_status',
        7 => 'state',
        8 => 'name',
      ),
      'BRR' => 
      array (
        0 => 'created_by_name',
        1 => 'date_entered',
        2 => 'date_modified',
        3 => 'date_sent',
        4 => 'direction',
        5 => 'modified_by_name',
        6 => 'reply_to_status',
        7 => 'state',
        8 => 'name',
      ),
      'ET' => 
      array (
        0 => 'created_by_name',
        1 => 'date_entered',
        2 => 'date_modified',
        3 => 'date_sent',
        4 => 'direction',
        5 => 'modified_by_name',
        6 => 'reply_to_status',
        7 => 'state',
        8 => 'name',
      ),
    ),
  ),
  'portal_visibility' => 
  array (
    'class' => 'Emails',
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
    'EmailsVisibility' => true,
  ),
  'templates' => 
  array (
    'favorite' => 'favorite',
    'following' => 'following',
    'taggable' => 'taggable',
    'team_security' => 'team_security',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);