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
 * Installs various items for Shifts and Shift Exceptions
 */
class SugarUpgradeInstallShiftsAndShiftExceptions extends UpgradeDBScript
{
    public $order = 9800;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (!$this->shouldInstall()) {
            $this->log('Not installing items for Shifts and Shift Exceptions');
            return;
        }

        $this->installShiftExceptionDefaultDashboard();
    }

    /**
     * Determine if we should install items for Shifts and Shift Exceptions
     *
     * @return bool true if we should install
     */
    public function shouldInstall()
    {
        $isFlavorConversion = ($this->fromFlavor('ent') || $this->fromFlavor('pro')) && $this->toFlavor('ent');
        $isBelow1010 = version_compare($this->from_version, '10.1.0', '<');
        return $isFlavorConversion && $isBelow1010;
    }

    /**
     * Installs the Shift Exceptions default dashboard for the Shifts module
     */
    public function installShiftExceptionDefaultDashboard()
    {
        $fileName = 'modules/Shifts/dashboards/records/list-dashboard.php';

        $dashboardInstaller = new DefaultDashboardInstaller();
        $result = $dashboardInstaller->buildDashboardFromFile($fileName, 'Shifts', 'records');

        if (!$result) {
            $this->log('Did not install Shift Exceptions default dashboard');
        }
    }
}
