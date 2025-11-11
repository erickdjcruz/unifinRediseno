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
 * Upgrade custom console configurations
 */
class SugarUpgradeCustomConsoleConfigurations extends UpgradeScript
{
    public $order = 9500;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '12.2.0', '>=')) {
            return;
        }

        $this->log('Upgrading custom console configurations ...');
        $consoles = [
            'c108bb4a-775a-11e9-b570-f218983a1c3e',
            'da438c86-df5e-11e9-9801-3c15c2c53980',
        ];
        $contexts = [
            'Cases' => 'service_console',
            'Accounts' => 'renewals_console',
            'Opportunities' => 'renewals_console',
        ];
        $metrics = [];

        // check custom metadata files
        foreach ($contexts as $module => $context) {
            $filename = 'custom/modules/' . $module . '/clients/base/views/multi-line-list/multi-line-list.php';
            if (file_exists($filename)) {
                $viewdefs[$module] = null;
                require $filename;
                $metrics[$module] = BeanFactory::newBean('Metrics');
                $metrics[$module]->metric_context = $context;
                $metrics[$module]->metric_module = $module;
                $metrics[$module]->name = translate('LBL_UNTITLED');
                $metrics[$module]->team_id = 1;
                $metrics[$module]->team_set_id = 1;
                $metrics[$module]->assigned_user_id = 1;
                $metrics[$module]->status = 'Active';
                $metrics[$module]->viewdefs = json_encode($viewdefs[$module]);
                unlink($filename);
            }
        }

        // check custom configs
        $configs = ['order_by_primary', 'order_by_secondary', 'freeze_first_column', 'filter_def'];
        $admin = BeanFactory::newBean('Administration');
        $settings = $admin->getConfigForModule('ConsoleConfiguration', 'base', true);

        foreach ($settings as $key => $value) {
            if (!in_array($key, $configs)) {
                continue;
            }
            foreach ($value as $consoleId => $moduleConfigs) {
                if (!in_array($consoleId, $consoles)) {
                    continue;
                }
                foreach ($moduleConfigs as $module => $config) {
                    if (!isset($metrics[$module])) {
                        continue;
                    }
                    if (strpos($key, 'order_by') === 0) {
                        $orderBy = explode(':', $config);
                        $metrics[$module]->$key = $orderBy[0];
                        $direction = $key . '_direction';
                        $metrics[$module]->$direction = $orderBy[1] ?? 'asc';
                    } elseif ($key === 'filter_def') {
                        $metrics[$module]->$key = json_encode($config);
                    } else {
                        $metrics[$module]->$key = $config;
                    }
                }
            }
        }

        // save metrics
        foreach ($metrics as $metric) {
            $metric->save();
        }

        $this->log('Upgraded custom console configurations!');
    }
}
