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
global $theme;


global $app_strings;
global $app_list_strings;
global $mod_strings;

global $urlPrefix;
global $currentModule;

if (!empty($_REQUEST['workflow_id'])) {
    $seed_object = BeanFactory::retrieveBean('WorkFlow', $_REQUEST['workflow_id']);
}
if (empty($seed_object)) {
    sugar_die("You shouldn't be here");
}


////////////////////////////////////////////////////////
// Start the output
////////////////////////////////////////////////////////
if (!isset($_REQUEST['html'])) {
    $form = new XTemplate('modules/WorkFlowActions/Selector.html');
    $GLOBALS['log']->debug('using file modules/WorkFlowActions/Selector.html');
} else {
    $_REQUEST['html'] = preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['html']);
    $GLOBALS['log']->debug("_REQUEST['html'] is " . $_REQUEST['html']);
    $form = new XTemplate('modules/WorkFlowActions/' . $_REQUEST['html'] . '.html');
    $GLOBALS['log']->debug('using file modules/WorkFlowActions/' . $_REQUEST['html'] . '.html');
}

$form->assign('MOD', $mod_strings);
$form->assign('APP', $app_strings);

$focus = BeanFactory::newBean('WorkFlowActionShells');


///////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!?////////////////////

//Add When Expressions Object is availabe
//$exp_object = new Expressions();


//////////////////////////////////////////////////////////////////

$action_object = BeanFactory::newBean('WorkFlowActions');

if (!empty($_REQUEST['action_id'])) {
    $action_object->retrieve($_REQUEST['action_id']);
}

if (!empty($_REQUEST['target_field'])) {
    $action_object->field = $_REQUEST['target_field'];
}

foreach ($action_object->selector_fields as $field) {
    if (isset($_REQUEST[$field])) {
        $action_object->$field = $_REQUEST[$field];
    }
}

if (!empty($_REQUEST['adv_value'])) {
    $action_object->value = $_REQUEST['adv_value'];
}

$temp_module = BeanFactory::newBean($_REQUEST['target_module']);
global $mod_strings, $current_language;

if ($temp_module === null) {
    sugar_die('Target_module required');
}

if (isset($_REQUEST['field_num'])) {
    $field_num = intval($_REQUEST['field_num']);
} else {
    sugar_die('field_num is not set in request');
}

$allowed_action_types = ['update', 'update_rel', 'new', 'new_rel'];
if (isset($_REQUEST['action_type']) && in_array($_REQUEST['action_type'], $allowed_action_types)) {
    $action_type = $_REQUEST['action_type'];
} else {
    sugar_die('action_type is invalid or not set in request');
}

$output_array = $action_object->build_field_selector($field_num, $_REQUEST['target_module'], $seed_object->type, $action_type);

$form->assign('PREFIX', 'field_' . $field_num . '__');
$form->assign('VALUE', $output_array['value_select']['display']);
$form->assign('FIELD_NAME', $output_array['name']);
$form->assign('MOD', $GLOBALS['mod_strings']);
$form->assign('TITLE', $GLOBALS['mod_strings']['LBL_TITLE']);

$form->assign('FIELD_NUM', 'field_' . $field_num);
$form->assign('FIELD_NUMBER', $field_num);
$form->assign('ADV_TYPE', $output_array['adv_type']['display']);
$form->assign('ADV_VALUE', $output_array['adv_value']['display']);
$form->assign('EXT1', $output_array['ext1']['display']);
$form->assign('EXT2', $output_array['ext2']['display']);
$form->assign('EXT3', $output_array['ext3']['display']);

$form->assign('FIELD_TYPE', $output_array['real_type']);
if ($action_object->set_type == '') {
    $action_object->set_type = 'Basic';
}

$form->assign('SET_TYPE', $action_object->set_type);

if (!empty($output_array['set_type']['disabled'])) {
    $form->assign('SET_DISABLED', 'Yes');
}


$form->assign('ADVANCED_SEARCH_PNG', SugarThemeRegistry::current()->getImage('advanced_search', '  border="0"', null, null, '.gif', $app_strings['LNK_ADVANCED_SEARCH']));
$form->assign('BASIC_SEARCH_PNG', SugarThemeRegistry::current()->getImage('basic_search', '  border="0"', null, null, '.gif', $app_strings['LNK_BASIC_SEARCH']));

$form->assign('MODULE_NAME', $currentModule);

$form->assign('GRIDLINE', $gridline);

insert_popup_header($theme);

$form->parse('embeded');
$form->out('embeded');

$form->parse('main');
$form->out('main');

$mod_strings = return_module_language($current_language, $temp_module->module_dir);
//now build toggle js
$type = $output_array['real_type'];
$js = '<script type="text/javascript">';
$js .= 'function toggle_type(type){';
$js .= "if(type == 'Advanced'){";
$javascript = new javascript();
$js .= processJsForSelectorField($javascript, $action_object->field, $type, $temp_module, $field_num, 'adv') . '}';
$js .= 'else{' . processJsForSelectorField($javascript, $action_object->field, $type, $temp_module, $field_num, 'field') . '}}</script>';
if (empty($action_object->set_type) || ($action_object->set_type === 'Basic')) {
    $js .= $javascript->getScript(true);
} else {
    $javascript = new javascript();
    $javascript->setFormName('EditView');
    $javascript->setSugarBean($temp_module);
    $javascript->addField($action_object->field, '', '', 'field_' . $field_num . '__adv_value');
    $js .= $javascript->getScript(true);
}
echo $js;
//rsmith
echo $GLOBALS['timedate']->get_javascript_validation();


function processJsForSelectorField(&$javascript, $field, $type, $tempModule, $fieldNumber, $ifAdvanced = 'field')
{
    $jsString = '';
    $javascript = new javascript();
    // Validate everything.
    $workFlowActionsExceptionFields = [];

    $js_full_field_name = json_encode('field_' . $fieldNumber . '__' . $ifAdvanced . '_value', JSON_HEX_TAG);
    $js_vname = json_encode($javascript->stripEndColon(translate($tempModule->field_defs[$field]['vname'])), JSON_HEX_TAG);

    if (in_array($type, $workFlowActionsExceptionFields) != 1) {
        $jsString .= 'removeFromValidate("EditView", ' . $js_full_field_name . ');';
    }

    if (in_array($type, ['date', 'time', 'datetimecombo'])) {
        $jsString .= 'addToValidate('
            . "'EditView', "
            . $js_full_field_name . ', '
            . "'assigned_user_name', "
            . '1, '
            . $js_vname
            . ')';
    } elseif (!(in_array($type, $workFlowActionsExceptionFields) == 1)) {
        $javascript->setFormName('EditView');
        $javascript->setSugarBean($tempModule);
        $javascript->addField($field, '', '', $js_full_field_name);
        $jsString .= $javascript->getScript(false);
    }
    return $jsString;
}

insert_popup_footer();
