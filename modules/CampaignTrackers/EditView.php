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

require_once 'modules/CampaignTrackers/Forms.php';
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $sugar_version, $sugar_config;

$focus = BeanFactory::newBean('CampaignTrackers');

if (isset($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}
$old_id = '';

if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] === 'true') {
    $focus->id = '';
}

$GLOBALS['log']->info('Campaign Tracker Edit View');

$xtpl = new XTemplate('modules/CampaignTrackers/EditView.html');
$xtpl->assign('MOD', $mod_strings);
$xtpl->assign('APP', $app_strings);

$campaignName = '';
$campaignId = '';
if (!empty($_REQUEST['campaign_name'])) {
    $xtpl->assign('CAMPAIGN_NAME', $_REQUEST['campaign_name']);
    $campaignName = $_REQUEST['campaign_name'];
} else {
    $xtpl->assign('CAMPAIGN_NAME', $focus->campaign_name);
    $campaignName = $focus->campaign_name;
}
if (!empty($_REQUEST['campaign_id'])) {
    $xtpl->assign('CAMPAIGN_ID', $_REQUEST['campaign_id']);
    $campaignId = $_REQUEST['campaign_id'];
} else {
    $xtpl->assign('CAMPAIGN_ID', $focus->campaign_id);
    $campaignId = $focus->campaign_id;
}
$params = [];
$href = 'index.php?' . http_build_query([
        'module' => 'Campaigns',
        'action' => 'DetailView',
        'record' => $campaignId,
    ]);
$params[] = sprintf(
    '<a href="%s">%s</a>',
    htmlspecialchars($href, ENT_COMPAT),
    htmlspecialchars($campaignName, ENT_COMPAT)
);
$params[] = htmlspecialchars($mod_strings['LBL_MODULE_NAME'], ENT_COMPAT);
$xtpl->assign('PAGE_TITLE', getClassicModuleTitle($focus->module_dir, $params, true));

$request = InputValidation::getService();
$returnModule = $request->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName');
$returnId = $request->getValidInputRequest('return_id', 'Assert\Guid');
$returnAction = $request->getValidInputRequest('return_action');

if ($returnModule !== null) {
    $xtpl->assign('RETURN_MODULE', $returnModule);
}
if ($returnAction !== null) {
    $xtpl->assign('RETURN_ACTION', $returnAction);
}
if ($returnId !== null) {
    $xtpl->assign('RETURN_ID', $returnId);
}

$xtpl->assign('JAVASCRIPT', get_set_focus_js() . get_validate_record_js());
$xtpl->assign('ID', $focus->id);


$xtpl->assign('TRACKER_NAME', $focus->tracker_name);
$xtpl->assign('TRACKER_URL', $focus->tracker_url);

global $current_user;
$module = $request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');

if (!empty($focus->is_optout) && $focus->is_optout == 1) {
    $xtpl->assign('IS_OPTOUT_CHECKED', 'checked');
    $xtpl->assign('TRACKER_URL_DISABLED', 'disabled');
}

$xtpl->parse('main');

$xtpl->out('main');

$javascript = new javascript();
$javascript->setFormName('EditView');
$javascript->setSugarBean($focus);
$javascript->addAllFields('');
echo $javascript->getScript();
