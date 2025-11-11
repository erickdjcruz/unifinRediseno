<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/Tasks/Ext/LogicHooks/customer_journey_hook.php

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

$hook_array['before_save'][] = [
    1,
    'Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::beforeSave',
    null,
    \Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::class,
    'beforeSave',
];

$hook_array['after_save'][] = [
    1,
    'Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::afterSave',
    null,
    \Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::class,
    'afterSave',
];

$hook_array['before_delete'][] = [
    1,
    'Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::beforeDelete',
    null,
    \Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::class,
    'beforeDelete',
];

$hook_array['after_delete'][] = [
    1,
    'Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::afterDelete',
    null,
    \Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHooks::class,
    'afterDelete',
];

?>
