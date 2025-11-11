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

namespace Sugarcrm\Sugarcrm\Reports\Charts\Types;

class LineChart extends BaseChart
{
    /**
     * @inheritdoc
     */
    public function transformData()
    {
        $labels = $this->getLabels($this->data);

        $datasets = [];
        foreach ($this->data['values'] as $index => $value) {
            $datasets [] = [
                'label' => $value['label'],
                'fill' => false,
                'borderColor' => $this->getColor($index),
                'pointRadius' => 3,
                'backgroundColor' => $this->getColor($index),
                'data' => $value['values'],
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $props = $this->getProperties();
        $reportDef = $this->getReportDef();
        $options = parent::getOptions();

        $options['scales'] = [
            'x' => [
                'title' => [
                    'display' => $this->showXLabel,
                    'text' => $this->getXAxisLabel($props, ['chartType' => 'line chart'], $reportDef),
                    'color' => $this->getTextColor(),
                ],
                'grid' => [
                    'display' => true,
                    'drawBorder' => true,
                    'drawOnChartArea' => true,
                    'drawTicks' => false,
                    'color' => $this->getAxisColors(),
                ],
            ],
            'y' => [
                'title' => [
                    'display' => $this->showYLabel,
                    'text' => $this->getYAxisLabel($reportDef),
                    'color' => $this->getTextColor(),
                ],
                'grid' => [
                    'display' => true,
                    'drawBorder' => true,
                    'drawOnChartArea' => true,
                    'drawTicks' => false,
                    'color' => $this->getAxisColors(),
                ],
            ],
        ];

        $options['plugins']['legend']['labels']['usePointStyle'] = true;
        $options['plugins']['legend']['labels']['pointStyle'] = 'circle';

        return $options;
    }
}
