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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\Denormalization\Relate\FieldConfig;
use Sugarcrm\Sugarcrm\Maps\FilterUtils as MapsFilterUtils;

class FilterApi extends SugarApi
{
    /**
     * Registers endpoints for the FilterApi
     *
     * @return array[] the endpoint data
     */
    public function registerApiRest()
    {
        return [
            'filterModuleGet' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'filter'],
                'pathVars' => ['module', ''],
                'method' => 'filterList',
                'jsonParams' => ['filter'],
                'shortHelp' => 'Lists filtered records.',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterList and filterListSetup
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup, getPredefinedFilterById, and parseArguments
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'filterModuleAll' => [
                'reqType' => 'GET',
                'path' => ['<module>'],
                'pathVars' => ['module'],
                'method' => 'filterList',
                'jsonParams' => ['filter'],
                'shortHelp' => 'List of all records in this module',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterList and filterListSetup
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup, getPredefinedFilterById, and parseArguments
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'filterModuleAllCount' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'count'],
                'pathVars' => ['module', ''],
                'jsonParams' => ['filter'],
                'method' => 'getFilterListCount',
                'shortHelp' => 'List of all records in this module',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterListSetup and getPredefinedFilterById
                    'SugarApiExceptionNotAuthorized',
                    // Thrown in filterListSetup
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'filterModulePost' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'filter'],
                'pathVars' => ['module', ''],
                'method' => 'filterList',
                'shortHelp' => 'Lists filtered records.',
                'longHelp' => 'include/api/help/module_filter_post_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterList and filterListSetup
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup, getPredefinedFilterById, and parseArguments
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'filterModulePostCount' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'filter', 'count'],
                'pathVars' => ['module', '', ''],
                'method' => 'filterListCount',
                'shortHelp' => 'Lists filtered records.',
                'longHelp' => 'include/api/help/module_filter_post_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterListSetup and getPredefinedFilterById
                    'SugarApiExceptionNotAuthorized',
                    // Thrown in filterListSetup
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'filterModuleCount' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'filter', 'count'],
                'pathVars' => ['module', '', ''],
                'method' => 'getFilterListCount',
                'shortHelp' => 'Lists filtered records.',
                'longHelp' => 'include/api/help/module_filter_post_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterListSetup
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'filterModuleSum' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'total'],
                'pathVars' => ['module', '', ''],
                'method' => 'getFilterListSum',
                'shortHelp' => 'Lists field sum by filtered records.',
                'longHelp' => 'include/api/help/module_sum_by_filter_get_help.html',
                'exceptions' => [
                    // Thrown in getPredefinedFilterById
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    // Thrown in filterListSetup
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionInvalidParameter',
                ],
                'minVersion' => '11.21',
            ],
        ];
    }

    protected static $isFavorite = false;

    protected $defaultLimit = 20; // How many records should we show if they don't pass up a limit
    protected $defaultOrderBy = [
        ['date_modified', 'DESC'],
    ];

    protected static $current_user;

    /**
     * List of fields that are mandatory for all filters
     * @var array
     */
    protected static $mandatory_fields = [
        // id and date_modified should always be in the response
        'id',
        'date_modified',
        // user fields are needed for ACLs since they check owners
        'assigned_user_id',
        'created_by',
        // Locked fields are necessary to enforce data integrity for processes on
        // on the client
        'locked_fields',
    ];

    public function __construct()
    {
        global $current_user;
        self::$current_user = $current_user;
    }

    protected function parseArguments(ServiceBase $api, array $args, SugarBean $seed = null)
    {
        $options = [];

        // Set up the defaults
        $options['limit'] = $this->defaultLimit;
        $options['offset'] = 0;
        $options['add_deleted'] = true;

        if ($seed && isset($seed->force_limit)) {
            $options['limit'] = $seed->force_limit;
        } else {
            if (!empty($args['max_num'])) {
                $options['limit'] = (int)$args['max_num'];
            }

            $options['limit'] = $this->checkMaxListLimit($options['limit']);
        }

        if (!empty($args['deleted'])) {
            $options['add_deleted'] = false;
        }

        if (!empty($args['offset'])) {
            if ($args['offset'] == 'end') {
                $options['offset'] = 'end';
            } else {
                $options['offset'] = (int)$args['offset'];
            }
        }

        $orderBy = $this->getOrderByFromArgs($args, $seed);
        if ($orderBy) {
            $options['order_by'] = $orderBy;
        }

        // Set $options['module'] so that runQuery can create beans of the right
        // type.
        if (!empty($seed)) {
            $options['module'] = $seed->module_name;
        }

        // Set the list of fields to be used in the select.
        $options['select'] = $this->getFieldsFromArgs($api, $args, $seed, 'view', $options['display_params']);

        $options['select'] = array_unique(
            array_merge($options['select'], self::$mandatory_fields)
        );

        // Some modules have fields that are composed of or require other fields
        // Add those in now so that they can be selected and set onto the bean
        // so formatting can use them. This is necessary for file type fields.
        if (!empty($seed) && !empty($seed->field_defs)) {
            foreach ($seed->field_defs as $field => $def) {
                if (isset($def['name']) && safeInArray($def['name'], $options['select']) && isset($def['fields'])) {
                    foreach ($def['fields'] as $field) {
                        if (!safeInArray($field, $options['select'])) {
                            $options['select'][] = $field;
                        }
                    }
                }
            }

            foreach ($options['select'] as $field) {
                if (isset($seed->field_defs[$field]) && !empty($seed->field_defs[$field]['relate_collection'])) {
                    $options['relate_collections'][$field] = $seed->field_defs[$field];
                }
            }
        }

        $options['action'] = $api->action;

        if (!empty($args['erased_fields'])) {
            $options['erased_fields'] = true;
        }

        // if true, return nulls last for order by fields
        $options['nulls_last'] = !empty($args['nulls_last']);

        return $options;
    }

    /**
     * Creates internal representation of ORDER BY expression from API arguments
     *
     * Overrides parent implementation in order to convert the value to the structure
     * which is currently used in Filter API
     *
     * {@inheritDoc}
     */
    protected function getOrderByFromArgs(array $args, SugarBean $seed = null)
    {
        $orderBy = parent::getOrderByFromArgs($args, $seed);
        // denormalized and synchronized relate fields to be used for ORDER BY queries
        $synchronizedFields = isset($args['module'])
            ? (new FieldConfig())->getSynchronizedModuleFields($args['module'])
            : null;
        $converted = [];
        foreach ($orderBy as $field => $direction) {
            // use denormalized fields for ORDER BY if this was configured
            if (isset($synchronizedFields[$field])) {
                $field = $synchronizedFields[$field]['denorm_field_name'];
            }
            $converted[] = [$field, $direction ? 'ASC' : 'DESC'];
        }

        return $converted;
    }

    /**
     * Retrieve a predefined filter by the given ID
     *
     * @param ServiceBase $api The API class of the request.
     * @param string $filterId ID of the filter to retrieve.
     * @return array The (possibly empty) filter as an associative array.
     * @throws SugarApiExceptionError If JSON decoding failed or json_decode
     *   returned something other than an array.
     * @throws SugarApiExceptionNotFound If we cannot find the filter.
     * @throws SugarApiExceptionNotAuthorized If we do not have permission to
     *   load the filter.
     */
    private function getPredefinedFilterById(ServiceBase $api, $filterId)
    {
        $predefinedFilter = $this->loadBean(
            $api,
            [
                'module' => 'Filters',
                'record' => $filterId,
            ]
        );

        if (empty($predefinedFilter->filter_definition)) {
            LoggerManager::getLogger()->warn('Filter ' . $predefinedFilter->id . ' has no definition');
            return [];
        }

        // Try to decode the filter. Note that the expectation is that
        // json_decode returns an array based on the assumption that filters are
        // encoded in the database as objects.
        $decodedFilter = json_decode($predefinedFilter->filter_definition, true);
        if (is_array($decodedFilter)) {
            return $decodedFilter;
        }
        $jsonErrorMessage = 'Decoding definition for filter ' . $predefinedFilter->id . ' failed';
        LoggerManager::getLogger()->error($jsonErrorMessage);
        throw new SugarApiExceptionError($jsonErrorMessage);
    }

    /**
     * Preprocess the args array to set filter options.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array An array containing the modified args array, a query object
     *   with all the filters applied, the modified options array, and a
     *   SugarBean for the chosen module.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function filterListSetup(ServiceBase $api, array $args, $acl = 'list')
    {
        $seed = BeanFactory::newBean($args['module']);

        if (!$seed->ACLAccess($acl, ['source' => 'filter_api'])) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }

        $options = $this->parseArguments($api, $args, $seed);

        // In case the view parameter is set, reflect those fields in the
        // fields argument as well so formatBean only takes those fields
        // into account instead of every bean property.
        if (!empty($args['view'])) {
            $args['fields'] = $options['select'];
        }

        // Relate collections should not be in the select clause of the query
        // since we have trouble doing group bys on certain databases. We should
        // be getting the relate collection values later anyways.
        if (isset($options['relate_collections'])) {
            $options = $this->removeRelateCollectionsFromSelect($options);
        }

        if (empty($args['filter_id'])) {
            $predefinedFilter = [];
        } else {
            $predefinedFilter = $this->getPredefinedFilterById($api, $args['filter_id']);
            unset($args['filter_id']);
        }

        // FIXME TY-1821: Empty filter definitions are currently supported to
        // maintain backward compatibility on v10. This behaviour will change on
        // one of the upcoming API versions.
        if (isset($args['filter']) && $args['filter'] == '') {
            // Remove filter if it is an empty string.
            unset($args['filter']);
        }

        // filter must be an array
        if (isset($args['filter']) && !is_array($args['filter'])) {
            throw new SugarApiExceptionInvalidParameter('Unexpected filter type ' . gettype($args['filter']) . '.');
        }

        if (isset($args['filter'])) {
            $filterDefinition = $args['filter'];
            $args['filter'] = array_merge($predefinedFilter, $filterDefinition);
        } else {
            $args['filter'] = $predefinedFilter;
        }

        $q = $this->createQuery($seed, $args, $options);

        if (safeCount($q->join) > 0) {
            $options['id_query'] = $this->createQuery($seed, $args, array_merge($options, [
                'select' => ['id'],
                'erased_fields' => false,
            ]));

            $seed->disable_row_level_security = true;
            $q = $this->createQuery($seed, $args, $options);
        }

        return [$args, $q, $options, $seed];
    }

    private function createQuery(SugarBean $seed, array $args, array $options): SugarQuery
    {
        $query = self::getQueryObject($seed, $options);

        if (!empty($args['filter'])) {
            static::addFilters($args['filter'], $query->where(), $query);
        }

        if (!empty($args['my_items'])) {
            static::addOwnerFilter($query, $query->where(), '_this');
        }

        if (!empty($args['favorites'])) {
            static::addFavoriteFilter($query, $query->where(), '_this', 'INNER');
        }

        if (safeCount($query->order_by) < 1) {
            self::addOrderBy($query, $this->defaultOrderBy, $options['nulls_last']);
        }

        // This section of code is a portion of the code referred
        // to as Critical Control Software under the End User
        // License Agreement.  Neither the Company nor the Users
        // may modify any portion of the Critical Control Software.

        // add not in filter
        $inaccessibleList = AccessControlManager::instance()->getNotAccessibleRecords($seed->getModuleName());
        if (!empty($inaccessibleList)) {
            self::addFilter('id', ['$not_in' => $inaccessibleList], $query->where(), $query);
        }
        //END REQUIRED CODE DO NOT MODIFY

        return $query;
    }

    /**
     * Returns the records for the module and filter provided.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array The REST response as a PHP array.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function filterList(ServiceBase $api, array $args, $acl = 'list')
    {
        if (!empty($args['q'])) {
            if (!empty($args['filter']) || !empty($args['filter_id']) || !empty($args['deleted'])) {
                // These flags can be used with the filter API, but not with the search API
                throw new SugarApiExceptionInvalidParameter();
            }
            // We need to use unified search for this for compatibilty with Nomad
            $search = new UnifiedSearchApi();
            $args['module_list'] = $args['module'];
            return $search->globalSearch($api, $args);
        }

        $api->action = 'list';

        return $this->runQuery($api, ...$this->filterListSetup($api, $args, $acl));
    }

    /**
     * Returns the number of records for the module and filter provided:
     *
     * Example:
     *     {"record_count": 50}
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array The number of filtered/unfiltered records for the module provided.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter if any of the parameters are
     *  invalid.
     * @throws SugarApiExceptionNotAuthorized if we lack ACL access.
     */
    public function getFilterListCount(ServiceBase $api, array $args)
    {
        $api->action = 'list';

        /** @var SugarQuery $q */
        [, $q, $options] = $this->filterListSetup($api, $args);

        $q = $options['id_query'] ?? $q;

        return [
            'record_count' => $this->fetchCount($q),
        ];
    }

    /**
     * Returns the number of records for the module and filter provided:
     *
     * Example:
     *     {"record_sum": 123.45}
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array The number of filtered/unfiltered records for the module provided.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter if any of the parameters are
     *  invalid.
     * @throws SugarApiExceptionNotAuthorized if we lack ACL access.
     */
    public function getFilterListSum(ServiceBase $api, array $args)
    {
        if (empty($args['sumField']) || !(is_scalar($args['sumField']))) {
            return null;
        }

        $api->action = 'list';

        /** @var SugarQuery $q */
        [, $q, $options] = $this->filterListSetup($api, $args);

        $q = $options['id_query'] ?? $q;

        if (empty($q->select('*')->select[$args['sumField']])) {
            return null;
        }

        return [
            'sum_by_field' => $this->fetchSum($q, $args['sumField']),
            'field_type' => $q->select('*')->select[$args['sumField']]->def['type'],
        ];
    }

    /**
     * Returns the number of records for the module and filter provided:
     *
     * Example:
     *     {'record_count': '50'}
     *
     * This method is now deprecated. Use getFilterListCount instead.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return Object The number of filtered/unfiltered records for the module
     *   provided.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter if any of the parameters are
     *  invalid.
     * @throws SugarApiExceptionNotAuthorized if we lack ACL access.
     * @deprecated Since 7.7.0. Will be removed in 7.9.0.
     */
    public function filterListCount(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->fatal('POST <module>/filter/count has been deprecated as of 7.7.0. ' .
            'Please use the equivalent GET endpoint instead.');

        return $this->getFilterListCount($api, $args);
    }

    protected static function getQueryObject(SugarBean $seed, array $options)
    {
        if (empty($options['select'])) {
            $options['select'] = self::$mandatory_fields;
        }

        $queryOptions = [
            'add_deleted' => !isset($options['add_deleted']) || $options['add_deleted'],
        ];

        if ($queryOptions['add_deleted'] == false) {
            $options['select'][] = 'deleted';
        }

        if (!empty($options['erased_fields'])) {
            $queryOptions['erased_fields'] = true;
        }

        $q = static::newSugarQuery(DBManagerFactory::getInstance('listviews'));
        $q->from($seed, $queryOptions);
        $q->distinct(false);
        $fields = [];
        foreach ($options['select'] as $field) {
            // Skip the related bean options since related collections
            // are expected to be handled later, e.g. FilterApi for Tags
            if (isset($seed->field_defs[$field]['relate_collection']) &&
                $seed->field_defs[$field]['relate_collection']) {
                continue;
            }

            // FIXME: convert this to vardefs too?
            if ($field == 'my_favorite') {
                if (self::$isFavorite) {
                    $joinType = 'INNER';
                } else {
                    $joinType = 'LEFT';
                }
                $fjoin = $q->join('favorites', ['joinType' => $joinType]);
                $fields[] = [$fjoin->joinName() . '.id', 'my_favorite'];
                continue;
            }

            // fields that aren't in field defs are removed, since we don't know
            // what to do with them
            if (isset($seed->field_defs[$field]['type'])) {
                $sf = SugarFieldHandler::getSugarField($seed->field_defs[$field]['type']);
                $sf->addFieldToQuery($field, $fields);
            }
        }

        $q->select($fields);

        if (!empty($options['order_by'])) {
            self::addOrderBy($q, $options['order_by'], $options['nulls_last']);
        }

        // nagative limit means no limit
        if (isset($options['limit']) && $options['limit'] >= 0) {
            // Add an extra record to the limit so we can detect if there are more records to be found
            $q->limit($options['limit'] + 1);
        }
        if (isset($options['offset'])) {
            $q->offset($options['offset']);
        }

        return $q;
    }

    /**
     * Adds order by to query
     * @param SugarQuery $q
     * @param array $orderByOption
     * @param bool $nullsLast
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    protected static function addOrderBy(SugarQuery $q, array $orderByOption, bool $nullsLast = false)
    {
        foreach ($orderByOption as $orderBy) {
            // ID and date_modified are used to give some order to the system
            if ($orderBy[0] != 'date_modified' && $orderBy[0] != 'id') {
                self::verifyField($q, $orderBy[0]);
            }
            $q->orderBy($orderBy[0], $orderBy[1], $nullsLast);
        }
    }

    /**
     * Populate related beans from data array.
     *
     * @param SugarBean $bean
     * @param array $data
     */
    protected function populateRelatedFields(SugarBean $bean, $data)
    {
        $relates = [];
        // fill in related rows data by field
        foreach ($data as $key => $value) {
            if (($split = strpos($key, '__')) > 0) {
                $relates[substr($key, 0, $split)][] = substr($key, $split + 2);
            }
        }

        foreach ($bean->field_defs as $field => $fieldDef) {
            if (safeInArray($fieldDef['type'], $bean::$relateFieldTypes)
                && (!empty($fieldDef['link']) || !empty($fieldDef['module']))) {
                if (empty($data[$field]) && empty($relates[$field])) {
                    continue;
                }

                if (!empty($fieldDef['link'])) {
                    $rbean = $bean->getRelatedBean($fieldDef['link']);
                } else {
                    $rbean = BeanFactory::newBean($fieldDef['module']);
                }

                if (empty($rbean)) {
                    continue;
                }

                if (!empty($data[$field])) {
                    if (empty($fieldDef['rname'])) {
                        LoggerManager::getLogger()->fatal("Field $field has invalid metadata, " .
                            'has source of relate but is missing rname');
                        continue;
                    }
                    // we have direct data - populate it
                    $rbean->populateFromRow(
                        [$fieldDef['rname'] => $data[$field]],
                        true
                    );
                } else {
                    if (empty($relates[$field])) {
                        continue;
                    }

                    $reldata = [];
                    foreach ($relates[$field] as $relfield) {
                        $reldata[$relfield] = $data["{$field}__{$relfield}"];
                    }
                    if (!empty($reldata)) {
                        $rbean->populateFromRow($reldata, true);
                    }
                    if (empty($fieldDef['link'])) {
                        $bean->related_beans[$fieldDef['name']] = $rbean;
                    }
                }

                if (empty($rbean->id) && !empty($fieldDef['id_name']) && !empty($data[$fieldDef['id_name']])) {
                    $rbean->id = $data[$fieldDef['id_name']];
                }
            }
        }
        // Call some data fillings for the bean
        foreach ($bean->related_beans as $rbean) {
            if (empty($rbean->id)) {
                continue;
            }

            $rbean->check_date_relationships_load();
            // $rbean->fill_in_additional_list_fields();
            if ($rbean->hasCustomFields()) {
                $rbean->custom_fields->fill_relationships();
            }
            $rbean->call_custom_logic('process_record');
        }
    }

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
            $ids = $options['id_query']
                ->compile()
                ->execute()
                ->fetchFirstColumn();

            if (safeCount($ids) < 1) {
                return [
                    'records' => [],
                    'next_offset' => -1,
                ];
            }

            $q->where()
                ->in('id', $ids);
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
        foreach ($beans as $bean_id => $bean) {
            if ($i == $options['limit'] || ($i - $distinctCompensation) == $options['limit']) {
                if (safeCount($beans) > $options['limit']) {
                    unset($beans[$bean_id]);
                }
                $data['next_offset'] = (int)($options['limit'] + $options['offset']);
                continue;
            }
            $i++;

            if (isset($rows[$bean_id]['last_viewed_date'])) {
                $lastViewedDates[$bean_id] = $db->fromConvert($rows[$bean_id]['last_viewed_date'], 'datetime');
            }

            if ($bean instanceof User && empty($bean->is_group)) {
                $bean->is_group = 0;
            }

            if (isset($rows[$bean_id]['is_external_link'])) {
                $bean->_is_external_link = $rows[$bean_id]['is_external_link'];
            }
            $this->populateRelatedFields($bean, $rows[$bean_id]);
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
     * Run additional queries to find all the related records pointed to by relate_collection fields.
     *
     * @param array $beans
     * @param array $options
     * @return array
     */
    protected function runRelateCollectionQuery(array $beans, array $options)
    {
        $rc_beans = [];

        // Sanity check, just to make sure things are kosher
        if (empty($beans) || empty($options['relate_collections'])) {
            return $rc_beans;
        }

        // Grab the string of bean_ids for use in the IN clause, making sure to
        // quote them according their own DB
        $bean_ids = array_keys($beans);

        foreach ($options['relate_collections'] as $name => $def) {
            // Parent bean
            $bean = BeanFactory::newBean($options['module']);

            // Related bean
            $relate_bean = BeanFactory::newBean($def['module']);

            // If the related bean has the necessary method to get related records
            // then call it
            if (is_callable([$relate_bean, 'getRelatedModuleRecords'])) {
                $rc_beans[$name] = $relate_bean->getRelatedModuleRecords($bean, $bean_ids);
            } else {
                LoggerManager::getLogger()->fatal('Field is a relate collection, ' .
                    'but associated module does not have function getRelatedModuleRecords');
            }
        }

        return $rc_beans;
    }

    /**
     * Parse fetched result set as returned by SugarBean::fetchFromQuery.
     * Besides an array of beans some additional parameters are passed
     * which we want to abstract from it and cleanup the actually array.
     *
     * @param array $fetched Result set from SugarBean::fetchFromQuery
     * @return array
     */
    protected function parseQueryResults(array $fetched)
    {
        $rows = [];
        $distinctCompensation = 0;

        if (isset($fetched['_rows'])) {
            $rows = $fetched['_rows'];
            unset($fetched['_rows']);
        }

        if (isset($fetched['_distinctCompensation'])) {
            $distinctCompensation = $fetched['_distinctCompensation'];
            unset($fetched['_distinctCompensation']);
        }

        return [$fetched, $rows, $distinctCompensation];
    }

    /**
     * Verify that the passed field is correct
     *
     * @param SugarQuery $q
     * @param string $field
     * @return bool
     * @throws SugarApiExceptionInvalidParameter
     */
    protected static function verifyField(SugarQuery $q, $field)
    {
        $ret = [];
        if (strpos($field, '.')) {
            // It looks like it's a related field that it's searching by
            [$linkName, $field] = explode('.', $field);

            $q->from->load_relationship($linkName);
            if (empty($q->from->$linkName)) {
                throw new SugarApiExceptionInvalidParameter("Invalid link $linkName for field $field");
            }

            if ($q->from->$linkName->getType() == 'many') {
                // FIXME TY-1192: we have a problem here: we should allow 'many' links for related to match against
                // parent object but allowing 'many' in other links may lead to duplicates. So for now we allow 'many'
                // but we should figure out how to find if 'many' is permittable or not.
                // throw new SugarApiExceptionInvalidParameter("Cannot use condition against multi-link $linkName");
            }

            $join = $q->join($linkName, ['joinType' => 'LEFT']);
            $table = $join->joinName();
            $ret['field'] = "$table.$field";

            $bean = $q->getTableBean($table);
            if (empty($bean)) {
                $bean = $q->getTableBean($linkName);
            }
            if (empty($bean) && $q->getFromBean() && $q->getFromBean()->$linkName) {
                $bean = BeanFactory::newBean($q->getFromBean()->$linkName->getRelatedModuleName());
            }
            if (empty($bean)) {
                throw new SugarApiExceptionInvalidParameter("Cannot use condition against $linkName - unknown module");
            }
        } else {
            $bean = $q->from;
        }
        $defs = $bean->field_defs;

        if (empty($defs[$field])) {
            throw new SugarApiExceptionInvalidParameter("Unknown field $field");
        }

        if (!$bean->ACLFieldAccess($field)) {
            throw new SugarApiExceptionNotAuthorized("Access for field $field is not allowed");
        }

        $field_def = $defs[$field];

        if (!empty($field_def['source']) && $field_def['source'] == 'relate') {
            if (empty($field_def['rname']) || empty($field_def['link'])) {
                throw new SugarApiExceptionInvalidParameter("Field $field has invalid metadata, has source of relate" .
                    ' but is missing rname or link');
            }
            $relfield = $field_def['rname'];
            $link = $field_def['link'];
            return self::verifyField($q, "$link.$relfield");
        }

        $ret['bean'] = $bean;
        $ret['def'] = $field_def;

        return $ret;
    }

    /**
     * Add filters to the query
     *
     * @param array $filterDefs
     * @param SugarQuery_Builder_Where $where
     * @param SugarQuery $q
     * @throws SugarApiExceptionInvalidParameter
     */
    protected static function addFilters(array $filterDefs, SugarQuery_Builder_Where $where, SugarQuery $q)
    {
        foreach ($filterDefs as $filterDef) {
            if (!is_array($filterDef)) {
                throw new SugarApiExceptionInvalidParameter(
                    sprintf(
                        'Did not recognize the definition: %s',
                        print_r($filterDef, true)
                    )
                );
            }
            foreach ($filterDef as $field => $filter) {
                static::addFilter($field, $filter, $where, $q);
            }
        }
    }

    /**
     * Add an individual filter part to the query
     *
     * @param string $field name of the field or shorcut to operate on. Ex. 'name' , '$owner'
     * @param array|string $filter filter definition. Ex. {'$equals':'foo'}
     * @param SugarQuery_Builder_Where $where
     * @param SugarQuery $q
     *
     * This function should be considered internal to sugar and not extended by external customizations.
     *
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarQueryException
     */
    protected static function addFilter($field, $filter, SugarQuery_Builder_Where $where, SugarQuery $q)
    {
        // It's an email participant filter if the module is Emails and the field is an email participants operand.
        if ($q->getFromBean()->getModuleName() === 'Emails' && safeInArray($field, ['$from', '$to', '$cc', '$bcc'])) {
            static::addEmailParticipantFilter($q, $where, $filter, $field);
        } elseif (safeInArray($q->getFromBean()->getModuleName(), ['Calls', 'Meetings']) && $field === '$guest') {
            static::addGuestFilter($q, $where, $filter);
        } elseif ($field == '$or') {
            static::addFilters($filter, $where->queryOr(), $q);
        } elseif ($field == '$and') {
            static::addFilters($filter, $where->queryAnd(), $q);
        } elseif ($field == '$favorite') {
            static::addFavoriteFilter($q, $where, $filter);
        } elseif ($field == '$owner') {
            static::addOwnerFilter($q, $where, $filter);
        } elseif ($field == '$creator') {
            static::addCreatorFilter($q, $where, $filter);
        } elseif ($field == '$tracker') {
            static::addTrackerFilter($q, $where, $filter);
        } elseif ($field == '$following') {
            static::addFollowFilter($q, $where, $filter);
        } elseif ($field == '$distance') {
            static::addMapsDistanceFilter($q, $where, $filter);
        } else {
            static::addFieldFilter($q, $where, $filter, $field);
        }
    }

    /**
     * Processes filter parts that operate on standard (non-macro) fields
     *
     * @param SugarQuery_Builder_Where $where
     * @param SugarQuery $q
     * @param array $filter
     * @param string $field
     *
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    protected static function addFieldFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter, $field)
    {
        static $sfh;
        if (!isset($sfh)) {
            $sfh = new SugarFieldHandler();
        }

        // Looks like just a normal field, parse its options
        $fieldInfo = self::verifyField($q, $field);

        // If the field was a related field and we added a join, we need to adjust the table name used
        // to get the right join table alias
        if (!empty($fieldInfo['field'])) {
            $field = $fieldInfo['field'];
        }

        // It's an email participant filter if the module is Emails and the field is an email participants field.
        $fromModule = $q->getFromBean()->getModuleName();
        $emailParticipantsFields = ['from_collection', 'to_collection', 'cc_collection', 'bcc_collection'];
        if ($fromModule === 'Emails' && safeInArray($field, $emailParticipantsFields)) {
            static::addEmailParticipantFieldFilter($q, $where, $filter, $field);
            return;
        }

        $fieldType = !empty($fieldInfo['def']['custom_type']) ? $fieldInfo['def']['custom_type'] :
            $fieldInfo['def']['type'];
        $sugarField = $sfh->getSugarField($fieldType);
        if (!is_array($filter)) {
            $value = $filter;
            $filter = [];
            $filter['$equals'] = $value;
        }
        foreach ($filter as $op => $value) {
            /*
             * occasionally fields may need to be fixed up for the Filter, for instance if you are
             * doing an operation on a datetime field and only send in a date, we need to fix that field to
             * be a dateTime then unFormat it so that its in GMT ready for DB use
             */

            if (strpos($field, '.') === false) {
                if (isset($fieldInfo['def']['source']) && $fieldInfo['def']['source'] === 'custom_fields') {
                    $tableName = $fieldInfo['bean']->get_custom_table_name();
                } else {
                    $tableName = $fieldInfo['bean']->getTableName();
                }
                $columnName = $tableName . '.' . $field;
            } else {
                $columnName = $field;
            }

            if ($sugarField->fixForFilter($value, $columnName, $fieldInfo['bean'], $q, $where, $op) == false) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $i => $val) {
                    // FIXME: BR-4063 apiUnformat() is deprecated, this will change to apiUnformatField() in
                    // next API version
                    $value[$i] = $sugarField->apiUnformat($val);
                }
            } else {
                // FIXME: BR-4063 apiUnformat() is deprecated, this will change to apiUnformatField() in
                // next API version
                $value = $sugarField->apiUnformat($value);
            }

            switch ($op) {
                case '$more_x_days_ago':
                case '$last_x_days':
                case '$next_x_days':
                case '$more_x_days_ahead':
                    break;
                case '$equals':
                    $where->equals($field, $value);
                    break;
                case '$not_equals':
                    $where->notEquals($field, $value);
                    break;
                case '$starts':
                    $where->starts($field, $value);
                    break;
                case '$ends':
                    $where->ends($field, $value);
                    break;
                case '$contains':
                    $where->contains($field, $value);
                    break;
                case '$not_contains':
                    $where->notContains($field, $value);
                    break;
                case '$in':
                    if (!is_array($value)) {
                        throw new SugarApiExceptionInvalidParameter('$in requires an array');
                    }
                    $where->in($field, $value);
                    break;
                case '$not_in':
                    if (!is_array($value)) {
                        throw new SugarApiExceptionInvalidParameter('$not_in requires an array');
                    }
                    $where->notIn($field, $value);
                    break;
                case '$dateBetween':
                case '$between':
                    if (!is_array($value) || safeCount($value) != 2) {
                        throw new SugarApiExceptionInvalidParameter(
                            '$between requires an array with two values.'
                        );
                    }
                    $where->between($field, $value[0], $value[1]);
                    break;
                case '$is_null':
                    $where->isNull($field);
                    break;
                case '$not_null':
                    $where->notNull($field);
                    break;
                case '$empty':
                    $where->isEmpty($field);
                    break;
                case '$not_empty':
                    $where->isNotEmpty($field);
                    break;
                case '$lt':
                    $where->lt($field, $value);
                    break;
                case '$lte':
                    $where->lte($field, $value);
                    break;
                case '$gt':
                    $where->gt($field, $value);
                    break;
                case '$gte':
                    $where->gte($field, $value);
                    break;
                case '$dateRange':
                    $where->dateRange($field, $value, $fieldInfo['bean']);
                    break;
                default:
                    throw new SugarApiExceptionInvalidParameter('Did not recognize the operand: ' . $op);
            }
        }
    }

    /**
     * Handles the from_collection, to_collection, cc_collection, and bcc_collection fields for filtering.
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param array $filter
     * @param string $field
     * @throws SugarApiExceptionInvalidParameter
     */
    protected static function addEmailParticipantFieldFilter(
        SugarQuery               $q,
        SugarQuery_Builder_Where $where,
        $filter,
        $field
    ) {

        static $operands = [
            'from_collection' => '$from',
            'to_collection' => '$to',
            'cc_collection' => '$cc',
            'bcc_collection' => '$bcc',
        ];
        $operand = $operands[$field];

        if (!is_array($filter)) {
            throw new SugarApiExceptionInvalidParameter("No operands defined for {$field}");
        }

        if (!array_key_exists('$in', $filter)) {
            throw new SugarApiExceptionInvalidParameter("{$field} requires the use of the \$in operand");
        }

        $supportedOperands = ['$in'];
        $unsupportedOperands = array_diff(array_keys($filter), $supportedOperands);

        if (!empty($unsupportedOperands)) {
            throw new SugarApiExceptionInvalidParameter(
                "{$field} does not support these operands: " . implode(', ', $unsupportedOperands)
            );
        }

        foreach ($supportedOperands as $op) {
            static::addEmailParticipantFilter($q, $where, $filter[$op], $operand);
        }
    }

    /**
     * This function adds an owner filter to the sugar query
     *
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param string $link Which module are you adding the owner filter to.
     */
    protected static function addOwnerFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $link)
    {
        if ($link == '' || $link == '_this') {
            $linkPart = '';
        } else {
            $join = $q->join($link, ['joinType' => 'LEFT']);
            $linkPart = $join->joinName() . '.';
        }

        $where->equals($linkPart . 'assigned_user_id', self::$current_user->id);
    }

    /**
     * Add a Following Filter
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $filter
     */
    protected static function addFollowFilter(
        SugarQuery               $q,
        SugarQuery_Builder_Where $where,
        $filter,
        $joinType = 'LEFT'
    ) {

        $field = 'following';
        $q->select($field);
        if (isset($q->from->field_defs[$field]['link'])) {
            $link = $q->from->field_defs[$field]['link'];
            if (isset($q->joinLinkToKey[$link])) {
                $alias = $q->joinLinkToKey[$link];
                $where->addRaw("$alias.id IS NOT NULL");
            }
        }
    }


    /**
     * Add a Maps Distance Filter
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $filter
     */
    protected static function addMapsDistanceFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter)
    {
        if (array_key_exists('$in_radius_from_zip', $filter)) {
            self::applyMapsDistanceZipFilter($q, $where, $filter);
        }

        if (array_key_exists('$in_radius_from_coords', $filter)) {
            self::applyMapsDistanceCoordsFilter($q, $where, $filter);
        }
    }

    /**
     * Add a Maps Distance Filter by ZipCode
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $filter
     */
    protected static function applyMapsDistanceZipFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter)
    {
        if (!hasMapsLicense()) {
            return;
        }

        $filterData = $filter['$in_radius_from_zip'];

        $radius = $filterData['radius'];
        $country = $filterData['countries'];
        $zipCode = $filterData['zipCode'];
        $unitType = $filterData['unitType'];

        if (!$radius || !$zipCode) {
            return;
        }

        $coords = MapsFilterUtils::getCoordsFromZip($zipCode, $country);

        if ($coords === false) {
            $q->whereRaw('1 = 0');
            return;
        }

        self::applyMapDistance($q, $coords, $radius, $unitType);
    }

    /**
     * Add a Maps Distance Filter by Coords
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $filter
     */
    protected static function applyMapsDistanceCoordsFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter)
    {
        if (!hasMapsLicense()) {
            return;
        }

        $filterData = $filter['$in_radius_from_coords'];

        $radius = $filterData['radius'];
        $unitType = $filterData['unitType'];

        if (!$radius) {
            return;
        }

        if (!array_key_exists('latitude', $filterData) || !array_key_exists('longitude', $filterData)) {
            $q->whereRaw('1 = 0');
            return;
        }

        $latitude = $filterData['latitude'];
        $longitude = $filterData['longitude'];

        $coords = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        self::applyMapDistance($q, $coords, $radius, $unitType);
    }

    /**
     * Apply map distance as filter
     *
     * @param SugarQuery $q
     * @param array $coords
     * @param mixed $radius
     * @param mixed $unitType
     */
    public static function applyMapDistance(SugarQuery $q, array $coords, $radius, $unitType)
    {
        $admin = BeanFactory::getBean('Administration');
        $mapsConfig = $admin->retrieveSettings('maps', true)->settings;

        if (!$mapsConfig['maps_modulesData']) {
            $q->select->fieldRaw('0', 'maps_distance');
            $q->whereRaw('1 = 0');

            return;
        }

        $targetModule = $q->getFromBean()->getModuleName();
        $recordTable = $q->getFromBean()->getTableName();
        $mapsModuleData = $mapsConfig['maps_modulesData'][$targetModule];

        if ($mapsModuleData && $mapsModuleData['mappingType'] === 'relateRecord') {
            $recordTable = self::addMapsRelatedJoins($q, $mapsModuleData, []);
        }

        $geocodeTable = MapsFilterUtils::getCoordsTableName();

        $join = $q->joinTable($geocodeTable, [
            'joinType' => 'LEFT',
        ]);

        $join->on()->queryAnd()
            ->equalsField("{$geocodeTable}.parent_id", "{$recordTable}.id");

        $earthRadiusKm = 6371;
        $pi = pi();

        /* Casting to float type is necessary here for sanitization of query arguments */
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
        $q->whereRaw(
            "((
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
            ) * $earthRadiusKm) < $radius"
        );
    }

    /**
     * Make extra joins when we use related records mapping
     *
     * @param SugarQuery $q
     * @param mixed $mapsModuleData
     * @param mixed $fields
     */
    protected static function addMapsRelatedJoins(SugarQuery $q, $mapsModuleData, $fields)
    {
        $mappingRecord = $mapsModuleData['mappingRecord'];
        $relatedKey = array_keys($mappingRecord)[0];
        $relateLinkName = $mappingRecord[$relatedKey]['rel'];
        $relatedModule = $mappingRecord[$relatedKey]['module'];

        $hasCustomFields = self::hasMapsCustomFields($relatedModule, $fields);

        $relDef = SugarRelationshipFactory::getInstance()->getRelationship($relateLinkName)->def;

        $joinKeyRhs = $relDef['join_key_rhs'];
        $joinKeyLhs = $relDef['join_key_lhs'];

        $rhsTableName = $relDef['rhs_table'];
        $lhsTableName = $relDef['lhs_table'];
        $joinTableName = $relDef['join_table'];

        $lhsTableNameAlias = $q->getFromAlias();
        $rhsTableNameAlias = 'rhs_alias_maps';

        if (empty($joinTableName)) {
            $joinKeyRhs = $relDef['rhs_key'];
            $joinKeyLhs = $relDef['lhs_key'];
            $rhsTableNameAlias = $q->getFromAlias();
            $lhsTableNameAlias = 'lhs_alias_maps';

            $join = $q->joinTable($lhsTableName, [
                'alias' => $lhsTableNameAlias,
                'joinType' => 'INNER',
            ]);

            $join->on()->equalsField("{$lhsTableNameAlias}.{$joinKeyLhs}", "{$rhsTableNameAlias}.{$joinKeyRhs}");
        } else {
            $join = $q->joinTable($joinTableName, [
                'alias' => $rhsTableNameAlias,
                'joinType' => 'INNER',
            ]);

            $join->on()->equalsField("{$rhsTableNameAlias}.{$relDef['join_key_rhs']}", "{$lhsTableNameAlias}.id");

            $lhsTableNameAlias = 'lhs_table_alias_maps';

            $join = $q->joinTable($lhsTableName, [
                'alias' => $lhsTableNameAlias,
                'joinType' => 'INNER',
            ]);

            $join->on()->equalsField("{$rhsTableNameAlias}.{$relDef['join_key_lhs']}", "{$lhsTableNameAlias}.id");
        }

        if ($hasCustomFields) {
            $customLhsTableName = "{$lhsTableName}_cstm";
            $customLhsTableNameAlias = "{$lhsTableNameAlias}_cstm";
            $join = $q->joinTable($customLhsTableName, [
                'alias' => $customLhsTableNameAlias,
                'joinType' => 'LEFT',
            ]);

            $join->on()->equalsField("{$lhsTableNameAlias}.id", "{$customLhsTableNameAlias}.id_c");
        }

        return $lhsTableNameAlias;
    }

    /**
     * Check if a module has maps custom fields.
     *
     * @param string $relatedModule
     * @param array $fields
     * @return bool
     */
    protected static function hasMapsCustomFields(string $relatedModule, array $fields): bool
    {
        $seed = BeanFactory::newBean($relatedModule);

        $fieldsMapping = self::getMapsFieldsMapping($fields, $relatedModule);

        foreach ($fieldsMapping as $moduleFieldName) {
            if ($moduleFieldName) {
                $def = $seed->field_defs[$moduleFieldName];

                if (!empty($def['source']) && $def['source'] === 'custom_fields') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return real module fields name
     *
     * @param array $fields
     * @param string $module
     *
     * @return array
     */
    protected static function getMapsFieldsMapping(array $fields, string $module): array
    {
        $fieldsMapping = [];

        $admin = BeanFactory::getBean('Administration');
        $mapsConfig = $admin->retrieveSettings('maps', true)->settings;

        if (!array_key_exists('maps_modulesData', $mapsConfig)) {
            return $fieldsMapping;
        }

        if (!array_key_exists($module, $mapsConfig['maps_modulesData'])) {
            return $fieldsMapping;
        }

        if (!array_key_exists('mappings', $mapsConfig['maps_modulesData'][$module])) {
            return $fieldsMapping;
        }

        $moduleMappings = $mapsConfig['maps_modulesData'][$module]['mappings'];

        foreach ($fields as $field) {
            $realFieldName = str_replace('maps_', '', $field);

            if ($realFieldName !== $field && array_key_exists($realFieldName, $moduleMappings)) {
                $fieldsMapping[$field] = $moduleMappings[$realFieldName];
            }
        }

        return $fieldsMapping;
    }

    /**
     * This function adds a creator filter to the sugar query
     *
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param string $link Which module are you adding the owner filter to.
     */
    protected static function addCreatorFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $link)
    {
        if ($link == '' || $link == '_this') {
            $linkPart = '';
        } else {
            $q->join($link, ['joinType' => 'LEFT']);
            $linkPart = $link . '.';
        }

        $where->equals($linkPart . 'created_by', self::$current_user->id);
    }

    /**
     * Add a guest filter to the query for Meetings and Calls
     *
     * @param SugarQuery $q
     * @param SugarQuery_Builder_Where $where
     * @param $link
     * @throws SugarQueryException
     */
    protected static function addGuestFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $link)
    {
        $tableName = $q->getFromBean()->getTableName();
        $joinKeys = [
            'meetings' => 'meeting_id',
            'calls' => 'call_id',
        ];

        $joinTable = $tableName . '_users';
        $join = $q->joinTable($joinTable, [
            'joinType' => 'LEFT',
        ]);
        $join->on()->queryAnd()
            ->equalsField($tableName . '.id', $joinTable . '.' . $joinKeys[$tableName])
            ->notEquals($joinTable . '.' . 'accept_status', 'decline')
            ->equals($joinTable . '.' . 'deleted', 0);

        $where->equals($joinTable . '.user_id', self::$current_user->id);
    }

    /**
     * This function adds a favorite filter to the sugar query
     *
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param string $link Which module are you adding the favorite filter to.
     */
    protected static function addFavoriteFilter(
        SugarQuery               $q,
        SugarQuery_Builder_Where $where,
        $link,
        $joinType = 'LEFT'
    ) {

        $sfOptions = ['joinType' => $joinType, 'favorites' => true];
        if ($link == '' || $link == '_this') {
            $link_name = 'favorites';
        } else {
            $joinTo = $q->join($link, ['joinType' => 'LEFT']);
            $sfOptions['joinTo'] = $joinTo;
            $sfOptions['joinModule'] = $q->getFromBean()->module_name;
            $link_name = 'sf_' . $link;
        }

        $fjoin = $q->join($link_name, $sfOptions);

        $where->notNull($fjoin->joinName() . '.id');
    }

    protected static function addTrackerFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $interval)
    {
        global $db;

        $td = new SugarDateTime();
        $td->modify($interval);
        $min_date = $td->asDb();

        // Have to do a subselect because MAX() and GROUP BY don't get along with
        // databases other than MySQL
        $join = $q->joinTable(
            '(SELECT t.item_id item_id, MAX(t.date_modified) track_max ' .
            ' FROM tracker t ' .
            ' WHERE t.module_name = ' . $db->quoted($q->from->module_name) . ' ' .
            ' AND t.user_id = ' . $db->quoted($GLOBALS['current_user']->id) . ' ' .
            ' AND t.date_modified >= ' . $db->convert("'$min_date'", 'datetime') . ' ' .
            ' AND t.deleted = 0 ' .
            ' GROUP BY t.item_id)',
            ['alias' => 'tracker']
        );
        $join->on()->equalsField('tracker.item_id', $q->from->getTableName() . '.id');

        if (empty($q->order_by)) {
            // Now, if they want tracker records without specific order, so let's order it by the tracker date_modified
            $q->order_by = [];
            $q->orderByRaw('tracker.track_max', 'DESC');
        }
        $q->distinct(false);
        $q->select()->fieldRaw('tracker.track_max', 'last_viewed_date');
    }

    /**
     * This function adds a from, to, cc, or bcc filter to the sugar query based on the value of `$field`.
     *
     * <code>
     * array(
     *     'filter' => array(
     *         '$from' => array(
     *             array(
     *                 'parent_type' => 'Users',
     *                 'parent_id' => '$current_user_id',
     *             ),
     *             array(
     *                 'parent_type' => 'Contacts',
     *                 'parent_id' => 'fa300a0e-0ad1-b322-9601-512d0983c19a',
     *             ),
     *             array(
     *                 'email_address_id' => 'b0701501-1fab-8ae7-3942-540da93f5017',
     *             ),
     *             array(
     *                 'parent_type' => 'Leads',
     *                 'parent_id' => '73b1087e-4bb6-11e7-acaa-3c15c2d582c6',
     *                 'email_address_id' => 'b651d834-4bb6-11e7-bfcf-3c15c2d582c6',
     *             ),
     *         ),
     *     ),
     * )
     * </code>
     *
     * The above filter definition would return all emails sent by the current user, by the contact whose ID is
     * fa300a0e-0ad1-b322-9601-512d0983c19a, using the email address foo@bar.com, which is referenced by the ID
     * b0701501-1fab-8ae7-3942-540da93f5017, or by the lead whose ID is 73b1087e-4bb6-11e7-acaa-3c15c2d582c6 using the
     * email address biz@baz.com, which is referenced by the ID b651d834-4bb6-11e7-bfcf-3c15c2d582c6. Any number of
     * tuples can be provided in the definition. When the $current_user_id macro is used for the parent_id field, it is
     * swapped for the current user's ID.
     *
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param array $filter
     * @param string $field The filter to use: $from, $to, $cc, or $bcc.
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    protected static function addEmailParticipantFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $filter, $field)
    {
        if (!is_array($filter)) {
            throw new SugarApiExceptionInvalidParameter('$from requires an array');
        }

        static $links = [
            '$from' => 'from',
            '$to' => 'to',
            '$cc' => 'cc',
            '$bcc' => 'bcc',
        ];
        $link = $links[$field];

        $fta = $q->getFromAlias();
        $joinParams = [
            'joinType' => 'LEFT',
        ];
        $join = $q->join($link, $joinParams);
        $jta = $join->joinName();
        $join->on()->equalsField("{$fta}.id", "{$jta}.email_id");
        $or = $where->queryOr();

        foreach ($filter as $def) {
            if (!is_array($def)) {
                throw new SugarApiExceptionInvalidParameter(
                    "definition for {$field} operand is invalid: must be an array"
                );
            }

            // The `parent_type` and `parent_id` fields must be defined if the `email_address_id` isn't. The `parent_id`
            // field must be defined if the `parent_type` is defined. The `parent_type` field must be defined if the
            // `parent_id` field is defined.
            if (!isset($def['email_address_id']) || isset($def['parent_type']) || isset($def['parent_id'])) {
                if (!isset($def['parent_type'])) {
                    throw new SugarApiExceptionInvalidParameter(
                        "definition for {$field} operand is invalid: parent_type is required"
                    );
                }

                if (!isset($def['parent_id'])) {
                    throw new SugarApiExceptionInvalidParameter(
                        "definition for {$field} operand is invalid: parent_id is required"
                    );
                }
            }

            if (isset($def['parent_id']) && $def['parent_id'] === '$current_user_id') {
                $def['parent_id'] = static::$current_user->id;
            }

            $and = $or->queryAnd();
            $and->equals("{$jta}.address_type", $link);

            if (isset($def['email_address_id'])) {
                $and->equals("{$jta}.email_address_id", $def['email_address_id']);
            }

            if (isset($def['parent_type'])) {
                $and->equals("{$jta}.parent_type", $def['parent_type']);
            }

            if (isset($def['parent_id'])) {
                $and->equals("{$jta}.parent_id", $def['parent_id']);
            }
        }
    }

    /**
     * Returns default limit of returned records
     */
    public function getDefaultLimit()
    {
        return $this->defaultLimit;
    }

    /**
     * Returns default record order
     */
    public function getDefaultOrderBy()
    {
        return $this->defaultOrderBy;
    }

    /**
     * Gets relate collection information from a collection of beans
     *
     * @param array $beans Collection of beans to get relate collections from
     * @param array $fields List of fields to check for relate collection information
     * @return array
     */
    protected function getRelatedCollectionOptions(array $beans, array $fields)
    {
        $options = [];
        if (empty($beans) || !is_array($beans)) {
            return $options;
        }
        $bean = $beans[array_key_first($beans)];

        // Do some sanity checking, since some tests might send this array as a
        // simple array of values
        if ($bean instanceof SugarBean) {
            foreach ($bean->field_defs as $def) {
                if ((safeCount($fields) == 0 || safeInArray($def['name'], $fields)) && !empty($def['relate_collection'])) {
                    $options['relate_collections'][$def['name']] = $def;
                    if (!isset($options['module'])) {
                        $options['module'] = $bean->getModuleName();
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Relate collections should be gathered separately from the main filter
     * query. There are multiple records for each row of data and cannot be
     * shown in the select as such.
     *
     * @param $options array of options to use
     * @return mixed the options without any relate collections
     */
    protected function removeRelateCollectionsFromSelect(array $options)
    {
        if (isset($options['select'])) {
            foreach ($options['select'] as $index => $field) {
                if (isset($options['relate_collections'][$field])) {
                    unset($options['select'][$index]);
                }
            }
        }
        return $options;
    }

    /**
     * Returns a result of a COUNT query
     */
    protected function fetchCount(SugarQuery $q): int
    {
        $q->select->selectReset()->setCountQuery();
        $q->orderByReset();
        $q->limit = null;

        $stmt = $q->compile()->execute();

        return (int)$stmt->fetchOne();
    }

    /**
     * Returns a result of a SUM query
     */
    protected function fetchSum(SugarQuery $q, string $field): ?float
    {
        $q->select->getSelectedFieldsByTable($q->from->table_name);

        $q->select->selectReset()->setSumField($field);
        $q->orderByReset();
        $q->limit = null;

        $stmt = $q->compile()->executeQuery();

        return $stmt->fetchOne();
    }

    /**
     * Creat new SugarQuery object
     * @param DBManager $db
     * @return SugarQuery
     */
    protected static function newSugarQuery(DBManager $db)
    {
        return new SugarQuery($db);
    }
}
