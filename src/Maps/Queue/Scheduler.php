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
 * Persistent scheduler which is responsible to create subsequent jobs based
 * on what needs to be consumed from the database queue.
 *
 */
class Scheduler implements RunnableSchedulerJob
{
    /**
     * @var SchedulersJob
     */
    protected $job;

    /**
     * @var Sugarcrm\Sugarcrm\Maps\Engine
     */
    protected $engine;

    /**
     * @param String $engineClassName
     */
    public function __construct(string $engineClassName)
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
        if (!hasSystemMapsLicense()) {
            return $this->job->failJob(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        if (!$this->engine->isConfigured()) {
            $clientName = $this->engine->getContainer()->utils::CLIENT_NAME;

            return $this->job->failJob(sprintf('%s not available, postponing consumer job creation', $clientName));
        }

        $this->engine->queueModules();

        // Create consumer jobs
        $list = [];

        foreach ($this->engine->getQueuedModules() as $module) {
            $this->engine->createConsumer($module);

            $list[] = $module;
        }

        if (!empty($list)) {
            $message = 'Created consumers for: ' . implode(', ', $list);
        } else {
            $message = 'No records currently in queue - nothing to do';
        }

        return $this->job->succeedJob($message);
    }
}
