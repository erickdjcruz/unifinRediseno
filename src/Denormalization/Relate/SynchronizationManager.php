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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use BeanFactory;
use SchedulersJob;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;
use SugarJobFieldDenormalization;
use SugarJobQueue;
use SugarQuery;

class SynchronizationManager
{
    private const JOB_NAME = self::class;
    private const TMP_TABLE_NAME = 'denorm_tmp_admin';

    public function setUpJob(Entity $entity): void
    {
        $this->removeJobIfExists();

        $options = [
            'module_name' => $entity->getTargetModuleName(),
            'field_name' => $entity->fieldName,
            'tmp_table_name' => self::TMP_TABLE_NAME,
        ];

        /* @var $job SchedulersJob */
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = self::JOB_NAME;
        $job->target = 'class::' . SugarJobFieldDenormalization::class;
        $job->data = json_encode($options);
        $job->retry_count = 0;
        $job->assigned_user_id = $GLOBALS['current_user']->id;

        $queue = new SugarJobQueue();
        $queue->submitJob($job);
    }

    public function removeJobIfExists(): void
    {
        if ($job = $this->getJob()) {
            $job->mark_deleted($job->id);
        }
    }

    public function getJob(): ?SchedulersJob
    {
        /** @var SchedulersJob $schedulerJob */
        $schedulerJob = BeanFactory::newBean('SchedulersJobs');
        $q = new SugarQuery();
        $q->from($schedulerJob)->where()->equals('name', self::JOB_NAME);
        $result = $q->execute();

        if (!empty($result[0])) {
            $schedulerJob->populateFromRow($result[0]);

            return $schedulerJob;
        }

        return null;
    }

    public function isJobInProgress(): bool
    {
        if ($job = $this->getJob()) {
            if ($job->status !== SchedulersJob::JOB_STATUS_DONE) {
                return true;
            }
        }

        return false;
    }
}
