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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\ParentToSmartGuideRSA;

/**
 * SugarJobPerformRSAOnSmartGuides.php
 *
 * This class implements RunnableSchedulerJob and provides the support for
 * executing the RSA for the Automate Enabled module.
 *
 */
class SugarJobPerformRSAOnSmartGuides implements RunnableSchedulerJob
{

    /**
     * @var $job the job object
     */
    protected $job;

    /**
     * This method implements setJob from RunnableSchedulerJob and sets the SchedulersJob instance for the class
     *
     * @param SchedulersJob $job the SchedulersJob instance set by the job queue
     *
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * This method implements the run function of RunnableSchedulerJob and handles processing a SchedulersJob
     *
     * @param string $data parameter passed in from the job_queue.data column when a SchedulerJob is run
     * @return bool true on success, false on error
     */
    public function run($data)
    {
        global $timedate;
        try {
            if (!empty($data)) {
                $data = json_decode($data, true);
                if (!empty($data) && !empty($data['parent_id']) && !empty($data['parent_module'])) {
                    $parentBean = \BeanFactory::getBean($data['parent_module'], $data['parent_id']);
                    if (!empty($parentBean)) {
                        $activityRecords = ParentToSmartGuideRSA::checkAndPerformParentRSA($parentBean);
                        if (!empty($activityRecords)) {
                            $activitiesChunk = array_chunk($activityRecords, 50);
                            foreach ($activitiesChunk as $activities) {
                                $activityRecordsData = [];
                                $job = $this->getSchedulersJobs();
                                $job->name = 'Perform RSA On Activity Records: ' . $timedate->getNow();
                                $job->target = 'class::SugarJobPerformRSAOnActivityRecords';
                                $activityRecordsData['activities'] = $activities;
                                $job->data = json_encode($activityRecordsData);
                                $job->retry_count = 0;
                                $jobQueue = new \SugarJobQueue();
                                $jobQueue->submitJob($job);
                            }
                        }
                    }
                }
            }

            $this->job->succeedJob();

            return true;
        } catch (Exception $e) {
            $this->job->failJob($e->getMessage());
            return false;
        }
    }

    /**
     * Gets a new instance of the SchedulersJobs bean
     *
     * @return null|SugarBean
     */
    private function getSchedulersJobs()
    {
        return \BeanFactory::newBean('SchedulersJobs');
    }
}
