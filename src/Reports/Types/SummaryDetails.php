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

use Sugarcrm\Sugarcrm\Reports\Traits\SummaryDetailsHelper;
use Sugarcrm\Sugarcrm\Reports\ReportFactory;
use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;

class SummaryDetails extends Reporter
{
    use SummaryDetailsHelper;

    /**
     * {@inheritDoc}
     */
    public function getListData(bool $header, bool $needTotalCount): array
    {
        $report = new \Report($this->getReportDef());

        $hasDisplayColumn = array_key_exists('display_columns', $report->report_def)
            && !empty($report->report_def['display_columns']);

        $noGroupDefs = (array_key_exists('group_defs', $report->report_def)
                && empty($report->report_def['group_defs'])) || !array_key_exists('group_defs', $report->report_def);

        // strange issue with some reports having the report type different in def than in bean
        if ($report->show_columns && $hasDisplayColumn && $noGroupDefs) {
            $report = ReportFactory::getReport(ReportType::SUMMARY, $this->data);

            return $report->getListData(true, true);
        }

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
        $arguments = $this->getData();

        $reportDef = $report->report_def;
        $groupDefs = (array)$reportDef['group_defs'];
        $header = [];

        $report->embeddedData = array_key_exists('embeddedData', $arguments) ? $arguments['embeddedData'] : false;

        $summaryColumns = (array)$report->report_def['summary_columns'];
        $displayColumns = (array)$report->report_def['display_columns'];
        $fieldsDef = [];

        foreach ($displayColumns as $displayColumn) {
            $fieldDef = $report->getFieldDefFromLayoutDef($displayColumn);
            $fieldDef['table_alias'] = $report->getTableFromField($displayColumn);
            $fieldDef['column_key'] = $report->_get_full_key($displayColumn);

            $fieldDef = $this->sanitizeFieldLabel($fieldDef, $displayColumn);

            $fieldsDef[] = $fieldDef;
        }

        $header['header'] = $fieldsDef;

        $report->run_summary_combo_query();
        $report->fixGroupLabels();
        $headerRow = $report->get_summary_header_row();

        $headerRow = $this->replaceHeaderRowWithSummaryColumns($headerRow, $summaryColumns, $report, false);

        $groupByIndexInHeaderRow = [];

        for ($i = 0; $i < safeCount($groupDefs); $i++) {
            $groupByColumnInfo = $this->getGroupByInfo($groupDefs[$i], $summaryColumns);
            $groupByIndexInHeaderRow[$this->getGroupByKey($groupDefs[$i])] = $groupByColumnInfo;
        }

        $report->group_defs_Info = $groupByIndexInHeaderRow;
        $report->addedColumn = 0;

        $allData = [];
        $groupNr = 0;
        $groupDefsCount = safeCount($groupDefs);

        while (($row = $this->getSummaryNextRow($report)) !== 0) {
            $this->generateSummaryDetailRowData($allData, $groupNr, $row, 0, $groupDefsCount, $report, $headerRow);
        }

        $recordsNo = 0;
        $computeTotal = true;

        $this->generateCount($allData, $recordsNo, $computeTotal);

        $grandTotal = $this->getSummaryDetailsGrandTotal($report);

        $header['grandTotal'] = $grandTotal;

        $header['groups'] = $allData;

        $header['recordsNo'] = $recordsNo;

        $functionOptions = $this->getFunctionOptions();
        if (!empty($functionOptions)) {
            $header['functionOptions'] = $functionOptions;
        }

        $header['orderBy'] = array_key_exists('order_by', $reportDef) ? $reportDef['order_by'] : [];
        $header['queries'] = $report->query_list;

        if (array_key_exists('summaryDetailsCount', $arguments)) {
            $header['countRecords'] = $this->generateCount($allData);
        }

        $header['reportType'] = ReportType::SUMMARYDETAILS;

        return $header;
    }
}
