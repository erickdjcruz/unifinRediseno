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

namespace Sugarcrm\Sugarcrm\Maps\Queue;

use RunnableSchedulerJob;
use SchedulersJob;
use SugarAutoLoader;

/**
 *
 * Handle the consumption of records from the database queue.
 *
 */
class Consumer implements RunnableSchedulerJob
{
    /**
     * @var \SchedulersJob
     */
    protected $job;

    /**
     * @var \Sugarcrm\Sugarcrm\Maps\Geocode\Engine
     */
    protected $engine;

    /**
     * @param String $engineClassName
     */
    public function __construct($engineClassName)
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass($engineClassName);
        $this->engine = $class::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * {@inheritdoc}
     */
    public function run($data)
    {
        // The passed in data is expected to contain the module name
        if (empty($data)) {
            return $this->job->failJob('Missing module parameter');
        }

        // Force connectivity check
        if (!$this->engine->isConfigured()) {
            $clientName = $this->engine->getContainer()->utils::CLIENT_NAME;
            $msg = sprintf('%s not available, postponing consumer job creation', $clientName);

            return $this->job->postponeJob($msg);
        }

        [$success, $processed, $duration, $errorMsg] = $this->engine->consumeModuleFromQueue($data);

        $msg = sprintf('Processed %s records in %s second(s)', $processed, $duration);

        if ($success) {
            return $this->job->succeedJob($msg);
        } else {
            $msg .= sprintf(' with error \'%s\'', $errorMsg);

            return $this->job->failJob($msg);
        }
    }
}
