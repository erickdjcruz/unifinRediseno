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
 * Disallow users to change the service duration for products that were marked
 * as services before the lock_duration field was added.
 */
class SugarUpgradeSetProductCatalogLockDuration extends UpgradeScript
{
    public $order = 7500;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if ($this->fromFlavor('ent') &&
            version_compare($this->from_version, '10.2.0', '<')
        ) {
            $this->log('Setting lock_duration for all product templates marked as a service');
            $ptQuery = $this->db->getConnection()->createQueryBuilder()
                ->update('product_templates')
                ->set('lock_duration', '1');
            $ptQuery->where($ptQuery->expr()->eq('service', '1'));
            $ptQuery->execute();

            // Next we need to do that for all related line items
            $lineItemTables = ['products', 'revenue_line_items'];
            foreach ($lineItemTables as $lineItemTable) {
                $this->log("Setting lock_duration for all $lineItemTable linked to service product templates");

                $ptSubquery = $this->db->getConnection()->createQueryBuilder()
                    ->select('lock_duration')
                    ->from('product_templates');
                $ptSubquery->where($ptSubquery->expr()->eq('id', $lineItemTable . '.product_template_id'));

                $lineItemQuery = $this->db->getConnection()->createQueryBuilder()
                    ->update($lineItemTable)
                    ->set('lock_duration', '(' . $ptSubquery->getSQL() . ')');
                $lineItemQuery->execute();
            }
        }
    }
}
