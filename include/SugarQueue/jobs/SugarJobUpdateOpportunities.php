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


/**
 * SugarJobUpdateOpportunities.php
 *
 * Class to run a job which should upgrade every old opp with commit stage, date_closed_timestamp,
 * best/worst cases and related product
 */
class SugarJobUpdateOpportunities extends JobNotification implements RunnableSchedulerJob
{
    /**
     * @var SchedulersJob
     */
    protected $job;


    /**
     * The Label that will be used for the subject line
     *
     * @var string
     */
    protected $subjectLabel = 'LBL_JOB_NOTIFICATION_OPP_FORECAST_SYNC_SUBJECT';

    /**
     * The Label that will be used for the body of the notification and email
     *
     * @var string
     */
    protected $bodyLabel = 'LBL_JOB_NOTIFICATION_OPP_FORECAST_SYNC_BODY';

    /**
     * Include the help link
     *
     * @var bool
     */
    protected $includeHelpLink = true;

    /**
     * What module is the help link for
     *
     * @var string
     */
    protected $helpModule = 'Opportunities';

    /**
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * @param $data
     * @return bool
     */
    public function run($data)
    {
        $this->job->runnable_ran = true;
        $this->job->runnable_data = $data;

        $data = json_decode($data, true);

        Activity::disable();
        $ftsSearch = \Sugarcrm\Sugarcrm\SearchEngine\SearchEngine::getInstance();
        $ftsSearch->setForceAsyncIndex(true);

        foreach ($data as $row) {
            $id = !empty($row['id']) ? $row['id'] : $row['opportunity_id'];

            /* @var $opp Opportunity */
            $opp = BeanFactory::getBean('Opportunities', $id);
            $opp->save(false);
        }

        $ftsSearch->setForceAsyncIndex(
            SugarConfig::getInstance()->get('search_engine.force_async_index', false)
        );
        Activity::restoreToPreviousState();

        $this->job->succeedJob();
        $this->notifyAssignedUser();
        return true;
    }

    /**
     * This function creates a job for to run the SugarJobUpdateOpportunities class
     * @param integer $perJob
     * @return array|string An array of the jobs that were created, unless there
     * is one, then just that job's id
     * @throws SugarQueryException
     */
    public static function updateOpportunitiesForForecasting($perJob = 100)
    {
        $sq = new SugarQuery();
        $sq->select(['id']);
        $sq->from(BeanFactory::newBean('Opportunities'));
        $sq->orderBy('date_closed');

        $rows = $sq->execute();

        return self::doUpdateOppsForForecasting($rows, $perJob);
    }


    /**
     * Creates jobs to run the SugarJobUpdateOpportunities class, using the RLIs to get a list of
     * all Opps that need to be updated
     * @param int $perJob
     * @return array|string An array of the jobs that were created, unless there
     * is one, then just that job's id
     * @throws SugarQueryException
     */
    public static function updateRliOppsForForecasting($perJob = 100)
    {
        $q = new SugarQuery();
        $q->select(['opportunity_id']);
        $q->distinct(true);
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->where()->isNotEmpty('commit_stage');

        $rows = $q->execute();

        return self::doUpdateOppsForForecasting($rows, $perJob, true, false);
    }


    /**
     * Does the work of queueing up the jobs for updating Opps for forecasting
     * @param $rows
     * @param int $perJob
     * @param bool $delay
     * @param bool $runImmediately
     * @return array|bool
     */
    private static function doUpdateOppsForForecasting($rows, $perJob = 100, $delay = false, $runImmediately = true)
    {
        if (empty($rows)) {
            return false;
        }

        $chunks = array_chunk($rows, $perJob);
        $job_group = md5(microtime());

        $jobs = [];
        // process the first job now
        $job = static::createJob($chunks[0], $runImmediately, $runImmediately ? null : $job_group, $delay);

        if ($runImmediately) {
            $jobs[] = $job->id;

            // run the first job
            $self = new self();
            $self->setJob($job);
            $self->sendNotifications = false;
            $self->run($job->data);
        } else {
            $jobs[] = $job;
        }

        for ($i = 1; $i < safeCount($chunks); $i++) {
            $jobs[] = static::createJob($chunks[$i], false, $job_group, $delay);
        }

        // if only one job was created, just return that id
        if (safeCount($jobs) == 1) {
            return array_shift($jobs);
        }

        return $jobs;
    }

    /**
     * @param array $data The data for the Job
     * @param bool $returnJob When `true` the job will be returned, otherwise the job id will be returned
     * @param string|null $job_group The Group that this job belongs to
     * @param bool $delay if true, add a delay to the execute time of the job
     * @return SchedulersJob|String
     */
    public static function createJob(array $data, $returnJob = false, $job_group = null, $delay = false)
    {
        global $current_user;

        /* @var $job SchedulersJob */
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = 'Update Old Opportunities';
        $job->target = 'class::SugarJobUpdateOpportunities';
        $job->data = json_encode($data);
        $job->retry_count = 0;
        $job->assigned_user_id = $current_user->id;
        if (!is_null($job_group)) {
            $job->job_group = $job_group;
        }
        if ($delay) {
            $timeDate = TimeDate::getInstance();
            $job->execute_time = $timeDate->getNow()->modify('+1 second')->asDb();
        }
        $job_queue = new SugarJobQueue();
        $job_queue->submitJob($job);

        if ($returnJob === true) {
            return $job;
        }

        return $job->id;
    }
}
