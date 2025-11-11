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

final class MySql extends Db
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
            UPDATE $targetTableName target
            INNER JOIN (SELECT target_id, value
              FROM $tmpTableName WHERE id > $offset AND id < $offsetEnd) tmp
            ON tmp.target_id = target.id
            SET target.$targetFieldName = tmp.value";

        return $this->connection->executeUpdate($sql);
    }
}
