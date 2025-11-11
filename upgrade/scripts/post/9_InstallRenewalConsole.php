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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/**
 * Install the Renewal Console.
 */
class SugarUpgradeInstallRenewalConsole extends UpgradeScript
{
    public $order = 7550;
    public $type = self::UPGRADE_DB;
    public $defaultDashboardInstaller;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldInstallRenewalConsole()) {
            $this->installRenewalConsole();
        } else {
            $this->log('Not installing Renewal Console');
        }
    }

    /**
     * Determine if we should install the Renewal Console
     *
     * @return bool true if we should install the Renewal Console; false otherwise
     */
    public function shouldInstallRenewalConsole(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow930Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.3.0', '<');
        return $isFlavorConversion || $isBelow930Ent;
    }

    /**
     * Install the Renewal Console and its related dashboards
     */
    public function installRenewalConsole()
    {
        $this->log('Installing Renewal Console and its related dashboards');

        $this->defaultDashboardInstaller = new DefaultDashboardInstaller();

        // Install the main Renewal Console dashboard
        $dashboardFile = 'modules/Home/dashboards/renewal-console/renewal-console.php';
        $this->installDashboard($dashboardFile, 'Home', 'renewal-console');

        // Install the Opportunities multi-line drawer dashboard
        $dashboardFile = 'modules/Opportunities/dashboards/multi-line/multi-line.php';
        $this->installDashboard($dashboardFile, 'Opportunities', 'multi-line');

        // Install the Accounts multi-line drawer dashboard
        $dashboardFile = 'modules/Accounts/dashboards/multi-line/multi-line-dashboard.php';
        $this->installDashboard($dashboardFile, 'Accounts', 'multi-line');
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
            $this->log('Did not install Sell Console dashboard: ' . $dashboardFile);
        }
    }
}
