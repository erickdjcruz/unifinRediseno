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

global $dictionary;

$sugar_smarty = new Sugar_Smarty();
$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);

$sugar_smarty->assign('APP_LIST', $app_list_strings);
$role = BeanFactory::getBean('ACLRoles', $_REQUEST['record']);
$categories = ACLRole::getRoleActions($_REQUEST['record']);
$names = ACLAction::setupCategoriesMatrix($categories);

// Skipping modules that have 'hidden_to_role_assignment' property
$hidden_categories = [
    'Campaigns',
    'EmailTemplates',
    'EmailMarketing',
    'Forecasts',
    'PdfManager',
    'Reports',
    'ReportSchedules',
    'Metrics',
];
$metadataManager = MetaDataManager::getManager('base');
foreach ($categories as $name => $category) {
    $objName = BeanFactory::getObjectName($name) ?: $name;
    if (isset($dictionary[$objName])) {
        $vardefs = $dictionary[$objName];
    } else {
        $vardefs = $metadataManager->getVarDef($name);
    }
    if (!empty($vardefs)) {
        if (!empty($vardefs['hidden_to_role_assignment'])) {
            unset($categories[$name]);
        }
        if (!empty($vardefs['hide_fields_to_edit_role'])) {
            $hidden_categories[] = $name;
        }
    }
}

$categories2 = $categories;
foreach ($hidden_categories as $v) {
    if (isset($categories2[$v])) {
        unset($categories2[$v]);
    }
}
$sugar_smarty->assign('CATEGORIES2', $categories2);
if (!empty($names)) {
    $tdwidth = 100 / sizeof($names);
}
$sugar_smarty->assign('ROLE', $role->toArray());
$sugar_smarty->assign('CATEGORIES', $categories);
$sugar_smarty->assign('TDWIDTH', $tdwidth);
$sugar_smarty->assign('ACTION_NAMES', $names);

$return = ['module' => 'ACLRoles', 'action' => 'DetailView', 'record' => $role->id];
$sugar_smarty->assign('RETURN', $return);

$buttons = [
    <<<EOD
    <input id="ACLROLE_EDIT_BUTTON" title="{$app_strings['LBL_EDIT_BUTTON_TITLE']}" accessKey="{$app_strings['LBL_EDIT_BUTTON_KEY']}" class="button" onclick="var _form = $('#form')[0]; _form.action.value='EditView'; _form.submit();" type="submit" name="button" value="{$app_strings['LBL_EDIT_BUTTON']}" />
EOD
    ,
    <<<EOD
    <input id="ACLROLE_DUPLICATE_BUTTON" title="{$app_strings['LBL_DUPLICATE_BUTTON_TITLE']}" accessKey="{$app_strings['LBL_DUPLICATE_BUTTON_KEY']}" class="button" onclick="this.form.isDuplicate.value='1'; this.form.action.value='EditView'" type="submit" name="button" value=" {$app_strings['LBL_DUPLICATE_BUTTON']} " />
EOD
    ,
    <<<EOD
    <input id="ACLROLE_DELETE_BUTTON" title="{$app_strings['LBL_DELETE_BUTTON_TITLE']}" accessKey="{$app_strings['LBL_DELETE_BUTTON_KEY']}" class="button" onclick="this.form.return_module.value='ACLRoles'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$app_strings['NTC_DELETE_CONFIRMATION']}')" type="submit" name="button" value=" {$app_strings['LBL_DELETE_BUTTON']} " />
EOD
    ,
];
foreach ($buttons as $button) {
    $sugar_smarty->append('buttons', $button);
}

echo getClassicModuleTitle('ACLRoles', [
    sprintf('<a href="index.php?module=ACLRoles&action=index">%s</a>', htmlspecialchars($mod_strings['LBL_MODULE_NAME'], ENT_COMPAT)),
    htmlspecialchars($role->get_summary_text(), ENT_COMPAT),
], true);

$hide_hide_supanels = true;

echo $sugar_smarty->fetch('modules/ACLRoles/DetailView.tpl');
//for subpanels the variable must be named focus;
$focus =& $role;
$_REQUEST['module'] = 'ACLRoles';

$subpanel = new SubPanelTiles($role, 'ACLRoles');

echo $subpanel->display();
