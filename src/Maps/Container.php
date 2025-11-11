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

namespace Sugarcrm\Sugarcrm\Maps;

use LoggerManager;
use SugarAutoLoader;
use Sugarcrm\Sugarcrm\Maps\Queue\QueueManager;

/**
 *
 * Geocode Maps service container
 *
 * List of properties exposed through `$this->__get()`
 *
 * @property-read Logger logger
 * @property-read QueueManager queueManager
 * @property-read Client client
 * @property-read Geocoder geocoder
 * @property-read Utils utils
 *
 */
class Container
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var QueueManager
     */
    protected $queueManager;

    /**
     * @var Class $client
     */
    protected $client;

    /**
     * @var Class $utils
     */
    protected $utils;

    /**
     * @var Class $geocoder
     */
    protected $geocoder;

    /**
     * @param string $containerClassName
     * @param string $clientClassName
     * @param string $utilsClassName
     * @param string $geocoderClassName
     */
    public function __construct(
        string $containerClassName,
        string $clientClassName,
        string $utilsClassName,
        string $geocoderClassName
    ) {

        $this->initLogger();
        $this->initClient($clientClassName);
        $this->initUtils($utilsClassName);
        $this->initGeocoder($geocoderClassName);
        $this->initQueueManager($containerClassName);
    }

    /**
     * Overload for container resources
     *
     * @param string $resource
     */
    public function __get($resource)
    {
        // Return the resource if already initialized
        if (!empty($this->$resource)) {
            return $this->$resource;
        }
    }

    /**
     * Initialize Logger
     */
    protected function initLogger()
    {
        $this->logger = new Logger(LoggerManager::getLogger());
    }

    /**
     * Initialize QueueManager
     *
     * @param String $containerClassName
     */
    protected function initQueueManager(string $containerClassName)
    {
        $this->queueManager = new QueueManager($containerClassName);
    }

    /**
     * Initialize Client
     *
     * @param String $clientClassName
     */
    protected function initClient(string $clientClassName)
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass($clientClassName);
        $this->client = new $class($this->logger);
    }

    /**
     * Initialize Utils
     *
     * @param String $utilsClassName
     */
    protected function initUtils(string $utilsClassName)
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass($utilsClassName);
        $this->utils = new $class($this->logger);
    }

    /**
     * Initialize Geocoder
     *
     * @param String $geocoderClassName
     */
    protected function initGeocoder(string $geocoderClassName)
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass($geocoderClassName);
        $this->geocoder = $class::getInstance();
    }
}
