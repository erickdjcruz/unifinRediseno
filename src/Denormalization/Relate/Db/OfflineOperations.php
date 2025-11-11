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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate\Db;

use SugarBean;

interface OfflineOperations
{
    /**
     * Copy a data chunk [$offset:$limit] from TMP table to Target table: Target field
     *
     * @return int Rows copied
     */
    public function migrateTemporaryTableChunk(
        string $targetTableName,
        string $targetFieldName,
        int    $offset,
        int    $limit
    ): int;

    public function createTemporaryTable(array $fieldDefForValue): void;

    public function dropTemporaryTable(): void;

    public function getTemporaryTableCount(): int;

    public function setTmpTableName(string $name): void;

    public function getTmpTableName(): string;

    public function getTableRowCount(string $tableName): int;

    public function getAlterSql(string $tableName, array $fieldDef): ?string;

    public function ensureTemporaryTableIndex(): void;

    public function finalizeTemporaryTable(): void;

    public function ensureColumnExists(string $tableName, array $fieldDef): void;

    public function isAltered(string $tableName, string $fieldName): bool;

    public function getTableDescription(string $tableName): array;

    public function populateTemporaryTable(
        string $idField,
        string $sourceFieldName,
        string $fromTable,
        string $joinTable,
        string $joinConditionTargetField,
        string $joinConditionSourceField
    ): void;

    public function replicateIndex(string $tableName, string $indexName, SugarBean $targetBean, array $fieldDefs): void;
}
