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
/*********************************************************************************
 ********************************************************************************/
if (!$GLOBALS['current_user']->isAdminForModule('Users')) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}

$_REQUEST['edit'] = 'true';

$focus = BeanFactory::newBean('TeamNotices');
if (!empty($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}

$GLOBALS['log']->info('Team Notice edit view');

$params = [];
$params[] = sprintf(
    '<a href="index.php?action=index&module=TeamNotices">%s</a>',
    htmlspecialchars($mod_strings['LBL_MODULE_NAME'], ENT_COMPAT)
);
if (empty($focus->id)) {
    $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL'], ENT_COMPAT);
} else {
    $params[] = htmlspecialchars($focus->name, ENT_COMPAT);
    $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_EDIT_BUTTON_LABEL'], ENT_COMPAT);
}

$xtpl = new XTemplate('modules/TeamNotices/EditView.html');
$xtpl->assign('MOD', $mod_strings);
$xtpl->assign('APP', $app_strings);
$xtpl->assign('PAGE_TITLE', getClassicModuleTitle('TeamNotices', $params, true));
if (isset($_REQUEST['return_module'])) {
    $xtpl->assign('RETURN_MODULE', $_REQUEST['return_module']);
}
if (isset($_REQUEST['return_action'])) {
    $xtpl->assign('RETURN_ACTION', $_REQUEST['return_action']);
}
if (isset($_REQUEST['return_id'])) {
    $xtpl->assign('RETURN_ID', $_REQUEST['return_id']);
}
$xtpl->assign('THEME', SugarThemeRegistry::current()->__toString());
$xtpl->assign('JAVASCRIPT', get_set_focus_js());
$xtpl->assign('ID', $focus->id);
$xtpl->assign('NAME', $focus->name);
$xtpl->assign('URL', $focus->url);
$xtpl->assign('URL_TITLE', $focus->url_title);
$xtpl->assign('NAME', $focus->name);
$xtpl->assign('DESCRIPTION', $focus->description);

$escapedHTML = static function ($html): string {
    return htmlspecialchars($html, ENT_QUOTES);
};

$buttons = [
    <<<HTML
<input id="btn_teamnotices_save" title="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_TITLE'])}"
    accessKey="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_KEY'])}"  
    class="button primary" onclick="this.form.action.value='Save'; return check_form('EditView');" 
    type="submit" name="button" value="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_LABEL'])}" >
HTML
    ,
    <<<HTML
<input id="btn_teamnotices_cancel" title="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_TITLE'])}"
    accessKey="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_KEY'])}" onclick="this.form.action.value='index';"
    class="button" type="submit" name="button" value="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_LABEL'])}" >
HTML
    ,
];

require_once 'include/SugarSmarty/plugins/function.sugar_action_menu.php';
$action_button = smarty_function_sugar_action_menu([
    'id' => 'teamnotices_editview_buttons',
    'buttons' => $buttons,
    'flat' => true,
], $xtpl);

$xtpl->assign('ACTION_BUTTON', $action_button);

$teamSetField = new SugarFieldTeamset('Teamset');
$code = $teamSetField->getClassicView($focus->field_defs);
$xtpl->assign('TEAM_SET_FIELD', $code);

if (!isset($focus->date_start)) {
    $xtpl->assign('DATE_START', $timedate->nowDate());
} else {
    $xtpl->assign('DATE_START', $focus->date_start);
}
if (!isset($focus->date_start)) {
    $xtpl->assign('DATE_END', $timedate->asUser($timedate->getNow()->get('+1 week')));
} else {
    $xtpl->assign('DATE_END', $focus->date_end);
}
$xtpl->assign('CALENDAR_DATEFORMAT', $timedate->get_cal_date_format());
$xtpl->assign('STATUS_OPTIONS', get_select_options_with_id($mod_strings['dom_status'], $focus->status));
$xtpl->parse('main.pro');
$xtpl->parse('main');
$xtpl->out('main');

$javascript = new javascript();
$javascript->setFormName('EditView');
$javascript->setSugarBean($focus);
$javascript->addAllFields('');
$javascript->addFieldGeneric('team_name', 'varchar', $app_strings['LBL_TEAM'], 'true');
$javascript->addToValidateBinaryDependency('team_name', 'alpha', $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_TEAM'], 'false', '', 'team_id');
echo $javascript->getScript();
