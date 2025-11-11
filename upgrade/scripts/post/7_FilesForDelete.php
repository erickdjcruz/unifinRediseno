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
 * Select files to delete during install
 */
class SugarUpgradeFilesForDelete extends UpgradeScript
{
    public $order = 7000;
    public $version = '7.1.5';
    public $type = self::UPGRADE_CORE;

    public function run()
    {
        $files = ['themes/Sugar/js',
            //Remove the themes/Sugar/tpls directory
            'themes/Sugar/tpls',
            'themes/Sugar5',
            // remove the files moved to vendor
            'include/HTMLPurifier',
            'include/HTTP_WebDAV_Server',
            'include/Smarty',
            'include/Sugar_Smarty.php',
            'XTemplate',
            'Zend',
            'include/oauth2-php',
            'include/tcpdf',
            'include/ytree',
            'include/SugarSearchEngine/Elastic/Elastica',
            //Remove the SugarFeed files
            'modules/Cases/SugarFeeds',
            'modules/Contacts/SugarFeeds',
            'modules/Leads/SugarFeeds',
            'modules/Opportunities/SugarFeeds/OppFeed.php',
            'modules/SugarFeed',
            // remove old popup picker files from RLI
            'modules/RevenueLineItems/Popup_picker.html',
            'modules/RevenueLineItems/Popup_picker.php',
            // remove phpunit from vendor
            'vendor/phpunit',
            // remove the old base metadata file template for the dashablelist view
            'include/SugarObjects/templates/basic/clients/base/views/dashablelist/dashablelist.php',
            // old phpmailer in thr include directory is no longer needed or referenced as of 7.0
            'include/phpmailer',
            //remove old connectors
            'modules/Connectors/connectors/sources/ext/rest/zoominfocompany',
            'modules/Connectors/connectors/sources/ext/rest/zoominfoperson',
            'modules/Connectors/connectors/sources/ext/rest/linkedin',
            'modules/Connectors/connectors/sources/ext/rest/insideview',
            'modules/Connectors/connectors/sources/ext/eapm/facebook',
            'modules/Connectors/connectors/sources/ext/soap/hoovers',
            // Remove old less files from styleguide
            'styleguide/less/clients/mobile/fixed_variables.less',
            'styleguide/less/clients/mobile/font-awesome.less',
            'styleguide/less/clients/mobile/forms.less',
            'styleguide/less/clients/mobile/labels-badges.less',
            'styleguide/less/clients/mobile/navbar.less',
            'styleguide/less/clients/mobile/navs.less',
            'styleguide/less/clients/mobile/nomad.less',
            'styleguide/less/clients/mobile/sugarmobile.less',
            'styleguide/less/clients/portal/config.less',
            'styleguide/less/modules/nv.d3.less',
            'styleguide/less/sugar-bootstrap',
            'styleguide/less/sugar-specific/actions.less',
            'styleguide/less/sugar-specific/activitystreams.less',
            'styleguide/less/sugar-specific/chosen.less',
            'styleguide/less/sugar-specific/clickmenu.less',
            'styleguide/less/sugar-specific/dcmenu.less',
            'styleguide/less/sugar-specific/modulelist.less',
            'styleguide/less/sugar-specific/position.less',
            'styleguide/less/sugar-specific/progress.less',
            'styleguide/less/sugar-specific/quickcreate.less',
            'styleguide/less/sugar-specific/responsive-forecast.less',
            'styleguide/less/sugar-specific/responsive.less',
            'styleguide/less/sugar-specific/topline-forecast.less',
            'styleguide/less/sugar-specific/vcard.less',
            'styleguide/less/sugar-specific/yui.less',
            'styleguide/less/twitter-bootstrap/carousel.less',
            'styleguide/less/twitter-bootstrap/charts.less',
            'styleguide/less/twitter-bootstrap/chosen.less',
            'styleguide/less/twitter-bootstrap/datatables.less',
            'styleguide/less/twitter-bootstrap/pager.less',
            'styleguide/less/twitter-bootstrap/pagination.less',
            'styleguide/less/twitter-bootstrap/responsive.less',
            'styleguide/less/twitter-bootstrap/sprites.less',
            'styleguide/less/twitter-bootstrap/tiptip.less',
            'styleguide/less/twitter-bootstrap/toggle.less',
            // BR 796 api files
            'clients/mobile/api/MetadataMobileApi.php',
            'clients/portal/api/MetadataPortalApi.php',
            'clients/base/views/activitystream-bottom/activitystream-bottom.php',
            // NOMAD-1179 mobile search definitions
            'modules/Accounts/clients/mobile/views/search/search.php',
            'modules/Bugs/clients/mobile/views/search/search.php',
            'modules/Calls/clients/mobile/views/search/search.php',
            'modules/Cases/clients/mobile/views/search/search.php',
            'modules/Contacts/clients/mobile/views/search/search.php',
            'modules/Employees/clients/mobile/views/search/search.php',
            'modules/Leads/clients/mobile/views/search/search.php',
            'modules/Meetings/clients/mobile/views/search/search.php',
            'modules/Opportunities/clients/mobile/views/search/search.php',
            'modules/ProductTemplates/clients/mobile/views/search/search.php',
            'modules/Tasks/clients/mobile/views/search/search.php',
            'modules/Users/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/basic/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/company/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/file/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/issue/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/person/clients/mobile/views/search/search.php',
            'include/SugarObjects/templates/sale/clients/mobile/views/search/search.php',
            // NOMAD-1384 remove mobile views from modules which are not supported by mobile app
            'modules/Bugs/clients/mobile/',
            // NOMAD-1295
            'modules/Accounts/metadata/wireless.subpaneldefs.php',
            'modules/Bugs/metadata/wireless.listviewdefs.php',
            'modules/Bugs/metadata/wireless.listviewdefs.php',
            'modules/Calls/metadata/wireless.listviewdefs.php',
            'modules/Calls/metadata/wireless.subpaneldefs.php',
            'modules/Calls/clients/base/layouts/records/records.php',
            'modules/Cases/metadata/wireless.subpaneldefs.php',
            'modules/Contacts/metadata/wireless.subpaneldefs.php',
            'modules/Documents/metadata/wireless.editviewdefs.php',
            'modules/Documents/metadata/wireless.subpaneldefs.php',
            'modules/Leads/metadata/wireless.subpaneldefs.php',
            'modules/Meetings/metadata/wireless.listviewdefs.php',
            'modules/Meetings/metadata/wireless.subpaneldefs.php',
            'modules/Meetings/clients/base/layouts/records/records.php',
            'modules/Notes/metadata/wireless.editviewdefs.php',
            'modules/Notes/metadata/wireless.listviewdefs.php',
            'modules/Opportunities/metadata/wireless.subpaneldefs.php',
            'modules/ProductTemplates/metadata/wireless.detailviewdefs.php',
            'modules/ProductTemplates/metadata/wireless.editviewdefs.php',
            'modules/Products/metadata/wireless.detailviewdefs.php',
            'modules/Products/metadata/wireless.editviewdefs.php',
            'modules/Quotes/metadata/wireless.subpaneldefs.php',
            'modules/Tasks/metadata/wireless.listviewdefs.php',
            'modules/Tasks/metadata/wireless.subpaneldefs.php',
            'modules/Users/metadata/wireless.detailviewdefs.php',
            'modules/Users/metadata/wireless.editviewdefs.php',
            'modules/Users/metadata/wireless.listviewdefs.php',
            'modules/Users/metadata/wireless.searchdefs.php',
            'modules/Employees/views/view.wirelessedit.php',
            'modules/Opportunities/views/view.wirelessedit.php',
            'modules/Users/views/view.wirelesslogin.php',
            'modules/Users/views/view.wirelessmain.php',
            'modules/Calls/views/view.wirelesssave.php',
            'modules/Meetings/views/view.wirelesssave.php',
            'tests/include/SubPanel/Bug63486Test.php',
            'modules/Meetings/api/MeetingsApi.php',
            // MAR-1736 / SC-2611
            'modules/Emails/clients/base/views/panel-top/panel-top.js',
            // NOMAD-1799
            'modules/Meetings/clients/mobile/api/MobileMeetingsApi.php',
            // BR-1574 Move Elastica library to composer (new path vendor/ruflin/elastica)
            'vendor/Elastica/',
            'modules/EmailMan/Save.php',
            //CRYS-773 Delete version.json from upgrade wizard directory
            'modules/UpgradeWizard/version.json',
            // jira:MAR-2706 Move PHPMailer library to composer (new path vendor/phpmailer/phpmailer)
            'vendor/PHPMailer/',
            // PAT-2081 Move Google API library to composer
            'include/google-api-php-client',
            // Delete files that should have been deleted before
            'upgrade/scripts/post/5_MinifyJS.php',
            // MACAROON-1125
            'include/javascript/pmse/business_rules.js',
            // MACAROON-1328
            'modules/pmse_Inbox/engine/PMSEAccessManagement.php',
            // BR-4286 - Retire unused MVC action files
            'modules/Calendar/views/view.createinvitee.php',
            'modules/Calendar/views/view.getgr.php',
            'modules/Calendar/views/view.getgrusers.php',
            // MACAROON-1385
            'modules/pmse_Inbox/engine/PMSEHandlers/PMSECronHandler.php',
            // BR-3766 - Remove unused non-INT flagged file
            'modules/UpgradeWizard/populateColumns.php',
            // BR-8503 - use portal index.php file instead of index.html
            'portal2/index.html',
        ];

        if (version_compare($this->from_version, '7.8.0.0', '<')) {
            $files[] = 'modules/Forecasts/clients/base/view/forecast-pipeline/forecast-pipeline.hbs';
            $files[] = 'include/javascript/sugar7/plugins/QuickSearchFilter.js';
            $files[] = 'styleguide/assets/css/sugar.css';
            $files[] = 'styleguide/assets/css/bootstrap.css';
            //MACAROON-1005 remove casesList-list.js
            $files[] = 'modules/pmse_Inbox/clients/base/layouts/casesList-list/casesList-list.js';
            $files[] = 'clients/base/views/passwordmodal';
            $files[] = 'clients/portal/views/passwordmodal';
            $files[] = 'modules/Notifications/controller.php';
            $files[] = 'include/api/AttachmentListApi.php';
            $files[] = 'clients/base/views/pmse-case/businesscard.hbs';
            $files[] = 'clients/base/views/pmse-case/headerpane.hbs';
            $files[] = 'clients/base/views/pmse-case/tabspanels.hbs';
            $files[] = 'clients/base/layouts/create-actions';
            $files[] = 'clients/base/views/create-actions';
            $files[] = 'clients/base/views/news';
            $files[] = 'styleguide/assets/css/jsduck.css';
            $files[] = 'styleguide/less/sugar-specific/rtl.less';
            $files[] = 'styleguide/less/twitter-bootstrap/alerts.less';
            $files[] = 'themes/default/css/ie.css';
            $files[] = 'jssource/src_files';
            $files[] = 'clients/base/layouts/create-dupecheck/create-dupecheck.js';
            $files[] = 'clients/base/layouts/dashlet-preview/dashlet-preview.js';
            $files[] = 'clients/base/layouts/first-login-wizard/first-login-wizard.js';
            $files[] = 'clients/base/layouts/multi-selection-list-link/multi-selection-list-link.js';
            $files[] = 'clients/base/layouts/multi-selection-list/multi-selection-list.js';
            $files[] = 'clients/base/views/help-dashboard-headerpane/help-dashboard-headerpane.js';
            $files[] = 'modules/Accounts/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/Contacts/clients/base/views/panel-top-for-cases/panel-top-for-cases.js';
            $files[] = 'modules/Contacts/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/Leads/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/Prospects/clients/base/views/panel-top-for-prospectlists/' .
                'panel-top-for-prospectlists.js';
            $files[] = 'modules/Quotes/clients/base/views/panel-top-for-accounts/panel-top-for-accounts.js';
            $files[] = 'modules/Users/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/Leads/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/Prospects/clients/base/views/panel-top-for-prospectlists/' .
                'panel-top-for-prospectlists.js';
            $files[] = 'modules/Quotes/clients/base/views/panel-top-for-accounts/panel-top-for-accounts.js';
            $files[] = 'modules/Users/clients/base/views/panel-top-for-prospectlists/panel-top-for-prospectlists.js';
            $files[] = 'modules/ACLRoles/clients/base/layouts/records/records.js';
            $files[] = 'modules/Administration/clients/base/layouts/records/records.js';
            $files[] = 'modules/Calendar/clients/base/layouts/records/records.js';
            $files[] = 'modules/Campaigns/clients/base/layouts/records/records.js';
            $files[] = 'modules/ContractTypes/clients/base/layouts/records/records.js';
            $files[] = 'modules/Contracts/clients/base/layouts/records/records.js';
            $files[] = 'modules/DocumentRevisions/clients/base/layouts/records/records.js';
            $files[] = 'modules/Documents/clients/base/layouts/records/records.js';
            $files[] = 'modules/EmailTemplates/clients/base/layouts/records/records.js';
            $files[] = 'modules/Emails/clients/base/layouts/records/records.js';
            $files[] = 'modules/Employees/clients/base/layouts/records/records.js';
            $files[] = 'modules/InboundEmail/clients/base/layouts/records/records.js';
            $files[] = 'modules/Manufacturers/clients/base/layouts/records/records.js';
            $files[] = 'modules/OAuthKeys/clients/base/layouts/records/records.js';
            $files[] = 'modules/PdfManager/clients/base/layouts/records/records.js';
            $files[] = 'modules/Project/clients/base/layouts/records/records.js';
            $files[] = 'modules/ProjectTask/clients/base/layouts/records/records.js';
            $files[] = 'modules/Quotas/clients/base/layouts/records/records.js';
            $files[] = 'modules/Quotes/clients/base/layouts/records/records.js';
            $files[] = 'modules/Reports/clients/base/layouts/records/records.js';
            $files[] = 'modules/TaxRates/clients/base/layouts/records/records.js';
            $files[] = 'modules/TeamNotices/clients/base/layouts/records/records.js';
            $files[] = 'modules/Teams/clients/base/layouts/records/records.js';
            $files[] = 'modules/UserSignatures/clients/base/layouts/records/records.js';
            $files[] = 'modules/Users/clients/base/layouts/records/records.js';
            $files[] = 'clients/base/layouts/history-summary-preview/history-summary-preview.js';
            $files[] = 'clients/base/views/history-summary-preview-header/history-summary-preview-header.js';
            $files[] = 'clients/base/views/history-summary/history-summary.hbs';
            $files[] = 'mobile/js/sidecar.js';
            $files[] = 'modules/pmse_Inbox/engine/Crypt.php';
            $files[] = 'modules/pmse_Inbox/engine/PMSEAccessManager.php';
            $files[] = 'modules/pmse_Inbox/engine/PMSELicenseManager.php';
            $files[] = 'modules/pmse_Project/pmse_BpmAccessManagement';
            $files[] = 'themes/default/images/ical-settings-icon.gif';
            $files[] = 'modules/Categories/clients/base/views/tree/tree.php';
            $files[] = 'modules/Forecasts/clients/base/views/forecast-pipeline/forecast-pipeline.hbs';
            $files[] = 'modules/ProductTemplates/clients/base/layouts/subpanels';
            $files[] = 'modules/RevenueLineItems/clients/base/views/filter-rows/filter-rows.php';
            $files[] = 'modules/RevenueLineItems/clients/base/views/subpanel-list-with-massupdate/' .
                'subpanel-list-with-massupdate.js';
            $files[] = 'src/JobQueue';
            $files[] = 'queueManager.php';
        }

        //Remove NotificationCenter, iCal and JobQ.
        if (version_compare($this->from_version, '7.8.0.0RC3', '>=') &&
            version_compare($this->from_version, '7.8.0.0', '<')
        ) {
            $files[] = 'Ext/LogicHooks/Notifications.php';
            $files[] = 'caldav.php';
            $files[] = 'clients/base/api/ReminderApi.php';
            $files[] = 'clients/base/api/TokenVerificationApi.php';
            $files[] = 'data/acl/SugarACLAddressees.php';
            $files[] = 'include/api/help/reminder.html';
            $files[] = 'include/api/help/token_verification_help.html';
            $files[] = 'include/api/help/verify_socket_token_help.html';
            $files[] = 'include/api/help/verify_trigger_token_help.html';
            $files[] = 'include/javascript/sugar7/socket.js';
            $files[] = 'include/nmb.php';
            $files[] = 'include/sfr.php';
            $files[] = 'install/templates/triggerServerConfig.tpl';
            $files[] = 'install/templates/websocketConfig.tpl';
            $files[] = 'metadata/calls_addresseesMetaData.php';
            $files[] = 'metadata/meetings_addresseesMetaData.php';
            $files[] = 'modules/Addressees';
            $files[] = 'modules/Administration/ReExportEvents.php';
            $files[] = 'modules/Administration/RebuildReminders.php';
            $files[] = 'modules/Administration/templates/ReExportEvents.tpl';
            $files[] = 'modules/Administration/templates/RebuildReminders.tpl';
            $files[] = 'modules/Administration/templates/TriggerServer.tpl';
            $files[] = 'modules/Administration/templates/WebSockets.tpl';
            $files[] = 'modules/Administration/views/view.triggerserver.php';
            $files[] = 'modules/Administration/views/view.websockets.php';
            $files[] = 'modules/CalDav';
            $files[] = 'modules/Calls/Emitter.php';
            $files[] = 'modules/Calls/Ext/LogicHooks/logic_hooks.php';
            $files[] = 'modules/CarrierEmail';
            $files[] = 'modules/CarrierSugar';
            $files[] = 'modules/Meetings/Emitter.php';
            $files[] = 'modules/Meetings/Ext/LogicHooks/logic_hooks.php';
            $files[] = 'modules/NotificationCenter';
            $files[] = 'modules/Notifications/Ext/LogicHooks/logic_hooks.php';
            $files[] = 'modules/TriggerServer';
            $files[] = 'modules/UserPreferences/Ext/LogicHooks/logic_hooks.php';
            $files[] = 'modules/WebSockets';
            $files[] = 'queueManager.php';
            $files[] = 'src/Dav';
            $files[] = 'src/JobQueue';
            $files[] = 'src/Notification';
            $files[] = 'src/Socket';
            $files[] = 'src/Trigger';
            $files[] = 'styleguide/less/sugar-specific/notification-center.less';
            $files[] = 'upgrade/scripts/post/1_LockJobQueue.php';
            $files[] = 'upgrade/scripts/post/9_AddMeetingsAndCallsToEvents.php';
            $files[] = 'upgrade/scripts/post/9_ParticipantsLinksUpdate.php';
            $files[] = 'upgrade/scripts/post/9_RepairReminders.php';
            $files[] = 'upgrade/scripts/post/9_UnlockJobQueue.php';
            $files[] = 'upgrade/scripts/post/7_FixCallsMeetingsReminderSelection.php';
            $files[] = 'vendor/sabre';
        }

        if (version_compare($this->from_version, '7.9.0.0', '<')) {
            $files[] = 'sidecar/src/utils/file.js';
            $files[] = 'clients/base/fields/dnb-bal-import-menu-label';
            $files[] = 'clients/base/fields/dnbenum';
            $files[] = 'clients/base/layouts/dnb-bal';
            $files[] = 'clients/base/views/dnb';
            $files[] = 'clients/base/views/dnb-account-create';
            $files[] = 'clients/base/views/dnb-bal-header';
            $files[] = 'clients/base/views/dnb-bal-params';
            $files[] = 'clients/base/views/dnb-bal-results';
            $files[] = 'clients/base/views/dnb-company-info';
            $files[] = 'clients/base/views/dnb-competitors';
            $files[] = 'clients/base/views/dnb-contact-info';
            $files[] = 'clients/base/views/dnb-family-tree';
            $files[] = 'clients/base/views/dnb-financial-info';
            $files[] = 'clients/base/views/dnb-industry-info';
            $files[] = 'clients/base/views/dnb-lite-company-info';
            $files[] = 'clients/base/views/dnb-meter';
            $files[] = 'clients/base/views/dnb-news-and-media';
            $files[] = 'clients/base/views/dnb-premium-company-info';
            $files[] = 'clients/base/views/dnb-standard-company-info';
            $files[] = 'clients/base/api/DnbApi.php';
            $files[] = 'include/api/help/dnb_bulkimport_help.html';
            $files[] = 'include/api/help/dnb_get_help.html';
            $files[] = 'include/api/help/dnb_post_help.html';
            $files[] = 'include/externalAPI/Dnb';
            $files[] = 'modules/Accounts/clients/base/views/dnb-bal-params';
            $files[] = 'modules/Accounts/clients/base/views/dnb-bal-results';
            $files[] = 'modules/Connectors/connectors/formatters/ext/rest/dnb';
            $files[] = 'modules/Connectors/connectors/sources/ext/rest/dnb';
            $files[] = 'styleguide/less/clients/base/components/dnb-dashlets.less';
        }

        if (version_compare($this->from_version, '7.9.0.0', '<')) {
            $files[] = 'modules/Reports/clients/base/layouts/records';
        }

        if (version_compare($this->from_version, '8.1.0', '<')) {
            $files[] = 'modules/Reports/clients/base/fields/next-run';
        }

        if (version_compare($this->from_version, '8.3.0', '<')) {
            $files[] = 'include/SugarCharts/Jit';
        }

        $this->upgrader->fileToDelete($files, $this);
    }
}
