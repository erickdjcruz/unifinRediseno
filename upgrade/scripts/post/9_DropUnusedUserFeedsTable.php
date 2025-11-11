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
 * Drop unused 'users_feeds' table
 */
class SugarUpgradeDropUnusedUserFeedsTable extends UpgradeScript
{
    public $order = 9601;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $unusedTable = 'users_feeds';
        if (version_compare($this->from_version, '14.0.0', '<') && $this->db->tableExists($unusedTable)) {
            $this->db->dropTableName($unusedTable);
        }
    }
}
