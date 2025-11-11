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
 * Update metadata for some dashboards
 */
class SugarUpgradeUpdateDashboardMetadataKpiMetrics extends UpgradeScript
{
    public $order = 9500;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        if (version_compare($this->from_version, '13.3.0', '>=')) {
            return;
        }

        $this->log('Updating KPI Metrics metadata for Service console dashboard ...');
        $consoleIDs = [
            'c108bb4a-775a-11e9-b570-f218983a1c3e', // Service Console
            'da438c86-df5e-11e9-9801-3c15c2c53980', // Renewals Console
        ];

        $bean = BeanFactory::newBean('Dashboards');
        $query = new SugarQuery();
        $query->select(['id', 'name', 'metadata']);
        $query->from($bean);
        $query->where()->in('id', $consoleIDs);
        $rows = $query->execute();

        foreach ($rows as $row) {
            $metadata = json_decode($row['metadata'], true);

            switch ($row['id']) {
                // Service console
                case 'c108bb4a-775a-11e9-b570-f218983a1c3e':
                    if (version_compare($this->from_version, '12.2.0', '>=')) {
                        $updated = $this->updateKpiMetricsMeta($metadata, 1);
                    } else {
                        $updated = $this->updateConsole(
                            $metadata,
                            'Cases',
                            'service_console',
                            'follow_up_datetime',
                            1
                        );
                        $serviceConsoleUpdate = $this->updateServiceConsole($metadata);
                    }
                    if ($updated || $serviceConsoleUpdate) {
                        $this->doUpdate($metadata, $row);
                    }
                    break;
                    // Renewals console
                case 'da438c86-df5e-11e9-9801-3c15c2c53980':
                    if (version_compare($this->from_version, '12.2.0', '>=')) {
                        $updated = $this->updateKpiMetricsMeta($metadata, 1);
                        $updated = $this->updateKpiMetricsMeta($metadata, 2) || $updated;
                    } else {
                        $updated = $this->updateConsole(
                            $metadata,
                            'Accounts',
                            'renewals_console',
                            'next_renewal_date',
                            1
                        );
                        if ($updated) {
                            $this->doUpdate($metadata, $row);
                        }
                        $updated = $this->updateConsole(
                            $metadata,
                            'Opportunities',
                            'renewals_console',
                            'date_closed',
                            2
                        );
                    }
                    if ($updated) {
                        $this->doUpdate($metadata, $row);
                    }
                    break;
            }
        }

        $this->log('Service console KPI metrics metadata update complete!');
    }

    /**
     * Update the service console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateServiceConsole(&$metadata)
    {
        $updated = false;
        // fix overview tab 'Status of Open Tasks Assigned by Me' dashlet
        if ($metadata['tabs'] && $metadata['tabs'][0] && $metadata['tabs'][0]['components'] &&
            $metadata['tabs'][0]['components']['rows'] && $metadata['tabs'][0]['components']['rows'][2] &&
            $metadata['tabs'][0]['components']['rows'][2][2]) {
            $metadata['tabs'][0]['components']['rows'][2][2] = [
                'width' => 4,
                'context' => [
                    'module' => 'Tasks',
                ],
                'view' => [
                    'label' => 'LBL_REPORT_DASHLET_TITLE_139',
                    'type' => 'saved-reports-chart',
                    'module' => 'Tasks',
                    'saved_report_id' => '0da8f498-beae-11ee-9d94-095590d26ca4',
                ],
            ];
            $updated = true;
        }
        // move badges from cases tab to metrics tab
        if ($metadata['tabs'] && $metadata['tabs'][1]) {
            if (isset($metadata['tabs'][1]['badges'])) {
                unset($metadata['tabs'][1]['badges']);
                $updated = true;
            }
            if ($metadata['tabs'][1]['components'] && $metadata['tabs'][1]['components'][0] &&
                $metadata['tabs'][1]['components'][0]['layout']) {
                $metadata['tabs'][1]['components'][0]['layout']['badges'] = [
                    [
                        'type' => 'record-count',
                        'module' => 'Cases',
                        'filter' => [
                            [
                                'follow_up_datetime' => [
                                    '$lt' => '$nowTime',
                                ],
                            ],
                        ],
                        'cssClass' => 'case-expired',
                        'tooltip' => 'LBL_CASE_OVERDUE',
                    ],
                    [
                        'type' => 'record-count',
                        'module' => 'Cases',
                        'filter' => [
                            [
                                'follow_up_datetime' => [
                                    '$between' => ['$nowTime', '$tomorrowTime'],
                                ],
                            ],
                        ],
                        'cssClass' => 'case-soon',
                        'tooltip' => 'LBL_CASE_DUE_SOON',
                    ],
                    [
                        'type' => 'record-count',
                        'module' => 'Cases',
                        'filter' => [
                            [
                                'follow_up_datetime' => [
                                    '$gt' => '$tomorrowTime',
                                ],
                            ],
                        ],
                        'cssClass' => 'case-future',
                        'tooltip' => 'LBL_CASE_DUE_LATER',
                    ],
                ];
                $updated = true;
            }
        }

        return $updated;
    }

    /**
     * @param $metadata
     * @param $row
     * @throws \Doctrine\DBAL\Exception
     */
    private function doUpdate($metadata, $row)
    {
        $query = 'UPDATE dashboards SET metadata = ? WHERE id = ?';
        $this->db->getConnection()->executeUpdate(
            $query,
            [json_encode($metadata), $row['id']],
            [\Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::STRING]
        );
        $this->log('Metadata is updated for dashboard name = ' . $row['name']);
    }

    /**
     * Update the console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateConsole(&$metadata, $module, $context, $primaryOrderField, $tabNumber): bool
    {
        $kpiMetricsMeta = $this->getConsoleKpiMetricsMeta($module, $context, $primaryOrderField);

        // Add KPI metrics
        if (isset($metadata['tabs']) && isset($metadata['tabs'][$tabNumber])
            && isset($metadata['tabs'][$tabNumber]['components'])) {
            array_unshift($metadata['tabs'][$tabNumber]['components'], $kpiMetricsMeta);
            return true;
        }

        return false;
    }

    /**
     * Get the console KPI Metrics metadata
     * @param string $module
     * @return array
     */
    private function getConsoleKpiMetricsMeta(string $module, string $context, string $primaryOrderField): array
    {
        if ($module) {
            return [
                'layout' => [
                    'name' => 'kpi-metrics',
                    'type' => 'base',
                    'css_class' => 'kpi-metrics flex border-b border-[--border-color]',
                    'metric_module' => $module,
                    'metric_context' => $context,
                    'order_by_primary' => $primaryOrderField,
                    'components' => [
                        [
                            'context' => [
                                'module' => $module,
                            ],
                            'layout' => 'kpi-metrics-tabs',
                        ],
                        [
                            'context' => [
                                'module' => $module,
                            ],
                            'view' => 'kpi-metrics-tools',
                        ],
                    ],
                ],
            ];
        }
    }

    /**
     * Update KpiMetricsMeta
     * @param array $metadata
     * @param integer $tabNumber
     * @return bool
     */
    private function updateKpiMetricsMeta(array &$metadata, int $tabNumber): bool
    {
        if (isset($metadata['tabs'][$tabNumber]['components'][0]['layout']['name']) &&
            $metadata['tabs'][$tabNumber]['components'][0]['layout']['name'] === 'kpi-metrics') {
            $metadata['tabs'][$tabNumber]['components'][0]['layout']['css_class'] =
                'kpi-metrics flex border-b border-[--border-color]';
            return true;
        }
        return false;
    }
}
