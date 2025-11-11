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

final class Oracle extends Db
{
    public function migrateTemporaryTableChunk(
        string $targetTableName,
        string $targetFieldName,
        int    $offset,
        int    $limit
    ): int {

        $offsetEnd = $offset + $limit + 1;
        $tmpTableName = $this->getTmpTableName();

        $sql = "
            UPDATE $targetTableName
              SET $targetFieldName = (
              SELECT value
                FROM $tmpTableName WHERE id > $offset AND id < $offsetEnd AND target_id = $targetTableName.id
              )
              WHERE EXISTS (
	            SELECT value FROM $tmpTableName 
	            WHERE id > $offset AND id < $offsetEnd AND target_id = $targetTableName.id 
	          )";

        return $this->connection->executeUpdate($sql);
    }

    public function finalizeTemporaryTable(): void
    {
        parent::finalizeTemporaryTable();
        $this->gatherTemporaryTableStats();
    }

    private function gatherTemporaryTableStats(): void
    {
        $tmpTableName = $this->getTmpTableName();
        $tmpPrimaryIndexName = $this->getPrimaryIndexName();
        $tmpTargetIdIndexName = $this->getTargetIdIndexName();

        $sql = "BEGIN
            DBMS_STATS.GATHER_TABLE_STATS(
               NULL,
               '$tmpTableName',
               NULL,
               DBMS_STATS.AUTO_SAMPLE_SIZE, FALSE, 'FOR ALL COLUMNS SIZE AUTO'
              );
             DBMS_STATS.GATHER_INDEX_STATS(NULL, '$tmpPrimaryIndexName', NULL, DBMS_STATS.AUTO_SAMPLE_SIZE);
             DBMS_STATS.GATHER_INDEX_STATS(NULL, '$tmpTargetIdIndexName', NULL, DBMS_STATS.AUTO_SAMPLE_SIZE);
            END;";

        $this->connection->executeStatement($sql);
    }
}
