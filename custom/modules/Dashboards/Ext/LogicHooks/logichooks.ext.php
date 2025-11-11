<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/Dashboards/Ext/LogicHooks/ProductConsoleHooks.php


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

$hook_array['before_filter'][] = [
    1,
    'removeRenewalsConsole',
    'modules/Dashboards/ProductConsoleHelper.php',
    'ProductConsoleHelper',
    'removeRenewalsConsole',
];

$hook_array['before_retrieve'][] = [
    1,
    'checkRenewalsConsole',
    'modules/Dashboards/ProductConsoleHelper.php',
    'ProductConsoleHelper',
    'checkRenewalsConsole',
];

?>
<?php
// Merged from modules/Dashboards/Ext/LogicHooks/PortalDashboardHooks.php


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

$hook_array['before_filter'][] = [
    1,
    'removePortalDashboards',
    'modules/Dashboards/PortalDashboardHelper.php',
    'PortalDashboardHelper',
    'removePortalDashboards',
];

$hook_array['before_retrieve'][] = [
    1,
    'checkPortalDashboard',
    'modules/Dashboards/PortalDashboardHelper.php',
    'PortalDashboardHelper',
    'checkPortalDashboard',
];

?>
