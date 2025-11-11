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
 * Rename field 'platform' for MobileDevices.
 */
class SugarUpgradeUpdateMobileDevices extends UpgradeScript
{
    public $order = 2099;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (!$this->fromFlavor('ent') ||
            version_compare($this->from_version, '11.0.0', '<') ||
            version_compare($this->from_version, '11.1.0', '>=')) {
            return;
        }

        $index = $this->db->get_index('mobile_devices', 'idx_assigned_device_id');
        if (is_array($index) && isset($index['name'])) {
            $this->db->dropIndexes('mobile_devices', ['idx_assigned_device_id' => $index]);
        }

        $query = $this->db->renameColumnSQL('mobile_devices', 'platform', 'device_platform');
        $this->db->query($query);
    }
}
