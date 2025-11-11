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

class ForecastMetricsEngine
{
    protected $userId;
    protected $isRollup;
    protected $timePeriodId;
    protected $customMetrics;
    protected $forecastConfig;
    protected $resultCache;


    /**
     * Construct
     * {@inheritdoc}
     */
    public function __construct($userId, $isRollup, $timeperiodId)
    {
        $this->initForecastSettings();
        $this->initCustomMetrics();
        $this->setUserId($userId);
        $this->setUserType($isRollup);
        $this->setTimePeriod($timeperiodId);
    }

    /**
     * Init of Forecast settings.
     *
     * @protected
     */
    protected function initForecastSettings()
    {
        $this->forecastConfig = [];
        $config = Forecast::getSettings();
        $this->forecastConfig['closedWonSalesStages'] = $config['sales_stage_won'] ?? [Opportunity::STAGE_CLOSED_WON];
        $this->forecastConfig['closedLostSalesStages'] = $config['sales_stage_lost'] ?? [Opportunity::STAGE_CLOSED_LOST];
        $this->forecastConfig['closedSalesStages'] = array_merge($this->forecastConfig['closedWonSalesStages'], $this->forecastConfig['closedLostSalesStages']);
        $this->forecastConfig['includedCommitStages'] = $config['commit_stages_included'] ?? ['include'];
        $this->forecastConfig['forecastRange'] = $config['forecast_ranges'] ?? 'show_binary';
    }

    /**
     * Init of custom metrics.
     *
     * @protected
     */
    protected function initCustomMetrics()
    {
        $this->customMetrics = [];
        $viewdefManager = new \Sugarcrm\Sugarcrm\MetaData\ViewdefManager();
        $forecastMetricsMeta = $viewdefManager->loadViewdef('base', 'Forecasts', 'forecast-metrics');
        $metrics = $forecastMetricsMeta['forecast-metrics'] ?? [];
        foreach ($metrics as $metric) {
            $this->customMetrics[$metric['name']] = $metric;
        }
    }

    /**
     * Set User Identifier.
     *
     * @param string $userId
     * @public
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set User Type.
     *
     * @param bool $isManager
     * @public
     */
    public function setUserType($isManager)
    {
        $this->isRollup = $isManager;
    }

    /**
     * Set Time Period Identifier.
     *
     * @param string $timePeriodId
     * @public
     */
    public function setTimePeriod($timePeriodId)
    {
        $this->timePeriodId = $timePeriodId;
    }

    /**
     * Get list of metric names.
     *
     * @param array $metricNames
     * @return array
     * @public
     */
    public function getMetrics($metricNames)
    {
        $this->resultCache = [];
        $results = [];
        foreach ($metricNames as $metricName) {
            $results[$metricName] = $this->getMetric($metricName);
        }
        return $results;
    }

    /**
     * Get Metric.
     *
     * @param string $metricName
     * @return array
     * @protected
     */
    protected function getMetric($metricName)
    {
        if (!empty($this->resultCache[$metricName])) {
            return $this->resultCache[$metricName];
        }

        $metricNameCamelCase = ucwords($metricName, '_');
        $metricNameCamelCase = str_replace('_', '', $metricNameCamelCase);
        $methodName = "calculate{$metricNameCamelCase}";

        if (!empty($this->customMetrics[$metricName])) {
            $result = $this->calculateCustomMetric($metricName);
        } elseif (method_exists($this, $methodName)) {
            $result = $this->$methodName();
        } else {
            $result = [
                'type' => 'number',
                'value' => 0,
            ];
        }

        $value = $result['value'] ?? 0;
        if (SugarMath::init($value)->comp(0) === 0) {
            $value = 0;
        }
        $result['value'] = $value;

        $this->resultCache[$metricName] = $result;
        return $result;
    }

    /**
     * Calculate `quota` metric.
     *
     * @return array
     * @public
     */
    public function calculateQuota()
    {
        $result = $this->getRollupQuota();

        return [
            'type' => 'currency',
            'value' => $result['amount'],
        ];
    }

    /**
     * Calculate `commitment` metric.
     *
     * @return array
     * @public
     */
    public function calculateCommitment()
    {
        $seed = BeanFactory::newBean('Forecasts');
        $result = $seed->getCommitment($this->timePeriodId, $this->userId, $this->isRollup);

        return [
            'type' => 'currency',
            'value' => $result['likely_case'],
        ];
    }

    /**
     * Calculate `quota_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateQuotaCoverage()
    {
        $value = 0;
        $quota = $this->getMetric('quota')['value'];

        if ($quota) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $won = $this->getMetric('won')['value'];
            $lost = $this->getMetric('lost')['value'];
            $value = ($pipeline + $won + $lost) / $quota;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `gap_quota` metric.
     *
     * @return array
     * @public
     */
    public function calculateGapQuota()
    {
        $quota = $this->getMetric('quota')['value'];
        $won = $this->getMetric('won')['value'];

        return [
            'type' => 'currency',
            'value' => $quota - $won,
        ];
    }

    /**
     * Calculate `pct_won_quota` metric.
     *
     * @return array
     * @public
     */
    public function calculatePctWonQuota()
    {
        $value = 0;
        $quota = $this->getMetric('quota')['value'];

        if ($quota) {
            $won = $this->getMetric('won')['value'];
            $value = $won / $quota;
        }

        return [
            'type' => 'ratio',
            'value' => $value,
        ];
    }

    /**
     * Calculate `quota_gap_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateQuotaGapCoverage()
    {
        $value = 0;
        $gapQuota = $this->getMetric('gap_quota')['value'];

        if ($gapQuota) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $value = $pipeline / $gapQuota;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `commitment_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateCommitmentCoverage()
    {
        $value = 0;
        $commitment = $this->getMetric('commitment')['value'];

        if ($commitment) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $won = $this->getMetric('won')['value'];
            $lost = $this->getMetric('lost')['value'];
            $value = ($pipeline + $won + $lost) / $commitment;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `gap_commitment` metric.
     *
     * @return array
     * @public
     */
    public function calculateGapCommitment()
    {
        $commitment = $this->getMetric('commitment')['value'];
        $won = $this->getMetric('won')['value'];

        return [
            'type' => 'currency',
            'value' => $commitment - $won,
        ];
    }

    /**
     * Calculate `commitment_gap_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateCommitmentGapCoverage()
    {
        $value = 0;
        $gapCommitment = $this->getMetric('gap_commitment')['value'];

        if ($gapCommitment) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $value = $pipeline / $gapCommitment;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `pct_won_commitment` metric.
     *
     * @return array
     * @public
     */
    public function calculatePctWonCommitment()
    {
        $value = 0;
        $commitment = $this->getMetric('commitment')['value'];

        if ($commitment) {
            $won = $this->getMetric('won')['value'];
            $value = $won / $commitment;
        }

        return [
            'type' => 'ratio',
            'value' => $value,
        ];
    }

    /**
     * Calculate `forecast_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateForecastCoverage()
    {
        $value = 0;
        $forecast = $this->getMetric('forecast_list')['value'];

        if ($forecast) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $won = $this->getMetric('won')['value'];
            $lost = $this->getMetric('lost')['value'];
            $value = ($pipeline + $won + $lost) / $forecast;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `gap_forecast` metric.
     *
     * @return array
     * @public
     */
    public function calculateGapForecast()
    {
        $forecast = $this->getMetric('forecast_list')['value'];
        $won = $this->getMetric('won')['value'];

        return [
            'type' => 'currency',
            'value' => $forecast - $won,
        ];
    }

    /**
     * Calculate `forecast_gap_coverage` metric.
     *
     * @return array
     * @public
     */
    public function calculateForecastGapCoverage()
    {
        $value = 0;
        $gapForecast = $this->getMetric('gap_forecast')['value'];

        if ($gapForecast) {
            $pipeline = $this->getMetric('pipeline')['value'];
            $value = $pipeline / $gapForecast;
        }

        return [
            'type' => 'float',
            'value' => $value,
        ];
    }

    /**
     * Calculate `pct_won_forecast` metric.
     *
     * @return array
     * @public
     */
    public function calculatePctWonForecast()
    {
        $value = 0;
        $forecast = $this->getMetric('forecast_list')['value'];

        if ($forecast) {
            $won = $this->getMetric('won')['value'];
            $value = $won / $forecast;
        }

        return [
            'type' => 'ratio',
            'value' => $value,
        ];
    }

    /**
     * Calculate `forecast_list` metric.
     *
     * @return array
     * @public
     */
    public function calculateForecastList()
    {
        $meta = [
            'name' => 'forecast_list',
            'sum_fields' => 'forecasted_likely',
            'result_type' => 'currency',
            'filter' => [
                [
                    'commit_stage' => [
                        '$in' => $this->forecastConfig['includedCommitStages'],
                    ],
                ],
            ],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `pipeline` metric.
     *
     * @return array
     * @protected
     */
    protected function calculatePipeline()
    {
        $value = $this->getMetric('included_pipeline')['value'] + $this->getMetric('excluded_pipeline')['value'];
        if ($this->forecastConfig['forecastRange'] === 'show_buckets') {
            $value += $this->getMetric('upside_pipeline')['value'];
        }

        return [
            'type' => 'currency',
            'value' => $value,
        ];
    }

    /**
     * Calculate `included_pipeline` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateIncludedPipeline()
    {
        $meta = [
            'name' => 'included_pipeline',
            'sum_fields' => 'forecasted_likely',
            'result_type' => 'currency',
            'filter' => [
                [
                    'commit_stage' => [
                        '$in' => $this->forecastConfig['includedCommitStages'],
                    ],
                    'sales_stage' => [
                        '$not_in' => $this->forecastConfig['closedSalesStages'],
                    ],
                ],
            ],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `included_pipeline` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateUpsidePipeline()
    {
        $meta = [
            'name' => 'upside_pipeline',
            'sum_fields' => 'amount',
            'result_type' => 'currency',
            'filter' => [
                [
                    'commit_stage' => [
                        '$equals' => 'upside',
                    ],
                    'sales_stage' => [
                        '$not_in' => $this->forecastConfig['closedSalesStages'],
                    ],
                ],
            ],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `excluded_pipeline` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateExcludedPipeline()
    {
        $meta = [
            'name' => 'excluded_pipeline',
            'sum_fields' => 'amount',
            'result_type' => 'currency',
        ];

        if ($this->forecastConfig['forecastRange'] === 'show_buckets') {
            $meta['filter'] = [
                [
                    'commit_stage' => [
                        '$equals' => 'exclude',
                    ],
                    'sales_stage' => [
                        '$not_in' => $this->forecastConfig['closedSalesStages'],
                    ],
                ],
            ];
        } else {
            $meta['filter'] = [
                [
                    'commit_stage' => [
                        '$not_in' => $this->forecastConfig['includedCommitStages'],
                    ],
                    'sales_stage' => [
                        '$not_in' => $this->forecastConfig['closedSalesStages'],
                    ],
                ],
            ];
        }
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `won` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateWon()
    {
        $meta = [
            'name' => 'won',
            'sum_fields' => 'amount',
            'result_type' => 'currency',
            'filter' => [
                [
                    'sales_stage' => [
                        '$in' => $this->forecastConfig['closedWonSalesStages'],
                    ],
                ],
            ],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `lost` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateLost()
    {
        $meta = [
            'name' => 'lost',
            'sum_fields' => 'lost',
            'result_type' => 'currency',
            'filter' => [
                [
                    'sales_stage' => [
                        '$in' => $this->forecastConfig['closedLostSalesStages'],
                    ],
                ],
            ],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Calculate `all` metric.
     *
     * @return array
     * @protected
     */
    protected function calculateAll()
    {
        $meta = [
            'name' => 'all',
            'sum_fields' => ['amount', 'lost'],
            'result_type' => 'currency',
            'filter' => [],
        ];
        return $this->processOpportunitiesSumMetric($meta);
    }

    /**
     * Retrieve a user's quota using the rollup value, if available.
     *
     * @return array [currency_id => int, amount => number, formatted_amount => String]
     */
    protected function getRollupQuota()
    {
        $seed = BeanFactory::newBean('Quotas');
        return $seed->getRollupQuota($this->timePeriodId, $this->userId, $this->isRollup);
    }

    /**
     * Calculate custom metric.
     *
     * @param string $metricName
     * @return object | null
     * @protected
     */
    protected function calculateCustomMetric($metricName)
    {
        $metric = $this->customMetrics[$metricName] ?? null;
        if ($metric) {
            return $this->processOpportunitiesSumMetric($metric);
        }
        return null;
    }

    /**
     * Process Opportunities sum metric.
     *
     * @param object $metric
     * @return array
     * @protected
     */
    protected function processOpportunitiesSumMetric($metric)
    {
        if (!isset($metric['sum_fields']) && isset($metric['sumFields'])) {
            $metric['sum_fields'] = $metric['sumFields'];
        }
        $api = new RestService();
        $api->user = $GLOBALS['current_user'];
        $metricsApi = new ForecastMetricsApi();
        $result = $metricsApi->getMetrics($api, [
            'module' => 'Opportunities',
            'user_id' => $this->userId,
            'time_period' => $this->timePeriodId,
            'type' => $this->isRollup ? 'Rollup' : 'Direct',
            'metrics' => [$metric],
        ]);
        return [
            'type' => $metric['result_type'] ?? 'currency',
            'value' => $result['metrics'][$metric['name']]['values']['sum'] ?? 0,
        ];
    }
}
