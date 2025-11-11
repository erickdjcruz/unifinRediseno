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

use Sugarcrm\Sugarcrm\Denormalization\Relate\Process;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;

final class SugarJobFieldDenormalization implements RunnableSchedulerJob, JsonSerializable
{
    private const COPY_ITERATION_COUNT = 50;
    private const COPY_CHUNK_SIZE = 10000;
    private const JOB_ITERATION_DELAY = 5;
    // job internal status
    private const STATUS_ALTER = 1;
    private const STATUS_PREPARE = 2;
    private const STATUS_COPY = 3;

    private const SERIALIZABLE_PROPERTIES = [
        'module_name',
        'field_name',
        'chunk_offset',
        'count',
        'status',
        'tmp_table_name',
    ];

    /**
     * @var SchedulersJob
     */
    private $job;

    /** @var DBManager */
    private $db;

    /** @var Process */
    private $process;

    /** @var string */
    private $module_name;

    /** @var string */
    private $field_name;

    /** @var string */
    private $tmp_table_name;

    /** @var int */
    private $chunk_offset;

    /** @var int */
    private $count;

    /** @var int */
    private $status;

    /** @var Entity */
    private $processEntity;

    public function __construct()
    {
        $this->db = DBManagerFactory::getInstance();
    }

    /**
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * @param string $data The job data set for this particular Scheduled Job instance
     * @return boolean true if the run succeeded; false otherwise
     */
    public function run($data)
    {
        $this->initialize($data);

        switch ($this->status) {
            case self::STATUS_COPY:
                $this->doCopy();
                break;

            case self::STATUS_PREPARE:
                $this->doPrepare();
                break;

            case self::STATUS_ALTER:
                $this->doAlter();
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * Alter table and postpone job
     */
    private function doAlter(): void
    {
        $this->process->alterTable($this->processEntity);

        $this->setStatus(self::STATUS_PREPARE);
        $this->job->postponeJob(null, self::JOB_ITERATION_DELAY);
    }

    /**
     * Create tmp table, turn on logic hooks, populate tmp table and postpone job
     */
    private function doPrepare(): void
    {
        $this->process->prepareForCopy($this->processEntity);

        $this->setStatus(self::STATUS_COPY);
        $this->job->postponeJob(null, self::JOB_ITERATION_DELAY);
    }

    private function doCopy(): void
    {
        if (!isset($this->chunk_offset)) {
            $this->initCopyProcess();
        }

        $this->setProgress();
        $this->job->message = sprintf('copying next %d starting from %d', self::COPY_CHUNK_SIZE, $this->chunk_offset);

        // there is no need to process empty dataset
        if ($this->count === 0) {
            $this->setData();
            $this->succeedJob();
            return;
        }

        $this->updateJobData();

        $lastChunkProcessed = false;
        for ($i = 0; $i < self::COPY_ITERATION_COUNT; $i++) {
            $this->process->migrateTemporaryTableChunk(
                $this->processEntity,
                $this->chunk_offset,
                self::COPY_CHUNK_SIZE
            );

            $this->chunk_offset += self::COPY_CHUNK_SIZE;

            if ($this->chunk_offset >= $this->count) {
                $lastChunkProcessed = true;
                break;
            }

            $this->setProgress();
            $this->updateJobData();
        }

        if ($lastChunkProcessed) {
            $this->setData();
            $this->succeedJob();
        } else {
            $this->job->message = 'waiting ' . self::JOB_ITERATION_DELAY
                . " seconds for next iteration ({$this->chunk_offset}/{$this->count}) ";
            $this->setData();
            $this->job->postponeJob(null, self::JOB_ITERATION_DELAY);
        }
    }

    private function succeedJob()
    {
        $this->process->onDataSetCopied($this->processEntity);
        $this->job->message = 'Sync is done';
        $this->job->succeedJob();
    }

    private function initCopyProcess(): void
    {
        $this->count = $this->process->getTemporaryTableCount();

        $this->chunk_offset = 0;
    }

    private function setProgress(): void
    {
        if ($this->count > 0) {
            $this->job->percent_complete = ($this->chunk_offset / $this->count) * 100;
            $this->job->percent_complete = $this->job->percent_complete > 100 ? 100 : $this->job->percent_complete;
        } else {
            $this->job->percent_complete = 100;
        }
    }

    private function setData(): void
    {
        $this->job->data = json_encode($this);
    }

    private function initialize($data): void
    {
        foreach (json_decode($data, true) as $property => $value) {
            $this->{$property} = $value;
        }

        $bean = BeanFactory::getBean($this->module_name);
        $this->processEntity = new Entity($bean, $this->field_name);
        $this->process = new Process();

        if ($this->tmp_table_name) {
            $this->process->setTemporaryTableName($this->tmp_table_name);
        }

        if (!$this->status) {
            if (!$this->process->isAltered($this->processEntity)) {
                $this->setStatus(self::STATUS_ALTER);
            } else {
                $this->setStatus(self::STATUS_PREPARE);
            }
        }
    }

    private function updateJobData(): void
    {
        $this->setData();
        $this->job->save();
    }

    private function setStatus(int $status): void
    {
        $this->status = $status;
        $this->updateJobData();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];

        foreach (self::SERIALIZABLE_PROPERTIES as $property) {
            $data[$property] = $this->{$property};
        }

        return $data;
    }
}
