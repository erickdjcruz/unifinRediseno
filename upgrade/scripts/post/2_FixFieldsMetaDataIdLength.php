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
 * Class SugarUpgradeAlterKBVoteField.
 * Changes length to vardefs value of vote field in kbusefulness table.
 */
class SugarUpgradeFixFieldsMetaDataIdLength extends UpgradeScript
{
    public $order = 2001;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '10.1.0', '>=')) {
            return;
        }
        global $dictionary;

        $fieldsMetaData = BeanFactory::newBean('EditCustomFields');

        $tracker = BeanFactory::newBean('Trackers');
        $conn = $this->db->getConnection();
        $idLengthExpression = $this->db->convert('id', 'length');

        $query = new SugarQuery();
        $query->select(['id']);
        $query->from($fieldsMetaData, ['add_deleted' => false]);
        $query->where()->addRaw($idLengthExpression . '> 36');
        $stmt = $query->compile()->execute();
        while ($row = $stmt->fetchAssociative()) {
            $newId = Sugarcrm\Sugarcrm\Util\Uuid::uuid1();
            $oldId = $row['id'];
            $conn->update($fieldsMetaData->getTableName(), ['id' => $newId], ['id' => $oldId]);
            if ((isset($tracker->field_defs['item_id']['type']) && $tracker->field_defs['item_id']['type'] != 'id')
                || (isset($tracker->field_defs['item_id']['len']) && $tracker->field_defs['item_id']['len'] > 36)) {
                $conn->update($tracker->getTableName(), ['item_id' => $newId], ['item_id' => $oldId]);
            }
            $this->db->commit();
        }

        if ($this->db instanceof IBMDB2Manager) {
            $tableName = $fieldsMetaData->getTableName();
            $tableIndexDefinitions = $dictionary['FieldsMetaData']['indices'];
            $tableIdDefinition = $dictionary['FieldsMetaData']['fields']['id'];
            $tableColumns = $this->db->get_columns($tableName);
            $tableIndexes = $this->db->get_indices($tableName);
            if (!empty($tableColumns['id']['len']) && $tableColumns['id']['len'] > 36) {
                $this->db->dropIndexes($tableName, $tableIndexes, true);
                $this->db->alterColumn($tableName, $tableIdDefinition);
                $this->db->addIndexes($tableName, $tableIndexDefinitions, true);
            }
        }
    }
}
