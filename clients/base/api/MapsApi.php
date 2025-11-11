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

use Sugarcrm\Sugarcrm\Maps\FilterUtils as MapsFilterUtils;
use Sugarcrm\Sugarcrm\Maps\MapsGenerator;

/**
 * API for Maps List.
 */
// @codingStandardsIgnoreLine
class MapsApi extends FilterApi
{
    /**
     * @inheritDoc
     */
    public function registerApiRest()
    {
        $mapsEndpointsDefs = $this->getMapsEndpointsDefs();
        $parentApiEndpoints = parent::registerApiRest();

        $mapsEndpoints = [];
        $mapPath = ['maps'];
        $emptyPath = [''];

        $endpointDef = $parentApiEndpoints['filterModuleAll'];

        array_splice($endpointDef['path'], 1, 0, $mapPath);
        array_splice($endpointDef['pathVars'], 1, 0, $emptyPath);

        $mapsEndpoints['filterModuleAll'] = $endpointDef;

        $endpoints = array_merge($mapsEndpoints, $mapsEndpointsDefs);

        return $endpoints;
    }

    /**
     * Get custom maps endpoinds
     *
     * @return array
     */
    private function getMapsEndpointsDefs(): array
    {
        $keyEndpointDef = [
            'reqType' => 'GET',
            'path' => ['maps', 'getApiKey', '?'],
            'pathVars' => ['', '', 'provider'],
            'method' => 'getApiKey',
            'shortHelp' => 'Get Map Api Key',
            'longHelp' => 'include/api/help/maps_getapikey_get_help.html',
            'minVersion' => '11.16',
        ];

        $generateMapEndpointDef = [
            'reqType' => 'POST',
            'path' => ['maps', 'generateMap'],
            'pathVars' => [''],
            'method' => 'generateMap',
            'shortHelp' => 'Generate Map as Document',
            'longHelp' => 'include/api/help/maps_generate_pdf_post_help.html',
            'minVersion' => '11.16',
        ];

        $updateGeocodeStatusEndpointDef = [
            'reqType' => 'POST',
            'path' => ['maps', 'updateGeocodeStatus'],
            'pathVars' => [''],
            'method' => 'updateGeocodeStatus',
            'shortHelp' => 'Update the geocode status of the parent record',
            'longHelp' => 'include/api/help/maps_update_geocode_status_post_help.html',
            'minVersion' => '11.17',
        ];

        $mapsNearbyEndpointDef = [
            'reqType' => 'GET',
            'path' => ['maps', 'nearby'],
            'pathVars' => [''],
            'method' => 'getNearBy',
            'shortHelp' => 'Get nearby records based on latitude, longitude and radius',
            'longHelp' => 'include/api/help/maps_near_by_get_help.html',
            'minVersion' => '11.18',
        ];

        return [
            'getApiKey' => $keyEndpointDef,
            'generateMaps' => $generateMapEndpointDef,
            'updateGeocodeStatus' => $updateGeocodeStatusEndpointDef,
            'mapsNearby' => $mapsNearbyEndpointDef,
        ];
    }

    /**
     * Get Api Key
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return string
     */
    public function getApiKey(ServiceBase $api, array $args): string
    {
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        $this->requireArgs($args, ['provider']);
        //todo get from config?
        $provider = $args['provider'];

        if ($provider === 'bing') {
            $configurator = new Configurator();
            $configurator->loadConfig();

            $key = $configurator->config['map_key']['bing'];

            return $key;
        }

        throw new SugarApiExceptionError("{$provider} is not a valid provider.");
    }

    /**
     * Update the geocode status of the parent record
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return boolean
     */
    public function updateGeocodeStatus(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['id', 'module', 'status', 'fieldName']);

        $targetBean = BeanFactory::retrieveBean($args['module'], $args['id']);
        if (empty($targetBean)) {
            throw new SugarApiExceptionNotFound();
        }
        if (!isset($targetBean->field_defs[$args['fieldName']]) ||
            $targetBean->field_defs[$args['fieldName']]['type'] !== 'geocodestatus'
        ) {
            throw new SugarApiExceptionError();
        }
        $targetBean->{$args['fieldName']} = $args['status'];
        $targetBean->save();

        return true;
    }

    /**
     * Generate Map as Document
     *
     * @param ServiceBase $api
     * @param array $args
     */
    public function generateMap(ServiceBase $api, array $args)
    {
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        $this->requireArgs($args, [
            'mapMeta',
            'provider',
            'recordsMeta',
        ]);

        $mapMeta = $args['mapMeta'];
        $provider = $args['provider'];
        $recordsMeta = $args['recordsMeta'];

        $providerLicenseKey = $this->getApiKey($api, ['provider' => $provider]);

        $mapsGenerator = new MapsGenerator($provider, $providerLicenseKey);
        return $mapsGenerator->generatePdfMap($recordsMeta, $mapMeta);
    }


    /**
     * @inheritDoc
     */
    protected static function addMapsDistanceFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter)
    {
        if (array_key_exists('$in_radius_from_record', $filter)) {
            self::applyMapsDistanceRecordFilter($q, $where, $filter);
        }
    }

    /**
     * Add a Maps Distance Filter by Record
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $filter
     */
    protected static function applyMapsDistanceRecordFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter)
    {
        $filterData = $filter['$in_radius_from_record'];

        $unitType = $filterData['unitType'];
        $radius = $filterData['radius'];
        $recordId = $filterData['recordId'];
        $recordModule = $filterData['recordModule'];
        $fields = $filterData['requiredFields'];
        $distanceFilter = true;
        $moduleName = $q->getFromBean()->module_name;

        $admin = BeanFactory::getBean('Administration');
        $mapsConfig = $admin->retrieveSettings('maps', true)->settings;

        if (!$mapsConfig['maps_modulesData']) {
            $q->select->fieldRaw('0', 'maps_distance');
            $q->whereRaw('1 = 0');

            return;
        }

        $mapsModuleData = $mapsConfig['maps_modulesData'][$moduleName];

        $validData = self::getValidStartRecordData($mapsConfig, $recordModule, $recordId);
        $recordId = $validData['id'];
        $recordModule = $validData['module'];

        $coords = MapsFilterUtils::getDbCoordsFromRecord($recordModule, $recordId);

        if ($coords === false) {
            $q->select->fieldRaw('0', 'maps_distance');
            $q->whereRaw('1 = 0');

            return;
        }

        $recordTable = $q->getFromBean()->getTableName();
        $geocodeTable = MapsFilterUtils::getCoordsTableName();

        if ($mapsModuleData && $mapsModuleData['mappingType'] === 'relateRecord') {
            $recordTable = self::addMapsRelatedJoins($q, $mapsModuleData, $fields);

            self::addMapsExtraFields($q, $mapsModuleData, $fields, $recordTable);
        } else {
            self::addMapsExtraFields($q, $mapsModuleData, $fields);
        }


        $join = $q->joinTable($geocodeTable, [
            'joinType' => 'LEFT',
        ]);

        $join->on()->equalsField("{$geocodeTable}.parent_id", "{$recordTable}.id");

        $distanceSql = self::addMapsWhereClause(
            $q,
            [
                'radius' => $radius,
                'unitType' => $unitType,
                'coords' => $coords,
                'geocodeTable' => $geocodeTable,
                'recordId' => $recordId,
                'recordTable' => $recordTable,
                'distanceFilter' => $distanceFilter,
            ]
        );

        $q->select->fieldRaw($distanceSql, 'maps_distance');
    }

    /**
     * Add maps subpanel fields
     *
     * @param SugarQuery $q
     * @param array|null $recordModule
     * @param array|null $recordId
     * @param string $targetTableAlias
     */
    private static function addMapsExtraFields(SugarQuery $q, $mapsModuleData, $fields, $targetTableAlias = '')
    {
        if ($mapsModuleData && $mapsModuleData['mappingType'] === 'relateRecord') {
            $mappingRecord = $mapsModuleData['mappingRecord'];
            $relatedKey = array_keys($mappingRecord)[0];
            $relatedModule = $mappingRecord[$relatedKey]['module'];

            self::addMapsSelectFields($q, $fields, $relatedModule, $targetTableAlias);
        } else {
            self::addMapsSelectFields($q, $fields);
        }
    }

    /**
     * Returns either current record or related record data
     *
     * @param array $mapsConfig
     * @param string $recordModule
     * @param string $recordId
     */
    private static function getValidStartRecordData(array $mapsConfig, string $recordModule, string $recordId)
    {
        $mapsModuleData = $mapsConfig['maps_modulesData'][$recordModule];

        if ($mapsModuleData && $mapsModuleData['mappingType'] === 'relateRecord') {
            $targetRecordBean = BeanFactory::getBean($recordModule, $recordId);
            $relatedKey = array_keys($mapsModuleData['mappingRecord'])[0];

            $targetRecordBean->load_relationship($relatedKey);

            $relatedLink = $targetRecordBean->{$relatedKey};

            if ($relatedLink) {
                $relatedRecords = $relatedLink->getBeans();
                $hasParent = safeCount($relatedRecords) > 0;

                $recordId = $hasParent ? array_keys($relatedRecords)[0] : $recordId;
                $recordModule = $hasParent ? $relatedRecords[$recordId]->module_name : $recordModule;
            }
        }

        return ['id' => $recordId, 'module' => $recordModule];
    }

    /**
     * Add maps fields into query select
     *
     * @param SugarQuery $q
     * @param array $fields
     * @param string $targetModule
     * @param string $targetTableAlias
     */
    private static function addMapsSelectFields(
        SugarQuery $q,
        array      $fields,
        string     $targetModule = '',
        string     $targetTableAlias = ''
    ) {

        global $db;

        $module = $targetModule ?: $q->getFromBean()->getModuleName();
        $table = $targetTableAlias ?: $q->getFromAlias();
        $seed = BeanFactory::newBean($module);

        $fieldsMapping = self::getMapsFieldsMapping($fields, $module);

        foreach ($fieldsMapping as $mapsFieldName => $moduleFieldName) {
            if ($moduleFieldName) {
                $def = $seed->field_defs[$moduleFieldName];
                $isCustomField = !empty($def['source']) && $def['source'] === 'custom_fields';
                $targetTable = $isCustomField ? "{$table}_cstm" : $table;

                $q->select->fieldRaw("{$targetTable}.{$moduleFieldName}", $mapsFieldName);
            } else {
                $q->select->fieldRaw($db->quoted(''), $mapsFieldName);
            }
        }
    }

    /**
     * Get nearby records based on latitude, longitude and radius
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     */
    public function getNearBy(ServiceBase $api, array $args): array
    {
        $result = [];
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        $this->requireArgs($args, ['radius', 'latitude', 'longitude']);

        $coords = [
            'latitude' => $args['latitude'],
            'longitude' => $args['longitude'],
        ];

        $radius = $args['radius'];
        $unitType = $args['unitType'];

        $administration = BeanFactory::getBean('Administration');

        if (empty($unitType)) {
            if (!empty($administration->settings['maps_unitType'])) {
                $unitType = $administration->settings['maps_unitType'];
            }
        }

        $q = new SugarQuery();

        $geocodeTable = MapsFilterUtils::getCoordsTableName();

        $q->from(\BeanFactory::newBean('Geocode'), ['team_security' => false]);
        $q->select(['parent_id', 'parent_name', 'parent_type']);

        $distanceSql = self::addMapsWhereClause(
            $q,
            [
                'radius' => $radius,
                'unitType' => $unitType,
                'coords' => $coords,
                'geocodeTable' => $geocodeTable,
            ]
        );

        $q->select->fieldRaw($distanceSql, 'maps_distance');

        $result['records'] = $q->execute();

        return $result;
    }

    /**
     * Add maps fields into query clause
     *
     * @param SugarQuery $q
     * @param array $distanceData
     */
    private static function addMapsWhereClause(SugarQuery $q, array $distanceData): string
    {
        $radius = null;
        $unitType = null;
        $coords = [];
        $recordId = null;
        $geocodeTable = null;
        $distanceFilter = null;
        $recordTable = null;
        global $db;

        if (array_key_exists('radius', $distanceData)) {
            $radius = $distanceData['radius'];
        }

        if (array_key_exists('unitType', $distanceData)) {
            $unitType = $distanceData['unitType'];
        }

        if (array_key_exists('coords', $distanceData)) {
            $coords = $distanceData['coords'];
        }

        if (array_key_exists('geocodeTable', $distanceData)) {
            $geocodeTable = $distanceData['geocodeTable'];
        }

        if (array_key_exists('recordId', $distanceData)) {
            $recordId = $distanceData['recordId'];
        }

        if (array_key_exists('recordTable', $distanceData)) {
            $recordTable = $distanceData['recordTable'];
        }

        if (array_key_exists('distanceFilter', $distanceData)) {
            $distanceFilter = $distanceData['distanceFilter'];
        }

        $earthRadiusKm = 6371;
        $pi = pi();
        $recordIdQuoted = $db->quote($recordId);

        $latitude = (float)$coords['latitude'];
        $longitude = (float)$coords['longitude'];
        $radius = (float)$radius;

        if (is_string($unitType) && strtolower($unitType) === 'miles') {
            //convert miles to km
            $radius = $radius * 1.60934;
        }

        // SQRT in SQL is part of the standard ANSI SQL-92 so is safety to use it in RAW SQL
        // COS is in the base functions of supported database by SUGARCRM: MySQL, DB2, Oracle and SQL Server
        // so, it's safe to use them as a raw query.
        // ASIN is present in all of the supported database by SUGARCRM: MySQL, DB2, Oracle and SQL Server
        // Discover the available locations in the given radius
        $distanceSql = "(
            2 * (
                ASIN(
                    SQRT(
                        POWER(
                            SIN(
                                (($geocodeTable.latitude * $pi / 180) - ($latitude * $pi / 180)) / 2
                            )
                            ,2
                        )
                        +
                        COS($latitude * $pi / 180)
                        *
                        COS($geocodeTable.latitude * $pi / 180)
                        *
                        POWER(
                            SIN(
                                (($geocodeTable.longitude * $pi / 180) - ($longitude * $pi / 180)) / 2
                            )
                            ,2
                        )
                    )
                )
            )
        ) * $earthRadiusKm";

        $whereClause = "$distanceSql < $radius";

        if ($distanceFilter) {
            $whereClause .= " AND {$recordTable}.id <> '{$recordIdQuoted}'";
        }

        if ($radius) {
            $q->whereRaw($whereClause);
        } else {
            $q->whereRaw("{$recordTable}.id <> '{$recordIdQuoted}'");
        }

        return $distanceSql;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery(ServiceBase $api, array $args, SugarQuery $q, array $options, SugarBean $seed = null)
    {
        $seed->call_custom_logic('before_filter', [$q, $options]);

        if (empty($args['fields'])) {
            $fields = [];
        } else {
            $fields = $options['select'];
        }

        $queryOptions = [
            'returnRawRows' => true,
            'compensateDistinct' => true,
        ];

        if (isset($options['id_query'])) {
            $orderForMap = $this->getMapsOrderBy($args);

            try {
                $ids = $options['id_query']
                    ->orderByReset()
                    ->orderByRaw($orderForMap[0], $orderForMap[1])
                    ->compile()
                    ->execute()
                    ->fetchFirstColumn();
            } catch (\Throwable $e) {
                $errorMessage = sprintf('Maps failed retrieving geocoded records: %s', $e->getMessage());
                $GLOBALS['log']->fatal($errorMessage);

                return [
                    'records' => [],
                    'next_offset' => -1,
                ];
            }

            if (safeCount($ids) < 1) {
                return [
                    'records' => [],
                    'next_offset' => -1,
                ];
            }

            $q->where()
                ->in('id', $ids);

            $q->orderByReset()
                ->orderByRaw($orderForMap[0], $orderForMap[1]);

            $q->offset(null);
            $q->limit(null);

            $queryOptions['skipFixQuery'] = true;
        }

        if (!empty($options['skipFixQuery'])) {
            $queryOptions['skipFixQuery'] = true;
        }

        $fetched = $seed->fetchFromQuery($q, $fields, $queryOptions);

        [$beans, $rows, $distinctCompensation] = $this->parseQueryResults($fetched);

        $data = [];
        $data['next_offset'] = -1;

        // Get the related bean options to be able to handle related collections, like
        // in tags. Do this early, before beans in the collection are mutated
        $rcOptions = $this->getRelatedCollectionOptions($beans, $fields);
        $rcBeans = $this->runRelateCollectionQuery($beans, $rcOptions);

        // 'Cause last_viewed_date is an alias (not a real field), we need to
        // temporarily store its values and append it later to each recently
        // viewed record
        $lastViewedDates = [];
        $db = DBManagerFactory::getInstance();

        $i = $distinctCompensation;
        foreach ($beans as $beanId => $bean) {
            if ($i == $options['limit']) {
                if (safeCount($beans) > $options['limit']) {
                    unset($beans[$beanId]);
                }
                $data['next_offset'] = (int)($options['limit'] + $options['offset']);
                continue;
            }
            $i++;

            if (isset($rows[$beanId]['last_viewed_date'])) {
                $lastViewedDates[$beanId] = $db->fromConvert($rows[$beanId]['last_viewed_date'], 'datetime');
            }

            $this->populateRelatedFields($bean, $rows[$beanId]);
        }

        if (!empty($options['relate_collections'])) {
            // If there is no module set in the options array set the options
            // module to the args module
            if (!isset($options['module'])) {
                $options['module'] = $args['module'];
            }

            // Put all relate collection beans together so that parent beans and
            // relate beans all have a chance to load their relate collections
            // from memory
            $options['rc_beans'] = array_merge($this->runRelateCollectionQuery($beans, $options), $rcBeans);
        }

        $data['records'] = $this->formatBeans($api, $args, $beans, $options);
        $data['records'] = $this->addMapsFields($data, $args, $rows);

        if (!empty($lastViewedDates) && !empty($data['records'])) {
            global $timedate;

            // Append _last_viewed_date to each recently viewed record
            foreach ($data['records'] as &$record) {
                if (isset($lastViewedDates[$record['id']])) {
                    $record['_last_viewed_date'] = $timedate->asIso($timedate->fromDb($lastViewedDates[$record['id']]));
                }
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function parseArguments(ServiceBase $api, array $args, SugarBean $seed = null)
    {
        if (array_key_exists('fields', $args)) {
            $fields = $args['fields'];

            if (!is_array($fields) && strlen($args['fields']) > 0) {
                $fields = explode(',', $fields);
            }

            foreach ($fields as $field) {
                if (!array_key_exists($field, $seed->field_defs)) {
                    $seed->field_defs[$field] = [
                        'name' => $field,
                        'type' => 'text',
                        'source' => 'non-db',
                    ];
                }
            }
        }

        return parent::parseArguments($api, $args, $seed);
    }

    /**
     * Get formatted order by for maps
     *
     * @param array $args
     */
    protected function getMapsOrderBy(array $args)
    {
        $orderBy = $args['order_by'];
        $converted = ['id', 'asc'];

        if (!isset($orderBy) || !is_string($orderBy)) {
            return $converted;
        }

        $converted = explode(':', $orderBy);

        return $converted;
    }

    /**
     * Add maps fields
     *
     * @param array $data
     * @param array $args
     * @param array $rows
     *
     * @return array
     */
    private function addMapsFields(array $data, array $args, array $rows): array
    {
        $module = $args['module'];
        $fields = $args['fields'];

        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }

        $fieldsMapping = self::getMapsFieldsMapping($fields, $module);

        $fieldsMapping['maps_distance'] = 'maps_distance';

        foreach ($data['records'] as &$record) {
            $recordId = $record['id'];

            foreach ($fieldsMapping as $mapFieldName => $moduleFieldName) {
                $record[$mapFieldName] = $rows[$recordId][$mapFieldName];
            }

            $record['maps_distance'] = $this->convertToSelectedUnitType(floatval($record['maps_distance']), $args);

            $record['maps_distance'] = number_format($record['maps_distance'], 2);
        }

        return $data['records'];
    }

    /**
     * Convert current value to km/miles
     *
     * @param float $filter
     * @param array $filter
     *
     * @return float
     */
    private function convertToSelectedUnitType(float $mapDistance, array $args): float
    {
        $unitType = $this->getUnitType($args['filter']);

        if (!$unitType) {
            return $mapDistance;
        }

        if (strtolower($unitType) === 'miles') {
            //convert km to miles
            $mapDistance = $mapDistance * 0.62137;
        }

        return $mapDistance;
    }

    /**
     * @param array $filter
     *
     * @return mixed
     */
    private function getUnitType(array $filter)
    {
        if (array_key_exists('unitType', $filter)) {
            return $filter['unitType'];
        }

        foreach ($filter as $value) {
            if (is_array($value)) {
                $unitType = $this->getUnitType($value);

                if (is_string($unitType)) {
                    return $unitType;
                }
            }
        }

        return false;
    }
}
