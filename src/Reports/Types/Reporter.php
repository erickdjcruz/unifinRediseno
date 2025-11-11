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

use SugarApiExceptionNotFound;
use Sugarcrm\Sugarcrm\Reports\Charts\ConfigBuilder;
use Sugarcrm\Sugarcrm\Reports\Traits\ReportHelper;

class Reporter
{
    use ReportHelper;
    /** @var bool */
    public $detaliedHeader;

    /**
     * @var string
     */
    protected $reportId;

    /**
     * @var array
     */
    protected $groupFilters;
    protected $reportDef;
    protected $data;

    /**
     * @var bool
     */
    protected $useSavedFilters;

    /**
     * Constructor
     *
     * @param array $data
     * @param bool $ignoreBuildReportDef
     */
    public function __construct(array $data, bool $ignoreBuildReportDef = false)
    {
        $this->data = $data;
        $this->reportId = $data['record'];
        $this->groupFilters = $data['group_filters'] ?? [];
        $this->useSavedFilters = false;
        $this->detaliedHeader = false;

        if (array_key_exists('use_saved_filters', $data)) {
            // we can't use strict equal as use_saved_filters can be either 'true' or bool(true)
            // neither boolVal($data['use_saved_filters']) nor (bool) $data['use_saved_filters'] can be used here
            $this->useSavedFilters = $data['use_saved_filters'] == 'true';
        }

        if (array_key_exists('detaliedHeader', $data)) {
            $this->detaliedHeader = (bool)$data['detaliedHeader'];
        }

        if ($ignoreBuildReportDef) {
            $this->reportDef = [];
        } else {
            $this->reportDef = $this->buildReportDef();
        }
    }

    /**
     * Build the report definition
     *
     * @param string $previewReportDef
     *
     * @return array
     */
    public function buildReportDef(string $previewReportDef = null): array
    {
        $savedReport = \BeanFactory::retrieveBean('Reports', $this->reportId);

        if (!$savedReport) {
            throw new SugarApiExceptionNotFound(translate('LBL_NO_ACCESS'));
        }

        $reportDef = json_decode($savedReport->content, true) ?? [];

        if (empty($reportDef)) {
            $decodedContent = html_entity_decode($savedReport->content, ENT_COMPAT);
            $reportDef = json_decode($decodedContent, true) ?? [];
        }

        $reportMeta = $this->data;

        if ($previewReportDef) {
            $reportDef = json_decode($previewReportDef, true) ?? [];
        }

        if ($this->useSavedFilters) {
            $reportCache = new \ReportCache();

            if ($reportCache->retrieve($savedReport->id) && $reportCache->contents_array) {
                $reportDef['filters_def'] = $reportCache->contents_array['filters_def'];
            }
        }

        if (array_key_exists('filtersDef', $reportMeta) &&
            is_array($reportMeta['filtersDef']) &&
            !empty($reportMeta['filtersDef'])
        ) {
            $reportDef['filters_def'] = $reportMeta['filtersDef'];
        }

        if (array_key_exists('summaryColumns', $reportMeta) && $reportMeta['summaryColumns']) {
            $reportDef['summary_columns'] = $reportMeta['summaryColumns'];
        }

        if (array_key_exists('displayColumns', $reportMeta) && $reportMeta['displayColumns']) {
            $reportDef['display_columns'] = $reportMeta['displayColumns'];
        }

        if (array_key_exists('groupDefs', $reportMeta) && $reportMeta['groupDefs']) {
            $reportDef['group_defs'] = $reportMeta['groupDefs'];
        }

        if (array_key_exists('fullTableList', $reportMeta) && $reportMeta['fullTableList']) {
            $reportDef['full_table_list'] = $reportMeta['fullTableList'];
        }

        if (array_key_exists('multipleOrderBy', $reportMeta)) {
            $reportDef['multipleOrderBy'] = !!$reportMeta['multipleOrderBy'];
        }

        if (array_key_exists('orderBy', $reportMeta) && is_array($reportMeta['orderBy'])) {
            $reportDef['order_by'] = $reportMeta['orderBy'];
        }

        if (array_key_exists('summaryOrderBy', $reportMeta) && is_array($reportMeta['summaryOrderBy'])) {
            $reportDef['summary_order_by'] = $reportMeta['summaryOrderBy'];
        }

        if ($this->groupFilters) {
            $reportDef = $this->addGroupFilters($reportDef);
        }

        if (array_key_exists('intelligent', $reportMeta) && is_array($reportMeta['intelligent'])
            && array_key_exists('intelligent', $reportMeta['intelligent'])
            && !!$reportMeta['intelligent']['intelligent'] === true) {
            $this->addLinkFilter($reportDef, $reportMeta['intelligent']);
        }

        if (array_key_exists('chartType', $reportMeta)) {
            $reportDef['chart_type'] = $reportMeta['chartType'];
        }


        if (array_key_exists('filters_def', $reportDef) && is_array($reportDef['filters_def'])) {
            $this->cleanupFilters($reportDef['filters_def']['Filter_1']);
        }

        return $reportDef;
    }

    /**
     * Sanitize broken filters
     */
    public function cleanupFilters(&$filters)
    {
        if (!is_array($filters)) {
            return;
        }

        foreach ($filters as $key => &$filter) {
            // remove the runtimeFilterId from filters that do not need it
            if ($key === 'runtimeFilterId' && is_array($filters) && array_key_exists('operator', $filters)) {
                unset($filters[$key]);
            }

            $this->cleanupFilters($filter);
        }
    }

    /**
     * Save Report Cache
     * Also update the last accessed date of the report
     */
    public function updateReportCache()
    {
        $saveForAllUsers = false;

        $reportCache = new \ReportCache();
        $reportCache->retrieve($this->reportId);

        if (empty($reportCache->id)) {
            $savedReport = \BeanFactory::getBean('Reports', $this->reportId);
            $reportDef = json_decode($savedReport->content, true) ?? [];

            if (empty($reportDef)) {
                $decodedContent = html_entity_decode($savedReport->content);
                $reportDef = json_decode($decodedContent, true) ?? [];
            }

            $filtersDef = array_key_exists('filters_def', $reportDef) ? $reportDef['filters_def'] : [];
            $encodedFiltersDef = json_encode([
                'filters_def' => $filtersDef,
            ]);

            $reportCache->contents = $encodedFiltersDef;

            $reportCache->id = $this->reportId;
            $reportCache->new_with_id = true;

            $reportCache->save($saveForAllUsers);
        } else {
            $reportCache->update();
        }
    }

    /**
     * Track the report when it is accessed
     *
     * @param string $action
     */
    public function setTracker(string $action)
    {
        global $current_user;

        $reportId = $this->reportId;
        $savedReport = \BeanFactory::retrieveBean('Reports', $reportId);

        if (!$savedReport) {
            throw new SugarApiExceptionNotFound(translate('LBL_NO_ACCESS'));
        }

        $trackerManager = \TrackerManager::getInstance();
        $timeStamp = \TimeDate::getInstance()->nowDb();
        $monitor = $trackerManager->getMonitor('tracker');

        if ($monitor) {
            $visible = 0;

            if (($action === 'detailview') || ($action === 'editview')) {
                $visible = 1;
            }

            $monitor->setValue('team_id', $current_user->getPrivateTeamID());
            $monitor->setValue('action', $action);
            $monitor->setValue('user_id', $current_user->id);
            $monitor->setValue('module_name', 'Reports');
            $monitor->setValue('date_modified', $timeStamp);
            $monitor->setValue('visible', $visible);
            $monitor->setValue('item_id', $reportId);
            $monitor->setValue('item_summary', $savedReport->get_summary_text());

            $trackerManager->saveMonitor($monitor, true, true);
        }
    }

    /**
     * Getter for the schedules
     *
     * @return array
     */
    public function getSchedules(): array
    {
        $savedReport = \BeanFactory::getBean('Reports', $this->reportId);

        return $savedReport->getSchedules();
    }

    /**
     * Build chart data from report def
     *
     * @return array|string
     */
    public function getChartData()
    {
        $reporter = new \Report($this->reportDef);

        $reporter->saved_report_id = $this->reportId;

        if ($reporter && !$reporter->has_summary_columns()) {
            return '';
        }

        // build report data
        $reportData = [
            'id' => $reporter->saved_report_id,
            'label' => $reporter->name,
            'summary_columns' => $reporter->report_def['summary_columns'],
            'group_defs' => $reporter->report_def['group_defs'],
            'filters_def' => $reporter->report_def['filters_def'],
            'base_module' => $reporter->report_def['module'],
            'full_table_list' => $reporter->report_def['full_table_list'],
            'numericalChartColumn' => $reporter->report_def['numerical_chart_column'],
        ];

        $chartDisplay = new \ChartDisplay();
        $chartDisplay->setReporter($reporter);
        $chartDisplay->sortByDataSeries($reporter->report_def);

        $chart = $chartDisplay->getSugarChart();

        if (is_string($chart)) {
            return [
                'error' => true,
                'responseText' => $chart,
            ];
        }

        $chart->setReporter($reporter);

        $chartXML = $chart->generateXML();
        $chartXML = $chart->cleanupXML($chartXML);
        $chartJSON = $chart->buildJson($chartXML, true);

        if (is_object($chart)) {
            $chartImageType = $chart->image_export_type;
            $reportData['pdfChartImageExt'] = $chartImageType;
        }

        return [
            'reportData' => $reportData,
            'chartData' => json_decode($chartJSON, true),
        ];
    }

    /**
     * Add a link to the target record within the result of the report
     *
     * @param array $reportDef
     * @param array $intelligenceData
     *
     * @return void
     */
    protected function addLinkFilter(array &$reportDef, array $intelligenceData)
    {
        // add table data
        $tableListAddIn = self::getTableListAddin($intelligenceData);
        $reportDef['full_table_list'] = array_merge($reportDef['full_table_list'], $tableListAddIn);

        // add the filter
        self::setFiltersDefAddin($reportDef, $intelligenceData);
    }

    /**
     * Retrieve a list of the table addIns
     *
     * @param array $intelligenceData
     *
     * @return array
     */
    protected function getTableListAddin(array $intelligenceData): array
    {
        $targetModule = null;
        global $dictionary;
        $targetFieldName = '';

        $module = $intelligenceData['targetModule'];
        $linkField = $intelligenceData['link'];

        $beanName = \BeanFactory::getObjectName($module);
        $bean = \BeanFactory::getBean($module);
        $restrictName = false;

        // get the module starting from a link field name
        if ($bean->field_defs[$linkField]) {
            $targetLinkedField = $bean->field_defs[$linkField];
            $targetModule = $targetLinkedField['module'];
            $targetFieldName = $targetLinkedField['name'];

            if (!$targetModule && $targetLinkedField['type'] === 'link' && $targetLinkedField['name']) {
                $targetModule = ucfirst($targetLinkedField['name']);
                $restrictName = true;
            }
        }

        $targetBeanName = \BeanFactory::getObjectName($targetModule);

        if (!$targetBeanName) {
            $targetModule = $intelligenceData['targetModule'];
            $targetBeanName = \BeanFactory::getObjectName($targetModule);
        }

        // make sure we have all the metadata needed loaded
        \VardefManager::loadVarDef($targetModule, $targetBeanName);

        $relName = $dictionary[$beanName]['fields'][$linkField]['relationship'];

        $targetLinkField = null;

        // now we need to get the target link field
        if ($dictionary[$targetBeanName]['fields']) {
            foreach ($dictionary[$targetBeanName]['fields'] as $fieldName => $field) {
                if (isset($field['type'])
                    && $field['type'] === 'link'
                    && isset($field['relationship'])
                    && $field['relationship'] === $relName
                    && (($restrictName && $field['name'] === $targetFieldName) || !$restrictName)
                ) {
                    $targetLinkField = $field;
                }
            }
        }

        if (is_null($targetLinkField)) {
            return [];
        }

        $targetLinkFieldName = $targetLinkField['name'];

        $tmpBean = \BeanFactory::getBean($targetModule);

        $tmpBean->load_relationship($targetLinkFieldName);

        $ret = [];
        $tmpLink = $tmpBean->$targetLinkFieldName;
        $keyName = $targetModule . ':' . $targetLinkFieldName;

        // build the table def
        $ret[$keyName] = [
            'name' => $targetModule . ' > ' . $beanName,
            'parent' => 'self',
            'link_def' => [
                'name' => $targetLinkFieldName,
                'relationship_name' => $relName,
                'bean_is_lhs' => (bool)($tmpLink->_get_bean_position()),
                'link_type' => $tmpLink->getType(),
                'label' => $beanName,
                'module' => $module,
                'table_key' => $keyName,
            ],
            'dependents' => ['Filter.1_table_filter_row_1'],
            'module' => $module,
            'label' => $beanName,
        ];

        return $ret;
    }

    /**
     * Retrieve the filters defs
     *
     * @param array $reportDef
     * @param array $intelligenceData
     */
    protected function setFiltersDefAddin(array &$reportDef, array $intelligenceData)
    {
        $targetModule = null;
        $ret = [];
        global $dictionary;
        $targetFieldName = '';

        $beanName = \BeanFactory::getObjectName($intelligenceData['targetModule']);
        $bean = \BeanFactory::getBean($intelligenceData['targetModule']);
        $restrictName = false;

        // we need to get the target module
        if ($bean->field_defs[$intelligenceData['link']]) {
            $targetLinkedField = $bean->field_defs[$intelligenceData['link']];
            $targetFieldName = $targetLinkedField['name'];
            $targetModule = $targetLinkedField['module'];

            if (!$targetModule && $targetLinkedField['type'] === 'link' && $targetLinkedField['name']) {
                $targetModule = ucfirst($targetLinkedField['name']);
                $restrictName = true;
            }
        }

        $targetBeanName = \BeanFactory::getObjectName($targetModule);

        if (!$targetBeanName) {
            $targetModule = $intelligenceData['targetModule'];
            $targetBeanName = \BeanFactory::getObjectName($targetModule);
        }

        // we want to make sure we have all the metadata we need loaded
        \VardefManager::loadVarDef($targetModule, $targetBeanName);

        $relName = $dictionary[$beanName]['fields'][$intelligenceData['link']]['relationship'];

        $targetLinkField = null;

        // let's look for the target link field
        if ($dictionary[$targetBeanName]['fields']) {
            foreach ($dictionary[$targetBeanName]['fields'] as $fieldName => $field) {
                if (isset($field['type']) &&
                    $field['type'] === 'link' &&
                    isset($field['relationship']) &&
                    $field['relationship'] === $relName &&
                    (($restrictName && $field['name'] === $targetFieldName) || !$restrictName)
                ) {
                    $targetLinkField = $field;
                }
            }
        }

        $presetFilters = $reportDef['filters_def']['Filter_1'];

        if (is_null($targetLinkField)) {
            return [];
        }

        $targetLinkFieldName = $targetLinkField['name'];
        unset($reportDef['filters_def']['Filter_1']);

        // build our relate filter
        $ret['Filter_1'] = [
            'operator' => 'AND',
            0 => [
                'name' => 'id',
                'table_key' => $targetModule . ':' . $targetLinkFieldName,
                'qualifier_name' => 'is',
                'input_name0' => $intelligenceData['contextId'],
                'input_name1' => $intelligenceData['contextName'],
            ],
        ];

        $ret['Filter_1'][] = $presetFilters;

        $reportDef['filters_def'] = array_merge($reportDef['filters_def'], $ret);
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data ?? [];
    }

    /**
     * Setter for the report def
     *
     * @param array $reportDef
     *
     * @return array
     */
    public function setReportDef(array $reportDef = [])
    {
        $this->reportDef = $reportDef;
    }

    /**
     * Getter for the report def
     *
     * @return array
     */
    public function getReportDef(): array
    {
        return $this->reportDef;
    }

    /**
     * Retrieves the report type
     *
     * @return string
     */
    public function getReportType(): string
    {
        $reportType = 'tabular';

        if ($this->reportDef['report_type'] === 'summary') {
            $reportType = 'summary';

            if (!empty($this->reportDef['display_columns'])) {
                $reportType = 'detailed_summary';
            } else {
                if (!empty($this->reportDef['group_defs'])) {
                    $groupDefArray = $this->reportDef['group_defs'];

                    if (isset($this->reportDef['layout_options']) &&
                        (safeCount($groupDefArray) === 2 || safeCount($groupDefArray) === 3)) {
                        $reportType = 'Matrix';
                    }
                }
            }
        }

        return $reportType;
    }

    /**
     * Returns report specific table data
     *
     * @param bool $header - should include the table header
     * @param bool $recordsCount - should include record count of this report
     *
     * @return array
     */
    public function getListData(bool $header, bool $needTotalCount): array
    {
        return [];
    }

    /**
     * Returns report specific filter data
     *
     * @return array
     */
    public function getFilterData()
    {
        $userBean = \BeanFactory::newBean('Users');
        $users = $userBean ? $userBean->getUserArray(false) : [];
        $users['Current User'] = 'Current User';

        $savedReport = \BeanFactory::retrieveBean('Reports', $this->reportId);
        $dateModified = null;

        if ($savedReport && property_exists($savedReport, 'date_modified')) {
            $dateModified = $savedReport->date_modified;
        }

        if (!$this->reportDef) {
            return [
                'reportDef' => $this->reportDef,
                'runtimeOperators' => $this->getRuntimeOperators(),
                'users' => $users,
                'dateModified' => $dateModified,
                'reportId' => $this->reportId,
            ];
        }

        return [
            'reportType' => $this->getReportType(),
            'reportDef' => $this->reportDef,
            'runtimeOperators' => $this->getRuntimeOperators(),
            'users' => $users,
            'dateModified' => $dateModified,
            'reportId' => $this->reportId,
        ];
    }


    /**
     * Returns report specific filter operators
     *
     * @return array
     */
    public function getRuntimeOperators()
    {
        $fileList = \MetaDataFiles::getClientFiles(['base'], 'filter', 'Reports');
        $results = \MetaDataFiles::getClientFileContents($fileList, 'filter', 'Reports');

        return $results['runtime-operators']['meta'];
    }

    /**
     * Update Report's Cache filters
     *
     * @param array $filtersContent
     */
    public function updateReportFilters(array $filtersContent)
    {
        $reportCache = new \ReportCache();
        $reportCache->retrieve($this->reportId);

        if (empty($reportCache->id)) {
            $reportCache->id = $this->reportId;
            $reportCache->new_with_id = true;
        }

        $cachedFilters = [];
        $cachedFilters['filters_def'] = $filtersContent;
        $reportCache->contents = json_encode($cachedFilters);
        $reportCache->save();
    }

    /**
     * Returns report list header
     *
     * @param \Report $report
     *
     * @return array
     */
    protected function getHeader(\Report $report): array
    {
        return [];
    }

    /**
     * Set the useSavedFilters flag
     *
     * @param bool $use
     */
    public function useCachedFilters(bool $use)
    {
        $this->useSavedFilters = $use;
    }

    /**
     * Adds group filters to report def
     * @param array $reportDef
     *
     * @return array
     */
    protected function addGroupFilters(array $reportDef): array
    {
        // Construct a Report module filter from group filters
        $adhocFilter = [];

        foreach ($this->groupFilters as $filter) {
            foreach ($filter as $field => $value) {
                if (is_string($value)) {
                    $value = [$value];
                }

                $fieldDef = $this->getGroupFilterFieldDef($reportDef, $field);

                if ($fieldDef && !empty($fieldDef['type'])) {
                    $filterRow = [
                        'adhoc' => true,
                        'name' => $fieldDef['name'],
                        'table_key' => $fieldDef['table_key'],
                    ];

                    switch ($fieldDef['type']) {
                        case 'enum':
                            $filterRow['qualifier_name'] = 'one_of';
                            $filterRow['input_name0'] = $value;
                            break;
                        case 'date':
                        case 'datetime':
                        case 'datetimecombo':
                            if (safeCount($value) === 1) {
                                $filterRow['qualifier_name'] = 'on';
                                $filterRow['input_name0'] = reset($value);
                            } else {
                                $filterRow['qualifier_name'] = 'between_dates';
                                $filterRow['input_name0'] = $value[0];
                                $filterRow['input_name1'] = $value[1];
                            }
                            break;
                        case 'radioenum':
                        case 'id':
                            $filterRow['qualifier_name'] = 'is';
                            $filterRow['input_name0'] = reset($value);
                            break;
                        default:
                            $filterRow['qualifier_name'] = 'equals';
                            $filterRow['input_name0'] = reset($value);
                            break;
                    }

                    // special case when the input value is empty string
                    // create a filter similar to the 'Is Empty' filter
                    $firstItem = reset($value);
                    if (!is_string($firstItem) || strlen(reset($value)) === 0) {
                        $filterRow['qualifier_name'] = 'empty';
                        $filterRow['input_name0'] = 'empty';
                        $filterRow['input_name1'] = 'on';
                    }

                    array_push($adhocFilter, $filterRow);
                }
            }
        }

        $adhocFilter['operator'] = 'AND';

        // Make sure Filter_1 is defined
        if (empty($reportDef['filters_def']) || !isset($reportDef['filters_def']['Filter_1'])) {
            $reportDef['filters_def']['Filter_1'] = [];
        }

        $savedReportFilter = $reportDef['filters_def']['Filter_1'];

        // For the conditions [] || {'Filter_1':{'operator':'AND'}}
        if (empty($savedReportFilter) ||
            (sizeof($savedReportFilter) === 1 && isset($savedReportFilter['operator']))
        ) {
            // Just set Filter_1 to adhocFilter
            $newFilter = $adhocFilter;
        } else {
            // Concatenate existing and adhocFilter
            $newFilter = [];
            array_push($newFilter, $savedReportFilter);
            array_push($newFilter, $adhocFilter);
            $newFilter['operator'] = 'AND';
        }

        $reportDef['filters_def']['Filter_1'] = $newFilter;

        return $reportDef;
    }

    /**
     * Sanitize the field label
     *
     * @param array $fieldDef
     * @param array $dataColumn
     *
     * @return array
     */
    protected function sanitizeFieldLabel(array $fieldDef, array $dataColumn): array
    {
        if (array_key_exists('name', $dataColumn) && $dataColumn['name'] === 'count' &&
            !array_key_exists('vname', $dataColumn) && !array_key_exists('label', $dataColumn)) {
            $fieldDef['name'] = 'name';
            $fieldDef['vname'] = translate('LBL_COUNT', 'Reports');
            $fieldDef['isvNameTranslated'] = false;
        } elseif (array_key_exists('label', $dataColumn)) {
            $labelDef = array_key_exists('vname', $fieldDef) ? $fieldDef['vname'] : '';
            $module = array_key_exists('module', $fieldDef) ? $fieldDef['module'] : null;

            $fieldLabel = $this->getFieldLabel($labelDef, $dataColumn['label'], $module);
            $fieldDef['vname'] = $fieldLabel['value'];
            $fieldDef['isvNameTranslated'] = $fieldLabel['translated'];
        } elseif (array_key_exists('vname', $dataColumn)) {
            $module = array_key_exists('module', $dataColumn) ? $dataColumn['module'] : null;
            $fieldLabel = $this->getFieldLabel($dataColumn['vname'], $dataColumn['vname'], $module);
            $fieldDef['vname'] = $fieldLabel['value'];
            $fieldDef['isvNameTranslated'] = !$fieldLabel['translated'];
        }

        return $fieldDef;
    }

    /**
     * Set the module for those special fields like count to allow them to be sortable in the frontend
     *
     * @param array $fieldDef
     * @param Report $report
     */
    protected function setModuleByTableKey(array &$fieldDef, \Report $report)
    {
        if (!array_key_exists('module', $fieldDef) &&
            array_key_exists('table_key', $fieldDef) && $fieldDef['table_key']) {
            $fullTableList = $report->full_table_list;
            $tableAlias = $fieldDef['table_key'];

            if (is_array($fullTableList) && array_key_exists($tableAlias, $fullTableList) &&
                is_array($fullTableList[$tableAlias]) && array_key_exists('module', $fullTableList[$tableAlias])) {
                $fieldDef['module'] = $fullTableList[$tableAlias]['module'];
            }
        }
    }

    /**
     * Gets group field def
     *
     * @param array $reportDef
     * @param string $field
     *
     * @return array|boolean
     */
    protected function getGroupFilterFieldDef(array $reportDef, string $field)
    {
        $pos = strrpos($field, ':');

        $fieldName = $pos !== false ? substr($field, $pos + 1) : $field;
        $tableKey = $pos !== false ? substr($field, 0, $pos) : 'self';

        if (!is_array($reportDef['group_defs'])) {
            return false;
        }

        $report = null;

        foreach ($reportDef['group_defs'] as $groupColumn) {
            if ($groupColumn['table_key'] !== $tableKey || $groupColumn['name'] !== $fieldName) {
                continue;
            }

            if (!empty($groupColumn['type'])) {
                return $groupColumn;
            }

            if (!$report) {
                $report = new \Report($reportDef);
            }

            if (empty($report->full_bean_list[$tableKey])) {
                return $groupColumn;
            }

            $bean = $report->full_bean_list[$tableKey];
            $fieldDef = $bean->getFieldDefinition($fieldName);

            if (!empty($fieldDef['type'])) {
                $groupColumn['type'] = $fieldDef['type'];
            }

            return $groupColumn;
        }

        return false;
    }

    /**
     * Retrieves a chart config so it can be generated on the server
     *
     * @return array
     * @throws SugarApiExceptionNotFound
     */
    public function buildChartConfig()
    {
        $chartConfig = [];
        $reporter = new \Report($this->reportDef);
        $chartData = $this->getChartData();

        if ($chartData) {
            $configBuilder = new ConfigBuilder($chartData['chartData'], $reporter);
            $configBuilder->build();
            $chartConfig = $configBuilder->getConfig();
        }

        return $chartConfig;
    }

    /**
     * Provides information about the change
     * @return array
     */
    public function getLastChangeInfo(): array
    {
        global $timedate;
        global $current_user;

        $reportCache = new \ReportCache();
        $reportCache->retrieve($this->reportId);

        $reportBean = \BeanFactory::retrieveBean('Reports', $this->reportId);

        return [
            'lastReportSeenDate' => $timedate->to_db($reportCache->date_modified),
            'lastReportModifiedDate' => $reportBean->date_modified,
            'currentUserId' => $current_user->id,
            'modifiedUserId' => $reportBean->modified_user_id,
        ];
    }

    /**
     * Get function options
     *
     * Gets the list of options of enum fields which use a PHP function
     *
     * @return array
     */
    protected function getFunctionOptions()
    {
        $fieldDefsToCheck = [];
        if (isset($this->reportDef['group_defs'])) {
            foreach ($this->reportDef['group_defs'] as $fieldDefToCheck) {
                $fieldDefsToCheck[] = $fieldDefToCheck;
            }
        }
        if (isset($this->reportDef['summary_columns'])) {
            foreach ($this->reportDef['summary_columns'] as $fieldDefToCheck) {
                $fieldDefsToCheck[] = $fieldDefToCheck;
            }
        }
        if (isset($this->reportDef['display_columns'])) {
            foreach ($this->reportDef['display_columns'] as $fieldDefToCheck) {
                $fieldDefsToCheck[] = $fieldDefToCheck;
            }
        }

        $functionOptions = [];
        foreach ($fieldDefsToCheck as $fieldDefToCheck) {
            $fieldModule = $this->reportDef['full_table_list'][$fieldDefToCheck['table_key']]['module'];
            $seed = \BeanFactory::newBean($fieldModule);

            if (isset($seed->field_defs[$fieldDefToCheck['name']])) {
                $vardef = $seed->field_defs[$fieldDefToCheck['name']];
                if (isset($vardef['function'])) {
                    $options = getOptionsFromVardef($vardef);
                    $functionOptions[$vardef['function']] = $options;
                }
            }
        }

        return $functionOptions;
    }
}
