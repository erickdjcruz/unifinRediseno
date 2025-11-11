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

class SugarUpgradeRemoveFieldLength extends UpgradeScript
{
    /**
     * {@inheritdoc }
     * @var int
     */
    public $type = self::UPGRADE_DB;

    private $fieldsToUpgrade = [
        [
            'module' => 'pmse_BpmFlow',
            'fieldName' => 'cas_id',
        ],
        [
            'module' => 'pmse_BpmFormAction',
            'fieldName' => 'cas_id',
        ],
        [
            'module' => 'pmse_BpmThread',
            'fieldName' => 'cas_id',
        ],
        [
            'module' => 'pmse_BpmNotes',
            'fieldName' => 'cas_index',
        ],
        [
            'module' => 'pmse_BpmFlow',
            'fieldName' => 'cas_index',
        ],
    ];

    public function run()
    {
        if (!$this->db instanceof OracleManager) {
            return;
        }
        if (version_compare($this->from_version, '12.0.0', '<')) {
            $this->removeFieldLength();
        }
    }

    /**
     * Remove DB column length so DB can set its own max
     */
    public function removeFieldLength()
    {
        global $dictionary;

        foreach ($this->fieldsToUpgrade as $fieldToUpgrade) {
            $bean = BeanFactory::newBean($fieldToUpgrade['module']);
            $objectName = $bean->getObjectName();
            $newFieldDef = $dictionary[$objectName]['fields'][$fieldToUpgrade['fieldName']];
            $alterSql = $this->db->alterColumnSQL($bean->getTableName(), $newFieldDef);
            $this->log('Running SQL: ' . $alterSql);
            $this->db->query($alterSql);
        }
    }
}
