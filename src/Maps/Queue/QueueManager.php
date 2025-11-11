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

namespace Sugarcrm\Sugarcrm\Maps\Queue;

use BeanFactory;
use DBManagerFactory;
use Exception;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use SchedulersJob;
use SugarAutoLoader;
use SugarBean;
use Sugarcrm\Sugarcrm\Maps\Constants;
use SugarJobQueue;
use SugarQuery;
use Sugarcrm\Sugarcrm\Maps\FilterUtils as MapsFilterUtils;

/**
 *
 * Queue Manager
 *
 */
class QueueManager
{
    /**
     * @var \Sugarcrm\Sugarcrm\Maps\Container
     */
    protected $container;

    /**
     *
     * @var string
     */
    protected $containerClassName;

    /**
     * @var \DBManager
     */
    protected $db;

    /**
     * Maximum amount of records we retrieve from database queue
     * @var integer
     */
    protected $maxBulkQueryThreshold = 1000;

    /**
     * Maximum amount of records we will queue in database
     * @var integer
     */
    protected $maxQueueRecords = 2000;

    /**
     * number of beans to batch retrieve related data
     * @var integer
     */
    protected $numOfRecordsToRetrieveFromDBInBatch = 5;

    /**
     * Maximum amount of records we cleanup from database queue as
     * @var integer
     */
    protected $maxBulkDeleteThreshold = 5;

    /**
     * Grace time when postponing consumer jobs
     * @var integer
     */
    protected $postponeJobTime = 120;

    /**
     * In memory queue for processed queue record ids
     *
     * @var array
     */
    protected $deleteFromQueue = [];

    /**
     *  Lazy load for container class
     *
     * @param String $containerClassName
     */
    public function __construct(string $containerClassName)
    {
        $this->containerClassName = $containerClassName;
        $this->db = DBManagerFactory::getInstance();
    }

    /**
     * Create consumer job for given module
     *
     * @param string $module
     * @param string $consumerClass
     */
    public function createConsumer(string $module, string $consumerClass)
    {
        $clientName = $this->getContainer()->utils::CLIENT_NAME;

        $jobClass = SugarAutoLoader::customClass($consumerClass);
        $jobExec = "class::\\{$jobClass}";

        $job = $this->getNewBean('SchedulersJobs');

        foreach ([SchedulersJob::JOB_STATUS_QUEUED, SchedulersJob::JOB_STATUS_RUNNING] as $status) {
            $sq = new SugarQuery();
            $sq->select('id');
            $sq->from($job)->where()
                ->equals('target', $jobExec)
                ->starts('data', $module)
                ->equals('status', $status);
            $sq->limit(1);

            $result = $job->fetchFromQuery($sq, ['id']);

            if (!empty($result)) {
                $this->getLogger()->info(sprintf('%s consumer for %s already present', $clientName, $module));
                return;
            }
        }

        // No job is found for this module, let's create one.
        $job->name = sprintf('%s Queue Consumer', $clientName);
        $job->target = $jobExec;
        $job->data = $module;
        $job->job_delay = $this->postponeJobTime;
        $job->assigned_user_id = $GLOBALS['current_user']->id;

        $this->submitNewJob($job);

        $this->getLogger()->info(sprintf('Create %s consumer for %s', $clientName, $module));
    }

    /**
     * Queue records for given module(s).
     *
     * @param null|array $modules
     * @param null|int $batchSize
     */
    public function queueModules(?array $modules = null, ?int $batchSize = null)
    {
        if (!is_array($modules)) {
            $modules = $this->getContainer()->client->getEnabledModules();
        }

        if (!$batchSize) {
            $batchSize = $this->maxQueueRecords;
        }

        foreach ($modules as $module) {
            if ($this->canAddModuleToQueue($module)) {
                $this->insertModuleToQueue($module, $batchSize);
            }
        }
    }

    /**
     * Consume records from database queue for given module
     *
     * @param string $module
     *
     * @return array
     */
    public function consumeModuleFromQueue(string $module): array
    {
        $start = time();
        $errorMsg = '';
        $success = true;
        $data = $this->getModuleRecordsFromQueue($module);

        $geocodeBeans = [];
        $targetBeans = [];
        $mapsIds = [];
        $queuedRecordsToBeDeleted = [];

        foreach ($data as $row) {
            $geocodeBean = BeanFactory::retrieveBean(Constants::GEOCODE_MODULE, $row['geocode_id']);
            $targetBean = BeanFactory::retrieveBean($module, $row['target_id']);

            if (is_null($geocodeBean) || is_null($targetBean)) {
                $queuedRecordsToBeDeleted[] = $row['maps_id'];

                continue;
            }

            $mapsIds[$geocodeBean->id] = $row['maps_id'];

            $geocodeBeans[$geocodeBean->id] = $geocodeBean;
            $targetBeans[$targetBean->id] = $targetBean;
        }

        if (!empty($queuedRecordsToBeDeleted)) {
            $this->deleteQueuedGeocodeRecords($queuedRecordsToBeDeleted);
        }

        $this->geocodeBeans($targetBeans, $geocodeBeans, $mapsIds, $module);

        if (!empty($this->deleteFromQueue)) {
            $this->flushDeleteFromQueue($module);
        }

        $duration = time() - $start;

        return [$success, $this->maxBulkQueryThreshold, $duration, $errorMsg];
    }

    /**
     * Delete a bucket of queued geocode records
     *
     * @param string $id
     */
    private function deleteQueuedGeocodeRecords(array $ids)
    {
        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $whereCondition = $queryBuilder->expr()->in(
            'id',
            $queryBuilder->createPositionalParameter(
                (array)$ids,
                \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
            )
        );

        $queryBuilder
            ->delete($this->getContainer()->utils::QUEUE_TABLE)
            ->where($whereCondition);

        $queryBuilder->execute();
    }

    /**
     * Get a list of modules for which records are queued
     *
     * @return array
     */
    public function getQueuedModules(): array
    {
        $modules = [];

        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $queryBuilder
            ->select(['bean_module'])
            ->distinct()
            ->from($this->getContainer()->utils::QUEUE_TABLE);

        $queryBuilderResult = $queryBuilder->execute();

        if (!$queryBuilderResult) {
            return [];
        }

        while ($row = $queryBuilderResult->fetchAssociative()) {
            $modules[] = $row['bean_module'];
        }

        return $modules;
    }

    protected function geocodeBeans(array $targeBeans, array $gecodeBeans, array $mapsIds, string $module)
    {
        $this->getContainer()->geocoder->geocodeBeans($targeBeans, $gecodeBeans);
        $this->batchDeleteFromQueue($mapsIds, $module);
    }

    /**
     * Geocode the bean
     *
     * @param SugarBean $targetBean
     * @param SugarBean $geocodeBean
     * @param string $mapsId
     */
    protected function geocodeBean(SugarBean $targetBean, SugarBean $geocodeBean, string $mapsId)
    {
        $this->getContainer()->geocoder->geocodeBean($targetBean, $geocodeBean);
        $this->batchDeleteFromQueue([$mapsId], $targetBean->getModuleName());
    }

    /**
     * Submit job into job queue
     *
     * @param SchedulersJob $job
     */
    protected function submitNewJob(SchedulersJob $job)
    {
        $queue = new SugarJobQueue();
        $queue->submitJob($job);
    }

    /**
     * Helper function to create new sugar beans
     *
     * @param string $module
     *
     * @return SugarBean
     */
    protected function getNewBean(string $module): SugarBean
    {
        return BeanFactory::newBean($module);
    }

    /**
     * Generate SQL query to insert records into the queue for givem nodule
     *
     * @param string $module
     * @param integer $batchSize
     */
    protected function insertModuleToQueue(string $module, ?int $batchSize = null)
    {
        $geocodeSeed = $this->getNewBean(Constants::GEOCODE_MODULE);
        $seed = $this->getNewBean($module);
        $collectedGeocodeRecords = [];

        $targetTableName = $seed->table_name;
        $geocodeTableName = $geocodeSeed->table_name;

        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $expr = $queryBuilder->expr();
        $whereCondition = $expr->andX();
        $orExprStatus = $expr->orX();
        $orExprGeocoded = $expr->orX();
        $andStatus = $expr->andX();

        $whereCondition->add(
            $expr->eq(
                "{$targetTableName}.deleted",
                $queryBuilder->createPositionalParameter(0)
            )
        );

        $andStatus->add(
            $expr->neq(
                "{$geocodeTableName}.status",
                $queryBuilder->createPositionalParameter(Constants::GEOCODE_SCHEDULER_STATUS_QUEUED)
            )
        );

        $andStatus->add(
            $expr->neq(
                "{$geocodeTableName}.status",
                $queryBuilder->createPositionalParameter(Constants::GEOCODE_SCHEDULER_STATUS_NOT_FOUND)
            )
        );

        $orExprStatus->add(
            $expr->isNull(
                "{$geocodeTableName}.status"
            )
        );

        $orExprStatus->add($andStatus);

        $whereCondition->add($orExprStatus);

        $orExprGeocoded->add(
            $expr->eq(
                "{$geocodeTableName}.geocoded",
                $queryBuilder->createPositionalParameter(0)
            )
        );

        $orExprGeocoded->add(
            $expr->isNull(
                "{$geocodeTableName}.geocoded"
            )
        );

        $whereCondition->add($orExprGeocoded);

        $guid = $this->db->getGuidSQL();
        $date = $this->db->now();
        $quotedModuleName = $this->db->quoted($module);

        $queryBuilder
            ->select([
                "{$guid} id",
                "{$targetTableName}.id bean_id",
                "{$quotedModuleName} bean_module",
                "{$date} date_modified",
                "{$date} date_created",
                "{$geocodeTableName}.geocoded geocoded",
                "{$geocodeTableName}.id geocode_id",
                "{$geocodeTableName}.status status",
            ])
            ->from($targetTableName)
            ->leftJoin(
                $targetTableName,
                $geocodeTableName,
                $geocodeTableName,
                $geocodeTableName . '.parent_id=' . $targetTableName . '.id'
            )
            ->where($whereCondition);

        if ($batchSize && $batchSize > 0) {
            $queryBuilder->setMaxResults($batchSize);
        }

        $queryBuilderResult = $queryBuilder->execute();

        if (!$queryBuilderResult) {
            return [];
        }

        $queueBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $queueBuilder->insert($this->getContainer()->utils::QUEUE_TABLE);

        $insertQueueFields = [
            'id',
            'bean_id',
            'bean_module',
            'date_modified',
            'date_created',
            'geocode_id',
        ];

        foreach ($queryBuilderResult->iterateAssociative() as $record) {
            if (is_null($record['geocoded'])) {
                $this->createGeocodeBean($record);

                // we have no id for the empty addresses
                if (array_key_exists('geocode_id', $record) && $record['geocode_id']) {
                    $collectedGeocodeRecords[] = $record['geocode_id'];
                }
            } elseif ($record['status'] === Constants::GEOCODE_SCHEDULER_STATUS_REQUEUE) {
                $collectedGeocodeRecords[] = $record['geocode_id'];
            }

            // we have an invalid address
            // so we have to prevent it from being sent to the server
            if ($record['status'] === Constants::GEOCODE_SCHEDULER_STATUS_NOT_FOUND) {
                continue;
            }

            foreach ($insertQueueFields as $index => $column) {
                $queueBuilder
                    ->setValue($column, '?')
                    ->setParameter($index, $record[$column]);
            }

            $queueBuilder->execute();
        }

        if (safeCount($collectedGeocodeRecords) > 0) {
            $this->markSentRecordsToGeocode($collectedGeocodeRecords);
        }
    }

    /**
     * Mark as queued those records that are already sent to GCS for geocoding
     *
     * @param mixed $records
     *
     * @throws Exception
     * @throws DoctrineDBALException
     */
    private function markSentRecordsToGeocode($records)
    {
        $geocodeSeed = $this->getNewBean(Constants::GEOCODE_MODULE);

        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($geocodeSeed->table_name)
            ->set('status', $qb->createPositionalParameter(Constants::GEOCODE_SCHEDULER_STATUS_QUEUED));

        $qb->where($qb->expr()->in(
            'id',
            $qb->createPositionalParameter($records, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
        ));

        $qb->execute();

        MapsFilterUtils::updateGeocodeStatuses($records, Constants::GEOCODE_SCHEDULER_STATUS_QUEUED);
    }

    /**
     * Create a geocode record
     *
     * @param array $record
     *
     * @return string
     */
    protected function createGeocodeBean(array &$record): string
    {
        $targetRecordId = $record['bean_id'];
        $targetRecordModule = $record['bean_module'];
        $address = [];

        $targetBean = BeanFactory::retrieveBean(
            $targetRecordModule,
            $targetRecordId,
            [
                'disable_row_level_security' => true,
            ]
        );

        $geocodeBean = $this->getNewBean(Constants::GEOCODE_MODULE);

        $mappingTable = $this->getContainer()->geocoder->getGeocodingMapping($targetRecordModule);

        foreach ($mappingTable as $clientKey => $sugarKey) {
            $value = null;

            if ($targetBean !== null && $sugarKey && $targetBean->getFieldDefinition($sugarKey)) {
                $value = $targetBean->{$sugarKey};
            }

            if ($value) {
                $address[$clientKey] = $value;
            }
        }

        $isEmptyAddress = false;

        $rawAddress = implode('', $address);

        if (strlen($rawAddress) < Constants::MIN_CHARS_NR_FOR_VALID_ADDRESS) {
            $isEmptyAddress = true;
        }

        $addressString = implode(', ', $address);

        $geocodeBean->parent_id = $targetRecordId;
        $geocodeBean->parent_type = $targetRecordModule;
        $geocodeBean->parent_name = $targetBean->name;
        $geocodeBean->parent_user_name = $targetBean->assigned_user_name;
        $geocodeBean->address = $addressString;
        $geocodeBean->geocoded = 0;

        //if address is empty
        if ($isEmptyAddress === true) {
            $geocodeBean->geocoded = 1;
            $geocodeBean->status = Constants::GEOCODE_SCHEDULER_STATUS_NOT_FOUND;

            $record['status'] = Constants::GEOCODE_SCHEDULER_STATUS_NOT_FOUND;
        }

        $geocodeId = $geocodeBean->save();

        // add record to be queued for geocoding process
        if ($isEmptyAddress === false) {
            $record['geocode_id'] = $geocodeId;
        }

        return $geocodeId;
    }

    /**
     * Check if there are no records of a given module in the geocoding queue
     *
     * @param String $module
     *
     * @return bool
     *
     * @throws Exception
     * @throws DBALException
     */
    protected function canAddModuleToQueue(string $module): bool
    {
        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $expr = $queryBuilder->expr();
        $whereCondition = $expr->andX();

        $whereCondition->add(
            $expr->eq(
                'bean_module',
                $queryBuilder->createPositionalParameter($module)
            )
        );

        $queryBuilder
            ->select(['count(id) records'])
            ->from($this->getContainer()->utils::QUEUE_TABLE)
            ->where($whereCondition);

        $queryBuilderResult = $queryBuilder->execute();

        if (!$queryBuilderResult) {
            return false;
        }

        $result = $queryBuilderResult->fetchAssociative();

        return $result && array_key_exists('records', $result) && $result['records'] < 1;
    }

    /**
     * Generate SQL query
     *
     * @param string $module
     *
     * @return array
     */
    protected function getModuleRecordsFromQueue(string $module): array
    {
        $data = [];

        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $whereCondition = $queryBuilder->expr()->eq('bean_module', $queryBuilder->createPositionalParameter($module));

        $queryBuilder
            ->select(['id maps_id', 'bean_id target_id', 'geocode_id geocode_id'])
            ->from($this->getContainer()->utils::QUEUE_TABLE)
            ->where($whereCondition)
            ->setMaxResults($this->maxBulkQueryThreshold);

        $queryBuilderResult = $queryBuilder->execute();

        if ($queryBuilderResult) {
            $data = $queryBuilderResult->fetchAll();
        }

        return $data;
    }

    /**
     * Processes and geocodes a batch of beans
     *
     * @param array $targetBeans
     * @param array $geocodeBeans
     * @param array $mapsIds
     *
     * @return int
     */
    protected function batchGeocodeBeans(array $targetBeans, array $geocodeBeans, array $mapsIds): int
    {
        $count = safeCount($geocodeBeans);
        $start = 0;
        $processed = 0;

        // break beans into groups for batch processing
        while ($count > 0) {
            $slicedBeans = array_slice($geocodeBeans, $start, $this->numOfRecordsToRetrieveFromDBInBatch, true);

            if (empty($slicedBeans)) {
                break;
            }

            $count -= $this->numOfRecordsToRetrieveFromDBInBatch;
            $start += $this->numOfRecordsToRetrieveFromDBInBatch;

            foreach ($slicedBeans as $geocodeBean) {
                $targetBeanId = $geocodeBean->parent_id;
                $targetBean = $targetBeans[$targetBeanId];

                $this->geocodeBean($targetBean, $geocodeBean, $mapsIds[$geocodeBean->id]);
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Batch given record id to be removed from queue and flush queue
     * when necessary.
     *
     * @param array $ids bean ids
     * @param string $module bean module
     */
    protected function batchDeleteFromQueue(array $ids, string $module)
    {
        $this->deleteFromQueue = array_merge($this->deleteFromQueue, $ids);

        if (safeCount($this->deleteFromQueue) >= $this->maxBulkDeleteThreshold) {
            $this->flushDeleteFromQueue($module);
        }
    }

    /**
     * Flush records from queue tracked in `$this->deleteFromQueue`
     *
     * @param sring $module
     */
    protected function flushDeleteFromQueue(string $module)
    {
        $targetTable = $this->getContainer()->utils::QUEUE_TABLE;

        $queryBuilder = DBManagerFactory::getConnection()->createQueryBuilder();

        $expr = $queryBuilder->expr();
        $whereCondition = $expr->andX();

        $whereCondition->add(
            $expr->eq(
                'bean_module',
                $queryBuilder->createPositionalParameter($module)
            )
        );

        $whereCondition->add(
            $expr->in(
                'id',
                $queryBuilder->createPositionalParameter($this->deleteFromQueue, Connection::PARAM_STR_ARRAY)
            )
        );

        $queryBuilder
            ->delete($targetTable)
            ->where($whereCondition);

        $queryBuilder->execute();

        $this->deleteFromQueue = [];
    }

    /**
     *  Lazy load of container
     *
     * @return Class
     */
    protected function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->containerClassName::getInstance();
        }

        return $this->container;
    }

    /**
     * Get logger object
     *
     * @return Class
     */
    protected function getLogger()
    {
        return $this->getContainer()->logger;
    }
}
