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

require_once 'modules/CJ_Forms/clients/base/api/CJ_FormsApi.php';

/**
 * SugarJobPerformRSAOnActivityRecords.php
 *
 * This class implements RunnableSchedulerJob and provides the support for
 * executing the RSA for the Automate Enabled module.
 *
 */
class SugarJobPerformRSAOnActivityRecords implements RunnableSchedulerJob
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
        try {
            $api = new RestService();
            $api->user = $GLOBALS['current_user'];
            if (!empty($data)) {
                $data = json_decode($data, true);
                if (!empty($data) && !empty($data['activities'])) {
                    $activities = $data['activities'];
                    if (!empty($activities)) {
                        $cjFormsAPI = new CJ_FormsApi();
                        $activities = $cjFormsAPI->performTargetActions($api, ['records_to_update' => $activities]);
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
}
