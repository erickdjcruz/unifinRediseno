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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sugarcrm\Sugarcrm\DbArchiver\DbArchiver;

class DataArchiverApi extends ModuleApi
{
    /**
     * @var DbArchiver
     */
    private $archiver;

    /**
     * Array that contains required filter fields for specific modules
     * @var string[][]
     */
    protected $moduleRequirements = [
        'pmse_Inbox' => [
            'cas_status',
        ],
    ];

    /**
     * Rest Api Registration Method
     *
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'run' => [
                'reqType' => 'POST',
                'path' => ['DataArchiver', '?', 'run'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'performArchive',
                'shortHelp' => 'Performs the archiving process for the given archive',
                'longHelp' => 'include/api/help/archiver_run.html',
            ],
        ];
    }

    /**
     * Defines the api method for performing the archiving process
     * @param ServiceBase $api
     * @param array $args
     * @return bool
     * @throws SugarQueryException
     * @throws DriverException
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionInvalidParameter
     */
    public function performArchive(ServiceBase $api, array $args)
    {
        $criteria = [];
        // Only allow admins to make this api call
        if (!$api->user->isAdmin()) {
            throw new SugarApiExceptionNotAuthorized();
        }

        $bean = $this->loadBean($api, $args);

        $module = $bean->filter_module_name;

        $type = DataArchiver::getProcessType($bean->process_type);

        $criteria['filter'] = json_decode($bean->filter_def, true);
        $criteria['module'] = $module;

        $this->verifyModuleRequirements($module, $criteria['filter']);

        $where = $this->applyCriteria($criteria);
        $this->archiver = new DbArchiver($module);

        try {
            $returnedIds = $this->archiver->performProcess($where, $type);
            return $this->createArchiveRunAfterProcess($bean, $returnedIds);
        } catch (DriverException $e) {
            $this->archiver->removeArchivedRows();
            throw $e;
        }
    }

    /**
     * Create an entry in the archive_runs database table
     * @param $args
     * @param $returnedIds
     * @return string
     * @throws SugarApiException
     */
    private function createArchiveRunAfterProcess($bean, $returnedIds)
    {
        // Create and store the new bean in the database
        $archiveRunsBean = BeanFactory::newBean('ArchiveRuns');
        $archiveRunsBean->archiver_id = $bean->id;
        $archiveRunsBean->process_type = $bean->process_type;
        $archiveRunsBean->source_module = $bean->filter_module_name;
        $archiveRunsBean->filter_def = $bean->filter_def;
        $archiveRunsBean->date_of_archive = (new TimeDate())->nowDb();
        $archiveRunsBean->num_processed = safeCount($returnedIds);
        $archiveRunsBean->ids_processed = json_encode($returnedIds);
        $archiveRunsBean->save();

        // Make sure the relationship is handled correctly
        $archiverBean = BeanFactory::retrieveBean('DataArchiver', $bean->id);
        if (!$archiverBean || !$archiverBean->load_relationship('archive_runs')) {
            throw new SugarApiException('Cannot create Archive Run relationship');
        }
        $archiverBean->archive_runs->add($archiveRunsBean);

        return $archiveRunsBean->toArray();
    }

    /**
     * Applies filtering criteria for the archiving process
     * @param $criteria
     * @return SugarQuery_Builder_Andwhere|SugarQuery_Builder_Where|null
     * @throws SugarApiExceptionInvalidParameter|SugarQueryException
     */
    private function applyCriteria($criteria)
    {
        // Use extended filterApi to access special method unique to DataArchiver
        $filterApi = new \DataArchiverFilterApi();
        return $filterApi->convertFiltersToWhere($criteria['filter'], $criteria['module']);
    }

    /**
     * Verifies module specific requirements defined
     * @param $module
     * @param $filter
     * @throws SugarApiExceptionInvalidParameter
     */
    private function verifyModuleRequirements($module, $filter)
    {
        // Throw an error if an archival process is attempted on a module that has requirements that are unsatisfied as a filter
        // Throws at first instance of error
        if (isset($this->moduleRequirements[$module])) {
            $filterKeys = [];
            foreach ($filter as $f) {
                $filterKeys[] = array_keys($f)[0];
            }

            foreach ($this->moduleRequirements[$module] as $req) {
                if (!array_key_exists($req, array_flip($filterKeys))) {
                    throw new SugarApiExceptionInvalidParameter($req, null, null, 0, 'ModuleReqError');
                }
            }
        }
    }
}
