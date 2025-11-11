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
 * Migrates portal settings.
 */
class SugarUpgradePortalSettings extends UpgradeScript
{
    public $order = 2170;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (!$this->toFlavor('ent')) {
            return;
        }

        // Grabs the portal config setting.
        $query = "SELECT value FROM config WHERE category='portal' AND name='on'";
        $portalEnabled = (bool)$this->db->getOne($query);

        // Remove `portal_on` with platform equals to NULL or platform equals to empty string
        $query = "DELETE FROM config WHERE category='portal' AND name='on' AND (platform IS NULL OR platform='')";
        $this->db->query($query);
    }
}
