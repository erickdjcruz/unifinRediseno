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


/**
 * Handle setting up the Charting for Display
 *
 * @api
 */
class ChartDisplay
{
    /**
     * Report Class
     *
     * @var Report
     */
    protected $reporter;

    /**
     * What type of chart are we displaying
     *
     * @var string
     */
    protected $chartType;

    /**
     * Can we draw a chart?
     *
     * @var bool
     */
    protected $canDrawChart = true;

    /**
     * Is this a stackable chart?
     *
     * @var bool
     */
    protected $stackChart = false;

    /**
     * What's the title for this chart
     *
     * @var string
     */
    protected $chartTitle = '';

    /**
     * Rows to display in the chart
     *
     * @var array
     */
    protected $chartRows = [];

    /**
     * Set the Reporter (Report Object) to use
     *
     * @param Report $reporter
     */
    public function setReporter(Report $reporter)
    {
        $this->reporter = $reporter;

        // set the default stuff we need on the reporter
        // and run the queries
        $this->reporter->is_saved_report = true;
        // only run if the chart_header_row variable is empty
        if (empty($this->reporter->chart_header_row)) {
            $this->reporter->get_total_header_row();
            $this->reporter->run_chart_queries();
        }

        // set the chart type
        $this->chartType = $this->reporter->chart_type;

        $this->parseReportHeaders();
        $this->parseChartTitle();
        $this->parseChartRows();
    }

    /**
     * Sort chart by first group using data series function
     *
     * @param array $reportDef
     */
    public function sortByDataSeries(array &$reportDef)
    {
        if (!$this->chartRows && !is_array($this->chartRows) && safeCount($this->chartRows) < 1) {
            return;
        }

        if (!array_key_exists('numerical_chart_column', $reportDef) || !$reportDef['numerical_chart_column']) {
            return;
        }

        $this->sortFirstGroup($reportDef);
    }

    /**
     * Sort chart by first group using data series function
     *
     * @param array $reportDef
     */
    protected function sortFirstGroup(array $reportDef)
    {
        if (!array_key_exists('order_by', $reportDef)) {
            return;
        }

        $orderBy = $reportDef['order_by'];

        if (!is_array($orderBy)) {
            return;
        }

        if (safeCount($orderBy) < 1) {
            return;
        }

        $dataSeries = $orderBy[0];

        if (array_key_exists('group_function', $dataSeries)) {
            $this->sortFirstGroupUsingDataSeries($dataSeries);
        }
    }

    /**
     * Sort the first group based on the function
     *
     * @param array $orderBy
     */
    private function sortFirstGroupUsingDataSeries(array $orderBy)
    {
        $sortDir = $orderBy['sort_dir'];

        uksort(
            $this->chartRows,
            function ($first, $second) use ($sortDir) {
                $firstItemGroup = $this->chartRows[$first];
                $secondItemGroup = $this->chartRows[$second];
                $firstItemsCollector = 0;
                $secondItemsCollector = 0;

                if (!is_array($firstItemGroup) || !is_array($secondItemGroup)) {
                    return;
                }

                foreach ($firstItemGroup as $firstItem) {
                    if (is_array($firstItem) && array_key_exists('numerical_value', $firstItem)) {
                        $firstItemsCollector += $firstItem['numerical_value'];
                    }
                }

                foreach ($secondItemGroup as $secondItem) {
                    if (is_array($secondItem) && array_key_exists('numerical_value', $secondItem)) {
                        $secondItemsCollector += $secondItem['numerical_value'];
                    }
                }

                return $this->sortComparation($sortDir, $firstItemsCollector, $secondItemsCollector);
            }
        );
    }

    /**
     * Comparation for sorting
     *
     * @param string $sortDir
     *
     * @return bool
     */
    private function sortComparation(string $sortDir, $firstItemsCollector, $secondItemsCollector)
    {
        if ($sortDir === 'd') {
            return $secondItemsCollector > $firstItemsCollector;
        }

        return $firstItemsCollector > $secondItemsCollector;
    }

    /**
     * Get the Reporter (Report Object)
     *
     * @return Report
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Return the SugarChart's object with all the values set and ready for display/consumption.
     *
     * @return JitReports|string
     */
    public function getSugarChart()
    {
        if ($this->canDrawChart()) {
            /* @var $sugarChart SugarChart or its sub class */
            $sugarChart = SugarChartFactory::getInstance('', 'Reports');

            $sugarChart->setData($this->chartRows);
            global $do_thousands;

            $sugarChart->setProperties(
                $this->chartTitle, // title
                '', // subtitle
                $this->chartType, // type
                'on', // legend
                'value', // labels
                'on', // print
                $do_thousands, // thousands
                $this->reporter->module,
                $this->reporter->name
            );

            $reportDef = $this->reporter->report_def;

            // set the measure data type
            //TODO: this fails for some RLI/QLI charts
            if (isset($reportDef['numerical_chart_column_type'])) {
                $yDataType = $reportDef['numerical_chart_column_type'];
            } else {
                $yDataType = 'numeric';
            }
            $xDataType = 'numeric';

            if (empty($yDataType) && $this->isChartColumnCurrency()) {
                $yDataType = 'currency';
            }

            // no drillthru for bwc modules
            $allowDrillthru = !isModuleBWC($this->reporter->module);

            if (isset($reportDef['group_defs'])) {
                $groupByNames = [];
                $groupByLabels = [];
                $groupByTypes = [];

                foreach ($reportDef['group_defs'] as $group_def) {
                    $groupByNames[] = $group_def['name'];

                    $bean = $this->reporter->full_bean_list[$group_def['table_key']];
                    if (!empty($bean)) {
                        $fieldDef = $bean->getFieldDefinition($group_def['name']);
                        $moduleName = $reportDef['full_table_list'][$group_def['table_key']]['module'];
                        $groupByLabel = translate($fieldDef['vname'], $moduleName);
                    } else {
                        $groupByLabel = $group_def['label'];
                    }
                    $groupByLabels[] = rtrim($groupByLabel, ':');

                    if (!empty($group_def['type'])) {
                        $groupByType = $group_def['type'];
                    } elseif (isset($fieldDef) && !empty($fieldDef['type'])) {
                        $groupByType = $fieldDef['type'];
                    }

                    if ($groupByType === 'int' || $groupByType === 'decimal') {
                        $groupByTypes[] = 'numeric';
                    } elseif ($groupByType === 'datetime' || $groupByType === 'currency') {
                        $groupByTypes[] = $groupByType;
                    } else {
                        $groupByTypes[] = 'string';
                    }

                    // check for any unsupported drillthru fields for sidecar modules
                    if ($allowDrillthru) {
                        // no drillthru on fields: 'datetime/datetimecombo' (no column function), 'multienum'
                        if ($groupByType === 'multienum'
                            || ((in_array($groupByType, ['datetime', 'datetimecombo'])
                                && empty($group_def['column_function'])))) {
                            $allowDrillthru = false;
                        }
                    }
                }

                $sugarChart->setDisplayProperty('groupName', $groupByLabels[0]);
                $sugarChart->setDisplayProperty('groupType', $groupByTypes[0]);
                $xDataType = $groupByTypes[0] === 'string' ? 'ordinal' : $groupByTypes[0];

                if (isset($groupByLabels[1])) {
                    $sugarChart->setDisplayProperty('seriesName', $groupByLabels[1]);
                    $sugarChart->setDisplayProperty('seriesType', $groupByTypes[1]);
                }

                $sugarChart->group_by = $groupByNames;
            }

            $sugarChart->setDisplayProperty('xDataType', $xDataType);
            $sugarChart->setDisplayProperty('yDataType', $yDataType);
            $sugarChart->setDisplayProperty('allow_drillthru', $allowDrillthru);

            return $sugarChart;
        } else {
            global $current_language;
            $mod_strings = return_module_language($current_language, 'Reports');
            return $mod_strings['LBL_NO_CHART_DRAWN_MESSAGE'];
        }
    }

    /**
     * Parse the Report Headers and such set the values we need for items later in the string
     */
    protected function parseReportHeaders()
    {
        $group_key = ($this->reporter->report_def['group_defs'][0]['table_key'] ?? '') .
            ':' .
            ($this->reporter->report_def['group_defs'][0]['name'] ?? '');

        if (!empty($this->reporter->report_def['group_defs'][0]['qualifier'])) {
            $group_key .= ':' . $this->reporter->report_def['group_defs'][0]['qualifier'];
        }

        $i = 0;
        foreach ($this->reporter->chart_header_row as $header_cell) {
            if ($header_cell['column_key'] == 'count') {
                $header_cell['column_key'] = 'self:count';
            }
            if ($header_cell['column_key'] == $this->reporter->report_def['numerical_chart_column']) {
                $this->reporter->chart_numerical_position = $i;
            }
            if ($header_cell['column_key'] == $group_key) {
                $this->reporter->chart_group_position = $i;
            }
            $i++;
        }
    }

    /**
     * Check if the value should be unformatted.
     *
     * @param $val
     * @return bool
     */
    protected function shouldUnformat($val): bool
    {
        $report_defs = $this->reporter->report_def;
        $isCurrency = isset($report_defs['numerical_chart_column_type']) &&
            $report_defs['numerical_chart_column_type'] === 'currency';

        // For currency, the numeric value is already converted to normal format like "1,234.56"
        // even if user has customized 1000s separator and/or Decimal Symbol.
        return is_string($val) && ((!is_numeric($val) && $isCurrency) || !$isCurrency);
    }

    /**
     * Generate the Title for the Chart
     */
    protected function parseChartTitle()
    {
        global $current_language, $do_thousands, $current_user;
        if (isset($this->reporter->report_def['layout_options'])) {
            // This is for matrix report
            $this->reporter->run_total_query();
            // start template_total_table code
            $total_row = $this->reporter->get_summary_total_row();
            for ($i = 0; $i < safeCount($this->reporter->chart_header_row); $i++) {
                if ($this->reporter->chart_header_row[$i]['column_key'] == 'count') {
                    $this->reporter->chart_header_row[$i]['column_key'] = 'self:count';
                } // if
                if ($this->reporter->chart_header_row[$i]['column_key'] == $this->reporter->report_def['numerical_chart_column']) {
                    $total = $i;
                    //break;
                }
                if ($this->reporter->chart_header_row[$i]['column_key'] == 'self:count') {
                    $this->reporter->chart_header_row[$i]['column_key'] = 'count';
                } // if
            } // for
            if (empty($total)) {
                $total = 0;
            }
            $total = $total_row['cells'][$total];

            if ($this->shouldUnformat($total)) {
                $total = (float)unformat_number($total);
            }
            if ($total > 100000) {
                $do_thousands = true;
                $total = (string)round($total / 1000);
            } else {
                $do_thousands = false;
            }
            array_pop($this->reporter->chart_rows);
        } else {
            $total_row = array_pop($this->reporter->chart_rows);
            $total = $this->get_total($total_row);
        }

        $mod_strings = return_module_language($current_language, 'Reports');

        // Use Locale values if we are not rounding to thousands
        $round = null;
        $precision = null;
        if ($do_thousands) {
            $round = 0;
            $precision = 0;
        }

        //grab the currency symbol for this chart
        $symbol = $this->print_currency_symbol();

        //if there is no symbol, then precision should be saved as this is not a currency
        if (empty($symbol)) {
            $round = $precision = strpos(strrev($total), '.');
        } else {
            $currentUserPrecision = $current_user->getPreference('default_currency_significant_digits');

            if ($currentUserPrecision && !$do_thousands) {
                $precision = $currentUserPrecision;
            }
        }

        $this->chartTitle = $mod_strings['LBL_TOTAL_IS']
            . ' '
            . $symbol
            . format_number($total, $round, $precision)
            . $this->get_k();
    }

    /**
     * Format the ChartRows from the Reporting engine
     */
    protected function parseChartRows()
    {
        $chart_rows = [];
        $chart_groupings = [];
        foreach ($this->reporter->chart_rows as $row) {
            $row_remap = $this->get_row_remap($row);
            if (!isset($row_remap['group_base_text'])) {
                continue;
            }
            $chart_groupings[$row_remap['group_base_text']] = true; // store all the groupingstem
            if (!empty($row['cells'][$this->reporter->chart_numerical_position]['key'])) {
                $fieldKey = $row['cells'][$this->reporter->chart_numerical_position]['key'];

                if (!empty($this->reporter->all_fields[$fieldKey])) {
                    $fieldDef = $this->reporter->all_fields[$fieldKey];
                    if ($fieldDef['type'] == 'currency') {
                        $numKey = 'numerical_is_currency';
                        $chart_rows[$row_remap['group_text']][$row_remap['group_base_text']][$numKey] = true;
                    }
                }
            }
            if (empty($chart_rows[$row_remap['group_text']][$row_remap['group_base_text']])) {
                $chart_rows[$row_remap['group_text']][$row_remap['group_base_text']] = $row_remap;
            } else {
                if (!isset($chart_rows[$row_remap['group_text']][$row_remap['group_base_text']]['numerical_value'])) {
                    $chart_rows[$row_remap['group_text']][$row_remap['group_base_text']]['numerical_value'] = 0;
                }

                $groupText = $row_remap['group_text'];
                $groupBaseText = $row_remap['group_base_text'];
                if (isset($chart_rows[$groupText][$groupBaseText]['numerical_value'])
                    && is_numeric($chart_rows[$groupText][$groupBaseText]['numerical_value'])) {
                    $chart_rows[$groupText][$groupBaseText]['numerical_value'] += intval($row_remap['numerical_value']);
                } else {
                    if (isset($chart_rows[$groupText][$groupBaseText]['numerical_value'])) {
                        $chart_rows[$groupText][$groupBaseText]['numerical_value'] = intval($chart_rows[$groupText][$groupBaseText]['numerical_value']);
                    }
                    $chart_rows[$groupText][$groupBaseText]['numerical_value'] = intval($row_remap['numerical_value']);
                }
            }
            $chart_rows[$row_remap['group_text']][$row_remap['group_base_text']]['raw_value'] = $row_remap['raw_value'] ?? '';
            $chart_rows[$row_remap['group_text']][$row_remap['group_base_text']]['group_base_text'] = $row_remap['group_base_text'];
        }

        //Determine if the original report def has a grouping level greater than one
        if (isset($this->reporter->report_def['group_defs'])) {
            $this->stackChart = (safeCount($this->reporter->report_def['group_defs']) > 1) ? true : false;
        }

        switch ($this->chartType) {
            case 'hBarF':
                if ($this->isStackable()) {
                    $this->chartType = 'horizontal group by chart';
                } else {
                    $this->chartType = 'horizontal bar chart';
                }
                break;
            case 'hGBarF':
                if ($this->isStackable()) {
                    $this->chartType = 'horizontal grouped bar chart';
                } else {
                    $this->chartType = 'horizontal bar chart';
                }
                break;
            case 'vBarF':
                if ($this->isStackable()) {
                    $this->chartType = 'stacked group by chart';
                } else {
                    $this->chartType = 'bar chart';
                }
                break;
            case 'vGBarF':
                if ($this->isStackable()) {
                    $this->chartType = 'vertical grouped bar chart';
                } else {
                    $this->chartType = 'bar chart';
                }
                break;
            case 'pieF':
                $this->chartType = 'pie chart';
                break;
            case 'lineF':
                if ($this->isStackable()) {
                    $this->chartType = 'line chart';
                } else {
                    $this->canDrawChart = false;
                }
                break;
            case 'funnelF':
                $this->chartType = 'funnel chart 3D';
                break;
            case 'donutF':
                $this->chartType = 'donut chart';
                break;
            case 'treemapF':
                $this->chartType = 'treemap chart';
                break;
            default:
                break;
        }

        $this->chartRows = $chart_rows;
    }

    /**
     * Can we draw the chart?
     *
     * @return bool
     */
    public function canDrawChart()
    {
        return $this->canDrawChart;
    }

    /**
     * Is the chart Stackable
     *
     * @return bool
     */
    public function isStackable()
    {
        return $this->stackChart;
    }


    protected function print_currency_symbol()
    {
        $report_defs = $this->reporter->report_def;
        $currency_symbol = '';

        if ((isset($report_defs['numerical_chart_column_type']) &&
                $report_defs['numerical_chart_column_type'] == 'currency') ||
            $this->isChartColumnCurrency()
        ) {
            $currency = BeanFactory::newBean('Currencies')->getUserCurrency();

            $currency_symbol = $currency->symbol;
        } elseif (!isset($report_defs['numerical_chart_column_type'])) {
            return '';
        }

        return $currency_symbol;
    }

    /**
     * Check if the used field in numerical chart column type is currency
     */
    protected function isChartColumnCurrency(): bool
    {
        $report = $this->reporter;

        $reportDefs = (array)$report->report_def;

        if (!array_key_exists('numerical_chart_column', $reportDefs)) {
            return false;
        }

        $allFields = $report->all_fields;

        if (!is_array($allFields)) {
            return false;
        }

        $numericalChartColumn = $reportDefs['numerical_chart_column'];
        $numericalChartComponents = explode(':', $numericalChartColumn);

        if (safeCount($numericalChartComponents) < 2) {
            return false;
        }

        if (end($numericalChartComponents) !== 'count') {
            array_pop($numericalChartComponents);
            $fieldName = array_pop($numericalChartComponents);
            $tableListKeyColumn = array_pop($numericalChartComponents);

            $fieldKey = $tableListKeyColumn . ':' . $fieldName;

            if (!array_key_exists($fieldKey, $allFields)) {
                return false;
            }

            $fieldMeta = $allFields[$fieldKey];

            if (!is_array($fieldMeta) || !array_key_exists('type', $fieldMeta)) {
                return false;
            }

            if ($fieldMeta['type'] === 'currency') {
                return true;
            }
        }

        return false;
    }

    /**
     * Remap the row to conform to how the charting engine needs the data
     *
     * @param $row
     * @return array
     */
    protected function get_row_remap($row)
    {
        global $locale;
        $row_remap = [];
        if (!isset($row['cells'][$this->reporter->chart_numerical_position]['val'])) {
            return $row_remap;
        }

        $val = strip_tags($row['cells'][$this->reporter->chart_numerical_position]['val']);
        if ($this->shouldUnformat($val)) {
            $val = unformat_number($val, true);
        }
        $row_remap['numerical_value'] = $val;
        // format to user prefs
        $row_remap['formatted_value'] = $this->print_currency_symbol() . format_number($row_remap['numerical_value']);

        $row_remap['group_text'] = $group_text = (isset($this->reporter->chart_group_position) && !is_array($this->reporter->chart_group_position)) ? chop($row['cells'][$this->reporter->chart_group_position]['val']) : '';
        $row_remap['group_key'] = ((isset($this->reporter->chart_group_position) && !is_array($this->reporter->chart_group_position)) ? $row['cells'][$this->reporter->chart_group_position]['key'] : '');
        $row_remap['count'] = $row['count'] ?? 0;
        $row_remap['group_label'] = ((isset($this->reporter->chart_group_position) && !is_array($this->reporter->chart_group_position)) ? $this->reporter->chart_header_row[$this->reporter->chart_group_position]['label'] : '');
        $row_remap['numerical_label'] = $this->reporter->chart_header_row[$this->reporter->chart_numerical_position]['label'];
        $row_remap['numerical_key'] = $this->reporter->chart_header_row[$this->reporter->chart_numerical_position]['column_key'];
        $row_remap['module'] = $this->reporter->module;

        $groupDefs = $this->reporter->report_def['group_defs'];

        if (safeCount($groupDefs) > 1) {
            // multiple group by, use second group by as legend
            $secondGroupIndex = 1;
            $secondGroupDef = $groupDefs[$secondGroupIndex];
            $secondGroupByKey = $secondGroupDef['table_key'] . ':' . $secondGroupDef['name'];

            $secondGroupDef['table_alias'] = $this->reporter->getTableFromField($secondGroupDef);
            $columnClass = $this->reporter->layout_manager->getClassFromWidgetDef($secondGroupDef);
            $columnAlias = strtoupper($columnClass->_get_column_alias($secondGroupDef));

            foreach ($row['cells'] as $cell) {
                if ((isset($cell['alias']) && $cell['alias'] === $columnAlias) ||
                    ($cell['key'] === $secondGroupByKey)) {
                    $row_remap['group_base_text'] = $cell['val'];
                    $row_remap['raw_value'] = $cell['raw_value'] ?? '';
                    break;
                }
            }
        } else { // single group by
            $row_remap['group_base_text'] = $row['cells'][0]['val'];
        }

        //jclark - bug 47329 - report charts show html link text
        $row_remap['group_text'] = strip_tags($row_remap['group_text']);
        $row_remap['group_base_text'] = strip_tags($row_remap['group_base_text']);
        //end jclark fix

        return $row_remap;
    }

    /**
     * Generate a total amount for a row
     *
     * @param $total_row
     * @return float|string
     */
    protected function get_total($total_row)
    {
        $total_index = 0;
        if (!empty($this->reporter->chart_total_header_row)) {
            for ($i = 0; $i < safeCount($this->reporter->chart_total_header_row); $i++) {
                if ($this->reporter->chart_total_header_row[$i]['column_key'] == $this->reporter->report_def['numerical_chart_column']) {
                    $total_index = $i;
                    break;
                }
            }
        } else {
            $total_index = 0; // special for dashlets!!
        }
        $total = $total_row['cells'][$total_index]['val'];
        if ($this->shouldUnformat($total)) {
            $total = (float)unformat_number($total, true);
        }
        global $do_thousands;
        if ($this->get_maximum() > 100000 && (!isset($this->reporter->report_def['do_round'])
                || (isset($this->reporter->report_def['do_round']) && $this->reporter->report_def['do_round'] == 1))
        ) {
            $do_thousands = true;
            return (string)round($total / 1000);
        } else {
            $do_thousands = false;
            return (string)$total;
        }
    }

    /**
     * Return a Thousands Symbol if one is required
     *
     * @return string
     */
    protected function get_k()
    {
        global $do_thousands, $app_strings;
        if ($do_thousands) {
            return $app_strings['LBL_THOUSANDS_SYMBOL'];
        } else {
            return '';
        }
    }

    /**
     * Get the Maximum Number from the rows
     *
     * @return float|int|mixed
     */
    protected function get_maximum()
    {
        $numbers = [];
        foreach ($this->reporter->chart_rows as $row) {
            $row_remap = $this->get_row_remap($row);
            array_push($numbers, $row_remap['numerical_value']);
        }
        return $this->get_max_from_numbers($numbers);
    }

    /**
     * Get the Maximum Number of an array of numbers
     *
     * @param $numbers
     * @return float|int|mixed
     */
    protected function get_max_from_numbers($numbers)
    {
        if (!empty($numbers)) {
            $max = max($numbers);
            if ($max < 1) {
                return $max;
            }
            $base = 10 ** floor(log10($max));
            return ceil($max / $base) * $base;
        } else {
            return 0;
        }
    }

    /**
     * Figure out the cache name for a chart
     *
     * @param null|Report $reporter If Null, this will use the set reporter, other wise it will use the passed in one
     * @return string                       File name for the cache file.
     */
    public function get_cache_file_name($reporter = null)
    {
        if (is_null($reporter)) {
            $reporter = $this->reporter;
        }
        global $current_user;

        $xml_cache_dir = sugar_cached('xml/');
        if ($reporter->is_saved_report == true) {
            $filename = $xml_cache_dir . $current_user->getUserPrivGuid() . '_' . $reporter->saved_report_id . '_saved_chart.xml';
        } else {
            $filename = $xml_cache_dir . $current_user->getUserPrivGuid() . '_' . time() . '_curr_user_chart.xml';
        }

        if (!is_dir(dirname($filename))) {
            create_cache_directory('xml/');
        }

        return $filename;
    }

    /**
     * Method for displaying the old legacy way that was done.
     *
     * @param $id                       ID use for the guid
     * @param bool $is_dashlet Are we displaying a dashlet or not
     * @return JitReports|string        Return the HTML
     */
    public function legacyDisplay($id, $is_dashlet = false)
    {

        if ($is_dashlet) {
            $width = '100%';
            $height = '480';
            $guid = $id;
        } else {
            $width = '100%';
            $height = '480';
            $guid = $this->reporter->saved_report_id;
        }

        // Bug #57213 : Reports with data series removed render charts inconsistently
        if ($this->reporter && !$this->reporter->has_summary_columns()) {
            global $current_language;
            $mod_strings = return_module_language($current_language, 'Reports');
            return $mod_strings['LBL_CANNOT_DISPLAY_CHART_MESSAGE'];
        }

        $sugarChart = $this->getSugarChart();
        if (is_object($sugarChart)) {
            $sugarChart->setReporter($this->reporter);
            $xmlFile = $this->get_cache_file_name();
            $sugarChart->saveXMLFile($xmlFile, $sugarChart->generateXML());
            return $sugarChart->display($guid, $xmlFile, $width, $height);
        }

        return $sugarChart;
    }

    /**
     * Generate the xml string from the SugarChart Object
     *
     * @return string
     */
    public function generateXML()
    {
        return $this->getSugarChart()->generateXML();
    }

    /**
     * Generate JSON
     *
     * @return mixed|string
     */
    public function generateJson()
    {
        return $this->getSugarChart()->buildJson($this->generateXML(), true);
    }
}
