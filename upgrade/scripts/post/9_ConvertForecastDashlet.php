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

/**
 * Converts old "In Forecast" (forecastdetails, forecastdetails-record)
 * dashlets in dashboards to the new "Pipeline Metrics"
 * (pipeline-metrics) dashlet
 */
class SugarUpgradeConvertForecastDashlet extends UpgradeScript
{
    public $order = 9252;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '13.1.0', '<')) {
            $this->log('Converting forecastdetails dashlets to pipeline-metrics...');
            $this->convertDashlets();
            $this->log('Done converting forecastdetails dashlets to pipeline-metrics');
        }
    }

    /**
     * Converts metadata for all dashboards with forecastdetails or
     * forecastdetails-record dashlets in the system to replace them with the
     * new pipeline-metrics dashlet
     */
    protected function convertDashlets()
    {
        $dashboards = $this->getTargetDashboards();
        foreach ($dashboards as $dashboard) {
            $dashboardMeta = json_decode($dashboard['metadata'], true);
            if (!empty($dashboardMeta)) {
                $this->convertForecastDashlets($dashboardMeta);
                $this->updateDashboardMetadata($dashboard, $dashboardMeta);
            }
        }
    }

    /**
     * Retrieves the dashboards that contain forecastdetails or
     * forecastdetails-record dashlets
     *
     * @return array the resulting dashboard rows
     */
    protected function getTargetDashboards(): array
    {
        $dashboardBean = BeanFactory::newBean('Dashboards');

        $sq = new SugarQuery();
        $sq->select('id', 'metadata');
        $sq->from($dashboardBean)
            ->where()
            ->like('metadata', '%forecastdetails%');

        return $sq->execute();
    }

    /**
     * Converts all forecastdetails and forecastdetails-record dashlets in a
     * single dashboard's metadata to the new pipeline-metrics dashlet
     *
     * @param array $dashboardMeta
     */
    protected function convertForecastDashlets(&$dashboardMeta)
    {
        foreach ($dashboardMeta as $property => $propertyValue) {
            if (!is_array($propertyValue)) {
                continue;
            }

            if (array_key_exists('view', $propertyValue) && is_array($propertyValue['view'])) {
                $dashboardMeta[$property]['view'] = $this->convertForecastDashlet($propertyValue['view']);
            } else {
                $this->convertForecastDashlets($dashboardMeta[$property]);
            }
        }
    }

    /**
     * Converts a single forecastdetails or forecastdetails-record dashlet to
     * the new pipeline-metrics dashlet
     *
     * @param array $dashlet
     * @return array
     */
    private function convertForecastDashlet(array $dashlet): array
    {
        if (!in_array($dashlet['type'], ['forecastdetails', 'forecastdetails-record'])) {
            return $dashlet;
        }

        return [
            'type' => 'pipeline-metrics',
            'label' => 'LBL_PIPELINE_METRICS_DASHLET_NAME',
            'module' => 'Forecasts',
        ];
    }

    /**
     * Updates the metadata for the given dashboard in the DB
     *
     * @param array $dashboard the dashboard row
     * @param array $dashboardMeta the new JSON-decoded array of dashboard meta
     */
    private function updateDashboardMetadata(array $dashboard, array $dashboardMeta)
    {
        $qb = \DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();

        $metaParam = $qb->createPositionalParameter(json_encode($dashboardMeta));
        $idParam = $qb->createPositionalParameter($dashboard['id']);

        $qb->update('dashboards')
            ->set('metadata', $metaParam)
            ->where($qb->expr()->eq('id', $idParam))
            ->executeStatement();
    }
}
