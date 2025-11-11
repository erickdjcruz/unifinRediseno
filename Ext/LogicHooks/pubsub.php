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

$hook_array['after_save'][] = [
    1,
    'Publish after_save events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];

$hook_array['after_delete'][] = [
    1,
    'Publish after_delete events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];

$hook_array['after_restore'][] = [
    1,
    'Publish after_restore events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];

$hook_array['after_relationship_add'][] = [
    1,
    'Publish after_relationship_add events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];

$hook_array['after_relationship_delete'][] = [
    1,
    'Publish after_relationship_delete events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];

$hook_array['after_relationship_update'][] = [
    1,
    'Publish after_relationship_update events to Pub/Sub subscribers',
    null,
    '\\' . \Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher::class,
    'handleEvent',
];
