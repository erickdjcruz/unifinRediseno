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
 * Install the Focus Drawer dashboards
 */
class SugarUpgradeInstallFocusDrawerDashboards extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;
    public $defaultDashboardInstaller;

    private $focusDrawerModules = [
        'Accounts', 'Bugs', 'Calls', 'Cases', 'Contacts', 'Contracts', 'DataPrivacy',
        'Leads', 'Meetings', 'Notes', 'Opportunities', 'pmse_Business_Rules',
        'pmse_Project', 'pmse_Emails_Templates', 'ProductTemplates', 'Products',
        'Quotes', 'RevenueLineItems', 'Shifts', 'Tags', 'ProspectLists', 'Prospects',
        'Tasks', 'BusinessCenters', 'Purchases', 'PurchasedLineItems',
    ];

    private $focusDrawerExclude = [
        'Home', 'Forecasts', 'Messages', 'Emails', 'OutboundEmail', 'Reports', 'pmse_Inbox',
        'KBContents',
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldInstallDashboards()) {
            $this->log('Installing focus drawer dashboards for stock modules...');
            $this->installFocusDrawerDashboards($this->focusDrawerModules);

            $customModules = $this->getCustomNonBwcModules();
            if (!empty($customModules)) {
                $this->log('Installing focus drawer dashboards for custom modules...');
                $this->copyFocusDrawerDashboardFiles($customModules);
                $this->createDashboardLabels($customModules);
                $this->installFocusDrawerDashboards($customModules, true);
            }
        }
    }

    /**
     * Determines if dashboards need to be installed - either on upgrade or pro->ent conversion
     * @return bool
     */
    public function shouldInstallDashboards()
    {
        $isProToEnt = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isEntUpgrade = version_compare($this->from_version, '10.3.0', '<') && $this->toFlavor('ent');
        return $isProToEnt || $isEntUpgrade;
    }

    /**
     * Gets the list of non-BWC custom modules that need to have a focus dashboard installed.
     *
     * @return array
     */
    public function getCustomNonBwcModules()
    {
        global $moduleList;

        $mm = MetaDataManager::getManager();

        $diff = array_diff($moduleList, $this->focusDrawerModules, $this->focusDrawerExclude);
        foreach ($diff as $idx => $moduleName) {
            if ($mm->getModuleData($moduleName)['isBwcEnabled']) {
                unset($diff[$idx]);
            }
        }

        return array_values($diff);
    }

    /**
     * Copies the focus drawer dashboard files to the specified modules.
     *
     * @param $moduleList
     */
    public function copyFocusDrawerDashboardFiles($moduleList)
    {
        $sourceDir = 'include/SugarObjects/templates/basic/dashboards/focus/';
        foreach ($moduleList as $module) {
            $moduleDir = BeanFactory::getModuleDir($module);
            $destDir = 'custom/modules/' . $moduleDir . '/dashboards/focus/';
            if (!file_exists($destDir)) {
                mkdir_recursive($destDir);
            }

            // Do this similarly to how MBModule/copyMetaRecursive() does it
            $contents = file_get_contents($sourceDir . 'focus-dashboard.php');
            $contents = str_replace('<module_name>', $module, $contents);
            $contents = str_replace('<MODULE_NAME>', strtoupper($module), $contents);
            $fw = sugar_fopen($destDir . 'focus-dashboard.php', 'w');
            fwrite($fw, $contents);
            fclose($fw);
        }
    }

    /**
     * Creates the dashboard label strings for custom modules.
     *
     * @param $moduleList
     */
    public function createDashboardLabels($moduleList)
    {
        foreach ($moduleList as $module) {
            $moduleName = translate('LBL_MODULE_NAME', $module);

            $labelKey = 'LBL_' . strtoupper($module) . '_FOCUS_DRAWER_DASHBOARD';
            $labelValue = $moduleName . ' ' . translate('LBL_FOCUS_DRAWER_DASHBOARD');

            ParserLabel::addLabels($GLOBALS['current_language'], [
                $labelKey => $labelValue,
            ], $module);
        }
    }

    /**
     * Install the focus drawer dashboards.
     *
     * @param $moduleList
     */
    public function installFocusDrawerDashboards($moduleList, $fromCustom = false)
    {
        foreach ($moduleList as $module) {
            $this->defaultDashboardInstaller = new DefaultDashboardInstaller();
            $this->log('Installing focus drawer dashboard for module ' . $module);
            $path = 'modules/' . $module . '/dashboards/focus/focus-dashboard.php';
            if ($fromCustom) {
                $path = 'custom/' . $path;
            }
            $this->installDashboard($path, $module, 'focus');
        }
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
