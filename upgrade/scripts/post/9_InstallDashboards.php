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
 * Install the Dashboards.
 */
class SugarUpgradeInstallDashboards extends UpgradeScript
{
    public $order = 7552;
    public $type = self::UPGRADE_DB;
    public $defaultDashboardInstaller;

    private $dashboards = [
        [
            'file' => 'modules/Purchases/dashboards/records/list-dashboard.php',
            'module' => 'Purchases',
            'layout' => 'records',
            'from_flavor' => 'ent',
            'to_flavor' => 'ent',
            'version' => '10.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Purchases/dashboards/record/record-dashboard.php',
            'module' => 'Purchases',
            'layout' => 'record',
            'from_flavor' => 'ent',
            'to_flavor' => 'ent',
            'version' => '10.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/PurchasedLineItems/dashboards/records/list-dashboard.php',
            'module' => 'PurchasedLineItems',
            'layout' => 'records',
            'from_flavor' => 'ent',
            'to_flavor' => 'ent',
            'version' => '10.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/PurchasedLineItems/dashboards/record/record-dashboard.php',
            'module' => 'PurchasedLineItems',
            'layout' => 'record',
            'from_flavor' => 'ent',
            'to_flavor' => 'ent',
            'version' => '10.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Dashboards/dashboards/omnichannel/omnichannel.php',
            'module' => 'Dashboards',
            'layout' => 'omnichannel',
            'from_flavor' => 'ent',
            'to_flavor' => 'ent',
            'version' => '10.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/ProductTemplates/dashboards/records/list-dashboard.php',
            'module' => 'ProductTemplates',
            'layout' => 'records',
            'version' => '10.3',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/ProductTemplates/dashboards/record/record-dashboard.php',
            'module' => 'ProductTemplates',
            'layout' => 'record',
            'version' => '10.3',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Contacts/dashboards/record/record-dashboard.php',
            'module' => 'Contacts',
            'layout' => 'record',
            'from_flavor' => 'pro',
            'to_flavor' => 'pro',
            'version' => '11.1',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Documents/dashboards/record/record-dashboard.php',
            'module' => 'Documents',
            'layout' => 'record',
            'version' => '11.1',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Escalations/dashboards/record/record-dashboard.php',
            'module' => 'Escalations',
            'layout' => 'record',
            'version' => '11.2',
            'version_operator' => '<',
        ],
        [
            'file' => 'modules/Escalations/dashboards/records/list-dashboard.php',
            'module' => 'Escalations',
            'layout' => 'records',
            'version' => '11.2',
            'version_operator' => '<',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->log('Installing dashboards...');
        foreach ($this->dashboards as $dashboard) {
            if ($this->shouldInstallDashboard(
                $dashboard['version'],
                $dashboard['from_flavor'],
                $dashboard['to_flavor'],
                $dashboard['version_operator']
            )) {
                $this->defaultDashboardInstaller = new DefaultDashboardInstaller();
                $this->log('Installing dashboard: ' . $dashboard['file']);
                $this->installDashboard($dashboard['file'], $dashboard['module'], $dashboard['layout']);
            } else {
                $this->log($dashboard['file'] . ' does not meet install criteria');
            }
        }
        $this->log('Dashboards installation complete!');
    }

    /**
     * Determine if we should install the dashboards
     *
     * @param string $version To the version being upgraded
     * @param string|null $from_flavor Current flavor
     * @param string|null $to_flavor To the flavor being upgraded to
     * @param string $version_operator Version comparision operator
     * @return bool true if we should install the dashboards; false otherwise
     */
    public function shouldInstallDashboard(
        string $version,
        string $from_flavor = null,
        string $to_flavor = null,
        string $version_operator = '<'
    ): bool {

        $isUpgradeToVersion = version_compare($this->from_version, $version, $version_operator);
        if (!is_null($from_flavor) && !is_null($to_flavor)) {
            $isFlavorConversion = !$this->fromFlavor($from_flavor) && $this->toFlavor($to_flavor);
            $isBelow = $this->toFlavor($to_flavor) && $isUpgradeToVersion;
            return $isFlavorConversion || $isBelow;
        }
        return $isUpgradeToVersion;
    }

    /**
     * Install the specified dashboard and log a message if not installed.
     *
     * @param string $dashboardFile Relative path to the dashboard file.
     * @param string $module Module name.
     * @param string $layout Layout name.
     */
    public function installDashboard(string $dashboardFile, string $module, string $layout)
    {
        $result = $this->defaultDashboardInstaller->buildDashboardFromFile($dashboardFile, $module, $layout);
        if (!$result) {
            $this->log('Did not install dashboard: ' . $dashboardFile);
        }
    }
}
