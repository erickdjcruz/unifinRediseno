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
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

require_once 'modules/Campaigns/utils.php';

//if campaign_id is passed then we assume this is being invoked from the campaign module and in a popup.
$has_campaign = true;
$inboundEmail = true;
if (empty($_REQUEST['campaign_id'])) {
    $has_campaign = false;
}
if (empty($_REQUEST['inboundEmail'])) {
    $inboundEmail = false;
}

/**
 * @var EmailTemplate $focus
 */
$focus = BeanFactory::newBean('EmailTemplates');

if (isset($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}

$old_id = '';
if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] === 'true') {
    $old_id = $focus->id; // for attachments down below
    $focus->id = '';
}


//setting default flag value so due date and time not required
if (!isset($focus->id)) {
    $focus->date_due_flag = 1;
}

//needed when creating a new case with default values passed in
if (isset($_REQUEST['contact_name']) && $focus->contact_name === null) {
    $focus->contact_name = $_REQUEST['contact_name'];
}
if (isset($_REQUEST['contact_id']) && $focus->contact_id === null) {
    $focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['parent_name']) && $focus->parent_name === null) {
    $focus->parent_name = $_REQUEST['parent_name'];
}
if (isset($_REQUEST['parent_id']) && $focus->parent_id === null) {
    $focus->parent_id = $_REQUEST['parent_id'];
}
if (isset($_REQUEST['parent_type'])) {
    $focus->parent_type = $_REQUEST['parent_type'];
} elseif (!isset($focus->parent_type)) {
    $focus->parent_type = $app_list_strings['record_type_default_key'];
}
if (isset($_REQUEST['filename']) && $_REQUEST['isDuplicate'] !== 'true') {
    $focus->filename = $_REQUEST['filename'];
}

if ($has_campaign || $inboundEmail) {
    insert_popup_header($theme);
}


$params = [];

if (empty($focus->id)) {
    $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL'], ENT_COMPAT);
} else {
    $href = 'index.php?' . http_build_query([
            'module' => $focus->module_dir,
            'action' => 'DetailView',
            'record' => $focus->id,
        ]);
    $params[] = sprintf(
        '<a href="%s">%s</a>',
        htmlspecialchars($href, ENT_COMPAT),
        htmlspecialchars($focus->name, ENT_COMPAT)
    );
    $params[] = htmlspecialchars($GLOBALS['app_strings']['LBL_EDIT_BUTTON_LABEL'], ENT_COMPAT);
}

$pageTitle = getClassicModuleTitle($focus->module_dir, $params, true);

if (!$focus->ACLAccess('EditView')) {
    ACLController::displayNoAccess(true);
    sugar_cleanup(true);
}

$GLOBALS['log']->info('EmailTemplate detail view');

if ($has_campaign || $inboundEmail) {
    $xtpl = new XTemplate('modules/EmailTemplates/EditView.html');
} else {
    $xtpl = new XTemplate('modules/EmailTemplates/EditViewMain.html');
} // else
$xtpl->assign('PAGE_TITLE', $pageTitle);
$xtpl->assign('MOD', $mod_strings);
$xtpl->assign('APP', $app_strings);

$xtpl->assign('LBL_ACCOUNT', $app_list_strings['moduleList']['Accounts']);
$xtpl->parse('main.variable_option');

$returnModule = '';
if (isset($_REQUEST['return_module'])) {
    $returnModule = $_REQUEST['return_module'];
    $xtpl->assign('RETURN_MODULE', $returnModule);
}

$returnAction = 'index';
if (isset($_REQUEST['return_action'])) {
    $xtpl->assign('RETURN_ACTION', $_REQUEST['return_action']);
    $returnAction = $_REQUEST['return_action'];
}
if (isset($_REQUEST['return_id'])) {
    $xtpl->assign('RETURN_ID', $_REQUEST['return_id']);
}
// handle Create $module then Cancel
if (empty($_REQUEST['return_id'])) {
    $xtpl->assign('RETURN_ACTION', 'index');
}

$json = getJSONobj();
if ($has_campaign || $inboundEmail) {
    $cancel_script = 'window.close();';
} else {
    $returnActionJs = $json->encode($returnAction);
    $returnModuleJs = $json->encode($returnModule);
    $cancel_script = "this.form.action.value={$returnActionJs}; this.form.module.value={$returnModuleJs}';
    this.form.record.value=";
    if (empty($_REQUEST['return_id'])) {
        $cancel_script = "this.form.action.value='index'; this.form.module.value={$returnModuleJs};this.form.name.value='';this.form.description.value=''";
    } else {
        $cancel_script .= $json->encode($_REQUEST['return_id']);
    }
}
$cancel_script_html = htmlspecialchars($cancel_script, ENT_COMPAT);
//Setup assigned user name
$popup_request_data = [
    'call_back_function' => 'set_return',
    'form_name' => 'EditView',
    'field_to_name_array' => [
        'id' => 'assigned_user_id',
        'user_name' => 'assigned_user_name',
    ],
];
$xtpl->assign('encoded_assigned_users_popup_request_data', $json->encode($popup_request_data));
if (!empty($focus->assigned_user_name)) {
    $xtpl->assign('ASSIGNED_USER_NAME', $focus->assigned_user_name);
}

$xtpl->assign('assign_user_select', SugarThemeRegistry::current()->getImage('id-ff-select', '', null, null, '.png', $mod_strings['LBL_SELECT']));
$xtpl->assign('assign_user_clear', SugarThemeRegistry::current()->getImage('id-ff-clear', '', null, null, '.gif', $mod_strings['LBL_ID_FF_CLEAR']));
//Assign qsd script
$qsd = QuickSearchDefaults::getQuickSearchDefaults();
$sqs_objects = ['EditView_assigned_user_name' => $qsd->getQSUser()];
$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . $json->encode($sqs_objects) . '; enableQS();</script>';

$xtpl->assign('CANCEL_SCRIPT', $cancel_script_html);
$xtpl->assign('JAVASCRIPT', get_set_focus_js() . $quicksearch_js);

if (!is_file(sugar_cached('jsLanguage/') . $GLOBALS['current_language'] . '.js')) {
    jsLanguage::createAppStringsCache($GLOBALS['current_language']);
}
$jsLang = getVersionedScript("cache/jsLanguage/{$GLOBALS['current_language']}.js", $GLOBALS['sugar_config']['js_lang_version']);
$xtpl->assign('JSLANG', $jsLang);

$xtpl->assign('ID', $focus->id);
if (isset($focus->name)) {
    $xtpl->assign('NAME', $focus->name);
} else {
    $xtpl->assign('NAME', '');
}

//Bug45632
if (isset($focus->assigned_user_id)) {
    $xtpl->assign('ASSIGNED_USER_ID', $focus->assigned_user_id);
} else {
    $xtpl->assign('ASSIGNED_USER_ID', '');
}
//Bug45632

if (isset($focus->description)) {
    $xtpl->assign('DESCRIPTION', $focus->description);
} else {
    $xtpl->assign('DESCRIPTION', '');
}
if (isset($focus->subject)) {
    $xtpl->assign('SUBJECT', $focus->subject);
} else {
    $xtpl->assign('SUBJECT', '');
}
if ($focus->published === 'on') {
    $xtpl->assign('PUBLISHED', 'CHECKED');
}

$isForgotPasswordEmail = $focus->isForgotPasswordTemplate();
$xtpl->assign('TEXTONLY_DISABLED', $isForgotPasswordEmail ? 'DISABLED' : '');

//if text only is set to true, then make sure input is checked and value set to 1
if ($focus->text_only || $isForgotPasswordEmail) {
    $xtpl->assign('TEXTONLY_CHECKED', 'CHECKED');
    $xtpl->assign('TEXTONLY_VALUE', '1');
} else {//set value to 0
    $xtpl->assign('TEXTONLY_VALUE', '0');
}

//Assign the Teamset field
$teamSetField = new SugarFieldTeamset('Teamset');
$code = $teamSetField->getClassicView($focus->field_defs);
$xtpl->assign('TEAM', $code);

$xtpl->assign('FIELD_DEFS_JS', $focus->generateFieldDefsJS());
$xtpl->assign('LBL_CONTACT', $app_list_strings['moduleList']['Contacts']);

global $current_user;
$request = InputValidation::getService();
$module = $request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');

$escapedHTML = static function ($html): string {
    return htmlspecialchars($html, ENT_QUOTES);
};

if (!empty($focus->parent_type)) {
    $change_parent_button = <<<HTML
<input title="{$escapedHTML($app_strings['LBL_SELECT_BUTTON_TITLE'])}" 
    tabindex="3" type="button" class="button" value="{$escapedHTML($app_strings['LBL_SELECT_BUTTON_LABEL'])}" 
    name="button" 
    onclick="return window.open('index.php?module=' + encodeURIComponent(document.EditView.parent_type.value) + '&action=Popup&html=Popup_picker&form=TasksEditView', 'test', 'width=600,height=400,resizable=1,scrollbars=1');">
HTML;

    $xtpl->assign('CHANGE_PARENT_BUTTON', $change_parent_button);
}
if ($focus->parent_type === 'Account') {
    $searchQuery = '&' . http_build_query([
            'query' => 'true',
            'account_id' => $focus->parent_id,
            'account_name' => $focus->parent_name,
        ]);
    $xtpl->assign('DEFAULT_SEARCH', $searchQuery);
}

$xtpl->assign('DESCRIPTION', $focus->description);
$xtpl->assign('TYPE_OPTIONS', get_select_options_with_id($app_list_strings['record_type_display'], $focus->parent_type));

if (isset($focus->body)) {
    $xtpl->assign('BODY', $focus->body);
} else {
    $xtpl->assign('BODY', '');
}
if (isset($focus->body_html)) {
    $xtpl->assign('BODY_HTML', $focus->body_html);
} else {
    $xtpl->assign('BODY_HTML', '');
}

if (!isTouchScreen()) {
    $tiny = new SugarTinyMCE();
    $tiny->defaultConfig['cleanup_on_startup'] = true;
    $tiny->defaultConfig['height'] = 600;
    $tiny->defaultConfig['plugins'] .= ',fullpage';
    $tinyHtml = $tiny->getInstance();
    $xtpl->assign('tiny', $tinyHtml);
}
///////////////////////////////////////
////	MACRO VARS
$xtpl->assign('INSERT_VARIABLE_ONCLICK', 'insert_variable(document.EditView.variable_text.value)');

// bug 37255, included without condition
$xtpl->parse('main.NoInbound.variable_button');

///////////////////////////////////////
////	CAMPAIGNS
if ($has_campaign || $inboundEmail) {
    $xtpl->assign('INPOPUPWINDOW', 'true');
    $xtpl->assign('INSERT_URL_ONCLICK', 'insert_variable_html_link(document.EditView.tracker_url.value)');
    if ($has_campaign) {
        $campaign_urls = get_campaign_urls($_REQUEST['campaign_id']);
    }
    if (!empty($campaign_urls)) {
        $xtpl->assign('DEFAULT_URL_TEXT', key($campaign_urls));
    }
    if ($has_campaign) {
        $xtpl->assign('TRACKER_KEY_OPTIONS', get_select_options_with_id($campaign_urls, null));
        $xtpl->parse('main.NoInbound.tracker_url');
    }
}

// create option of "Contact/Lead/Task" from corresponding module
// translations
$lblContactAndOthers = implode('/', [
    $app_list_strings['moduleListSingular']['Contacts'] ?? 'Contact',
    $app_list_strings['moduleListSingular']['Leads'] ?? 'Lead',
    $app_list_strings['moduleListSingular']['Prospects'] ?? 'Target',
]);

// The insert variable drodown should be conditionally displayed.
// If it's campaign then hide the Account.
if ($has_campaign) {
    $dropdown = '<option value="Contacts">' . htmlspecialchars($lblContactAndOthers, ENT_COMPAT) . '</option>';
    $xtpl->assign('DROPDOWN', $dropdown);
    $xtpl->assign('DEFAULT_MODULE', 'Contacts');
} else {
    $dropdown = <<<HTML
<option value="Accounts">
    {$escapedHTML($app_list_strings['moduleListSingular']['Accounts'])} 
</option>
<option value="Contacts">'
    {$escapedHTML($lblContactAndOthers)}.
</option>
<option value="Users">
    {$escapedHTML($app_list_strings['moduleListSingular']['Users'])}
</option>
<option value="Current User">
    Current {$escapedHTML($app_list_strings['moduleListSingular']['Users'])}
</option>
HTML;

    $xtpl->assign('DROPDOWN', $dropdown);
    $xtpl->assign('DEFAULT_MODULE', 'Accounts');
}
////	END CAMPAIGNS
///////////////////////////////////////

///////////////////////////////////////
////    ATTACHMENTS
$attachments = '';
if (!empty($focus->id)) {
    $etid = $focus->id;
} elseif (!empty($old_id)) {
    $xtpl->assign('OLD_ID', $old_id);
    $etid = $old_id;
}
if (!empty($etid)) {
    $note = BeanFactory::newBean('Notes');
    //FIXME: notes.email_type should be EmailTemplates
    //FIXME: notes.filename IS NOT NULL is probably not necessary
    $where = 'notes.email_id=' . $GLOBALS['db']->quoted($etid) . ' AND notes.filename IS NOT NULL';
    $notes_list = $note->get_full_list('', $where, true) ?? [];

    for ($i = 0, $iMax = safeCount($notes_list); $i < $iMax; $i++) {
        $the_note = $notes_list[$i];
        if (empty($the_note->filename)) {
            continue;
        }
        $secureLink = 'index.php?' . http_build_query([
                'entryPoint' => 'download',
                'id' => $the_note->id,
                'type' => 'Notes',
            ]);
        $attachments .= '<input type="checkbox" name="remove_attachment[]" value="' . htmlspecialchars($the_note->id, ENT_COMPAT) . '" /> '
            . htmlspecialchars($app_strings['LNK_REMOVE'], ENT_COMPAT) . '&nbsp;&nbsp;'
            . '<a href="' . htmlspecialchars($secureLink, ENT_COMPAT) . '" target="_blank">' . htmlspecialchars($the_note->filename, ENT_COMPAT) . '</a><br>';
    }
}
$attJs = '<script type="text/javascript">'
    . 'var lnk_remove = ' . $json->encode($app_strings['LNK_REMOVE']) . ';'
    . '</script>';
$xtpl->assign('ATTACHMENTS', $attachments);
$xtpl->assign('ATTACHMENTS_JAVASCRIPT', $attJs);

////    END ATTACHMENTS
///////////////////////////////////////
$templateType = !empty($focus->type) ? $focus->type : '';
if ($has_campaign) {
    if (empty($_REQUEST['record'])) {
        // new record, default to campaign
        $xtpl->assign('TYPEDROPDOWN', get_select_options_with_id($app_list_strings['emailTemplates_type_list_campaigns'], 'campaign'));
    } else {
        $xtpl->assign('TYPEDROPDOWN', get_select_options_with_id($app_list_strings['emailTemplates_type_list_campaigns'], $templateType));
    }
} else {
    // if the type is workflow, we will show it
    // otherwise we don't allow user to select workflow type because workflow type email template
    // should be created from within workflow module because it requires more fields (such as base module, etc)
    if ($templateType === 'workflow') {
        $xtpl->assign('TYPEDROPDOWN', get_select_options_with_id($app_list_strings['emailTemplates_type_list'], $templateType));
    } elseif ($templateType === 'system') {
        // if the type is system, the type cannot be changed, the dropdown should contain 'system' only
        $availableOptions = ['system' => $app_list_strings['emailTemplates_type_list']['system']];
        $xtpl->assign('TYPEDROPDOWN', get_select_options_with_id($availableOptions, $templateType));
    } else {
        $xtpl->assign('TYPEDROPDOWN', get_select_options_with_id($app_list_strings['emailTemplates_type_list_no_workflow'], $templateType));
    }
}
// done and parse
$xtpl->parse('main.textarea');

//Add Custom Fields
require_once 'modules/DynamicFields/templates/Files/EditView.php';
$xtpl->parse('main.NoInbound');
if (!$inboundEmail) {
    $xtpl->parse('main.NoInbound1');
    $xtpl->parse('main.NoInbound2');
    $xtpl->parse('main.NoInbound3');
}
$xtpl->parse('main.NoInbound4');
$xtpl->parse('main.NoInbound5');
$xtpl->parse('main');

$xtpl->out('main');

$javascript = new javascript();
$javascript->setFormName('EditView');
$javascript->setSugarBean($focus);
$javascript->addAllFields('');
echo $javascript->getScript();
