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

use Sugarcrm\Sugarcrm\AccessControl\AdminWork;

/**
 * Install the Portal Home Dashboard.
 */
class SugarUpgradeInstallPortalHomeDashboard extends UpgradeScript
{
    public $order = 7551;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldInstallDashboard()) {
            $this->installDashboard();
        } else {
            $this->log('Not installing the Portal Home Dashboard');
        }
    }

    /**
     * Determine if we should install the Portal Home Dashboard.
     *
     * @return bool true if we should install the Portal Home Dashboard and its
     *   dependencies, false otherwise.
     */
    public function shouldInstallDashboard(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow920Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.2.0', '<');
        return $isFlavorConversion || $isBelow920Ent;
    }

    /**
     * Install the specified dashboard and log a message if not installed.
     */
    public function installDashboard()
    {
        $this->log('Temporarily enabling admin work for Portal Home Dashboard installation');
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        $this->log('Installing Portal Home Dashboard and dependencies');

        $this->defaultDashboardInstaller = new DefaultDashboardInstaller();

        $dashboardFile = 'modules/Home/dashboards/portal-home/portal-home.php';

        $result = $this->defaultDashboardInstaller->buildDashboardFromFile($dashboardFile, 'Home', 'portal-home');
        if (!$result) {
            $this->log('Did not install Portal dashboard: ' . $dashboardFile);
        }
    }
}
