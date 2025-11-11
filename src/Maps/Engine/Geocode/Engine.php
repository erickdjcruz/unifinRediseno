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

namespace Sugarcrm\Sugarcrm\Maps\Engine\Geocode;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\DBALException;
use Sugarcrm\Sugarcrm\Maps\Queue\Geocode\Consumer;
use Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Container;
use SugarQueryException;

class Engine
{
    /**
     * @var \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Engine
     */
    private static $instance;

    /**
     * @var \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Container
     */
    protected $container;

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * Get Geocode instance based on current system configuration.
     *
     * @return \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Engine
     * @throws \RuntimeException
     */
    public static function getInstance(): Engine
    {
        if (empty(self::$instance)) {
            self::$instance = self::create();
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function isConfigured(): bool
    {
        return $this->container->client->isConfigured();
    }

    /**
     * Get Geocode Maps service container
     * @return \Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Create new container object. Use self::getInstance unless you know
     * what you are doing.
     *
     * @return Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Engine
     */
    public static function create(): Engine
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = \SugarAutoLoader::customClass(self::class);
        return new $class();
    }

    /**
     * Wrapper to insert records to be geocoded in queue
     *
     * @param null|array $modules
     * @param null|int $batchSize
     */
    public function queueModules(?array $modules = null, ?int $batchSize = null)
    {
        $this->container->queueManager->queueModules($modules, $batchSize);
    }

    /**
     * Wrapper to get list of modules for which queued records exist
     *
     * @return array
     */
    public function getQueuedModules(): array
    {
        return $this->container->queueManager->getQueuedModules();
    }

    /**
     * Crate a consumer job
     *
     * @param string $module
     *
     * @throws SugarQueryException
     * @throws Exception
     * @throws DBALException
     */
    public function createConsumer(string $module)
    {
        $this->container->queueManager->createConsumer($module, Consumer::class);
    }

    /**
     * Consume queue for givem nodule
     *
     * @param string $module
     *
     * @return array
     */
    public function consumeModuleFromQueue(string $module)
    {
        return $this->container->queueManager->consumeModuleFromQueue($module);
    }
}
