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
 * Create default Reports Panels
 */
class SugarUpgradeConvertToReportsDashlet extends UpgradeScript
{
    public $order = 9252;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->log('Converting saved-reports-chart dashlets to report-dashlet...');

        if (version_compare($this->from_version, '12.2.0', '<')) {
            require_once 'install/Reports/ConvertToReportDashlet.php';
            $converter = new ConvertToReportDashlet();

            $converter->run();
        }

        $this->log('Done converting saved-reports-chart dashlets to report-dashlet');
    }
}
