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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Adapter;

use Elastica\Response;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\Elasticsearch\Exception\ConnectionException;
use Elastica\Client as BaseClient;
use Elastica\Connection;
use Elastica\Request;
use Psr\Log\LoggerInterface;

/**
 *
 * Adapter class for \Elastica\Client
 *
 */
class Client extends BaseClient
{
    /**
     * Administration config settings
     */
    public const STATUS_CATEGORY = 'info';
    public const STATUS_KEY = 'fts_down';

    /**
     * Connection status
     */
    public const CONN_SUCCESS = 1;
    public const CONN_ERROR = -1;
    public const CONN_VERSION_NOT_SUPPORTED = -2;
    public const CONN_NO_VERSION_AVAILABLE = -3;
    public const CONN_FAILURE = -99;

    /**
     * User-agent settings
     */
    public const USER_AGENT = 'SugarCRM';
    public const VERSION_UNKNOWN = 'unknown';

    /**
     * @var string, current installed elastic version, not the opensearch Version
     */
    protected $version;

    /**
     * @var string, current installed opensearch version
     */
    protected $openSearchVersion;

    /**
     * indicator if server is OpenSearch
     * @var bool
     */
    protected $openSearch = false;

    /**
     * flag to indicate if server has been pinged
     * @var bool
     */
    protected $pinged = false;

    /**
     * Return allowed versions array
     * @var array
     */
    protected $allowedVersions = [
        '5.4',
        '5.6',
        '6.x',
        '7.x',
        '8.x',
    ];

    /**
     * supported ES versions
     * @var array
     */
    protected static array $supportedVersions = [
        ['version' => '5.4', 'operator' => '>='],
        ['version' => '9.0', 'operator' => '<'],
    ];

    /**
     * max supported versioin of OpenSearch
     */
    const OPENSEARCH_MAX_SUPPORTED_VERSION = '{"open": ["2.7"]}';

    /**
     * OpenSearch Version
     * @var array[]
     */
    protected static array $supportedOpenSearchVersions = [
        ['version' => '1.0', 'operator' => '>='],
        ['version' => '2.8', 'operator' => '<'],
    ];

    /**
     * List of supported $sugar_config Elastic configuration options
     * @see \Elastica\Client::$_config
     */
    protected $connAllowedConfig = [
        'host',
        'port',
        'username',
        'password',
        'path',
        'transport',
        'timeout',
        'curl',
        'headers',
        'url',
        'persistent',
        'aws_access_key_id',
        'aws_secret_access_key',
        'aws_session_token',
        'aws_region',
    ];

    /**
     * @var \Sugarcrm\Sugarcrm\Elasticsearch\Logger
     */
    protected $_logger;

    /**
     * @var boolean Elasticsearch backend availability
     */
    protected $available;

    /**
     *  Search Version Header name
     */
    const SEARCH_HEADER_NAME = 'SEARCHVERSION';

    /**
     * Ctor
     * @param array $config Connection configuration from `$sugar_config`
     * @param LoggerInterface $logger
     * @param bool|null $isMts MTS flag
     */
    public function __construct(array $config, LoggerInterface $logger, ?bool $isMts = null)
    {
        $this->setLogger($logger);
        $config = $this->parseConfig($config, $isMts);
        parent::__construct($config, [$this, 'onConnectionFailure'], $logger);
    }

    /**
     * get the version of Elastic Server
     *
     * @param bool $forceRefresh to retrieve version info from server
     * @param bool $useCache to use cache
     * @return string elasticsearch version
     * @throws \Exception
     */
    public function getElasticServerVersion(bool $forceRefresh = false, bool $useCache = true): string
    {
        $cacheKey = 'elastic_server_version';
        if (!$forceRefresh && $useCache) {
            $this->version = sugar_cache_retrieve($cacheKey);
            if (!empty($this->version)) {
                return $this->version;
            }
        }

        if (empty($this->version) || $forceRefresh) {
            $result = $this->ping();
            if ($result->isOk()) {
                $data = $result->getData();
                $this->version = $this->getServerVersion($data);
                $this->pinged = true;
                if (!empty($this->version) && $useCache) {
                    sugar_cache_put($cacheKey, $this->version);
                }
            }
        }

        if (empty($this->version)) {
            $this->pinged = true;
            $this->_logger->critical('Elasticsearch: not able to get ES version');
            throw new \Exception('Elasticsearch: not able to get ES version');
        }
        return $this->version;
    }

    /**
     * get Elastic server version
     * @param array|null $data
     * @return string
     */
    protected function getServerVersion(?array $data): string
    {
        if (empty($data)) {
            return '';
        }

        if (isset($data['version']['distribution']) && $data['version']['distribution'] === 'opensearch') {
            $this->openSearch = true;
            $this->openSearchVersion = $data['version']['number'] ?? '';
            $version = $data['version']['minimum_index_compatibility_version'] ?? '';
        } else {
            $version = $data['version']['number'] ?? '';
        }

        return $version;
    }

    /**
     * Check if Elasticsearch is available. Note that the availability state
     * is based on a cached value saved in config table for 'info_fts_down'.
     * Once declared unavailable only the cron execution will be able to lift
     * it and promote the connection back to available.
     *
     * @return boolean
     */
    public function isAvailable($force = false)
    {
        // To avoid incorrectly declaring the connection down because of
        // indexing timeouts, only check and use the availability when forced
        if ($force) {
            $this->verifyConnectivity();
            return $this->loadAvailability();
        }

        // When not forced to check, assume the connection is available
        return true;
    }

    /**
     * check if this is open search
     * @return bool
     * @throws \Exception
     */
    public function isOpenSearch(): bool
    {
        if (!$this->pinged) {
            $this->getElasticServerVersion(true, false);
        }
        return $this->openSearch;
    }

    /**
     * Check the data response to determine status.
     * @param $data array the data response
     * @return string
     */
    protected function processDataResponse($data)
    {
        $this->version = $this->getServerVersion($data);
        if (empty($this->version)) {
            $status = self::CONN_NO_VERSION_AVAILABLE;
            $this->_logger->critical('Elasticsearch verify conn: No valid version string available');
        } else {
            $version = $this->version;
            if ($this->openSearch) {
                $version = $this->openSearchVersion;
            }
            if ($this->checkEsVersion($version)) {
                $status = self::CONN_SUCCESS;
            } else {
                $status = self::CONN_VERSION_NOT_SUPPORTED;
                if (!$this->openSearch) {
                    $this->_logger->critical('Elasticsearch verify conn: Unsupported Elasticsearch version: ' . $version);
                } else {
                    $this->_logger->critical('Elasticsearch verify conn: Unsupported OpenSearch version' . $version);
                }
            }
        }
        return $status;
    }

    /**
     * This call will *always* try to create a connection to the Elasticsearch
     * backend to determine its availability. This should basically only be
     * called during install/upgrade and the search admin section. The usage
     * of `$this->isAvailable` is preferred.
     *
     * @param boolean $updateAvailability , Update cached availability flag
     * @return integer Connection status, see declared CONN_ constants
     */
    public function verifyConnectivity($updateAvailability = true)
    {
        try {
            $result = $this->ping();
            if ($result->isOk()) {
                $data = $result->getData();
                if (is_array($data)) {
                    $this->_logger->info('ES Ping Response: ' . print_r($data, true));
                }
                $status = $this->processDataResponse($data);
            } else {
                $status = self::CONN_ERROR;
                $this->_logger->critical("Elasticsearch verify conn: No valid return code ({$result->getStatus()})");
            }
        } catch (\Exception $e) {
            $status = self::CONN_FAILURE;
            $this->_logger->critical('Elasticsearch verify conn: failure');
        }

        if ($updateAvailability) {
            $availability = ($status > 0) ? true : false;
            $this->updateAvailability($availability);
        }

        return $status;
    }

    /**
     * Handle connection pool failures. At this point we don't flag the backend
     * as unavailable as there may be multiple connections. In case all
     * connections from the pool are exhausted a ConnectionException will be
     * thrown further down the pipe triggering the backend as unavailable.
     *
     * Will be more useful when we support multiple connection to the backend.
     *
     * @param \Elastica\Connection $conn
     * @param \Exception $e
     * @param \Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Client $client
     */
    public function onConnectionFailure(Connection $conn, \Exception $e, Client $client)
    {
        $msg = sprintf(
            'Elasticsearch: connection went away to %s:%s',
            $conn->getHost(),
            $conn->getPort()
        );
        $this->_logger->critical($msg);
    }

    /**
     * Send generic ping to backend
     * @return \Elastica\Response
     */
    protected function ping()
    {
        return parent::request('', Request::GET);
    }

    /**
     * Verify if Elasticsearch version meets the supported list. In developer
     * mode only the minumum version applies.
     * @param string $version Elasticsearch version
     * @return boolean
     */
    protected function checkEsVersion(string $version) : bool
    {
        $result = true;
        if (!$this->openSearch) {
            // verify supported Elastic versions
            foreach (self::$supportedVersions as $check) {
                $result = $result && version_compare($version, $check['version'], $check['operator']);
            }
        } else {
            // verify OpenSearch versions
            foreach (self::$supportedOpenSearchVersions as $check) {
                $result = $result && version_compare($version, $check['version'], $check['operator']);
            }
        }
        return $result;
    }

    /**
     * Return array of allowed ES versions
     * @return array
     */
    public function getAllowedVersions()
    {
        return $this->allowedVersions;
    }

    /**
     * Update new persistent status
     * @param boolean $status True if available, false if not
     * @return boolean
     */
    protected function updateAvailability($status)
    {
        $currentStatus = $this->loadAvailability();

        if ($status !== $currentStatus) {
            $this->saveAdminStatus($status);
            $this->available = $status;
            if ($status) {
                $this->_logger->info('Elasticsearch promoted as available');
            } else {
                $this->_logger->critical('Elasticsearch no longer available');
            }
        }
        return $status;
    }

    /**
     * save status for Administration
     * @param boolean $status
     */
    protected function saveAdminStatus($status)
    {
        $admin = \BeanFactory::getBean('Administration');
        $admin->saveSetting(self::STATUS_CATEGORY, self::STATUS_KEY, ($status ? 0 : 1));
    }

    /**
     * Load the current availability
     * @return boolean
     */
    protected function loadAvailability()
    {
        if ($this->available === null) {
            $this->available = $this->isSearchEngineAvailable();
        }
        return $this->available;
    }

    /**
     * check if search engine is available using
     * Administration settings for key=info_fts_down
     * @return boolean
     */
    protected function isSearchEngineAvailable()
    {
        $settings = \Administration::getSettings();
        return empty($settings->settings['info_fts_down']);
    }

    /**
     * Build connection configuration from $sugar_config format
     * @param array $config `$sugar_config['full_text_search']`
     * @param bool|null $isMts MTS indicator
     * @return array
     */
    protected function parseConfig(array $config, ?bool $isMts = null) : array
    {
        // Currently only one connection is supported. This might be extended
        // in the future being able to use multiple connections and/or having
        // a split between search endpoints and index endpoints.
        $connection = [];
        foreach ($config as $k => $v) {
            if (in_array($k, $this->connAllowedConfig)) {
                $connection[$k] = $v;
            }
        }

        // Force the user-agent header to match SugarCRM's version
        $connection['curl'][CURLOPT_USERAGENT] = self::USER_AGENT . '/' . $this->getSugarVersion();

        // add header for MTS instance
        if (($isMts === null && isMts()) || $isMts === true) {
            $connection['headers'][self::SEARCH_HEADER_NAME] = self::OPENSEARCH_MAX_SUPPORTED_VERSION;
        }
        return ['connections' => [$connection]];
    }

    /**
     * Override request taking logging into our own hands. This will be removed
     * when the logging capabilities in Elastica are cleaned up:
     * https://github.com/ruflin/Elastica/issues/712
     * https://github.com/ruflin/Elastica/issues/482
     *
     * {@inheritdoc}
     *
     * @throws \Exception
     * @throws \Sugarcrm\Sugarcrm\Elasticsearch\Exception\ConnectionException
     */
    public function request(string $path, string $method = Request::GET, $data = [], array $query = [], string $contentType = Request::DEFAULT_CONTENT_TYPE): Response
    {
        // Enforce cached availability
        if (!$this->isAvailable()) {
            throw new \Exception('Elasticsearch not available');
        }

        try {
            $response = parent::request($path, $method, $data, $query);

            // Handle HTTP 502 Bad Gateway

            // In case a reverse proxy is sitting in between sugar and Elastic
            // it must generate an HTTP 502 in case Elastic goes down. If this
            // happens we declare the backend as unavailable as well just like
            // we do if we encouter a connection failure.
            // Note that this handling should be directly handled by Elastica
            // instead as we are escaping from the ConnectionPool here and not
            // retrying any other connections. For the current implementation
            // this is okay as SugarCRM only supports one single connection at
            // the moment.

            if ($response->getStatus() === 502) {
                throw new \Elastica\Exception\ConnectionException('HTTP 502 Bad gateway');
            }

            $this->_logger->onRequestSuccess($this->_lastRequest, $this->_lastResponse);
        } catch (\Exception $e) {
            $this->_logger->onRequestFailure($this->getConnection(), $e, $path, $method, $data);

            // On connection issues flag Elasticsearch as unavailable
            if ($e instanceof \Elastica\Exception\ConnectionException) {
                $this->updateAvailability(false);
                throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
            }

            // Let is pass
            throw $e;
        }

        return $response;
    }

    /**
     * Override logging capabilities.
     * {@inheritdoc}
     */
    protected function _log($context)
    {
        return;
    }

    /**
     * Get sugar version number, returns "unknown" if not available.
     * @return string
     */
    protected function getSugarVersion()
    {
        return empty($GLOBALS['sugar_version']) ? self::VERSION_UNKNOWN : $GLOBALS['sugar_version'];
    }
}
