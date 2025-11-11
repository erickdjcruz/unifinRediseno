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

namespace Sugarcrm\Sugarcrm\Reports\Schedules;

use ReportsApi;
use RestService;
use Sugar_Smarty;

class ReportSchedules
{
    public $reporter;
    public $savedReport;
    public $templateTypes = [
        'summary' => 'summation.tpl',
        'detailed_summary' => 'summationdetails.tpl',
        'tabular' => 'rowscolumns.tpl',
    ];

    /**
     * Constructor
     *
     * @param object $options
     */
    public function __construct(object $options)
    {
        $this->reporter = $options;
        $this->savedReport = \BeanFactory::getBean('Reports', $options->saved_report_id);
    }

    /**
     * Get the html of data table
     *
     * @return string
     */
    public function getDataTableHtml()
    {
        $templateName = $this->getTemplateName();

        $data = $this->getData();
        $reportType = $this->savedReport->report_type;

        $tpl = '';

        if ($this->rowsExceeded($data)) {
            $tpl = 'exceeded';

            return $tpl;
        }

        if ($reportType === 'tabular' || $reportType === 'summary') {
            $data = $this->translateHeader($data);
        }

        if ($reportType === 'detailed_summary') {
            $data = $this->parseSummaryData($data);
        }

        $tpl = new Sugar_Smarty();
        $tpl->assign('data', $data);
        $tpl = $tpl->fetch("src/Reports/Schedules/Templates/{$templateName}");

        // strange issue with some reports having the report type different in def than in bean
        if (isset($this->reporter->report_def['layout_options'])) {
            $tpl = $this->getMatrixHtml();

            //need to add table border without modifying the original templates
            $tpl = str_replace('border="0"', 'border=1', $tpl);
        }

        return $tpl;
    }

    /**
     * Get report data
     *
     * @return array
     */
    protected function getData()
    {
        $reportsApi = new ReportsApi();
        $serviceBase = new RestService();

        $args = [
            'record' => $this->reporter->saved_report_id,
            'reportType' => $this->savedReport->report_type,
            'embeddedData' => true,
            'use_save_filters' => true,
            'reportSchedulesEnablePaging' => false,
            'summaryDetailsCount' => true,
        ];

        $data = $reportsApi->retrieveSavedReportRecordsById($serviceBase, $args);

        return $data;
    }

    /**
     * Get the template name
     *
     * @return string
     */
    protected function getTemplateName()
    {
        $type = $this->savedReport->report_type;

        return $this->templateTypes[$type];
    }

    /**
     * Parse summary data
     *
     * @param array $data
     */
    protected function parseSummaryData($data)
    {
        $groups = [];
        $translatedHeader = $this->translateHeader($data);

        foreach ($data['groups'] as $groupName => $group) {
            $headers = $this->getGroupHeaders($group, []);
            $groupData = $this->getGroupData($group);

            $groups[] = [
                'headers' => $headers,
                'header' => $translatedHeader['header'],
                'data' => $groupData,
            ];
        }

        $groups[] = ['grandTotal' => $data['grandTotal']];

        return $groups;
    }

    /**
     * Get group data
     *
     * @param array $group
     *
     * @return array
     */
    protected function getGroupData($group)
    {
        if (is_array($group) && array_key_exists('records', $group) && $group['records']) {
            return $group['records'];
        }

        $groupKeys = array_keys($group);

        if (safeCount($groupKeys) === 1) {
            return $this->getGroupData($group[$groupKeys[0]]);
        }

        return $this->getGroupData($group['dataStructure']);
    }

    /**
     * Get group headers
     * [
     *      'Account Name' => 'Air Safety Inc',
     *      'Billing City' => 'Denver',
     *      '' => Number of Meetings = 2
     * ]
     *
     * @param array $group
     * @param array $headers
     *
     * @return array
     */
    protected function getGroupHeaders($group, $headers)
    {
        if (is_array($group) && array_key_exists('records', $group) && $group['records']) {
            $headers[] = ['' => $group['header']];
            return $headers;
        }

        $groupKeys = array_keys($group);

        if (safeCount($groupKeys) === 1) {
            return $this->getGroupHeaders($group[$groupKeys[0]], $headers);
        }

        $headers[] = [
            $group['key'] => $group['id'] . ', Count = ' . $group['count'],
        ];

        return $this->getGroupHeaders($group['dataStructure'], $headers);
    }

    /**
     * Get the matrix html data table
     *
     * @return string
     */
    protected function getMatrixHtml()
    {
        global $mod_strings, $startLinkWrapper, $endLinkWrapper, $report_smarty, $current_language;

        require_once 'modules/Reports/templates/templates_list_view.php';

        $args = ['reporter' => $this->reporter];

        $summaryColumnsArray = $this->reporter->report_def['summary_columns'];
        $addedColumns = 0;
        $isAvgExists = false;
        $indexOfAvg = 0;
        $isSumExists = false;
        $isCountExists = false;

        foreach ($summaryColumnsArray as $key => $valueArray) {
            if (isset($valueArray['group_function'])) {
                if ($valueArray['group_function'] === 'avg') {
                    $isAvgExists = true;
                    $indexOfAvg = $key;
                }

                if ($valueArray['group_function'] === 'sum') {
                    $isSumExists = true;
                }

                if ($valueArray['group_function'] === 'count') {
                    $isCountExists = true;
                }
            }
        }

        if ($isAvgExists) {
            $avgValueArray = $summaryColumnsArray[$indexOfAvg];

            if (!$isSumExists) {
                $sumArray = $avgValueArray;
                $sumArray['label'] = 'sum';
                $sumArray['group_function'] = 'sum';
                $this->reporter->report_def['summary_columns'][] = $sumArray;
                $addedColumns = $addedColumns + 1;
                $summaryColumnsArray[] = ['label' => 'sum'];
            }

            if (!$isCountExists) {
                $countArray = $avgValueArray;
                $countArray['name'] = 'count';
                $countArray['label'] = 'count';
                $countArray['group_function'] = 'count';
                $this->reporter->report_def['summary_columns'][] = $countArray;
                $addedColumns = $addedColumns + 1;
                $summaryColumnsArray[] = ['label' => 'count'];
            }
        }

        $this->reporter->run_summary_query();
        $startLinkWrapper = "javascript:set_sort('";
        $endLinkWrapper = "','summary');";
        $report_smarty->assign('reporter', $this->reporter);
        $report_smarty->assign('args', $args);

        $headerRow = $this->reporter->get_summary_header_row();
        $groupDefArray = $this->reporter->report_def['group_defs'];

        replaceHeaderRowdataWithSummaryColumns($headerRow, $summaryColumnsArray, $this->reporter);
        $groupByIndexInHeaderRow = [];

        for ($i = 0; $i < safeCount($groupDefArray); $i++) {
            $groupByColumnInfo = getGroupByInfo($groupDefArray[$i], $summaryColumnsArray);
            $groupByIndexInHeaderRow[getGroupByKey($groupDefArray[$i])] = $groupByColumnInfo;
        }

        $this->reporter->group_defs_Info = $groupByIndexInHeaderRow;
        $this->reporter->addedColumns = $addedColumns;

        $report_smarty->assign('header_row', $headerRow);
        $report_smarty->assign('list_type', 'summary');

        template_header_row($headerRow, $args);

        $groupDefArray = $this->reporter->report_def['group_defs'];

        $matrixHtml = '';

        if (empty($mod_strings)) {
            $GLOBALS['mod_strings'] = return_module_language($current_language, 'Reports');
        }

        if (!isset($this->reporter->report_def['layout_options'])) {
            $matrixHtml = $report_smarty->fetch('modules/Reports/templates/_template_summary_list_view.tpl');
        } else {
            if (safeCount($groupDefArray) === 1 || safeCount($groupDefArray) > 3) {
                $matrixHtml = $report_smarty->fetch('modules/Reports/templates/_template_summary_list_view.tpl');
            } elseif (safeCount($groupDefArray) === 2) {
                $matrixHtml = $report_smarty->fetch('modules/Reports/templates/_template_summary_list_view_2gpby.tpl');
            } else {
                if ($this->reporter->report_def['layout_options'] === '1x2') {
                    $matrixHtml = $report_smarty->fetch('modules/Reports/templates/_template_summary_list_view_3gpbyL2.tpl');
                } else {
                    $matrixHtml = $report_smarty->fetch('modules/Reports/templates/_template_summary_list_view_3gpbyL1.tpl');
                }
            }
        }

        return $matrixHtml;
    }

    /**
     * Check if rows exceeded
     *
     * The limit of the rows which should be displayed in the email body is 200
     * If we have a report without any data it should not be displayed in the email body
     *
     * @param array $data
     * @return bool
     */
    private function rowsExceeded($data)
    {
        $noBodyReport = false;
        $rowsLimit = 200;

        $rowsAndSummaryCount = null;
        $matrixCount = null;
        $summationDetailsCount = null;

        if (is_array($data) && array_key_exists('records', $data)) {
            $rowsAndSummaryCount = safeCount($data['records']);
        }

        if (is_array($data) && array_key_exists('data', $data)) {
            $matrixCount = safeCount($data['data']);
        }

        if (is_array($data) && array_key_exists('countRecords', $data)) {
            $summationDetailsCount = $data['countRecords'];
        }

        if ($rowsAndSummaryCount > $rowsLimit || $matrixCount > $rowsLimit ||
            $summationDetailsCount > $rowsLimit) {
            $noBodyReport = true;
        }

        //we don't need a report body if we have a report without data
        if ($rowsAndSummaryCount === 0 || $matrixCount === 0 || $summationDetailsCount === 0) {
            $noBodyReport = true;
        }

        return $noBodyReport;
    }

    /**
     * Translate header values
     *
     * @param array $data
     * @return array
     */
    private function translateHeader($data)
    {
        if ($data && is_array($data) && array_key_exists('header', $data)) {
            foreach ($data['header'] as &$header) {
                if ($header['isvNameTranslated'] === false) {
                    $header['vname'] = translate($header['vname'], $header['module']);
                }
            }
        }
        return $data;
    }
}
