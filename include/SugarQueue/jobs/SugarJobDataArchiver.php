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

use Doctrine\DBAL\Exception\DriverException;

class SugarJobDataArchiver implements RunnableSchedulerJob
{
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
     */
    public function run($data): bool
    {
        // Grab all rows from the data_archivers table that is marked as active
        // Only grab fields we need
        $sq = new SugarQuery();
        $sq->from(BeanFactory::getBean('DataArchiver'))
            ->select(['id', 'filter_module_name', 'filter_def', 'process_type']);
        $sq->where()->equals('active', 1);

        $results = $sq->execute();

        if (safeCount($results) > 0) {
            // Create a mock api arg
            $api = new RestService();
            $api->user = (BeanFactory::newBean('Users'))->getSystemUser();

            // Go through each result row returned and run the archive
            $numSuccessfulArchives = 0;
            foreach ($results as $result) {
                // Create the args array necessary for the api call
                $args['record'] = $result['id'];
                $args['module'] = 'DataArchiver';

                // Create an instance of the DataArchiverAPI
                $dataArchiverAPI = new DataArchiverApi();

                try {
                    // Perform the process and return the ids
                    $dataArchiverAPI->performArchive($api, $args);

                    // update the number of successful archives
                    $numSuccessfulArchives++;
                } catch (DriverException $e) {
                    // Job fails when API throws this exception
                    $this->job->failJob("Unable to perform archive/delete definition. {$numSuccessfulArchives} 
                    successful archives before failure");

                    return false;
                }
            }
            $successMessage = 'Data successfully archived/deleted';
        } else {
            $successMessage = 'No active archive/deletion definitions to perform';
        }

        // Job Success
        $this->job->succeedJob($successMessage);
        return true;
    }
}
