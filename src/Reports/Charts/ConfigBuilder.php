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

namespace Sugarcrm\Sugarcrm\Reports\Charts;

use Report;

class ConfigBuilder
{
    /** @var array */
    public $chartData;
    public $reporter;
    /**
     * @var mixed|mixed[]
     */
    public $chartType;
    public $reportDef;
    /**
     * @var \Sugarcrm\Sugarcrm\Reports\Charts\Types\BaseChart|null|mixed
     */
    public $chart;
    // map chartjs types with our chart types
    private $typeMap = [
        'treemapF' => 'treemap',
        'pieF' => 'pie',
        'donutF' => 'doughnut',
        'funnelF' => 'funnel',
        'hBarF' => 'bar',
        'hGBarF' => 'bar',
        'vBarF' => 'bar',
        'vGBarF' => 'bar',
        'lineF' => 'line',
    ];
    private $config = [];

    /**
     * Config builder constructor
     *
     * @param array $chartData
     * @param Report $reporter
     */
    public function __construct(array $chartData, Report $reporter)
    {
        $this->chartData = $chartData;
        $this->reporter = $reporter;
        $this->chartType = $this->reporter->chart_type;
        $this->reportDef = $this->reporter->report_def;
        $this->chart = ChartFactory::getChart($this->chartType, $this->chartData, $this->reportDef);
    }

    /**
     * Setup all the necessary stuff for a chart config
     */
    public function build()
    {
        $chartOptions = $this->chart->getOptions();

        $this->config['type'] = $this->typeMap[$this->chartType];
        $this->config['data'] = $this->chart->transformData();
        $this->config['options'] = $chartOptions;
        $this->config['plugins'] = $chartOptions['plugins'];
    }

    /**
     * Returns the config
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
