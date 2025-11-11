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

require_once 'modules/ActivityStream/Activities/ActivityStreamCleaner.php';

$job_strings[] = 'class::ActivityStreamPurgerJob';

/**
 * SugarJobActivityStreamPurger.php
 *
 * This class implements RunnableSchedulerJob and provides the support for purging old activity stream
 * records.
 *
 */
class SugarJobActivityStreamPurger implements \RunnableSchedulerJob
{
    protected $job;

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
        $acs = new ActivityStreamCleaner();

        // Delete all activity records that have been soft-deleted (deleted = 1)
        $acs->purgeSoftDeletedRecords();

        // Delete all the old activity records (< default_months_to_keep)
        $acs->purgeOldActivitiesRecords();

        $this->job->succeedJob();
        return true;
    }
}
