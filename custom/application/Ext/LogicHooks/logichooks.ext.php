<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from Ext/LogicHooks/pmse.php


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

// Full text search.

$pmseHookClassPath = SugarAutoLoader::requireWithCustom('modules/pmse_Inbox/engine/PMSELogicHook.php');
$pmseHookClassName = SugarAutoLoader::customClass('PMSELogicHook');
$hook_array['after_save'][] = [
    100,
    'pmse',
    $pmseHookClassPath,
    $pmseHookClassName,
    'after_save',
];
$hook_array['after_delete'][] = [
    100,
    'pmse',
    $pmseHookClassPath,
    $pmseHookClassName,
    'after_delete',
];
$hook_array['after_relationship_add'][] = [
    100,
    'pmse',
    $pmseHookClassPath,
    $pmseHookClassName,
    'after_relationship',
];
$hook_array['after_relationship_delete'][] = [
    100,
    'pmse',
    $pmseHookClassPath,
    $pmseHookClassName,
    'after_relationship',
];
//remove unnecessary globals
unset($pmseHookClassPath);
unset($pmseHookClassName);

?>
<?php
// Merged from Ext/LogicHooks/SugarMetricHooks.php

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

$hook_array['after_entry_point'][] = [1, 'smm', 'include/SugarMetric/HookManager.php', 'SugarMetric_HookManager', 'afterEntryPoint'];
$hook_array['server_round_trip'][] = [1, 'smm', 'include/SugarMetric/HookManager.php', 'SugarMetric_HookManager', 'serverRoundTrip'];

?>
<?php
// Merged from Ext/LogicHooks/customerjourney.php

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
    'Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\JourneyParentHooksHelper::afterSave',
    null,
    '\\' . \Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\JourneyParentHooksHelper::class,
    'afterSave',
];

?>
<?php
// Merged from Ext/LogicHooks/activitystream.php


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

// Activity stream.
$activitystream = [
    1,
    'activitystream',
    'modules/ActivityStream/Activities/ActivityQueueManager.php',
    'ActivityQueueManager',
    'eventDispatcher',
];
$hook_array['after_save'][] = $activitystream;
$hook_array['after_delete'][] = $activitystream;
$hook_array['after_undelete'][] = $activitystream;
$hook_array['after_relationship_add'][] = $activitystream;
$hook_array['after_relationship_delete'][] = $activitystream;
unset($activitystream);

?>
<?php
// Merged from Ext/LogicHooks/sugarconnect.php


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

?>
<?php
// Merged from Ext/LogicHooks/fts.php


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

// Full text search after_save hook to update/index a bean

$hook_array['after_save'][] = [
    1,
    'fts',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SearchEngine\HookHandler::class,
    'indexBean',
];

$hook_array['after_delete'][] = [
    1,
    'fts',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SearchEngine\HookHandler::class,
    'indexBean',
];

$hook_array['after_restore'][] = [
    1,
    'fts',
    null,
    '\\' . \Sugarcrm\Sugarcrm\SearchEngine\HookHandler::class,
    'indexBean',
];

?>
<?php
// Merged from Ext/LogicHooks/pubsub.php


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

?>
<?php
// Merged from Ext/LogicHooks/maps.php


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
    'geocode',
    null,
    '\\' . \Sugarcrm\Sugarcrm\Maps\HookHandler::class,
    'geocode',
];

?>
