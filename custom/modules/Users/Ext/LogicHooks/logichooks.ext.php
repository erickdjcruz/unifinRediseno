<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/Users/Ext/LogicHooks/auth_hooks.php

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

$hook_array['after_logout'][] = [
    1,
    'auth_hooks',
    'modules/MobileDevices/MobileDevice.php',
    'MobileDevice',
    'onLoggedOut',
];

$hook_array['after_login'][] = [
    1,
    'auth_hooks',
    'modules/MobileDevices/MobileDevice.php',
    'MobileDevice',
    'onLoggedIn',
];

?>
<?php
// Merged from modules/Users/Ext/LogicHooks/cj_hook.php

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
    'Set Grace Period start date if user limit reached',
    null,
    \Sugarcrm\Sugarcrm\modules\Users\CJUsersHook::class,
    'afterSave',
];

?>
<?php
// Merged from modules/Users/Ext/LogicHooks/hint_hook.php

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
    'Hook description',
    null,
    \Sugarcrm\Sugarcrm\modules\Users\HintUsersHook::class,
    'afterSave',
];

$hook_array['before_delete'][] = [
    1,
    'Hook description',
    null,
    \Sugarcrm\Sugarcrm\modules\Users\HintUsersHook::class,
    'beforeDelete',
];

?>
