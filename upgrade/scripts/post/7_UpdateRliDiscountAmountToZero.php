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
 * Update discount_amount from NULL to 0.000000 for all RLIs where discount_amount is NULL.
 */
class SugarUpgradeUpdateRliDiscountAmountToZero extends UpgradeScript
{
    public $order = 7550;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if ($this->fromFlavor('ent') &&
            Opportunity::usingRevenueLineItems() &&
            version_compare($this->from_version, '10.1.0', '<')
        ) {
            $this->log('Updating discount_amount from NULL to 0.000000 for all RLIs where discount_amount is NULL.');
            $this->db->query('UPDATE revenue_line_items SET discount_amount = 0.000000 WHERE discount_amount IS NULL');
        }
    }
}
