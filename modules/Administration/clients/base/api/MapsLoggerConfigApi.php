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

use Sugarcrm\Sugarcrm\Maps\Constants;

/**
 * API for Maps Logger.
 */
// @codingStandardsIgnoreLine
class MapsLoggerConfigApi extends ConfigApi
{
    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getConfig' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'config', 'maps-logger'],
                'pathVars' => ['', '', ''],
                'method' => 'getConfig',
                'shortHelp' => 'Gets configuration for a category',
                'longHelp' => 'include/api/help/administration_config_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.17',
            ],
            'getMapsLog' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'maps', 'logs'],
                'pathVars' => ['', '', ''],
                'method' => 'getMapsLog',
                'shortHelp' => 'Get the maps logs',
                'longHelp' => 'include/api/help/administration_maps_logs_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.17',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(ServiceBase $api, array $args)
    {
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        $mapsConfig = parent::getConfig($api, ['category' => 'maps']);

        return $mapsConfig;
    }

    /**
     * Retrieve the maps logs
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     *
     * @return array
     */
    public function getMapsLog(ServiceBase $api, array $args)
    {
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        $this->requireArgs($args, ['modules', 'startDate', 'logLevel', 'offset']);

        $targetModules = $args['modules'];
        $startDate = $args['startDate'];
        $logLevel = $args['logLevel'];

        [$offset, $limit] = $this->getPagination($args);

        $count = $this->getMapsLogData(
            $targetModules,
            $startDate,
            $logLevel,
            null,
            null,
            true
        );

        $records = $this->getMapsLogData(
            $targetModules,
            $startDate,
            $logLevel,
            $offset,
            $limit,
            false
        );

        $nextOffset = $this->getNextOffset($limit, $offset, $count);

        return [
            'totalPages' => $this->getTotalPagesNumber($count, $limit),
            'records' => $records,
            'nextOffset' => $nextOffset,
            'totalRecords' => $count,
        ];
    }

    /**
     * getMapsLogData function
     *
     * Get the geocode logs
     *
     * @param array $modules
     * @param string $startDate
     * @param string $logLevel
     * @param integer|null $offset
     * @param integer|null $end
     * @param boolean $count
     *
     * @return mixed
     */
    private function getMapsLogData(
        array  $modules,
        string $startDate,
        string $logLevel,
        ?int   $offset,
        ?int   $limit,
        bool   $count
    ) {

        $geocodeBean = BeanFactory::newBean(Constants::GEOCODE_MODULE);

        $dt = new DateTime($startDate);
        $td = new TimeDate();
        $date = $td->asDb($dt);

        $sq = new SugarQuery();

        if ($count) {
            $sq->select->selectReset()->setCountQuery();
            $sq->limit = null;
        } else {
            $sq->select('parent_id', 'parent_type', 'parent_name', 'status', 'geocoded');

            if (!empty($geocodeBean->field_defs) && array_key_exists('error_message', $geocodeBean->field_defs)) {
                $sq->select('error_message');
            }
        }

        $sq->from($geocodeBean)
            ->where()
            ->in('parent_type', $modules)
            ->gte('date_entered', $date);

        if ($logLevel === 'error') {
            $sq->where()
                ->in('status', ['FAILED', 'NOT_FOUND']);
        }

        if ($logLevel === 'success') {
            $sq->where()
                ->equals('status', 'COMPLETED');
        }

        if (!$count) {
            $sq->orderBy('parent_type', 'ASC');
        }

        if ($offset) {
            $sq->offset($offset);
        }

        if ($limit) {
            $sq->limit($limit);
        }

        if ($count) {
            return $sq->getOne();
        }

        $result = $sq->execute();

        if (empty($result)) {
            $result = [];
        }

        return $result;
    }

    /**
     * Get Next Offset
     *
     * @param number $limit
     * @param number $offset
     * @param number $totalCount
     *
     * @return int
     */
    protected function getNextOffset($limit, $offset, $totalCount): int
    {
        $nextOffset = -1;

        if ($offset < 0) {
            $offset = 0;
        }

        $nextOffset = $offset + (int)$limit;

        if ($nextOffset >= $totalCount) {
            $nextOffset = -1;
        }

        return $nextOffset;
    }

    /**
     * Gets offset and limit for pagination.
     *
     * @param array $args The arguments array passed in from the API containing the module and the record
     *
     * @return array
     */
    protected function getPagination(array $args): array
    {
        $offset = 0;
        $limit = -1;
        $defaultLimit = 25;

        if (isset($args['offset'])) {
            $offset = (int)$args['offset'];
        }

        if ($offset < 0) {
            $offset = 0;
        }

        if (isset($args['limit']) && $args['limit'] !== '') {
            $limit = (int)$args['limit'];
        }

        if (!$limit || $limit < 1 || $limit > $defaultLimit) {
            $limit = $defaultLimit;
        }

        return [
            $offset,
            $limit,
        ];
    }

    /**
     * Get total number of pages.
     *
     * @param $args array The arguments array passed in from the API containing the module and the record
     *
     * @return array
     */
    protected function getTotalPagesNumber($totalRecords, $itemsPerPage): int
    {
        return (int)ceil($totalRecords / $itemsPerPage);
    }
}
