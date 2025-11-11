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

class ForecastsOpportunityApi extends FilterApi
{
    public function registerApiRest()
    {
        $parentApi = [
            'filterForecastOpportunities' => [
                'reqType' => 'GET',
                'path' => ['forecastOpportunities'],
                'pathVars' => [],
                'method' => 'getForecastOpportunities',
                'shortHelp' => 'Gets a list of Opportunities in the requested Forecast',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastOpportunityFilterGetAPI.html',
            ],
            'filterForecastOpportunitiesPost' => [
                'reqType' => 'POST',
                'path' => ['forecastOpportunities'],
                'pathVars' => [],
                'method' => 'getForecastOpportunities',
                'shortHelp' => 'Gets a list of Opportunities in the requested Forecast',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastOpportunityFilterPostAPI.html',
            ],
            'filterForecastOpportunitiesCount' => [
                'reqType' => 'GET',
                'path' => ['forecastOpportunities', 'count'],
                'pathVars' => [],
                'method' => 'getForecastOpportunitiesCount',
                'shortHelp' => 'Gets a count of Opportunities in the requested Forecast',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastOpportunityCountAPI.html',
            ],
            'filterForecastOpportunitiesCountPost' => [
                'reqType' => 'POST',
                'path' => ['forecastOpportunities', 'count'],
                'pathVars' => [],
                'method' => 'getForecastOpportunitiesCount',
                'shortHelp' => 'Gets a count of Opportunities in the requested Forecast',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastOpportunityCountAPI.html',
            ],
        ];
        return $parentApi;
    }

    /**
     * Returns a list of opportunities used for forecast list view.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array The REST response as a PHP array.
     */
    public function getForecastOpportunities(ServiceBase $api, array $args, $acl = 'list')
    {
        $api->action = 'list';
        $args['module'] = 'Opportunities';
        return parent::runQuery($api, ...$this->forecastOpportunityFilter($api, $args, $acl));
    }

    /**
     * Returns a count of opportunities used for forecast list view.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array The REST response as a PHP array.
     */
    public function getForecastOpportunitiesCount(ServiceBase $api, array $args)
    {
        $api->action = 'list';
        $args['module'] = 'Opportunities';
        /** @var SugarQuery $q */
        [, $q, $options] = $this->forecastOpportunityFilter($api, $args);

        $q = $options['id_query'] ?? $q;

        return [
            'record_count' => $this->fetchCount($q),
        ];
    }

    /**
     * Preprocess the args array to add the filter criteria used specifically
     * in the forecast's opportunity list view
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
    private function forecastOpportunityFilter(ServiceBase $api, array $args, $acl = 'list')
    {
        $filterSetup = parent::filterListSetup($api, $args, $acl);
        $options = $filterSetup[2];
        $query = $filterSetup[1];

        //We need to add the filter to this query to this to handle view
        //api parameters
        if (isset($options['id_query'])) {
            $options['id_query'] = $this->addForecastFilter($api, $args, $options['id_query']);
        }

        $filterSetup[1] = $this->addForecastFilter($api, $args, $query);
        return $filterSetup;
    }

    /**
     * Adds the forecast Filter parameters to the filter generated by the
     * FilterApi
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param SugarQuery $query The query the filter is going to be added to.
     * @return SugarQuery The query with the additional filters applied.
     * @throws SugarApiExceptionNotFound
     */
    private function addForecastFilter(ServiceBase $api, array $args, SugarQuery $query)
    {
        $forecastUserId = $args['user_id'];
        $forecastUserType = $args['type'];
        $forecastTimePeriodId = $args['time_period'];

        $admin = BeanFactory::getBean('Administration', null);
        $forecastsSettings = $admin->getConfigForModule('Forecasts', 'base', true);

        if (is_null($forecastTimePeriodId)) {
            $forecastTimePeriodId = TimePeriod::getCurrentId();
        }

        $timePeriod = BeanFactory::retrieveBean('TimePeriods', $forecastTimePeriodId);

        if (!$timePeriod) {
            $timePeriod = TimePeriod::getCurrentTimePeriod($forecastsSettings['timeperiod_leaf_interval']);
        }

        $query->where()->between('date_closed', $timePeriod->start_date, $timePeriod->end_date);

        if ($forecastUserType == 'Rollup') {
            $reportees = Forecast::getAllReporteesIds([$forecastUserId], 0);
            $reportees[] = $forecastUserId;
            $query->where()->in('assigned_user_id', $reportees);
        } else {
            $query->where()->equals('assigned_user_id', $forecastUserId);
        }

        return $query;
    }
}
