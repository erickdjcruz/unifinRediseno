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


class DashboardListApi extends FilterApi
{
    protected static $mandatory_fields = [
        'id',
        'name',
        'view_name',
    ];

    /**
     * Rest Api Registration Method
     *
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getDashboardsForModule' => [
                'reqType' => 'GET',
                'minVersion' => '10',
                'maxVersion' => '10',
                'path' => ['Dashboards', '<module>'],
                'pathVars' => ['', 'module'],
                'method' => 'getDashboards',
                'shortHelp' => 'Get dashboards for a module',
                'longHelp' => 'include/api/help/get_dashboards.html',
                'cacheEtag' => true,
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionError',
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'getDashboardsForHome' => [
                'reqType' => 'GET',
                'minVersion' => '10',
                'maxVersion' => '10',
                'path' => ['Dashboards'],
                'pathVars' => [''],
                'method' => 'getDashboards',
                'shortHelp' => 'Get dashboards for home',
                'longHelp' => 'include/api/help/get_home_dashboards.html',
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionError',
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'getDashboardsForActivities' => [
                'reqType' => 'GET',
                'minVersion' => '10',
                'maxVersion' => '10',
                'path' => ['Dashboards', 'Activities'],
                'pathVars' => ['', 'module'],
                'method' => 'getDashboards',
                'shortHelp' => 'Get dashboards for activity stream',
                'longHelp' => 'include/api/help/get_activities_dashboards.html',
                'cacheEtag' => true,
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionError',
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'filterModuleAllCount' => [
                'reqType' => 'GET',
                'path' => ['Dashboards', 'count'],
                'pathVars' => ['module', ''],
                'jsonParams' => ['filter'],
                'method' => 'getFilterListCount',
                'minVersion' => '11.24',
                'shortHelp' => 'Counts all filtered dashboards',
                'longHelp' => 'include/api/help/dashboards_filter_count_get_help.html',
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
        ];
    }

    /**
     * Get the dashboards for the current user
     *
     * 'view' is deprecated because it's reserved db word.
     * Some old API (before 7.2.0) can use 'view'.
     * Because of that API will use 'view' as 'view_name' if 'view_name' isn't present.
     *
     * @param ServiceBase $api The Api Class
     * @param array $args Service Call Arguments
     * @return mixed
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function getDashboards(ServiceBase $api, array $args)
    {
        if (empty($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }

        // Tack on some required filters.
        $module = empty($args['module']) ? 'Home' : $args['module'];
        $args['filter'][]['dashboard_module'] = $module;

        $args['module'] = 'Dashboards';

        if (isset($args['view']) && !isset($args['view_name'])) {
            $args['view_name'] = $args['view'];
        }

        if (!empty($args['view_name'])) {
            $args['filter'][]['view_name'] = $args['view_name'];
        }
        $args['fields'] = 'id,name,view_name';

        $ret = $this->filterList($api, $args);

        // Add dashboard URL's
        foreach ($ret['records'] as $idx => $dashboard) {
            $ret['records'][$idx]['url'] = $api->getResourceURI('Dashboards/' . $dashboard['id']);
        }

        return $ret;
    }

    /**
     * Redefine the getoptions to pull in the correct Dashboard filters
     */
    protected function parseArguments(ServiceBase $api, array $args, SugarBean $seed = null)
    {
        if (!isset($args['order_by'])) {
            $args['order_by'] = 'date_entered:DESC';
        }
        $options = parent::parseArguments($api, $args, $seed);

        return $options;
    }

    /**
     *
     * The view parameter (in combination with view_name) is already in
     * use for Dashboards. The field list is never created from a
     * viewdef for dashboards anyway so we should remove it.
     *
     * @see SugarApi::getFieldsFromArgs()
     */
    protected function getFieldsFromArgs(
        ServiceBase $api,
        array       $args,
        SugarBean   $bean = null,
        $viewName = 'view',
        &$displayParams = []
    ) {

        if (isset($args['view'])) {
            unset($args['view']);
        }
        return parent::getFieldsFromArgs($api, $args, $bean, $viewName, $displayParams);
    }

    /**
     * Returns the number of records for the Dashboards module and filter provided:
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array The number of filtered/unfiltered records for the Dashboards module.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter if any of the parameters are
     *  invalid.
     * @throws SugarApiExceptionNotAuthorized if we lack ACL access.
     */
    public function getFilterListCount(ServiceBase $api, array $args)
    {
        $api->action = 'list';

        /** @var SugarQuery $q */
        [$args, $q, $options, $seed] = parent::filterListSetup($api, $args);

        $q = $options['id_query'] ?? $q;

        return [
            'record_count' => $this->fetchCountWithPreQueryProcessing($q, $options, $seed),
        ];
    }

    /**
     * Returns the result of a COUNT query  while taking into account any before_filter hooks
     * This will check for platform. If platform is not portal it will filter the portal Dashboard and decrese the count
     *
     * This overrides the fetchCount function from FilterApi and uses a different signature
     * @see FilterApi::fetchCount()
     *
     * @param SugarQuery $q
     * @param array $options
     * @param SugarBean|null $seed
     * @return int The count of records.
     */
    public function fetchCountWithPreQueryProcessing(SugarQuery $q, array $options, SugarBean $seed = null): int
    {
        if (isset($seed)) {
            $seed->call_custom_logic('before_filter', [$q, $options]);
        }

        return parent::fetchCount($q);
    }
}
