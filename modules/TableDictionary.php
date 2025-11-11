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

include 'metadata/accounts_bugsMetaData.php';
include 'metadata/accounts_casesMetaData.php';
include 'metadata/accounts_contactsMetaData.php';
include 'metadata/accounts_opportunitiesMetaData.php';
include 'metadata/audit_eventsMetaData.php';
include 'metadata/calls_contactsMetaData.php';
include 'metadata/calls_usersMetaData.php';
include 'metadata/calls_leadsMetaData.php';
include 'metadata/cases_bugsMetaData.php';
include 'metadata/contacts_bugsMetaData.php';
include 'metadata/contacts_casesMetaData.php';
include 'metadata/configMetaData.php';
include 'metadata/contacts_usersMetaData.php';
include 'metadata/custom_fieldsMetaData.php';
include 'metadata/email_addressesMetaData.php';
include 'metadata/emails_beansMetaData.php';
include 'metadata/erased_fieldsMetaData.php';
include 'metadata/foldersMetaData.php';
include 'metadata/import_mapsMetaData.php';
include 'metadata/meetings_contactsMetaData.php';
include 'metadata/meetings_usersMetaData.php';
include 'metadata/meetings_leadsMetaData.php';
include 'metadata/opportunities_contactsMetaData.php';
include 'metadata/users_passwordLinkMetaData.php';
include 'metadata/team_sets_teamsMetaData.php';
include 'metadata/prospect_list_campaignsMetaData.php';
include 'metadata/prospect_lists_prospectsMetaData.php';
include 'metadata/roles_modulesMetaData.php';
include 'metadata/roles_usersMetaData.php';
include 'metadata/acl_role_sets_acl_rolesMetaData.php';
include 'metadata/addressBookMetaData.php';
include 'metadata/recordListMetaData.php';
include 'metadata/project_bugsMetaData.php';
include 'metadata/project_casesMetaData.php';
include 'metadata/project_productsMetaData.php';
include 'metadata/project_resourcesMetaData.php';
include 'metadata/projects_accountsMetaData.php';
include 'metadata/projects_contactsMetaData.php';
include 'metadata/projects_opportunitiesMetaData.php';
include 'metadata/calendar_ical_configsMetaData.php';

include 'metadata/report_cache.php';
include 'metadata/saved_reportsMetaData.php';
include 'metadata/reportschedules_usersMetaData.php';
include 'metadata/reports_panelsMetaData.php';

include 'metadata/product_bundle_noteMetaData.php';
include 'metadata/product_bundle_productMetaData.php';
include 'metadata/product_bundle_quoteMetaData.php';
include 'metadata/product_productMetaData.php';
include 'metadata/quotes_accountsMetaData.php';
include 'metadata/quotes_contactsMetaData.php';
include 'metadata/quotes_opportunitiesMetaData.php';
include 'metadata/products_categoryTreeMetaData.php';
include 'metadata/fts_queueMetaData.php';
include 'metadata/geocode_queueMetaData.php';
include 'metadata/workflow_schedulesMetaData.php';
include 'metadata/schedulers_timesMetaData.php';
include 'metadata/contracts_opportunitiesMetaData.php';
include 'metadata/contracts_contactsMetaData.php';
include 'metadata/contracts_quotesMetaData.php';
include 'metadata/contracts_productsMetaData.php';
include 'metadata/projects_quotesMetaData.php';
include 'metadata/projects_revenuelineitemsMetaData.php';
include 'metadata/users_holidaysMetaData.php';
include 'metadata/contacts_dataprivacyMetaData.php';
include 'metadata/leads_dataprivacyMetaData.php';
include 'metadata/prospects_dataprivacyMetaData.php';
include 'metadata/accounts_dataprivacyMetaData.php';
require 'modules/Trackers/tracker_tracker_queriesMetaData.php';

//ACL RELATIONSHIPS
include 'metadata/acl_roles_actionsMetaData.php';
include 'metadata/acl_roles_usersMetaData.php';
// INBOUND EMAIL
include 'metadata/inboundEmail_autoreplyMetaData.php';
include 'metadata/inboundEmail_cacheTimestampMetaData.php';
include 'metadata/email_cacheMetaData.php';
include 'metadata/email_marketing_prospect_listsMetaData.php';

//linked documents.
include 'metadata/linked_documentsMetaData.php';
include 'metadata/sessionHistoryMetaData.php';

// Documents, so we can start replacing Notes as the primary way to attach something to something else.
include 'metadata/documents_accountsMetaData.php';
include 'metadata/documents_contactsMetaData.php';
include 'metadata/documents_opportunitiesMetaData.php';
include 'metadata/documents_casesMetaData.php';
include 'metadata/documents_bugsMetaData.php';
include 'metadata/documents_productsMetaData.php';
include 'metadata/documents_revenuelineitemsMetaData.php';
include 'metadata/documents_quotesMetaData.php';
include 'metadata/forecast_treeMetaData.php';
include 'metadata/oauth_nonce.php';
include 'metadata/activities_usersMetaData.php';
include 'metadata/metadata_cacheMetaData.php';
include 'metadata/tag_bean_relMetaData.php';
include 'metadata/kbusefulnessMetaData.php';

// TeamSecurity Denormalization tables
include 'metadata/team_sets_usersMetaData.php';

// Locked fields
include 'metadata/locked_field_bean_relMetaData.php';

// Holidays to Business Centers relationship
include 'metadata/business_centers_holidaysMetaData.php';

// Users to Shift Exceptions relationship
include 'metadata/shift_exceptions_usersMetaData.php';

// Users to Shifts  relationship
include 'metadata/shifts_usersMetaData.php';

// Purchases Relationships
include 'metadata/documents_purchasesMetaData.php';
include 'metadata/contacts_purchasesMetaData.php';
include 'metadata/cases_purchasesMetaData.php';
include 'metadata/accounts_purchasesMetaData.php';

// Purchased Line Items to Documents relationship
include 'metadata/documents_purchasedlineitemsMetaData.php';

// Commentlog relation field
include 'metadata/commentlog_relMetaData.php';

// Product definition table
include 'metadata/product_definitionMetaData.php';

// Time-Aware Schedules table
include 'metadata/time_aware_schedulesMetaData.php';

// Messages guest relationships
include 'metadata/messages_contactsMetaData.php';
include 'metadata/messages_leadsMetaData.php';
include 'metadata/messages_usersMetaData.php';

// Escalations relationships
include 'metadata/accounts_escalationsMetaData.php';
include 'metadata/contacts_escalationsMetaData.php';
include 'metadata/documents_escalationsMetaData.php';

// External users relationships
include 'metadata/external_users_accountsMetaData.php';
include 'metadata/external_users_bugsMetaData.php';
include 'metadata/external_users_casesMetaData.php';
include 'metadata/external_users_opportunitiesMetaData.php';
include 'metadata/external_users_quotesMetaData.php';

//hint metadata
include 'metadata/hint-packageMetaData.php';

// User last state
include 'metadata/users_last_statesMetaData.php';

// System process lock
include 'metadata/system_process_lockMetaData.php';

$defs = SugarAutoLoader::loadExtension('tabledictionary');
if ($defs) {
    require $defs;
}
