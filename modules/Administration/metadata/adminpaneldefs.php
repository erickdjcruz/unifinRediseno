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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication;
use Sugarcrm\Sugarcrm\Entitlements\Subscription;

/** @var User $current_user */
global $current_user, $admin_group_header;

$config = \SugarConfig::getInstance();
$idpConfig = new Authentication\Config($config);
$idmModeConfig = $idpConfig->getIDMModeConfig();
$admin_option_defs = [];
$cloudSettingsPanelKey = $current_user->isIdmUserManager && !$current_user->isAdmin() ? 'Users' : 'Administration';

if ($idpConfig->isIDMModeEnabled()) {
    if (!key_exists($cloudSettingsPanelKey, $admin_option_defs)) {
        $admin_option_defs[$cloudSettingsPanelKey] = [];
    }
    $admin_option_defs[$cloudSettingsPanelKey]['sugarCloudSettings'] = [
        'Administration',
        'icon' => 'sicon-cloud',
        'LBL_SUGAR_CLOUD_SETTINGS_TITLE',
        'LBL_SUGAR_CLOUD_SETTINGS_DESC',
        $idpConfig->buildCloudConsoleUrl('/', [], $GLOBALS['current_user']->id),
        null,
        null,
        '_blank',
    ];
}

// Needed to see if this is configured for Sugar Cloud Insights
$insights = $config->get('cloud_insight', []);
if (!empty($insights['enabled'])) {
    if (!key_exists('Administration', $admin_option_defs)) {
        $admin_option_defs['Administration'] = [];
    }
    $admin_option_defs['Administration']['insights'] = [
        'LeadReports',
        'icon' => 'sicon-cloud-insights',
        'LBL_CLOUD_INSIGHTS_ADMIN_TITLE',
        'LBL_CLOUD_INSIGHTS_ADMIN_DESC',
        './index.php?module=Administration&action=CloudInsights',
        null,
        sprintf(
            'onclick="return SUGAR.Administration.CloudInsights.LinkToLanding(\'%s\', \'%s\');"',
            $insights['url'],
            $insights['key']
        ),
    ];
}

if (!empty($admin_option_defs[$cloudSettingsPanelKey]['sugarCloudSettings']) || !empty($admin_option_defs['Administration']['insights'])) {
    $admin_group_header[] = ['LBL_SUGAR_CLOUD_TITLE', '', false, $admin_option_defs, 'LBL_SUGAR_CLOUD_DESC'];
}

//users and security.
$admin_option_defs = [];
$admin_option_defs['Users']['user_management'] = ['Users', 'icon' => 'sicon-user-group', 'LBL_MANAGE_USERS_TITLE', 'LBL_MANAGE_USERS', '#Users'];
$admin_option_defs['Users']['roles_management'] = ['Roles', 'icon' => 'sicon-role-mgmt', 'LBL_MANAGE_ROLES_TITLE', 'LBL_MANAGE_ROLES', './index.php?module=ACLRoles&action=index'];
$admin_option_defs['Users']['teams_management'] = ['Teams', 'icon' => 'sicon-team-mgmt', 'LBL_MANAGE_TEAMS_TITLE', 'LBL_MANAGE_TEAMS', './index.php?module=Teams&action=index'];

if ($idpConfig->isIDMModeEnabled()) {
    $passwordManagerUrl = $idpConfig->buildCloudConsoleUrl('passwordManagement', [], $GLOBALS['current_user']->id);
    $passwordManagerTarget = '_blank';
    $passwordManagerLink = str_replace(
        '"',
        "\\'",
        sprintf($GLOBALS['app_strings']['ERR_PASSWORD_MANAGEMENT_DISABLED_FOR_IDM_MODE'], $passwordManagerUrl)
    );
    $passwordManagerOnClick = sprintf(
        'onclick = "parent.SUGAR.App.alert.show(\'disabled-for-idm-mode\', {level: \'info\', messages: \'%s\'});"',
        $passwordManagerLink
    );
} else {
    $passwordManagerUrl = './index.php?module=Administration&action=PasswordManager';
    $passwordManagerTarget = '_self';
    $passwordManagerOnClick = null;
}

$admin_option_defs['Administration']['password_management'] = [
    'Password',
    'icon' => 'sicon-password-mgmt',
    'LBL_MANAGE_PASSWORD_TITLE',
    'LBL_MANAGE_PASSWORD',
    $passwordManagerUrl,
    null,
    $passwordManagerOnClick,
    $passwordManagerTarget,
];

$admin_option_defs['Users']['tba_management'] = ['TbACLs', 'icon' => 'sicon-team-perm', 'LBL_TBA_CONFIGURATION', 'LBL_TBA_CONFIGURATION_DESC', './index.php?module=Teams&action=tba'];
$admin_group_header[] = ['LBL_USERS_TITLE', '', false, $admin_option_defs, 'LBL_USERS_DESC'];


//system.
$admin_option_defs = [];
$admin_option_defs['Administration']['configphp_settings'] = ['Administration', 'icon' => 'sicon-settings', 'LBL_CONFIGURE_SETTINGS_TITLE', 'LBL_CONFIGURE_SETTINGS', './index.php?module=Configurator&action=EditView'];
$admin_option_defs['Administration']['import'] = ['Import', 'LBL_IMPORT_WIZARD', 'icon' => 'sicon-import', 'LBL_IMPORT_WIZARD_DESC', './index.php?module=Import&action=step1&import_module=Administration&from_admin_wizard=1'];
$admin_option_defs['Administration']['locale'] = ['Currencies', 'icon' => 'sicon-map-pin', 'LBL_MANAGE_LOCALE', 'LBL_LOCALE', './index.php?module=Administration&action=Locale&view=default'];

if (!isset($GLOBALS['sugar_config']['disable_uw_upload']) || !$GLOBALS['sugar_config']['disable_uw_upload']) {
    $admin_option_defs['Administration']['upgrade_wizard'] = ['Upgrade', 'icon' => 'sicon-upgrade', 'LBL_UPGRADE_WIZARD_TITLE', 'LBL_UPGRADE_WIZARD', './index.php?module=Administration&action=Upgrader'];
}

$admin_option_defs['Administration']['currencies_management'] = ['Currencies', 'icon' => 'sicon-currencies', 'LBL_MANAGE_CURRENCIES', 'LBL_CURRENCY', 'javascript:void(parent.SUGAR.App.router.navigate("Currencies", {trigger: true}));'];

$admin_option_defs['Administration']['languages'] = ['Currencies', 'icon' => 'sicon-languages', 'LBL_MANAGE_LANGUAGES', 'LBL_LANGUAGES', './index.php?module=Administration&action=Languages&view=default'];

$admin_option_defs['Administration']['repair'] = ['Repair', 'icon' => 'sicon-repair', 'LBL_UPGRADE_TITLE', 'LBL_UPGRADE', './index.php?module=Administration&action=Upgrade'];

$admin_option_defs['Administration']['global_search'] = ['icon_SearchForm', 'icon' => 'sicon-search', 'LBL_GLOBAL_SEARCH_SETTINGS', 'LBL_GLOBAL_SEARCH_SETTINGS_DESC', './index.php?module=Administration&action=GlobalSearchSettings'];

if (!isset($GLOBALS['sugar_config']['hide_admin_diagnostics']) || !$GLOBALS['sugar_config']['hide_admin_diagnostics']) {
    $admin_option_defs['Administration']['diagnostic'] = ['Diagnostic', 'icon' => 'sicon-diagnostics', 'LBL_DIAGNOSTIC_TITLE', 'LBL_DIAGNOSTIC_DESC', './index.php?module=Administration&action=Diagnostic'];
}

// Connector Integration
$admin_option_defs['Administration']['connector_settings'] = ['icon_Connectors', 'icon' => 'sicon-connectors', 'LBL_CONNECTOR_SETTINGS', 'LBL_CONNECTOR_SETTINGS_DESC', './index.php?module=Connectors&action=ConnectorSettings'];

$admin_option_defs['Administration']['tracker_settings'] = ['Trackers', 'icon' => 'sicon-tracker', 'LBL_TRACKER_SETTINGS', 'LBL_TRACKER_SETTINGS_DESC', './index.php?module=Trackers&action=TrackerSettings'];

$admin_option_defs['Administration']['scheduler'] = ['Schedulers', 'icon' => 'sicon-scheduler', 'LBL_SUGAR_SCHEDULER_TITLE', 'LBL_SUGAR_SCHEDULER', './index.php?module=Schedulers&action=index'];

$admin_option_defs['Administration']['pdfmanager'] = ['icon_PdfManager', 'icon' => 'sicon-pdf-manager', 'LBL_PDFMANAGER_SETTINGS', 'LBL_PDFMANAGER_SETTINGS_DESC', './index.php?module=PdfManager&action=index'];

$admin_option_defs['Administration']['archive_records'] = [
    'Administration',
    'icon' => 'sicon-archive',
    'LBL_DBARCHIVER_TITLE',
    'LBL_DBARCHIVER',
    'javascript:void(parent.SUGAR.App.router.navigate("DataArchiver", {trigger: true}));',
];

// Enable/Disable wireless modules
$admin_option_defs['Administration']['enable_wireless_modules'] = ['icon_AdminMobile', 'icon' => 'sicon-mobile', 'LBL_WIRELESS_MODULES_ENABLE', 'LBL_WIRELESS_MODULES_ENABLE_DESC', './index.php?module=Administration&action=EnableWirelessModules'];
$admin_option_defs['Administration']['web_logic_hooks'] = ['Administration', 'icon' => 'sicon-web-logic', 'LBL_WEB_LOGIC_HOOKS', 'LBL_WEB_LOGIC_HOOKS_DESC', 'javascript:void(parent.SUGAR.App.router.navigate("WebLogicHooks", {trigger: true}));'];


if (SugarOAuthServer::enabled()) {
    $admin_option_defs['Administration']['oauth'] = ['Password', 'icon' => 'sicon-oauth-key', 'LBL_OAUTH_TITLE', 'LBL_OAUTH', './index.php?module=OAuthKeys&action=index'];
}


$license_management = false;
if (!isset($GLOBALS['sugar_config']['hide_admin_licensing']) || !$GLOBALS['sugar_config']['hide_admin_licensing']) {
    $license_management = ['License', 'icon' => 'sicon-password-mgmt', 'LBL_MANAGE_LICENSE_TITLE', 'LBL_MANAGE_LICENSE', './index.php?module=Administration&action=LicenseSettings'];
}

$license_key = 'no_key';

$admin_option_defs['Administration']['content_security_policy'] = [
    'Administration',
    'icon' => 'sicon-content-security',
    'LBL_CSP_TITLE',
    'LBL_MANAGE_CSP',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/config/csp", {trigger: true}));',
];

$admin_option_defs['Administration']['update'] = ['sugarupdate', 'icon' => 'sicon-update', 'LBL_SUGAR_UPDATE_TITLE', 'LBL_SUGAR_UPDATE', './index.php?module=Administration&action=Updater'];
if (!empty($license->settings['license_latest_versions'])) {
    $encodedVersions = $license->settings['license_latest_versions'];
    $versions = unserialize(base64_decode($encodedVersions), ['allowed_classes' => false]);
    include 'sugar_version.php';

    if (!empty($versions)) {
        foreach ($versions as $version) {
            if (isset($version['version']) && compareVersions($version['version'], $sugar_version)) {
                $minorVersion = getMinorVersion($version['version']);
                if ($minorVersion > 0 && !isOnCloud()) {
                    // ignore minor releases for non on-demand instances
                    continue;
                }
                $admin_option_defs['Administration']['update'][] = 'red';
                if (!isset($admin_option_defs['Administration']['update']['additional_label'])) {
                    $admin_option_defs['Administration']['update']['additional_label'] = '(' . $version['version'] . ')';
                }
            }
        }
    }
}

$admin_option_defs['Administration']['license_management'] = $license_management;
$focus = Administration::getSettings();
$license_key = $focus->settings['license_key'];

$admin_group_header[] = ['LBL_ADMINISTRATION_HOME_TITLE', '', false, $admin_option_defs, 'LBL_ADMINISTRATION_HOME_DESC'];


//email manager.
$admin_option_defs = [];
$admin_option_defs['Emails']['mass_Email_config'] = ['EmailMan', 'icon' => 'sicon-email', 'LBL_MASS_EMAIL_CONFIG_TITLE', 'LBL_MASS_EMAIL_CONFIG_DESC', './index.php?module=EmailMan&action=config'];

$admin_option_defs['Campaigns']['campaignconfig'] = ['Campaigns', 'icon' => 'sicon-email-campaign', 'LBL_CAMPAIGN_CONFIG_TITLE', 'LBL_CAMPAIGN_CONFIG_DESC', './index.php?module=EmailMan&action=campaignconfig'];

$admin_option_defs['Emails']['mailboxes'] = ['InboundEmail', 'icon' => 'sicon-email-inbound', 'LBL_MANAGE_MAILBOX', 'LBL_MAILBOX_DESC', './index.php?module=InboundEmail&action=index'];
$admin_option_defs['Campaigns']['mass_Email'] = ['EmailMan', 'icon' => 'sicon-email-queue', 'LBL_MASS_EMAIL_MANAGER_TITLE', 'LBL_MASS_EMAIL_MANAGER_DESC', './index.php?module=EmailMan&action=index'];
$admin_option_defs['Emails']['history_contacts_emails'] = ['ConfigureTabs', 'icon' => 'sicon-email-contacts', 'LBL_HISTORY_CONTACTS_EMAILS', 'LBL_HISTORY_CONTACTS_EMAILS_DESC', './index.php?module=Configurator&action=historyContactsEmails'];

$admin_option_defs['Campaigns']['register_snip'] = ['icon_AdminThemes', 'icon' => 'sicon-email-archive', 'LBL_CONFIGURE_SNIP', 'LBL_CONFIGURE_SNIP_DESC', './index.php?module=SNIP&action=ConfigureSnip'];

$admin_group_header[] = ['LBL_EMAIL_TITLE', '', false, $admin_option_defs, 'LBL_EMAIL_DESC'];


//studio.
$admin_option_defs = [];
$admin_option_defs['studio']['studio'] = ['Studio', 'icon' => 'sicon-studio', 'LBL_STUDIO', 'LBL_STUDIO_DESC', './index.php?module=ModuleBuilder&action=index&type=studio'];
if (isset($GLOBALS['beanFiles']['iFrame'])) {
    $admin_option_defs['Administration']['portal'] = ['iFrames', 'icon' => 'sicon-my-sites', 'LBL_IFRAME', 'DESC_IFRAME', './index.php?module=iFrames&action=index'];
}
$admin_option_defs['Administration']['rename_tabs'] = [
    'Administration',
    'icon' => 'sicon-edit',
    'LBL_RENAME_TABS',
    'LBL_CHANGE_NAME_MODULES',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/module-names-and-icons", {trigger: true}));',
];
$admin_option_defs['Administration']['moduleBuilder'] = ['ModuleBuilder', 'icon' => 'sicon-module-builder', 'LBL_MODULEBUILDER', 'LBL_MODULEBUILDER_DESC', './index.php?module=ModuleBuilder&action=index&type=mb'];
$admin_option_defs['Administration']['configure_tabs'] = ['ConfigureTabs', 'icon' => 'sicon-display-modules', 'LBL_CONFIGURE_TABS_AND_SUBPANELS', 'LBL_CONFIGURE_TABS_AND_SUBPANELS_DESC', './index.php?module=Administration&action=ConfigureTabs'];
$admin_option_defs['Administration']['module_loader'] = ['ModuleLoader', 'icon' => 'sicon-module-loader', 'LBL_MODULE_LOADER_TITLE', 'LBL_MODULE_LOADER', './index.php?module=Administration&action=UpgradeWizard&view=module'];


$admin_option_defs['Administration']['config_prod_bar'] = ['icon_ShortcutBar', 'icon' => 'sicon-config-nav', 'LBL_CONFIGURE_SHORTCUT_BAR', 'LBL_CONFIGURE_SHORTCUT_BAR_DESC', './index.php?module=Administration&action=ConfigureShortcutBar'];

$admin_option_defs['any']['dropdowneditor'] = ['Dropdown', 'icon' => 'sicon-dropdown-editor', 'LBL_DROPDOWN_EDITOR', 'DESC_DROPDOWN_EDITOR', './index.php?module=ModuleBuilder&action=index&type=dropdowns'];

$admin_option_defs['Administration']['sugarportal'] = ['SugarPortal', 'icon' => 'sicon-portal', 'LBL_SUGARPORTAL', 'LBL_SUGARPORTAL_DESC', './index.php?module=ModuleBuilder&action=index&type=sugarportal'];

$admin_option_defs['any']['workflow_management'] = ['WorkFlow', 'icon' => 'sicon-workflow', 'LBL_MANAGE_WORKFLOW', 'LBL_WORKFLOW_DESC', './index.php?module=WorkFlow&action=ListView'];
$admin_option_defs['Administration']['api_platforms'] = [
    'Administration',
    'icon' => 'sicon-config-api',
    'LBL_CONFIGURE_CUSTOM_API_PLATFORMS',
    'LBL_CUSTOM_API_PLATFORMS_DESC',
    './index.php?module=Administration&action=apiplatforms',
];

$admin_option_defs['Administration']['styleguide'] = [
    'Documents',
    'icon' => 'sicon-lab',
    'LBL_MANAGE_STYLEGUIDE',
    'LBL_MANAGE_STYLEGUIDE_TITLE',
    'javascript:void(parent.SUGAR.App.router.navigate("Styleguide", {trigger: true}));',
];

$admin_option_defs['any']['denormalization'] = [
    'Administration',
    'icon' => 'sicon-relate-fields',
    'LBL_MANAGE_RELATE_DENORMALIZATION_TITLE',
    'LBL_MANAGE_RELATE_DENORMALIZATION_DESC',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/denormalization", {trigger: true}));',
];
if ($current_user && !$current_user->hasLicenses([Subscription::SUGAR_SELL_ESSENTIALS_KEY])) {
    $admin_option_defs['any']['sugarOutfitters'] = [
        'Administration',
        'icon' => 'sicon-marketplace',
        'LBL_SUGAR_OUTFITTER',
        'LBL_SUGAR_OUTFITTER_TOOLTIP',
        'https://www.sugaroutfitters.com/',
        null,
        null,
        '_blank',
    ];
}

$admin_group_header[] = ['LBL_STUDIO_TITLE', '', false, $admin_option_defs, 'LBL_TOOLS_DESC'];


//product catalog.

$admin_option_defs = [];
$admin_option_defs['Products']['product_catalog'] = ['Products', 'icon' => 'sicon-catalog', 'LBL_PRODUCTS_TITLE', 'LBL_PRODUCTS', 'javascript:void(parent.SUGAR.App.router.navigate("ProductTemplates", {trigger: true}));'];
$admin_option_defs['Products']['manufacturers'] = ['Manufacturers', 'icon' => 'sicon-manufacturers', 'LBL_MANUFACTURERS_TITLE', 'LBL_MANUFACTURERS', 'javascript:void(parent.SUGAR.App.router.navigate("Manufacturers", {trigger: true}));'];
$admin_option_defs['Products']['product_categories'] = ['Product_Categories', 'icon' => 'sicon-filter', 'LBL_PRODUCT_CATEGORIES_TITLE', 'LBL_PRODUCT_CATEGORIES', 'javascript:void(parent.SUGAR.App.router.navigate("ProductCategories", {trigger: true}));'];
$admin_option_defs['Products']['shipping_providers'] = ['Shippers', 'icon' => 'sicon-shippers', 'LBL_SHIPPERS_TITLE', 'LBL_SHIPPERS', 'javascript:void(parent.SUGAR.App.router.navigate("Shippers", {trigger: true}));'];
$admin_option_defs['Products']['product_types'] = ['Product_Types', 'icon' => 'sicon-product-types', 'LBL_PRODUCT_TYPES_TITLE', 'LBL_PRODUCT_TYPES', 'javascript:void(parent.SUGAR.App.router.navigate("ProductTypes", {trigger: true}));'];

$admin_option_defs['Quotes']['tax_rates'] = ['TaxRates', 'icon' => 'sicon-tax-rates', 'LBL_TAXRATES_TITLE', 'LBL_TAXRATES', 'javascript:void(parent.SUGAR.App.router.navigate("TaxRates", {trigger: true}));'];
$admin_option_defs['Quotes']['quotes_config'] = [
    'Quotes',
    'icon' => 'sicon-quotes',
    'LBL_MANAGE_QUOTES_TITLE',
    'LBL_MANAGE_QUOTES',
    'javascript:void(parent.SUGAR.App.router.navigate("Quotes/config", {trigger: true}));',
];

$admin_group_header[] = ['LBL_PRICE_LIST_TITLE', '', false, $admin_option_defs, 'LBL_PRICE_LIST_DESC'];

// AWS Configuration for Serve and Sell only
if ($focus->isLicensedForServe() || $focus->isLicensedForSell()) {
    $admin_option_defs = [];
    $admin_option_defs['Administration']['connect'] = [
        'Administration',
        'icon' => 'sicon-amazon',
        'LBL_AWS_CONNECT_TITLE',
        'LBL_AWS_CONNECT_DESCR',
        'javascript:void(parent.SUGAR.App.router.navigate("Administration/config/aws", {trigger: true}));',
    ];
    $admin_group_header[] = ['LBL_AWS', '', false, $admin_option_defs, 'LBL_AWS_DESCR'];
}

//Maps
if (hasMapsLicense()) {
    $admin_option_defs = [];
    $admin_option_defs['Administration']['maps'] = [
        'Administration',
        'icon' => 'sicon-map-pin',
        'LBL_MAPS_ADMIN_CONFIG_TITLE',
        'LBL_MAPS_ADMIN_CONFIG_DESCRIPTION',
        'javascript:void(parent.SUGAR.App.router.navigate("Administration/config/maps", {trigger: true}));',
    ];
    $admin_option_defs['Administration']['maps-logger'] = [
        'Administration',
        'icon' => 'sicon-preview',
        'LBL_MAPS_ADMIN_LOG_VIEWER',
        'LBL_MAPS_ADMIN_CONFIG_LOG_VIEWER_DESCRIPTION',
        'javascript:void(parent.SUGAR.App.router.navigate("Administration/config/maps-logger", {trigger: true}));',
    ];
    $admin_group_header[] = [
        'LBL_MAPS_ADMIN_CONFIG_TITLE',
        '',
        false,
        $admin_option_defs,
        'LBL_MAPS_ADMIN_CONFIG_DESCRIPTION',
        'LBL_MAPS_ADMIN_CONFIG_DESCRIPTION',
    ];
}

//bug tracker.
$admin_option_defs = [];
$admin_option_defs['Bugs']['bug_tracker'] = ['Releases', 'icon' => 'sicon-bug', 'LBL_MANAGE_RELEASES', 'LBL_RELEASE', './index.php?module=Releases&action=index'];
$admin_group_header[] = ['LBL_BUG_TITLE', '', false, $admin_option_defs, 'LBL_BUG_DESC'];

//Forecasting
$admin_option_defs = [];
$admin_option_defs['Forecasts']['forecast_setup'] = ['ForecastReports', 'icon' => 'sicon-forecasts', 'LBL_MANAGE_FORECASTS_TITLE', 'LBL_MANAGE_FORECASTS', 'javascript:void(parent.SUGAR.App.router.navigate("Forecasts/config", {trigger: true}));'];
$admin_group_header[] = ['LBL_FORECAST_TITLE', '', false, $admin_option_defs, 'LBL_FORECAST_DESC'];

//Opportunities
$admin_option_defs = [];
$admin_option_defs['Opportunities']['opportunities_setup'] = ['Opportunities', 'icon' => 'sicon-opportunities', 'LBL_MANAGE_OPPORTUNITIES_TITLE', 'LBL_MANAGE_OPPORTUNITIES_DESC', 'javascript:void(parent.SUGAR.App.router.navigate("Opportunities/config", {trigger: true}));'];
$admin_group_header[] = ['LBL_MANAGE_OPPORTUNITIES_TITLE', '', false, $admin_option_defs, 'LBL_OPPORTUNITIES_DESC'];

//Contracts
$admin_option_defs = [];
$admin_option_defs['Contracts']['contract_type_management'] = ['Contracts', 'icon' => 'sicon-contracts', 'LBL_MANAGE_CONTRACTEMPLATES_TITLE', 'LBL_CONTRACT_TYPES', 'javascript:void(parent.SUGAR.App.router.navigate("ContractTypes", {trigger: true}));'];

// fetch "Contracts" module name from localization data (bug #46740)
$admin_group_header[] = ['LBL_CONTRACTS_TITLE', '', false, $admin_option_defs, 'LBL_CONTRACT_DESC'];


$admin_option_defs = [
    'pmse_Project' => [
        'CasesList' => [
            'CasesList',
            'icon' => 'sicon-refresh',
            'LBL_PMSE_ADMIN_TITLE_CASESLIST',
            'LBL_PMSE_ADMIN_DESC_CASESLIST',
            'javascript:void(parent.SUGAR.App.router.navigate("pmse_Inbox/layout/casesList", {trigger: true}));',
        ],
        'EngineLogs' => [
            'EngineLogs',
            'icon' => 'sicon-log-viewer',
            'LBL_PMSE_ADMIN_TITLE_ENGINELOGS',
            'LBL_PMSE_ADMIN_DESC_ENGINELOGS',
            'javascript:void(parent.SUGAR.App.router.navigate("pmse_Inbox/layout/logView", {trigger: true}));',
        ],
    ],
];
$admin_group_header [] = [
    'LBL_SUGARBPM_TITLE',
    '',
    false,
    $admin_option_defs,
    'LBL_SUGARBPM_DESC',
];


$admin_option_defs = [];
$admin_option_defs['Administration']['PipelineSettingsPanel'] = [
    'Administration',
    'icon' => 'sicon-tile-view',
    'LBL_PIPELINE_LINK_NAME',
    'LBL_PIPELINE_LINK_DESCRIPTION',
    'javascript:void(parent.SUGAR.App.router.navigate("VisualPipeline/config", {trigger: true}));',
];

$admin_group_header[] = [
    'LBL_PIPELINE_SECTION_HEADER',
    '',
    false,
    $admin_option_defs,
    'LBL_PIPELINE_SECTION_DESCRIPTION',
];

if (hasHintLicense()) {
    $admin_option_defs = [];
    $admin_option_defs['Administration']['hint_data_enrichment'] = [
        //Icon name. Available icons are located in ./themes/default/images
        'Administration',
        'icon' => 'sicon-dropdown-editor',
        'LBL_HINT_NAME',
        'LBL_HINT_DESCRIPTION',
        'javascript:void(parent.SUGAR.App.router.navigate("hint/data-enrichment", {trigger: true}));',
    ];

    $admin_option_defs['Administration']['hint_resync'] = [
        false,
        'icon' => 'sicon-refresh',
        'LBL_HINT_RESYNC',
        'LBL_HINT_RESYNC_DESCRIPTION',
        'javascript:void(parent.SUGAR.App.router.navigate("hint/insights/resync", {trigger: true}));',
    ];

    $admin_option_defs['Administration']['hint_config'] = [
        'Administration',
        'icon' => 'sicon-settings',
        'LBL_HINT_CONFIG_NAME',
        'LBL_HINT_CONFIG_DESCRIPTION',
        'javascript:void(parent.SUGAR.App.router.navigate("hint/config", {trigger: true}));',
    ];

    $admin_group_header[] = [
        'LBL_HINT_SECTION_HEADER',
        '',
        false,
        $admin_option_defs,
        'LBL_HINT_SECTION_DESCRIPTION',
    ];
}

$admin_option_defs = [];
$admin_option_defs['DocuSignEnvelopes']['docusign-settings'] = [
    'Administration',
    'icon' => 'sicon-settings',
    'LBL_DOCUSIGN_NAME',
    'LBL_DOCUSIGN_TOOLTIP',
    'javascript:void(parent.SUGAR.App.router.navigate("DocuSign/settings", {trigger: true}));',
];

$admin_group_header[] = [
    'LBL_DOCUSIGN_GROUP',
    '',
    false,
    $admin_option_defs,
    'LBL_DOCUSIGN_DESCRIPTION',
];

$admin_option_defs = [];
$admin_option_defs['CloudDrivePaths']['google_drive'] = [
    'Administration',
    'icon' => 'sicon-settings',
    'LBL_GOOGLE_DRIVE_NAME',
    'LBL_GOOGLE_DRIVE_TOOLTIP',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/drive-path/google", {trigger: true}));',
];
$admin_option_defs['CloudDrivePaths']['microsoft_onedrive'] = [
    'Administration',
    'icon' => 'sicon-settings',
    'LBL_MICROSOFT_ONEDRIVE',
    'LBL_MICROSOFT_ONEDRIVE_TOOLTIP',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/drive-path/onedrive", {trigger: true}));',
];
$admin_option_defs['CloudDrivePaths']['microsoft_dropbox'] = [
    'Administration',
    'icon' => 'sicon-settings',
    'LBL_DROPBOX_DRIVE',
    'LBL_DROPBOX_DRIVE_TOOLTIP',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/drive-path/dropbox", {trigger: true}));',
];
$admin_option_defs['CloudDrivePaths']['microsoft_sharepoint'] = [
    'Administration',
    'icon' => 'sicon-settings',
    'LBL_SHAREPOINT_DRIVE',
    'LBL_SHAREPOINT_DRIVE_TOOLTIP',
    'javascript:void(parent.SUGAR.App.router.navigate("Administration/drive-path/sharepoint", {trigger: true}));',
];

$admin_group_header[] = [
    'LBL_CLOUD_DRIVE',
    '',
    false,
    $admin_option_defs,
    'LBL_CLOUD_DRIVE_DESCRIPTION',
];

if (SugarAutoLoader::existing('custom/modules/Administration/Ext/Administration/administration.ext.php')) {
    include 'custom/modules/Administration/Ext/Administration/administration.ext.php';
}

//For users with MLA access we need to find which entries need to be shown.
//lets process the $admin_group_header and apply all the access control rules.
$access = $current_user->getDeveloperModules();
foreach ($admin_group_header as $key => $values) {
    $module_index = array_keys($values[3]);  //get the actual links..
    foreach ($module_index as $mod_key => $mod_val) {
        if (is_admin($current_user) ||
            safeInArray($mod_val, $access) ||
            $mod_val == 'studio' ||
            ($mod_val == 'Forecasts') ||
            ($mod_val == 'any')
        ) {
            if (!is_admin($current_user) && isset($values[3]['Administration'])) {
                unset($values[3]['Administration']);
            }
            if (displayStudioForCurrentUser() == false) {
                unset($values[3]['studio']);
            }

            if (displayWorkflowForCurrentUser() == false) {
                unset($admin_group_header[$key][3]['any']['workflow_management']);
            }

            // Need this check because Quotes and Products share the header group
            if (!safeInArray('Quotes', $access) && isset($values[3]['Quotes'])) {
                unset($values[3]['Quotes']);
            }
            if (!safeInArray('Products', $access) && isset($values[3]['Products'])) {
                unset($values[3]['Products']);
            }

            // Need this check because Emails and Campaigns share the header group
            if (!safeInArray('Campaigns', $access) && isset($values[3]['Campaigns'])) {
                unset($values[3]['Campaigns']);
            }

            // Unless a user is a system admin, or module admin, they cannot see Forecasts config links
            if ($mod_val == 'Forecasts'
                && !($current_user->isAdmin() || $current_user->isDeveloperForModule('Forecasts'))
                && isset($values[3]['Forecasts'])) {
                unset($admin_group_header[$key][3][$mod_val]);
            }
            // Unless a user is a system admin, or module admin, they cannot see TBACLs config links
            if ($mod_val == 'Users'
                && !$current_user->isAdminForModule('Users')
                && isset($values[3]['Users']['tba_management'])
            ) {
                unset($admin_group_header[$key][3][$mod_val]['tba_management']);
            }

            // Maintain same access for Opps as we have for Forecasts
            // Unless a user is a system admin, or module admin, they cannot see Forecasts config links
            if ($mod_val == 'Opportunities'
                && !($current_user->isAdmin() || $current_user->isDeveloperForModule('Opportunities'))
                && isset($values[3]['Opportunities'])) {
                unset($admin_group_header[$key][3][$mod_val]);
            }

            // Unless a user is a system admin, or module admin, they cannot see DocuSign config links
            $adminOptionDefsKey = 3;
            if ($mod_val === 'DocuSignEnvelopes'
                && !($current_user->isAdmin() || $current_user->isDeveloperForModule('DocuSignEnvelopes'))
                && isset($values[$adminOptionDefsKey]['DocuSignEnvelopes'])) {
                unset($admin_group_header[$key][$adminOptionDefsKey][$mod_val]);
            }

            if ($mod_val === 'CloudDrivePaths'
                && !($current_user->isAdmin() || $current_user->isDeveloperForModule('CloudDrivePaths'))
                && isset($values[$adminOptionDefsKey]['CloudDrivePaths'])) {
                unset($admin_group_header[$key][$adminOptionDefsKey][$mod_val]);
            }
        } else {
            //hide the link
            unset($admin_group_header[$key][3][$mod_val]);
        }
    }
}

// Sugar Automate Administration Settings
if ($current_user->isAdmin() && hasAutomateLicense()) {
    $admin_option_defs = [];

    $admin_option_defs['DRI_Workflows']['dri_customer_journey_templates'] = [
        'customer_journey_workflow_templates',
        'LBL_DRI_CUSTOMER_JOURNEY_TEMPLATES_LINK_NAME',
        'LBL_DRI_CUSTOMER_JOURNEY_TEMPLATES_LINK_DESC',
        'javascript:void(parent.SUGAR.App.router.navigate("DRI_Workflow_Templates", {trigger: true}));',
    ];

    $admin_option_defs['Administration']['dri_customer_journey_configure_modules'] = [
        'customer_journey_configure_modules',
        'LBL_DRI_CUSTOMER_JOURNEY_CONFIGURE_MODULES_LINK_NAME',
        'LBL_DRI_CUSTOMER_JOURNEY_CONFIGURE_MODULES_LINK_DESC',
        'javascript:void(parent.SUGAR.App.router.navigate("DRI_Workflows/layout/configure-modules", {trigger: true}));',
    ];

    $admin_group_header[] = [
        'LBL_DRI_CUSTOMER_JOURNEY_SETTINGS_TITLE',
        '',
        false,
        $admin_option_defs,
        'LBL_DRI_CUSTOMER_JOURNEY_SETTINGS_DESC',
    ];
}
