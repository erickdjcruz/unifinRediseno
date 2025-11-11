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

global $current_user;

$idpConfig = new Authentication\Config(\SugarConfig::getInstance());

$moduleName = 'Administration';
$adminRoute = '#bwc/index.php?module=Administration&action=';
$viewdefs[$moduleName]['base']['menu']['sweetspot'] = [
    // Users and security
    // User Management
    [
        'label' => 'LBL_MANAGE_USERS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Users',
    ],
    // Role Management
    [
        'label' => 'LBL_MANAGE_ROLES_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=ACLRoles&action=index',
    ],
    // Team Management
    [
        'label' => 'LBL_MANAGE_TEAMS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Teams&action=index',
    ],

    // Team-based Permissions.
    [
        'label' => 'LBL_TBA_CONFIGURATION',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Teams&action=tba',
    ],

    // Password Management
    [
        'label' => 'LBL_MANAGE_PASSWORD_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'PasswordManager',
        'idm_mode_link' =>
            $idpConfig->isIDMModeEnabled() ? $idpConfig->buildCloudConsoleUrl('passwordManagement') : null,
    ],

    // Sugar Connect
    // License Management
    [
        'label' => 'LBL_MANAGE_LICENSE_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'LicenseSettings',
    ],
    // Sugar Updates
    [
        'label' => 'LBL_SUGAR_UPDATE_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'Updater',
    ],

    // System
    // System settings
    [
        'label' => 'LBL_CONFIGURE_SETTINGS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Configurator&action=EditView',
    ],
    // Import Wizard
    [
        'label' => 'LBL_IMPORT_WIZARD',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Import&action=step1&import_module=Administration',
    ],

    // Locale
    [
        'label' => 'LBL_MANAGE_LOCALE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'Locale&view=default',
    ],

    // Currencies
    [
        'label' => 'LBL_MANAGE_CURRENCIES',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Currencies',
    ],

    // Languages
    [
        'label' => 'LBL_MANAGE_LANGUAGES',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'Languages&view=default',
    ],
    // Repair
    [
        'label' => 'LBL_UPGRADE_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'Upgrade',
    ],
    // -- Quick Repair and Rebuild
    [
        'label' => 'LBL_QUICK_REPAIR_AND_REBUILD',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'repair',
    ],

    // Search
    [
        'label' => 'LBL_GLOBAL_SEARCH_SETTINGS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'GlobalSearchSettings',
    ],
    // Diagnostic Tool
    [
        'label' => 'LBL_DIAGNOSTIC_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'Diagnostic',
    ],

    // Connectors
    [
        'label' => 'LBL_CONNECTOR_SETTINGS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Connectors&action=ConnectorSettings',
    ],
    // Tracker
    [
        'label' => 'LBL_TRACKER_SETTINGS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Trackers&action=TrackerSettings',
    ],

    // Scheduler
    [
        'label' => 'LBL_REBUILD_SCHEDULERS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Schedulers&action=index',
    ],
    // PDF Manager
    [
        'label' => 'LBL_PDFMANAGER_SETTINGS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=PdfManager&action=index',
    ],
    // Mobile
    [
        'label' => 'LBL_WIRELESS_MODULES_ENABLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'EnableWirelessModules',
    ],
    // Web Logic Hooks
    [
        'label' => 'LBL_WEB_LOGIC_HOOKS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#WebLogicHooks',
    ],
    // OAuth Keys
    [
        'label' => 'LBL_OAUTH_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=OAuthKeys&action=index',
    ],

    // Email
    // Email Settings
    [
        'label' => 'LBL_MASS_EMAIL_CONFIG_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=EmailMan&action=config',
    ],
    // Imbound Email
    [
        'label' => 'LBL_INBOUND_EMAIL_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=InboundEmail&action=index',
    ],

    // Related Contacts Emails
    [
        'label' => 'LBL_HISTORY_CONTACTS_EMAILS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Configurator&action=historyContactsEmails',
    ],
    // Campaign Email Settings
    [
        'label' => 'LBL_CAMPAIGN_CONFIG_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=EmailMan&action=campaignconfig',
    ],

    // Email Queue
    [
        'label' => 'LBL_MASS_EMAIL_MANAGER_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=EmailMan&action=index',
    ],
    // Email Archiving
    [
        'label' => 'LBL_CONFIGURE_SNIP',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=SNIP&action=ConfigureSnip',
    ],

    // Developer Tools
    // Studio
    [
        'label' => 'LBL_STUDIO',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=ModuleBuilder&action=index&type=studio',
    ],
    // Modules Name and Icons
    [
        'label' => 'LBL_RENAME_TABS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Administration/module-names-and-icons',
    ],

    // Module Builder
    [
        'label' => 'LBL_MODULEBUILDER',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=ModuleBuilder&action=index&type=mb',
    ],
    // Navigation Bar and Subpanels
    [
        'label' => 'LBL_CONFIG_TABS',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'ConfigureTabs',
    ],

    // Module Loader
    [
        'label' => 'LBL_MODULE_LOADER_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'UpgradeWizard&view=module',
    ],
    // Configure Navigation Bar Quick Create
    [
        'label' => 'LBL_CONFIGURE_SHORTCUT_BAR',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => $adminRoute . 'ConfigureShortcutBar',
    ],
    // Sugar Portal
    [
        'label' => 'LBL_SUGARPORTAL',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=ModuleBuilder&action=index&type=sugarportal',
    ],
    // Styleguide
    [
        'label' => 'LBL_MANAGE_STYLEGUIDE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Styleguide',
    ],

    // Dropdown Editor
    [
        'label' => 'LBL_DROPDOWN_EDITOR',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=ModuleBuilder&action=index&type=dropdowns',
    ],
    // Workflow Management
    [
        'label' => 'LBL_WORKFLOW_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=WorkFlow&action=ListView',
    ],

    // Product Catalog
    [
        'label' => 'LBL_PRODUCTS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#ProductTemplates',
    ],
    // Manufacturers
    [
        'label' => 'LBL_MANUFACTURERS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Manufacturers',
    ],

    // Product Categories
    [
        'label' => 'LBL_PRODUCT_CATEGORIES_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#ProductCategories',
    ],
    // Shipping Providers
    [
        'label' => 'LBL_SHIPPERS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#Shippers',
    ],

    // Product Types
    [
        'label' => 'LBL_PRODUCT_TYPES_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#ProductTypes',
    ],
    // Tax Rates
    [
        'label' => 'LBL_TAXRATES_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#TaxRates',
    ],

    // Releases
    [
        'label' => 'LBL_MANAGE_RELEASES',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#bwc/index.php?module=Releases&action=index',
    ],

    // Contract Types
    [
        'label' => 'LBL_MANAGE_CONTRACTEMPLATES_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#ContractTypes',
    ],

    // Process Management
    [
        'label' => 'LBL_PMSE_ADMIN_TITLE_CASESLIST',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'route' => '#pmse_Inbox/layout/casesList',
    ],
];

// Sugar Cloud Settings
if ($idpConfig->isIDMModeEnabled()) {
    $userId = $GLOBALS['current_user'] && $GLOBALS['current_user']->id ? $GLOBALS['current_user']->id : '';
    $viewdefs[$moduleName]['base']['menu']['sweetspot'][] = [
        'label' => 'LBL_SUGAR_CLOUD_SETTINGS_TITLE',
        'acl_action' => 'studio',
        'module' => $moduleName,
        'icon' => 'sicon-settings',
        'idm_mode_link' => $idpConfig->buildCloudConsoleUrl('/', [], $userId),
    ];
}

// SugarOutfitters
if ($current_user && !$current_user->hasLicenses([Subscription::SUGAR_SELL_ESSENTIALS_KEY])) {
    $viewdefs[$moduleName]['base']['menu']['sweetspot'][] = [
        'label' => 'LBL_SUGAR_OUTFITTER',
        'icon' => 'sicon-marketplace',
        'route' => 'https://www.sugaroutfitters.com/',
        'openwindow' => true,
    ];
}
