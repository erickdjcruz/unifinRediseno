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

namespace Sugarcrm\Sugarcrm\Reports\AccessRules\Rules;

use Sugarcrm\Sugarcrm\Reports\Exception\SugarReportsExceptionFieldsRestricted;
use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;
use ACLField;
use SugarBean;

class AccessFieldsRightsRule extends BaseRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($bean): bool
    {
        if (empty($bean->content)) {
            return true;
        }

        $reportContent = $bean->content;

        if (is_string($reportContent)) {
            $reportContent = json_decode($bean->content, true);

            if ($reportContent === null) {
                return true;
            }
        }

        $fullTableList = $reportContent['full_table_list'];

        $fieldsToValidate = [];
        if ($reportContent['report_type'] === ReportType::ROWSANDCOLUMNS) {
            $fieldsToValidate = array_merge(
                $fieldsToValidate,
                $reportContent['display_columns'],
            );
        } elseif ($reportContent['report_type'] === ReportType::SUMMARY) {
            $fieldsToValidate = array_merge(
                $fieldsToValidate,
                $reportContent['summary_columns'],
                $reportContent['group_defs'],
            );
        } elseif ($reportContent['report_type'] === ReportType::SUMMARYDETAILS) {
            $fieldsToValidate = array_merge(
                $fieldsToValidate,
                $reportContent['display_columns'],
                $reportContent['summary_columns'],
                $reportContent['group_defs'],
            );
        } elseif ($reportContent['report_type'] === ReportType::MATRIX) {
            $fieldsToValidate = array_merge(
                $fieldsToValidate,
                $reportContent['summary_columns'],
                $reportContent['group_defs'],
            );
        }

        if (isset($reportContent['filters_def']) && isset($reportContent['filters_def']['Filter_1'])) {
            $filters = $this->extractFiltersLeafs($reportContent['filters_def']['Filter_1']);
            $fieldsToValidate = array_merge($fieldsToValidate, $filters);
        }

        $this->validateFields($fieldsToValidate, $fullTableList);

        return true;
    }

    protected function validateFields($fieldsToValidate, $fullTableList)
    {
        foreach ($fieldsToValidate as $key => $field) {
            $module = $fullTableList[$field['table_key']]['module'];
            $fieldAccess = ACLField::hasAccess($field['name'], $module, $this->user->id, true);

            if (!$fieldAccess) {
                throw new SugarReportsExceptionFieldsRestricted();
            }
        }
    }

    /**
     * Extract filters
     *
     * @param array $filters
     * @param array $filterLeafs
     * @return array
     */
    protected function extractFiltersLeafs(array $filters, &$filterLeafs = []): array
    {
        $nrOfFilters = safeCount($filters);

        for ($i = 0; $i < $nrOfFilters - 1; $i++) {
            $currentFilterDef = $filters[$i];
            if (isset($currentFilterDef['operator'])) {
                $this->extractFiltersLeafs($currentFilterDef);
            } else {
                array_push($filterLeafs, $currentFilterDef);
            }
        }

        return $filterLeafs;
    }
}
