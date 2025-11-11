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
 * Update hours_to_resolution from time_to_resolution with minute to hour conversion
 */
class SugarUpgradeUpdateTimeToResolution extends UpgradeScript
{
    public $order = 7500;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if ($this->fromFlavor('ent') &&
            version_compare($this->from_version, '9.1.0', '>=') &&
            version_compare($this->from_version, '9.3.0', '<')) {
            $this->log('Updating hours_to_resolution from time_to_resolution with minute to hour conversion.');
            $this->db->query('UPDATE cases SET hours_to_resolution = time_to_resolution / 60 WHERE time_to_resolution IS NOT NULL');
        }
    }
}
