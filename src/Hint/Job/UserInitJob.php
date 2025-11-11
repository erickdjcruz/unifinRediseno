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

namespace Sugarcrm\Sugarcrm\Hint\Job;

use Sugarcrm\Sugarcrm\Hint\Logger\Logger;
use Sugarcrm\Sugarcrm\Hint\Queue\QueueTrait;
use Sugarcrm\Sugarcrm\Hint\HintConstants;

class UserInitJob implements \RunnableSchedulerJob
{
    use QueueTrait;

    /**
     * Job name
     */
    public const NAME = 'Hint User Init Job';

    // Max number of user ids per job
    public const ID_CHUNK_SIZE = 100;

    /**
     * @var \SchedulersJob
     */
    protected $job;

    /**
     * {@inheritdoc}
     */
    public function setJob(\SchedulersJob $job)
    {
        $this->eventQueue = $this->getEventQueue();
        $this->job = $job;
        //REMIND: Set global logger here.
    }

    /**
     * {@inheritdoc}
     */
    public function run($data)
    {
        $processed = null;
        $data = json_decode($data, true);
        $logger = new Logger();
        $users = $this->getUsers($data['ids']);
        if (!$users) {
            $logger->alert('No users data found');
            return $this->job->failJob('Invalid data');
        }

        if (!hasSystemHintLicense()) {
            return $this->job->succeedJob(translate('LBL_HINT_NO_LICENSE_ACCESS'));
        }

        // Only the final UserInitJob that runs on package installation will contain the completion
        // event. So, when it is present in the job data, we know it's time to send the completion
        // event.
        if ($data['completionEventData']) {
            $logger->info($data['completionEventData']['log']);
            $this->eventQueue->recordEvent(
                HintConstants::getCompletionEvent($data['completionEventData']['event'])
            );
        }

        $successJob = $this->job->succeedJob(sprintf('Processed users: %s', implode(', ', $processed)));
        return $successJob;
    }

    /**
     * Parses job data and returns an array of user ids
     *
     * @param $data
     * @return \User[]
     * @throws \SugarQueryException
     */
    private function getUsers($ids)
    {
        $logger = new Logger();
        if (!$ids || !is_array($ids)) {
            return [];
        }
        try {
            $seed = \BeanFactory::newBean('Users');
            $query = new \SugarQuery();
            $query->from($seed)->where()->in('id', $ids);
            return $seed->fetchFromQuery($query, ['id']);
        } catch (\Throwable $e) {
            $logger->alert('Hint: Sugar Query with the given ID failed.');
            throw new \SugarQueryException($e->getMessage());
        }
    }
}
