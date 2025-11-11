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

use Sugarcrm\Sugarcrm\Reports\Charts\Types\BarChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\BaseChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\DoughnutChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\FunnelChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\LineChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\PieChart;
use Sugarcrm\Sugarcrm\Reports\Charts\Types\TreemapChart;

class ChartFactory
{
    /**
     * Get the type of Chart class
     *
     * @param string $type
     * @param array $data
     * @param array $reportDef
     * @return BaseChart
     */
    public static function getChart(string $type, array $data, array $reportDef): ?BaseChart
    {
        $chart = null;
        switch ($type) {
            case 'treemapF':
                $chart = new TreemapChart($data);
                break;
            case 'pieF':
                $chart = new PieChart($data);
                break;
            case 'donutF':
                $chart = new DoughnutChart($data);
                break;
            case 'funnelF':
                $chart = new FunnelChart($data);
                break;
            case 'hBarF':
            case 'hGBarF':
            case 'vBarF':
            case 'vGBarF':
                $chart = new BarChart($data);
                break;
            case 'lineF':
                $chart = new LineChart($data);
                break;
            default:
                $chart = new BaseChart($data);
        }

        // used in BarChart
        $chart->setReportDef($reportDef);

        return $chart;
    }
}
