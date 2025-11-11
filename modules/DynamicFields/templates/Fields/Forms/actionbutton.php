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
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

function get_body(&$ss, $vardef)
{
    // get the module
    $vars = $ss->get_template_vars();
    $fields = $vars['module']->mbvardefs->vardefs['fields'];
    $module = $vars['module']->key_name ?: $vars['module']->name;

    $actionButtonData = '{"settings": {}, "actionMenu": {}, "buttons": {}}';
    // if we have ext4 data get it instead of default settings
    if (isset($fields[$vardef['name']]['options']) && ($fields[$vardef['name']]['options'] != '')) {
        $actionButtonData = $fields[$vardef['name']]['options'];
    }

    $controller = new TabController();
    $tabs = $controller->get_tabs_system();
    $modules = $tabs[0];
    $hiddenModules = $tabs[1];

    $badModules = [
        'Home',
        'Calendar',
        'Forecasts',
        'Reports',
        'ops_Backups',
        'Documents',
        'Campaigns',
        'pmse_Inbox',
        'BusinessCenters',
        'DataPrivacy',
        'Emails',
        'OutboundEmail',
        'ProductTemplates',
        'Shifts',
        'Tags',
        'pmse_Business_Rules',
        'pmse_Emails_Templates',
        'pmse_Project',
    ];

    // get all the modules an user can use
    $allModules = array_diff_key(array_merge($modules, $hiddenModules), array_flip($badModules));

    $ss->assign('ACTIONBUTTON_SETTINGS', $actionButtonData);
    $ss->assign('ACTIONBUTTON_MODULE', $module);
    $ss->assign('ACTIONBUTTON_MODULES', json_encode($allModules));

    return $ss->fetch('modules/DynamicFields/templates/Fields/Forms/actionbutton.tpl');
}
