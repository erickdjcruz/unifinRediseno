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
 * Class DynamicFieldVerification
 * Contains pre-install verification routines of dynamic fields
 */
class DynamicFieldVerification
{
    public const ERROR_DATABASE_ROW_SIZE_LIMIT = 'ERROR_DATABASE_ROW_SIZE_LIMIT';

    /** @var DBManager */
    private $db;
    /** @var LoggerManager */
    private $log;

    public function __construct()
    {
        $this->db = DBManagerFactory::getInstance();
        $this->log = LoggerManager::getLogger();
    }

    /**
     * Perform pre-install verification of dynamic field
     *
     * @param TemplateField $templateField
     * @param DynamicField $dynamicField
     * @throws DynamicFieldVerificationException
     */
    public function verifyField(TemplateField $templateField, DynamicField $dynamicField): void
    {
        if (!$this->db instanceof MysqlManager) {
            return;
        }
        // Workaround for BR-8329: getContainedDefs uses get_field_def, which changes state of TemplateField when
        // calculated=true, it causes access to undefined property and PHP notice after field verification.
        // Need to use clone of $templateField to avoid it.
        $newDefs = (clone $templateField)->getContainedDefs($dynamicField);
        if (!$newDefs) {
            return;
        }

        $tableNameCustom = $dynamicField->bean->get_custom_table_name();
        $tableNameClone = $tableNameCustom . '_tmp_' . date('YmdHis');

        if ($this->db->tableExists($tableNameCustom)) {
            $query = "create table $tableNameClone like $tableNameCustom";
            $ret = $this->db->query($query, false, 'Cannot clone table');
            if (!$ret) {
                throw new DynamicFieldVerificationException(self::ERROR_DATABASE_ROW_SIZE_LIMIT);
            }
        } else {
            $iddef = [
                'id_c' => [
                    'name' => 'id_c',
                    'type' => 'id',
                    'required' => 1,
                ],
            ];
            $ididx = [
                [
                    'name' => $tableNameCustom . '_pk',
                    'type' => 'primary',
                    'fields' => ['id_c'],
                ],
            ];
            $query = $this->db->createTableSQLParams($tableNameClone, $iddef, $ididx);
            $ret = $this->db->query($query, false, 'Cannot create verification table');
            if (!$ret) {
                throw new DynamicFieldVerificationException(self::ERROR_DATABASE_ROW_SIZE_LIMIT);
            }
        }

        $existingColumns = $this->db->get_columns($tableNameClone);
        $newDefsAdd = $newDefsAlter = [];
        foreach ($newDefs as $def) {
            if (isset($existingColumns[$def['name']])) {
                $newDefsAlter[] = $def;
            } else {
                $newDefsAdd[] = $def;
            }
        }
        if ($newDefsAdd) {
            $addColumnsSQL = $this->db->addColumnSQL($tableNameClone, $newDefsAdd);
            $ret = $this->db->query($addColumnsSQL, false, 'Cannot alter cloned table');
        } else {
            $ret = true;
        }
        if ($ret && $newDefsAlter) {
            $alterColumnsSQL = $this->db->alterColumnSQL($tableNameClone, $newDefsAlter);
            $ret = $this->db->query($alterColumnsSQL, false, 'Cannot alter cloned table');
        }

        $dropSql = $this->db->dropTableNameSQL($tableNameClone);
        $dropped = $this->db->query($dropSql, false, 'Cannot alter cloned table');
        if (!$dropped) {
            $this->log->warn("Cannot drop temporary table $tableNameClone");
        }

        if (!$ret) {
            throw new DynamicFieldVerificationException(self::ERROR_DATABASE_ROW_SIZE_LIMIT);
        }
    }
}
