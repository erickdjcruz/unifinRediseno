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

use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;

class RowsAndColumns extends Reporter
{
    /**
     * {@inheritDoc}
     */
    public function getListData(bool $header, bool $needTotalCount): array
    {
        $listData = [];
        $report = new \Report($this->getReportDef());

        $arguments = $this->getData();

        [$offset, $limit] = $this->getPagination($arguments);

        $headerMeta = $this->getHeader($report);

        if ($header) {
            $listData['header'] = $headerMeta;
        }

        $report->report_offset = $offset;
        $report->report_max = $limit;

        //we don't need paginate for data table in report schedules
        if (array_key_exists('reportSchedulesEnablePaging', $arguments) &&
            $arguments['reportSchedulesEnablePaging'] === false) {
            $report->enable_paging = false;
        }

        $data = $report->getData();

        $totalCount = property_exists($report, 'total_count') ? $report->total_count : 0;

        if ($needTotalCount) {
            $listData['totalCount'] = (int)$totalCount;
        }

        $nextOffset = $this->getNextOffset($limit, $offset, $totalCount);

        $listData['orderBy'] = $this->getOrderBy($report);
        $listData['nextOffset'] = $nextOffset;
        $listData['records'] = $this->formatRecordsData($report, $data);
        $listData['totalPages'] = $this->getTotalPagesNumber($totalCount, $limit);
        $listData['queries'] = $report->query_list;
        $listData['reportType'] = ReportType::ROWSANDCOLUMNS;

        return $listData;
    }

    /**
     * Format records for Sidecare
     *
     * @param \Report $report
     * @param array $records
     *
     * @return array
     */
    protected function formatRecordsData(\Report $report, array $records): array
    {
        $formattedData = [];

        $report->layout_manager->setAttribute('context', 'ListPlain');
        $sugarWidgerReport = new \SugarWidgetReportField($report->layout_manager);

        foreach ($records as $record) {
            $record = array_combine(array_map('strtoupper', array_keys($record)), array_values($record));

            $formattedData[] = $this->formatRow($report, $record, $sugarWidgerReport);
        }

        return $formattedData;
    }

    /**
     * Format each row
     *
     * @param \Report $report
     * @param array $record
     * @param \SugarWidgetReportField $sugarWidgerReport
     *
     * @return array
     */
    protected function formatRow(\Report $report, array $record, \SugarWidgetReportField $sugarWidgerReport): array
    {
        $displayColumns = (array)$report->report_def['display_columns'];
        $row = [];

        foreach ($displayColumns as $displayColumn) {
            $field = [];

            $displayColumn['table_alias'] = $report->getTableFromField($displayColumn);

            $fieldDef = $report->getFieldDefFromLayoutDef($displayColumn);

            $displayColumn['fieldDef'] = $fieldDef;
            $displayColumn['type'] = $displayColumn['fieldDef']['type'];
            $displayColumn['fields'] = $record;
            $displayColumn['module'] = $displayColumn['fieldDef']['module'];
            $displayColumn['column_key'] = $report->_get_full_key($displayColumn);

            $this->resolveCustomField($displayColumn, $fieldDef);

            $displayValue = $sugarWidgerReport->getSidecarFieldData($displayColumn);

            $field['type'] = $displayColumn['type'];
            $field['module'] = $displayColumn['module'];
            $field['name'] = $fieldDef['name'];


            if (is_array($displayValue)) {
                $field = array_merge($field, $displayValue);
            } else {
                $field['value'] = $displayValue;
            }

            if (array_key_exists('id_name', $fieldDef)) {
                $field['id_name'] = $fieldDef['id_name'];
            }

            $row[] = $field;
        }

        return $row;
    }

    /**
     * {@inheritDoc}
     */
    protected function getHeader(\Report $report): array
    {
        $displayColumns = (array)$report->report_def['display_columns'];
        $fieldsDef = [];

        foreach ($displayColumns as $displayColumn) {
            $fieldDef = $report->getFieldDefFromLayoutDef($displayColumn);
            $fieldDef['table_alias'] = $report->getTableFromField($displayColumn);
            $fieldDef['column_key'] = $report->_get_full_key($displayColumn);
            $fieldDef['table_key'] = $displayColumn['table_key'];

            $fieldDef = $this->sanitizeFieldLabel($fieldDef, $displayColumn);

            $fieldsDef[] = $fieldDef;
        }

        return $fieldsDef;
    }

    /**
     * Get orderBy for current report result
     *
     * @param Report $report
     *
     * @return array
     */
    protected function getOrderBy(\Report $report): array
    {
        $orderBy = [];

        if (!array_key_exists('order_by', $report->report_def)) {
            return $orderBy;
        }

        $orderByMeta = (array)$report->report_def['order_by'];

        foreach ($orderByMeta as $index => $orderByField) {
            $orderBy[$index] = [];

            $orderBy[$index]['sort_dir'] = $orderByField['sort_dir'];
            $orderBy[$index]['name'] = $orderByField['name'];
            $orderBy[$index]['type'] = $orderByField['type'];
            $orderBy[$index]['table_key'] = $orderByField['table_key'];
        }

        return $orderBy;
    }
}
