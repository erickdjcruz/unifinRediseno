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

use Sugarcrm\Sugarcrm\AccessControl\AdminWork;
use Sugarcrm\Sugarcrm\CSP\ContentSecurityPolicy;
use Sugarcrm\Sugarcrm\CSP\Directive;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\Engine\Elastic;

/**
 *
 * Administration API
 *
 */
class AdministrationApi extends SugarApi
{
    /**
     * Register endpoints
     * @return array
     */
    public function registerApiRest()
    {
        return [

            //// Search administration ////

            'searchReindex' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'search', 'reindex'],
                'pathVars' => [''],
                'method' => 'searchReindex',
                'shortHelp' => 'Perform a reindex',
                'longHelp' => 'include/api/help/administration_search_reindex_post_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'searchStatus' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'search', 'status'],
                'pathVars' => [''],
                'method' => 'searchStatus',
                'shortHelp' => 'Search status',
                'longHelp' => 'include/api/help/administration_search_status_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'searchFields' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'search', 'fields'],
                'pathVars' => [''],
                'method' => 'searchFields',
                'shortHelp' => 'List search field configuration',
                'longHelp' => 'include/api/help/administration_search_fields_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],

            //// Elasticsearch administration ////

            'elasticSearchQueue' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'queue'],
                'pathVars' => [''],
                'method' => 'elasticSearchQueue',
                'shortHelp' => 'Elasticsearch queue statistics',
                'longHelp' => 'include/api/help/administration_elasticsearch_queue_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchRouting' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'routing'],
                'pathVars' => [''],
                'method' => 'elasticSearchRouting',
                'shortHelp' => 'Elasticsearch index routing',
                'longHelp' => 'include/api/help/administration_elasticsearch_routing_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchIndices' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'indices'],
                'pathVars' => [''],
                'method' => 'elasticSearchIndices',
                'shortHelp' => 'Elasticsearch index statistics',
                'longHelp' => 'include/api/help/administration_elasticsearch_indices_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchMapping' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'mapping'],
                'pathVars' => [''],
                'method' => 'elasticSearchMapping',
                'shortHelp' => 'Elasticsearch index mappings',
                'longHelp' => 'include/api/help/administration_elasticsearch_mapping_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],

            // Refresh API's
            'elasticSearchRefreshStatus' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'refresh', 'status'],
                'pathVars' => [''],
                'method' => 'elasticSearchRefreshStatus',
                'shortHelp' => 'Elasticsearch index refresh status',
                'longHelp' => 'include/api/help/administration_elasticsearch_refresh_status_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchRefreshTrigger' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'elasticsearch', 'refresh', 'trigger'],
                'pathVars' => [''],
                'method' => 'elasticSearchRefreshTrigger',
                'shortHelp' => 'Elasticsearch trigger an index refresh',
                'longHelp' => 'include/api/help/administration_elasticsearch_refresh_trigger_post_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchRefreshEnable' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'elasticsearch', 'refresh', 'enable'],
                'pathVars' => [''],
                'method' => 'elasticSearchRefreshEnable',
                'shortHelp' => 'Elasticsearch enable index refresh',
                'longHelp' => 'include/api/help/administration_elasticsearch_refresh_enable_post_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],

            // Replica API's
            'elasticSearchReplicasStatus' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'elasticsearch', 'replicas', 'status'],
                'pathVars' => [''],
                'method' => 'elasticSearchReplicasStatus',
                'shortHelp' => 'Elasticsearch index replica status',
                'longHelp' => 'include/api/help/administration_elasticsearch_replicas_status_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],
            'elasticSearchReplicasEnable' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'elasticsearch', 'replicas', 'enable'],
                'pathVars' => [''],
                'method' => 'elasticSearchReplicasEnable',
                'shortHelp' => 'Elasticsearch enable index replicas',
                'longHelp' => 'include/api/help/administration_elasticsearch_replicas_enable_post_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                ],
            ],

            // Enable migration mode for Idm
            'enableIdmMigration' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'idm', 'migration', 'enable'],
                'pathVars' => [''],
                'method' => 'enableIdmMigration',
                'shortHelp' => 'Enable IDM api to perform migrations',
                'longHelp' => 'include/api/help/administration_enable_idm_migrations_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'minVersion' => '11.2',
                'ignoreSystemStatusError' => true,
            ],
            // Disable migration mode for Idm
            'disableIdmMigration' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'idm', 'migration', 'disable'],
                'pathVars' => [''],
                'method' => 'disableIdmMigration',
                'shortHelp' => 'Disable IDM migrations',
                'longHelp' => 'include/api/help/administration_disable_idm_migrations_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'minVersion' => '11.2',
                'ignoreSystemStatusError' => true,
            ],
            // license limits
            'licenseLimits' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'license', 'limits'],
                'pathVars' => [''],
                'method' => 'getLicenseLimits',
                'shortHelp' => 'get license seats',
                'longHelp' => 'include/api/help/administration_disable_license_limits_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
            ],
            'getAWSConfig' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'aws'],
                'pathVars' => [''],
                'method' => 'getAWSConfig',
                'shortHelp' => 'Gets configuration settings for Amazon Web Services integrations',
                'longHelp' => 'include/api/help/administration_get_aws_config_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'maxVersion' => '11.14',
            ],
            'setAWSConfig' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'aws'],
                'pathVars' => [''],
                'method' => 'setAWSConfig',
                'shortHelp' => 'Sets configuration settings for Amazon Web Services integrations',
                'longHelp' => 'include/api/help/administration_set_aws_config_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'maxVersion' => '11.14',
            ],
            'getCSPConfig' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'csp'],
                'pathVars' => [''],
                'method' => 'getCSPSConfig',
                'shortHelp' => 'Gets configuration settings for Content Security Policy',
                'longHelp' => 'include/api/help/administration_get_csp_setting_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'maxVersion' => '11.14',
            ],
            'setCSPConfig' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'csp'],
                'pathVars' => [''],
                'method' => 'setCSPConfig',
                'shortHelp' => 'Sets configuration settings for Content Security Policy',
                'longHelp' => 'include/api/help/administration_set_csp_setting_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'maxVersion' => '11.14',
            ],
            'getValidateIPAddress' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'settings', 'validateIPAddress'],
                'pathVars' => [''],
                'method' => 'getValidateIPAddress',
                'shortHelp' => 'Gets value of Validate IP Address setting',
                'longHelp' => 'include/api/help/administration_get_validate_ip_address.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'minVersion' => '11.12',
            ],
            'getErrors' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'errors'],
                'pathVars' => [''],
                'method' => 'getErrors',
                'shortHelp' => 'Get errors',
                'longHelp' => 'include/api/help/administration_errors_get_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
                'minVersion' => '11.15',
            ],
            'getPortalModules' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'portalmodules'],
                'pathVars' => ['module', ''],
                'method' => 'getPortalModules',
                'shortHelp' => 'This method returns the modules currently enabled in Portal Settings',
                'longHelp' => 'include/api/help/administration_get_portal_modules.html',
                'minVersion' => '11.13',
            ],
            'getAdminPanelDefs' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'adminPanelDefs'],
                'pathVars' => ['module', ''],
                'method' => 'getAdminPanelDefs',
                'shortHelp' => 'Get metadata for Admin Panels',
                'longHelp' => 'include/api/help/administration_get_admin_panel_defs.html',
                'minVersion' => '11.15',
            ],
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['Administration', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'retrieveRecord',
            ],
        ];
    }

    /**
     * Added to prevent handling of `/Administration/<something>` with \ModuleApi::retrieveRecord
     * if `/Administration/<something>` doesn't match any of the routes registered in AdministrationApi
     * @param ServiceBase $api
     * @param array $args
     * @throws SugarApiExceptionNotFound
     */
    public function retrieveRecord(ServiceBase $api, array $args)
    {
        throw new SugarApiExceptionNotFound();
    }

    /**
     * Search reindex
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function searchReindex(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $clearData = isset($args['clear_data']) ? (bool)$args['clear_data'] : false;
        $modules = empty($args['module_list']) ? [] : explode(',', $args['module_list']);
        $engine = $this->getSearchEngine();
        $status = $engine->scheduleIndexing($modules, $clearData);

        return ['success' => $status];
    }

    /**
     * Search status
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function searchStatus(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine();

        // Check if search backend is available
        if (!$engine->isAvailable(true)) {
            return ['available' => false];
        }

        $modules = $engine->getMetaDataHelper()->getAllEnabledModules();
        sort($modules);

        $status = [
            'available' => true,
            'enabled_modules' => $modules,
        ];

        return $status;
    }

    /**
     * Search field configuration
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function searchFields(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();
        $modules = empty($args['module_list']) ? [] : explode(',', $args['module_list']);
        $list = $this->getSearchFields($modules);

        if (isset($args['order_by_boost'])) {
            $orderByBoost = true;
            $searchOnly = true;
        } else {
            $orderByBoost = false;
            $searchOnly = isset($args['search_only']);
        }

        // filter searchable fields only
        if ($searchOnly) {
            foreach ($list as $module => $fields) {
                $list[$module] = array_filter($fields, function ($value) {
                    return !empty($value['searchable']);
                });
            }
        }

        // order by boost returning a flat list
        if ($orderByBoost) {
            $flat = [];
            foreach ($list as $module => $fields) {
                foreach ($fields as $field => $defs) {
                    $key = $module . '.' . $field;
                    $flat[$key] = $defs['boost'];
                }
            }
            arsort($flat, SORT_NUMERIC);
            $list = $flat;
        }

        return $list;
    }

    /**
     * Get search fields for given modules
     * @param array $modules
     * @return array
     */
    protected function getSearchFields(array $modules)
    {
        $metaDataHelper = $this->getSearchEngine()->getMetaDataHelper();

        // use all modules if non given
        $modules = $modules ?: $metaDataHelper->getAllEnabledModules();

        $fields = [];
        foreach ($modules as $module) {
            $fields[$module] = [];
            foreach ($metaDataHelper->getFtsFields($module) as $defs) {
                $searchable = $metaDataHelper->isFieldSearchable($defs);

                $field = [
                    'name' => $defs['name'],
                    'type' => $defs['type'],
                    'searchable' => $searchable,
                ];

                // add boost value for searchable fields
                if ($searchable) {
                    if (isset($defs['full_text_search']['boost'])) {
                        $field['boost'] = $defs['full_text_search']['boost'];
                    } else {
                        $field['boost'] = 1;
                    }
                }

                $fields[$module][$defs['name']] = $field;
            }
        }
        return $fields;
    }

    /**
     * Elasticsearch queue
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchQueue(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $total = 0;
        $queued = [];

        // get statistics per module
        $queueManager = $this->getSearchEngine(true)->getContainer()->queueManager;
        foreach ($queueManager->getQueuedModules() as $module) {
            $queued[$module] = $queueManager->getQueueCountModule($module);
        }

        // total count
        if ($queued) {
            $total = array_reduce($queued, function ($carry, $value) {
                return $carry + $value;
            });
        }

        return [
            'total' => $total,
            'queued' => $queued,
        ];
    }

    /**
     * Elasticsearch index routing
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchRouting(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        $metaDataHelper = $engine->getMetaDataHelper();
        $indexPool = $engine->getContainer()->indexPool;

        $result = [];
        foreach ($metaDataHelper->getAllEnabledModules() as $module) {
            $read = [];
            foreach ($indexPool->getReadIndices([$module])->getIterator() as $index) {
                $read[] = $index->getName();
            }

            $result[$module] = [
                'strategy' => $indexPool->getStrategy($module)->getIdentifier(),
                'routing' => [
                    'write_index' => $indexPool->getWriteIndex($module)->getName(),
                    'read_indices' => $read,
                ],
            ];
        }

        return $result;
    }

    /**
     * Elasticsearch index statistics
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchIndices(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);

        $indices = [];
        foreach ($this->getIndices($engine) as $index) {
            $indices[$index->getName()] = $index->getStats()->getData();
        }

        return $indices;
    }

    /**
     * Elasticsearch mapping
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchMapping(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);

        $indices = [];
        foreach ($this->getIndices($engine) as $index) {
            $indices[$index->getName()] = $index->getMapping();
        }
        return $indices;
    }

    /**
     * Get managed indices
     * @param Elastic $engine
     * @return \Elastica\Index[]
     */
    protected function getIndices(Elastic $engine)
    {
        $indexPool = $engine->getContainer()->indexPool;
        $modules = $engine->getMetaDataHelper()->getAllEnabledModules();
        return $indexPool->getManagedIndices($modules)->getIterator();
    }

    /**
     * Get SearchEngine
     * @param boolean $checkElastic Check if backend is Elastic
     * @return Elastic
     * @throws SugarApiExceptionSearchUnavailable
     */
    protected function getSearchEngine($checkElastic = false)
    {
        $searchEngine = SearchEngine::getInstance()->getEngine();
        if ($checkElastic && !$searchEngine instanceof Elastic) {
            throw new SugarApiExceptionSearchUnavailable(
                'Administration not supported for non Elasticsearch backend'
            );
        }
        return $searchEngine;
    }

    /**
     * Ensure current user has admin permissions
     * @throws SugarApiExceptionNotAuthorized
     */
    protected function ensureAdminUser()
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isAdmin()) {
            throw new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['EXCEPTION_NOT_AUTHORIZED']
            );
        }
    }

    /**
     * Ensure current user has admin permissions, or he is developer for any module
     * @throws SugarApiExceptionNotAuthorized
     */
    public function ensureDeveloperUser()
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isDeveloperForAnyModule()) {
            throw new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['EXCEPTION_NOT_AUTHORIZED']
            );
        }
    }

    /**
     * Get refresh status for all indices
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchRefreshStatus(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();

        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        $indices = [];

        foreach ($this->getIndices($engine) as $index) {
            $indices[$index->getName()] = $index->getSettings()->getRefreshInterval();
        }

        return $indices;
    }

    /**
     * Trigger a manual refresh on all indices
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchRefreshTrigger(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        $indices = [];

        foreach ($this->getIndices($engine) as $index) {
            $status = $index->refresh();
            $indices[$index->getName()] = $status->getStatus();
        }

        return $indices;
    }

    /**
     * Enable refresh on all indices
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchRefreshEnable(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        return $engine->getContainer()->indexManager->enableRefresh();
    }

    /**
     * Get replica status for all indices
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchReplicasStatus(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        $indices = [];

        foreach ($this->getIndices($engine) as $index) {
            $indices[$index->getName()] = $index->getSettings()->get('number_of_replicas');
        }
        return $indices;
    }

    /**
     * Enable replicas on all indices
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function elasticSearchReplicasEnable(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $engine = $this->getSearchEngine(true);
        return $engine->getContainer()->indexManager->enableReplicas();
    }

    /**
     * Enable idm migrations.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function enableIdmMigration(ServiceBase $api, array $args): array
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $configurator = $this->getConfigurator();
        $configurator->config['maintenanceMode'] = true;
        $configurator->config['idmMigration'] = true;
        $configurator->handleOverride();
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate('config_override.php', true);
        }

        return ['success' => 'true'];
    }

    /**
     * Disable idm migrations.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function disableIdmMigration(ServiceBase $api, array $args): array
    {
        $this->ensureAdminUser();
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $configurator = $this->getConfigurator();
        $configurator->config['maintenanceMode'] = false;
        $configurator->config['idmMigration'] = false;
        $configurator->handleOverride();
        $this->clearCache();
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate('config_override.php', true);
        }

        return ['success' => 'true'];
    }

    /**
     * get license limits for the instance, the limits are based on license types
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getLicenseLimits(ServiceBase $api, array $args): array
    {
        $this->ensureAdminUser();
        $seats = SubscriptionManager::instance()->getSystemSubscriptionSeats();
        $defaultType = SubscriptionManager::instance()->getUserDefaultLicenseType();
        $usedSeats = SubscriptionManager::instance()->getSystemUserCountByLicenseTypes();
        $availableSeats = [];
        foreach (SubscriptionManager::instance()->getAllSupportedProducts() as $key) {
            $availableSeats[$key] = ($seats[$key] ?? 0) - ($usedSeats[$key] ?? 0);
        }
        $admin = Administration::getSettings();

        return [
            'license_key' => SubscriptionManager::instance()->getLicenseKey(),
            'default_limit' => $seats[$defaultType],
            'default_license_type' => $defaultType,
            'limit_enforced' => empty($admin->settings['license_enforce_user_limit']) ? 0 : 1,
            'seats' => $seats,
            'available_seats' => $availableSeats,
            'metadata' => SubscriptionManager::instance()->getSystemSubscriptions(),
        ];
    }

    /**
     * Gets AWS configuration details for Serve instances
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @deprecated Since 11.2.0. Please use getConfig in ConfigApi instead.
     */
    public function getAWSConfig(ServiceBase $api, array $args)
    {
        $msg = 'This endpoint is deprecated as of 11.2.0 and will be removed in a future release.';
        LoggerManager::getLogger()->deprecated($msg);

        $this->ensureAdminUser();
        $admin = BeanFactory::getBean('Administration');
        if ($admin->isLicensedForServe() || $admin->isLicensedForSell()) {
            return $admin->retrieveSettings('aws', true)->settings;
        }

        return [];
    }

    /**
     * Saves new AWS configuration details for Serve instances and returns what was saved
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @deprecated Since 11.2.0. Please use setConfig in ConfigApi instead.
     */
    public function setAWSConfig(ServiceBase $api, array $args)
    {
        $msg = 'This endpoint is deprecated as of 11.2.0 and will be removed in a future release.';
        LoggerManager::getLogger()->deprecated($msg);

        $this->ensureAdminUser();
        $admin = BeanFactory::getBean('Administration');

        // We only want to do this for Serve and Sell licensed intances
        if ($admin->isLicensedForServe() || $admin->isLicensedForSell()) {
            $category = 'aws';
            $prefix = $category . '_';
            $changes = [];

            $oldSettings = $admin->retrieveSettings($category)->settings;

            foreach ($args as $key => $value) {
                // Look specifically for anything prefixed with aws_
                if (substr($key, 0, 4) === $prefix) {
                    if ($admin->saveSetting($category, str_replace($prefix, '', $key), $value, $api->platform)) {
                        $changes[$key] = $value;
                    }
                }
            }

            if ($changes) {
                $this->updateAWSDomainsOnCSP($changes, $oldSettings);

                // Only reset the config metadata cache if there were changes to save
                self::clearCache();
                return $changes;
            }
        }

        return [];
    }

    /**
     * Update allowed AWS domains in content security policies
     *
     * @param array $settings
     * @param array $oldSettings
     */
    public function updateAWSDomainsOnCSP(array $settings, array $oldSettings)
    {
        $awsUrlKey = 'aws_connect_url';

        // if the key does not exist, nothing to update
        if (!array_key_exists($awsUrlKey, $settings)) {
            return;
        }

        $oldAwsUrl = $oldSettings[$awsUrlKey];
        $awsUrl = $settings[$awsUrlKey];

        $domainsToAppend = $domainsToRemove = [];
        $allowListDomains = $this->getAWSAllowListDomains($settings);
        $csp = ContentSecurityPolicy::fromAdministrationSettings();

        if (empty($awsUrl)) {
            // remove previously added domains as the new value is empty
            $domainsToRemove = $allowListDomains;
        } else {
            $domainsToAppend = $allowListDomains;
        }

        // remove the previously added AWS URL
        if (!empty($oldAwsUrl)) {
            $domainsToRemove[] = parse_url($oldAwsUrl, PHP_URL_HOST);
        }

        foreach ($domainsToRemove as $domain) {
            $directive = Directive::createHidden('default-src', $domain);
            $csp->removeDirective($directive);
        }

        foreach ($domainsToAppend as $domain) {
            $directive = Directive::createHidden('default-src', $domain);
            $csp->appendDirective($directive);
        }

        $csp->saveToSettings();
    }

    /**
     * Get the list of allowed domains for AWS
     *
     * @param array $args
     * @return array
     */
    public function getAWSAllowListDomains(array $args): array
    {
        $awsConfig = $this->getSugarConfig()->get('aws_connect');
        $allowListDomains = $awsConfig['allow_list_domains'];
        $awsUrl = $args['aws_connect_url'];

        if (!empty($awsUrl)) {
            $allowListDomains[] = parse_url($awsUrl, PHP_URL_HOST);
        }

        return $allowListDomains;
    }

    /**
     * Gets CSP configuration settings
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @deprecated Since 11.2.0. Please use getConfig in ConfigApi instead.
     */
    public function getCSPSConfig(ServiceBase $api, array $args): array
    {
        $cspSettings = [];
        $msg = 'This endpoint is deprecated as of 11.2.0 and will be removed in a future release.';
        LoggerManager::getLogger()->deprecated($msg);

        $this->ensureAdminUser();
        $admin = BeanFactory::getBean('Administration');
        $settings = $admin->retrieveSettings('csp', true)->settings;
        $cspSettings['csp_default_src'] = $settings['csp_default_src'] ?? '';

        return array_merge(['csp_default_src' => ''], $cspSettings);
    }

    /**
     * Saves new CSP settings configuration and returns what was saved
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     * @throws SugarApiException
     * @deprecated Since 11.2.0. Please use setConfig in ConfigApi instead.
     */
    public function setCSPConfig(ServiceBase $api, array $args): array
    {
        $msg = 'This endpoint is deprecated as of 11.2.0 and will be removed in a future release.';
        LoggerManager::getLogger()->deprecated($msg);

        $this->ensureAdminUser();
        $prefix = 'csp_';
        $directives = [];
        foreach ($args as $key => $value) {
            if (substr($key, 0, 4) === $prefix) {
                if ($key === 'csp_default_src') {
                    if (trim($value) === '') {
                        $directives[] = Directive::createWithEmptySource('default-src');
                    } else {
                        $directives[] = Directive::create('default-src', $value);
                    }
                }
                if (!empty($directives)) {
                    $csp = ContentSecurityPolicy::fromDirectivesList(...$directives);
                    $csp->saveToSettings($api->platform);
                }
            }
        }
        return $this->getCSPSConfig($api, $args);
    }

    /**
     * Gets Validate IP Address setting
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getValidateIPAddress(ServiceBase $api, array $args)
    {
        $this->ensureAdminUser();
        $verifyClientIp = (bool)$this->getSugarConfig()->get('verify_client_ip', false);
        return ['validate_ip_address' => $verifyClientIp];
    }

    /**
     * Factory method to mock Configurator
     *
     * @return Configurator
     */
    protected function getConfigurator(): Configurator
    {
        return new Configurator();
    }

    /**
     * Clears required metadata cache
     */
    protected function clearCache(): void
    {
        \MetaDataManager::refreshSectionCache(\MetaDataManager::MM_CONFIG);
    }

    /**
     * @return SugarConfig|null
     */
    public function getSugarConfig(): ?SugarConfig
    {
        return SugarConfig::getInstance();
    }

    /**
     * Get errors
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getErrors(ServiceBase $api, array $args): array
    {
        $this->ensureDeveloperUser();
        $errorMessages = [];
        $GLOBALS['system_notification_buffer'] = [];
        $GLOBALS['buffer_system_notifications'] = true;
        $GLOBALS['system_notification_count'] = 0;
        $sv = new SugarView();
        $sv->includeClassicFile('modules/Administration/DisplayWarnings.php');
        if (!empty($GLOBALS['system_notification_buffer'])) {
            foreach ($GLOBALS['system_notification_buffer'] as $errorMessage) {
                // remove tag, eg, <p class="error">there is an error</p>
                $errorMessages[] = substr(substr($errorMessage, 17), 0, -4);
            }
        }
        $errorMessages = array_merge($errorMessages, SugarApplication::getErrorMessages());
        // convert bwc links to sidecar links
        foreach ($errorMessages as &$errorMessage) {
            $errorMessage = $this->convertBWCLinks($errorMessage);
        }
        return $errorMessages;
    }

    /**
     * Convert bwc links in a message to sidecar links, eg, index.php?module=EmailMan&action=config
     * will be converted to #bwc/index.php?module=EmailMan&action=config
     * @param string $message
     * @return string
     */
    protected function convertBWCLinks(string $message): string
    {
        $regexp = "<a\s[^>]*href\s*=\s*([\"\']??)(index\.php[^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if (preg_match_all("/$regexp/siU", $message, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $link = $match[2];
                $message = str_replace($link, '#bwc/' . $link, $message);
            }
        }
        return $message;
    }


    /**
     * Method to check which modules are currently enabled for Sugar Portal
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array Array of modules enabled in portal
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getPortalModules(ServiceBase $api, array $args): array
    {
        $this->ensureAdminUser();
        $tabController = new TabController();
        return $tabController::getPortalTabs();
    }

    /**
     * Get metadata for Admin Panels
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getAdminPanelDefs(ServiceBase $api, array $args): array
    {
        $this->ensureDeveloperUser();

        return $this->getParsedAdminPanelDefsFromLegacyDefs();
    }

    /**
     * Get and return the legacy Admin Panels metadata
     *
     * @return array
     */
    public function getAdminPanelLegacyDefs(): array
    {
        $admin_group_header = [];
        require 'modules/Administration/metadata/adminpaneldefs.php';

        return $admin_group_header;
    }

    /**
     * Get and parse the Admin Panels metadata
     *
     * @return array
     */
    public function getParsedAdminPanelDefsFromLegacyDefs(): array
    {
        $legacyDefs = $this->getAdminPanelLegacyDefs();
        $defs = [];

        foreach ($legacyDefs as $legacyDef) {
            $newDefs = [];

            // the label is at index 0 in legacy metadata
            $newDefs['label'] = array_key_exists(0, $legacyDef) ? $legacyDef[0] : '';

            // the description is at index 4 in legacy metadata
            $newDefs['description'] = array_key_exists(4, $legacyDef) ? $legacyDef[4] : '';

            // $legacyDef[3] = [
            //      [module] => [
            //          [section_key] => [
            //              [0] => module,
            //              [link] => sicon,
            //              [1] => label,
            //              [2] => description,
            //              [3] => link,
            //              [5] => onclick,
            //              [6] => target,
            //          ]
            //      ]
            // ]
            if (array_key_exists(3, $legacyDef)) {
                $legacyOptionDefs = $legacyDef[3];

                $newDefs['options'] = [];

                // each [module]
                foreach ($legacyOptionDefs as $legacyOptionDef) {
                    $optionDefSections = array_values($legacyOptionDef);

                    // each [section_key]
                    foreach ($optionDefSections as $section) {
                        if (!is_array($section)) {
                            continue;
                        }

                        $option = [];

                        $option['label'] = array_key_exists(1, $section) ? $section[1] : '';
                        $option['description'] = array_key_exists(2, $section) ? $section[2] : '';

                        if (array_key_exists(3, $section)) {
                            $link = $section[3];
                            $linkBwcIndex = strpos($link, '#bwc/');

                            // append '#bwc/' if it does not already exist in the link
                            if ($linkBwcIndex === false) {
                                // the index where '#bwc/' should be inserted
                                $bwcIndex = strpos($link, 'index.php');

                                if ($bwcIndex !== false) {
                                    // convert '...index.php...' to '...#bwc/index.php...'
                                    $link = substr($link, 0, $bwcIndex) . '#bwc/' . substr($link, $bwcIndex);
                                }
                            }

                            $option['link'] = $link;
                        }

                        // If there is a icon defined, use it
                        // If no icon, but we have a legacy image use that
                        // If neither, use the default icon
                        if (!empty($section['icon'])) {
                            $option['icon'] = $section['icon'];
                            $option['customIcon'] = '';
                        } elseif (!empty($section[0])) {
                            $imageName = $section[0] . '.gif';
                            if ($imageURL = $this->getImageFromTheme($imageName)) {
                                $option['customIcon'] = $imageURL;
                                $option['icon'] = '';
                            } else {
                                $option['customIcon'] = '';
                                $option['icon'] = 'sicon-sugar-logo-12';
                            }
                        } else {
                            $option['icon'] = 'sicon-sugar-logo-12';
                            $option['customIcon'] = '';
                        }
                        if (!empty($section[5])) {
                            $option['onclick'] = $section[5];
                        }
                        if (!empty($section[6])) {
                            $option['target'] = $section[6];
                        }

                        array_push($newDefs['options'], $option);
                    }
                }
            }

            if ($newDefs['options']) {
                array_push($defs, $newDefs);
            }
        }

        return $defs;
    }

    /**
     * Helper function to grab the image url from the current theme
     *
     * @param $imageName
     * @return false|string
     */
    protected function getImageFromTheme($imageName)
    {
        return SugarThemeRegistry::current()->getImageURL($imageName, false, false);
    }
}
