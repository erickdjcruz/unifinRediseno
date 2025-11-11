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

use Sugarcrm\Sugarcrm\Reports\ReportFactory;
use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;
use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 * Create default Reports Panels
 */
class ConvertToReportDashlet
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $dashboards = $this->getTargetDashboards();

        foreach ($dashboards as $dashboard) {
            $dashboardMeta = $dashboard['metadata'];
            $dashboard['metadata'] = json_decode($dashboardMeta, true);

            $this->convertToReportsDashlet($dashboard);
            $this->updateDashboardsMeta($dashboard);
        }
    }

    /**
     * Convert all saved reports chart dashlets to report dashlets
     *
     * @param array|string $data
     */
    private function convertToReportsDashlet(&$data)
    {
        foreach ($data as $property => $propertyValue) {
            if (!is_array($propertyValue)) {
                continue;
            }

            if (array_key_exists('view', $propertyValue) && is_array($propertyValue['view'])) {
                $data[$property]['view'] = $this->massageDashletSettings($propertyValue['view']);
            } else {
                $this->convertToReportsDashlet($data[$property]);
            }
        }
    }

    /**
     * Change the keys and add additional data to meta
     *
     * @param array $settings
     *
     * @return array
     */
    private function massageDashletSettings(array $settings): array
    {
        if (!array_key_exists('type', $settings) ||
            !$settings['type'] ||
            $settings['type'] !== 'saved-reports-chart' ||
            !array_key_exists('saved_report_id', $settings) ||
            !$settings['saved_report_id']) {
            return $settings;
        }

        $mappingTable = [
            'saved_report_id' => 'reportId',
            'show_legend' => 'showLegend',
            'show_title' => 'showTitle',
            'show_x_label' => 'showXLabel',
            'show_y_label' => 'showYLabel',
            'x_axis_label' => 'xAxisLabel',
            'y_axis_label' => 'yAxisLabel',
            'report_title' => 'title',
            'auto_refresh' => 'autoRefresh',
        ];

        $chartTypesMapping = [
            'pie chart' => 'pieF',
            'donut chart' => 'donutF',
            'treemap chart' => 'treemapF',
            'bar chart' => 'vBarF',
            'group by chart' => 'vGBarF',
            'horizontal bar chart' => 'hBarF',
            'horizontal group by chart' => 'hGBarF',
            'line chart' => 'lineF',
            'funnel chart' => 'funnelF',
        ];

        $reportDashletSettings = [];

        foreach ($settings as $propertyName => $propertyValue) {
            $reportDashletKey = array_key_exists($propertyName, $mappingTable) ?
                $mappingTable[$propertyName] :
                $propertyName;

            $reportDashletSettings[$reportDashletKey] = $propertyValue;
        }

        $reportDashletSettings['type'] = 'report-dashlet';

        if (array_key_exists('chart_type', $settings)) {
            $oldType = $settings['chart_type'];

            if (array_key_exists($oldType, $chartTypesMapping)) {
                $reportDashletSettings['chartType'] = $chartTypesMapping[$oldType];
            }
        }

        // do not fetch report meta unless we have a valid report id
        if (!$reportDashletSettings['reportId']) {
            return $settings;
        }

        return array_merge($reportDashletSettings, $this->getReportMeta($reportDashletSettings['reportId']));
    }

    /**
     * Build and retrieve the full report meta given a reportId
     *
     * @param string $reportId
     *
     * @return array
     */
    private function getReportMeta(string $reportId): array
    {
        $reportMeta = [];

        try {
            $report = ReportFactory::getReport(ReportType::DEFAULT, ['record' => $reportId]);

            $reportDef = $report->getReportDef();
            $reportType = $report->getReportType();

            $defaultOrderBy = [];

            if (array_key_exists('summary_order_by', $reportDef)) {
                $defaultOrderBy = $reportDef['summary_order_by'];
            }

            if (array_key_exists('order_by', $reportDef)) {
                $defaultOrderBy = $reportDef['order_by'];
            }

            $reportMeta = [
                'module' => $reportDef['module'],
                'filtersDef' => $reportDef['filters_def'],
                'fullTableList' => $reportDef['full_table_list'],
                'groupDefs' => $reportDef['group_defs'],
                'displayColumns' => $reportDef['display_columns'],
                'summaryColumns' => $reportDef['summary_columns'],
                'reportName' => $reportDef['report_name'],
                'listOrderBy' => $defaultOrderBy,
                'reportType' => $reportType,
                'uniqueStateId' => Uuid::uuid4(),
            ];
        } catch (\Throwable $th) {
            return $reportMeta;
        }

        return $reportMeta;
    }

    /**
     * Retrieve the dashboards that contains saved-reports-charts
     *
     * @return array
     */
    private function getTargetDashboards(): array
    {
        $result = [];
        $query = 'SELECT id, metadata FROM dashboards';
        $stmt = $GLOBALS['db']->getConnection()->executeQuery($query);
        foreach ($stmt->iterateAssociative() as $row) {
            if (str_contains($row['metadata'], 'saved-reports-chart')) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Update the dashboards metadata
     *
     * @param array $dashboardMeta
     */
    private function updateDashboardsMeta(array $dashboardMeta)
    {
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();

        $metaParam = $qb->createPositionalParameter(json_encode($dashboardMeta['metadata']));
        $idParam = $qb->createPositionalParameter($dashboardMeta['id']);

        $qb->update('dashboards')
            ->set('metadata', $metaParam)
            ->where($qb->expr()->eq('id', $idParam))
            ->executeStatement();
    }
}
