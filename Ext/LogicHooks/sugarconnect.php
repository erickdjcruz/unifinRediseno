<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
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
 * @deprecated This file will be removed in the next release.
 */

// Logic hooks to publish a bean to the Sugar Connect webhook.

$hook_array['after_save'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];

$hook_array['after_delete'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];

$hook_array['after_restore'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];

$hook_array['after_relationship_add'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];

$hook_array['after_relationship_delete'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];

$hook_array['after_relationship_update'][] = [
    1,
    'sugarconnect',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SugarConnect\LogicHooks\Handler::class,
    'publish',
];
