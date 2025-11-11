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

namespace Sugarcrm\Sugarcrm\Reports\Types;

require_once 'modules/Reports/templates/templates_list_view.php';

use Report;
use Sugarcrm\Sugarcrm\Reports\Traits\SummaryDetailsHelper;
use Sugarcrm\Sugarcrm\Reports\ReportFactory;
use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;

class Matrix extends Reporter
{
    use SummaryDetailsHelper;

    /**
     * {@inheritDoc}
     */
    public function getListData(bool $header, bool $needTotalCount): array
    {
        $reportDef = $this->getReportDef();

        // strange issue with some reports having the report type different in def than in bean
        if (!isset($reportDef['layout_options'])) {
            $report = ReportFactory::getReport(ReportType::SUMMARY, $this->data);

            return $report->getListData(true, true);
        }

        $report = new \Report($reportDef);
        $data = $this->generateData($report);

        return $data;
    }

    /**
     * Generate report data
     *
     * @param \Report $report
     *
     * @return array
     */
    private function generateData(\Report $report): array
    {
        $reportDef = (array)$report->report_def;
        $groupDefs = (array)$reportDef['group_defs'];
        $summaryColumns = (array)$report->report_def['summary_columns'];

        $addedColumns = 0;
        $response = [];

        $this->addGroupFunctions($report, $addedColumns, $summaryColumns);

        $report->run_summary_query();
        $report->fixGroupLabels();
        $this->setGroupDefsInfo($report);

        $groupDefs = (array)$reportDef['group_defs'];
        $layoutOptions = $this->getLayoutType($reportDef);

        if (safeCount($groupDefs) == 2) {
            $data = $this->getDataForTwoGroupBy($report, $addedColumns);
        } else {
            $data = $this->getDataForThreeGroupBy($report, $addedColumns);

            $response['grandTotalBottom'] = $data['grandTotalBottom'];
            $response['grandTotalBottomFormatted'] = $data['grandTotalBottomFormatted'];
        }

        $report->current_summary_row_count = null;

        if (array_key_exists('groupColumns', $data)) {
            $response['groupColumns'] = $data['groupColumns'];
        }

        $response['header'] = $data['header'];
        $response['legend'] = $data['legend'];
        $response['layoutType'] = $layoutOptions;
        $response['data'] = $data['data'];
        $response['queries'] = $report->query_list;
        $response['reportType'] = ReportType::MATRIX;

        return $response;
    }

    /**
     * Generate matrix table data for two groups
     *
     * @param \Report $report
     * @param int $addedColumns ,
     *
     * @return array
     */
    protected function getDataForTwoGroupBy(\Report $report, ?int $addedColumns): array
    {
        $data = [];
        $legend = [];
        $columnDataForSecondGroup = [];
        $groupByIndex = 1;
        $maximumCellSize = 0;

        $reportDef = (array)$report->report_def;
        $summaryColumns = $reportDef['summary_columns'];
        $groupDefs = $reportDef['group_defs'];

        $headerRow = $report->get_summary_header_row();

        replaceHeaderRowdataWithSummaryColumns($headerRow, $summaryColumns, $report);

        $groupByIndexInHeaderRow = [];

        for ($i = 0; $i < safeCount($groupDefs); $i++) {
            $groupByColumnInfo = getGroupByInfo($groupDefs[$i], $summaryColumns);
            $groupByIndexInHeaderRow[getGroupByKey($groupDefs[$i])] = $groupByColumnInfo;
        }

        $report->group_defs_Info = $groupByIndexInHeaderRow;
        $report->addedColumns = $addedColumns;
        $report->layout_manager->setAttribute('no_sort', 1);

        getColumnDataAndFillRowsFor2By2GPBY(
            $report,
            $headerRow,
            $data,
            $columnDataForSecondGroup,
            $groupByIndex,
            $maximumCellSize,
            $legend
        );

        $groupColumns = getColumnNamesForMatrix($report, $headerRow, $columnDataForSecondGroup);

        $layoutType = $this->getLayoutType($reportDef);

        $header = $this->createHeader($report, $headerRow, $layoutType, $columnDataForSecondGroup, null);

        return [
            'header' => $header,
            'data' => $data,
            'legend' => $legend,
            'groupColumns' => $groupColumns,
        ];
    }

    /**
     * Generate matrix table data for two groups
     *
     * @param \Report $report
     * @param int $addedColumns ,
     *
     * @return array
     */
    protected function getDataForThreeGroupBy(\Report $report, ?int $addedColumns): array
    {
        $data = [];
        $legend = [];
        $columnDataForSecondGroup = [];
        $columnDataForThirdGroup = [];
        $maximumCellSize = 0;
        $grandTotal = [];

        $reportDef = (array)$report->report_def;
        $summaryColumns = $reportDef['summary_columns'];
        $groupDefs = $reportDef['group_defs'];

        $headerRow = $report->get_summary_header_row();

        replaceHeaderRowdataWithSummaryColumns($headerRow, $summaryColumns, $report);

        $groupByIndexInHeaderRow = [];

        for ($i = 0; $i < safeCount($groupDefs); $i++) {
            $groupByColumnInfo = getGroupByInfo($groupDefs[$i], $summaryColumns);
            $groupByIndexInHeaderRow[getGroupByKey($groupDefs[$i])] = $groupByColumnInfo;
        }

        $report->group_defs_Info = $groupByIndexInHeaderRow;
        $report->addedColumns = $addedColumns;
        $report->layout_manager->setAttribute('no_sort', 1);

        getColumnDataAndFillRowsFor3By3GPBY(
            $report,
            $headerRow,
            $data,
            $columnDataForSecondGroup,
            $columnDataForThirdGroup,
            $maximumCellSize,
            $legend,
            $grandTotal
        );

        $layoutType = $this->getLayoutType($reportDef);

        $header = $this->createHeader(
            $report,
            $headerRow,
            $layoutType,
            $columnDataForSecondGroup,
            $columnDataForThirdGroup
        );

        $grandTotalBottomFormatted = [];

        foreach ($grandTotal as $gtKey => $gtValue) {
            if ($layoutType === '1x2') {
                $shouldAdd = !in_array($gtKey, $columnDataForThirdGroup) ||
                    in_array($gtKey, $columnDataForSecondGroup);
            } else {
                $shouldAdd = !in_array($gtKey, $columnDataForSecondGroup) ||
                    in_array($gtKey, $columnDataForThirdGroup);
            }

            if ($shouldAdd) {
                $grandTotalBottomFormatted[$gtKey] = $gtValue;
            }
        }


        return [
            'header' => $header,
            'data' => $data,
            'legend' => $legend,
            'grandTotalBottom' => $grandTotal,
            'grandTotalBottomFormatted' => $grandTotalBottomFormatted,
        ];
    }

    /**
     * Create data for table header
     *
     * @param \Report $report
     * @param array $headerRow ,
     * @param array $secondGroupColumns
     *
     * @return array
     */
    protected function createHeader(
        \Report $report,
        array   $headerRow,
        string  $layout,
        array   $secondGroupColumns,
        ?array  $thirdGroupColumns
    ): array {

        $firstHeader = [];

        $groupDefs = (array)$report->report_def['group_defs'];
        $countAdjustment = 0;

        if ($thirdGroupColumns && $layout === '1x2') {
            $countAdjustment = 1;
        }

        for ($i = 0; $i < safeCount($groupDefs) - $countAdjustment; $i++) {
            $firstHeader[] = $headerRow[$i];
        }

        $firstHeader[] = translate('LBL_REPORT_GRAND_TOTAL', 'Reports');

        $header = [$firstHeader, $secondGroupColumns];

        if ($countAdjustment === 1) {
            $header[] = [$headerRow[safeCount($groupDefs) - 1]];
        }

        if ($thirdGroupColumns) {
            $header[] = $thirdGroupColumns;
        }

        return $header;
    }

    /**
     * Set group defs info on report
     *
     * @param \Report $report
     */
    protected function setGroupDefsInfo(\Report $report)
    {
        $reportDef = (array)$report->report_def;
        $groupDefs = (array)$reportDef['group_defs'];
        $summaryColumns = (array)$report->report_def['summary_columns'];

        $headerRow = $report->get_summary_header_row();
        $headerRow = $this->replaceHeaderRowWithSummaryColumns($headerRow, $summaryColumns, $report, false);

        $groupByIndexInHeaderRow = [];

        for ($i = 0; $i < safeCount($groupDefs); $i++) {
            $groupByColumnInfo = $this->getGroupByInfo($groupDefs[$i], $summaryColumns);
            $groupByIndexInHeaderRow[$this->getGroupByKey($groupDefs[$i])] = $groupByColumnInfo;
        }

        $report->group_defs_Info = $groupByIndexInHeaderRow;
    }

    /**
     * We have to handle special group function if we have average
     *
     * @param Report $report
     * @param int $addedColumns
     * @param array $summaryColumns
     */
    private function addGroupFunctions(\Report $report, int &$addedColumns, array &$summaryColumns)
    {
        $hasAvg = false;
        $hasSum = false;
        $hasCount = false;
        $avgIndex = 0;


        foreach ($summaryColumns as $index => $summaryColumn) {
            if (!array_key_exists('group_function', $summaryColumn) || !isset($summaryColumn)) {
                continue;
            }

            $groupFunction = $summaryColumn['group_function'];

            switch ($groupFunction) {
                case 'avg':
                    $hasAvg = true;
                    $avgIndex = $index;
                    break;
                case 'sum':
                    $hasSum = true;
                    break;
                case 'count':
                    $hasCount = true;
                    break;
                default:
                    break;
            }
        }

        //we have not avg so we have to go back
        if (!$hasAvg) {
            return;
        }

        $avgColumn = $summaryColumns[$avgIndex];

        if (!$hasSum) {
            $sumColumn = $avgColumn;
            $sumColumn['label'] = 'LBL_SUM_LC';
            $sumColumn['group_function'] = 'sum';

            $addedColumns += 1;

            $report->report_def['summary_columns'][] = $sumColumn;

            $summaryColumns[] = [
                'label' => 'LBL_SUM_LC',
            ];
        }

        if (!$hasCount) {
            $countColumn = $avgColumn;
            $countColumn['name'] = 'count';
            $countColumn['label'] = 'LBL_COUNT_LC';
            $countColumn['group_function'] = 'count';

            $addedColumns += 1;

            $report->report_def['summary_columns'][] = $countColumn;

            $summaryColumns[] = [
                'label' => 'LBL_COUNT_LC',
            ];
        }
    }

    /**
     * Get the report displayed type
     *
     * @param array $reportDef
     *
     * @return string
     */
    private function getLayoutType(array $reportDef): string
    {
        return array_key_exists('layout_options', $reportDef) ? $reportDef['layout_options'] : '';
    }
}
