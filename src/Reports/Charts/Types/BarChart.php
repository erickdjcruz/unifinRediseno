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

class BarChart extends BaseChart
{
    /** @var int */
    public $minDatapointThickness;
    // Maximum thickness a bar will render as in pixels
    private $maxBarThickness = 35;
    // Percentage of how much space a data point takes up in its axis container
    private $categoryPercentage = 0.8;
    // Percentage of how much space a bar takes up in its category container
    private $barPercentage = 0.9;

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $props = $this->getProperties();
        $reportDef = $this->getReportDef();

        $config = $this->getConfig($props);

        $config['y_axis_label'] = $this->getYAxisLabel($reportDef);
        $config['x_axis_label'] = $this->getXAxisLabel($props, $config, $reportDef);
        $config['stacked'] = $config['barType'] === 'stacked' || $config['barType'] === 'basic' ? true : false;

        $this->minDatapointThickness = $config['orientation'] === 'horizontal' ? 15 : 30;

        $options = parent::getOptions();
        $options['indexAxis'] = $config['orientation'] === 'horizontal' ? 'y' : 'x';
        $options['maintainAspectRatio'] = false;
        $options['scales'] = [
            'x' => [
                'title' => [
                    'display' => $config['orientation'] === 'horizontal' ? $this->showYLabel : $this->showXLabel,
                    'text' => $config['orientation'] === 'horizontal'
                        ? $config['y_axis_label'] : $config['x_axis_label'],
                    'color' => $this->getTextColor(),
                ],
                'stacked' => $config['stacked'],
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
                    'display' => $config['orientation'] === 'horizontal' ? $this->showXLabel : $this->showYLabel,
                    'text' => $config['orientation'] === 'horizontal'
                        ? $config['x_axis_label'] : $config['y_axis_label'],
                    'color' => $this->getTextColor(),
                ],
                'stacked' => $config['stacked'],
                'grid' => [
                    'display' => true,
                    'drawBorder' => true,
                    'drawOnChartArea' => true,
                    'drawTicks' => false,
                    'color' => $this->getAxisColors(),
                ],
            ],
        ];
        $options['plugins']['datalabels'] = [
            'display' => $this->shouldDisplayDataLabels($config),
            'color' => $this->getDataLabelsColor(),
            'anchor' => $this->getLabelAnchorValue($config),
            'align' => $this->getLabelAlignValue($config),
            'rotation' => $this->getLabelRotationValue($config),
            'padding' => 4,
            'format' => $props['yDataType'],
            'formatCurrencyDecimals' => 2,
        ];
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function transformData()
    {
        $config = $this->getConfig($this->getProperties());
        $barType = $config['barType'];
        $labels = $this->getLabels($this->data);

        $datasets = [];

        if (($barType === 'grouped' || $barType === 'stacked') && !$this->isSingleDataset($this->data)) {
            $labels = array_map(
                function ($item) {
                    return $item['label'];
                },
                $this->data['values']
            );

            foreach ($this->data['label'] as $index => $label) {
                if ($label === '') {
                    $label = translate('LBL_CHART_NO_LABEL');
                }
                $datasets [] = [
                    'categoryPercentage' => $this->categoryPercentage,
                    'barPercentage' => $this->barPercentage,
                    'maxBarThickness' => $this->maxBarThickness,
                    'label' => $label,
                    'backgroundColor' => array_fill(0, safeCount($this->data['values'][0]), $this->getColor($index)),
                    'data' => $this->getSeriesData($this->data['values'], $index),
                ];
            }
        } else {
            foreach ($this->getLabels($this->data) as $index => $label) {
                if ($index === 0) {
                    $datasets [] = [
                        'categoryPercentage' => $this->categoryPercentage,
                        'barPercentage' => $this->barPercentage,
                        'maxBarThickness' => $this->maxBarThickness,
                        'label' => $label,
                        'backgroundColor' => $this->buildBackgroundColors(),
                        'data' => $this->getData($this->data['values']),
                    ];
                } else {
                    $datasets [] = [
                        'label' => $label,
                        'backgroundColor' => $this->buildBackgroundColors(),
                    ];
                }
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * Get some config options based on bar type
     *
     * @param array $props
     * @return array
     */
    private function getConfig($props): array
    {
        $chartConfig = [];

        switch ($props['type']) {
            case 'stacked group by chart':
                $chartConfig = [
                    'orientation' => 'vertical',
                    'barType' => 'stacked',
                    'chartType' => 'group by chart',
                ];
                break;

            case 'group by chart':
                $chartConfig = [
                    'orientation' => 'vertical',
                    'barType' => 'grouped',
                    'chartType' => 'group by chart',
                ];
                break;

            case 'bar chart':
                $chartConfig = [
                    'orientation' => 'vertical',
                    'barType' => 'basic',
                    'chartType' => 'group by chart',
                ];
                break;

            case 'horizontal group by chart':
                $chartConfig = [
                    'orientation' => 'horizontal',
                    'barType' => 'stacked',
                    'chartType' => 'horizontal group by chart',
                ];
                break;

            case 'horizontal bar chart':
            case 'horizontal':
                $chartConfig = [
                    'orientation' => 'horizontal',
                    'barType' => 'basic',
                    'chartType' => 'horizontal group by chart',
                ];
                break;

            case 'horizontal grouped bar chart':
                $chartConfig = [
                    'orientation' => 'horizontal',
                    'barType' => 'grouped',
                    'chartType' => 'horizontal group by chart',
                ];
                break;
            case 'vertical grouped bar chart':
                $chartConfig = [
                    'orientation' => 'vertical',
                    'barType' => 'grouped',
                    'chartType' => 'group by chart',
                ];
                break;

            default:
                $chartConfig = [
                    'orientation' => 'vertical',
                    'barType' => 'stacked',
                    'chartType' => 'bar chart',
                ];
                break;
        }

        return $chartConfig;
    }

    /**
     * Get label anchor value
     *
     * @param array $config
     * @return null|string
     */
    private function getLabelAnchorValue($config): ?string
    {
        $anchorMap = [
            '1' => 'end',
            'start' => 'start',
            'middle' => 'center',
            'end' => 'end',
        ];
        if (isset($config['showValues'])) {
            return $anchorMap[$config['showValues']];
        }

        return 'center';
    }

    /**
     * Get label align value
     * @param array $config
     * @return null|string
     */
    private function getLabelAlignValue($config): ?string
    {
        $anchorMap = [
            '1' => 'start',
            'start' => 'end',
            'middle' => 'center',
            'end' => 'start',
            'top' => $config['stacked'] ? 'start' : 'end',
        ];
        if (isset($config['showValues'])) {
            return $anchorMap[$config['showValues']];
        }

        return 'center';
    }

    /**
     * Get label rotation value
     *
     * @param mixed $config
     * @return int
     */
    private function getLabelRotationValue($config): int
    {
        return $config['barType'] === 'grouped' && $config['orientation'] !== 'horizontal' ?
            -90 : 0;
    }

    /**
     * Is a single dataset?
     *
     * @param mixed $data
     * @return bool
     */
    private function isSingleDataset($data): bool
    {
        return $data['values'] && $data['values'][0] && safeCount($data['values'][0]['values']) === 1;
    }

    /**
     * If it should display data labels
     *
     * @param mixed $config
     * @return bool
     */
    private function shouldDisplayDataLabels($config): bool
    {
        // do not display datalables on v1
        return false;

        // let's show data labels only for grouped horizontal bars
        // this will be implemented at a later time
        if ($config['chartType'] === 'horizontal group by chart' && $config['barType'] !== 'stacked') {
            return true;
        }
    }
}
