<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

/**
 * Remove unused indexes
 */
class SugarUpgradeCleanupIndexes extends UpgradeScript
{
    public $order = 2099;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }

        $this->dropIndexes('accounts', ['idx_account_billing_address_country']);
        $this->dropIndexes('accounts_audit', ['idx_accounts_audit_parent_id']);
        $this->dropIndexes('accounts_bugs', ['idx_acc_bug_acc']);
        $this->dropIndexes('accounts_dataprivacy', ['idx_acc_dataprivacy_acc']);
        $this->dropIndexes('acl_actions', ['idx_del_category_name', 'idx_aclaction_id_del', 'idx_category_name']);
        $this->dropIndexes('acl_role_sets_acl_roles', ['idx_rsr_acl_role_set_id']);
        $this->dropIndexes('acl_roles_actions', ['idx_aclrole_action']);
        $this->dropIndexes('acl_roles_users', ['idx_aclrole_id']);
        $this->dropIndexes('address_book_list_items', ['abli_list_id_idx']);
        $this->dropIndexes('bugs_audit', ['idx_bugs_audit_parent_id']);
        $this->dropIndexes('bugs', ['idx_bug_name', 'idx_bugs_assigned_user']);
        $this->dropIndexes('calls_audit', ['idx_calls_audit_parent_id']);
        $this->dropIndexes('calls_contacts', ['idx_con_call_call']);
        $this->dropIndexes('calls_leads', ['idx_lead_call_call']);
        $this->dropIndexes('calls_users', ['idx_call_users', 'idx_usr_call_call']);
        $this->dropIndexes('calls', ['idx_calls_date_start']);
        $this->dropIndexes('campaign_log', ['idx_target_id']);
        $this->dropIndexes('campaigns_audit', ['idx_campaigns_audit_parent_id']);
        $this->dropIndexes('campaigns', ['idx_campaign_name']);
        $this->dropIndexes('cases_audit', ['idx_cases_audit_parent_id']);
        $this->dropIndexes('cases_bugs', ['idx_cas_bug_cas']);
        $this->dropIndexes('config', ['idx_config_platform']);
        $this->dropIndexes('contacts_audit', ['idx_contacts_audit_parent_id']);
        $this->dropIndexes('contacts_bugs', ['idx_con_bug_con']);
        $this->dropIndexes('contacts_cases', ['idx_con_case_con']);
        $this->dropIndexes('contacts_dataprivacy', ['idx_con_dataprivacy_con']);
        $this->dropIndexes('contacts_users', ['idx_con_users_con']);
        $this->dropIndexes('contacts', ['idx_cont_assigned']);
        $this->dropIndexes('contracts_audit', ['idx_contracts_audit_parent_id']);
        $this->dropIndexes('contracts', ['idx_contract_name']);
        $this->dropIndexes('data_privacy_audit', ['idx_data_privacy_audit_parent_id']);
        $this->dropIndexes('data_privacy', ['idx_dataprivacy_name']);
        $this->dropIndexes('eapm', ['idx_eapm_name']);
        $this->dropIndexes('email_addr_bean_rel', ['idx_email_address_id']);
        $this->dropIndexes('email_addresses_audit', ['idx_email_addresses_audit_parent_id']);
        $this->dropIndexes('email_cache', ['idx_ie_id']);
        $this->dropIndexes('emailman', ['idx_eman_relid_reltype_id']);
        $this->dropIndexes('fields_meta_data', ['idx_meta_id_del', 'idx_meta_cm_del']);
        $this->dropIndexes('forecast_manager_worksheets_audit', ['idx_forecast_manager_worksheets_audit_parent_id']);
        $this->dropIndexes('forecast_worksheets', ['idx_forecastworksheet_account_id_del']);
        $this->dropIndexes('fts_queue', ['idx_beans_bean_id', 'idx_fts_queue_bean_id', 'idx_bean_id_processed', 'idx_beans_bean_id_processed']);
        $this->dropIndexes('job_queue', ['idx_status_scheduler', 'idx_status_time', 'idx_status_entered', 'idx_status_modified', 'idx_group_status']);
        $this->dropIndexes('kbcontent_templates_audit', ['idx_kbcontent_templates_audit_parent_id']);
        $this->dropIndexes('kbcontents_audit', ['idx_kbcontents_audit_parent_id']);
        $this->dropIndexes('kbcontents', ['idx_kbcontent_name']);
        $this->dropIndexes('leads_audit', ['idx_leads_audit_parent_id']);
        $this->dropIndexes('leads_dataprivacy', ['idx_lead_dataprivacy_lead']);
        $this->dropIndexes('leads', ['idx_lead_assigned', 'idx_lead_del_stat']);
        $this->dropIndexes('meetings_audit', ['idx_meetings_audit_parent_id']);
        $this->dropIndexes('meetings_contacts', ['idx_con_mtg_mtg']);
        $this->dropIndexes('meetings_leads', ['idx_lead_meeting_meeting']);
        $this->dropIndexes('meetings_users', ['idx_meeting_users', 'idx_usr_mtg_mtg']);
        $this->dropIndexes('meetings', ['idx_meet_date_start', 'idx_mtg_name', 'idx_meet_recurrence_id']);
        $this->dropIndexes('metadata_cache', ['type_indx']);
        $this->dropIndexes('notes_audit', ['idx_notes_audit_parent_id']);
        $this->dropIndexes('notes', ['idx_note_email_id']);
        $this->dropIndexes('oauth_consumer', ['idx_oauthkey_name']);
        $this->dropIndexes('opportunities_audit', ['idx_opportunities_audit_parent_id']);
        $this->dropIndexes('opportunities_contacts', ['idx_con_opp_opp']);
        $this->dropIndexes('opportunities', ['idx_opp_assigned_timestamp', 'idx_opportunity_sales_status', 'idx_opportunity_opportunity_type', 'idx_opportunity_lead_source', 'idx_opportunity_mkto_id']);
        $this->dropIndexes('outbound_email', ['oe_user_id_idx']);
        $this->dropIndexes('pdfmanager', ['idx_pdfmanager_name']);
        $this->dropIndexes('product_bundle_note', ['idx_pbn_note']);
        $this->dropIndexes('product_bundle_product', ['idx_pbp_quote']);
        $this->dropIndexes('product_bundle_quote', ['idx_pbq_quote']);
        $this->dropIndexes('product_templates_audit', ['idx_product_templates_audit_parent_id']);
        $this->dropIndexes('product_templates', ['idx_producttemplate_status']);
        $this->dropIndexes('products_audit', ['idx_products_audit_parent_id']);
        $this->dropIndexes('project_task_audit', ['idx_project_task_audit_parent_id']);
        $this->dropIndexes('projects_accounts', ['idx_proj_acct_proj']);
        $this->dropIndexes('projects_bugs', ['idx_proj_bug_proj']);
        $this->dropIndexes('projects_cases', ['idx_proj_case_proj']);
        $this->dropIndexes('projects_contacts', ['idx_proj_con_proj']);
        $this->dropIndexes('projects_opportunities', ['idx_proj_opp_proj']);
        $this->dropIndexes('projects_products', ['idx_proj_prod_project']);
        $this->dropIndexes('projects_quotes', ['idx_proj_quote_proj']);
        $this->dropIndexes('projects_revenue_line_items', ['idx_proj_rli_project']);
        $this->dropIndexes('prospect_list_campaigns', ['idx_pro_id']);
        $this->dropIndexes('prospects_audit', ['idx_prospects_audit_parent_id']);
        $this->dropIndexes('prospects_dataprivacy', ['idx_prospect_dataprivacy_prospect']);
        $this->dropIndexes('prospects', ['idx_prospects_assigned']);
        $this->dropIndexes('quotas_audit', ['idx_quotas_audit_parent_id']);
        $this->dropIndexes('quotes_accounts', ['idx_acc_qte_opp']);
        $this->dropIndexes('quotes_audit', ['idx_quotes_audit_parent_id']);
        $this->dropIndexes('quotes_contacts', ['idx_con_qte_opp']);
        $this->dropIndexes('quotes', ['idx_qte_name']);
        $this->dropIndexes('report_schedules_audit', ['idx_report_schedules_audit_parent_id']);
        $this->dropIndexes('reportschedules_users', ['idx_rs_users', 'idx_usr_rs_rs']);
        $this->dropIndexes('revenue_line_items_audit', ['idx_revenue_line_items_audit_parent_id']);
        $this->dropIndexes('revenue_line_items', ['idx_rli_user_dc_timestamp', 'idx_revenuelineitem_probability', 'idx_revenuelineitem_commit_stage', 'idx_revenuelineitem_quantity']);
        $this->dropIndexes('roles', ['idx_role_id_del']);
        $this->dropIndexes('roles_modules', ['idx_role_id']);
        $this->dropIndexes('tags_audit', ['idx_tags_audit_parent_id']);
        $this->dropIndexes('tasks_audit', ['idx_tasks_audit_parent_id']);
        $this->dropIndexes('tasks', ['idx_task_assigned']);
        $this->dropIndexes('timeperiods', ['idx_timestamps', 'idx_timeperiod_name', 'idx_timeperiod_start_date', 'idx_timeperiod_end_date']);
        $this->dropIndexes('users', ['idx_user_last_first', 'idx_last_login', 'idx_users_tmst_id', 'idx_user_title', 'idx_user_department']);
        $this->dropIndexes('users_holidays', ['idx_user_holi_user']);
        $this->dropIndexes('workflow_alerts', ['idx_workflowalerts']);
        $this->dropIndexes('workflow_alertshells', ['idx_workflowalertshell']);
        $this->dropIndexes('workflow_schedules', ['idx_wkfl_schedule']);

        // Ent/Ult-only tables
        $this->dropIndexes('business_centers_audit', ['idx_business_centers_audit_parent_id']);
        $this->dropIndexes('messages_audit', ['idx_messages_audit_parent_id']);
        $this->dropIndexes('messages_contacts', ['idx_contact_message_message']);
        $this->dropIndexes('messages_leads', ['idx_lead_message_message']);
        $this->dropIndexes('messages_users', ['idx_message_user', 'idx_user_message_message']);
        $this->dropIndexes('pmse_bpm_flow', ['idx_pmse_bpm_flow_cas_id', 'idx_pmse_bpm_flow_cas_sugar_object_id', 'idx_pmse_bpm_flow_parent', 'idx_pmse_bpm_flow_status']);
        $this->dropIndexes('purchased_line_items_audit', ['idx_purchased_line_items_audit_parent_id']);
        $this->dropIndexes('purchases_audit', ['idx_purchases_audit_parent_id']);
        $this->dropIndexes('shift_exceptions_audit', ['idx_shift_exceptions_audit_parent_id']);
        $this->dropIndexes('shift_exceptions_users', ['idx_shift_exception_id']);
        $this->dropIndexes('shifts_audit', ['idx_shifts_audit_parent_id']);
        $this->dropIndexes('shifts_users', ['idx_shift_id']);
    }

    private function dropIndexes(string $table, array $indexes)
    {
        if (!$indexes) {
            return;
        }
        if (!$this->db->tableExists($table)) {
            $this->log("Table $table does not exist, skipping");
            return;
        }
        $existingIndexes = $this->db->get_indices($table);
        $defs = [];
        foreach ($indexes as $index) {
            if (!isset($existingIndexes[$index])) {
                $this->log("Index $index does not exist, skipping");
                continue;
            }
            $defs[] = $existingIndexes[$index];
        }
        $this->db->dropIndexes($table, $defs);
    }
}
