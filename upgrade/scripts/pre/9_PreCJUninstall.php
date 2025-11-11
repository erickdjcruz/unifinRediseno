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

class SugarUpgradePreCJUninstall extends UpgradeScript
{
    public $type = self::UPGRADE_CUSTOM;
    public $order = 9999;

    private $CJPackages = [];
    private $customCJPackages = [];

    public function run()
    {
        if ($this->shouldRun()) {
            $this->log('CustomerJourney package: Fetch Custom CJ package(s)');
            $this->fetchCustomCJPackages();
            $this->log('CustomerJourney package: Uninstall All CJ package(s)');
            $this->uninstallAllCJPackages();
        }
    }

    /**
     * Determines if this upgrader should run
     *
     * @return bool true if the upgrader should run
     */
    protected function shouldRun()
    {
        $query = new SugarQuery();
        $query->from(\BeanFactory::newBean('UpgradeHistory'));
        $query->where()->starts('id_name', 'addoptify-customer-journey');
        $result = $query->execute();

        foreach ($result as $row) {
            array_push($this->CJPackages, $row);
        }

        return is_countable($this->CJPackages) ? count($this->CJPackages) : 0 > 0 && version_compare($this->from_version, '12.3.0', '<');
    }

    /**
     * Retrieve package of adoptify-customer-journey, uninstall and delete the package
     */
    public function uninstallAllCJPackages()
    {
        $removeTables = false;

        $this->backupCJConfig();
        $this->removeCJFromSystemTabs();

        foreach ($this->customCJPackages as $package) {
            try {
                $packageManager = new \Sugarcrm\Sugarcrm\PackageManager\PackageManager();
                $upgradeHistory = (new UpgradeHistory())->retrieve($package['id']);
                $this->log('CustomerJourney package: Uninstalling custom packages');
                if ($upgradeHistory->status === UpgradeHistory::STATUS_INSTALLED) {
                    $packageManager->uninstallPackage($upgradeHistory, $removeTables);
                    $this->log('CustomerJourney package: Uninstalled custom packages');
                }
                $this->log('CustomerJourney package: Deleting custom packages');
                $packageManager->deletePackage($upgradeHistory);
                $this->log('CustomerJourney package: Deleted custom packages');
            } catch (ModuleInstallerException $e) {
                $this->log('CustomerJourney Uninstaller Exception : ' . $e->getMessage());
            } catch (Exception $e) {
                $this->log('Deploy package error: ' . $e->getMessage());
            }
        }

        $this->log('CustomerJourney package: Start Deleting Directories');
        $cjDirectories = array_merge($this->getCJModuleDirectories(), $this->cjDirectories);
        foreach ($cjDirectories as $cjDirectory) {
            $cjFiles = [];
            $this->scandir_rec($cjDirectory, $cjFiles);

            foreach ($cjFiles as $file) {
                if (file_exists($file)) {
                    $success = unlink($file);
                    if ($success) {
                        $this->log('CustomerJourney package: Deleted File: ' . $file);
                    } else {
                        $this->log('CustomerJourney package: NOT Deleted File: ' . $file);
                    }
                }
            }
        }
        $this->log('CustomerJourney package:Completed Deleting Directories');

        $this->log('CustomerJourney package:Start Deleting Files: ');
        $deleteFiles = array_merge($this->getCJLanguageFiles(), $this->deleteFiles);

        foreach ($deleteFiles as $toDeleteFile) {
            if (file_exists($toDeleteFile)) {
                if (unlink($toDeleteFile)) {
                    $this->log('CustomerJourney package: Deleted File: ' . $toDeleteFile);
                } else {
                    $this->log('CustomerJourney package: NOT Deleted File: ' . $toDeleteFile);
                }
            }
        }
        $this->log('CustomerJourney package:Completed Deleting Files: ');

        foreach ($this->CJPackages as $package) {
            try {
                $this->log('CustomerJourney package:Mark this CJ Package Deleted: ');
                $qb = DBManagerFactory::getConnection()->createQueryBuilder();
                $qb->update('upgrade_history')
                    ->set('status', $qb->expr()->literal('staged'))
                    ->set('deleted', $qb->expr()->literal('1'))
                    ->where($qb->expr()->eq('id', $qb->expr()->literal($package['id'])));
                $qb->execute();
                $this->log('CustomerJourney package:Marked the CJ Package Deleted: ');
            } catch (Exception $e) {
                $this->log('CustomerJourney package: Delete default files error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get the files inside directory
     */
    protected function scandir_rec($rootDir, &$files)
    {
        if (is_file($rootDir) || !is_dir($rootDir)) {
            $files[] = $rootDir;
            return;
        }

        $dirs = scandir($rootDir);
        foreach ($dirs as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }

            $path = $rootDir . '/' . $dir;
            $this->scandir_rec($path, $files);
        }
    }

    /**
     * Backup previous CJ config values in config table
     */
    protected function backupCJConfig()
    {
        global $db;

        $connection = $db->getConnection();
        $configurator = new Configurator();

        $configurator->loadConfig();
        $cjBackup = json_encode($configurator->config['additional_js_config']['customer_journey']);

        // delete previous value for cjBackup
        $connection->delete(
            'config',
            ['category' => 'cjBackup']
        );
        $connection->insert(
            'config',
            ['value' => $cjBackup, 'category' => 'cjBackup', 'name' => 'customer_journey']
        );
    }

    /**
     * Remove CJ related modules from System Tabs
     */
    protected function removeCJFromSystemTabs()
    {
        global $db;

        $enabledCJTabs = [];
        $connection = $db->getConnection();
        $tabController = new TabController();
        $currentTabs = $tabController->get_system_tabs();

        foreach ($this->cjModules as $module) {
            if (in_array($module, $currentTabs)) {
                unset($currentTabs[$module]);
                $enabledCJTabs[$module] = $module;
            }
        }

        if (empty($enabledCJTabs)) {
            return;
        }

        // Backup previous CJ enabled tabs
        $connection->insert(
            'config',
            ['value' => json_encode($enabledCJTabs), 'category' => 'cjBackup', 'name' => 'enabledCJTabs']
        );

        $tabController->set_system_tabs($currentTabs);
    }

    /**
     * Fetches custom CJ packages which were created via configure modules option
     */
    protected function fetchCustomCJPackages()
    {
        $query = new SugarQuery();
        $query->from(\BeanFactory::newBean('UpgradeHistory'));
        $query->where()->starts('id_name', 'addoptify-customer-journey-custom-modules');
        $result = $query->execute();

        foreach ($result as $row) {
            array_push($this->customCJPackages, $row);
        }
    }

    /**
     * Returns a list of module directories for CJP
     * @return array
     */
    protected function getCJModuleDirectories()
    {
        $dirs = [];
        foreach ($this->cjModules as $module) {
            $dirs[] = 'modules/' . $module;
            $dirs[] = 'custom/Extension/modules/' . $module;
            $dirs[] = 'custom/modules/' . $module;
        }
        return $dirs;
    }

    /**
     * Returns a list of language files for CJP
     * @return array
     */
    protected function getCJLanguageFiles()
    {
        $files = [];
        $modules = [
            'Tasks',
            'Calls',
            'Meetings',
            'Users',
        ];
        foreach ($modules as $module) {
            $moduleFiles = glob('custom/Extension/modules/' . $module .
                '/Ext/Language/*.dri-customer-journey.php');
            $files = array_merge($files, $moduleFiles);
        }
        $appFiles = glob('custom/Extension/application/Ext/Language/*.dri-customer-journey.php');
        $files = array_merge($files, $appFiles);
        $adminFiles = glob('custom/Extension/modules/Administration/Ext/Language/*.' .
            'dri_customer_journey_settings.php');
        $files = array_merge($files, $adminFiles);
        $schedulerFiles = glob('custom/Extension/modules/Schedulers/Ext/Language/*.update_momentum_cj.php');
        $files = array_merge($files, $schedulerFiles);
        return $files;
    }

    /**
     * List of CJP modules
     * @var array
     */
    private $cjModules = [
        'CJ_Forms',
        'CJ_WebHooks',
        'DRI_Workflows',
        'DRI_Workflow_Templates',
        'DRI_Workflow_Task_Templates',
        'DRI_SubWorkflows',
        'DRI_SubWorkflow_Templates',
    ];

    /**
     * List of folders across all CJP packages which need to be deleted
     */
    private $cjDirectories = [
        'custom/clients/base/views/dri-workflow',
        'custom/clients/base/layouts/dri-workflows',
        'custom/clients/base/views/dri-workflows-header',
        'custom/clients/base/views/cj-webhook-dashlet',
        'custom/clients/base/fields/dri-progress-bar',
        'custom/include/SugarObjects/implements/customer_journey_parent',
        'custom/modules/Users/clients/base/views/customer-journey-config-users',
        'custom/include/CustomerJourney',
        'custom/clients/base/views/dri-workflows-widget-configuration',
        'custom/clients/base/views/dri-customer-journey-dashlet',
        'custom/clients/base/views/dri-customer-journey-momentum-dashlet',
        'custom/clients/base/fields/cj_populate_fields',
        'custom/clients/base/fields/cj_select_to',
        'custom/clients/base/fields/cj_time',
        'custom/clients/base/fields/cj_progress_bar',
        'custom/clients/base/fields/cj_momentum_bar',
        'custom/clients/base/fields/cj_widget_config_toggle_field',
        'custom/src/CustomerJourney',
        'custom/modules/Accounts/workflow',
        'custom/clients/base/views/cj-as-a-dashlet',
        'custom/clients/base/fields/cj_presentation_mode',
        'custom/include/SugarFields/Fields/Dri_percentage',
    ];

    /**
     * List of files across all CJP packages which need to be deleted
     */
    private $deleteFiles = [
        'custom/Extension/application/Ext/Include/addoptify-customer-journey.php',
        'custom/include/js/jquery-ui-1.9.2/jquery-ui-progressbar.min.js',
        'custom/include/generic/SugarWidgets/SugarWidgetFielddri_percentage.php',
        'custom/Extension/modules/Tasks/Ext/ActionViewMap/dri_workflows.php',
        'custom/Extension/modules/Tasks/Ext/Language/en_us.dri_workflows.php',
        'custom/Extension/modules/Tasks/Ext/LogicHooks/dri_workflows.php',
        'custom/Extension/modules/Tasks/Ext/Vardefs/yaml_vardefs_dri_workflows.php',
        'custom/Extension/modules/Accounts/Ext/clients/base/layouts/subpanels/dri_workflows.php',
        'custom/Extension/modules/Accounts/Ext/Layoutdefs/dri_workflows.php',
        'custom/Extension/modules/Accounts/Ext/LogicHooks/dri_wokflows.php',
        'custom/Extension/modules/Accounts/Ext/Vardefs/yaml_vardefs_dri_workflows.php',
        'custom/Extension/application/Ext/Include/dri_workflows.php',
        'custom/Extension/application/Ext/Language/en_us.dri_workflows.php',
        'custom/modules/Accounts/vardefs/dri_workflows.yml',
        'custom/modules/Tasks/clients/base/layouts/dri-workflow-quick-edit/dri-workflow-quick-edit.php',
        'custom/modules/Tasks/clients/base/views/dri-workflow-quick-edit/dri-workflow-quick-edit.js',
        'custom/modules/Tasks/clients/base/views/dri-workflow-quick-edit/dri-workflow-quick-edit.php',
        'custom/modules/Tasks/vardefs/dri_workflows.yml',
        'custom/modules/Tasks/views/view.closedriworkflowtask.php',
        'custom/Extension/modules/Tasks/Ext/Vardefs/dri-workflows.php',
        'custom/Extension/modules/Accounts/Ext/Vardefs/dri-workflows.php',
        'custom/themes/default/images/CJ_Forms_32.png',
        'custom/themes/default/images/DRI_Workflows_32.png',
        'custom/themes/default/images/DRI_Workflow_Task_Templates_32.png',
        'custom/themes/default/images/DRI_SubWorkflow_Templates_32.png',
        'custom/themes/default/images/DRI_Workflow_Templates_32.png',
        'custom/themes/default/images/customer_journey_workflow_templates.png',
        'custom/themes/default/images/customer_journey_settings.png',
        'custom/themes/default/images/CJ_WebHooks_32.png',
        'custom/themes/default/images/customer_journey_configure_record_view_display.png',
        'custom/themes/default/images/customer_journey_configure_modules.png',
        'custom/themes/default/images/customer_journey_plugin_update.png',
        'custom/themes/default/images/DRI_SubWorkflows_32.png',
        'custom/themes/default/less/dri-customer-journey.less',
        'custom/Extension/modules/Tasks/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Tasks/Ext/clients/base/views/record/dri-customer-journey.php',
        'custom/Extension/modules/Tasks/Ext/clients/base/filters/default/dri-customer-journey.php',
        'custom/Extension/modules/Tasks/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Administration/Ext/Administration/dri_customer_journey_settings.php',
        'custom/Extension/modules/Accounts/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Accounts/Ext/clients/base/layouts/extra-info/dri-customer-journey.php',
        'custom/Extension/modules/Accounts/Ext/clients/mobile/layouts/subpanels/customer-journey.php',
        'custom/Extension/modules/Accounts/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Leads/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Leads/Ext/clients/base/layouts/record-dashboard/dri-customer-journey.php',
        'custom/Extension/modules/Leads/Ext/clients/base/layouts/extra-info/dri-customer-journey.php',
        'custom/Extension/modules/Leads/Ext/clients/mobile/layouts/subpanels/customer-journey.php',
        'custom/Extension/modules/Leads/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Contacts/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Contacts/Ext/clients/base/layouts/extra-info/dri-customer-journey.php',
        'custom/Extension/modules/Contacts/Ext/clients/mobile/layouts/subpanels/customer-journey.php',
        'custom/Extension/modules/Contacts/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Opportunities/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Opportunities/Ext/clients/base/layouts/extra-info/dri-customer-journey.php',
        'custom/Extension/modules/Opportunities/Ext/clients/mobile/layouts/subpanels/customer-journey.php',
        'custom/Extension/modules/Opportunities/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Calls/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Calls/Ext/clients/base/views/record/dri-customer-journey.php',
        'custom/Extension/modules/Calls/Ext/clients/base/filters/default/dri-customer-journey.php',
        'custom/Extension/modules/Calls/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/updateMomentumCJ.php',
        'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/checkCJPLatestVersion.php',
        'custom/Extension/modules/Cases/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Cases/Ext/clients/base/views/record/dri-customer-journey.php',
        'custom/Extension/modules/Cases/Ext/clients/base/layouts/extra-info/dri-customer-journey.php',
        'custom/Extension/modules/Cases/Ext/clients/mobile/layouts/subpanels/customer-journey.php',
        'custom/Extension/modules/Cases/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Meetings/Ext/LogicHooks/dri-customer-journey.php',
        'custom/Extension/modules/Meetings/Ext/clients/base/views/record/dri-customer-journey.php',
        'custom/Extension/modules/Meetings/Ext/clients/base/filters/default/dri-customer-journey.php',
        'custom/Extension/modules/Meetings/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/modules/Users/Ext/clients/base/filters/basic/addoptify-customer-journey.php',
        'custom/Extension/modules/Users/Ext/clients/base/filters/default/addoptify-customer-journey.php',
        'custom/Extension/modules/Users/Ext/Vardefs/dri-customer-journey.php',
        'custom/Extension/application/Ext/WirelessModuleRegistry/addoptify-customer-journey.php',
        'custom/Extension/application/Ext/JSGroupings/customerJourneyGroupings.php',
        'custom/Extension/application/Ext/Utils/dri-customer-journey.php',
        'custom/data/acl/SugarACLCustomerJourney.php',
        'custom/modules/Accounts/CustomerJourney/EnumManager.php',
        'custom/modules/Leads/LogicHook/DRICustomerJourney.php',
        'custom/modules/Leads/CustomerJourney/EnumManager.php',
        'custom/modules/Contacts/CustomerJourney/EnumManager.php',
        'custom/modules/Opportunities/CustomerJourney/EnumManager.php',
        'custom/modules/Cases/CustomerJourney/EnumManager.php',
        'custom/clients/base/views/dri-license-errors/dri-license-errors.js',
        'custom/clients/base/layouts/dri-workflows-widget-configuration/dri-workflows-widget-configuration.php',
        'custom/Extension/application/Ext/JSGroupings/addCssLoaderPlugin.php',
        'custom/include/javascript/sugar7/plugins/CssLoader.js',
        'custom/themes/default/images/DRI_SubWorkflow_Templates.gif',
        'custom/themes/default/images/DRI_Workflow_Templates.gif',
        'custom/themes/default/images/DRI_Workflow_Task_Templates.gif',
        'custom/clients/base/views/dri-workflows-header/dri-workflows-user-limit-warning.hbs',
        'custom/clients/base/fields/cj_fieldset_for_date_in_populate_fields/cj_fieldset_for_date_in_populate_fields.js',
        'custom/clients/base/fields/cj_active_or_archive_filter/cj_active_or_archive_filter.js',
        'custom/clients/base/fields/cj_active_or_archive_filter/edit.hbs',
    ];
}
