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
 * Install the Record dashboards
 */
class SugarUpgradeInstallRecordDashboards extends UpgradeScript
{
    public $order = 7555;
    public $type = self::UPGRADE_DB;
    public $defaultDashboardInstaller;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        global $moduleList;

        $moduleNotInMegaMenu = ['ReportSchedules', 'ShiftExceptions',];
        $modules = array_merge($moduleList, $moduleNotInMegaMenu);

        $this->log('Installing dashboards...');

        $versionCompare = version_compare($this->from_version, '11.0.0', '<');

        if ($versionCompare) {
            $dashboardBean = BeanFactory::newBean('Dashboards');
            $query = new SugarQuery();
            $query->select(['dashboard_module']);
            $query->from($dashboardBean, ['add_deleted' => false,]);
            $query->where()
                ->equals('view_name', 'record');
            $results = $query->execute();
            $modulesToSkip = ['Home', 'Reports', 'Messages', 'OutboundEmail',];
            foreach ($results as $module) {
                array_push($modulesToSkip, $module['dashboard_module']);
            }

            $this->defaultDashboardInstaller = new DefaultDashboardInstaller();
            $mm = MetaDataManager::getManager();
            foreach ($modules as $module) {
                if (!in_array($module, $modulesToSkip) && !$mm->getModuleData($module)['isBwcEnabled']) {
                    if ($this->isCustomModule($module)) {
                        $this->copyCustomRecordDashboardFiles($module);
                        $this->createDashboardLabels($module);
                    }
                    $this->log('Installing record dashboard for: ' . $module);
                    $this->installDashboard($this->dashboardFile($module), $module, 'record');
                } else {
                    $this->log('Dashboard for ' . $module . ' already exist/deleted');
                }
            }
            $this->log('Dashboards installation complete!');
        } else {
            $this->log('Version did not meet installation criteria.');
        }
    }

    /**
     * Check if it is a custom module
     * @param $module module to check
     * @return bool return true if it is a custom module
     */
    public function isCustomModule($module)
    {
        if (strpos($module, 'pmse_') !== false) {
            return false;
        }
        $customFiles = glob('modules/*/*_sugar.php', GLOB_NOSORT);
        foreach ($customFiles as $customFile) {
            $moduleName = str_replace('_sugar', '', pathinfo($customFile, PATHINFO_FILENAME));
            if ($module === $moduleName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Copies the record dashboard files to the specified modules.
     *
     * @param $module
     */
    public function copyCustomRecordDashboardFiles($module)
    {
        $sourceDir = 'include/SugarObjects/templates/basic/dashboards/record/';
        $moduleDir = BeanFactory::getModuleDir($module);
        $destDir = 'modules/' . $moduleDir . '/dashboards/record/';
        if (!file_exists($destDir)) {
            mkdir_recursive($destDir);
        }

        // Do this similarly to how MBModule/copyMetaRecursive() does it
        $contents = file_get_contents($sourceDir . 'record-dashboard.php');
        $contents = str_replace('<module_name>', $module, $contents);
        $contents = str_replace('<MODULE_NAME>', strtoupper($module), $contents);
        $fw = sugar_fopen($destDir . 'record-dashboard.php', 'w');
        fwrite($fw, $contents);
        fclose($fw);
    }

    /**
     * Creates the dashboard label strings for custom modules.
     *
     * @param $module
     */
    public function createDashboardLabels($module)
    {
        $moduleName = translate('LBL_MODULE_NAME', $module);

        $labelKey = 'LBL_' . strtoupper($module) . '_RECORD_DASHBOARD';
        $labelValue = $moduleName . ' ' . translate('LBL_RECORD_DASHBOARD');

        ParserLabel::addLabels($GLOBALS['current_language'], [
            $labelKey => $labelValue,
        ], $module);
    }

    /**
     * @param string $module Module name
     * @return string file path to the dashboard metadata
     */
    public function dashboardFile(string $module): string
    {
        $moduleDir = BeanFactory::getModuleDir($module);
        return 'modules/' . $moduleDir . '/dashboards/record/record-dashboard.php';
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
