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
 * Remove ForecastWorksheet list view from merge done in 7_Merge7Templates.
 * @see SS-2535
 */
class SugarUpgradeForecastWorksheetMerge7Fix extends UpgradeScript
{
    public $order = 6999;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        $targetVersion = '12.0.0';
        if (version_compare($this->from_version, $targetVersion, '<')) {
            unset($this->upgrader->state['for_merge']['modules/ForecastWorksheets/clients/base/views/list/list.php']);
            $this->log('Removed from merge queue: modules/ForecastWorksheets/clients/base/views/list/list.php');
        }
    }
}
