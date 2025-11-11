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
 * Set the account_id using the parent purchase for PLIs that were created
 * before that field was added
 */
class SugarUpgradeSetAccountForExistingPLIs extends UpgradeScript
{
    public $order = 7550;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if ($this->fromFlavor('ent') &&
            version_compare($this->from_version, '10.2.0', '<')
        ) {
            $this->log('Setting account_id for existing PLIs');

            // QueryBuilder does not support using joins in update calls, so
            // we must use a subquery here
            $subQuery = $this->db->getConnection()->createQueryBuilder()
                ->select('account_id')
                ->from('purchases');
            $subQuery->where($subQuery->expr()->eq('id', 'purchased_line_items.purchase_id'));
            $query = $this->db->getConnection()->createQueryBuilder()
                ->update('purchased_line_items')
                ->set('account_id', '(' . $subQuery->getSQL() . ')');
            $query->execute();
        }
    }
}
