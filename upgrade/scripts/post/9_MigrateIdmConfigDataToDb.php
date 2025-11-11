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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication;

/**
 * Upgrade script to migrate IDM config data from config file to DB
 */
class SugarUpgradeMigrateIdmConfigDataToDb extends UpgradeScript
{
    public $order = 9650;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '9.3.0', '>=')
            || empty($GLOBALS['sugar_config']['idm_mode']['enabled'])
            || $this->isIdmDataInDb()) {
            return;
        }

        $this->migrateConfigDataToDb($GLOBALS['sugar_config']['idm_mode']);
    }

    protected function migrateConfigDataToDb(array $configData)
    {
        $config = new Authentication\Config(SugarConfig::getInstance());
        // don't need to refresh cache right now
        $config->setIDMMode($configData, false);
    }

    protected function isIdmDataInDb()
    {
        $admin = \Administration::getSettings('idm_mode', true);
        return !empty($admin->settings['idm_mode_enabled']);
    }
}
