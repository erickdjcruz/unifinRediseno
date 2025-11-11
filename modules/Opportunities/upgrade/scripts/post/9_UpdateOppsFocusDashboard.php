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
 * Add RLI tab on first RV dashlet in the opportunities focus dashboards
 */
class SugarUpgradeUpdateOppsFocusDashboard extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     * @throws SugarQueryException
     */
    public function run()
    {
        if (version_compare($this->from_version, '12.0.0', '<')) {
            $this->log('Update opportunities focus drawer dashboard...');
            $this->log('Adding RLI tab to first RV dashlet on Opps Focus Dashboard');
            $this->addRLITabToRVDashlet();
            $this->log('Finished updating opportunities focus drawer dashboard');
        }
    }

    /**
     * Adds RLI tab to first RV Dashlet on Opps Focus Dashboard
     * @throws SugarQueryException
     */
    private function addRLITabToRVDashlet()
    {
        $dashboardBean = BeanFactory::newBean('Dashboards');

        $query = new SugarQuery();
        $query->select(['id', 'metadata']);
        $query->from($dashboardBean);
        $query->where()->equals('id', 'e64f2d12-13cb-11eb-a909-acde48001122');

        $results = $query->execute();

        if (empty($results) || !$results[0]) {
            return;
        }

        $dashboard = $results[0];
        $metadata = json_decode($dashboard['metadata'], true);

        // For customized dashboards, do not add RLI tab
        if (!empty($metadata['dashlets'])) {
            return;
        }

        // If the focus dashboard has not been customized
        if (!empty($metadata['components'])) {
            $rliTab = [
                'active' => false,
                'link' => 'revenuelineitems',
                'module' => 'RevenueLineItems',
                'limit' => 5,
                'fields' => [
                    'name',
                    'quantity',
                    'likely_case',
                    'service_duration',
                    'service_start_date',
                    'service_end_date',
                    'commit_stage',
                ],
            ];
            array_push($metadata['components'][0]['rows'][0][0]['view']['tabs'], $rliTab);
            $metadata['components'][0]['rows'][0][0]['view']['tab_list'] = [
                'Opportunities',
                'revenuelineitems',
            ];

            $this->saveDashboard($dashboard['id'], $metadata);
        }
    }

    /**
     * Saves the dashboard metadata for the given id
     * @param $id
     * @param $metadata
     */
    private function saveDashboard($id, $metadata)
    {
        $bean = BeanFactory::retrieveBean('Dashboards', $id);
        $bean->metadata = json_encode($metadata);
        $bean->save();
    }
}
