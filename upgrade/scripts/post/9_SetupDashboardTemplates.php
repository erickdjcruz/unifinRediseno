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
 * Update is_template flag for Template Dashboards
 */
class SugarUpgradeSetupDashboardTemplates extends UpgradeScript
{
    public $order = 9500;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        $this->log('Running 9_SetupDashboardTemplates script...');

        // install the new Dashboards as templates
        if (version_compare($this->from_version, '14.0.0', '<')) {
            $this->log('Installing new Template Dashboards');
            $this->installTemplateDashboards();
            $this->log('Successfully Installed new Template Dashboards');
        } else {
            $this->log('Not installing new Template Dashboards');
        }
    }

    /**
     * Install the Template Dashboards
     */
    public function installTemplateDashboards()
    {
        $dashboardsToBuild = [
            'bdr-dashboard',
            'customer-success-dashboard',
            'executive-dashboard',
            'marketing-dashboard',
            'sales-manager-dashboard',
            'sales-rep-dashboard',
        ];

        $defaultDashboardInstaller = new DefaultDashboardInstaller();

        $module = 'Home';
        $moduleDir = "modules/$module/dashboards/";
        $layoutDirs = $defaultDashboardInstaller->getSubDirs($moduleDir);

        // Loop over module's dashboard views to get each view dir
        foreach ($layoutDirs as $layoutDir) {
            $layout = basename($layoutDir);
            $dashboardFiles = $defaultDashboardInstaller->getPhpFiles($layoutDir);

            foreach ($dashboardFiles as $dashboardFile) {
                if (in_array($layout, $dashboardsToBuild)) {
                    $defaultDashboardInstaller->buildDashboardFromFile($dashboardFile, $module, $layout);
                }
            }
        }
    }
}
