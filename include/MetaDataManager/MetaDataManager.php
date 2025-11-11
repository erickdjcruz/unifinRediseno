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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\CSP\ContentSecurityPolicy;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use Sugarcrm\Sugarcrm\AccessControl\AdminWork;
use Sugarcrm\Sugarcrm\MetaData\RefreshQueue;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication;
use Sugarcrm\Sugarcrm\SystemProcessLock\SystemProcessLock;
use Sugarcrm\Sugarcrm\DocuSign\DocuSignUtils;
use Sugarcrm\Sugarcrm\Util\Files\FileLoader;

require_once 'soap/SoapHelperFunctions.php';
require_once 'include/SugarObjects/LanguageManager.php';

SugarAutoLoader::requireWithCustom('include/MetaDataManager/MetaDataHacks.php');
SugarAutoLoader::requireWithCustom('include/MetaDataManager/MetaDataCache.php');

/**
 * This class is for access to metadata for all sugarcrm modules in a read only
 * state.  This means that you can not modify any of the metadata using this
 * class currently.
 *
 * For cache handling, the naming paradigm has the following meaning:
 *  - refresh* methods are public static methods that generally call rebuild*
 *    methods after getting a proper platform specific manager based on visibility.
 *  - rebuild* methods are instance methods and can be either public or protected
 *    and are the methods that do the actual work of recollecting the metadata
 *    for a given section or module and rewriting that data to the cache. In some
 *    cases there are rebuild* methods that are consumed by other rebuild* methods.
 */
class MetaDataManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var \DBManager|mixed
     */
    public $db;
    /**
     * @var mixed|mixed[]
     */
    public $args;

    /**
     * Constants that define the sections of the metadata
     */
    public const MM_MODULES = 'modules';
    public const MM_FULLMODULELIST = 'full_module_list';
    public const MM_MODULESINFO = 'modules_info';
    public const MM_FIELDS = 'fields';
    public const MM_LABELS = 'labels';
    public const MM_ORDEREDLABELS = 'ordered_labels';
    public const MM_VIEWS = 'views';
    public const MM_LAYOUTS = 'layouts';
    public const MM_RELATIONSHIPS = 'relationships';
    public const MM_DATA = 'datas';
    public const MM_CURRENCIES = 'currencies';
    public const MM_JSSOURCE = 'jssource';
    public const MM_SERVERINFO = 'server_info';
    public const MM_CONFIG = 'config';
    public const MM_LANGUAGES = 'languages';
    public const MM_HIDDENSUBPANELS = 'hidden_subpanels';
    public const MM_MODULETABMAP = 'module_tab_map';
    public const MM_LOGOURL = 'logo_url';
    public const MM_LOGOURLDARK = 'logo_url_dark';
    public const MM_OVERRIDEVALUES = '_override_values';
    public const MM_FILTERS = 'filters';
    public const MM_EDITDDFILTERS = 'editable_dropdown_filters';

    /**
     * Collection of fields in the user metadata that can trigger a reauth when
     * changed.
     *
     * Mapping is 'prefname' => 'metadataname'
     * @var array
     */
    protected $userPrefsToCache = [
        'datef' => 'datepref',
        'timef' => 'timepref',
        'timezone' => 'timezone',
    ];

    /**
     * The metadata hacks class
     *
     * @var MetaDataHacks
     */
    protected $metaDataHacks;

    /**
     * Stack of flag that tells this class to clear the metadata cache on shutdown
     * of the request. The stack is keyed on whether a delete module client cache
     * was requested or not, so a cache clear will happen no more than twice (and
     * more than likely will only happen once).
     *
     * @var array
     */
    protected static $clearCacheOnShutdown = [];

    /**
     * A collection of metadata managers for use as a simple cache in the factory.
     *
     * @var array
     */
    protected static $managers = [];

    /**
     * In memory cache of module ACLs that are record independent
     * @var array
     */
    protected static $aclForModules = [];

    /**
     * The requested platform, or collection of platforms
     *
     * @var array
     */
    protected $platforms;

    /**
     * Flag that determines whether this is a public or private request
     *
     * @var bool
     */
    protected $public = false;

    /**
     * String visibility type indicator used in various methods that depend on
     * visibility. This will be set based on the value of $this->public.
     *
     * @var string
     */
    protected $visibility = 'private';

    /**
     * Sections of the metadata based on visibility
     *
     * @var array
     */
    protected $sections = [];

    /**
     * Mapping of metadata sections to the methods that get the data for that
     * section. If the value of the section index is false, the method will be
     * rebuild{section}Section() and will require the current metadata array as an argument.
     *
     * @var array
     */
    protected $sectionMap = [
        self::MM_MODULES => false,
        self::MM_FULLMODULELIST => 'getModuleList',
        self::MM_MODULESINFO => 'getModulesInfo',
        self::MM_FIELDS => 'getSugarFields',
        self::MM_LABELS => 'getStringUrls',
        self::MM_ORDEREDLABELS => 'getOrderedStringUrls',
        self::MM_VIEWS => 'getSugarViews',
        self::MM_LAYOUTS => 'getSugarLayouts',
        self::MM_DATA => 'getSugarData',
        self::MM_RELATIONSHIPS => 'getRelationshipData',
        self::MM_CURRENCIES => 'getSystemCurrencies',
        self::MM_JSSOURCE => false,
        self::MM_SERVERINFO => 'getServerInfo',
        self::MM_CONFIG => 'getConfigs',
        self::MM_LANGUAGES => 'getAllLanguages',
        self::MM_HIDDENSUBPANELS => 'getHiddenSubpanels',
        self::MM_MODULETABMAP => 'getModuleTabMap',
        self::MM_LOGOURL => 'getLogoUrl',
        self::MM_LOGOURLDARK => 'getLogoUrlDark',
        self::MM_FILTERS => 'getSugarFilters',
        self::MM_EDITDDFILTERS => 'getEditableDropdownFilters',
    ];

    /**
     * Flag that tells the manager whether the cache refresher is queued. This
     * is off by default and can be toggled using enable/disableCacheRefresherQueue().
     *
     * @var bool
     */
    protected static $isQueued = false;

    /**
     * The actual cache refresher queue. When the cache refresher queue runner is
     * called, this array will drive what is done.
     *
     * @var RefreshQueue
     */
    protected static $queue = [];

    protected static $fullRefresh = [];

    /**
     * Set by the cache refresh queue runner, if true, the refresh*Cache functions
     * will not run. This prevents MetaDataManager from calling a support method
     * that clears a cache elsewhere that in turn triggers another section or
     * module cache clear in the metadata manager.
     *
     * @var bool
     */
    protected static $inProcess = false;

    /**
     * Current process stack. Used internally by the refresh* methods to prevent
     * infinite self-referencing method calls.
     *
     * @var array
     */
    protected static $currentProcess = [];

    /**
     * @var array List of metadata keys for values that should be overriden rather than
     * merged client side with existing metadata .
     */
    protected static $defaultOverrides = [
        'fields',
        'module_list',
        'relationships',
        'currencies',
        'server_info',
        'module_tab_map',
        'hidden_subpanels',
        'config',
        'editable_dropdown_filters',
    ];

    /**
     * List of the various parts of metadata that are cached mapped to the method
     * name that handles the refreshing of that part. These are not to be
     * confused with sections (the individual elements of metadata). These are,
     * instead, the areas of metadata that contain their own caches and are thus
     * handled a little differently. Order here is important so do not change
     * this without good reason.
     * @var array
     */
    protected static $cacheParts = [
        self::MM_MODULES => 'refreshModulesCache',
        self::MM_LANGUAGES => 'refreshLanguagesCache',
        'section' => 'refreshSectionCache',
    ];

    /**
     * List of deleted languages when clearing the cache. Used in {@see rebuildCache}
     * when deleting current caches.
     *
     * @var array
     */
    protected $deletedLanguageCaches = [];

    /**
     * Indicates the state of the cleared metadata so that subsequent calls to
     * clear the cache in the same request are ignored
     *
     * @var boolean
     */
    protected static $cacheHasBeenCleared = false;

    /**
     * White listed properties which shall be copied from server side
     * configurations to client side configurations.
     *
     * @var array
     * @see getConfigProperties
     * @see parseConfigProperties
     */
    protected static $configProperties = [
        'list_max_entries_per_page' => true,
        'list_max_entries_per_subpanel' => true,
        'collapse_subpanels' => true,
        'max_record_fetch_size' => true,
        'max_record_link_fetch_size' => true,
        'upload_maxsize' => true,
        'mass_actions' => [
            'mass_update_chunk_size' => true,
            'mass_delete_chunk_size' => true,
            'mass_link_chunk_size' => true,
        ],
        'merge_duplicates' => [
            'merge_relate_fetch_concurrency' => true,
            'merge_relate_fetch_timeout' => true,
            'merge_relate_fetch_limit' => true,
            'merge_relate_update_concurrency' => true,
            'merge_relate_update_timeout' => true,
            'merge_relate_max_attempt' => true,
        ],
        'catalog_enabled' => true,
        'catalog_url' => true,
        'default_decimal_seperator' => true,
        'default_number_grouping_seperator' => true,
        'default_currency_significant_digits' => true,
        'logger' => [
            'level' => true,
            'write_to_server' => true,
        ],
        'calendar' => [
            'max_repeat_count' => true,
        ],
        'lead_conv_activity_opt' => true,
        'team_based_acl' => [
            'enabled' => true,
            'enabled_modules' => true,
        ],
        'preview_edit' => true,
        'allow_freeze_first_column' => true,
        'freeze_list_headers' => true,
        'commentlog' => [
            'maxchars' => true,
        ],
        'max_aggregate_email_attachments_bytes' => true,
        'new_email_addresses_opted_out' => true,
        'activity_streams_enabled' => true,
        'enable_link_to_drawer' => true,
        'push_notification' => true,
    ];

    /**
     * Map of configuration properties that should assume a different name than
     * the one provided by parse mechanism.
     *
     * <code>
     *     array(
     *       'parsedKey1' => 'newKey1',
     *       'parsedKey2' => 'newKey2',
     *    )
     * </code>
     *
     * @deprecated This should only be used to handle legacy code, thus should
     * removed when that code gets cleaned up.
     *
     * @var array
     * @see handleConfigPropertiesExceptions
     */
    protected static $configPropertiesExceptions = [
        'listMaxEntriesPerPage' => 'maxQueryResult',
        'listMaxEntriesPerSubpanel' => 'maxSubpanelResult',
        'defaultDecimalSeperator' => 'defaultDecimalSeparator',
        'defaultNumberGroupingSeperator' => 'defaultNumberGroupingSeparator',
    ];

    /**
     * Stores the loaded metadata
     *
     * @var array
     */
    protected $data = [];

    /**
     * These sections are skipped as part of a full metadata fetch either because
     * they are handled in a combination method like the modules section builder
     * or because they are handled separately, like override values
     *
     * @var array
     */
    protected $sectionsToSkip = [
        self::MM_OVERRIDEVALUES => true,
        self::MM_FULLMODULELIST => true,
        self::MM_MODULESINFO => true,
    ];

    /**
     * Explicit flag that tells the queue to run when the run method is called.
     * In most cases this will never be changed, but in the case of module builder
     * this will usually be turned off so that the postExecute method can force
     * it to run explicitly.
     *
     * @var boolean
     */
    protected static $runQueueOnCall = true;

    /**
     * Flag indicating that metadata caching is enabled
     *
     * @var bool
     */
    protected static $isCacheEnabled = true;

    /**
     * Name of the cache table used to store metadata cache data
     * @var string
     */
    protected static $cacheTable = 'metadata_cache';

    /**
     * @var MetaDataCache
     */
    protected $cache;

    /**
     * List of connector properties needed by the client
     *
     * @var array
     */
    protected $connectorProperties = [
        'id',
        'name',
        'enabled',
        'configured',
        'modules',
    ];

    /**
     * Additional vardefs that the front end may need to know about
     * @var array
     */
    protected $additionalVardefProps = [
        'dynamic_subpanel_name',
    ];

    /**
     * acl values for blocked access
     * @var array
     */
    protected $aclWithNoAccess = [
        'access' => 'no',
        'view' => 'no',
        'list' => 'no',
        'edit' => 'no',
        'delete' => 'no',
        'import' => 'no',
        'export' => 'no',
        'massupdate' => 'no',
    ];

    /**
     * access acl values for full access
     * @var array
     */
    protected $aclWithFullAccess = [
        'access' => 'yes',
        'view' => 'yes',
        'list' => 'yes',
        'edit' => 'yes',
        'delete' => 'yes',
        'import' => 'yes',
        'export' => 'yes',
        'massupdate' => 'yes',
    ];

    /**
     * idp config
     * @var
     */
    protected $idpConfig;

    /**
     * The constructor for the class. Sets the visibility flag, the visibility
     * string indicator and loads the appropriate metadata section list.
     *
     * @param array $platforms A list of clients
     * @param bool $public is this a public metadata grab
     */
    public function __construct($platforms = null, $public = false)
    {
        // To support previous iterations of MetaDataManager prior to 7.1, in
        // which the first required argument was a CurrentUser
        if ($platforms instanceof SugarBean) {
            // The first arg would have been User, the second arg platforms, which
            // could have been null but will be handled after this block is run
            $platforms = $public;

            // The public flag is a little trickier, since it wasn't required. If
            // it was passed, we need to grab it, otherwise just default it
            $public = false;
            if (func_num_args() === 3) {
                $public = func_get_arg(2);
            }

            // Let consumers know this isn't the correct use of this constuctor
            $this->logger->warning('MetaDataManager no longer accepts a User object as an arguments');
        }

        if ($platforms == null) {
            $platforms = ['base'];
        }

        // We should have an array of platforms
        if (!is_array($platforms)) {
            $platforms = (array)$platforms;
        }

        // Base needs to be in place if it isn't
        if (!in_array('base', $platforms)) {
            $platforms[] = 'base';
        }

        $this->platforms = $platforms;
        $this->public = $public;

        if ($public) {
            $this->visibility = 'public';
        }

        // Load up the metadata sections
        $this->loadSections($public);

        $this->db = DBManagerFactory::getInstance();

        $this->setLogger(new NullLogger());
    }

    /**
     * Gets member cache, the MetadataCache
     *
     *
     * @return MetaDataCache
     */
    protected function getCache()
    {

        if (self::$isCacheEnabled && !isset($this->cache)) {
            if ($this->db === false) {
                $this->db = DBManagerFactory::getInstance();
            }

            if ($this->db !== false) {
                $this->cache = static::newCache($this->db, $this->logger);
            }
        }

        if (is_null($this->cache)) {
            return false;
        } else {
            return $this->cache;
        }
    }

    protected static function newCache(DBManager $db = null, LoggerInterface $logger = null)
    {
        $db = $db ?: DBManagerFactory::getInstance();
        $cache = new MetaDataCache($db);
        $logger = $logger ?: LoggerFactory::getLogger('metadata');
        $cache->setLogger($logger);

        return $cache;
    }

    /**
     * Logs message and context with stack trace and additional information
     * such as user id, client type, request url.
     * This method should only be used when called in-frequently as it has heavy logging.
     *
     * @param LoggerInterface $logger
     * @param string $message
     * @param MetaDataContextInterface $context
     */
    protected static function logDetails(LoggerInterface $logger, $message, MetaDataContextInterface $context = null)
    {
        if ($context) {
            $message .= sprintf('; Context=%s', get_class($context));
        }

        $logger->info($message);
    }

    /**
     * Gets a class name for a metadata manager
     *
     * @param string $platform The platform of the metadata manager class
     * @return string
     */
    public static function getManagerClassName($platform)
    {
        return 'MetaDataManager' . ucfirst(strtolower($platform));
    }

    /**
     * Simple factory for getting a metadata manager
     *
     * @param string $platform The platform for the metadata
     * @param bool $public Public or private
     * @param bool $fresh Whether to skip the cache and get a new manager
     * @return MetaDataManager
     */
    public static function getManager($platform = null, $public = false, $fresh = false)
    {
        if ($platform == null) {
            $platform = ['base'];
        }
        $platform = (array)$platform;

        // Build a simple key
        $key = implode(':', $platform) . ':' . intval($public);

        if ($fresh || empty(self::$managers[$key])) {
            // Get the platform metadata class name
            $class = self::getManagerClassName(''); // MetaDataManager
            $path = 'include/MetaDataManager/';
            $found = false;
            foreach ($platform as $type) {
                $mmClass = self::getManagerClassName($type);
                $file = $path . $mmClass . '.php';
                if (SugarAutoLoader::requireWithCustom($file)) {
                    $class = SugarAutoLoader::customClass($mmClass);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                SugarAutoLoader::requireWithCustom($path . $class . '.php');
                $class = SugarAutoLoader::customClass($class);
            }

            $manager = new $class($platform, $public);

            $manager->setLogger(LoggerFactory::getLogger('metadata'));
            // Cache it and move on
            self::$managers[$key] = $manager;
        }

        return self::$managers[$key];
    }

    /**
     * Reset static instances of metadata managers. May be used in unit tests.
     */
    public static function resetManagers()
    {
        self::$managers = [];
    }

    /**
     * Gets a list of metadata sections based on visibility
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * For a specific module get any existing Subpanel Definitions it may have
     * @param string $moduleName
     * @return array
     */
    public function getSubpanelDefs($moduleName)
    {
        $parent_bean = BeanFactory::newBean($moduleName);
        //Hack to allow the SubPanelDefinitions class to check the correct module dir
        if (!$parent_bean) {
            $parent_bean = (object)['module_dir' => $moduleName];
        }

        $spd = new SubPanelDefinitions($parent_bean, '', '', $this->platforms[0]);
        $layout_defs = $spd->layout_defs;

        if (is_array($layout_defs) && isset($layout_defs['subpanel_setup'])) {
            foreach ($layout_defs['subpanel_setup'] as $name => $subpanel_info) {
                $aSubPanel = $spd->load_subpanel($name, false, false, true);

                if (!$aSubPanel) {
                    continue;
                }

                if ($aSubPanel->isCollection()) {
                    $collection = [];
                    foreach ($aSubPanel->sub_subpanels as $key => $subpanel) {
                        $collection[$key] = $subpanel->panel_definition;
                    }
                    $layout_defs['subpanel_setup'][$name]['panel_definition'] = $collection;
                } else {
                    $layout_defs['subpanel_setup'][$name]['panel_definition'] = $aSubPanel->panel_definition;
                }
            }
        }

        return $layout_defs;
    }

    /**
     * This method collects all view data for a modul
     *
     * @param $moduleName The name of the sugar module to collect info about.
     * @param MetaDataContextInterface|null $context Metadata context
     *
     * @return Array A hash of all of the view data.
     */
    public function getModuleViews($moduleName, MetaDataContextInterface $context = null)
    {
        $data = $this->getModuleClientData('view', $moduleName, $context);
        $data = $this->removeDisabledFields($data);
        return $data;
    }

    /**
     * This method collects all dropdown based view data for a module
     *
     * @param $moduleName The name of the sugar module to collect info about.
     * @param MetaDataContextInterface|null $context Metadata context
     *
     * @return Array A hash of all of the dropdown based view data.
     */
    public function getModuleDropdownViews($moduleName, MetaDataContextInterface $context = null)
    {
        $data = $this->getModuleClientData('dropdownViews', $moduleName, $context);
        $data = $this->removeDisabledFields($data);
        return $data;
    }

    /**
     * Removes disabled fields from view definition
     *
     * @param array $data
     * @return array
     */
    protected function removeDisabledFields(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($key === 'fields') {
                    $value = array_filter($value, function ($field) {
                        return !is_array($field) || !isset($field['enabled']) || $field['enabled'];
                    });

                    // make sure the resulting array has no gaps in keys
                    $value = array_values($value);
                } else {
                    $value = $this->removeDisabledFields($value);
                }
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Returns metadata context for current user
     *
     * @return MetaDataContextInterface
     */
    protected function getCurrentUserContext()
    {
        $user = $this->getCurrentUser();
        if ($user && $user->id) {
            return new MetaDataContextUser($user);
        }

        return $this->getDefaultContext();
    }

    /**
     * Returns default metadata context
     *
     * @return MetaDataContextInterface
     */
    protected function getDefaultContext()
    {
        return new MetaDataContextDefault();
    }

    /**
     * This method collects all view data for a modul
     *
     * @param $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all of the view data.
     */
    public function getModuleMenu($moduleName)
    {
        return $this->getModuleClientData('menu', $moduleName);
    }

    /**
     * This method collects all view data for a module
     *
     * @param $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all of the view data.
     */
    public function getModuleLayouts($moduleName)
    {
        return $this->getModuleClientData('layout', $moduleName);
    }

    /**
     * This method collects all field data for a module
     *
     * @param string $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all of the view data.
     */
    public function getModuleFields($moduleName)
    {
        return $this->getModuleClientData('field', $moduleName);
    }

    /**
     * This method collects all filter data for a module
     *
     * @param string $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all of the filter data.
     */
    public function getModuleFilters($moduleName)
    {
        return $this->getModuleClientData('filter', $moduleName);
    }

    /**
     * This method collects all dependency data for a module (except view specific dependencies)
     *
     * @param string $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all of the dependency data.
     */
    public function getModuleDependencies($moduleName)
    {
        return $this->getModuleClientData('dependency', $moduleName);
    }

    /**
     * This method collects all the collection controllers for a module
     *
     * @param string $moduleName The name of the sugar module to collect info about.
     *
     * @return Array A hash of all collections and models controllers
     */
    public function getModuleDatas($moduleName)
    {
        return $this->getModuleClientData('data', $moduleName);
    }

    /**
     * Gets metadata for all modules
     *
     * @param MetaDataContextInterface|null $context Metadata context
     * @return array An array of hashes containing the modules and their
     * metadata.
     */
    public function getModulesData(MetaDataContextInterface $context = null)
    {
        $filterModules = $this->getFilterModulesFlag($context);

        if (!isset($this->data['full_module_list'])) {
            $this->data['full_module_list'] = $this->getModuleList($filterModules);
        }
        $moduleList = $this->data['full_module_list'];
        $modules = [];
        foreach ($moduleList as $key => $module) {
            if ($key == '_hash') {
                continue;
            }

            $bean = BeanFactory::newBean($module);
            $modules[$module] = $this->getModuleData($module, $context);
            $this->relateFields($data, $module, $bean);
        }
        return $modules;
    }

    /**
     * The collector method for modules.  Gets metadata for all of the module specific data
     *
     * @param string $moduleName The name of the module to collect metadata about.
     * @param MetaDataContextInterface|null $context Metadata context
     * @return array An array of hashes containing the metadata.  Empty arrays are
     * returned in the case of no metadata.
     */
    public function getModuleData($moduleName, MetaDataContextInterface $context = null)
    {
        $data = [];
        $vardefs = $this->getVarDef($moduleName);
        if (!empty($vardefs['fields']) && is_array($vardefs['fields'])) {
            $vardefs['fields'] = MassUpdate::setMassUpdateFielddefs($vardefs['fields'], $moduleName);
        }

        $data['fields'] = $vardefs['fields'] ?? [];
        $data['templateEditableFields'] = $vardefs['template_editable_fields'] ?? [];
        $data['templateRestrictedActions'] = $vardefs['template_restricted_actions'] ?? [];
        $data['templateSpecificActions'] = $vardefs['template_specific_actions'] ?? [];
        // Add the _hash for the fields array
        $data['fields']['_hash'] = md5(serialize($data['fields']));
        $data['nameFormat'] = $vardefs['name_format_map'] ?? null;
        $data['views'] = $this->getModuleViews($moduleName, $context);
        $data['dropdownViews'] = $this->getModuleDropdownViews($moduleName, $context);
        $data['datas'] = $this->getModuleDatas($moduleName);
        $data['layouts'] = $this->getModuleLayouts($moduleName);
        $data['fieldTemplates'] = $this->getModuleFields($moduleName);
        $data['menu'] = $this->getModuleMenu($moduleName);
        $data['config'] = $this->getModuleConfig($moduleName);
        $data['filters'] = $this->getModuleFilters($moduleName);
        // Indicate whether Module has Table Based ACLs enabled
        $data['isTBAEnabled'] = TeamBasedACLConfigurator::isAccessibleForModule($moduleName);
        $deps = $this->getModuleDependencies($moduleName);
        if (!empty($deps) && !empty($deps['dependencies'])) {
            $data['dependencies'] = $deps['dependencies'];
        }

        // Items related to how modules are displayed
        if ($moduleName === 'Home') {
            $data['color'] = $data['config']['color'] ?? 'ocean';
            $data['icon'] = $data['config']['icon'] ?? 'sicon-default-module-lg';
            $data['display_type'] = $data['config']['display_type'] ?? 'icon';
        } else {
            $data['color'] = $vardefs['color'] ?? 'ocean';
            $data['icon'] = $vardefs['icon'] ?? 'sicon-default-module-lg';
            $data['display_type'] = $vardefs['display_type'] ?? 'icon';
        }

        // Archiving is always on unless we explicitly say it isn't
        $data['archiveEnabled'] = !(isset($vardefs['archive']) && $vardefs['archive'] === false);

        $data['isAudited'] = isset($vardefs['audited']) && $vardefs['audited'] === true;

        // Indicates whether a module is Escalatable
        $data['isEscalatable'] = isset($vardefs['escalatable']) && $vardefs['escalatable'] === true;

        // Indicate whether Module Has duplicate checking enabled --- Rules must exist and Enabled flag must be set
        $data['dupCheckEnabled'] = isset($vardefs['duplicate_check']) && isset($vardefs['duplicate_check']['enabled']) && ($vardefs['duplicate_check']['enabled'] === true);

        $visual_pipeline = new VisualPipeline();
        $data['isPipelineEnabled'] = $visual_pipeline->isEnabledForModule($moduleName);

        $data['isPipelineExcluded'] = $visual_pipeline->isModulePipelineExcluded($moduleName);
        // Indicate whether a Module has activity stream enabled
        $data['activityStreamEnabled'] = SugarConfig::getInstance()->get('activity_streams_enabled', false) ?
            ActivityQueueManager::isEnabledForModule($moduleName) : false;

        $data['ftsEnabled'] = SugarSearchEngineMetadataHelper::isModuleFtsEnabled($moduleName);

        // TODO we need to have this kind of information on the module itself not hacked around on globals
        $data['isBwcEnabled'] = safeInArray($moduleName, $GLOBALS['bwcModules']);

        $seed = BeanFactory::newBean($moduleName);
        $data['globalSearchEnabled'] = $this->getGlobalSearchEnabled($seed, $vardefs, $this->platforms[0]);

        if (!empty($seed)) {
            $favoritesEnabled = ($seed->isFavoritesEnabled() !== false) ? true : false;
            $data['favoritesEnabled'] = $favoritesEnabled;
        }
        // Currently no way to disable following
        // But this flag is here in case we add that feature in the future
        $data['followingEnabled'] = true;

        // Check if module's vardefs contains any of the properties found in the additional vardef properties array
        foreach ($this->additionalVardefProps as $prop) {
            if (isset($vardefs[$prop])) {
                $data['additionalProperties'][$prop] = $vardefs[$prop];
            }
        }

        // Check if a default filter for relate fields exists for this module.
        if (!empty($vardefs['default_relate_filter'])) {
            $data['defaultRelateFilter'] = $vardefs['default_relate_filter'];
        }

        // Indicate whether show KPI metric badges on console pages Module tab
        $data['consoleTabBadges'] = $vardefs['console_tab_badges'] ?? false;

        $data['_hash'] = $this->hashChunk($data);
        return $data;
    }

    /**
     * Helper to determine if vardef for module has global search enabled or not.
     * @param array $seed the new bean created from module name passed to BeanFactory::newBean
     * @param array $vardefs The vardefs
     * @param string $platform The platform
     * @return boolean indicating whether or not global search is enabled
     */
    public function getGlobalSearchEnabled($seed, $vardefs, $platform = null)
    {
        return !empty($vardefs['full_text_search']);
    }

    /**
     * Get the config for a specific module from the Administration Layer
     *
     * @param string $moduleName The Module we want the data back for.
     * @return array
     */
    public function getModuleConfig($moduleName)
    {
        /* @var $admin Administration */
        $admin = BeanFactory::newBean('Administration');

        return $admin->getConfigForModule($moduleName, $this->platforms[0]);
    }

    /**
     * The collector method for relationships.
     *
     * @return array An array of relationships, indexed by the relationship name
     */
    public function getRelationshipData()
    {
        $relFactory = SugarRelationshipFactory::getInstance();

        // Request fresh relationship metadata always
        $data = $relFactory->getRelationshipDefs(true);

        // Sanity check the rel defs, just in case they came back empty
        if (is_array($data)) {
            // Certain elements of the relationship defs need to be pruned
            $unsets = ['table', 'fields', 'indices', 'relationships'];
            foreach ($data as $relKey => $relData) {
                // Prune the relationship defs as needed
                foreach ($unsets as $unset) {
                    unset($relData[$unset]);
                }

                // Sort each def array for consistency to ensure sameness between
                // metadata cache refreshes
                ksort($relData);

                // Reset the defs for this key
                $data[$relKey] = $relData;
            }
        }

        // To maintain hashes between requests, make sure this array is always
        // in the same order. Otherwise, the serialized value of this data will
        // potentially be different from one request to another.
        ksort($data);

        $data['_hash'] = $this->hashChunk($data);

        return $data;
    }

    /**
     * Gets vardef info for a given module.
     *
     * @param string $moduleName The name of the module to collect vardef information about.
     * @return array The vardef's $dictonary array.
     */
    public function getVarDef($moduleName)
    {
        $data = [];
        $obj = BeanFactory::getObjectName($moduleName);

        if ($obj) {
            global $dictionary;
            VardefManager::loadVardef($moduleName, $obj);
            if (isset($dictionary[$obj])) {
                $data = $dictionary[$obj];
            }

            // vardefs are missing something, for consistency let's populate some arrays
            if (!isset($data['fields'])) {
                $data['fields'] = [];
            }
            if (!isset($data['relationships'])) {
                $data['relationships'] = [];
            }
        }

        if ($moduleName === 'Users') {
            $bean = BeanFactory::newBean($moduleName);
            if ($bean) {
                $data = $bean->patchVardefs($data);
            }
        }

        // Bug 56505 - multiselect fields default value wrapped in '^' character
        if (!empty($data['fields'])) {
            $data['fields'] = $this->getMetaDataHacks()->normalizeFieldDefs($data);
        }

        if (!isset($data['relationships'])) {
            $data['relationships'] = [];
        }

        return $data;
    }

    /**
     * Gets the ACL's for the module, will also expand them so the client side of the ACL's don't have to do as many checks.
     *
     * @param string $module The module we want to fetch the ACL for
     * @param object $userObject The user object for the ACL's we are retrieving.
     * @param object|bool $bean The SugarBean for getting specific ACL's for a module
     * @param bool $showYes Do not unset Yes Results
     * @return array       Array of ACL's, first the action ACL's (access, create, edit, delete) then an array of the field level acl's
     */
    public function getAclForModule($module, $userObject, $bean = false, $showYes = false)
    {
        //Cache ACL per user/module
        if (empty($bean->id)) {
            $cacheKey = "$module-{$userObject->id}";
            if ($showYes) {
                $cacheKey .= '-yes';
            }
            if (isset(static::$aclForModules[$cacheKey])) {
                return static::$aclForModules[$cacheKey];
            }
        }
        $outputAcl = ['fields' => []];
        $outputAcl['admin'] = ($userObject->isAdminForModule($module)) ? 'yes' : 'no';
        $outputAcl['developer'] = ($userObject->isDeveloperForModule($module)) ? 'yes' : 'no';

        // This section of code is a portion of the code referred
        // to as Critical Control Software under the End User
        // License Agreement.  Neither the Company nor the Users
        // may modify any portion of the Critical Control Software.
        if (!AccessControlManager::instance()->allowModuleAccess($module)) {
            $outputAcl = array_merge($outputAcl, $this->aclWithNoAccess);
            //END REQUIRED CODE DO NOT MODIFY
        } elseif (!SugarACL::moduleSupportsACL($module)) {
            $outputAcl = array_merge($outputAcl, $this->aclWithFullAccess);
        } else {
            $context = [
                'user' => $userObject,
            ];
            if ($bean instanceof SugarBean) {
                $context['bean'] = $bean;
            }

            // if the bean is not set, or a new bean.. set the owner override
            // this will allow fields marked Owner to pass through ok.
            if ($bean == false || empty($bean->id) || (isset($bean->new_with_id) && $bean->new_with_id == true)) {
                $context['owner_override'] = true;
            }

            $moduleAcls = SugarACL::getUserAccess($module, [], $context);

            // Bug56391 - Use the SugarACL class to determine access to different actions within the module
            foreach (SugarACL::$all_access as $action => $bool) {
                $outputAcl[$action] = ($moduleAcls[$action] == true || !isset($moduleAcls[$action])) ? 'yes' : 'no';
            }

            // Only loop through the fields if we have a reason to, admins give full access on everything, no access gives no access to anything
            if ($outputAcl['access'] == 'yes') {
                // Currently create just uses the edit permission, but there is probably a need for a separate permission for create
                $outputAcl['create'] = $outputAcl['edit'];

                // Custom ACL strategies may have special rules around "create". Try to respect them.
                if ($outputAcl['edit'] === 'yes') {
                    $createAcl = SugarACL::getUserAccess($module, ['create' => true], $context);

                    // Only change it if we're taking the permission away. Otherwise, the permission for "edit" is a
                    // reasonable default.
                    if (!$createAcl['create']) {
                        $outputAcl['create'] = 'no';
                    }
                }

                if ($bean === false) {
                    $bean = BeanFactory::newBean($module);
                }

                // we cannot use ACLField::getAvailableFields because it limits the fieldset we return.  We need all fields
                // for instance assigned_user_id is skipped in getAvailableFields, thus making the acl's look odd if Assigned User has ACL's
                // only assigned_user_name is returned which is a derived ["fake"] field.  We really need assigned_user_id to return as well.
                if (empty($GLOBALS['dictionary'][$bean->object_name]['fields'])) {
                    if (empty($bean->acl_fields)) {
                        $fieldsAcl = [];
                    } else {
                        $fieldsAcl = $bean->field_defs;
                    }
                } else {
                    $fieldsAcl = $GLOBALS['dictionary'][$bean->object_name]['fields'];
                    if (isset($GLOBALS['dictionary'][$bean->object_name]['acl_fields']) && $GLOBALS['dictionary'][$bean->object_name] === false) {
                        $fieldsAcl = [];
                    }
                }
                // get the field names

                SugarACL::listFilter($module, $fieldsAcl, $context, ['add_acl' => true]);
                $fieldsAcl = $this->getMetaDataHacks()->fixAcls($fieldsAcl);
                foreach ($fieldsAcl as $field => $fieldAcl) {
                    switch ($fieldAcl['acl']) {
                        case SugarACL::ACL_READ_WRITE:
                            // Default, don't need to send anything down
                            break;
                        case SugarACL::ACL_READ_ONLY:
                            $outputAcl['fields'][$field]['write'] = 'no';
                            $outputAcl['fields'][$field]['create'] = 'no';
                            break;
                        case 2:
                            $outputAcl['fields'][$field]['read'] = 'no';
                            break;
                        case SugarACL::ACL_NO_ACCESS:
                        default:
                            $outputAcl['fields'][$field]['read'] = 'no';
                            $outputAcl['fields'][$field]['write'] = 'no';
                            $outputAcl['fields'][$field]['create'] = 'no';
                            break;
                    }
                    $licenseAccess = AccessControlManager::instance()->allowFieldAccess($module, $field);
                    if ($licenseAccess === false) {
                        $outputAcl['fields'][$field]['create'] = 'no';
                        $outputAcl['fields'][$field]['write'] = 'no';
                        $outputAcl['fields'][$field]['license'] = 'no';
                    }
                }
            }
        }
        // there are times when we need the yes results, for instance comparing access for a record
        if ($showYes === false) {
            // for brevity, filter out 'yes' fields since UI assumes 'yes'
            foreach ($outputAcl as $k => $v) {
                if ($v == 'yes') {
                    unset($outputAcl[$k]);
                }
            }
        }
        $outputAcl['_hash'] = $this->hashChunk($outputAcl);

        if (!empty($cacheKey)) {
            static::$aclForModules[$cacheKey] = $outputAcl;
        }

        return $outputAcl;
    }

    /**
     * Fields accessor, gets sugar fields
     *
     * @return array array of sugarfields with a hash
     */
    public function getSugarFields()
    {
        return $this->getSystemClientData('field');
    }

    /**
     * Filters accessor, gets sugar filter operators
     *
     * @return array array of filters with a hash
     */
    public function getSugarFilters()
    {
        return $this->getSystemClientData('filter');
    }

    /**
     * Views accessor Gets client views
     *
     * @return array
     */
    public function getSugarViews()
    {
        return $this->getSystemClientData('view');
    }

    /**
     * Gets client layouts, similar to module specific layouts except used on a
     * global level by the clients consuming this data
     *
     * @return array
     */
    public function getSugarLayouts()
    {
        return $this->getSystemClientData('layout');
    }

    /**
     * Gets client models and collection controllers that maybe a platform would like
     * to override.
     *
     * @return array
     */
    public function getSugarData()
    {
        return $this->getSystemClientData('data');
    }

    /**
     * Gets client files of type $type (view, layout, field) for a module or for the system
     *
     * @param string $type The type of files to get
     * @param string $module Module name (leave blank to get the system wide files)
     * @return array
     */
    public function getSystemClientData($type)
    {
        // This is a semi-complicated multi-step process, so we're going to try and make this as easy as possible.
        // This should get us a list of the client files for the system
        $fileList = MetaDataFiles::getClientFiles($this->platforms, $type);

        // And this should get us the contents of those files, properly sorted and everything.
        $results = MetaDataFiles::getClientFileContents($fileList, $type);

        return $results;
    }

    /**
     * Gets the client cache for a given module
     *
     * @param string $type View, Layout, etc
     * @param string $module
     * @param MetaDataContextInterface|null $context Metadata context
     * @return array
     */
    public function getModuleClientData($type, $module, MetaDataContextInterface $context = null)
    {
        return MetaDataFiles::getModuleClientCache($this->platforms, $type, $module, $context);
    }

    /**
     * The collector method for the module strings
     *
     * @param string $moduleName The name of the module
     * @param string $language The language for the translations
     * @return array  The module strings for the requested language
     */
    public function getModuleStrings($moduleName, $language = 'en_us')
    {
        // Bug 58174 - Escaped labels are sent to the client escaped
        // TODO: SC-751, fix the way languages merge
        $strings = return_module_language($language, $moduleName);
        if (is_array($strings)) {
            foreach ($strings as $k => $v) {
                $strings[$k] = $this->decodeStrings($v);
            }
        }

        return $strings;
    }

    /**
     * The collector method for the app strings
     *
     * @param string $lang The language you wish to fetch the app strings for
     * @return array  The app strings for the requested language
     */
    public function getAppStrings($lang = 'en_us')
    {
        $strings = return_application_language($lang);
        if (is_array($strings)) {
            foreach ($strings as $k => $v) {
                $strings[$k] = $this->decodeStrings($v);
            }
        }

        return $strings;
    }

    /**
     * The collector method for the app strings
     *
     * @param string $lang The language you wish to fetch the app list strings for
     * @return array  The app list strings for the requested language
     */
    public function getAppListStrings($lang = 'en_us', $useTuples = false)
    {
        $strings = return_app_list_strings_language($lang);
        if (is_array($strings)) {
            foreach ($strings as $k => $v) {
                $list = $this->decodeStrings($v);
                if ($useTuples) {
                    $list = $this->convertToTuples($list);
                }
                $strings[$k] = $list;
            }
        }

        return $strings;
    }

    /**
     * Gets a list of platforms found in the application.
     *
     * @return array
     */
    public static function getPlatformList()
    {
        return self::getPlatformMetadata('platforms');
    }

    /**
     * Gets a list of platform options found in the application
     *
     * @return array Platform options in the form of
     * [
     *   'base' => [
     *     'disable_notifications' => false
     *     ...
     *    ],
     *   'portal' => [...]
     *   ...
     * ]
     */
    public static function getPlatformOptions()
    {
        return self::getPlatformMetadata('platformoptions');
    }

    /**
     * Util to get a list of metadata defined in a global array, including
     * extension metadata
     *
     * E.g. clients/platforms.php
     *
     * @param String $metaType Type of metadata to load (e.g. platforms)
     * @return array Metadata loaded from files
     */
    protected static function getPlatformMetadata(string $metaType): array
    {
        ${$metaType} = [];
        $metaDefs = SugarAutoLoader::existingCustom("clients/$metaType.php");
        if ($metaExtension = SugarAutoLoader::loadExtension($metaType)) {
            $metaDefs[] = $metaExtension;
        }
        foreach ($metaDefs as $file) {
            require $file;
        }
        return ${$metaType};
    }

    /**
     * Gets a list of platforms that currently have cached metadata. This is used
     * in methods that refresh parts of the cache and prevents the unnecessary
     * building of caches for platforms that don't need to be primed.
     *
     * @param boolean $addBase Flag to determine whether to add the base platform by default
     * @return array
     */
    public static function getPlatformsWithCaches($addBase = true)
    {
        $platforms = [];

        // Add base to the list by default
        if ($addBase) {
            $platforms['base'] = 'base';
        }

        $platforms = array_merge(
            $platforms,
            self::getPlatformsWithCachesInFilesystem(),
            self::getPlatformsWithCachesInDatabase()
        );

        // TODO - Re-Filter the list by known platforms to prevent builds of dead or invalid platforms
        // once unknown platforms are no longer allowed across the board
        return array_unique($platforms);
    }

    /**
     * Returns list of platforms that have cached metadata in filesystem cache.
     *
     * @return array
     */
    protected static function getPlatformsWithCachesInFilesystem()
    {
        $platforms = [];

        // Get the listing of files in the cache directory
        $caches = glob(sugar_cached('api/metadata/') . '*.*');
        foreach ($caches as $cache) {
            $file = basename($cache, '.' . pathinfo($cache, PATHINFO_EXTENSION));
            // If the filename fits the pattern of a metadata cache file get the
            // platform for the file so long as it isn't base
            preg_match('/^.*_(.*)_(private|public)/', $file, $m);
            if (isset($m[1])) {
                $platforms[$m[1]] = $m[1];
            }
        }

        return array_values($platforms);
    }

    /**
     * Returns list of platforms that have cached metadata in database cache.
     *
     * @param MetaDataCache $cache (optional)
     *
     * @return array
     */
    protected static function getPlatformsWithCachesInDatabase(MetaDataCache $cache = null)
    {
        $platforms = [];

        if (!static::$isCacheEnabled) {
            return $platforms;
        }

        if (!$cache) {
            $db = DBManagerFactory::getInstance();
            $cache = static::newCache($db);
        }

        $types = $cache->getKeys();
        foreach ($types as $type) {
            // If the cache key fits the pattern of a metadata cache key get the
            // platforms for the cache entry
            // @see static::getCachedMetadataHashKey()
            if (preg_match('/^meta:hash(:public)?:(.*)$/', $type, $matches)) {
                $key_platforms = explode(',', substr($type, strrpos($type, ':') + 1));
                foreach ($key_platforms as $platform) {
                    $platforms[$platform] = $platform;
                }
            }
        }

        return array_values($platforms);
    }

    /**
     * Recursive decoder that handles decoding of HTML entities in metadata strings
     * before returning them to a client
     *
     * @param mixed $source
     * @return array|string
     */
    protected function decodeStrings($source)
    {
        if (is_string($source)) {
            return html_entity_decode($source, ENT_QUOTES, 'UTF-8');
        } else {
            if (is_array($source)) {
                foreach ($source as $k => $v) {
                    $source[$k] = $this->decodeStrings($v);
                }
            }

            return $source;
        }
    }

    /**
     * Converts an associative array of strings to a flat array of tuples to preserve ordering
     * @param Array $list
     *
     */
    protected function convertToTuples($list)
    {
        if (!is_array($list)) {
            return $list;
        }
        $ret = [];
        foreach ($list as $key => $val) {
            $ret[] = [$key, $val];
        }
        return $ret;
    }

    /**
     * Registers the API metadata cache to be cleared at shutdown
     *
     * @param bool $deleteModuleClientCache Should we also delete the client file
     *             cache of the modules
     * @param bool $clearNow Tells this method to clear the cache now instead of
     *             at shutdown
     * @static
     */
    public static function clearAPICache($deleteModuleClientCache = true, $clearNow = false)
    {
        // True/false stack for handling both client cache cases
        $key = $deleteModuleClientCache ? 1 : 0;

        // If we are in unit tests we need to fire this off right away
        if ($clearNow || (defined('SUGAR_PHPUNIT_RUNNER') && SUGAR_PHPUNIT_RUNNER === true)) {
            self::clearAPICacheOnShutdown($deleteModuleClientCache);
        } elseif (($key === 0 && empty(self::$clearCacheOnShutdown)) || !isset(self::$clearCacheOnShutdown[$key])) {
            // Will only clear cache if
            //  - A) delete module cache is false and there is no stack of clears, OR
            //  - B) delete module cache is true and it hasn't already been called with true
            //
            // This prevents calling this once each for true and false when a true
            // would handle what a false would anyway
            register_shutdown_function(['MetaDataManager', 'clearAPICacheOnShutdown'], $deleteModuleClientCache, getcwd());
            self::$clearCacheOnShutdown[$key] = true;
        }
    }

    /**
     * Clears the API metadata cache of all cache files
     *
     * @param bool $deleteModuleClientCache Should we also delete the client file cache of the modules
     * @param string $workingDirectory directory to chdir into before starting the clears
     * @static
     */
    public static function clearAPICacheOnShutdown($deleteModuleClientCache = true, $workingDirectory = '')
    {
        if (!self::getCacheHasBeenCleared()) {
            //shutdown functions are not always called from the same working directory as the script that registered it
            //Need to chdir to ensure we can find the correct files
            if (!empty($workingDirectory)) {
                chdir($workingDirectory);
            }


            if ($deleteModuleClientCache) {
                // Delete this first so there is no race condition between deleting a metadata cache
                // and the module client cache being stale.
                MetaDataFiles::clearModuleClientCache();
            }

            // Wipe out any files from the metadata cache directory
            $metadataFiles = glob(sugar_cached('api/metadata/') . '*');
            if (is_array($metadataFiles)) {
                foreach ($metadataFiles as $metadataFile) {
                    // This removes the file and the reference from the map. This does
                    // NOT save the file map since that would be expensive in a loop
                    // of many deletes.
                    unlink($metadataFile);
                }
            }
            $cache = new MetaDataCache(DBManagerFactory::getInstance());
            $cache->reset();

            // clear the platform cache from sugar_cache to avoid out of date data as well as platform component files
            $platforms = self::getPlatformList();
            foreach ($platforms as $platform) {
                $jsFiles = glob(sugar_cached("javascript/{$platform}/") . '*');
                if (is_array($jsFiles)) {
                    foreach ($jsFiles as $jsFile) {
                        unlink($jsFile);
                    }
                }
            }
        }
    }

    /**
     * Determines if a section is valid for this visibility
     *
     * @param string $section
     * @return bool
     */
    protected function isValidSection($section)
    {
        return in_array($section, $this->sections);
    }

    /**
     * Rebuilds the modules section of the metadata. This will cover all modules
     * metadata. To refresh a single module or collection of modules, use
     * refreshModulesCache().
     *
     * @param array $data Existing metadata
     * @param MetaDataContextInterface $context Metadata context
     * @return array
     */
    protected function rebuildModulesSection($data, MetaDataContextInterface $context)
    {
        // If we are in a rebuild process for the modules section, clear the module
        // client cache so that module metadata is fresh
        if (isset(self::$currentProcess[self::MM_MODULES])) {
            MetaDataFiles::clearModuleClientCache();
        }
        return $this->setupModuleLists($data, $context);
    }

    /**
     * Rebuilds the JS Source File section of the metadata. Called by refreshSectionCache
     *
     * @param array $data Existing metadata
     * @param MetaDataContextInterface $context Metadata context
     * @return mixed
     */
    protected function rebuildJssourceSection($data, MetaDataContextInterface $context)
    {
        static::logDetails($this->logger, 'rebuildJssourceSection', $context);
        $data['jssource'] = $this->buildJavascriptComponentFile($data, !$this->public);
        //If this is private meta, we will still need to build the public javascript to verify that it hasn't changed.
        //If it has changed, the client will need to refresh to load it.
        if (!$this->public) {
            $publicJsSource = $this->getPublicJsSource($context);
            if ($publicJsSource) {
                $data['jssource_public'] = $publicJsSource;
            }
        }
        return $data;
    }

    /**
     * Returns the file path for the current public javascript component file
     * @param MetaDataContextInterface $context
     *
     * @return bool|string
     *
     */
    protected function getPublicJsSource(MetaDataContextInterface $context)
    {
        $publicJsSource = false;
        if (!$this->public) {
            $this->public = true;
            $cache = $this->getMetadataCache(true, $context);
            if (empty($cache['jssource'])) {
                $publicMM = MetaDataManager::getManager($this->platforms, true);
                $args = $this->args ?? [];
                $cache = $publicMM->getMetadata($args);
            }
            if ($cache && !empty($cache['jssource'])) {
                $publicJsSource = $cache['jssource'];
            }
            $this->public = false;
        }


        return $publicJsSource;
    }

    /**
     * Rebuilds the label section of metadata and clears language caches
     *
     * @param array $languages Array of languages to refresh the caches of
     * @param MetaDataContextInterface $context Metadata context
     */
    protected function rebuildLanguagesCache($languages, MetaDataContextInterface $context)
    {
        // We will always need the metadata for this process, but only if there
        // is existing metadata to work (why build a section of an empty set)
        $data = $this->getMetadataCache(true, $context);

        // NOTE: Do not try to rebuild language cache files as this could be
        // problematic on installations with many installed languages, like OD
        if (!empty($data)) {
            static::logDetails($this->logger, 'rebuildLanguagesCache.', $context);
            $this->clearLanguagesCache();
            $data = $this->loadSectionMetadata(self::MM_LABELS, $data, $context);
            $data = $this->loadSectionMetadata(self::MM_ORDEREDLABELS, $data, $context);
            $data = $this->normalizeMetadata($data);
            $data['_hash'] = $this->hashChunk($data);
            $this->putMetadataCache($data, $context);
        }
    }

    /**
     * Rebuilds the metadata for a module or modules provided the metadata cache
     * exists already.
     *
     * @param string|array $modules A single module or array of modules
     * @param MetaDataContextInterface $context Metadata context
     */
    protected function rebuildModulesCache($modules, MetaDataContextInterface $context)
    {
        // Only write if were actually asked for
        $write = false;

        // Only process if there are modules to work on
        if (!empty($modules) && $this->isValidSection('modules')) {
            // Module files should be rebuilt despite data existence.
            MetaDataFiles::clearModuleClientCache($modules);

            // Same as with section caching, we only want to rebuild the
            // modules metadata if there are modules metadata already.
            $data = $this->getMetadataCache(true, $context);

            if (!empty($data)) {
                // Handle the module(s)
                foreach ((array)$modules as $module) {
                    // Only work on modules that was have already grabbed
                    if (isset($data['modules'][$module])) {
                        $index = 'module:' . $module;
                        if (isset(self::$currentProcess[$index])) {
                            continue;
                        }

                        self::$currentProcess[$index] = true;

                        $bean = BeanFactory::newBean($module);
                        if ($bean) {
                            $data['modules'][$module] = $this->getModuleData($module, $context);
                            $this->relateFields($data, $module, $bean);
                            unset($bean);

                            if (!$write) {
                                $write = true;
                            }
                        }

                        unset(self::$currentProcess[$index]);
                    }
                }
            }

            // Now cache the new data if there is a need
            if ($write) {
                $data = $this->normalizeMetadata($data);
                $data['_hash'] = $this->hashChunk($data);
                $this->putMetadataCache($data, $context);
            }
        }
    }

    /**
     * Rebuilds a section or sections of the metadata cache provided the cache
     * already exists.
     *
     * @param string|array $section
     * @param MetaDataContextInterface $context Metadata context
     */
    protected function rebuildSectionCache($section, MetaDataContextInterface $context)
    {
        // Only write if the section or module(s) were actually found and gettable
        $write = false;

        // If there is no section passed then do nothing
        if (!empty($section)) {
            // We will always need the metadata for this process, but only if there
            // is existing metadata to work (why build a section of an empty set)
            $data = $this->getMetadataCache(true, $context);

            if (!empty($data)) {
                // Handle the section(s)
                foreach ((array)$section as $index) {
                    if (isset($this->sectionMap[$index]) && $this->isValidSection($index)) {
                        if (isset(self::$currentProcess[$index])) {
                            continue;
                        }

                        self::$currentProcess[$index] = true;
                        $data = $this->loadSectionMetadata($index, $data, $context);
                        unset(self::$currentProcess[$index]);

                        if (!$write) {
                            $write = true;
                        }
                    }
                }
            }

            // Now cache the new data if there is a need
            if ($write) {
                $data = $this->normalizeMetadata($data);
                $data['_hash'] = $this->hashChunk($data);
                $this->putMetadataCache($data, $context);
            }
        }
    }

    /**
     * Rebuilds the cache for this platform and visibility
     *
     * @param bool $force Indicator that tells this method whether to force a build
     * @param MetaDataContextInterface|null $context Metadata context
     */
    public function rebuildCache($force = false, MetaDataContextInterface $context = null)
    {
        if (!$context) {
            $context = $this->getDefaultContext();
        }

        static::logDetails($this->logger, 'rebuildCache ', $context);
        // Delete our current supply of caches if there are any
        $deleted = $this->deletePlatformVisibilityCaches($context);

        // Rebuild the cache if there was a deleted cache or if we are forced to
        if (($force || $deleted) && static::$isCacheEnabled) {
            // Clear the module client cache first
            MetaDataFiles::clearModuleClientCache([], '', [$this->platforms[0]]);
            $this->getMetadataInternal([], $context, true);
        }
    }

    /**
     * Invalidate the cache for a given context/platform without rebuilding. Useful when multiple caches change and we
     * don't have the resources to rebuild them all within this call.
     *
     * TODO: Its usage should be replaced by a queue mechanism to rebuild the caches outside of the request scope.
     *
     * @param array $platforms
     * @param MetaDataContextInterface $contexts
     */
    public function invalidateCache($platforms = [], MetaDataContextInterface $context = null)
    {
        if (!$context) {
            $context = $this->getDefaultContext();
        }
        if (empty($platforms)) {
            $platforms = static::getPlatformsWithCaches();
        }

        static::logDetails($this->logger, 'Invalidating cache. Platforms with caches ' . print_r($platforms, true), $context);

        $deleted = $this->deletePlatformVisibilityCaches($context);
        if ($deleted) {
            foreach ($platforms as $platform) {
                MetaDataFiles::clearModuleClientCache([], '', [$platform]);
            }
        }
    }

    /**
     * Invalidates a cache for the user context. Used by the User object when
     * changing preferences since some of those preferences and settings need to
     * be reflected in the metadata for the user context.
     *
     * @param User $user User bean
     */
    public function invalidateUserCache(User $user)
    {
        $context = new MetaDataContextUser($user);
        $platforms = static::getPlatformsWithCaches();
        $this->invalidateCache($platforms, $context);
    }

    /**
     * Rewrites caches for all metadata manager platforms and visibility
     *
     * @param array $platforms
     * @param bool $force Indicator that tells this method whether to force a build
     */
    public static function refreshCache($platforms = [], $force = false)
    {
        // If we are in queue state (like in RepairAndRebuild), hold on to this
        // request until we are told to run it
        if (static::$isQueued) {
            static::$fullRefresh = ['platforms' => $platforms];
            return;
        }

        // Set our inProcess flag;
        static::$inProcess = true;

        static::logDetails(LoggerFactory::getLogger('metadata'), 'refreshCache ');

        // The basics are, for each platform, rewrite the cache for public and private
        if (empty($platforms)) {
            $platforms = static::getPlatformsWithCaches();
        }

        // Make sure the LanguageManager created modules cache is clear
        LanguageManager::resetCreatedModules();

        //No need to actually build the cache if we can't store it.
        if (static::$isCacheEnabled) {
            foreach ((array)$platforms as $platform) {
                foreach ([true, false] as $public) {
                    $mm = static::getManager($platform, $public, true);
                    $contexts = static::getAllMetadataContexts($public);
                    foreach ($contexts as $context) {
                        if ($context instanceof MetaDataContextDefault) {
                            $mm->rebuildCache(true);
                        } else {
                            $mm->invalidateCache($platforms, $context);
                        }
                    }
                }
            }
        }

        // Reset the in process flag
        static::$inProcess = false;
    }

    /**
     * Refreshes the cache for a section or collection of sections
     *
     * @param string $section
     * @param array $platforms
     * @param array $params Additional metadata parameters
     */
    public static function refreshSectionCache($section, $platforms = [], $params = [])
    {
        self::refreshCachePart('section', $section, $platforms, $params);
    }

    /**
     * Refreshes the cache for a module or collection of modules.
     *
     * @param array $modules
     * @param array $platforms
     * @param array $params Additional metadata parameters
     */
    public static function refreshModulesCache($modules, $platforms = [], $params = [])
    {
        self::refreshCachePart('modules', $modules, $platforms, $params);
    }

    /**
     * Refreshes the language cache files for the metadata for a collection of
     * languages. This will primarily be used by studio to change lang strings.
     *
     * @param array $languages Array of languages to refresh the caches of
     * @param array $platforms List of platforms for this request
     * @param array $params Additional metadata parameters
     */
    public static function refreshLanguagesCache($languages, $platforms = [], $params = [])
    {
        self::refreshCachePart('languages', $languages, $platforms, $params);
    }

    /**
     * Refreshes a single part of the cache provided there is a compatible rebuild*
     * method to do so. A part of the cache can be modules, sections or languages
     * since these all have their own caches that need to be dealt with.
     *
     * @param string $part which part of the cache to build
     * @param array $items List of items to be passed to the rebuild method
     * @param array $platforms List of platforms to carry out the refresh for
     * @param array $params Additional metadata parameters
     * @return null
     */
    protected static function refreshCachePart($part, $items = [], $platforms = [], $params = [])
    {
        // No args, no worries
        if (empty($items)) {
            return;
        }

        // If we are in the middle of a refresh do nothing
        if (self::$inProcess) {
            return;
        }

        // If we are in queue state (like in RepairAndRebuild), hold on to this
        // request until we are told to run it
        if (self::$isQueued) {
            self::buildCacheRefreshQueueSection($part, $items, array_merge($params, [
                'platforms' => $platforms,
            ]));
            return;
        }

        if (empty($platforms)) {
            // Only get platforms with existing caches so we don't build everything
            // if we don't need to
            $platforms = self::getPlatformsWithCaches();
        }

        // Make sure the LanguageManager created modules cache is clear
        LanguageManager::resetCreatedModules();

        //No need to build the cache if we can't store it
        if (static::$isCacheEnabled) {
            // Handle refreshing based on the cache part
            $method = 'rebuild' . ucfirst(strtolower($part)) . 'Cache';
            foreach ((array)$platforms as $platform) {
                foreach ([true, false] as $public) {
                    $mm = MetaDataManager::getManager($platform, $public, true);
                    if (method_exists($mm, $method)) {
                        $sections = ($part == 'section') ? (is_array($items) ? $items : [$items]) : [$part];
                        $contextSections = array_intersect($sections, $mm->getContextAwareSections());
                        $baseOnly = empty($contextSections);

                        if ($baseOnly) {
                            $contexts = [$mm->getDefaultContext()];
                        } else {
                            $contexts = static::getMetadataContexts($public, $params);
                        }
                        //Always build the base partial if we are rebuilding any non-context aware section
                        if (!$public && sizeof($sections) !== sizeof($contextSections)) {
                            $contexts[] = new MetaDataContextPartial();
                        }

                        // When a change occurs in the base metadata which is filtered/modified by contexts,
                        // we need to clear the cache for all contexts
                        // except the ones we are going to rebuild in this request
                        if (empty($params) && !$baseOnly) {
                            $allContexts = array_filter(
                                static::getAllMetadataContexts($public),
                                function ($context) use ($contexts) {
                                    foreach ($contexts as $current_context) {
                                        if ($current_context->getHash() === $context->getHash()) {
                                            return false;
                                        }
                                    }
                                    return true;
                                }
                            );
                            foreach ($allContexts as $context) {
                                $mm->deletePlatformVisibilityCaches($context);
                            }
                        }

                        foreach ($contexts as $context) {
                            $mm->$method($items, $context);
                        }
                    }
                }
            }
        }
    }

    /**
     * Builds up a section of the refreshCacheQueue based on name.
     *
     * @param string $name Name of the queue section
     * @param array $items The list of modules or sections
     * @param array $params Additional metadata parameters
     */
    protected static function buildCacheRefreshQueueSection($name, $items, $params)
    {
        if (!self::$queue) {
            self::$queue = new RefreshQueue();
        }

        if (!is_array($items)) {
            $items = [$items];
        }

        self::$queue->enqueue($name, $items, $params);
    }

    /**
     * Runs all of the cache refreshers in the queue. If $disable is false, will
     * leave the queue state as is. By default, will turn the queue off when it
     * completes.
     *
     * @param bool $disable
     */
    public static function runCacheRefreshQueue($disable = true)
    {
        // Only run the runner if the explicit flag allowing it is true
        if (self::$runQueueOnCall) {
            // Hold on to the queue state until later when we need it
            $queueState = self::$isQueued;

            // Temporarily turn off queueing to allow this to happen
            self::$isQueued = false;

            // If full is set, run all cache clears and be done
            if (!empty(self::$fullRefresh)) {
                // Handle the refreshing of the cache and emptying of the queue
                self::refreshCache(self::$fullRefresh['platforms']);
                if (self::$queue) {
                    self::$queue->clear(self::$fullRefresh['platforms']);
                }
            }

            if (self::$queue) {
                while ($task = self::$queue->dequeue()) {
                    [$name, $items, $params] = $task;
                    if (isset(self::$cacheParts[$name])) {
                        $method = self::$cacheParts[$name];
                        if (isset($params['platforms'])) {
                            $platforms = $params['platforms'];
                            unset($params['platforms']);
                        } else {
                            $platforms = [];
                        }
                        self::$method($items, $platforms, $params);
                    }
                }
            }

            // Handle queue state
            if ($disable) {
                self::$isQueued = false;
            } else {
                self::$isQueued = $queueState;
            }
        }
    }

    /**
     * Turns off the queue runner. Setting this to false will prevent the queue
     * from running even if the run method is called.
     */
    public static function setRunQueueOnCallOff()
    {
        self::setRunQueueOnCall(false);
    }

    /**
     * Turns on the queue runner. This is the default state of the runner.
     */
    public static function setRunQueueOnCallOn()
    {
        self::setRunQueueOnCall(true);
    }

    /**
     * Sets the queue runner flag to boolean true or false
     *
     * @param boolean $value Flag that tells the queue runner to run or not
     */
    public static function setRunQueueOnCall($value)
    {
        self::$runQueueOnCall = (bool)$value;
    }

    /**
     * Turns on the cache refresh queue
     */
    public static function enableCacheRefreshQueue()
    {
        self::$isQueued = true;
    }

    /**
     * Turns off the cache refresh queue and runs any of the rebuild processes
     * currently in the queue
     */
    public static function disableCacheRefreshQueue()
    {
        self::$isQueued = false;
        self::runCacheRefreshQueue();
    }

    /**
     * Simply runs the queue and resets the queue to empty leaving queue state in
     * tact
     */
    public static function flushCacheRefreshQueue()
    {
        self::runCacheRefreshQueue(false);
    }

    /**
     * Gets a value for an instance version variable
     * @param string $var The string name of the variable to get a value for
     * @return mixed
     */
    protected function getInstanceVersionValue($var)
    {
        if (isset($GLOBALS[$var])) {
            return $GLOBALS[$var];
        }

        include 'sugar_version.php';
        if (isset(${$var})) {
            return ${$var};
        }

        return null;
    }

    /**
     * Gets server information
     *
     * @return array of ServerInfo
     */
    public function getServerInfo()
    {
        $custom_version = null;
        $data = [];
        $system_config = Administration::getSettings(false, true);
        $data['flavor'] = $this->getInstanceVersionValue('sugar_flavor');
        $data['version'] = $this->getInstanceVersionValue('sugar_version');
        $data['build'] = $this->getInstanceVersionValue('sugar_build');
        $data['marketing_version'] = $this->getInstanceVersionValue('sugar_mar_version');

        $data['product_name'] = 'SugarCRM';

        // Include all available licenses for the instance
        $sm = SubscriptionManager::instance();
        $data['licenses'] = $sm->getAllImpliedSubscriptions(array_keys($sm->getAllSystemSubscriptionKeys()));

        if (file_exists('custom/version.php')) {
            include 'custom/version.php';
            $data['custom_version'] = $custom_version;
        }

        if (isset($system_config->settings['system_skypeout_on']) && $system_config->settings['system_skypeout_on'] == 1) {
            $data['system_skypeout_on'] = true;
        }

        if (isset($system_config->settings['system_tweettocase_on']) && $system_config->settings['system_tweettocase_on'] == 1) {
            $data['system_tweettocase_on'] = true;
        }

        $fts_enabled = SugarSearchEngineFactory::getFTSEngineNameFromConfig();
        if (!empty($fts_enabled) && $fts_enabled != 'SugarSearchEngine') {
            $data['fts'] = [
                'enabled' => true,
                'type' => $fts_enabled,
            ];
        } else {
            $data['fts'] = [
                'enabled' => false,
            ];
        }

        //Adds the portal status to the server info collection.
        //Property 'on' of category 'portal' must be a boolean.
        $data['portal_active'] = !empty($system_config->settings['portal_on']);

        // For use in analytics, we want to make sure these parameters actually have a value so use a full ternary here
        $sugarConfig = $this->getSugarConfig();
        $data['host_environment'] = empty($sugarConfig['host_environment']) ? 'on-premise' : $sugarConfig['host_environment'];
        $data['host_designation'] = empty($sugarConfig['host_designation']) ? 'production' : $sugarConfig['host_designation'];

        // Add in analytic data
        return $system_config->getUpdatedAnalyticServerInfo($data, $system_config->settings);
    }

    /**
     * get Idp config
     *
     * @return Authentication\Config
     */
    protected function getIdpConfig()
    {
        if (empty($this->idpConfig)) {
            $this->idpConfig = new Authentication\Config(\SugarConfig::getInstance());
        }

        return $this->idpConfig;
    }

    /**
     * Gets configs
     *
     * @return array
     */
    protected function getConfigs()
    {
        $sugarConfig = $this->getSugarConfig();
        $administration = new Administration();
        $administration->retrieveSettings(false, true);

        $idpConfig = $this->getIdpConfig();
        $properties = $this->getConfigProperties();
        $properties = $this->parseConfigProperties($sugarConfig, $properties);
        $configs = $this->handleConfigPropertiesExceptions($properties);

        // FIXME: Clean up properties below in order to fit standards
        // regarding property names
        if (isset($administration->settings['honeypot_on'])) {
            $configs['honeypot_on'] = true;
        }
        if (isset($sugarConfig['passwordsetting']['forgotpasswordON'])) {
            if ($sugarConfig['passwordsetting']['forgotpasswordON'] === '1' || $sugarConfig['passwordsetting']['forgotpasswordON'] === true) {
                $configs['forgotpasswordON'] = true;
            } else {
                $configs['forgotpasswordON'] = false;
            }
        }

        $auth = AuthenticationController::getInstance($sugarConfig['authenticationClass'] ?? null, $idpConfig);

        if ($auth->isExternal()) {
            $configs['externalLogin'] = true;
            $configs['externalLoginSameWindow'] = SugarConfig::getInstance()->get('SAML_SAME_WINDOW');
        }

        if (isset($sugarConfig['analytics'])) {
            $configs['analytics'] = $sugarConfig['analytics'];
        } else {
            $configs['analytics'] = ['enabled' => false];
        }

        if (isset($sugarConfig['passwordsetting'])) {
            $configs['passwordsetting'] = $sugarConfig['passwordsetting'];
        }

        $caseBean = BeanFactory::newBean('Cases');
        if (!empty($caseBean)) {
            $configs['inboundEmailCaseSubjectMacro'] = $caseBean->getEmailSubjectMacro();
        }

        // System name setting for sidecar modules
        if (!empty($administration->settings['system_name'])) {
            $configs['systemName'] = $administration->settings['system_name'];
        }

        if (!empty($sugarConfig['unique_key'])) {
            $configs['uniqueKey'] = $sugarConfig['unique_key'];
        }

        // Maps settings for sidecar modules
        $mapsSettings = [];

        if (!empty($administration->settings['maps_modulesData'])) {
            $mapsSettings['modulesData'] = $administration->settings['maps_modulesData'];
        }

        if (!empty($administration->settings['maps_unitType'])) {
            $mapsSettings['unitType'] = $administration->settings['maps_unitType'];
        }

        if (!empty($administration->settings['maps_logLevel'])) {
            $mapsSettings['logLevel'] = $administration->settings['maps_logLevel'];
        }

        if (!empty($administration->settings['maps_enabled_modules'])) {
            $mapsSettings['enabled_modules'] = $administration->settings['maps_enabled_modules'];
        }

        $configs['maps'] = $mapsSettings;

        // configs for reports thresholds
        $configs['reports_complexity_display'] = $sugarConfig['reports_complexity_display'] ?? [];

        //DocuSign global settings
        $configs['docusign'] = DocuSignUtils::getDocusignGlobalConfigs();

        // Handle connectors
        $connectors = ConnectorUtils::getConnectors();
        $configs['connectors'] = $this->getFilteredConnectorList($connectors);

        if (isset($sugarConfig['sugar_min_int']) && is_numeric($sugarConfig['sugar_min_int'])) {
            $configs['sugarMinInt'] = $sugarConfig['sugar_min_int'];
        }
        if (isset($sugarConfig['sugar_max_int']) && is_numeric($sugarConfig['sugar_max_int'])) {
            $configs['sugarMaxInt'] = $sugarConfig['sugar_max_int'];
        }

        // IDM mode
        $configs['idmModeEnabled'] = $idpConfig->isIDMModeEnabled();
        if ($configs['idmModeEnabled']) {
            $configs['externalLoginSameWindow'] = true;
            $idmModeConfig = $idpConfig->getIDMModeConfig();
            $configs['cloudConsoleForgotPasswordUrl'] = $idpConfig->buildCloudConsoleUrl(
                'forgotPassword',
                [$idmModeConfig['tid']]
            );
            $configs['stsUrl'] = $idmModeConfig['stsUrl'];
            $configs['tenant'] = $idmModeConfig['tid'];
            // Enables catalog and assigns catalog url when in IDM mode
            if (isset($sugarConfig['catalog_enabled']) &&
                $sugarConfig['catalog_enabled'] === false &&
                !empty($idmModeConfig['tid'])) {
                $idpConfig->toggleCatalog(true);
                $sugarConfig = $this->getSugarConfig();
                if ($sugarConfig['catalog_enabled'] &&
                    !empty($sugarConfig['catalog_url'])) {
                    $configs['catalog_enabled'] = $sugarConfig['catalog_enabled'];
                    $configs['catalog_url'] = $sugarConfig['catalog_url'];
                }
            }
        }

        // SugarBPM settings
        $configs['autoValidateProcessesOnImport'] = $sugarConfig['processes_auto_validate_on_import'] ?? true;
        $configs['autoValidateProcessesOnAutosave'] = $sugarConfig['processes_auto_validate_on_autosave'] ?? true;
        $configs['processDesignerAutosaveInterval'] = $sugarConfig['processes_auto_save_interval'] ?? 30000;

        //Allowed Link Schemes Setting
        $configs['allowedLinkSchemes'] = $sugarConfig['allowed_link_schemes'] ?? ['http', 'https'];

        // Sidebar navigation settings
        $configs['maxPinnedModules'] = $sugarConfig['maxPinnedModules'] ?? $sugarConfig['default_max_pinned_modules'];

        // Get AWS configs for Serve and Sell
        if ($administration->isLicensedForServe() || $administration->isLicensedForSell()) {
            foreach ($administration->settings as $key => $value) {
                if (substr($key, 0, 4) === 'aws_') {
                    // Format the key for these configs correctly
                    $configs[$this->translateConfigProperty($key)] = $value;
                }
            }
        }
        $configs['csp'] = ContentSecurityPolicy::fromAdministrationSettings()
            ->withAddedDefaults()
            ->asString();

        $configs['versionMark'] = getVersionedPath('');
        $configs['customer_journey'] = $this->getCustomerJourneyConfig($sugarConfig);

        foreach ($administration->settings as $key => $value) {
            if (substr($key, 0, 9) === 'timeline_') {
                $configs['timeline'][substr($key, 9)] = $value;
            }
        }
        $modules = $this->getTabList();
        foreach ($modules as $module) {
            if (!isset($configs['timeline'][$module])) {
                $bean = BeanFactory::getBean($module);
                if ($bean) {
                    $configs['timeline'][$module]['enabledModules'] =
                        array_keys($this->getDefaultTimelineModules($bean));
                }
            }
        }
        return $configs;
    }

    /**
     * Get default timeline modules enabled for a module
     * @param SugarBean $bean
     * @return array
     */
    public function getDefaultTimelineModules(SugarBean $bean): array
    {
        $modules = [];
        $defaultModuleList = [
            'Meetings',
            'Calls',
            'Notes',
            'Emails',
            'Messages',
            'Tasks',
            // Market Modules
            'sf_webActivity',
            'sf_Dialogs',
            'sf_EventManagement',
        ];
        $module = $bean->getModuleName();
        $subpanelDefs = $this->getSubpanelMetadata($module);
        if (!empty($subpanelDefs[$module]['base']['layout']['subpanels'])) {
            foreach ($subpanelDefs[$module]['base']['layout']['subpanels']['components'] as $subpanel) {
                if (!empty($subpanel['context']['link'])) {
                    $link = $subpanel['context']['link'];
                    if ($bean->load_relationship($link)) {
                        $relatedModule = $bean->$link->getRelatedModuleName();
                        if ($relatedModule && in_array($relatedModule, $defaultModuleList)) {
                            $modules[$link] = $relatedModule;
                        }
                    }
                }
            }
        }
        return $modules;
    }

    /**
     * Retrieve the subpanel metadata for a given module.
     * @param string $module
     * @return array The subpanel metadata
     */
    public function getSubpanelMetadata(string $module): array
    {
        $viewdefs = [];
        $moduleDir = BeanFactory::getModuleDir($module);
        if ($moduleDir) {
            $viewdefs = [];
            // ootb
            foreach (SugarAutoLoader::existingCustom('modules/' . $moduleDir . '/clients/base/layouts/subpanels/subpanels.php') as $file) {
                require FileLoader::validateFilePath($file);
            }
            // customizations
            $ext = 'custom/modules/' . $moduleDir . '/Ext/clients/base/layouts/subpanels/subpanels.ext.php';
            if (file_exists($ext)) {
                require FileLoader::validateFilePath($ext);
            }
        }
        return $viewdefs;
    }

    /**
     * Gets the current connector list, filtered for consumption by the client
     * and normalized.
     *
     * @param array $connectors The current connector list
     * @return array
     */
    protected function getFilteredConnectorList($connectors)
    {
        // Declare the return
        $return = [];

        // Loop over the connectors, cleaning up the name and parsing the known
        // properties that the client needs
        foreach ($connectors as $id => $connector) {
            // The client doesn't need to know ext_eapm_googleapis, and besides,
            // it's in the name property anyway
            preg_match_all('#ext_(.*)_(.*)#', $id, $m, PREG_SET_ORDER);
            if (isset($m[0][2])) {
                $clientName = $m[0][2];

                // Loop the required client properties and set from that
                foreach ($this->connectorProperties as $prop) {
                    if (isset($connector[$prop])) {
                        $return[$clientName][$prop] = $connector[$prop];
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Retrieve server side configurations.
     *
     * @return array Server side configurations.
     */
    protected function getSugarConfig()
    {
        global $sugar_config;
        return $sugar_config;
    }

    /**
     * Retrieve white listed properties which shall be copied from server side
     * configurations to client side configurations.
     *
     * @return array Configuration properties.
     */
    protected function getConfigProperties()
    {
        return static::$configProperties;
    }

    /**
     * Retrieve map of configuration properties that should assume a different
     * name than the one provided by parse mechanism.
     *
     * @return array Configuration properties.
     * @deprecated
     *
     */
    protected function getConfigPropertiesExceptions()
    {
        return static::$configPropertiesExceptions;
    }

    /**
     * Parse supplied configurations.
     *
     * All $configProperties are translated to 'camelCase' and included on
     * client side configurations if exist on $config.
     *
     * @param array $config Server side configurations.
     * @param array $configProperties White listed properties which shall be
     *   copied from server side.
     *
     * @return array Array of client side configuration properties.
     */
    protected function parseConfigProperties(array $config, array $configProperties)
    {
        $configs = [];
        foreach ($configProperties as $key => $value) {
            if (!isset($config[$key])) {
                continue;
            }

            $translatedKey = $this->translateConfigProperty($key);

            if (is_array($value)) {
                $configs[$translatedKey] = $this->parseConfigProperties(
                    $config[$key],
                    $value
                );
            } elseif ($value === true) {
                $configs[$translatedKey] = $config[$key];
            }
        }
        return $configs;
    }

    /**
     * Translate supplied $property from an 'underscore' version to a
     * 'camelCase' version.
     *
     * @param string $property Configuration property name.
     *
     * @return string Translated property name.
     */
    protected function translateConfigProperty($property)
    {
        return lcfirst(
            preg_replace_callback(
                '/(^|_)([a-z])/',
                function ($match) {
                    return strtoupper($match[2]);
                },
                $property
            )
        );
    }

    /**
     * Handle configuration properties that should assume a different name than
     * the one provided by parse mechanism.
     *
     * @param array $configs Client side configuration properties.
     *
     * @return array Array of client side configuration properties
     * @deprecated This should only be used to handle legacy code, thus should
     * removed when that code gets cleaned up.
     *
     */
    protected function handleConfigPropertiesExceptions(array $configs)
    {
        $exceptions = $this->getConfigPropertiesExceptions();
        foreach ($exceptions as $key => $value) {
            if (!isset($configs[$key])) {
                continue;
            }

            $configs[$value] = $configs[$key];
            unset($configs[$key]);
        }
        return $configs;
    }

    /**
     * Checks the validity of the current session metadata hash value. Since the
     * only time the session value is set is after a metadata fetch has been made
     * a non-existent session value is valid. However if there is a session value
     * then there either has to be a metadata cache of hashes to check against
     * or the session value has to be false (meaning the session value was set
     * before the metadata cache was built) in order to pass the validity check.
     *
     * @param string $hash Metadata hash to validate against the cache.
     *
     * @return boolean
     */
    public function isMetadataHashValid($hash)
    {
        // Is there a current metadata hash sent in the request (empty string is not a valid hash)
        if (!empty($hash)) {
            // See if there is a hash cache. If there is, see if the hash cache
            // for this platform matches what's in the session, ensuring that the
            // session value isn't false (the default value when setting from
            // cache)
            $platformHash = $this->getMetadataHash();

            if ($platformHash === false) {
                //If the cache file doesn't exist, we have no way to know if the current hash is correct
                //and most likely the cache file was nuked due to a metadata change so the client
                //needs to hit the metadata api anyhow.
                return false;
            } else {
                return $platformHash == $hash;
            }
        }

        // There is no session var so we say we're good so as not to get stuck in
        // a continual logout loop
        return true;
    }

    /**
     * Tells the app the user preference metadata has changed.
     *
     * For now this will be done by simply changing the date_modified on the User
     * record and using that as the metadata hash value. This could change in the
     * future.
     *
     * @param Person $user The user that is changing preferences
     */
    public function setUserMetadataHasChanged($user)
    {
        $user->update_date_modified = true;
        $user->save();
    }

    /**
     * Checks the state of changed metadata for a user
     *
     * @param Person $user The user that is changing preferences
     * @param string $hash The user preference data hash to compare
     *
     * @return bool
     */
    public function hasUserMetadataChanged($user, $hash)
    {
        return $user->getUserMDHash() != $hash;
    }

    /**
     * Gets all metadata for the current platform and visibility
     *
     * NOTE ON $buildCache - In most cases this will be true. But in edge cases,
     * like installation when there isn't a database yet, this has to be false
     * since we can't try get module information without the ability to get to
     * the database.
     *
     * @param array $args Arguments passed into the request for metadata
     * @param MetaDataContextInterface|null $context Metadata context
     * @return mixed
     */
    public function getMetadata($args = [], MetaDataContextInterface $context = null)
    {
        // disable admin work, metadata is always access controlled
        $adminWork = new AdminWork();
        $adminWork->reset(false);
        $data = $this->getMetadataInternal($args, $context);
        //update the hash before returning to ensure the base and context hashes are incorperated.
        //Internally this hash is not stored with a context cache.
        $data['_hash'] = $this->getMetadataHash(false, $context);

        // Further filter metadata based on licensing
        $data = $this->applyLicensesFilter($data, true);

        return $data;
    }

    /**
     * Recursively parse metadata and filter out the parts
     * that the current user does not have a valid license for
     *
     * @param array $data
     * @param bool $ignoreLicenseCheck
     *
     * @return mixed
     */
    public function applyLicensesFilter(array $data, bool $ignoreLicenseCheck = false)
    {
        global $current_user;

        // as long we do not have a logged in user all the metadata is returned untouched
        if (empty($current_user) || empty($current_user->id)) {
            return $data;
        }

        if (array_key_exists('licenseFilter', $data) && !$ignoreLicenseCheck) {
            $requiredLicenses = $data['licenseFilter'];

            if (is_string($requiredLicenses)) {
                $requiredLicenses = [$requiredLicenses];
            }

            // if the current user does not have all the required licenses we hide this part of metadata
            if (is_array($requiredLicenses) && !$current_user->hasLicenses($requiredLicenses)) {
                return false;
            }
        }

        if (array_key_exists('licenseDependency', $data) && !$ignoreLicenseCheck) {
            $licenseDependency = is_array($data['licenseDependency']) ? $data['licenseDependency'] : [];

            // some parts of meta could have different values taking into account user licenses
            foreach ($licenseDependency as $licenseType => $meta) {
                if (is_string($licenseType) && $current_user->hasLicenses([$licenseType], false)) {
                    foreach ($meta as $category => $value) {
                        $data[$category] = $value;
                    }
                }
            }
        }

        // go recursively into the metadata looking for license restricted chunks
        foreach ($data as $category => $meta) {
            if (!is_array($meta)) {
                continue;
            }

            $filteredMeta = $this->applyLicensesFilter($meta);

            // as long as the current user has all the required licenses we need to keep the filtered data
            if (is_array($filteredMeta)) {
                $data[$category] = $filteredMeta;
            } else {
                // otherwise just erase this part of metadata for the logged in user
                unset($data[$category]);
            }
        }

        return $data;
    }

    protected function getMetadataInternal($args, MetaDataContextInterface $context = null, $ignoreCache = false)
    {
        if (!$context) {
            $context = $this->getCurrentUserContext();
        }

        $defaultContext = $this->getDefaultContext();
        $partialContext = new MetaDataContextPartial();
        $isDefaultContext = $context->getHash() == $defaultContext->getHash();

        $intialContext = ($isDefaultContext || $this->public) ? $defaultContext : $partialContext;

        // to retrieve default data
        $defaultData = [];
        if ($intialContext->getHash() != $defaultContext->getHash()) {
            $defaultData = $this->loadAndCacheMetadata($args, $defaultContext, $ignoreCache);
        }
        // Start with the default or partial metadata
        $data = $this->loadAndCacheMetadata($args, $intialContext, $isDefaultContext && $ignoreCache);

        // merge partial and default together
        if (!empty($defaultData)) {
            $data = array_merge($defaultData, $data);
        }

        // Get our metadata if a users specific context was provided
        if (!$this->public && !($context instanceof MetaDataContextDefault)) {
            $contextData = $this->loadAndCacheMetadata(false, $context, $ignoreCache);
            $data = array_merge($data, $contextData);
        }

        return $data;
    }

    protected function loadAndCacheMetadata($args, MetaDataContextInterface $context, $ignoreCache = false)
    {
        if ($ignoreCache) {
            $data = [];
        } else {
            $data = $this->getMetadataCache(false, $context);
        }

        $oldHash = !empty($data['_hash']) ? $data['_hash'] : null;
        $metadataRebuildRequired = empty($data) || !$this->verifyJSSource($data, $context);

        if ($metadataRebuildRequired) {
            // Allow more time for private metadata builds since it is much heavier
            if (!$this->public) {
                ini_set('max_execution_time', 0);
            }
            $hash = (string)$context->getHash();
            $systemProcessLock = new SystemProcessLock(__METHOD__, $hash);

            // returned true here will start the rebuild process
            $cacheCheck = function () use (&$data, $context) {
                $data = $this->getMetadataCache(false, $context);
                return empty($data) || !$this->verifyJSSource($data, $context);
            };

            // the rebuild function
            $longRunningFunction = function () use (&$data, $args, $context, $oldHash) {
                $data = $this->loadMetadata($args, $context);

                if ($data['_hash'] != $oldHash) {
                    $this->putMetadataCache($data, $context);
                } elseif ($this->getCachedMetadataHash($context, false) != $data['_hash']) {
                    // ensure that the metadata hashes is up to date
                    $this->cacheMetadataHash($data['_hash'], $context);
                }
            };

            $refuseFunction = $longRunningFunction;

            // the following is designed to prevent process race conditions in a long running process
            $systemProcessLock->isolatedCall($cacheCheck, $longRunningFunction, $refuseFunction);

            return $data;
        }

        // Cache the data so long as the current cache is different from the data
        // hash
        if ($data['_hash'] != $oldHash) {
            $this->putMetadataCache($data, $context);
        } //Ensure that the metadata hashes is up to date
        elseif ($this->getCachedMetadataHash($context, false) != $data['_hash']) {
            $this->cacheMetadataHash($data['_hash'], $context);
        }

        return $data;
    }

    /**
     * Gets the metadata cache for a given platform and visibility
     *
     * @param boolean $ignoreDevMode If true, ignore developer mode and return cached metadata
     * @param MetaDataContextInterface $context Metadata context
     * @return array The metadata cache is it exists, null otherwise
     */
    protected function getMetadataCache($ignoreDevMode, MetaDataContextInterface $context)
    {
        if (inDeveloperMode() && !$ignoreDevMode) {
            return null;
        }

        if (!$context) {
            $context = $this->getCurrentUserContext();
        }

        return $this->getCache()->get($this->getCachedMetadataHashKey($context));
    }

    /**
     * @param (array) $data
     *
     * @return bool true if the js-component file for this metadata call exists, false otherwise
     */
    protected function verifyJSSource($data, MetaDataContextInterface $context = null)
    {
        if (!empty($data['jssource']) && !file_exists($data['jssource'])) {
            //The jssource file is invalid, we need to invalidate the hash as well.
            return false;
        }
        //It is possible for the public and private metadata caches to get otu of sync around the public
        //JsSource. When this occurs we have to invalidated the private metadata cache.
        if (!empty($data['jssource_public'])) {
            if (!$context) {
                $context = $this->getDefaultContext();
            }
            $publicJsSource = $this->getPublicJsSource($context);
            if ($data['jssource_public'] != $publicJsSource || !file_exists($publicJsSource)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Builds the current platform and visibility metadata and returns it
     *
     * @param array $args Arguments passed into the request for metadata
     * @param MetaDataContextInterface $context Metadata context
     * @return array
     */
    protected function loadMetadata($args, MetaDataContextInterface $context)
    {
        $this->args = $args;
        // Start collecting data
        $this->data = [];

        $defaultContext = $this->getDefaultContext();
        $sections = $this->sections;

        if ($context instanceof MetaDataContextPartial) {
            $sections = array_diff($this->sections, $this->getContextAwareSections());
        } elseif ($context->getHash() != $defaultContext->getHash()) {
            $sections = $this->getContextAwareSections();
        }

        foreach ($sections as $section) {
            // Overrides are handled at the end because they are "special"
            // full_module_list and module_info are handled by the modules section
            // handler and is only found in private metadata
            if ($this->sectionIsSkipped($section)) {
                continue;
            }

            $this->data = $this->loadSectionMetadata($section, $this->data, $context);
        }

        // Handle overrides
        $this->data['_override_values'] = $this->getOverrides($this->data, $args);

        // Handle client specific normalizations
        $this->data = $this->normalizeMetadata($this->data);

        // Handle hashing
        $this->data['_hash'] = $this->hashChunk($this->data);

        // Send it back
        return $this->data;
    }

    /**
     * Utility method shared between the metadata loader and section rebuilder
     *
     * @param string $section The section to build
     * @param array $data The metadata payload that is appended to
     * @param MetaDataContextInterface $context Metadata context
     * @return array Appended metadata
     */
    protected function loadSectionMetadata($section, $data, MetaDataContextInterface $context)
    {
        // Adopt the same logic as the section rebuilder
        if (isset($this->sectionMap[$section])) {
            if ($this->sectionMap[$section] === false) {
                $method = 'rebuild' . ucfirst($section) . 'Section';
                $data = $this->$method($data, $context);
            } else {
                $method = $this->sectionMap[$section];
                $data[$section] = $this->$method($data, $context);
            }
        }

        return $data;
    }

    /**
     * Gets the system logo url
     * @return string
     */
    public function getLogoUrl()
    {
        return SugarThemeRegistry::current()->getImageURL('company_logo.png', true, true);
    }

    /**
     * Gets the system dark mode logo url
     * @return string
     */
    public function getLogoUrlDark()
    {
        return SugarThemeRegistry::current()->getImageURL('company_logo_dark.png', true, true);
    }

    /**
     * Gets the list of hidden subpanels
     *
     * @return array
     */
    public function getHiddenSubpanels()
    {
        // BR-29 Handle hidden subpanels - SubPanelDefinitons needs a bean at
        // construct time, so hand it an admin bean. This returns a list of
        // hidden subpanels in lowercase module name form:
        // array('accounts', 'bugs', 'contacts');
        $spd = new SubPanelDefinitions(BeanFactory::newBean('Administration'));
        return array_values($spd->get_hidden_subpanels());
    }

    /**
     * Builds the javascript file used by the clients
     *
     * @param array $data The metadata to build from
     * @param boolean $onlyReturnModuleComponents Indicator to return only module
     *                                            components
     * @return string A url to the file that was just built
     */
    protected function buildJavascriptComponentFile(&$data, $onlyReturnModuleComponents = false)
    {
        $platform = $this->platforms[0];

        $js = "(function(app) {\n SUGAR.jssource = {";

        $routesJs = '';

        $compJS = $this->buildJavascriptComponentSection($data);
        if (!$onlyReturnModuleComponents) {
            $js .= $compJS;
        }

        if (!empty($data['modules'])) {
            if (!empty($compJS) && !$onlyReturnModuleComponents) {
                $js .= ',';
            }

            $js .= "\n\t\"modules\":{";

            $allModuleJS = '';
            //Grab the keys this way rather than through $key => $value to preserve pass by reference for $data
            $modules = array_keys($data['modules']);
            foreach ($modules as $module) {
                $moduleJS = $this->buildJavascriptComponentSection($data['modules'][$module], true);
                if (!empty($moduleJS)) {
                    $allModuleJS .= ",\n\t\t\"$module\":{{$moduleJS}}";
                }
                $routesJs .= MetaDataFiles::loadRouterFile($module, $platform);
            }
            //Chop off the first comma in $allModuleJS
            $js .= substr($allModuleJS, 1);
            $js .= "\n\t}";
        }

        $js .= "}})(SUGAR.App);\n";
        $js .= $routesJs;
        $hash = md5($js);
        //If we are going to be using uglify to minify our JS, we should minify the entire file rather than each component separately.
        if (shouldResourcesBeMinified() && SugarMin::isMinifyFast()) {
            $js = SugarMin::minify($js);
        }
        $path = "cache/javascript/$platform/components_$hash.js";
        if (!file_exists($path)) {
            mkdir_recursive(dirname($path));
            sugar_file_put_contents_atomic($path, $js);
        }

        return $this->getUrlForCacheFile($path);
    }

    /**
     * Builds component javascript
     *
     * @param array $data The metadata to build from
     * @param boolean $isModule Module specific indicator
     * @return string A javascript string
     */
    protected function buildJavascriptComponentSection(&$data, $isModule = false)
    {
        $js = '';
        $platforms = array_reverse($this->platforms);

        $typeData = [];

        if ($isModule) {
            $types = ['fieldTemplates', 'views', 'layouts', 'datas'];
        } else {
            $types = ['fields', 'views', 'layouts', 'datas'];
        }

        foreach ($types as $mdType) {
            if (!empty($data[$mdType])) {
                $platControllers = [];

                foreach ($data[$mdType] as $name => $component) {
                    if (!is_array($component) || !isset($component['controller'])) {
                        continue;
                    }
                    $controllers = $component['controller'];

                    if (is_array($controllers)) {
                        foreach ($platforms as $platform) {
                            if (!isset($controllers[$platform])) {
                                continue;
                            }
                            $controller = $controllers[$platform];
                            // remove additional symbols in end of js content - it will be included in content
                            $controller = trim(trim($controller), ',;');
                            $controller = $this->insertHeaderComment($controller, $mdType, $name, $platform);

                            if (!isset($platControllers[$platform])) {
                                $platControllers[$platform] = [];
                            }
                            $platControllers[$platform][] = "\"$name\": {\"controller\": " . $controller . ' }';
                        }
                    }
                    unset($data[$mdType][$name]['controller']);
                    //Remove any entries that were only a controller
                    if (empty($data[$mdType][$name])) {
                        unset($data[$mdType][$name]);
                    }
                }

                // We should have all of the controllers for this type, split up by platform
                $thisTypeStr = "\"$mdType\": {\n";

                foreach ($platforms as $platform) {
                    if (isset($platControllers[$platform])) {
                        $thisTypeStr .= "\"$platform\": {\n" . implode(",\n", $platControllers[$platform]) . "\n},\n";
                    }
                }

                $thisTypeStr = trim($thisTypeStr, "\n,") . "}\n";
                $typeData[] = $thisTypeStr;
            }
        }

        $js = implode(",\n", $typeData) . "\n";

        return $js;
    }

    /**
     * Helper to insert header comments for controllers
     *
     * @param string $controller The controller this is being done for
     * @param string $mdType The type of metadata
     * @param string $name The name
     * @param string $platform The platform for this string
     * @return string
     */
    protected function insertHeaderComment($controller, $mdType, $name, $platform)
    {
        $singularType = substr($mdType, 0, -1);
        $needle = '({';
        $headerComment = "\n\t// " . ucfirst($name) . ' ' . ucfirst($singularType) . " ($platform) \n";

        // Find position "after" needle
        $pos = (strpos($controller, $needle) + strlen($needle));

        // Insert our comment and return ammended controller
        return substr($controller, 0, $pos) . $headerComment . substr($controller, $pos);
    }

    /**
     * Gets all enabled and disabled languages. Wraps the util function to allow
     * for manipulation of the return in the future.
     *
     * @return array Array of enabled and disabled languages
     */
    public function getAllLanguages()
    {
        $languages = LanguageManager::getEnabledAndDisabledLanguages();

        return [
            'enabled' => $this->getLanguageKeys($languages['enabled']),
            'disabled' => $this->getLanguageKeys($languages['disabled']),
        ];
    }

    /**
     * Gets language keys only. Used by the API in conjunction with language indexes
     * from app_list_strings.
     *
     * @param array $language An enabled or disabled language array
     * @return array
     */
    protected function getLanguageKeys($language)
    {
        $return = [];
        foreach ($language as $lang) {
            $return[] = $lang['module'];
        }
        return $return;
    }

    /**
     * Sets the flag that lets the metadata manager know NOT to clear the cache
     * again. Used in cases where the cache was nuked for some reason and the
     * metadata endpoint was hit, rebuilding certain caches which destroy the
     * metadata again.
     */
    public static function setCacheHasBeenCleared()
    {
        self::$cacheHasBeenCleared = true;
    }

    /**
     * Gets the flag that indicates whether the metadata manager has cleared the
     * cache on this request.
     *
     * @return bool
     */
    public static function getCacheHasBeenCleared()
    {
        return self::$cacheHasBeenCleared;
    }

    /**
     * Returns a language JSON contents
     *
     * @param array $args
     */
    public function getLanguage($args)
    {
        if (is_string($args)) {
            $lang = $args;
            $ordered = false;
        } else {
            $lang = $args['lang'];
            $ordered = empty($args['ordered']) ? false : (bool)$args['ordered'];
        }
        return $this->getLanguageFileData($lang, $ordered);
    }

    /**
     * Get the data element of the language file properties for a language
     *
     * @param string $lang The language to get data for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return string  A JSON string of langauge data
     */
    protected function getLanguageFileData($lang, $ordered = false)
    {
        $resp = $this->getLanguageFileProperties($lang, $ordered);
        return $resp['data'];
    }

    /**
     * get filter module flag
     * @param MetaDataContextInterface $context
     * @return bool
     */
    protected function getFilterModulesFlag(MetaDataContextInterface $context = null)
    {
        $filterModules = false;
        if (SugarConfig::getInstance()->get('roleBasedViews') && !($context instanceof MetaDataContextDefault)) {
            $filterModules = true;
        }

        return $filterModules;
    }

    /**
     * Gets full module list and data for each module and uses that data to
     * populate the modules/full_module_list section of the metadata
     *
     * @param array $data Existing metadata
     * @param MetaDataContextInterface|null $context Metadata context
     * @return array
     */
    public function populateModules($data, MetaDataContextInterface $context = null)
    {
        $filterModules = $this->getFilterModulesFlag($context);

        $this->data['full_module_list'] = $data['full_module_list'] = $this->getModuleList($filterModules);
        $this->data['modules'] = $data['modules'] = $this->getModulesData($context);
        $data['modules_info'] = $this->getModulesInfo([], $context);
        return $data;
    }

    /**
     * Gets the cleaned up list of modules for this client
     *
     * @param boolean $filtered Flag that tells this method whether to filter the
     *                          module list or not
     * @return array
     */
    public function getModuleList($filtered = true)
    {
        $moduleList = $this->getModules($filtered);
        $oldModuleList = $moduleList;
        $moduleList = [];
        foreach ($oldModuleList as $module) {
            $moduleList[$module] = $module;
        }

        $moduleList['_hash'] = $this->hashChunk($moduleList);
        return $moduleList;
    }

    /**
     * Gets every single module of the application and the properties for every
     * of these modules
     *
     * @return array An array with all the modules and their properties
     */
    public function getModulesInfo($data = [], MetaDataContextInterface $context = null)
    {
        global $moduleList;

        $filterModules = $this->getFilterModulesFlag($context);

        $fullModuleList = $this->getFullModuleList($filterModules);

        $modulesInfo = [];

        $visibleList = array_flip($moduleList);
        $tabs = array_flip($this->getTabList($filterModules));
        $subpanels = array_flip($this->getSubpanelList($filterModules));
        $quickcreate = array_flip($this->getQuickCreateList($filterModules));

        foreach ($fullModuleList as $module) {
            $modulesInfo[$module] = [];
            $modulesInfo[$module]['enabled'] = true;
            $modulesInfo[$module]['visible'] = isset($visibleList[$module]);
            $modulesInfo[$module]['display_tab'] = isset($tabs[$module]);
            $modulesInfo[$module]['show_subpanels'] = isset($subpanels[strtolower($module)]);
            $modulesInfo[$module]['quick_create'] = isset($quickcreate[$module]);
        }
        return $modulesInfo;
    }

    /**
     * Gets the full module list of this application. This list contains every
     * single module, not the restricted list returned by `getModuleList`.
     *
     * @return array An array of module names
     */
    public function getFullModuleList($filtered = false)
    {
        global $moduleList, $modInvisList;

        $fullModuleList = array_merge($moduleList, $modInvisList);

        if ($filtered) {
            $fullModuleList = array_keys($this->getFilteredModuleList(array_flip($fullModuleList)));
        }

        return $fullModuleList;
    }

    /**
     * Get tabs for the navigation bar of this application
     *
     * @param bool $filter when true, the tabs are filtered by the current user's ACLs
     *
     * @return array An array of module names
     */
    public function getTabList($filter = true)
    {
        $controller = new TabController();
        return array_keys($controller->get_system_tabs($filter));
    }

    /**
     * Gets the list of modules displayable as subpanels
     *
     * @param bool $filter when true, the subpanels are filtered by the current user's ACLs
     *
     * @return array An array of module names
     */
    public function getSubpanelList($filter = true)
    {
        return SubPanelDefinitions::get_all_subpanels(true, false, $filter);
    }

    /**
     * Gets the list of modules enabled in the quickcreate dropdown.
     *
     * @param bool $filter
     *
     * @return array An array of module names
     */
    public function getQuickcreateList($filter = true)
    {
        if (!isset($this->data['modules'])) {
            $this->data['modules'] = $this->getModulesData(null);
        }
        $modulesData = $this->data['modules'];

        $quickcreateModules = [];

        foreach ($modulesData as $key => $module) {
            if ($key == '_hash') {
                continue;
            }
            if (isset($modulesData[$key]) &&
                isset($modulesData[$key]['menu']) &&
                isset($modulesData[$key]['menu']['quickcreate']) &&
                isset($modulesData[$key]['menu']['quickcreate']['meta']) &&
                !empty($modulesData[$key]['menu']['quickcreate']['meta']['visible'])
            ) {
                $quickcreateModules[] = $key;
            }
        }
        return $quickcreateModules;
    }

    /**
     * Gets the list of modules for this client
     *
     * @param boolean $filtered Flag that tells this method whether to filter the
     *                          module list or not
     * @return array
     */
    protected function getModules($filtered = true)
    {

        // Loading a standard module list. Always force a fresh load to prevent inconsistent values based on bad customizations.
        $als = return_app_list_strings_language($GLOBALS['current_language'], false);
        $list = $als['moduleList'];
        // Handle filtration if we are supposed to
        if ($filtered) {
            $list = $this->getFilteredModuleList($list);
        }

        // TODO - need to make this more extensible through configuration
        $list['Audit'] = true;
        return array_keys($list);
    }

    /**
     * Gets a module list that is filtered by ACLs
     *
     * @param array $list List of modules for the application
     * @return array
     */
    public function getFilteredModuleList($list)
    {
        $user = $this->getCurrentUser();
        if (!empty($user->id) && !$this->public) {
            $list = SugarACL::filterModuleList($list);
        }

        return $list;
    }

    /**
     * Loads relationships for relate and link type fields
     * @param array $data load metadata array
     * @return array
     */
    protected function relateFields(&$data, $module, $bean)
    {
        if (isset($data['modules'][$module]['fields'])) {
            $fields = $data['modules'][$module]['fields'];

            foreach ($fields as $fieldName => $fieldDef) {
                // Load and assign any relate or link type fields
                if (isset($fieldDef['type']) && ($fieldDef['type'] == 'relate')) {
                    if (isset($fieldDef['module']) && !safeInArray($fieldDef['module'], $data['full_module_list'])) {
                        $data['full_module_list'][$fieldDef['module']] = $fieldDef['module'];
                    }
                } elseif (isset($fieldDef['type']) && ($fieldDef['type'] == 'link')) {
                    $bean->load_relationship($fieldDef['name']);
                    if (isset($bean->{$fieldDef['name']}) && method_exists($bean->{$fieldDef['name']}, 'getRelatedModuleName')) {
                        $otherSide = $bean->{$fieldDef['name']}->getRelatedModuleName();
                        $data['full_module_list'][$otherSide] = $otherSide;
                    }
                }
            }
        }
    }

    /**
     * Gets currencies
     * @return array
     */
    public function getSystemCurrencies()
    {
        $currencies = [];
        $lcurrency = new ListCurrency();
        $lcurrency->lookupCurrencies(true);
        if (!empty($lcurrency->list)) {
            foreach ($lcurrency->list as $current) {
                $currency = [];
                $currency['name'] = $current->name;
                $currency['iso4217'] = $current->iso4217;
                $currency['status'] = $current->status;
                $currency['symbol'] = $current->symbol;
                // format just like we do on the models
                $currency['conversion_rate'] = SugarMath::init($current->conversion_rate)->result();
                $currency['name'] = $current->name;
                $currency['date_entered'] = $current->date_entered;
                $currency['date_modified'] = $current->date_modified;
                $currencies[$current->id] = $currency;
            }
        }

        return $currencies;
    }

    /**
     * Gets the moduleTabMap array to allow clients to decide which menu element
     * a module should live in for non-module modules
     *
     * @return array
     */
    public function getModuleTabMap()
    {
        return $GLOBALS['moduleTabMap'];
    }

    /**
     * Returns a list of URL's pointing to json-encoded versions of the strings
     *
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return array
     */
    public function getStringUrls($ordered = false)
    {
        $languageList = array_keys(get_languages());
        sugar_mkdir(sugar_cached('api/metadata'), null, true);

        $fileList = [];
        foreach ($languageList as $language) {
            $fileList[$language] = $this->getLangUrl($language, $ordered);
        }
        $urlList = [];
        foreach ($fileList as $lang => $file) {
            // Get the hash for this lang file so we can append it to the URL.
            // This fixes issues where lang strings or list strings change but
            // don't force a metadata refresh
            $urlList[$lang] = getVersionedPath(
                $this->getUrlForCacheFile($file),
                $this->getLanguageCacheAttributes(),
                true
            );
        }
        $urlList['default'] = $GLOBALS['sugar_config']['default_language'];

        return $urlList;
    }

    /**
     * Returns additional language cache attributes for the given platform
     *
     * @return mixed
     */
    protected function getLanguageCacheAttributes()
    {
        return [
            'version' => $GLOBALS['sugar_config']['js_lang_version'],
        ];
    }

    public function getOrderedStringUrls()
    {
        return $this->getStringUrls(true);
    }

    /**
     * Public read only accessor for getting a language file hash if there is one
     *
     * @param string $lang The language to get a hash for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return string The hash if there is one, false otherwise
     */
    public function getLanguageHash($lang, $ordered = false)
    {
        return $this->getCachedLanguageHash($lang, $ordered);
    }

    /**
     * Gets a url for a language file
     *
     * @param string $language The language to get the file for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return string
     */
    protected function getLangUrl($language, $ordered = false)
    {
        $order_key = $ordered ? '_ordered' : '';
        $public_key = $this->public ? '_public' : '';
        $platform = $this->platforms[0];
        return sugar_cached("api/metadata/lang_{$language}_{$platform}{$public_key}{$order_key}.json");
    }

    /**
     * Get the hash element of the language file properties for a language
     *
     * @param string $lang The language to get data for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return string  The hash of the contents of the language file
     */
    protected function getLanguageFileHash($lang, $ordered = false)
    {
        $resp = $this->getLanguageFileProperties($lang, $ordered);
        return $resp['hash'];
    }

    /**
     * Gets the file properties for a language
     *
     * @param string $lang The language to get data for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return array   Array containing the hash and data for a language file
     */
    protected function getLanguageFileProperties($lang, $ordered = false)
    {
        $hash = $this->getCachedLanguageHash($lang, $ordered);

        // Do not filter the module list for language file generation
        $modules = $this->getModuleList(false);

        $resp = $this->buildLanguageFile($lang, $modules, $ordered);
        if (empty($hash) || $hash != $resp['hash']) {
            $this->putCachedLanguageHash($lang, $resp['hash'], $ordered);
        }

        return $resp;
    }

    /**
     * Gets a hash for a cached language
     *
     * @param string $lang The lang to get the hash for
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return string
     */
    protected function getCachedLanguageHash($lang, $ordered = false)
    {
        $key = $this->getLangUrl($lang, $ordered);

        return $this->getFromHashCache($key);
    }

    /**
     * Builds the language javascript file if needed, else returns what is known
     *
     * @param string $language The language for this file
     * @param array $modules The module list
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     * @return array Array containing the language file contents and the hash for the data
     */
    protected function buildLanguageFile($language, $modules, $ordered = false)
    {
        sugar_mkdir(sugar_cached('api/metadata'), null, true);
        $filePath = $this->getLangUrl($language, $ordered);
        if (file_exists($filePath)) {
            // Get the contents of the file so that we can get the hash
            $data = file_get_contents($filePath);

            // Decode the json and get the hash. The hash should be there but
            // check for it just in case something went wrong somewhere.
            $array = json_decode($data, true);
            $hash = $array['_hash'] ?? '';

            // Cleanup
            unset($array);

            // Return the same thing as would be returned if we had to build the
            // file for the first time
            return ['hash' => $hash, 'data' => $data];
        }

        $stringData = [];
        $stringData['app_list_strings'] = $this->getAppListStrings($language, $ordered);
        $stringData['app_strings'] = $this->getAppStrings($language);
        if ($this->public) {
            // Exception for the AppListStrings.
            $app_list_strings_public = [];
            $app_list_strings_public['available_language_dom'] = $stringData['app_list_strings']['available_language_dom'];

            // Let clients fill in any gaps that may need to be filled in
            $app_list_strings_public = $this->fillInAppListStrings($app_list_strings_public, $stringData['app_list_strings']);
            $stringData['app_list_strings'] = $app_list_strings_public;
        } else {
            $modStrings = [];
            foreach ($modules as $modName => $moduleDef) {
                $modData = $this->getModuleStrings($modName, $language);
                $modStrings[$modName] = $modData;
            }
            $stringData['mod_strings'] = $modStrings;
        }
        $stringData['_hash'] = $this->hashChunk($stringData);
        $data = json_encode($stringData);
        sugar_file_put_contents_atomic($filePath, $data);

        return ['hash' => $stringData['_hash'], 'data' => $data];
    }

    /**
     * Fills in additional app list strings data as needed by the client
     *
     * @param array $public Public app list strings
     * @param array $main Core app list strings
     * @return array
     */
    protected function fillInAppListStrings(array $public, array $main)
    {
        return $public;
    }

    /**
     * Gets a hash from a hash key in the hash cache
     *
     * @param string $key The hash cache key
     * @return string A metadata hash if found, false otherwise
     */
    protected function getFromHashCache($key)
    {
        if ($this->getCache() === false) {
            return false;
        }
        $hashes = self::$isCacheEnabled ? $this->getCache()->get('hashes') : [];
        return !empty($hashes[$key]) ? $hashes[$key] : false;
    }

    /**
     * Gets a URL for a cache file. This is most useful in overrides where a base
     * path can be prepended to the $cacheFile for use in CDN setups and such.
     *
     * @param string $cacheFile The cache file to build a URL to
     * @return string The url for the cached file
     */
    public function getUrlForCacheFile($cacheFile)
    {
        // This is here so we can override it and have the cache files upload to a CDN
        // and return the CDN locations later.
        return $cacheFile;
    }

    /**
     * Wrapper to add a language file hash to the hash cache
     *
     * @param string $lang The language string for the language file
     * @param string $hash The hash for the language file
     * @param boolean $ordered is a flag that determines $app_list_strings should be key => value pairs or tuples
     */
    protected function putCachedLanguageHash($lang, $hash, $ordered = false)
    {
        $key = $this->getLangUrl($lang, $ordered);
        $this->addToHashCache($key, $hash);
    }

    /**
     * Adds a language file hash to the hash cache
     *
     * @param string $key The key for this cache hash
     * @param string $hash The hash to match to this key
     */
    protected function addToHashCache($key, $hash)
    {
        if (!self::$isCacheEnabled) {
            return;
        }
        $hashes = $this->getCache()->get('hashes');
        if (empty($hashes)) {
            $hashes = [];
        }
        $hashes[$key] = $hash;
        $this->getCache()->set('hashes', $hashes);
    }

    /**
     * Gets override values from an argument list
     *
     * @param Array $data data to be returned to the client
     * @param Array $args args passed to the API
     */
    protected function getOverrides($data, $args = [])
    {
        if (isset($args['override_values']) && is_array($args['override_values'])) {
            return $args['override_values'];
        }

        return self::$defaultOverrides;
    }

    /**
     * Adds a metadata file hash to the hash cache
     *
     * @param string $hash The hash for the metadata file
     * @param MetaDataContextInterface $context Metadata context
     */
    protected function cacheMetadataHash($hash, MetaDataContextInterface $context)
    {
        $key = $this->getCachedMetadataHashKey($context);
        $this->addToHashCache($key, $hash);
    }

    /**
     * Gets the hash for a metadata file cache
     *
     * @param MetaDataContextInterface|null $context Metadata context
     * @return string A metadata cache file hash or false if not found
     */
    public function getCachedMetadataHash(MetaDataContextInterface $context = null, $includeBase = true)
    {
        if (!$context) {
            $context = $this->getCurrentUserContext();
        }

        $key = $this->getCachedMetadataHashKey($context);
        $hash = $this->getFromHashCache($key);

        if (!$hash) {
            return false;
        }

        if ($includeBase) {
            $baseHash = $this->getFromHashCache($this->getCachedMetadataHashKey($this->getDefaultContext()));

            if ($hash != $baseHash) {
                return md5($hash . $baseHash);
            }
        }

        return $hash;
    }

    /**
     * Saves the metadata into a cache file
     *
     * @param array $data The metadata to cache
     * @param MetaDataContextInterface $context Metadata context
     */
    protected function putMetadataCache($data, MetaDataContextInterface $context)
    {
        $this->getCache()->set($this->getCachedMetadataHashKey($context), $data);

        // Cache the hash as well
        if (isset($data['_hash'])) {
            $this->cacheMetadataHash($data['_hash'], $context);
        }
    }

    /**
     * Deletes caches for this metadata manager visibility and platform
     *
     * @param MetaDataContextInterface|null $context Metadata context
     * @return boolean
     */
    protected function deletePlatformVisibilityCaches(MetaDataContextInterface $context)
    {
        //Sets a flag that tells callers whether this action actually did something
        $deleted = false;
        // Get the hashes array
        $hashes = $this->getCache()->get('hashes');
        if (empty($hashes)) {
            $hashes = [];
        }

        // Delete language caches and remove from the hash cache
        foreach ([true, false] as $ordered) {
            $pattern = $this->getLangUrl('(.*)', $ordered);
            foreach ($hashes as $k => $v) {
                if (preg_match("#^{$pattern}$#", $k, $m)) {
                    // Add the deleted language to the stack
                    $this->deletedLanguageCaches[] = $m[1];

                    // Remove from the cache
                    unset($hashes[$k]);

                    // Delete the file
                    if (is_file($k)) {
                        unlink($k);
                    }

                    $deleted = true;
                    $this->logger->debug('deleted language cache for ' . $m[1]);
                }
            }
        }

        // Then delete the metadata cache and delete from the hash cache if there
        // is a cache to handle
        $cacheKey = $this->getCachedMetadataHashKey($context);
        if (!empty($hashes[$cacheKey])) {
            $this->getCache()->set($cacheKey, null);
            unset($hashes[$cacheKey]);
            $deleted = true;
        }

        // Save file I/O by only writing if there are changes to write
        if ($deleted) {
            $this->getCache()->set('hashes', $hashes);
        }

        // Return the flag
        return $deleted;
    }

    /**
     * Calculates a metadata hash. Removes any existing first level _hash index
     * prior to calculation.
     *
     * @param array $data
     * @return string
     */
    protected function hashChunk($data)
    {
        unset($data['_hash']);
        return md5(serialize($data));
    }

    /**
     * Determines if focus dashboards are available for the given module.
     *
     * @deprecated since 11.2.0, this is no longer used
     */
    public function isFocusDashboardEnabled(string $moduleName): bool
    {
        static $cached = null;

        if ($cached === null) {
            $moduleList = $this->getModules();
            $cached = array_fill_keys($moduleList, false);

            $query = new SugarQuery();
            $query->select(['dashboard_module']);
            $query->distinct(true);
            $query->from(BeanFactory::newBean('Dashboards'), ['team_security' => false]);
            $query->where()
                ->queryAnd()
                ->equals('view_name', 'focus')
                ->in('dashboard_module', $moduleList);
            $data = $query->execute();

            foreach ($data as $row) {
                $module = $row['dashboard_module'];
                $cached[$module] = true;
            }
        }

        return !empty($cached[$moduleName]);
    }

    /**
     * Loads up the metadata sections for this manager
     *
     * @param bool $public
     */
    protected function loadSections($public = false)
    {
        $method = 'get' . ($public ? 'Public' : 'Private') . 'Sections';
        $this->sections = $this->$method();
    }

    /**
     * Loads the standard public metadata sections. This can be overridden.
     */
    protected function getPublicSections()
    {
        return [
            self::MM_MODULES,
            self::MM_FIELDS,
            self::MM_VIEWS,
            self::MM_LAYOUTS,
            self::MM_LABELS,
            self::MM_ORDEREDLABELS,
            self::MM_CONFIG,
            self::MM_JSSOURCE,
            self::MM_LOGOURL,
            self::MM_LOGOURLDARK,
            self::MM_OVERRIDEVALUES,
        ];
    }

    /**
     * Loads the standard private metadata sections. This can be overridden.
     */
    protected function getPrivateSections()
    {
        return [
            self::MM_MODULES,
            self::MM_FULLMODULELIST,
            self::MM_MODULESINFO,
            self::MM_HIDDENSUBPANELS,
            self::MM_CURRENCIES,
            self::MM_MODULETABMAP,
            self::MM_FIELDS,
            self::MM_FILTERS,
            self::MM_VIEWS,
            self::MM_LAYOUTS,
            self::MM_DATA,
            self::MM_LABELS,
            self::MM_ORDEREDLABELS,
            self::MM_CONFIG,
            self::MM_RELATIONSHIPS,
            self::MM_JSSOURCE,
            self::MM_SERVERINFO,
            self::MM_LOGOURL,
            self::MM_LOGOURLDARK,
            self::MM_LANGUAGES,
            self::MM_OVERRIDEVALUES,
            self::MM_EDITDDFILTERS,
        ];
    }

    protected function getContextAwareSections()
    {
        return [
            self::MM_MODULES,
            self::MM_FULLMODULELIST,
            self::MM_MODULESINFO,
            self::MM_FIELDS,
            self::MM_FILTERS,
            self::MM_VIEWS,
            self::MM_LAYOUTS,
            self::MM_DATA,
            self::MM_JSSOURCE,
            self::MM_EDITDDFILTERS,
        ];
    }

    /**
     * Gets the user bean for this request
     *
     * @return User
     */
    protected function getCurrentUser()
    {
        global $current_user;
        return $current_user;
    }

    /**
     * Gets display module list per user defined tabs
     *
     * @return array The list of module names
     */
    public function getUserModuleList()
    {
        // Loading a standard module list
        $controller = new TabController();
        $tabs = $controller->get_tabs($this->getCurrentUser());
        $moduleList = array_keys($tabs[0]);
        $moduleList = $this->addHomeToModuleList($moduleList);
        return $moduleList;
    }

    /**
     * Deletes all language cache files and references in the hash cache
     *
     * @return bool True if the file no longer exists or never existed
     */
    protected function clearLanguagesCache()
    {
        // Get the hashes array handled first
        $hashes = [];
        $path = sugar_cached('api/metadata/hashes.php');
        @include $path;

        // Track which indexes were deleted
        $deleted = [];
        foreach ($hashes as $key => $hash) {
            // If the index is a .json file path, unset it and delete it
            if (strpos($key, '.json')) {
                unset($hashes[$key]);
                @unlink($key);
                $deleted[$key] = $key;
            }
        }

        // Now handle files on the file system. This should yield an empty array
        // but its better to be safe than sorry
        $files = glob(sugar_cached('api/metadata/*.json'));
        foreach ($files as $file) {
            @unlink($file);
            $deleted[$file] = $file;
        }

        if ($deleted) {
            write_array_to_file('hashes', $hashes, $path);
        }

        return true;
    }

    /**
     * Gets the key used for the metadata hash cache store
     *
     * @param MetaDataContextInterface|null $context Metadata context
     * @return string The key for this platform and visibility version of metadata
     */
    protected function getCachedMetadataHashKey(MetaDataContextInterface $context)
    {
        if ($this->public) {
            $prefix = 'public:';
        } else {
            $hash = $context->getHash();
            if ($hash) {
                $prefix = $hash . ':';
            } else {
                $prefix = '';
            }
        }

        $key = "meta:hash:$prefix" . implode(',', $this->platforms);
        return $key;
    }

    /**
     * get current user's hash key
     */
    public function getCurrentUserCachedMetadataHashKey()
    {
        global $current_user;
        if (empty($current_user)) {
            return null;
        }

        return $this->getCachedMetadataHashKey(new MetaDataContextUser($current_user));
    }

    /**
     * Public accessor that gets the hash for a metadata file cache. This is a
     * wrapper to {@see getCachedMetadataHash}
     *
     * @param bool $verifyDataExists if true, the javascript component files
     * will be verified as a part of the hash check
     * @param MetaDataContextInterface|null $context Metadata context
     *
     * @return string A metadata cache file hash or false if not found
     */
    public function getMetadataHash($verifyDataExists = false, MetaDataContextInterface $context = null)
    {
        if (!$context) {
            $context = $this->getCurrentUserContext();
        }

        // Start with the known has if there is one
        $hash = $this->getCachedMetadataHash($context);

        if ($verifyDataExists) {
            $data = $this->getMetadataCache(true, $context);
            if (!$this->verifyJSSource($data)) {
                //The jssource file is invalid, we need to invalidate the hash as well.
                return false;
            }
        }

        return $hash;
    }

    /**
     * Sets up the modules and full_module_list portion of metadata
     *
     * @param array $data Array of arguments or existing data to be written
     * @param MetaDataContextInterface $context Metadata context
     * @return array Array of data containing full_module_list and modules
     */
    protected function setupModuleLists($data, MetaDataContextInterface $context)
    {
        $method = 'setup' . ucfirst($this->visibility) . 'ModuleLists';
        return $this->$method($data, $context);
    }

    /**
     * Sets up the private module lists consisting of modules and full_module_list
     *
     * @param array $data Array of arguments or existing data to be written
     * @param MetaDataContextInterface $context Metadata context
     * @return array Array of data containing full_module_list and modules
     */
    protected function setupPrivateModuleLists($data, MetaDataContextInterface $context)
    {
        $data = $this->populateModules($data, $context);
        foreach ($data['modules'] as $moduleName => $moduleDef) {
            if (!array_key_exists($moduleName, $data['full_module_list']) && array_key_exists($moduleName, $data['modules'])) {
                unset($data['modules'][$moduleName]);
            }
        }
        $data['full_module_list']['_hash'] = $this->hashChunk($data['full_module_list']);
        $data['modules_info']['_hash'] = $this->hashChunk($data['modules_info']);
        return $data;
    }

    /**
     * Setups the public module lists, which includes modules only
     *
     * @param array $data Array of arguments or existing data to be written
     * @return array Array of data containing modules
     */
    protected function setupPublicModuleLists($data)
    {
        $data['modules'] = ['Login' => ['fields' => []]];
        return $data;
    }

    /**
     * Sets up metadata caches for various platforms and languages.
     *
     * NOTE: This can get expensive for many platforms and/or many languages.
     *
     * @param array $platforms Array of platforms for setup metadata for
     * @param array $languages Array of language metadata caches to build
     * @return void
     */
    public static function setupMetadata($platforms = [], $languages = [])
    {
        // Set up the platforms array
        if (empty($platforms)) {
            $platforms = ['base'];
        }

        if (!is_array($platforms)) {
            $platforms = (array)$platforms;
        }

        if (empty($languages)) {
            $languages = ['en_us'];
        }

        if (!is_array($languages)) {
            $languages = (array)$languages;
        }

        // Loop over the metadata managers for each platform and visibility, then
        // over each of the languages for each manager
        foreach ($platforms as $platform) {
            foreach ([true, false] as $public) {
                $mm = MetaDataManager::getManager($platform, $public);
                $mm->getMetadata(['platform' => $platform]);
                foreach ($languages as $language) {
                    $mm->getLanguage($language);
                }
            }
        }
    }

    /**
     * Adds Home as the first module of users modules lists
     *
     * @param array $moduleList Array of modules
     * @return array
     */
    protected function addHomeToModuleList($moduleList)
    {
        //If Home is not the first item of the list
        if (!empty($moduleList) && $moduleList[0] !== 'Home') {
            //Remove it if it is at a random position
            if (($key = array_search('Home', $moduleList)) !== false) {
                unset($moduleList[$key]);
            }
            //Add it to the first position
            array_unshift($moduleList, 'Home');
        }

        return $moduleList;
    }

    /**
     * Checks to see if a particular sections is supposed to be skipped in the
     * full metadata load
     *
     * @param string $section Name of the section to check
     * @return boolean
     */
    protected function sectionIsSkipped($section)
    {
        return !empty($this->sectionsToSkip[$section]);
    }

    /**
     * Normalizes the metadata response for the platform.
     *
     * This is here for platforms that need to manipulate the metadata collection
     * prior to sending it back to the client. This should be overridden as needed
     * in the platform specific metadata managers.
     *
     * @param array $data The metadata collection
     * @return array The normalize metadata collection for this platform
     */
    public function normalizeMetadata($data)
    {
        return $data;
    }

    /**
     *
     * This method collects view data for given module and view
     *
     * @param string $moduleName The name of the module
     * @param string $view The view name
     * @param MetaDataContextInterface|null $context Metadata context
     * @return array
     */
    public function getModuleView($moduleName, $view, $context = null)
    {
        $views = $this->getModuleViews($moduleName, $context);
        if (isset($views[$view])) {
            return $views[$view];
        }
        return [];
    }

    /**
     * Returns any dropdown-based view metadata defined for the given
     * module and view
     *
     * @param string $moduleName the name of the module
     * @param string $view the name of the view (e.g. 'record')
     * @param MetaDataContextInterface|null $context the metadata context
     * @return array the set of all dropdown-based metadata for the module/view
     */
    public function getModuleDropdownViewsForView($moduleName, $view, $context = null)
    {
        $dropdownViews = $this->getModuleDropdownViews($moduleName, $context);
        if (isset($dropdownViews[$view])) {
            return $dropdownViews[$view];
        }
        return [];
    }

    /**
     * Return flat list of fields defined for a given module and view
     *
     * @param string $moduleName The name of the module
     * @param string $view The view name
     * @param array $displayParams Associative array of field names and their display params on the given view
     * @return array
     */
    public function getModuleViewFields($moduleName, $view, &$displayParams = [])
    {
        $displayParams = [];
        $fields = [];

        // Get a flattened list of fields from the view metadata
        $viewData = $this->getModuleView($moduleName, $view, $this->getCurrentUserContext());
        if (!isset($viewData['meta']) || !isset($viewData['meta']['panels'])) {
            return $fields;
        }
        $varDefs = $this->getVarDef($moduleName);
        $fieldDefs = $varDefs['fields'];
        foreach ($viewData['meta']['panels'] as $panel) {
            if (isset($panel['fields']) && is_array($panel['fields'])) {
                $fields = array_merge(
                    $fields,
                    $this->getFieldNames($moduleName, $panel['fields'], $fieldDefs, $displayParams)
                );
            }
        }

        // Get a flattened list of fields from all dropdown-based view metadata
        // defined for the view and merge them into the fields list
        $viewDropdownData = $this->getModuleDropdownViewsForView($moduleName, $view, $this->getCurrentUserContext());
        $tempArray = [];
        foreach ($viewDropdownData as $dropdownValues) {
            foreach ($dropdownValues as $dropdownValue) {
                if (!empty($dropdownValue['meta']['panels'])) {
                    foreach ($dropdownValue['meta']['panels'] as $panel) {
                        if (!empty($panel['fields']) && is_array($panel['fields'])) {
                            $fields = array_merge(
                                $fields,
                                $this->getFieldNames($moduleName, $panel['fields'], $fieldDefs, $tempArray)
                            );
                        }
                    }
                }
            }
        }

        return array_values(array_unique($fields));
    }

    public function hasEditableDropdownFilter($fieldName, $role)
    {
        $filter = $this->getRawFilter($fieldName, $role);
        return safeCount($filter) > 0;
    }

    public function getEditableDropdownFilter($fieldName, $role)
    {
        $filter = $this->getRawFilter($fieldName, $role);
        $filter = $this->fixDropdownFilter($filter, $fieldName);

        return $filter;
    }

    /**
     * Return list of fields from view def field set and populate $displayParams with display parameters
     * of link and collection fields
     *
     * @param string $module Module name
     * @param array $fieldSet The field set
     * @param array $fieldDefs Bean field definitions
     * @param array $displayParams Associative array of field names and their display params
     * @return array
     *
     * @access protected Should be used only by SugarFieldBase and subclasses
     */
    public function getFieldNames($module, array $fieldSet, array $fieldDefs, &$displayParams)
    {
        $fields = [];
        $it = $this->getViewIterator($module, $fieldDefs);
        $it->apply($fieldSet, function (array $field) use (&$fields, &$displayParams) {
            $name = $field['name'];
            unset($field['name']);

            $displayParams[$name] = $field;
        });

        $fields = array_keys($displayParams);

        return $fields;
    }

    /**
     * Returns view iterator for the given module and field definitions
     *
     * @param string $module Module name
     * @param array $fieldDefs Field definitions
     *
     * @return ViewIterator
     */
    protected function getViewIterator($module, array $fieldDefs)
    {
        return new ViewIterator($module, $fieldDefs);
    }


    /**
     * Returns editable dropdown filters
     *
     * @param array $data Existing data
     * @param MetaDataContextInterface $context Metadata context
     *
     * @return array
     */
    public function getEditableDropdownFilters($data = [], MetaDataContextInterface $context = null)
    {
        if ($this->public) {
            return [];
        }
        if (is_null($context)) {
            $context = $this->getCurrentUserContext();
        }

        $platform = $this->platforms[0];
        $files = array_map(
            function ($file) use ($platform) {
                $info = pathinfo($file);
                return [
                    'path' => $file,
                    'file' => $info['basename'],
                    'subPath' => $info['dirname'],
                    'platform' => $platform,
                    'params' => MetaDataFiles::getClientFileParams($file),
                ];
            },
            glob('custom/application/Ext/DropdownFilters/roles/*/dropdownfilters.ext.php')
        );

        $files = array_filter($files, function (array $file) use ($context) {
            return $context->isValid($file);
        });

        uasort($files, function ($a, $b) use ($context) {
            return $context->compare($a, $b);
        });

        if (!safeCount($files)) {
            return [];
        }

        $filters = [];
        foreach ($files as $file) {
            $fileFilters = $this->readDropdownFilterFile($file['path']);
            foreach ($fileFilters as $fieldName => $filter) {
                // make sure first file wins and its metadata doesn't get overridden
                if (!isset($filters[$fieldName])) {
                    $filters[$fieldName] = $this->fixDropdownFilter($filter, $fieldName);
                    //To preserve order in JSON, we need to return the filters as tuples.
                    $filters[$fieldName] = array_map(null, array_keys($filters[$fieldName]), $filters[$fieldName]);
                }
            }
        }

        return $filters;
    }

    /**
     * Reads medatata file and returns raw data contained in it
     *
     * @param string $path File path
     * @return array
     */
    protected function readDropdownFilterFile($path)
    {
        $role_dropdown_filters = [];
        require $path;

        return $role_dropdown_filters;
    }

    /**
     * Returns filter definition for the given dropdown field exactly as it's stored in metadata files, or empty array
     * in case if the definition is not found
     *
     * @param string $fieldName Dropdown field name
     * @param string $role Role ID
     * @return array
     */
    protected function getRawFilter($fieldName, $role)
    {
        $path = 'custom/application/Ext/DropdownFilters/roles/' . $role . '/dropdownfilters.ext.php';
        if (file_exists($path)) {
            $filters = $this->readDropdownFilterFile($path);
            if (isset($filters[$fieldName])) {
                return $filters[$fieldName];
            }
        }

        return [];
    }

    /**
     * Fixes filter definition by making sure it contains all options from the default list and doesn't contain
     * options which are not in the default list
     *
     * @param array $filter Raw filter definition
     * @param string $fieldName Dropdown field name
     * @return array
     */
    protected function fixDropdownFilter(array $filter, $fieldName)
    {
        global $app_list_strings;

        if (isset($app_list_strings[$fieldName]) && is_array($app_list_strings[$fieldName])) {
            // by default, items not in the filter list are hidden unless the filter is empty
            $defaults = array_fill_keys(array_keys($app_list_strings[$fieldName]), empty($filter));
            // remove non-existing options from the filter
            $filter = array_intersect_key($filter, $defaults);
            // add default options to the filter and preserve original key order
            $filter = array_replace($filter, array_diff_key($defaults, $filter));
        }

        return $filter;
    }

    /**
     * Lazily loads metadata hacks instance
     *
     * @return MetaDataHacks
     */
    protected function getMetaDataHacks()
    {
        if (!$this->metaDataHacks) {
            $className = SugarAutoLoader::customClass('MetaDataHacks');
            $this->metaDataHacks = new $className();
        }

        return $this->metaDataHacks;
    }

    /**
     * Checks if metadata cache is operable with current database schema
     *
     * This check should be made during upgrades when the source code has been upgraded by the moment,
     * but database schema hasn't yet.
     *
     * @return bool
     */
    public static function isCacheOperable()
    {
        return DBManagerFactory::getInstance()->tableExists(static::$cacheTable);
    }

    /**
     * Enables metadata caching after it was temporarily disabled
     */
    public static function enableCache()
    {
        self::$isCacheEnabled = true;
    }

    /**
     * Temporarily disables metadata caching
     */
    public static function disableCache()
    {
        self::$isCacheEnabled = false;
    }

    /**
     * Returns the current state of the metadata cache
     * @return bool
     */
    public static function cacheEnabled()
    {
        return self::$isCacheEnabled;
    }

    /**
     * Returns all possible metadata context combinations from the given set of metadata parameters
     *
     * @param boolean $public Is metadata public
     * @param array $params Additional metadata parameters
     * @return MetaDataContextInterface[]
     */
    protected static function getMetadataContexts($public, array $params)
    {
        $contexts = [];
        if (!$public && isset($params['role'])) {
            $roleSets = self::getRoleSetsByRoles([$params['role']]);
            foreach ($roleSets as $roleSet) {
                $contexts[] = new MetaDataContextRoleSet($roleSet);
            }
        } else {
            $contexts[] = new MetaDataContextDefault();
        }

        return $contexts;
    }

    /**
     * Returns all possible metadata contexts, context will be based on license's types
     *
     * @param boolean $public Is metadata public
     * @return MetaDataContextInterface[]
     */
    protected static function getAllMetadataContexts($public)
    {
        $contexts = [];
        $allSubsetsOfSystemSubscriptions = SubscriptionManager::instance()->getAllSubsetsOfSystemSubscriptions();
        $users = [];
        foreach ($allSubsetsOfSystemSubscriptions as $subscriptions) {
            $user = BeanFactory::newBean('Users');
            $user->license_type = json_encode($subscriptions);
            $users[] = $user;
            $adminUser = BeanFactory::newBean('Users');
            $adminUser->is_admin = '1';
            $adminUser->license_type = json_encode($subscriptions);
            $users[] = $adminUser;
        }
        if (!$public) {
            $roleSets = self::getAllRoleSets();
            foreach ($roleSets as $roleSet) {
                $contexts[] = new MetaDataContextRoleSet($roleSet);
                foreach ($users as $user) {
                    $contexts[] = new MetaDataContextUser($user, $roleSet);
                }
            }
        }
        $contexts[] = new MetaDataContextDefault();
        foreach ($users as $user) {
            $contexts[] = new MetaDataContextUser($user);
        }

        return $contexts;
    }


    /**
     * Returns set of role sets which include any of the given roles
     *
     * @param array $roles IDs of roles
     * @return ACLRoleSet[]
     * @todo Move this to ACLRoleSet when it's merged
     */
    protected static function getRoleSetsByRoles(array $roles)
    {
        if (!$roles) {
            return [];
        }

        $roleSet = BeanFactory::newBean('ACLRoleSets');
        $query = new SugarQuery();
        $query->distinct(true);
        $query->from($roleSet);
        $query->select('id', 'hash');
        $query->join('acl_roles', ['alias' => 'roles']);
        $query->where()->in('roles.id', $roles);
        $data = $query->execute();

        return self::createCollectionFromDataSet($roleSet, $data);
    }

    /**
     * Returns all role sets
     *
     * @return ACLRoleSet[]
     * @todo Move this to ACLRoleSet when it's merged
     */
    protected static function getAllRoleSets()
    {
        $roleSet = BeanFactory::newBean('ACLRoleSets');
        //Verify that rolesets are operable before attempting to use them.
        if (empty($roleSet)) {
            return [];
        }
        $query = new SugarQuery();
        $query->from($roleSet);
        $query->select('id', 'hash');
        $data = $query->execute();

        return self::createCollectionFromDataSet($roleSet, $data);
    }

    /**
     * Creates collection of beans from the seed and data set
     *
     * @param SugarBean $seed Seed bean
     * @param array $data Data set
     *
     * @return SugarBean[]
     * @todo Move this to ACLRoleSet when it's merged
     */
    protected static function createCollectionFromDataSet(SugarBean $seed, array $data)
    {
        $result = [];

        // clone the seed for each row and populate it with the row data.
        // do not construct every instance individually since it's relatively expensive
        foreach ($data as $row) {
            $result[] = $clone = clone $seed;
            $clone->populateFromRow($row);
        }

        return $result;
    }

    /**
     * Gets the current config for Customer Journey
     * base on license
     *
     * @param array $sugarConfig The current Sugar Config
     * @return array
     */
    protected function getCustomerJourneyConfig($sugarConfig)
    {
        if (!isset($sugarConfig['customer_journey'])) {
            return [];
        }

        return $sugarConfig['customer_journey'];
    }
}
