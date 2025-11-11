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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

global $app_list_strings;// $modInvisList;

$sugar_smarty = new Sugar_Smarty();

$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);
$sugar_smarty->assign('ISDUPLICATE', '');
$duplicateString = '';
//mass localization
$sugar_smarty->assign('APP_LIST', $app_list_strings);
$role = BeanFactory::newBean('ACLRoles');
$role_name = '';
$return = ['module' => 'ACLRoles', 'action' => 'index', 'record' => ''];
$request = InputValidation::getService();
$record = $request->getValidInputRequest('record', 'Assert\Guid');
$isDuplicate = $request->getValidInputRequest('isDuplicate');

if (!empty($record)) {
    $role->retrieve($record);
    $categories = ACLRole::getRoleActions($record);
    $role_name = $role->name;
    if (!empty($isDuplicate)) {
        //role id is stripped here in duplicate so anything using role id after this will not have it
        $role->id = '';
        $sugar_smarty->assign('ISDUPLICATE', $record);
        $duplicateString = translate('LBL_DUPLICATE_OF', 'ACLRoles');
    } else {
        $return['record'] = $role->id;
        $return['action'] = 'DetailView';
    }
} else {
    $categories = ACLRole::getRoleActions('');
}
$sugar_smarty->assign('ROLE', $role->toArray());
$tdwidth = 10;

$returnModule = $request->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName');
$returnId = $request->getValidInputRequest('return_id', 'Assert\Guid');
$returnAction = $request->getValidInputRequest('return_action');
$returnRecord = $request->getValidInputRequest('return_record', 'Assert\Guid');

if ($returnModule !== null) {
    $return['module'] = $returnModule;

    if ($returnId !== null) {
        $return['record'] = $returnId;
    }
    if ($returnRecord !== null) {
        $return['record'] = $returnRecord;
    }
    if ($returnAction !== null) {
        $return['action'] = $returnAction;
    }
    if (!empty($return['record'])) {
        $return['action'] = 'DetailView';
    }
}

$sugar_smarty->assign('RETURN', $return);
$names = ACLAction::setupCategoriesMatrix($categories);

$sugar_smarty->assign('CATEGORIES', $categories);
$sugar_smarty->assign('TDWIDTH', $tdwidth);
$sugar_smarty->assign('ACTION_NAMES', $names);

$params = [];
$params[] = sprintf(
    '<a href="index.php?module=ACLRoles&action=index">%s</a>',
    htmlspecialchars($mod_strings['LBL_MODULE_NAME'], ENT_COMPAT)
);
if (empty($role->id)) {
    $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL'], ENT_COMPAT);
} else {
    $params[] = htmlspecialchars($role->get_summary_text(), ENT_COMPAT);
}
echo getClassicModuleTitle('ACLRoles', $params, true);

$escapedHTML = static function ($html): string {
    return htmlspecialchars($html, ENT_QUOTES);
};

$buttons = [
    <<<HTML
<input title="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_TITLE'])}" id="save_button" 
        accessKey="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_KEY'])}" class="button primary"
        onclick="this.form.action.value='Save';return check_form('EditView');"
        type="submit" name="button" value="{$escapedHTML($app_strings['LBL_SAVE_BUTTON_LABEL'])}" >
HTML
    ,
    <<<HTML
<input title="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_TITLE'])}" class="button cancel_button"
        accessKey="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_KEY'])}"
        type="submit" name="save" value="{$escapedHTML($app_strings['LBL_CANCEL_BUTTON_LABEL'])}"
        onclick="document.EditView.action.value='{$escapedHTML($return['action'])}';document.EditView.module.value='{$escapedHTML($return['module'])}';document.EditView.record.value='{$escapedHTML($return['record'])}';document.EditView.submit();">
HTML
    ,
];

$action_buttons = $buttons;
$sugar_smarty->assign('ACTION_MENU', $action_buttons);
echo $sugar_smarty->fetch('modules/ACLRoles/EditView.tpl');
