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

use Sugarcrm\Sugarcrm\Dbal\Connection;

final class SugarJobEmailArchiving implements RunnableSchedulerJob, JsonSerializable
{
    private const PROCESS_CHUNK_SIZE = 100;
    private const JOB_ITERATION_DELAY = 5;

    private const SERIALIZABLE_PROPERTIES = [
        'offset',
        'count',
    ];

    private const JOB_TEMPORARY_TABLE_NAME = 'email_archiving_job';

    private SchedulersJob $job;
    private DBManager $db;
    private Email $emailBean;
    private EmailText $emailTextBean;
    private int $offset = 0;
    private ?int $count = null;

    public function __construct()
    {
        $this->db = DBManagerFactory::getInstance();
        $this->emailBean = BeanFactory::newBean('Emails');
        $this->emailTextBean = BeanFactory::newBean('EmailText');
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
        $this->doArchiving();

        return true;
    }

    private function doArchiving(): void
    {
        if (!$this->emailBean->supportsGzip()) {
            $this->setData();
            $this->succeedJob();
            return;
        }

        $this->setProgress();
        $this->job->message = sprintf('archiving next %d starting from %d', self::PROCESS_CHUNK_SIZE, $this->offset);

        // there is no need to process empty dataset
        if ($this->count === 0) {
            $this->setData();
            $this->succeedJob();
            return;
        }

        $this->ensureTemporaryTableExists();

        $this->updateJobData();

        $sql = 'SELECT email_id FROM ' . self::JOB_TEMPORARY_TABLE_NAME;
        $sql = $this->db->getConnection()->getDatabasePlatform()->modifyLimitQuery($sql, self::PROCESS_CHUNK_SIZE);
        $ids = $this->db->getConnection()->fetchFirstColumn($sql);

        foreach ($ids as $id) {
            $sql = sprintf(
                'SELECT description, description_html FROM %s WHERE email_id = ?',
                $this->emailTextBean->getTableName()
            );
            $row = $this->db->getConnection()->fetchAssociative($sql, [$id]);

            $update = [];
            foreach (['description', 'description_html'] as $fieldName) {
                if (!is_string($row[$fieldName]) || strlen($row[$fieldName]) === 0) {
                    continue;
                }
                $content = $this->emailBean->tryUngzipContent($row[$fieldName]);
                if ($content === null) {
                    $update[$fieldName] = $this->emailBean->gzipContent($row[$fieldName]);
                }
            }
            if (!empty($update)) {
                $this->db->getConnection()->update($this->emailTextBean->getTableName(), $update, ['email_id' => $id]);
            }

            $this->offset++;
        }

        if (!empty($ids)) {
            $this->db->getConnection()
                ->createQueryBuilder()
                ->delete(self::JOB_TEMPORARY_TABLE_NAME)
                ->where('email_id in (:ids)')
                ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY)
                ->executeStatement();
        }

        $this->setProgress();
        $this->updateJobData();

        if ($this->offset >= $this->count) {
            $this->setData();
            $this->succeedJob();
            $this->deleteTemporaryTable();
        } else {
            $this->job->message = "waiting " . self::JOB_ITERATION_DELAY
                . " seconds for next iteration ({$this->offset}/{$this->count}) ";
            $this->setData();
            $this->job->postponeJob(null, self::JOB_ITERATION_DELAY);
        }
    }

    private function succeedJob()
    {
        $this->job->message = "Archiving is done";
        $this->job->succeedJob();
    }

    private function ensureTemporaryTableExists(): void
    {
        if (!$this->db->tableExists(self::JOB_TEMPORARY_TABLE_NAME)) {
            $sql = sprintf('CREATE TABLE %s AS SELECT email_id FROM emails_text', self::JOB_TEMPORARY_TABLE_NAME);
            $this->db->getConnection()->executeStatement($sql);

            $sql = sprintf(
                'CREATE INDEX %s ON %s (email_id)',
                self::JOB_TEMPORARY_TABLE_NAME . '_email_id',
                self::JOB_TEMPORARY_TABLE_NAME
            );
            $this->db->getConnection()->executeStatement($sql);
        }
    }

    private function deleteTemporaryTable(): void
    {
        if ($this->db->tableExists(self::JOB_TEMPORARY_TABLE_NAME)) {
            $this->db->getConnection()->executeStatement('DROP TABLE ' . self::JOB_TEMPORARY_TABLE_NAME);
        }
    }

    private function setProgress(): void
    {
        if ($this->count > 0) {
            $this->job->percent_complete = ($this->offset / $this->count) * 100;
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
        if (!empty($data)) {
            foreach (json_decode($data, true) as $property => $value) {
                $this->{$property} = $value;
            }
        }

        if ($this->count === null) {
            $this->count = (int)$this->db->getOne('SELECT COUNT(*) FROM ' . $this->emailTextBean->getTableName());
        }
    }

    private function updateJobData(): void
    {
        $this->setData();
        $this->job->save();
    }

    public function jsonSerialize(): array
    {
        $data = [];

        foreach (self::SERIALIZABLE_PROPERTIES as $property) {
            $data[$property] = $this->{$property};
        }

        return $data;
    }
}
