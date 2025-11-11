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

use Sugarcrm\Sugarcrm\Maps\Logger;

use BeanFactory;
use InvalidArgumentException;
use Sugarcrm\Sugarcrm\Maps\GCSClient;

class Geocode
{
    /**
     * @var \Sugarcrm\Sugarcrm\Maps\Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * getEnabledModules function
     *
     * @return array
     */
    public function getEnabledModules(): array
    {
        $admin = BeanFactory::getBean('Administration');
        $mapsSettings = $admin->retrieveSettings('maps', true)->settings;

        $availableModules = $mapsSettings['maps_enabled_modules'];

        return $availableModules;
    }

    /**
     * Send data to GCS service
     *
     * @param array $data
     */
    public function sendRecordsToGCS(array $data)
    {
        $gcsClient = new GCSClient();
        $gcsClient->createBatch($data);
    }

    /**
     * Replace the existing logger.
     *
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Check if Geocode Maps configured
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function isConfigured(): bool
    {
        return true;
    }
}
