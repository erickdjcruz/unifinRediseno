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

use Sugarcrm\Sugarcrm\AccessControl\AdminWork;

/**
 * Update is_template flag for Template Reports
 */
class SugarUpgradeSetupReportTemplates extends UpgradeScript
{
    public $order = 9500;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        $this->log('Running 9_SetupReportTemplates script...');

        // install the new Reports as templates
        if (version_compare($this->from_version, '14.0.0', '<')) {
            $this->log('Installing new Template Reports');
            $this->installTemplateReports();
            $this->log('Successfully Installed new Template Reports');
        } else {
            $this->log('Not installing new Template Reports');
        }
    }

    /**
     * Install the Template Reports
     */
    public function installTemplateReports()
    {
        $this->log('Temporarily enabling admin work for Template Report installation');
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();

        require_once 'modules/Reports/SavedReport.php';
        require_once 'modules/Reports/SeedReports.php';

        create_default_reports();
    }
}
