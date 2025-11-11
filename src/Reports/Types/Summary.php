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

class Summary extends Reporter
{
    protected $hasGroups = true;
    protected $resultFieldName = 'summary_result';
    protected $columnFieldName = 'summary_columns';

    /**
     * {@inheritDoc}
     */
    public function getListData(bool $header, bool $needTotalCount): array
    {
        $report = new \Report($this->getReportDef());

        $this->massageGroups($report);

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
        $result = [];

        $result['records'] = $this->getRecords($report);
        $result['orderBy'] = $this->getOrderBy($report);
        $result['header'] = $this->createHeader($report);
        $result['grandTotal'] = $this->getSummaryGrandTotal($report);
        $result['queries'] = $report->query_list;
        $result['reportType'] = ReportType::SUMMARY;

        return $result;
    }

    /**
     * Get all the records of the report
     *
     * @param \Report $report
     *
     * @return array
     */
    protected function getRecords(\Report $report): array
    {
        $records = [];

        $report->get_summary_header_row();

        if ($this->hasGroups) {
            $report->run_summary_query();
        } else {
            $report->run_query();
        }

        $report->fixGroupLabels();
        $report->_load_currency();

        $this->getRows($report, $records);

        return $records;
    }

    /**
     * Get records rows
     *
     * @param \Report $report
     * @param array $records
     *
     * @return array
     */
    protected function getRows(\Report $report, array &$records)
    {
        $row = $report->get_next_row(
            $this->resultFieldName,
            $this->columnFieldName,
            false,
            false,
        );

        if ($row !== 0) {
            if (array_key_exists('cells', $row)) {
                $records[] = $row['cells'];
            }

            if (isset($row['count'])) {
                $report->current_summary_row_count = $row['count'];
            }
            $this->getRows($report, $records);
        } else {
            $report->current_summary_row_count = null;
        }
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

        if (array_key_exists('summary_order_by', $report->report_def)) {
            $orderByMeta = (array)$report->report_def['summary_order_by'];

            foreach ($orderByMeta as $index => $orderByField) {
                $orderBy[$index] = $orderByField;
            }
        }

        return $orderBy;
    }

    /**
     * Generate Grand Total data for report
     *
     * @param Report $report
     *
     * @return array
     */
    protected function getSummaryGrandTotal(\Report $report): array
    {
        $grandTotal = [];

        $report->run_total_query();
        $report->_load_currency();
        $rawData = $report->get_next_row('total_result', 'summary_columns', true, false, 'widgetReportForSideCar');

        if (is_array($rawData) && array_key_exists('cells', $rawData) && is_array($rawData['cells'])) {
            $data = array_values($rawData['cells']);

            foreach ($data as $index => $fieldData) {
                //will translate the  grand total labels according to the header
                if (!is_array($fieldData)) {
                    continue;
                }
                if (array_key_exists('name', $fieldData) && $fieldData['name'] === 'count' &&
                    !array_key_exists('vname', $fieldData)) {
                    $data[$index]['name'] = 'name';
                    $data[$index]['vname'] = translate('LBL_COUNT', 'Reports');
                    $data[$index]['isvNameTranslated'] = true;
                } elseif (array_key_exists('label', $fieldData)) {
                    $labelDef = array_key_exists('vname', $data[$index]) ? $data[$index]['vname'] : '';
                    $module = array_key_exists('module', $data[$index]) ? $data[$index]['module'] : null;

                    $fieldLabel = $this->getFieldLabel($labelDef, $fieldData['label'], $module);
                    $data[$index]['vname'] = $fieldLabel['value'];
                    $data[$index]['isvNameTranslated'] = !$fieldLabel['translated'];
                } elseif (array_key_exists('vname', $fieldData)) {
                    $module = array_key_exists('module', $fieldData) ? $fieldData['module'] : null;
                    $fieldLabel = $this->getFieldLabel($fieldData['vname'], $fieldData['vname'], $module);
                    $data[$index]['vname'] = $fieldLabel['value'];
                    $data[$index]['isvNameTranslated'] = !$fieldLabel['translated'];
                }
            }

            $grandTotal = $data;
        }

        return $grandTotal;
    }

    /**
     * Create header
     *
     * @param \Report $report
     *
     * @return array
     */
    protected function createHeader(\Report $report): array
    {
        if ($this->hasGroups) {
            return $this->createHeaderWithGroups($report);
        }

        return $this->createHeaderWithoutGroups($report);
    }

    /**
     * Generate fields def for report
     *
     * @param \Report $report
     *
     * @return array
     */
    protected function createHeaderWithGroups(\Report $report): array
    {
        $header = [];

        $summaryColumns = (array)$report->report_def['summary_columns'];

        foreach ($summaryColumns as $summaryColumn) {
            $fieldDef = $report->getFieldDefFromLayoutDef($summaryColumn);
            $fieldDef = array_merge($fieldDef, $summaryColumn);
            $fieldDef['table_alias'] = $report->getTableFromField($summaryColumn);

            $this->resolveCustomField($fieldDef, $fieldDef);

            $fieldDef = $this->sanitizeFieldLabel($fieldDef, $summaryColumn);

            $this->setModuleByTableKey($fieldDef, $report);

            $header[] = $fieldDef;
        }

        return $header;
    }

    /**
     * Generate fields def for report
     *
     * Create header when we have no groups
     *
     * @param \Report $report
     *
     * @return array
     */
    protected function createHeaderWithoutGroups(\Report $report): array
    {
        $displayColumns = (array)$report->report_def['display_columns'];
        $fieldsDef = [];

        foreach ($displayColumns as $displayColumn) {
            $fieldDef = $report->getFieldDefFromLayoutDef($displayColumn);
            $fieldDef['table_alias'] = $report->getTableFromField($displayColumn);
            $fieldDef['column_key'] = $report->_get_full_key($displayColumn);
            $fieldDef['table_key'] = $displayColumn['table_key'];

            $this->resolveCustomField($fieldDef, $fieldDef);

            $fieldDef = $this->sanitizeFieldLabel($fieldDef, $displayColumn);

            $this->setModuleByTableKey($fieldDef, $report);

            $fieldsDef[] = $fieldDef;
        }

        return $fieldsDef;
    }

    /**
     * If there are no groups we have to let the logic to know
     */
    protected function massageGroups(\Report $report)
    {
        $hasDisplayColumn = array_key_exists('display_columns', $report->report_def)
            && !empty($report->report_def['display_columns']);

        $noGroupDefs = (array_key_exists('group_defs', $report->report_def)
                && empty($report->report_def['group_defs'])) || !array_key_exists('group_defs', $report->report_def);

        // Here we actually have a summation without groups as a rows and columns
        if ($report->show_columns && $hasDisplayColumn && $noGroupDefs) {
            $this->hasGroups = false;

            $this->resultFieldName = 'result';
            $this->columnFieldName = 'display_columns';
        }
    }
}
