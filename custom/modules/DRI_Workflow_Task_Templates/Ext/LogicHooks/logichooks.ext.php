<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/DRI_Workflow_Task_Templates/Ext/LogicHooks/logic_hooks.php

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
    2,
    'Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate\ActivityTemplateHooks::beforeSave',
    null,
    \Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate\ActivityTemplateHooks::class,
    'beforeSave',
];

?>
