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
 * Adds a new field into currencies table. It cannot be done by Repair DB as Currency is a special case:
 * it may be used in Bean constructors, so if we try to instantiate such Bean - we'll catch an error: Wrong
 * SQL as currencies table is not synchronized with metadata. To avoid this the synchronization should be
 * performed before any Bean instantiated with BeanFactory::newBean()
 */
class SugarUpgradeMigrateCurrencyToVersion11 extends UpgradeScript
{
    public $order = 1010;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.0', '>=')) {
            // do nothing if upgrading from 11.0 or newer
            return;
        }

        $currencies = BeanFactory::newBean('Currencies');
        $tableName = $currencies->getTableName();
        $column = 'sync_key';
        $def = $currencies->getFieldDefinition($column);

        if (!$def || $this->isAltered($tableName, $column)) {
            return;
        }

        $sql = $this->db->addColumnSQL($tableName, $def);
        $this->db->query($sql);
    }

    public function isAltered(string $tableName, string $fieldName): bool
    {
        $tableDescription = $this->db->getTableDescription($tableName, true);

        return !empty($tableDescription[$fieldName]);
    }
}
