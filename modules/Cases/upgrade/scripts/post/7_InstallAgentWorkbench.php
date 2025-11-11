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
 * Install the Agent Workbench and its corresponding drawer dashboard.
 */
class SugarUpgradeInstallAgentWorkbench extends UpgradeScript
{
    public $order = 7550;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldInstallWorkbench()) {
            $this->installAgentWorkbench();
        } else {
            $this->log('Not installing the Agent Workbench');
        }
    }

    /**
     * Determine if we should install the Agent Workbench.
     *
     * @return bool true if we should install the Agent Workbench and its
     *   dependencies, false otherwise.
     */
    public function shouldInstallWorkbench(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow910Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.1.0', '<');
        return $isFlavorConversion || $isBelow910Ent;
    }

    /**
     * Install the Agent Workbench and Cases' multi-line-dashboard.
     */
    public function installAgentWorkbench()
    {
        $this->log('Installing Agent Workbench and dependencies');

        $this->defaultDashboardInstaller = new DefaultDashboardInstaller();

        // dashboard for side pane
        $multiLineDashboardFile = 'modules/Cases/dashboards/multi-line/multi-line-dashboard.php';
        $this->installDashboard($multiLineDashboardFile, 'Cases', 'multi-line');

        // Agent Workbench itself
        $agentWorkbenchDashboardFile = 'modules/Home/dashboards/agent-dashboard/agent-dashboard.php';
        $this->installDashboard($agentWorkbenchDashboardFile, 'Home', 'agent-dashboard');
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
            $this->log('Did not install Serve dashboard: ' . $dashboardFile);
        }
    }
}
