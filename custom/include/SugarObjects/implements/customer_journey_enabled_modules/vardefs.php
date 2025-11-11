<?php
// created: 2025-11-11 16:17:54
$vardefs = array (
  'fields' => 
  array (
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
);