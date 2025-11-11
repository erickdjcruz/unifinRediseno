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

class chartjsReports extends chartjs
{
    /**
     * @var mixed[]
     */
    public $super_set_data;
    private $processed_report_keys = [];

    /**
     * @var Report
     */
    public $reporter;

    public function __construct()
    {
        parent::__construct();
    }

    private function calculateReportGroupTotal($dataset)
    {
        $total = 0;
        foreach ($dataset as $value) {
            if (isset($value['numerical_value'])) {
                $total += (float)$value['numerical_value'];
            }
        }

        return $total;
    }

    /**
     * Method checks is our dataset from currency field or not
     *
     * @param array $dataset of chart
     * @return bool is currency
     */
    public function isCurrencyReportGroupTotal(array $dataset)
    {
        $isCurrency = true;
        foreach ($dataset as $value) {
            if (empty($value['numerical_is_currency'])) {
                $isCurrency = false;
                break;
            }
        }
        return $isCurrency;
    }

    /**
     * Prepares data for chart
     * @param $dataset
     * @param int $level
     * @param false $first
     * @return string
     */
    private function processReportData($dataset, $level = 1, $first = false)
    {
        $data = '';
        $this->handleSort($this->super_set);

        // rearrange $dataset to get the correct order for the first row
        if ($first) {
            $temp_dataset = [];
            foreach ($this->super_set as $key) {
                $temp_dataset[$key] = $dataset[$key] ?? [];
            }
            $dataset = $temp_dataset;
        }

        foreach ($dataset as $key => $value) {
            if ($first && empty($value)) {
                $data .= $this->processDataGroup(4, $key, 'NULL', '', '');
            } elseif (array_key_exists('numerical_value', $dataset)) {
                $link = (isset($dataset['link'])) ? '#' . $dataset['link'] : '';
                $data .= $this->processDataGroup($level, $dataset['group_base_text'], $dataset['numerical_value'], $dataset['numerical_value'], $link);
                array_push($this->processed_report_keys, $dataset['group_base_text']);
                return $data;
            } else {
                $data .= $this->processReportData($value, $level + 1);
            }
        }

        return $data;
    }

    /**
     * Handle a section of the report data
     * @param $dataset
     * @return array
     */
    private function processReportGroup($dataset)
    {
        $super_set = [];
        $super_set_data = [];

        foreach ($dataset as $groupBy => $groups) {
            $prev_super_set = $super_set;
            foreach ($groups as $group => $groupData) {
                $super_set_data[$group] = $groupData;
            }
            if (safeCount($groups) > safeCount($super_set)) {
                $super_set = array_keys($groups);
                foreach ($prev_super_set as $prev_group) {
                    if (!in_array($prev_group, $groups)) {
                        array_push($super_set, $prev_group);
                    }
                }
            } else {
                foreach ($groups as $group => $groupData) {
                    if (!in_array($group, $super_set)) {
                        array_push($super_set, $group);
                    }
                }
            }
        }
        $super_set = array_unique($super_set);
        $this->super_set_data = $super_set_data;

        $this->handleSort($super_set);

        return $super_set;
    }

    /**
     * Handle sorting for special field types on grouped data.
     *
     * @param array &$super_set Grouped data
     */
    protected function handleSort(&$super_set)
    {
        if (!isset($this->reporter)) {
            return;
        }

        $firstTwoGroups = array_slice($this->group_by, 0, 2);

        // store last grouped field
        $lastgroupfield = end($firstTwoGroups);

        if ($this->isDateSort($lastgroupfield)) {
            usort($super_set, [$this, 'runDateSort']);
        } elseif (is_string($lastgroupfield) && $this->isEnumSort($lastgroupfield)) {
            $this->sortDropdownData($lastgroupfield, $super_set);
        } else {
            $sortDir = $this->getGroupSortDir();
            if ($sortDir === 'a') {
                asort($super_set, SORT_NATURAL | SORT_FLAG_CASE);
            }
            if ($sortDir === 'd') {
                rsort($super_set, SORT_NATURAL | SORT_FLAG_CASE);
            }
        }
    }

    /**
     * Check if the field is an enum to be sorted
     *
     * @param string field
     * @return bool
     */
    protected function isEnumSort(string $field): bool
    {
        if (isset($this->reporter->focus->field_defs[$field])
            && $this->reporter->focus->field_defs[$field]['type'] === 'enum') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the field is a date and needs to be sorted as a date
     * @param $field
     * @return bool
     */
    protected function isDateSort($field)
    {
        if (isset($this->reporter->focus->field_defs[$field])) {
            $dateTypes = ['date', 'datetime', 'datetimecombo'];
            $type = $this->reporter->focus->field_defs[$field]['type'];
            return in_array($type, $dateTypes);
        }
        return false;
    }

    /**
     * Sort the superSet based on the order of dropdown from studio
     *
     * @param string $fieldName
     * @param array $superSet
     * @return void
     */
    protected function sortDropdownData(string $fieldName, array &$superSet)
    {
        global $app_list_strings;

        if (!array_key_exists($fieldName, $this->reporter->focus->field_defs)) {
            return;
        }

        if (!array_key_exists('options', $this->reporter->focus->field_defs[$fieldName])) {
            return;
        }

        $dropdownOptionsKey = $this->reporter->focus->field_defs[$fieldName]['options'];

        if (!array_key_exists($dropdownOptionsKey, $app_list_strings)) {
            return;
        }

        $dropdownOptions = $app_list_strings[$dropdownOptionsKey];

        if (!$dropdownOptions) {
            return;
        }

        //here we have the values from studio with original positions
        //we are keeping the same pattenr as in the function processReportGroup,
        //where the elements are stored directly based on label not the key
        $dropdown = array_unique(array_values($dropdownOptions));

        usort($superSet, function ($a, $b) use ($dropdown) {
            if ($a == $b) {
                return 0;
            }

            $aPosition = array_search($a, $dropdown);
            $bPosition = array_search($b, $dropdown);

            return ($aPosition > $bPosition) ? 1 : -1;
        });
    }

    /**
     * Helper function for sorting dates.
     *
     * @param DateTime $a Date 1
     * @param DateTime $b Date 2
     * @return int an integer LT, EQ, or GT zero if Date 1 is respectively LT, EQ, or GT Date 2
     */
    protected function runDateSort($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        $aSuperSet = $this->super_set_data[$a];
        $bSuperSet = $this->super_set_data[$b];
        // fix the Week date format to be acceptable for DateTime
        if (preg_match('/^W\d{2}\s+\d{4}$/i', $aSuperSet['group_base_text'])) {
            $aSuperSet['raw_value'][4] = 'W';
        }
        if (preg_match('/^W\d{2}\s+\d{4}$/i', $bSuperSet['group_base_text'])) {
            $bSuperSet['raw_value'][4] = 'W';
        }

        $aRawValueTime = strtotime($aSuperSet['raw_value']);
        $bRawValueTime = strtotime($bSuperSet['raw_value']);
        if ($aRawValueTime === false && $bRawValueTime !== false) {
            return -1;
        }
        if ($aRawValueTime !== false && $bRawValueTime === false) {
            return 1;
        }

        return ($aRawValueTime < $bRawValueTime) ? -1 : 1;
    }

    /**
     * Get groups sort direction
     *
     * @return string
     */
    protected function getGroupSortDir()
    {
        $sortDir = 'a';
        if (isset($this->reporter->report_def['summary_order_by'])) {
            $lastSummaryOrderBy = end($this->reporter->report_def['summary_order_by']);
            if (isset($lastSummaryOrderBy['sort_dir']) && !empty($lastSummaryOrderBy['sort_dir'])) {
                $sortDir = $lastSummaryOrderBy['sort_dir'];
            }
        }

        return $sortDir;
    }

    private function xmlDataReportSingleValue()
    {
        $data = '';
        foreach ($this->data_set as $key => $dataset) {
            $total = $this->calculateReportGroupTotal($dataset);
            $this->checkYAxis($total);

            $data .= $this->tab('<group>', 2);
            $data .= $this->tabValue('title', $key, 3);
            $data .= $this->tab('<subgroups>', 3);
            $data .= $this->tab('<group>', 4);
            $data .= $this->tabValue('title', $total, 5);
            $data .= $this->tabValue('value', $total, 5);
            $data .= $this->tabValue('label', $key, 5);
            $data .= $this->tab('<link></link>', 5);
            $data .= $this->tab('</group>', 4);
            $data .= $this->tab('</subgroups>', 3);
            $data .= $this->tab('</group>', 2);
        }
        return $data;
    }

    private function xmlDataReportChart()
    {
        global $app_strings;
        $data = '';
        // correctly process the first row
        $first = true;
        foreach ($this->data_set as $key => $dataset) {
            $total = $this->calculateReportGroupTotal($dataset);
            $this->checkYAxis($total);

            $data .= $this->tab('<group>', 2);
            $data .= $this->tabValue('title', $key, 3);
            $data .= $this->tabValue('value', $total, 3);

            $label = $total;
            if ($this->isCurrencyReportGroupTotal($dataset)) {
                $label = currency_format_number($this->chart_properties['thousands'] ? $total / 1000 : $total, [
                    'currency_symbol' => $this->currency_symbol,
                    'decimals' => ($this->chart_properties['thousands'] ? 0 : null),
                ]);
            } elseif (is_numeric($label)) {
                $label = $this->formatNumber($this->chart_properties['thousands'] ? $label / 1000 : $label, 0);
            }

            if ($this->chart_properties['thousands']) {
                $label .= $app_strings['LBL_THOUSANDS_SYMBOL'];
            }
            $data .= $this->tabValue('label', $label, 3);

            $data .= $this->tab('<subgroups>', 3);

            if ((!is_float($total) && isset($dataset[$total]) && $total != $dataset[$total]['numerical_value'])
                || !array_key_exists($key, $dataset) || $key == '') {
                $data .= $this->processReportData($dataset, 4, $first);
            } elseif (safeCount($this->data_set) == 1 && $first) {
                foreach ($dataset as $k => $v) {
                    if (isset($v['numerical_value'])) {
                        $data .= $this->processDataGroup(4, $k, $v['numerical_value'], $v['numerical_value'], '');
                    }
                }
            }

            if (!$first) {
                $not_processed = array_diff($this->super_set, $this->processed_report_keys);
                $processed_diff_count = safeCount($this->super_set) - safeCount($not_processed);

                if ($processed_diff_count != 0) {
                    foreach ($not_processed as $title) {
                        $data .= $this->processDataGroup(4, $title, 'NULL', '', '');
                    }
                }
            }

            $data .= $this->tab('</subgroups>', 3);
            $data .= $this->tab('</group>', 2);
            $this->processed_report_keys = [];
            // we're done with the first row!
            //$first = false;
        }
        return $data;
    }

    public function processXmlData()
    {
        $data = '';

        $this->super_set = $this->processReportGroup($this->data_set);
        $single_value = false;

        foreach ($this->data_set as $key => $dataset) {
            if ((isset($dataset[$key]) && safeCount($this->data_set[$key]) == 1)) {
                $single_value = true;
            } else {
                $single_value = false;
            }
        }
        if ($this->chart_properties['type'] == 'line chart' && $single_value) {
            $data .= $this->xmlDataReportSingleValue();
        } else {
            $data .= $this->xmlDataReportChart();
        }

        return $data;
    }

    /**
     * wrapper function to return the html code containing the chart in a div
     *
     * @param string $name name of the div
     *            string $xmlFile    location of the XML file
     *            string $style    optional additional styles for the div
     * @return    string returns the html code through smarty
     */
    public function display($name, $xmlFile, $width = '320', $height = '480', $resize = false)
    {
        if (empty($name)) {
            $name = 'unsavedReport';
        }

        return parent::display($name, $xmlFile, $width, $height, $resize = false);
    }

    /**
     * Set the reporter property on this sucroseReport. Used in handleSort to
     * determine which sorting method to apply based on report field type.
     *
     * @param Report $reporter
     */
    public function setReporter(Report $reporter)
    {
        $this->reporter = $reporter;
    }
}
