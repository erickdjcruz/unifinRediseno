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
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use Sugarcrm\Sugarcrm\SystemProcessLock\SystemProcessLock;

/**
 * SugarJobLicenseInstaller
 *
 * Apply license changes to users on timely basis
 */
class SugarJobLicenseInstaller implements RunnableSchedulerJob
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * @var SchedulersJob
     */
    protected $job;

    public function __construct()
    {
        $this->logger = Container::getInstance()->get(\Psr\Log\LoggerInterface::class);
    }

    public function setJob(SchedulersJob $job): void
    {
        $this->job = $job;
    }

    /**
     * @return bool
     */
    public function run($data)
    {
        try {
            if (SubscriptionManager::instance()->applyDownloadedLicense()) {
                $this->logger->info('License entitlements have been updated');
            }
            $this->job->succeedJob();
        } catch (\Exception $e) {
            $this->job->failJob($e->getMessage());
        }
    }
}
