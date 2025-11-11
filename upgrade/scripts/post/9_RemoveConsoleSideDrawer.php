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
 * Remove the vestigial side drawer component from the console dashboards
 */
class SugarUpgradeRemoveConsoleSideDrawer extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;

    private $consoleIDs = [
        'c108bb4a-775a-11e9-b570-f218983a1c3e', // Service Console
        'da438c86-df5e-11e9-9801-3c15c2c53980', // Renewals Console
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->log('Removing side drawers from console dashboards...');
        if (version_compare($this->from_version, '11.2.0', '<')) {
            foreach ($this->consoleIDs as $consoleID) {
                $this->log('Removing side drawer from dashboard ' . $consoleID);
                $this->removeSideDrawersForId($consoleID);
            }
        }
        $this->log('Done removing side drawers from console dashboards');
    }

    /**
     * Remove the side drawer from the specified dashboard
     * @param $id
     * @throws SugarQueryException
     */
    private function removeSideDrawersForId($id)
    {
        $dashboard = $this->getDashboard($id);
        if (!$dashboard) {
            return;
        }

        $metadata = json_decode($dashboard['metadata'], true);

        foreach ($metadata['tabs'] as $tabIdx => $tab) {
            if (empty($tab['components'])) {
                continue;
            }
            foreach ($tab['components'] as $componentIdx => $component) {
                if (empty($component['layout']) ||
                    empty($component['layout']['name']) ||
                    $component['layout']['name'] !== 'side-drawer'
                ) {
                    continue;
                }
                unset($metadata['tabs'][$tabIdx]['components'][$componentIdx]);
            }
        }

        $this->saveDashboard($id, $metadata);
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

    /**
     * Gets the specified dashboard by ID
     * @param $id
     * @return array|null
     * @throws SugarQueryException
     */
    private function getDashboard($id)
    {
        $dashboardBean = BeanFactory::newBean('Dashboards');

        $query = new SugarQuery();
        $query->select(['metadata']);
        $query->from($dashboardBean);
        $query->where()->equals('id', $id);

        $results = $query->execute();

        if (!empty($results)) {
            return $results[0];
        } else {
            return null;
        }
    }
}
