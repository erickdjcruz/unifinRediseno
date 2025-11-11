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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

class SugarJobOpportunitiesCurrencyRateBatchUpdate implements RunnableSchedulerJob
{
    /**
     * @var SchedulersJob $job
     */
    protected $job;

    /**
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * @param string $data parameter passed in from the job_queue.data column when a SchedulerJob is run
     * @return bool true on success, false on error
     */
    public function run($data)
    {
        $data = json_decode($data, true);
        if (!is_array($data)) {
            // data must be array of arrays
            $this->job->failJob('invalid query data');
            return false;
        }
        /* @var $db DBManager */
        $db = DBManagerFactory::getInstance();
        $conn = $db->getConnection();
        $sql = <<<SQL
UPDATE opportunities
SET amount = ? * base_rate,
    best_case = ? * base_rate,
    worst_case = ? * base_rate
WHERE id = ?
  AND sales_status NOT IN (?)
SQL;
        foreach ($data as $params) {
            try {
                $conn->executeUpdate(
                    $sql,
                    $params,
                    [null, null, null, null, Connection::PARAM_STR_ARRAY]
                );
            } catch (DBALException $e) {
                $this->job->failJob($e->getMessage());
                return false;
            }
        }
        $this->job->succeedJob();
        return true;
    }
}
