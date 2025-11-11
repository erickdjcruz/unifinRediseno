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

use Sugarcrm\Sugarcrm\Entitlements\Subscription;

class ForecastMetricsApi extends FilterApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'forecasts_metrics' => [
                'reqType' => 'POST',
                'path' => ['Forecasts', 'metrics'],
                'pathVars' => ['', ''],
                'method' => 'getMetrics',
                'shortHelp' => 'Retrieve metrics for Forecast data',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastMetricsApiPost.html',
            ],
            'forecasts_metrics_named' => [
                'reqType' => 'POST',
                'path' => ['Forecasts', 'metrics', 'named'],
                'pathVars' => ['', '', ''],
                'method' => 'getMetricsByName',
                'shortHelp' => 'Retrieve named metrics for Forecast data',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastMetricsByNameApiPost.html',
                'minVersion' => '11.20',
            ],
        ];
    }

    /**
     * Returns metrics by name for a filtered list of Forecasts-related data
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array[]
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getMetricsByName(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['user_id', 'type', 'metrics']);
        $engine = new ForecastMetricsEngine(
            $args['user_id'],
            ($args['type'] === 'Rollup'),
            $args['time_period'] ?? TimePeriod::getCurrentId()
        );
        return $engine->getMetrics($args['metrics']);
    }

    /**
     * Returns metrics for a filtered list of Forecasts-related data
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array[]
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getMetrics(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, [
            'module',
            'user_id',
            'type',
            'metrics',
        ]);

        $metrics = $this->parseMetrics($args);
        return [
            'metrics' => $this->runMetrics($metrics),
        ];
    }

    /**
     * Parses API arguments to build a list of metrics in a standard format
     *
     * @param $args
     * @return array
     */
    protected function parseMetrics($args)
    {
        $metrics = [];

        $module = $args['module'] ?? '';
        $baseFilter = $this->parseBaseFilter($args);
        foreach ($args['metrics'] as $metric) {
            $metrics[] = $this->parseMetric($metric, $module, $baseFilter);
        }

        return $metrics;
    }

    /**
     * Formats arguments for a metric into a standard format
     *
     * @param array $metric the metric data to format
     * @param string $module the focus module of the metric
     * @param array $baseFilter base filter applied to all metrics in the request
     * @return array
     */
    protected function parseMetric($metric, $module, $baseFilter)
    {
        $parsedMetric = [];

        $parsedMetric['name'] = $metric['name'] ?? '';
        $parsedMetric['module'] = $module;
        $parsedMetric['filter'] = array_merge($baseFilter, $metric['filter']);

        $sumFields = $metric['sum_fields'] ?? [];
        $parsedMetric['sum_fields'] = is_array($sumFields) ? $sumFields : [$sumFields];

        // For summing up currency fields, we need to know if the currency
        // needs to be converted to the system currency or not
        $parsedMetric['currencyConvertFields'] = [];
        foreach ($parsedMetric['sum_fields'] as $sumField) {
            if ($this->isCurrencyConvertField($module, $sumField)) {
                $parsedMetric['currencyConvertFields'][] = $sumField;
            }
        }
        $parsedMetric['systemBaseRate'] = SugarCurrency::getBaseCurrency()->conversion_rate;

        return $parsedMetric;
    }

    /**
     * Parses the base filter from API arguments that applies to all metrics
     * being generated
     *
     * @param array $args request arguments
     * @return array[]
     */
    protected function parseBaseFilter(array $args): array
    {
        $baseFilter = $args['filter'] ?? [];
        $assignedToFilter = $this->buildBaseAssigneeFilter($args['user_id'], $args['type']);
        $dateClosedFilter = $this->buildBaseTimePeriodFilter($args['time_period'] ?? null);
        array_push($baseFilter, $assignedToFilter, $dateClosedFilter);

        return $baseFilter;
    }

    /**
     * Builds the assigned_to filter from args
     *
     * @param string $userId the ID of the forecast user
     * @param string $userType the type of the forecast user
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    protected function buildBaseAssigneeFilter($userId, $userType)
    {
        $filter = [];

        if ($userType === 'Rollup') {
            $reportees = Forecast::getAllReporteesIds([$userId], 0);
            $reportees[] = $userId;
            $filter['assigned_user_id'] = [
                '$in' => $reportees,
            ];
        } else {
            $filter['assigned_user_id'] = [
                '$equals' => $userId,
            ];
        }

        return $filter;
    }

    /**
     * Builds the date_closed filter from args
     *
     * @param string $timePeriodId the ID of the time period the metrics should be confined to
     * @return array
     */
    protected function buildBaseTimePeriodFilter($timePeriodId)
    {
        $filter = [];

        if (empty($timePeriodId)) {
            $timePeriodId = TimePeriod::getCurrentId();
        }

        $timePeriodBean = BeanFactory::retrieveBean('TimePeriods', $timePeriodId);
        if (!empty($timePeriodBean)) {
            $startDate = $timePeriodBean->start_date;
            $endDate = $timePeriodBean->end_date;

            $filter['date_closed'] = [
                '$gte' => $startDate,
                '$lte' => $endDate,
            ];
        }

        return $filter;
    }

    /**
     * Checks whether a field is a currency field that is not locked to the
     * system default base rate
     *
     * @param string $module the module of the field
     * @param string $fieldName the name of the field
     * @return bool
     */
    protected function isCurrencyConvertField($module, $fieldName)
    {
        $bean = BeanFactory::newBean($module);
        $def = $bean->getFieldDefinition($fieldName);

        $type = $def['type'];
        if (isset($def['custom_type']) && !empty($def['custom_type'])) {
            $type = $def['custom_type'];
        } elseif (isset($def['dbType']) && !empty($def['dbType'])) {
            $type = $def['dbType'];
        }

        $isBaseCurrency = isset($def['is_base_currency']) && isTruthy($def['is_base_currency']);

        return strtolower($type) === 'currency' && !$isBaseCurrency;
    }

    /**
     * Runs the queries required to gather data for each of the metrics
     *
     * @param array $metrics the list of metrics to collect
     * @return array the results of all metrics
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    protected function runMetrics($metrics)
    {
        $results = [];

        // For each in metrics, build a query on top of the base filter that adds the additional filter and sums from sumField
        foreach ($metrics as $metric) {
            $metricName = $metric['name'];
            $results[$metricName] = [
                'name' => $metricName,
                'values' => $this->runMetric($metric),
            ];
        }

        return $results;
    }

    /**
     * Runs the query required to gather data for the given metric
     *
     * @param array $metric the metric to collect
     * @return array the metric result
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    protected function runMetric($metric)
    {
        $module = $metric['module'];
        $seed = BeanFactory::newBean($module);
        $systemBaseRate = $metric['systemBaseRate'];

        $results = [];
        if (!empty($seed)) {
            $query = new SugarQuery();
            $query->from($seed);

            // Build the select. If we need to convert to the base currency
            // rate, we can do that here to be more performant
            $query->select()->fieldRaw('COUNT(*)', 'metric_count');
            foreach ($metric['sum_fields'] as $sumField) {
                $validSumField = $query->getDBManager()->getValidDBName($sumField);

                if (safeInArray($validSumField, $metric['currencyConvertFields'])) {
                    $query->select()->fieldRaw("SUM($validSumField / base_rate) * $systemBaseRate", $validSumField);
                } else {
                    $query->select()->fieldRaw("SUM($validSumField)", $validSumField);
                }
            }

            // Apply the filters
            self::addFilters($metric['filter'], $query->where(), $query);

            // Execute the query and update the results to return
            $results = $this->formatMetricResults($query->execute()[0], $metric);
        }

        return $results;
    }

    /**
     * Takes the results for the given metric query and formats it into the
     * data to be returned from the API
     *
     * @param array $results the raw result columns
     * @param array $metric the formatted metric results array
     * @return array
     */
    protected function formatMetricResults($results, $metric)
    {
        $data = [];

        // Now calculate the sum
        $sum = 0;
        foreach ($metric['sum_fields'] as $sumField) {
            $sum = SugarMath::init($sum)->add($results[$sumField])->result();
        }
        $data['sum'] = floatval($sum);
        $data['count'] = $results['metric_count'];

        // Format the query results and return them
        return $data;
    }
}
