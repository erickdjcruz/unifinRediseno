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
 * Set catalog_service_duration_value and catalog_service_duration_unit fields
 * for line items so proration calculations work on upgrade
 */
class SugarUpgradeSetCatalogServiceDurationFields extends UpgradeScript
{
    public $order = 7510;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if ($this->fromFlavor('ent') &&
            version_compare($this->from_version, '10.2.0', '<')
        ) {
            $lineItemTables = ['products', 'purchased_line_items', 'revenue_line_items'];
            foreach ($lineItemTables as $lineItemTable) {
                $sdValueSubquery = $this->db->getConnection()->createQueryBuilder()
                    ->select('service_duration_value')
                    ->from('product_templates');
                $sdValueSubquery->where($sdValueSubquery->expr()->eq('id', $lineItemTable . '.product_template_id'));

                $sdUnitSubquery = $this->db->getConnection()->createQueryBuilder()
                    ->select('service_duration_unit')
                    ->from('product_templates');
                $sdUnitSubquery->where($sdUnitSubquery->expr()->eq('id', $lineItemTable . '.product_template_id'));

                $this->log("Setting catalog_service_duration_value for all $lineItemTable linked to service product templates");
                $lineItemSdValueQuery = $this->db->getConnection()->createQueryBuilder()
                    ->update($lineItemTable)
                    ->set('catalog_service_duration_value', '(' . $sdValueSubquery->getSQL() . ')');
                $lineItemSdValueQuery->execute();

                $this->log("Setting catalog_service_duration_unit for all $lineItemTable linked to service product templates");
                $lineItemSdUnitQuery = $this->db->getConnection()->createQueryBuilder()
                    ->update($lineItemTable)
                    ->set('catalog_service_duration_unit', '(' . $sdUnitSubquery->getSQL() . ')');
                $lineItemSdUnitQuery->execute();
            }
        }
    }
}
