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

use Sugarcrm\Sugarcrm\Reports\Charts\ChartConfigInterface;

class BaseChart implements ChartConfigInterface
{
    /** @var array */
    public $data;
    public $reportDef;
    protected $showYLabel = true; //@todo check where this is coming from

    protected $showXLabel = true; //@todo check where this is coming from

    // init the base color list
    protected $baseColorList = [];
    // The chart default color list. Got from the chartjs plugin.
    protected $defaultColorList = [
        '#517bf8', // @ocean
        '#36b0ff', // @pacific
        '#00e0e0', // @teal
        '#00ba83', // @green
        '#6cdf46', // @army
        '#ffd132', // @yellow
        '#ff9445', // @orange
        '#fa374f', // @red
        '#f476b1', // @coral
        '#cd74f2', // @pink
        '#8f5ff5', // @purple
        '#29388c', // @darkOcean
        '#145c95', // @darkPacific
        '#00636e', // @darkTeal
        '#056f37', // @darkGreen
        '#537600', // @darkArmy
        '#866500', // @darkYellow
        '#9b4617', // @darkOrange
        '#bb0e1b', // @darkRed
        '#a23354', // @darkCoral
        '#832a83', // @darkPink
        '#4c2d85', // @darkPurple
        '#c6ddff', // @lightOcean
        '#c0edff', // @lightPacific
        '#c5fffb', // @lightTeal
        '#baffcc', // @lightGreen
        '#e4fbb4', // @lightArmy
        '#fff7ad', // @lightYellow
        '#ffdebc', // @lightOrange
        '#ffd4d0', // @lightRed
        '#fde2eb', // @lightCoral
        '#f7d0fd', // @lightPink
        '#e2d4fd', // @lightPurple
    ];

    /**
     * The BaseChart constructor
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->initColorList();
    }

    /**
     * Set the reportDef
     *
     * @param mixed $reportDef
     */
    public function setReportDef($reportDef)
    {
        $this->reportDef = $reportDef;
    }

    /**
     * Get report def
     */
    public function getReportDef()
    {
        return $this->reportDef;
    }

    /**
     * Transform data for chart rendering
     */
    public function transformData()
    {
    }

    /**
     * Get plugins for chart rendering
     */
    public function getPlugins()
    {
    }

    /**
     * Get options for chart rendering
     */
    public function getOptions()
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'color' => $this->getTextColor(),
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $this->getTitle(),
                    'color' => $this->getTextColor(),
                ],
                'datalabels' => [
                    'display' => false,
                ],
            ],
        ];
    }

    /**
     * Initialize color list
     */
    protected function initColorList()
    {
        if (isset($this->data['colorOverrideList'])) {
            $this->baseColorList = $this->data['colorOverrideList'];
        } else {
            $this->baseColorList = $this->defaultColorList;
        }
    }

    /**
     * Return a color at a specific index
     *
     * @param int $index
     * @return null|string
     */
    protected function getColor(int $index): ?string
    {
        $dataLength = safeCount($this->data['values']);
        if ($index > safeCount($this->baseColorList)) {
            $index = $dataLength - safeCount($this->baseColorList) - 1;
        }

        return $this->baseColorList[$index];
    }

    /**
     * Get labels
     *
     * @param array $data
     * @return mixed
     */
    protected function getLabels(array $data)
    {
        return $data['label'];
    }

    /**
     * Get properties
     *
     * @return null|array
     */
    protected function getProperties(): ?array
    {
        $props = is_array($this->data['properties']) ? $this->data['properties'][0] : $this->data['properties'];
        return $props;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle(): string
    {
        $props = $this->getProperties();
        return $props ? $props['title'] : '';
    }

    /**
     * Get text color
     *
     * @return string
     */
    protected function getTextColor(): string
    {
        return '#777';
    }

    /**
     * Get border color
     *
     * @return string
     */
    protected function getBorderColor(): string
    {
        return '#777';
    }

    /**
     * Get background colors
     */
    protected function buildBackgroundColors(): array
    {
        $colors = [];
        foreach ($this->data['values'] as $index => $value) {
            $colors[] = $this->getColor($index);
        }
        return $colors;
    }

    /**
     * Get data
     *
     * @param mixed $values
     * @return array
     */
    protected function getData($values): array
    {
        $data = [];
        foreach ($values as $value) {
            $data [] = $this->sumValues($value['values']);
        }
        return $data;
    }

    /**
     * Get X axis label
     *
     * @param mixed $props
     * @param mixed $config
     * @param mixed $reportDef
     * @return null|string
     */
    protected function getXAxisLabel($props, $config, $reportDef): ?string
    {
        $groupDefs = $reportDef['group_defs'] ?? null;

        if (is_array($groupDefs) && isset($groupDefs['name'])) {
            $groupDefs = [$groupDefs];
        }

        if ($config['chartType'] === 'line chart') {
            return $props['seriesName'] ?? $groupDefs[array_key_last($groupDefs)]['label'];
        }

        return $props['groupName'] ?? $groupDefs[array_key_last($groupDefs)]['label'];
    }

    /**
     * Get Y axis label
     *
     * @param mixed $reportDef
     * @return null|string
     */
    protected function getYAxisLabel($reportDef): ?string
    {
        $label = '';

        if ($reportDef && $reportDef['summary_columns']) {
            foreach ($reportDef['summary_columns'] as $column) {
                if (isset($column['group_function'])) {
                    $label = $column['label'];
                }
            }
        }

        return $label;
    }

    /**
     * Get axis colors
     *
     * @return string
     */
    protected function getAxisColors(): string
    {
        return '#777';//grey
    }

    /**
     * Get data laebls color
     *
     * @return string
     */
    protected function getDataLabelsColor(): string
    {
        return '#777';//grey
    }

    /**
     * Get data for a series
     *
     * @param mixed $data
     * @param mixed $index
     * @return array
     */
    protected function getSeriesData($data, $index): array
    {
        $result = [];
        foreach ($data as $item) {
            $result [] = $item['values'][$index];
        }
        return $result;
    }

    /**
     * Get sum for a data series
     *
     * @param mixed $values
     * @return int|float
     */
    private function sumValues($values)
    {
        $sum = 0;
        foreach ($values as $value) {
            $sum += (float)$value;
        }

        return $sum;
    }
}
