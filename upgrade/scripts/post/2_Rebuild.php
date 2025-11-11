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
 * Apply "repair&rebuild" to each bean's table
 * Rebuild relationships
 */
class SugarUpgradeRebuild extends UpgradeScript
{
    public $order = 2100;
    public $type = self::UPGRADE_ALL;

    public function run()
    {
        global $dictionary, $beanFiles;
        include 'include/modules.php';
        $rac = new RepairAndClear();
        $rac->execute = true;
        $rac->clearVardefs();
        $rac->rebuildExtensions();
        $rac->clearExternalAPICache();
        $rac->setStatementObserver(function (?string $statement): void {
            $this->log('Running sql: ' . $statement);
        });
        $rac->repairDatabase();

        // Refresh the SugarLogic cache to ensure that new SugarLogic functions
        // are included
        include 'include/Expressions/updatecache.php';

        $this->rebuildAudit($rac, $beanFiles);

        $this->log('Database repaired');

        $this->log('Start rebuilding relationships');
        $_REQUEST['silent'] = true;
        include 'modules/Administration/RebuildRelationship.php';
        $_REQUEST['upgradeWizard'] = true;
        include 'modules/ACL/install_actions.php';
        $this->log('Done rebuilding relationships');
        unset($GLOBALS['reload_vardefs']);

        // enable metadata caching once the database schema has been rebuilt
        MetaDataManager::enableCache();
    }

    public function rebuildAudit(RepairAndClear $rac, array $beanFiles): void
    {
        foreach ($beanFiles as $bean => $file) {
            if (file_exists($file)) {
                unset($GLOBALS['dictionary'][$bean]);
                require_once $file;
                $focus = new $bean();

                if ($focus instanceof SugarBean && $focus->is_AuditEnabled()) {
                    // Check to see if we need to create the audit table
                    $rac->module_list[] = $focus->module_name;
                }
            }
        }

        if (!empty($rac->module_list)) {
            $this->log('Verifying audit tables for modules: ' . implode(',', $rac->module_list));
            $rac->rebuildAuditTables();
        }
    }
}
