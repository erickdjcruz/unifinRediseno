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

// Relate Field Denormalization hook

$hook_array['before_save'][] = [
    1,
    'denorm_field_watcher',
    null,
    '\\' . \Sugarcrm\Sugarcrm\Denormalization\Relate\Hook::class,
    'handleBeforeUpdate',
];

$hook_array['after_save'][] = [
    1,
    'denorm_field_watcher',
    null,
    '\\' . \Sugarcrm\Sugarcrm\Denormalization\Relate\Hook::class,
    'handleAfterUpdate',
];

$hook_array['before_relationship_delete'][] = [
    1,
    'denorm_field_watcher',
    null,
    '\\' . \Sugarcrm\Sugarcrm\Denormalization\Relate\Hook::class,
    'handleDeleteRelationship',
];

$hook_array['after_relationship_add'][] = [
    1,
    'denorm_field_watcher',
    null,
    '\\' . \Sugarcrm\Sugarcrm\Denormalization\Relate\Hook::class,
    'handleAddRelationship',
];
