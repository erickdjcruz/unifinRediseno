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
 * Upgrade script to update forecast config
 */
class SugarUpgradeUpdateForecastDefaultConfig extends UpgradeScript
{
    public $order = 9999;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        $targetVersion = '12.0.0';
        if (version_compare($this->from_version, $targetVersion, '<')) {
            $this->log('Updating forecast default config');
            $admin = BeanFactory::newBean('Administration');
            $forecastConfig = $admin->getConfigForModule('Forecasts', 'base', true);
            if ($forecastConfig['is_setup'] === 0) {
                $admin->saveSetting('Forecasts', 'show_worksheet_best', 0, 'base');
                $defaultFields = [
                    'commit_stage',
                    'parent_name',
                    'account_name',
                    'date_closed',
                    'sales_stage',
                    'probability',
                    'likely_case',
                ];
                $encodedFields = json_encode($defaultFields);
                $admin->saveSetting(
                    'Forecasts',
                    'worksheet_columns',
                    $encodedFields,
                    'base'
                );
            }
        }
    }
}
