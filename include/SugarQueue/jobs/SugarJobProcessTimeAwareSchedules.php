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
 * Class SugarJobProcessTimeAwareSchedules is used to process items in the
 * Time-Aware Schedules table
 */
class SugarJobProcessTimeAwareSchedules implements RunnableSchedulerJob
{
    /**
     * @var int maximum number of schedules to process in a single run
     */
    public static $MAX_BATCH_SIZE = 100;

    /**
     * @var SchedulersJob used by RunnableSchedulerJob
     */
    protected $job;

    /**
     * This method implements setJob from RunnableSchedulerJob. It sets the
     * SchedulersJob instance for the class.
     *
     * @param SchedulersJob $job the SchedulersJob instance set by the job queue
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * Runs the job to process time aware schedules
     *
     * @param $data array of job data
     */
    public function run($data)
    {
        // Process the next batch of schedules in the Time-Aware Schedules table
        $schedulesToProcess = $this->retrieveExpiredSchedules();
        $schedulesProcessed = $this->processSchedules($schedulesToProcess);
        $this->deleteProcessedSchedules($schedulesProcessed);

        // Mark the job as finished
        $this->job->succeedJob();
        return true;
    }

    /**
     * Queries the time aware schedules table to get a list of rows that are
     * ready to be processed
     *
     * @return mixed[] a list of schedules to process
     * @throws Exception
     */
    protected function retrieveExpiredSchedules()
    {
        // Check if the max batch size has been overridden by config
        global $sugar_config;
        $maxBatchSize = $sugar_config['time_aware_job_max_batch_size'] ?? self::$MAX_BATCH_SIZE;

        // Get the next {maxBatchSize} schedules to process
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder()
            ->select('*')
            ->from('time_aware_schedules')
            ->orderBy('next_run');
        $qb->where($qb->expr()->lte('next_run', $qb->createPositionalParameter(TimeDate::getInstance()->nowDb())))
            ->andWhere($qb->expr()->eq('deleted', $qb->createPositionalParameter('0')));
        $qb->setMaxResults($maxBatchSize);
        $stmt = $qb->execute();
        return $stmt->fetchAllAssociative();
    }

    /**
     * Processes a list of schedules
     *
     * @param $schedulesToProcess array the list of schedules to process
     * @return array containing results of the processing for each schedule
     */
    protected function processSchedules($schedulesToProcess)
    {
        $schedulesProcessed = [];
        foreach ($schedulesToProcess as $schedule) {
            try {
                $this->processSchedule($schedule);
            } catch (Exception $e) {
                $GLOBALS['log']->error(
                    'Error processing time_aware_schedules row with ID ' . $schedule['id'] . ': ' . $e->getMessage()
                );
                continue;
            }
            $schedulesProcessed[] = $schedule['id'];
        }
        return $schedulesProcessed;
    }

    /**
     * Processes a single schedule
     *
     * @param $scheduleToProcess array the schedule to process
     */
    protected function processSchedule($scheduleToProcess)
    {
        // Take the appropriate action based on the time-aware schedule's type
        switch ($scheduleToProcess['type']) {
            case 'recalculation':
                $this->processRecalculationSchedule($scheduleToProcess);
                break;
            default:
                break;
        }
    }

    /**
     * Processes a record recalculation schedule, which simply loads the bean
     * and resaves it to update calculated field values in the database
     *
     * @param $schedule array of schedule data
     */
    protected function processRecalculationSchedule($schedule)
    {
        if (!empty($schedule['module']) && !empty($schedule['bean_id'])) {
            $beanToUpdate = BeanFactory::retrieveBean($schedule['module'], $schedule['bean_id']);
            if (!empty($beanToUpdate)) {
                $beanToUpdate->save();
            }
        }
    }

    /**
     * Soft deletes processed Time-Aware Schedules
     *
     * @param $schedulesProcessed array of IDs in the time aware schedules table to delete
     * @throws Exception
     */
    protected function deleteProcessedSchedules($schedulesProcessed)
    {
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder()
            ->update('time_aware_schedules')
            ->set('deleted', '1');
        $qb->where($qb->expr()->in(
            'id',
            $qb->createPositionalParameter($schedulesProcessed, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
        ));
        $qb->execute();
    }
}
