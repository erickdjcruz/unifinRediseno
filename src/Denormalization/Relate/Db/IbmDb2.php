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

final class IbmDb2 extends Db
{
    public function migrateTemporaryTableChunk(
        string $targetTableName,
        string $targetFieldName,
        int    $offset,
        int    $limit
    ): int {

        $offsetEnd = $offset + $limit + 1;
        $TemporaryTableName = $this->getTmpTableName();

        $sql = "
            UPDATE $targetTableName
              SET $targetFieldName = (
              SELECT value
                FROM $TemporaryTableName WHERE id > $offset AND id < $offsetEnd AND target_id = $targetTableName.id
              )
            WHERE EXISTS (
	          SELECT value 
	          FROM $TemporaryTableName 
	          WHERE id > $offset AND id < $offsetEnd AND target_id = $targetTableName.id 
	        )";

        return $this->connection->executeUpdate($sql);
    }
}
