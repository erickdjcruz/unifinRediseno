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

//Used in rebuildExtensions

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\AccessControl\AdminWork;
use Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Session\SessionStorage;
use Sugarcrm\Sugarcrm\SystemProcessLock\SystemProcessLock;

SugarAutoLoader::requireWithCustom('ModuleInstall/ModuleInstaller.php');

// Used in clearExternalAPICache

// Used in clearPDFFontCache

// Used in clearAdditionalCaches

//clear out the api metadata cache

require_once 'include/utils.php';

/**
 * Class for handling repairing of the sugar installation and rebuilding of caches
 */
class RepairAndClear
{
    public $module_list = [];
    public $show_output;
    protected $actions;
    public $execute;
    public $saveLogToSession = false;
    protected $module_list_from_cache;

    private const QRR_SESSION_LOG = 'qrr_log';
    /**
     * Stack of called methods that should not be repeated
     *
     * @var array
     */
    protected $called = [];

    /**
     * @var callable
     */
    private $statementObserver;

    /**
     * make sure mod_string contains 'LBL_ALL_MODULES'
     * RepairAndClear constructor.
     *
     * @param bool $saveLogToSession if true, any output is disabled and everything goes to the session log
     *
     */
    public function __construct(bool $saveLogToSession = false)
    {
        global $mod_strings;
        $mod_strings['LBL_ALL_MODULES'] = 'all_modules';
        $this->saveLogToSession = $saveLogToSession;
        if ($this->saveLogToSession) {
            SessionStorage::getInstance()[self::QRR_SESSION_LOG] = [];
            ob_start([$this, 'log'], 1);
        }
    }

    /**
     * Sets the callback function which will be invoked with every executed SQL statement
     *
     * @param callable $statementObserver
     */
    public function setStatementObserver(callable $statementObserver): void
    {
        $this->statementObserver = $statementObserver;
    }

    /**
     * Repair and Rebuild with race condition protection
     *
     * @param $selected_actions
     * @param $modules
     * @param bool $autoexecute
     * @param bool $show_output
     * @param bool $metadata_sections
     * @param array $skipExtensionsSections
     */
    public function repairAndClearAll(
        $selected_actions,
        $modules,
        $autoexecute = false,
        $show_output = true,
        $metadata_sections = false,
        array $skipExtensionsSections = []
    ) {

        $systemProcessLock = new SystemProcessLock(__METHOD__);

        $checkCondition = function () {
            // always run rebuild function
            return true;
        };

        $longRunningFunction = function (int $attempt) use (
            $selected_actions,
            $modules,
            $autoexecute,
            $show_output,
            $metadata_sections,
            $skipExtensionsSections
        ) {
            // if the lock was acquired immediately or if the current user is admin
            if ($attempt == 1
                || $GLOBALS['current_user']->isAdmin() || AccessControlManager::instance()->getAdminWork()) {
                $this->repairAndClearAllUnsafe(
                    $selected_actions,
                    $modules,
                    $autoexecute,
                    $show_output,
                    $metadata_sections,
                    $skipExtensionsSections
                );
            } else {
                // otherwise ignore regular user QRR requests
                return;
            }
        };

        $refuseFunction = $longRunningFunction;

        // the following is designed to prevent process race conditions in a long running process
        $systemProcessLock->isolatedCall($checkCondition, $longRunningFunction, $refuseFunction);
    }

    /**
     * Repair and Rebuild with race condition risk
     *
     * @param $selected_actions
     * @param $modules
     * @param bool $autoexecute
     * @param bool $show_output
     * @param bool $metadata_sections
     * @param array $skipExtensionsSections
     */
    public function repairAndClearAllUnsafe(
        $selected_actions,
        $modules,
        $autoexecute = false,
        $show_output = true,
        $metadata_sections = false,
        array $skipExtensionsSections = []
    ): void {

        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        global $mod_strings;
        $this->module_list = $modules;
        $this->show_output = $show_output;
        // Add repairDatabase to the actions stack
        $actions = array_merge($selected_actions, ['repairDatabase']);

        // Unique the action stack to prevent duplicate processing
        $this->actions = array_unique($actions);

        $this->execute = $autoexecute;

        // Clear vardefs and language cache always. Since this is called here it
        //  should not be in the actions
        $this->clearVardefs();
        $this->clearLanguageCache();

        ACLField::clearACLCache();

        // Enable the metadata manager cache refresh to queue. This allows the
        // cache refresh processes in metadata manager to be carried out one time
        // at the end of the rebuild instead of continual processing during the
        // request. This is set outside of the loop to allow any other cache reset
        // process to carry itself out as needed without firing off continual
        // calls to the metadata manager.
        MetaDataManager::enableCacheRefreshQueue();
        foreach ($this->actions as $current_action) {
            switch ($current_action) {
                case 'repairDatabase':
                    if (safeInArray($mod_strings['LBL_ALL_MODULES'], $this->module_list)) {
                        $this->repairDatabase();
                    } else {
                        $this->repairDatabaseSelectModules();
                    }
                    break;
                case 'rebuildExtensions':
                    if (safeInArray($mod_strings['LBL_ALL_MODULES'], $this->module_list)) {
                        $this->rebuildExtensions([], $skipExtensionsSections);
                        // Mark this as called so it doesn't get ran again
                        $this->called[$current_action] = true;
                    } else {
                        $this->rebuildExtensions($this->module_list, $skipExtensionsSections);
                    }
                    break;
                case 'clearTpls':
                    $this->clearTpls();
                    break;
                case 'clearJsFiles':
                    $this->clearJsFiles();
                    break;
                case 'clearDashlets':
                    $this->clearDashlets();
                    break;
                case 'clearThemeCache':
                    $this->clearThemeCache();
                    break;
                case 'clearJsLangFiles':
                    $this->clearJsLangFiles();
                    break;
                case 'rebuildAuditTables':
                    $this->rebuildAuditTables();
                    break;
                case 'clearSearchCache':
                    $this->clearSearchCache();
                    break;
                case 'clearAdditionalCaches':
                    $this->clearAdditionalCaches();
                    break;
                case 'repairMetadataAPICache':
                    $this->repairMetadataAPICache();
                    break;
                case 'clearPDFFontCache':
                    $this->clearPDFFontCache();
                    break;
                case 'resetForecasting':
                    $this->resetForecasting();
                    break;
                case 'repairConfigs':
                    $this->repairBaseConfig();
                    $this->repairPortalConfig();
                    // no break
                case 'clearAll':
                    $this->clearTpls();
                    $this->clearJsFiles();
                    $this->clearJsLangFiles();
                    $this->clearDashlets();
                    $this->clearSmarty();
                    $this->clearThemeCache();
                    $this->clearXMLfiles();
                    $this->clearSearchCache();
                    $this->clearExternalAPICache();
                    $this->clearAdditionalCaches();
                    $this->clearPDFFontCache();
                    $this->rebuildExtensions();
                    $this->rebuildFileMap();
                    $this->rebuildAuditTables();
                    $this->repairDatabase();
                    $this->repairBaseConfig();
                    $this->repairPortalConfig();
                    $this->repairMetadataAPICache($metadata_sections);
                    $this->rebuildJSCacheFiles();
                    $this->clearMultiTenantCache();
                    break;
            }
        }

        // Reset this so that things work properly after this is over
        $this->called = [];

        // Run the metadata cache refresh queue. This will turn queueing off
        // after it is run
        MetaDataManager::runCacheRefreshQueue();
    }

    /////////////OLD


    public function repairDatabase()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        // Repair database may have already been called and doesn't really need
        // to be called a second time
        if (isset($this->called['repairDatabase'])) {
            return;
        }

        $this->called['repairDatabase'] = true;

        global $dictionary, $mod_strings;
        if (false == $this->show_output) {
            $_REQUEST['repair_silent'] = '1';
        }
        $_REQUEST['execute'] = $this->execute;
        $GLOBALS['reload_vardefs'] = true;
        $hideModuleMenu = true;

        require 'modules/Administration/repairDatabase.php';
    }

    /**
     * Rebuilds the base Sidecar configuration file.
     */
    public function repairBaseConfig()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        $moduleInstallerClass::handleBaseConfig();
    }


    /**
     * Rebuild the portal javascript config file.
     */
    public function repairPortalConfig()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        $moduleInstallerClass::handlePortalConfig();
    }


    public function repairDatabaseSelectModules()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        global $current_user, $mod_strings, $dictionary;
        set_time_limit(3600);

        include 'include/modules.php'; //bug 15661
        $db = DBManagerFactory::getInstance();

        if (is_admin($current_user) || is_admin_for_any_module($current_user)) {
            $export = false;
            if ($this->show_output) {
                echo getClassicModuleTitle($mod_strings['LBL_REPAIR_DATABASE'], [$mod_strings['LBL_REPAIR_DATABASE']], false);
            }
            if ($this->show_output) {
                echo "<h1 id=\"rdloading\">{$mod_strings['LBL_REPAIR_DATABASE_PROCESSING']}</h1>";
                ob_flush();
            }
            $sql = '';
            if ($this->module_list && !safeInArray($mod_strings['LBL_ALL_MODULES'], $this->module_list)) {
                $repair_related_modules = array_keys($dictionary);
                //repair DB
                $dm = inDeveloperMode();
                $GLOBALS['sugar_config']['developerMode'] = true;
                $GLOBALS['reload_vardefs'] = true;
                foreach ($this->module_list as $bean_name) {
                    $focus = BeanFactory::newBean($bean_name);
                    if (!empty($focus)) {
                        #30273
                        if (empty($focus->disable_vardefs)) {
                            if (empty($dictionary[$focus->object_name])) {
                                VardefManager::loadVardef($bean_name, $focus->object_name);
                            }
                            if ($this->show_output) {
                                print_r('<p>' . $mod_strings['LBL_REPAIR_DB_FOR'] . ' ' . $bean_name . '</p>');
                            }
                            $sql .= $db->repairTable($focus, $this->execute);
                        }
                    }
                }

                $GLOBALS['sugar_config']['developerMode'] = $dm;

                if ($this->show_output) {
                    echo "<script type=\"text/javascript\">document.getElementById('rdloading').style.display = \"none\";</script>";
                }
                if (isset($sql) && !empty($sql)) {
                    $qry_str = '';
                    foreach (explode("\n", $sql) as $line) {
                        if (!empty($line) && substr($line, -2) != '*/') {
                            $line .= ';';
                        }

                        $qry_str .= $line . "\n";
                    }
                    if ($this->show_output) {
                        echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_DIFFERENCES']}</h3>";
                        echo "<p>{$mod_strings['LBL_REPAIR_DATABASE_TEXT']}</p>";

                        echo '<form method="post" action="index.php?module=Administration&amp;action=repairDatabase">';
                        echo "<textarea name=\"sql\" rows=\"24\" cols=\"150\" id=\"repairsql\">$qry_str</textarea>";
                        echo '<br /><input type="submit" value="' . $mod_strings['LBL_REPAIR_DATABASE_EXECUTE'] . '" name="raction" /> <input type="submit" name="raction" value="' . $mod_strings['LBL_REPAIR_DATABASE_EXPORT'] . '" />';
                    }
                } elseif ($this->show_output) {
                    echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
                }
            }
        } else {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
    }

    /**
     * @param array $objects
     * @param array $skipExtensionsSections
     */
    public function rebuildExtensions(array $objects = [], array $skipExtensionsSections = []): void
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $modules = [];
        global $beanList;
        $modBeans = array_flip($beanList);
        foreach ($objects as $obj) {
            //We expect $objects to be a list of bean classes that we need to map back to modules
            //But also check if the list is actually modules
            if (isset($modBeans[$obj])) {
                $modules[] = $modBeans[$obj];
            } elseif (isset($beanList[$obj])) {
                $modules[] = $obj;
            }
        }
        // Rebuild extensions may have already been called. Don't do it again.
        if (isset($this->called['rebuildExtensions'])) {
            return;
        }

        global $mod_strings;
        if ($this->show_output) {
            echo $mod_strings['LBL_QR_REBUILDEXT'];
        }

        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        $mi = new $moduleInstallerClass();
        foreach ($skipExtensionsSections as $skipExtensionsSection) {
            unset($mi->extensions[$skipExtensionsSection]);
        }
        $mi->rebuild_all(!$this->show_output, $modules);

        // Remove the "Rebuild Extensions" red text message on admin logins

        if ($this->show_output) {
            echo $mod_strings['LBL_REBUILD_REL_UPD_WARNING'];
        }
    }

    /**
     * rebuild mapping file
     */
    public function rebuildFileMap()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_REBUILDFILEMAP']}</h3>";
        }
        SugarAutoLoader::buildCache();
    }

    //Cache Clear Methods
    public function clearSmarty()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARSMARTY']}</h3>";
        }
        $this->_clearCache(sugar_cached('smarty/templates_c'), '.tpl.php');
    }

    public function clearXMLfiles()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_XMLFILES']}</h3>";
        }
        $this->_clearCache(sugar_cached('xml'), '.xml');
    }

    public function clearDashlets()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARDASHLET']}</h3>";
        }
        $this->_clearCache(sugar_cached('dashlets'), '.php');
    }

    public function clearThemeCache()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARTHEMECACHE']}</h3>";
        }
        SugarThemeRegistry::clearAllCaches();

        //Clear Sidecar Themes CSS files
        $this->_clearCache(sugar_cached('themes/clients/'), '.css');
    }

    public function clearTpls()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARTEMPLATE']}</h3>";
        }
        if (!safeInArray(translate('LBL_ALL_MODULES'), $this->module_list) && !empty($this->module_list)) {
            foreach ($this->module_list as $module_name) {
                $this->_clearCache(sugar_cached('modules/' . $module_name), '.tpl');
            }
        } else {
            $this->_clearCache(sugar_cached('modules/'), '.tpl');
        }
    }

    public function clearVardefs()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARVADEFS']}</h3>";
        }
        if (!empty($this->module_list) && is_array($this->module_list) && !safeInArray(translate('LBL_ALL_MODULES'), $this->module_list)) {
            foreach ($this->module_list as $module_name) {
                $this->_clearCache(sugar_cached('modules/' . $module_name), 'vardefs.php');
            }
        } else {
            $this->_clearCache(sugar_cached('modules/'), 'vardefs.php');
        }
    }

    public function clearJsFiles()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARJS']}</h3>";
        }

        if (!safeInArray(translate('LBL_ALL_MODULES'), $this->module_list) && !empty($this->module_list)) {
            foreach ($this->module_list as $module_name) {
                $this->_clearCache(sugar_cached('modules/' . $module_name), '.js');
            }
        } else {
            $this->_clearCache(sugar_cached('modules/'), '.js');
        }
        $this->_clearCache(sugar_cached('themes/'), '.js');
    }

    public function clearJsLangFiles()
    {
        global $mod_strings;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARJSLANG']}</h3>";
        }
        if (!safeInArray(translate('LBL_ALL_MODULES'), $this->module_list) && !empty($this->module_list)) {
            foreach ($this->module_list as $module_name) {
                $this->_clearCache(sugar_cached('jsLanguage/' . $module_name), '.js');
            }
        } else {
            $this->_clearCache(sugar_cached('jsLanguage'), '.js');
        }
    }

    /**
     * Remove the language cache files from cache/modules/<module>/language
     */
    public function clearLanguageCache()
    {
        global $mod_strings;

        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARLANG']}</h3>";
        }
        //clear cache using the list $module_list_from_cache
        if (!empty($this->module_list) && is_array($this->module_list)) {
            if (safeInArray(translate('LBL_ALL_MODULES'), $this->module_list)) {
                LanguageManager::clearLanguageCache();
            } else { //use the modules selected thrut the select list.
                foreach ($this->module_list as $module_name) {
                    LanguageManager::clearLanguageCache($module_name);
                }
            }
        }
        // Clear app* cache values too
        if (!empty($GLOBALS['sugar_config']['languages'])) {
            $languages = $GLOBALS['sugar_config']['languages'];
        } else {
            $languages = [$GLOBALS['current_language'] => $GLOBALS['current_language']];
        }
        foreach (array_keys($languages) as $language) {
            sugar_cache_clear('app_strings.' . $language);
            sugar_cache_clear('app_list_strings.' . $language);
        }
    }

    /**
     * Remove the cached unified_search_modules.php file
     */
    public function clearSearchCache()
    {
        global $mod_strings, $sugar_config;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARSEARCH']}</h3>";
        }
        // clear sugar_cache backend for SugarSearchEngine
        SugarSearchEngineMetadataHelper::clearCache();

        // Clear the cache file AFTER the cache clear, as it will be rebuilt by
        // clearCache otherwise
        UnifiedSearchAdvanced::unlinkUnifiedSearchModulesFile();
    }

    public function clearExternalAPICache()
    {
        global $mod_strings, $sugar_config;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEAR_EXT_API']}</h3>";
        }

        ExternalAPIFactory::clearCache();
    }

    public function clearPDFFontCache()
    {
        global $mod_strings, $sugar_config;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEARPDFFONT']}</h3>";
        }

        $fontManager = new FontManager();
        $fontManager->clearCachedFile();
    }

    /*
     * Catch all function to clear out any misc. caches we may have
     */

    public function clearAdditionalCaches()
    {
        global $mod_strings, $sugar_config;
        if ($this->show_output) {
            echo "<h3>{$mod_strings['LBL_QR_CLEAR_ADD_CACHE']}</h3>";
        }
        // clear out the API Cache

        $sd = new ServiceDictionary();
        $sd->clearCache();

        //Remove cached js component files
        $this->_clearCache(sugar_cached('include/javascript/'), '.js');
    }

    /**
     * Clears out the metadata file cache and memory caches.
     *
     * NOTE: While this is here as part of the collection of methods to be used
     * for clearing caches, it really should only be used in the most extreme
     * cases as it will result in long wait times the next time client apps are
     * called since rebuilding the metadata cache will be done then.
     *
     * Bug 55141 - Clear the metadata API cache
     */
    public function clearMetadataAPICache()
    {
        // Bug 55141: Metadata Cache is a Smart cache so we can delete everything from the cache dir
        MetaDataManager::clearAPICache();
        if (empty($this->module_list)) {
            return;
        }

        foreach ($this->module_list as $module_name) {
            $this->_clearCache(sugar_cached('modules/' . $module_name . '/clients'), '.php');
        }
    }

    /**
     * Cleans out current metadata cache and rebuilds it for
     * each platform and visibility
     */
    public function repairMetadataAPICache($section = '')
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        // Refresh metadata for selected modules only if there selected modules
        if (is_array($this->module_list) && !empty($this->module_list) && !safeInArray(translate('LBL_ALL_MODULES'), $this->module_list)) {
            MetaDataFiles::clearModuleClientCache($this->module_list);
            MetaDataManager::refreshModulesCache($this->module_list);
        }

        // If there is a section named (like 'fields') refresh that section
        if (!empty($section)) {
            MetaDataManager::refreshSectionCache($section);
        } else {
            // Otherwise if the section is not a false nuke all caches and rebuild
            // the base metadata cache
            if ($section !== false) {
                MetaDataManager::clearAPICache(true, true);
                MetaDataManager::setupMetadata();
            }
        }
    }


    //////////////////////////////////////////////////////////////
    /////REPAIR AUDIT TABLES
    public function rebuildAuditTables()
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        global $mod_strings;
        include 'include/modules.php';    //bug 15661
        if ($this->show_output) {
            echo "<h3> {$mod_strings['LBL_QR_REBUILDAUDIT']}</h3>";
        }

        if (!safeInArray(translate('LBL_ALL_MODULES'), $this->module_list) && !empty($this->module_list)) {
            foreach ($this->module_list as $module_name) {
                $bean = BeanFactory::newBean($module_name);
                if (!empty($bean)) {
                    $this->rebuildAuditTablesHelper($bean);
                }
            }
        } elseif (safeInArray(translate('LBL_ALL_MODULES'), $this->module_list)) {
            foreach ($beanFiles as $bean => $file) {
                $bean_instance = BeanFactory::newBeanByName($bean);
                if (!empty($bean_instance)) {
                    $this->rebuildAuditTablesHelper($bean_instance);
                }
            }
        }
        if ($this->show_output) {
            echo $mod_strings['LBL_DONE'];
        }
    }

    private function rebuildAuditTablesHelper($focus)
    {
        global $mod_strings;

        // skip if not a SugarBean object
        if (!($focus instanceof SugarBean)) {
            return;
        }

        if ($focus->is_AuditEnabled()) {
            $tableName = $focus->get_audit_table_name();
            if (!$focus->db->tableExists($tableName)) {
                if ($this->show_output) {
                    echo $mod_strings['LBL_QR_CREATING_TABLE'] . ' ' . $focus->get_audit_table_name() . ' ' .
                        $mod_strings['LBL_FOR'] . ' ' . $focus->object_name . '.<br/>';
                }
                $focus->create_audit_table();
            } else {
                $defs = $focus->get_audit_table_defs();
                $sql = $focus->db->repairTableParams(
                    $defs['table'],
                    $defs['fields'],
                    $defs['indices'],
                    true,
                    $defs['engine'] ?? null
                );
                if ($this->show_output) {
                    if (empty($sql)) {
                        echo str_replace('%1$', $focus->object_name, $mod_strings['LBL_REBUILD_AUDIT_SKIP']);
                    } else {
                        echo str_replace('%1$', $focus->object_name, $mod_strings['LBL_REBUILD_AUDIT_REPAIR']);
                    }
                }
            }
        } elseif ($this->show_output) {
            echo $focus->object_name . $mod_strings['LBL_QR_NOT_AUDIT_ENABLED'];
        }
    }

    ///////////////////////////////////////////////////////////////
    ////END REPAIR AUDIT TABLES


    ///////////////////////////////////////////////////////////////
    //// Recursively unlink all files of the given $extension in the given $thedir.
    //
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    private function _clearCache($thedir, $extension)
    {
        // allow admin to access everything,
        // don't remove $adminWork until you don’t need the privilege anymore
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        if (!file_exists($thedir)) {
            return;
        }

        if ($current = @opendir($thedir)) {
            while (false !== ($children = readdir($current))) {
                if ($children != '.' && $children != '..') {
                    if (is_dir($thedir . '/' . $children)) {
                        $this->_clearCache($thedir . '/' . $children, $extension);
                    } elseif (is_file($thedir . '/' . $children) && (substr_count($children, $extension))) {
                        unlink($thedir . '/' . $children);
                    }
                }
            }
        }
    }

    /**
     * This is a private function to allow forecasts config settings to be reset
     *
     */
    private function resetForecasting()
    {
        $db = DBManagerFactory::getInstance();
        $db->query("UPDATE config SET value = 0 WHERE name = 'is_setup'");
        $db->query("UPDATE config SET value = 0 WHERE name = 'has_commits'");
    }

    /*
     * Rebuild the Javascript files in Cache
     */
    public function rebuildJSCacheFiles()
    {
        $jsFiles = ['sugar_grp1.js', 'sugar_grp1_yui.js', 'sugar_grp1_jquery.js'];
        ensureJSCacheFilesExist($jsFiles);
        if (SugarConfig::getInstance()->get('update_js_custom_version_on_rebuild', true)) {
            $cfg = new Configurator();
            $jsCstmVersion = 'js_custom_version';
            $cfg->config[$jsCstmVersion] = (int)SugarConfig::getInstance()->get($jsCstmVersion, 1) + 1;
            $cfg->handleOverride();
            SugarConfig::getInstance()->clearCache($jsCstmVersion);
        }
    }

    public function clearMultiTenantCache(): void
    {
        $multiTenantCache = Container::getInstance()->get(MultiTenant::class);
        if ($multiTenantCache !== null) {
            $multiTenantCache->clear();
        }
    }

    public function log($message): void
    {
        $session = SessionStorage::getInstance();
        if ($this->saveLogToSession) {
            if ($session->isClosed()) {
                session_start();
            }
            $_SESSION[self::QRR_SESSION_LOG][] = $message;
            $session->unlock();
        }
    }

    public function sessionLogPop(): array
    {
        $session = SessionStorage::getInstance();
        $sessionData = $session[self::QRR_SESSION_LOG] ?? null;
        if ($sessionData) {
            $data = $sessionData->getArrayCopy();
            session_start();
            $_SESSION[self::QRR_SESSION_LOG] = [];
            $session->unlock();
            return $data;
        }
        return [];
    }
}
