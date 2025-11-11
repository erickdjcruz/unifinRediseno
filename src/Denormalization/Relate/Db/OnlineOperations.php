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

interface OnlineOperations
{
    public function updateBean(SugarBean $bean, string $fieldName): void;

    public function updateLinkedBean(
        string  $relateRecordId,
        ?string $joinTableName,
        ?string $joinPrimaryKey,
        ?string $joinLinkedKey,
        string  $denormalizedFieldName,
        string  $primaryTableName,
        string  $primaryKey,
        $value,
        $relatedId = null
    ): void;

    public function updateBeanWithLinkId(
        SugarBean $bean,
        string    $linkedFieldName,
        string    $linkedTableName,
        string    $linkedKey,
        string    $primaryTableName,
        string    $denormFieldName,
        string    $linkId
    ): void;

    public function updateTemporaryTableWithValue(SugarBean $bean, $value, ?string $temporaryTableName): void;

    public function updateTemporaryTable(
        SugarBean $bean,
        string    $linkedFieldName,
        string    $linkedTableName,
        string    $linkedKey,
        ?string   $temporaryTableName
    ): void;

    public function fetchValue(string $tableName, string $fieldName, string $id);

    public function setTmpTableName(string $name): void;
}
