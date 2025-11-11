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
 *  DefaultDashboardInstaller is used to build the default dashboards.
 */
class DefaultDashboardInstaller
{
    private $globalTeamId = '1';

    /**
     * Builds the default dashboards in the database using the dashboard files.
     *
     * @param array $modules The list of modules available in Sugar
     */
    public function buildDashboardsFromFiles(array $modules)
    {
        // Loop over each module to get each module's dashboard directory
        foreach ($modules as $module) {
            $moduleDir = "modules/$module/dashboards/";
            $layoutDirs = $this->getSubDirs($moduleDir);

            // Loop over each module's dashboard views to get each view dir
            foreach ($layoutDirs as $layoutDir) {
                $layout = basename($layoutDir);
                $dashboardFiles = $this->getPhpFiles($layoutDir);

                // Loop over each dashboard within the view dir
                foreach ($dashboardFiles as $dashboardFile) {
                    $this->buildDashboardFromFile($dashboardFile, $module, $layout);
                }
            }
        }
    }

    /**
     * Build a single dashboard.
     *
     * @param string $dashboardFile Path to the dashboard file.
     * @param string $module Module name.
     * @param string $layout Layout name.
     * @return bool true if installed. false if not installed.
     */
    public function buildDashboardFromFile(string $dashboardFile, string $module, string $layout)
    {
        $id = null;
        $dashboardContents = $this->getFileContents($dashboardFile);
        if (!$dashboardContents) {
            return false;
        }

        // if this dashboard has a preset ID, make sure we haven't installed it before
        if (isset($dashboardContents['id'])) {
            $id = $dashboardContents['id'];
            $bean = $this->getNewDashboardBean();
            $exists = $bean->fetch($id) !== false;
            if ($exists) {
                return false;
            }
        }

        $this->setupSavedReportDashlets($dashboardContents['metadata']);

        $dashboardProperties = [
            'name' => $dashboardContents['name'],
            'dashboard_module' => $module,
            'view_name' => $module !== 'Home' ? $layout : 'home',
            'metadata' => json_encode($dashboardContents['metadata']),
            'default_dashboard' => true,
            'is_template' => isset($dashboardContents['is_template']) ? $dashboardContents['is_template'] : false,
            'team_id' => $this->globalTeamId,
        ];

        if (isset($dashboardContents['assigned_user_id'])) {
            $dashboardProperties['assigned_user_id'] = $dashboardContents['assigned_user_id'];
        }

        // set up preset ID if necessary
        if (isset($dashboardContents['id'])) {
            $dashboardProperties['id'] = $id;
            $dashboardProperties['new_with_id'] = true;
        }

        $dashboardBean = $this->getNewDashboardBean();
        $this->storeDashboard($dashboardBean, $dashboardProperties);
        return true;
    }

    /**
     * Translate a saved Report title.
     *
     * @param string $title The translatable label for the Report title.
     * @return string The Report title, translated into the current language.
     */
    protected function translateSavedReportTitle($title)
    {
        return translate($title, 'Reports');
    }

    /**
     * Adds saved_report_id to metadata for saved report dashlets
     * @param array $metadata
     */
    public function setupSavedReportDashlets(&$metadata)
    {
        // recursively handle tabbed dashboards
        if (!empty($metadata['tabs'])) {
            foreach ($metadata['tabs'] as $index => $tab) {
                $this->setupSavedReportDashlets($metadata['tabs'][$index]);
            }
        }

        if (!empty($metadata['components'])) {
            for ($i = 0; $i < safeCount($metadata['components']); $i++) {
                if (!empty($metadata['components'][$i]['rows'])) {
                    for ($j = 0; $j < safeCount($metadata['components'][$i]['rows']); $j++) {
                        for ($k = 0; $k < safeCount($metadata['components'][$i]['rows'][$j]); $k++) {
                            if (!empty($metadata['components'][$i]['rows'][$j][$k]['view'])) {
                                $view = &$metadata['components'][$i]['rows'][$j][$k]['view'];
                                $isSavedReportsChart = !empty($view['type']) && $view['type'] == 'saved-reports-chart';
                                if ($isSavedReportsChart && empty($view['saved_report_id'])) {
                                    if (!empty($view['saved_report_key'])) {
                                        $title = $this->translateSavedReportTitle($view['saved_report_key']);
                                        if (empty($view['label'])) {
                                            $view['label'] = $title;
                                        }
                                        if (empty($view['saved_report'])) {
                                            $view['saved_report'] = $title;
                                        }
                                        // Assume OOB report names are unique
                                        $report = BeanFactory::getBean('Reports');
                                        $view['saved_report_id'] = $report->retrieveReportIdByName($title);
                                    }
                                    if (empty($view['saved_report_id'])) {
                                        // Remove this dashlet because we can't find the report
                                        installLog('removed invalid report dashlet: ' . print_r($metadata['components'][$i]['rows'][$j][$k], true));
                                        unset($metadata['components'][$i]['rows'][$j][$k]);
                                    }
                                } elseif ($isSavedReportsChart &&
                                    isset($view['saved_report_id']) &&
                                    isset($view['label']) &&
                                    empty($view['saved_report'])
                                ) {
                                    // we don't want to have to repeat the label twice
                                    $view['saved_report'] = $this->translateSavedReportTitle($view['label']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Given a directory, returns all its subdirectories.
     *
     * @param string $dir The base directory
     * @return array Array of subdirectories
     */
    public function getSubDirs($dir)
    {
        return glob($dir . '/*', GLOB_ONLYDIR);
    }

    /**
     * Given a directory, returns all the php files in it.
     *
     * @param string $dir The base directory
     * @return array Array of .php files
     */
    public function getPhpFiles($dir)
    {
        return glob($dir . '/*.php');
    }

    /**
     * Retrieves data from the specified file.
     *
     * @param string $dashboardFile The file to  data.
     *
     * @return array The data from the affiliated file.
     */
    public function getFileContents($dashboardFile)
    {
        return include $dashboardFile;
    }

    /**
     * Using the supplied properties, create and store a new dashboard bean.
     *
     * @param SugarBean $dashboardBean The dashboard bean to populate.
     * @param array $properties The properties to store to the dashboard bean.
     */
    public function storeDashboard($dashboardBean, $properties)
    {
        foreach ($properties as $key => $value) {
            $dashboardBean->$key = $value;
        }
        $dashboardBean->save();
    }

    /**
     * Creates a new blank dashboard bean.
     *
     * @return SugarBean
     */
    public function getNewDashboardBean()
    {
        return BeanFactory::newBean('Dashboards');
    }

    /**
     * Retrieve a system user.
     *
     * @return User A system user.
     */
    public function getAdminUser()
    {
        $user = BeanFactory::newBean('Users');
        if (empty($user)) {
            throw new SugarException('Unable to retrieve user bean.');
        }
        return $user->getSystemUser();
    }
}
