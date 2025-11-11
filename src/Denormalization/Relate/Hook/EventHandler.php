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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate\Hook;

use SugarBean;

interface EventHandler
{
    public function handleAddRelationship(string $fieldName, SugarBean $bean, array $options): void;

    public function handleAddRelationshipWithValue(SugarBean $bean, array $options, $value, $relatedId): void;

    public function handleDeleteRelationship(SugarBean $bean, array $options): void;

    public function handleBeforeUpdate($value, SugarBean $bean, array $options): void;

    public function handleAfterUpdateSourceField(SugarBean $bean, string $sourceLinkedFieldName, array $options): void;

    public function handleAfterUpdateTrackField(SugarBean $bean, array $options, array $dataChanges): void;
}
