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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Command\StateAwareRebuild;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\Util\Arrays\ArrayFunctions\ArrayFunctions;
use Sugarcrm\Sugarcrm\Security\Context;
use Sugarcrm\Sugarcrm\Security\Subject\Installer;
use Sugarcrm\Sugarcrm\Denormalization\Relate\FieldConfig;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;

use Sugarcrm\Sugarcrm\ProcessManager\Registry;


// This file will load the configuration settings from session data,
// write to the config file, and execute any necessary database steps.
$GLOBALS['installing'] = true;
if (!isset($install_script) || !$install_script) {
    die($mod_strings['ERR_NO_DIRECT_SCRIPT']);
}
ini_set('output_buffering', '0');

// Disable zlib compression as this will interfere with live scrolling
ini_set('zlib.output_compression', '0');

// Give the install ample time to finish
set_time_limit(3600);

// Implicitly set character set to skip browser buffer sniffing
header('Content-Type: text/html; charset=UTF-8');

// Bypass output buffering if enforced by FastCGI implementation
header('X-Accel-Buffering: no');

// Flush after each output so the user can see the progress in real-time
ob_implicit_flush();

// When output_buffering is enabled - which is recommended in production -
// make sure we flush the current output buffer(s) otherwise we are still
// buffering at this point and real-time updates wont make it to the screen.
while (@ob_end_flush()) {
}

require_once 'install/install_utils.php';

// set installer subject
$context = Container::getInstance()->get(Context::class);
$subject = new Installer();
$context->activateSubject($subject);

// since we need to make sure we have even the custom tabledictionary items in there
$mi = new ModuleInstaller();
$mi->silent = true;
$mi->rebuild_tabledictionary();
$mi->rebuild_vardefs();

MetaDataManager::disableCache();

include 'modules/Trackers/tracker_perfMetaData.php';
include 'modules/Trackers/tracker_queriesMetaData.php';
include 'modules/Trackers/tracker_sessionsMetaData.php';
include 'modules/Trackers/tracker_tracker_queriesMetaData.php';
require_once 'modules/TableDictionary.php';

$trackerManager = TrackerManager::getInstance();
$trackerManager->pause();


$cache_dir = sugar_cached('');
$line_entry_format = '&nbsp&nbsp&nbsp&nbsp&nbsp<b>';
$line_exit_format = '... &nbsp&nbsp</b>';
$rel_dictionary = $dictionary; // sourced by modules/TableDictionary.php
$render_table_close = '';
$render_table_open = '';
$setup_db_admin_password = $_SESSION['setup_db_admin_password'];
$setup_db_admin_user_name = $_SESSION['setup_db_admin_user_name'];
$setup_db_create_database = $_SESSION['setup_db_create_database'];
$setup_db_create_sugarsales_user = $_SESSION['setup_db_create_sugarsales_user'];
$setup_db_database_name = $_SESSION['setup_db_database_name'];
$setup_db_drop_tables = $_SESSION['setup_db_drop_tables'];
$setup_db_host_instance = $_SESSION['setup_db_host_instance'];
$setup_db_port_num = $_SESSION['setup_db_port_num'];
$setup_db_host_name = $_SESSION['setup_db_host_name'];
$demoData = $_SESSION['demoData'];
$setup_db_sugarsales_password = $_SESSION['setup_db_sugarsales_password'];
$setup_db_sugarsales_user = $_SESSION['setup_db_sugarsales_user'];
$setup_site_admin_user_name = $_SESSION['setup_site_admin_user_name'];
$setup_site_admin_password = $_SESSION['setup_site_admin_password'];
$setup_site_admin_email = $_SESSION['setup_site_admin_email'] ?? '';
$setup_site_guid = (isset($_SESSION['setup_site_specify_guid']) && $_SESSION['setup_site_specify_guid'] != '') ? $_SESSION['setup_site_guid'] : '';
$setup_site_url = $_SESSION['setup_site_url'];
$parsed_url = parse_url($setup_site_url);
$setup_site_host_name = $parsed_url['host'];
$setup_site_log_dir = isset($_SESSION['setup_site_custom_log_dir']) ? $_SESSION['setup_site_log_dir'] : '.';
$setup_site_log_file = 'sugarcrm.log';  // may be an option later
$setup_site_session_path = isset($_SESSION['setup_site_custom_session_path']) ? $_SESSION['setup_site_session_path'] : '';
$setup_site_log_level = 'fatal';

$langHeader = get_language_header();
$out = <<<EOQ
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html {$langHeader}>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta http-equiv="Content-Script-Type" content="text/javascript">
   <meta http-equiv="Content-Style-Type" content="text/css">
    <title>{$mod_strings['LBL_WIZARD_TITLE']} {$mod_strings['LBL_PERFORM_TITLE']}</title>
   <link REL="SHORTCUT ICON" HREF="$icon">
   <link rel="stylesheet" href="$css" type="text/css" />
   <script type="text/javascript" src="$common"></script>
</head>
<body onload="javascript:document.getElementById('button_next2').focus();">
<table cellspacing="0" cellpadding="0" border="0" align="center" class="shell">
      <tr><td colspan="2" id="help"><a href="{$help_url}" target='_blank'>{$mod_strings['LBL_HELP']} </a></td></tr>
    <tr>
      <th width="500">
		<p>
		<img src="{$sugar_md}" alt="SugarCRM" border="0">
		</p>
		{$mod_strings['LBL_PERFORM_TITLE']}</th>
    <th width="200" style="text-align: right;"><a href="http://www.sugarcrm.com" target="_blank">
    <img src="{$loginImage}" alt="SugarCRM" border="0" class="sugarcrm-logo"></a></th>
</tr>
<tr>
   <td colspan="2">
EOQ;
echo $out;
installLog('calling handleSugarConfig()');
$bottle = handleSugarConfig();

$server_software = $_SERVER['SERVER_SOFTWARE'];
if (strpos($server_software, 'Microsoft-IIS') !== false) {
    installLog('calling handleWebConfig()');
    handleWebConfig();
} else {
    installLog('calling handleHtaccess()');
    handleHtaccess();
}

///////////////////////////////////////////////////////////////////////////////
////    START TABLE STUFF
echo '<br>';
echo "<b>{$mod_strings['LBL_PERFORM_TABLES']}</b>";
echo '<br>';

// create the SugarCRM database
if ($setup_db_create_database) {
    installLog('calling handleDbCreateDatabase()');
    installerHook('pre_handleDbCreateDatabase');
    handleDbCreateDatabase();
    installerHook('post_handleDbCreateDatabase');
} else {
    // ensure the charset and collation are utf8
    installLog('calling handleDbCharsetCollation()');
    installerHook('pre_handleDbCharsetCollation');
    handleDbCharsetCollation();
    installerHook('post_handleDbCharsetCollation');
}

// create the SugarCRM database user
if ($setup_db_create_sugarsales_user) {
    installerHook('pre_handleDbCreateSugarUser');
    handleDbCreateSugarUser();
    installerHook('post_handleDbCreateSugarUser');
}

foreach ($beanFiles as $bean => $file) {
    require_once $file;
}

// load up the config_override.php file.
// This is used to provide default user settings
if (is_file('config_override.php')) {
    require_once 'config_override.php';
}

$db = DBManagerFactory::getInstance();
//Call preInstall function
$db->preInstall();
$startTime = microtime(true);
$focus = 0;
$processed_tables = []; // for keeping track of the tables we have worked on
$empty = '';
$new_tables = 1; // is there ever a scenario where we DON'T create the admin user?
$new_config = 1;
$new_report = 1;

// add non-module Beans to this array to keep the installer from erroring.
$nonStandardModules = [//'Tracker',
];

// TODO: Remove the following. (See MAR-1314)
// Disable the activity stream from creating messages while installing.
Activity::disable();
// Disable processes for the time being
Registry\Registry::getInstance()->set('setup:disable_processes', true);

/**
 * loop through all the Beans and create their tables
 */
installLog('looping through all the Beans and create their tables');
//start by clearing out the vardefs
VardefManager::clearVardef();

// Create default relationships for Sugar Automate
require 'install/CustomerJourney/CreateDefaultRelationships.php';

//Initialize languages as some Beans need app_strings to be loaded in constructor
$app_strings = return_application_language('en_us');
installerHook('pre_createAllModuleTables');
foreach ($beanFiles as $bean => $file) {
    $doNotInit = ['Scheduler', 'SchedulersJob', 'ProjectTask'];

    if (in_array($bean, $doNotInit)) {
        $focus = new $bean(false);
    } else {
        $focus = new $bean();
    }

    if ($bean == 'Configurator') {
        continue;
    }

    $table_name = $focus->table_name;
    installLog('processing table ' . $focus->table_name);
    // check to see if we have already setup this table
    if (!in_array($table_name, $processed_tables)) {
        if (!file_exists('modules/' . $focus->module_dir . '/vardefs.php')) {
            continue;
        }
        if (!in_array($bean, $nonStandardModules)) {
            require_once 'modules/' . $focus->module_dir . '/vardefs.php'; // load up $dictionary
            if ($dictionary[$focus->object_name]['table'] == 'does_not_exist') {
                continue; // support new vardef definitions
            }
        } else {
            continue; //no further processing needed for ignored beans.
        }

        // table has not been setup...we will do it now and remember that
        $processed_tables[] = $table_name;

        $focus->db->database = $db->database; // set db connection so we do not need to reconnect

        if ($setup_db_drop_tables) {
            drop_table_install($focus);
            installLog('dropping table ' . $focus->table_name);
        }

        if (create_table_if_not_exist($focus)) {
            installLog('creating table ' . $focus->table_name);
            if ($bean == 'User') {
                $new_tables = 1;
            }
            if ($bean == 'Administration') {
                $new_config = 1;
            }


            if ($bean == 'SavedReport') {
                $new_report = 1;
            }
        }

        installLog('creating Relationship Meta for ' . $focus->getObjectName());
        installerHook('pre_createModuleTable', ['module' => $focus->getObjectName()]);
        installerHook('post_createModuleTable', ['module' => $focus->getObjectName()]);
        echo '.';
    } // end if()
}
installerHook('post_createAllModuleTables');

// re-add this back to re-generate everything here $mi is created above
$mi->silent = true;
$mi->rebuild_extensions();

echo '<br>';
////    END TABLE STUFF

///////////////////////////////////////////////////////////////////////////////
////    START RELATIONSHIP CREATION

ksort($rel_dictionary);
foreach ($rel_dictionary as $rel_name => $rel_data) {
    $table = $rel_data['table'];

    if ($setup_db_drop_tables) {
        if ($db->tableExists($table)) {
            $db->dropTableName($table);
        }
    }

    if (!$db->tableExists($table)) {
        $db->createTableParams(
            $table,
            $rel_data['fields'],
            $rel_data['indices'] ?? []
        );
    }
}

// write init product definition
(new \Sugarcrm\Sugarcrm\ProductDefinition\Config\InitProductDefinition())->setDefaultProductDefinition();

// Setup the relationship cache and build out the initial vardef cache
SugarRelationshipFactory::rebuildCache();

// rebuild denormalized team security data at the earliest possible stage, if needed
$command = Container::getInstance()->get(StateAwareRebuild::class);
$command();

///////////////////////////////////////////////////////////////////////////////
////    START CREATE DEFAULTS
echo '<br>';
echo "<b>{$mod_strings['LBL_PERFORM_CREATE_DEFAULT']}</b><br>";
echo '<br>';
installLog('Begin creating Defaults');
installerHook('pre_createDefaultSettings');
if ($new_config) {
    installLog('INSERT defaults INTO config TABLE');
    insert_default_settings();
}
installerHook('post_createDefaultSettings');

$oe = new OutboundEmail();
$oe->getSystemMailerSettings();

$KBContent = new KBContent();
$KBContent->setupPrimaryLanguage();
$KBContent->setupCategoryRoot();

echo $line_entry_format . $mod_strings['LBL_PERFORM_LICENSE_SETTINGS'] . $line_exit_format;
installLog($mod_strings['LBL_PERFORM_LICENSE_SETTINGS']);
update_license_settings(
    $_SESSION['setup_license_key_users'],
    $_SESSION['setup_license_key_expire_date'],
    $_SESSION['setup_license_key']
);
echo $mod_strings['LBL_PERFORM_DONE'];

//Install forecasts configuration
$forecast_config = ForecastsDefaults::setupForecastSettings();

//Install Opportunities configuration
$opps_config = OpportunitiesDefaults::setupOpportunitiesSettings();

unset($opps_config);

//Install Home config
HomeDefaults::setupHomeSettings();

//Install Tile View configuration
$visualPipelineConfig = VisualPipelineDefaults::setupPipelineSettings();

// Install Console Configuration settings
$consoleConfig = ConsoleConfigurationDefaults::setupConsoleConfigurationSettings();

installerHook('pre_createUsers');
if ($new_tables) {
    echo $line_entry_format . $mod_strings['LBL_PERFORM_DEFAULT_USERS'] . $line_exit_format;
    installLog($mod_strings['LBL_PERFORM_DEFAULT_USERS']);
    create_default_users();
    echo $mod_strings['LBL_PERFORM_DONE'];
} else {
    echo $line_entry_format . $mod_strings['LBL_PERFORM_ADMIN_PASSWORD'] . $line_exit_format;
    installLog($mod_strings['LBL_PERFORM_ADMIN_PASSWORD']);
    $db->setUserName($setup_db_sugarsales_user);
    $db->setUserPassword($setup_db_sugarsales_password);
    set_admin_password($setup_site_admin_password);
    echo $mod_strings['LBL_PERFORM_DONE'];
}
installerHook('post_createUsers');

if ($new_report) {
    echo $line_entry_format . $mod_strings['LBL_PERFORM_DEFAULT_REPORTS'] . $line_exit_format;
    installLog($mod_strings['LBL_PERFORM_DEFAULT_REPORTS']);
    installerHook('pre_createDefaultReports');
    require_once get_custom_file_if_exists('modules/Reports/SeedReports.php');
    create_default_reports();
    installerHook('post_createDefaultReports');
    echo $mod_strings['LBL_PERFORM_DONE'];
}

// OOTB metrics
require_once 'modules/Metrics/SeedMetrics.php';
createDefaultMetrics();

// default OOB schedulers

echo $line_entry_format . $mod_strings['LBL_PERFORM_DEFAULT_SCHEDULER'] . $line_exit_format;
installLog($mod_strings['LBL_PERFORM_DEFAULT_SCHEDULER']);
$scheduler = new Scheduler();
installerHook('pre_createDefaultSchedulers');
$scheduler->rebuildDefaultSchedulers();
installerHook('post_createDefaultSchedulers');


echo $mod_strings['LBL_PERFORM_DONE'];

$defaultTrackerRoles = [
    'Tracker' => [
        'Trackers' => [
            'admin' => 1,
            'access' => 89,
            'view' => 90,
            'list' => 90,
            'edit' => 90,
            'delete' => 90,
            'import' => 90,
            'export' => 90,
        ],
        'TrackerQueries' => [
            'admin' => 1,
            'access' => 89,
            'view' => 90,
            'list' => 90,
            'edit' => 90,
            'delete' => 90,
            'import' => 90,
            'export' => 90,
        ],
        'TrackerPerfs' => [
            'admin' => 1,
            'access' => 89,
            'view' => 90,
            'list' => 90,
            'edit' => 90,
            'delete' => 90,
            'import' => 90,
            'export' => 90,
        ],
        'TrackerSessions' => [
            'admin' => 1,
            'access' => 89,
            'view' => 90,
            'list' => 90,
            'edit' => 90,
            'delete' => 90,
            'import' => 90,
            'export' => 90,
        ],
    ],
];
installerHook('pre_addDefaultRolesTracker');
addDefaultRoles($defaultTrackerRoles);
installerHook('post_addDefaultRolesTracker');

// Adding MLA Roles
installerHook('pre_addDefaultRoles');
require_once 'modules/ACLRoles/SeedRoles.php';
create_default_roles();
installerHook('post_addDefaultRoles');

// Hide certain subpanels by default

$disabledTabs = [
    'project',
    'bugs',
    'products',
    'contracts',
    'revenuelineitems',
    'dataprivacy',
    'externalusers',
    'changetimers',
];

installerHook('pre_setHiddenSubpanels');
$disabledTabsKeyArray = TabController::get_key_array($disabledTabs);
SubPanelDefinitions::set_hidden_subpanels($disabledTabsKeyArray);
installerHook('post_setHiddenSubpanels');

// Bug 28601 - Set the default list of tabs to show
$enabled_tabs = [];
$enabled_tabs[] = 'Home';

$enabled_tabs[] = 'Accounts';
$enabled_tabs[] = 'Contacts';
$enabled_tabs[] = 'Opportunities';
$enabled_tabs[] = 'Leads';
$enabled_tabs[] = 'Calendar';
$enabled_tabs[] = 'Reports';
$enabled_tabs[] = 'Quotes';
$enabled_tabs[] = 'Documents';
$enabled_tabs[] = 'Emails';
$enabled_tabs[] = 'Campaigns';
$enabled_tabs[] = 'Calls';
$enabled_tabs[] = 'Meetings';
$enabled_tabs[] = 'Tasks';
$enabled_tabs[] = 'Escalations';
$enabled_tabs[] = 'Notes';
$enabled_tabs[] = 'Forecasts';
$enabled_tabs[] = 'Cases';
$enabled_tabs[] = 'ProductTemplates';
$enabled_tabs[] = 'Prospects';
$enabled_tabs[] = 'ProspectLists';
$enabled_tabs[] = 'Tags';
$enabled_tabs[] = 'pmse_Project';
$enabled_tabs[] = 'pmse_Inbox';
$enabled_tabs[] = 'pmse_Business_Rules';
$enabled_tabs[] = 'pmse_Emails_Templates';
$enabled_tabs[] = 'BusinessCenters';
$enabled_tabs[] = 'Shifts';
$enabled_tabs[] = 'Purchases';
$enabled_tabs[] = 'PurchasedLineItems';
$enabled_tabs[] = 'Messages';
$enabled_tabs[] = 'DRI_Workflows';
$enabled_tabs[] = 'DRI_SubWorkflows';

if ($_SESSION['demoData'] != 'no') {
    $enabled_tabs[] = 'Bugs';
    $enabled_tabs[] = 'KBContents';
}

installerHook('pre_setSystemTabs');
$tabs = new TabController();
$tabs->set_system_tabs($enabled_tabs);
installerHook('post_setSystemTabs');

// Create the user that will be used by Snip
require_once 'install/createSnipUser.php';
/**
 * SP-1071 disable unsupported legacy connectors for 7.0
 * // Enable the InsideView connector and add all modules
 * installLog("Enable InsideView Connector");
 * enableInsideViewConnector();
 **/

installLog('Adding default maps configurations');
require_once 'install/MapsDefaultData.php';
$admin = BeanFactory::newBean('Administration');
$mapsDefaultConfig = [
    'logLevel' => $mapsDefaultLogLevel,
    'unitType' => $mapsDefaultUnitType,
    'enabled_modules' => $mapsDefaultEnabledModules,
    'modulesData' => $mapsDefaultModulesData,
];

foreach ($mapsDefaultConfig as $name => $value) {
    $admin->saveSetting('maps', $name, $value, 'base');
}


installLog('Converting Opportunities to use RevenueLineItems');
$admin = BeanFactory::newBean('Administration');
$admin->saveSetting('Opportunities', 'opps_view_by', 'RevenueLineItems', 'base');

$converter = new OpportunityWithRevenueLineItem();
$converter->doMetadataConvert();

if ($new_report) {
    installLog('Converting Opportunities Reports to use RevenueLineItems');
    // convert the stock reports
    $reports = new OpportunityReports();
    $reports->migrateToRevenueLineItems();
}

// use the converter to update forecasts, it's protected so we have to get around that for the setup
installLog('converting Forecasts to use RevenueLineItems');
$rm = new ReflectionMethod($converter, 'resetForecastData');
$rm->setAccessible(true);
$rm->invokeArgs($converter, ['RevenueLineItems']);

// Create default dashboards
installLog('creating default dashboards');
$defaultDashboardInstaller = new DefaultDashboardInstaller();
global $moduleList;
$dashboardModule = ['Dashboards'];
$nonMegaMenuModules = ['ReportSchedules', 'ShiftExceptions','Users',];
$defaultDashboardInstaller->buildDashboardsFromFiles(array_merge($dashboardModule, $moduleList, $nonMegaMenuModules));

///////////////////////////////////////////////////////////////////////////////
////    START DEMO DATA

/*
 * Disable indexing in general to avoid any Elasticsearch operations. At this
 * point the ES idex has not been initialized yet. The demo data loader uses
 * SugarBean->save() which automatically triggers an inline index of each
 * object. This is pointless as we want to take care of this in bulk
 * during the intial full indexer run. This will also speed up the install
 * process significantly and avoids ES errors when dyamic update is
 * disabled on the ES cluster itself.
 */
$engine = SearchEngine::getInstance();
$engine->setDisableIndexing(true);

// populating the db with seed data
installLog('populating the db with seed data');
if ($_SESSION['demoData'] != 'no') {
    installerHook('pre_installDemoData');
    set_time_limit(301);

    echo '<br>';
    echo "<b>{$mod_strings['LBL_PERFORM_DEMO_DATA']}</b>";
    echo '<br><br>';

    print($render_table_close);
    print($render_table_open);

    global $current_user;
    $current_user = new User();
    $current_user->retrieve(1);
    include 'install/populateSeedData.php';
    installerHook('post_installDemoData');
}
installLog('done populating the db with seed data');

installLog('Installing Business Process Management designs');

require 'install/installBusinessProcesses.php';
require 'install/Hint/AddSchedulers.php';

// Make sure that columns created by CreateDefaultRelationships.php exists in database
require 'install/CustomerJourney/SyncDatabase.php';

// Make sure Customer Journey OOTB scheduler is created
require 'install/CustomerJourney/CreateDefaultScheduler.php';

// Make sure Customer Journey OOTB Templates are created
require 'install/CustomerJourney/CreateDefaultTemplates.php';

installLog('Completed installation of Business Process Management designs');

if ((!empty($_SESSION['fts_type']) || !empty($_SESSION['setup_fts_type'])) &&
    (empty($_SESSION['setup_fts_skip']))
) {
    installLog('running full indexer');
    $engine->setDisableIndexing(false);
    $engine->runFullReindex(true);
}

$endTime = microtime(true);
$deltaTime = $endTime - $startTime;

installLog('Converting all the saved-reports-chart dashlets to the newly added report-dashlet');
require_once 'install/Reports/ConvertToReportDashlet.php';
$converter = new ConvertToReportDashlet();
$converter->run();
installLog('Done converting all the saved-reports-chart dashlets to the newly added report-dashlet');

///////////////////////////////////////////////////////////////////////////
////    FINALIZE LANG PACK INSTALL
if (isset($_SESSION['INSTALLED_LANG_PACKS']) && ArrayFunctions::is_array_access($_SESSION['INSTALLED_LANG_PACKS'])
    && !empty($_SESSION['INSTALLED_LANG_PACKS'])
) {
    updateUpgradeHistory();
}

require_once 'modules/Connectors/InstallDefaultConnectors.php';

///////////////////////////////////////////////////////////////////////////////
////    INSTALL Email TEMPLATES
include 'install/seed_data/Email_Notification_SeedData.php';
include 'install/seed_data/Advanced_Password_SeedData.php';

////    INSTALL PDF TEMPLATES
include 'install/seed_data/PdfManager_SeedData.php';

////    INSTALL Calendars
include 'install/seed_data/Calendar_SeedData.php';

///////////////////////////////////////////////////////////////////////////////
////    SETUP DONE
installLog('Done populating data *********');
$memoryUsed = '';
if (function_exists('memory_get_usage')) {
    $memoryUsed = $mod_strings['LBL_PERFORM_OUTRO_5'] . memory_get_usage() . $mod_strings['LBL_PERFORM_OUTRO_6'];
}

$errTcpip = '';
$fp = @fsockopen('www.sugarcrm.com', 80, $errno, $errstr, 3);
if (!$fp) {
    $errTcpip = "<p>{$mod_strings['ERR_PERFORM_NO_TCPIP']}</p>";
}
if ($fp) {
    @fclose($fp);
    if ($next_step == 9999) {
        $next_step = 8;
    }
    $fpResult = <<<FP
     <form action="install.php" method="post" name="form" id="form">
     <input type="hidden" name="current_step" value="{$next_step}">
     <table cellspacing="0" cellpadding="0" border="0" class="stdTable">
       <tr>
         <td>
            &nbsp;
         </td>
         <td><input class="button" type="submit" name="goto" value="{$mod_strings['LBL_NEXT']}" id="button_next2"/></td>
       </tr>
     </table>
     </form>
FP;
} else {
    $fpResult = <<<FP
     <table cellspacing="0" cellpadding="0" border="0" class="stdTable">
       <tr>
         <td>&nbsp;</td>
         <td>
            <form action="index.php" method="post" name="formFinish" id="formFinish">
                <input type="hidden" name="default_user_name" value="admin" />
                <input class="button" type="submit" name="next" value="{$mod_strings['LBL_PERFORM_FINISH']}" id="button_next2"/>
            </form>
         </td>
       </tr>
     </table>
FP;
}

if (isset($_SESSION['setup_site_sugarbeet_automatic_checks']) && $_SESSION['setup_site_sugarbeet_automatic_checks'] == true) {
    set_CheckUpdates_config_setting('automatic');
} else {
    set_CheckUpdates_config_setting('manual');
}
if (!empty($_SESSION['setup_system_name'])) {
    $admin = new Administration();
    $admin->saveSetting('system', 'name', $_SESSION['setup_system_name']);
}


installLog('Running post-install hooks');
post_install_modules();
installLog('Finished post-install hooks');

//Call rebuildSprites
if (function_exists('imagecreatetruecolor')) {
    require_once 'modules/UpgradeWizard/uw_utils.php';
    rebuildSprites(true);
}

if (is_array($bottle) && safeCount($bottle) > 0) {
    foreach ($bottle as $bottle_message) {
        $bottleMsg .= "{$bottle_message}\n";
    }
} else {
    $bottleMsg = $mod_strings['LBL_PERFORM_SUCCESS'];
}
installerHook('post_installModules');

installerHook('pre_handleMissingSmtpServerSettingsNotifications');
handleMissingSmtpServerSettingsNotifications();
installerHook('post_handleMissingSmtpServerSettingsNotifications');

// Adds the jobs related to the denormalized fields in 6 modules
$updateRelateDenormalizationState = function () {
    $modules = [
        'Contacts' => [
            'account_name',
        ],
        'Opportunities' => [
            'account_name',
        ],
        'RevenueLineItems' => [
            'account_name',
        ],
        'Cases' => [
            'account_name',
        ],
        'PurchasedLineItems' => [
            'account_name',
        ],
        'Quotes' => [
            'billing_account_name',
        ],
    ];

    $jobsAdded = [];
    $adminUser = BeanFactory::newBean('Users')->getSystemUser();
    foreach ($modules as $module => $fieldNames) {
        foreach ($fieldNames as $fieldName) {
            $bean = BeanFactory::newBean($module);
            $def = $bean->getFieldDefinition($fieldName);
            if (!empty($def['is_denormalized'])) {
                continue;
            }
            $entity = new Entity($bean, $fieldName);

            $config = new FieldConfig();
            $config->markFieldAsDenormalized($entity, true);

            $options = [
                'module_name' => $entity->getTargetModuleName(),
                'field_name' => $entity->fieldName,
                'tmp_table_name' => 'denorm_tmp_' . $module,
            ];

            /* @var $job SchedulersJob */
            $job = BeanFactory::newBean('SchedulersJobs');
            $job->name = 'Upgrade_Denormalization_' . $module . '_' . $fieldName;
            $job->target = 'class::' . SugarJobFieldDenormalization::class;
            $job->data = json_encode($options);
            $job->retry_count = 0;
            $job->job_group = 'install';
            $job->assigned_user_id = $adminUser->id;

            $queue = new SugarJobQueue();
            $queue->submitJob($job);
            // mark as deleted to disable execution from cron.php. It will be enabled by the Watcher Job added below
            $job->deleted = 1;
            $job->save();
            $jobsAdded[$job->id] = false;
        }
    }

    if (!empty($jobsAdded)) {
        /* @var $job SchedulersJob */
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = 'Upgrade_Denormalization_Watcher';
        $job->target = 'function::upgradeDenormalizationStateForSugar11';
        $job->data = json_encode($jobsAdded);
        $job->retry_count = 0;
        $job->job_group = 'install';
        $job->assigned_user_id = $adminUser->id;

        $queue = new SugarJobQueue();
        $queue->submitJob($job);
    }
};
$updateRelateDenormalizationState();

// rebuild cache after all is said and done
installLog('Populating file cache');
SugarAutoLoader::buildCache();

// Build the base platform metadata caches after everything else is done.
installLog('Populating metadata cache');
MetaDataManager::enableCache();
$app_list_strings = return_app_list_strings_language('en_us');
MetaDataManager::setupMetadata(['base'], ['en_us']);

// TODO: Remove the following. (See MAR-1314)
// Restore the activity stream behaviour.
Activity::restoreToPreviousState();
// Allow processes to resume at this point
Registry\Registry::getInstance()->drop('setup:disable_processes');
installerHook('post_performSetup');
$out = <<<EOQ
<br><p><b>{$mod_strings['LBL_PERFORM_OUTRO_1']} {$setup_sugar_version} {$mod_strings['LBL_PERFORM_OUTRO_2']}</b></p>

{$mod_strings['LBL_PERFORM_OUTRO_3']} {$deltaTime} {$mod_strings['LBL_PERFORM_OUTRO_4']}<br />
{$memoryUsed}
{$errTcpip}
    </td>
</tr>
<tr>
<td align="right" colspan="2">
<hr>
<table cellspacing="0" cellpadding="0" border="0" class="stdTable">
<tr>
<td>
{$fpResult}
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
<!--
<bottle>{$bottleMsg}</bottle>
-->
EOQ;

echo $out;

if (isset($context, $subject)) {
    $context->deactivateSubject($subject);
}
installLog('Installation has completed *********');
