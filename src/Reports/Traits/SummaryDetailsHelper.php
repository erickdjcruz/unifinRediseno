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

namespace Sugarcrm\Sugarcrm\Reports\Traits;

use Report;

trait SummaryDetailsHelper
{
    /**
     * Create table headers with GroupBy
     *
     * @param Reporter $reporter
     * @param int $index
     * @param array $row
     * @param bool $detaliedHeader
     *
     * @return mixed
     */
    protected function createHeaderWithGroupBy(
        \Report $reporter,
        int     $index,
        array   $row,
        ?array  $summaryColumns,
        bool    $detaliedHeader = false
    ) {

        $groupDef = $reporter->report_def['group_defs'];
        $attributeInfo = $groupDef[$index];

        $groupKey = $this->getGroupByKey($attributeInfo);
        $groupByColumnMeta = $reporter->group_defs_Info[$groupKey];

        $key = $groupByColumnMeta['index'];

        $groupByColumnHasVname = array_key_exists('vname', $groupByColumnMeta);
        $groupByColumnLabel = $groupByColumnHasVname ? $groupByColumnMeta['vname'] : $groupByColumnMeta['label'];
        $groupByColumnType = $groupByColumnMeta['type'];
        $groupByColumnModule = array_key_exists('vname', $groupByColumnMeta) ? $groupByColumnMeta['module'] : '';

        $headerValue = $row['cells'][$key];

        if (empty($headerValue)) {
            $headerValue = translate('LBL_NONE_STRING', 'Reports');
        }

        // Bug #39763 Summary label should be displayed instead of group label.
        // We have to ensure that we are adding the correct label
        if (isset($summaryColumns)) {
            foreach ($summaryColumns as $summaryField) {
                $isValid = true;

                if (isset($attributeInfo['qualifier'])) {
                    if (!isset($summaryField['qualifier'])) {
                        $isValid = false;
                    } elseif ($attributeInfo['qualifier'] !== $summaryField['qualifier']) {
                        $isValid = false;
                    }
                }

                if ($summaryField['table_key'] === $attributeInfo['table_key']
                    && $summaryField['name'] === $attributeInfo['name'] && $isValid === true) {
                    if ($detaliedHeader) {
                        $hasVname = array_key_exists('vname', $summaryField);
                        $groupByColumnLabel = $hasVname ? $summaryField['vname'] : $summaryField['label'];
                    } else {
                        $groupByColumnLabel = $summaryField['label'];
                    }
                }
            }
        }

        //to many variable to be passed in order to move this logic in a function
        if ($detaliedHeader) {
            $header = [];
            $header[] = [
                'label' => $groupByColumnLabel,
                'value' => $headerValue,
                'type' => $groupByColumnType,
                'module' => $groupByColumnModule,
            ];

            return $header;
        } else {
            $header = "{$groupByColumnLabel} = {$headerValue}";

            return $header;
        }
    }

    /**
     * Get next row
     *
     * @param Reporter $reporter
     *
     * @return mixed
     */
    protected function getSummaryNextRow(\Report $report)
    {
        $report->_load_currency();

        $nextRow = $report->get_next_row('summary_result', 'summary_columns', false, false);

        if (isset($nextRow['count'])) {
            $report->current_summary_row_count = $nextRow['count'];
        } else {
            $report->current_summary_row_count = null;
        }

        return $nextRow;
    }

    /**
     * Create header when there is no group by
     *
     * @param Report $reporter
     * @param array $headerRow
     * @param array $row
     * @param bool $detaliedHeader
     *
     * @return mixed
     */
    /** */
    protected function createHeaderWithoutGroupBy(
        \Report $reporter,
        array   $headerRow,
        array   $row,
        bool    $detaliedHeader = false
    ) {

        $groupByIndexInHeaderRow = [];
        $groupDef = (array)$reporter->report_def['group_defs'];

        for ($i = 0; $i < safeCount($groupDef); $i++) {
            $grKey = $this->getGroupByKey($groupDef[$i]);

            if (array_key_exists($grKey, $reporter->group_defs_Info)) {
                $key = $reporter->group_defs_Info[$grKey]['index'];
                $groupByIndexInHeaderRow[] = $key;
            }
        }

        $columnValues = $this->createColumnsForHeaderWithoutGroupBy(
            $headerRow,
            $row,
            $groupByIndexInHeaderRow,
            $detaliedHeader
        );

        return $columnValues;
    }

    /**
     * Create colummns for header without groupBy
     *
     * @param array $headerRow
     * @param array $row
     * @param array $groupByIndexInHeaderRow
     * @param bool $detaliedHeader
     *
     * @return mixed
     */
    private function createColumnsForHeaderWithoutGroupBy(
        array $headerRow,
        array $row,
        array $groupByIndexInHeaderRow,
        bool  $detaliedHeader = false
    ) {

        $columnValues = $detaliedHeader ? [] : '';

        $count = 0;

        for ($i = 0; $i < safeCount($headerRow); $i++) {
            if (!in_array($i, $groupByIndexInHeaderRow)) {
                $currentRow = $headerRow[$i];
                $value = $row['cells'][$i];

                if ($detaliedHeader) {
                    $label = array_key_exists('vname', $currentRow) ? $currentRow['vname'] : $currentRow['label'];
                    $module = array_key_exists('module', $currentRow) ? $currentRow['module'] : '';
                    $type = array_key_exists('type', $currentRow) ? $currentRow['type'] : '';

                    $columnValues[] = [
                        'label' => $label,
                        'module' => $module,
                        'type' => $type,
                        'value' => $value,
                    ];
                } else {
                    if ($count !== 0) {
                        $columnValues .= ', ';
                    }

                    $columnValue = $value;

                    if (is_array($columnValue) && array_key_exists('value', $columnValue)) {
                        $columnValue = $columnValue['value'];
                    }

                    $columnValues .= "{$currentRow} = {$columnValue}";
                }

                $count++;
            }
        }

        return $columnValues;
    }

    /**
     * Replace simple HeaderRow with SummaryColumns
     *
     * @param array $headerRow
     * @param array $summaryColumns
     * @param Report $reporter
     * @param bool $detaliedHeader
     *
     * @return array
     */
    protected function replaceHeaderRowWithSummaryColumns(
        array   $headerRow,
        array   $summaryColumns,
        \Report $reporter,
        bool    $detaliedHeader = false
    ): array {

        $count = 0;
        $removeHeaderRowLink = false;

        if (empty($reporter->report_def['display_columns']) && !isset($reporter->report_def['layout_options'])) {
            $removeHeaderRowLink = true;
        }

        for ($i = 0; $i < safeCount($summaryColumns); $i++) {
            if (!isset($summaryColumns[$i]['is_group_by']) || ($summaryColumns[$i]['is_group_by']) != 'hidden') {
                if (!$removeHeaderRowLink) {
                    $currentRow = $summaryColumns[$i];

                    if ($detaliedHeader) {
                        $label = array_key_exists('vname', $currentRow) ? $currentRow['vname'] : $currentRow['label'];
                        $module = array_key_exists('module', $currentRow) ? $currentRow['module'] : '';
                        $type = array_key_exists('type', $currentRow) ? $currentRow['type'] : '';

                        $headerRow[$count] = [
                            'label' => $label,
                            'module' => $module,
                            'type' => $type,
                        ];
                    } else {
                        $headerRow[$count] = $currentRow['label'];
                    }
                }

                $count++;
            }
        }

        return $headerRow;
    }

    /**
     * Check if we can find a perfect match between groupBy and summaryColumns
     *
     * @param array $groupBy
     * @param array $summaryColumns
     *
     * @return array
     */
    protected function getGroupByInfo(array $groupBy, array $summaryColumns): array
    {
        $groupByInfo = [];

        foreach ($summaryColumns as $i => $summaryColumn) {
            $sameName = $summaryColumn['name'] == $groupBy['name'];
            $sameLabel = $summaryColumn['label'] == $groupBy['label'];
            $sameTableKey = $summaryColumn['table_key'] == $groupBy['table_key'];

            if ($sameName && $sameLabel && $sameTableKey) {
                $groupByInfo = $groupBy;
                $groupByInfo['index'] = $i;

                break;
            }
        }

        return $groupByInfo;
    }

    /**
     * Creates a unique key for a groupBy column
     *
     * @param array $groupBy
     *
     * @return string
     */
    protected function getGroupByKey(array $groupBy): string
    {
        // name+table_key may not be unique for some groupby columns, eg, 'Quarter: Modified Date'
        // and 'Month: Modified Date' both have the same 'name' and 'table_key'
        $name = $groupBy['name'];
        $tableKey = $groupBy['table_key'];
        $label = $groupBy['label'];

        return "{$name}#{$tableKey}#{$label}";
    }

    /**
     * Create metadata for header columns
     *
     * @param Report $report
     * @param array $fieldsDef
     * @param array $allowedKeys
     *
     * @return array
     */
    protected function createHeaderColumnsMeta(\Report $report, array $fieldsDef, array $allowedKeys): array
    {
        $headerColumnMeta = [];

        foreach ($fieldsDef as $index => $fieldDef) {
            $fieldMeta = $report->getFieldDefFromLayoutDef($fieldDef);

            $headerColumnMeta[$index] = $fieldDef;

            if (!$fieldMeta) {
                continue;
            }

            foreach ($allowedKeys as $allowedKey) {
                if (array_key_exists($allowedKey, $fieldMeta)) {
                    $headerColumnMeta[$index][$allowedKey] = $fieldMeta[$allowedKey];
                }
            }
        }

        return $headerColumnMeta;
    }

    /**
     * Generate count value for each header/sub-header
     *
     * @param array $data
     * @param array $row
     * @param int $position
     * @param int $maxCount
     *
     * @return void
     */
    protected function generateSummaryDetailCount(array &$data, array $row, int $position, int $maxCount)
    {
        $value = $row['cells'][$position];
        $uniqueIdentifier = $value;

        if ($value && is_array($value)) {
            if (array_key_exists('value', $value)) {
                $uniqueIdentifier = $value['value'];

                if (isset($value['id'])) {
                    $uniqueIdentifier .= "{$value['id']}";
                }
            } else {
                $uniqueIdentifier = '';
            }
        }

        if (!array_key_exists($uniqueIdentifier, $data) && $position < $maxCount) {
            $data[$uniqueIdentifier] = ['count' => 0];
            $data[$uniqueIdentifier]['dataStructure'] = [];
        }

        if ($position < ($maxCount - 1)) {
            $this->generateSummaryDetailCount($data[$uniqueIdentifier]['dataStructure'], $row, ++$position, $maxCount);
        }

        $data[$uniqueIdentifier]['count'] += 1;
    }

    /**
     * Generate data like a tree
     *
     * @param array $allData
     * @param int $groupNr
     * @param array $row
     * @param int $depth
     * @param int $maxDepth
     * @param Report $report
     * @param array $headerRow
     *
     * @return void
     */
    protected function generateSummaryDetailRowData(
        array   &$allData,
        int     &$groupNr,
        array   $row,
        int     $depth,
        int     $maxDepth,
        \Report $report,
        array   $headerRow
    ) {

        $value = $row['cells'][$depth];
        $uniqueIdentifier = $value;
        $isSimpleField = true;

        if ($value && is_array($value)) {
            $isSimpleField = false;

            if (array_key_exists('value', $value)) {
                $uniqueIdentifier = $value['value'];

                if (isset($value['id'])) {
                    $uniqueIdentifier .= "{$value['id']}";
                }
            } else {
                $uniqueIdentifier = '';
            }
        }

        if (!array_key_exists($uniqueIdentifier, $allData) && $depth < $maxDepth) {
            $allData[$uniqueIdentifier] = [
                'id' => $uniqueIdentifier,
                'key' => $headerRow[$depth],
                'dataStructure' => [],
            ];

            if ($isSimpleField === false) {
                if (array_key_exists('column_function', $report->report_def['group_defs'][$depth])) {
                    $value['showPlainText'] = true;
                }
                $allData[$uniqueIdentifier]['fieldMeta'] = $value;
            }

            if ($depth === 0) {
                $allData[$uniqueIdentifier]['orderNo'] = $groupNr;
                $groupNr++;
            }
        }

        if ($depth < ($maxDepth - 1)) {
            $this->generateSummaryDetailRowData(
                $allData[$uniqueIdentifier]['dataStructure'],
                $groupNr,
                $row,
                $depth + 1,
                $maxDepth,
                $report,
                $headerRow
            );
        }

        if ($depth === ($maxDepth - 1) && $report->current_summary_row_count > 0) {
            $columnRowsData = $this->createColumnRow($report);

            $allData[$uniqueIdentifier]['dataStructure']['records'] = $columnRowsData;
            $allData[$uniqueIdentifier]['dataStructure']['header'] = $this->createHeaderWithoutGroupBy(
                $report,
                $headerRow,
                $row,
                false
            );
        }
    }

    /**
     * Generate all row data lik
     *
     * @param Report $report
     *
     * @return array
     */
    private function createColumnRow(\Report $report): array
    {
        $columnRowData = [];

        for ($j = 0; $j < $report->current_summary_row_count; $j++) {
            $columnRow = $report->get_next_row('result', 'display_columns', false, false);

            if ($columnRow !== 0) {
                $columnRow = array_key_exists('cells', $columnRow) ? $columnRow['cells'] : $columnRow;

                $columnRowData[] = $columnRow;
            }
        }

        return $columnRowData;
    }

    /**
     *
     * Generate count value for header/sub-header
     *
     * @param mixed $data
     * @param mixed $totalCount
     * @param bool computeTotal
     *
     * @return mixed
     */
    protected function generateCount(&$data, &$totalCount = 0, $computeTotal = false)
    {
        if (!is_array($data)) {
            return;
        }

        if (array_key_exists('records', $data)) {
            return safeCount($data['records']);
        }

        $groupCount = 0;

        foreach ($data as $dataKey => $dataValue) {
            if (array_key_exists('dataStructure', $dataValue)) {
                if (!array_key_exists('count', $dataValue)) {
                    $data[$dataKey]['count'] = 0;
                }

                $counter = $this->generateCount($data[$dataKey]['dataStructure']);

                if ($computeTotal) {
                    $totalCount += $counter;
                }

                $data[$dataKey]['count'] += $counter;

                $groupCount += $counter;
            }
        }

        return $groupCount;
    }

    /**
     * Generate Grand Total data for report
     *
     * @param Report $report
     *
     * @return array
     */
    protected function getSummaryDetailsGrandTotal(\Report $report): array
    {
        $grandTotal = [];

        $report->_load_currency();
        $rawData = $report->get_next_row('total_result', 'summary_columns', true, false, 'widgetReportForSideCar');

        if (is_array($rawData) && array_key_exists('cells', $rawData) && is_array($rawData['cells'])) {
            $data = array_values($rawData['cells']);

            foreach ($data as $index => $fieldData) {
                if (!is_array($fieldData)) {
                    continue;
                }
                //will translate the  grand total labels according to the header
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
}
