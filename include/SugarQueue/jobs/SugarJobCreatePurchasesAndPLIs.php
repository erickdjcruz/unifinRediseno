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

class SugarJobCreatePurchasesAndPLIs implements RunnableSchedulerJob
{
    /**
     * @var bool
     */
    public $runnable_ran;
    /**
     * @var SchedulersJob
     */
    protected $job;

    /**
     * @inheritDoc
     */
    public function setJob(SchedulersJob $job): void
    {
        $this->job = $job;
    }

    /**
     * @inheritDoc
     *
     * Running this job takes the array of RLI IDs from $data, and generates
     * Purchases/PLIs for each RLI using the processRliIds method in
     * RevenueLineItem.php
     * @param string $data The job data for this Scheduled Job instance
     * @return boolean true if job succeeded, otherwise false
     * @see RevenueLineItem
     */
    public function run($data): bool
    {
        if (!Opportunity::usingRevenueLineItems()) {
            LoggerManager::getLogger()->fatal('Current Opportunities configuration does not allow automatic creation of Purchases');
            return false;
        }

        $args = json_decode(html_entity_decode($data, ENT_COMPAT), true);
        $this->runnable_ran = true;

        // Process the chunk of RLI IDs with RLI::processRliIds method
        RevenueLineItem::processRliIds($args['data']);

        $this->job->succeedJob();
        return true;
    }
}
