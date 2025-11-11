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
 * Upgrade existing product catalog.
 */
class SugarUpgradeSetProductCatalogTeams extends UpgradeScript
{
    public $order = 9901;
    public $type = self::UPGRADE_DB;

    /**
     *
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        if (version_compare($this->from_version, '10.0.0', '>=')) {
            // do nothing if upgrading from 10.0.0 or newer
            return;
        }
        $this->log('Upgrading product catalog');
        // set team to global
        $sql = 'UPDATE product_templates SET team_id=1, team_set_id=1 WHERE deleted=0';
        $this->db->query($sql);
    }
}
