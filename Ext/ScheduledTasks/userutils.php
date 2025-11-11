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
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

use Sugarcrm\Sugarcrm\UserUtils\Invoker\Invoker;

if (!class_exists('UserUtilitiesJob')) {
    class UserUtilitiesJob implements RunnableSchedulerJob
    {
        /**
         * the job
         *
         * @var SchedulersJob
         */
        protected $job;

        /**
         * Set job
         *
         * @param SchedulersJob $job
         */
        public function setJob(SchedulersJob $job)
        {
            $this->job = $job;
        }

        /**
         * Job execution function
         *
         * @param array $data
         * @return true
         */
        public function run($data)
        {
            $data = $this->getJobData($data);
            $invoker = new Invoker([$data]);
            $newCommands = [];
            foreach ($invoker->getCommands() as $command) {
                $manager = $command->getManager();
                $manager->dontUseScheduledJob();
                $command->setManager($manager);
                $newCommands[] = $command;
            }

            $invoker->setCommands($newCommands);
            $invoker->execute();

            return true;
        }

        /**
         * @param $data
         * @return mixed
         */
        protected function getJobData($data)
        {
            $this->job->runnable_ran = true;
            $this->job->runnable_data = $data;
            return unserialize(base64_decode($this->job->runnable_data), ['allowed_classes' => false]);
        }
    }
}
