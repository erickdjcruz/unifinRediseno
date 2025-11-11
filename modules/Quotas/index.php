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
 * Description:
 ********************************************************************************/

global $theme;

use Sugarcrm\Sugarcrm\Security\Csrf\CsrfAuthenticator;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

/* Requires to get the Currencies available to use */

$headerHTML = '';
global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;
global $sugar_config;

$db = DBManagerFactory::getInstance();
$focus = BeanFactory::newBean('Quotas');
$currency = new ListCurrency();
$params = [];
$params[] = sprintf(
    '<a href="index.php?module=Forecasts&action=index">%s</a>',
    htmlspecialchars($mod_strings['LBL_MODULE_FORECASTS_NAME'], ENT_COMPAT)
);
$params[] = htmlspecialchars($mod_strings['LBL_MODULE_NAME'], ENT_COMPAT);
echo getClassicModuleTitle($focus->module_dir, $params, true);

/* Set initial booleans for the module */
$is_edit = false;
$is_new = false;
$is_timeperiod_set = false;

$timeperiod_id = InputValidation::getService()->getValidInputRequest('timeperiod_id', 'Assert\Guid');

/*
 * Check if the time period is set, if it isn't, only display a dropdown
 * to select a time period.
 */
if (!empty($timeperiod_id)) {
    $optionsTimePeriodHTML = $focus->getTimePeriodsSelectList($timeperiod_id);
    $currentUserQuota = $focus->getCurrentUserQuota($timeperiod_id);
} else {
    $optionsTimePeriodHTML = $focus->getTimePeriodsSelectList();
}

/*
 * Check to see if both the records and timeperiod query strings are
 * available. If the record string is not processed, only display
 * data (self quota and directed quota) for time period only
 */
if (!empty($_REQUEST['record'])) {
    /* if the record query string says new, must edit and bring up a
     * blank text field to fill in a new value for user
     */
    if ($_REQUEST['record'] == 'new') {
        $is_new = true;
        $is_edit = true;
        $user_id = $_REQUEST['user_id'];
    } /* otherwise, it is possible to edit the record */
    else {
        $result = $focus->retrieve($_REQUEST['record']);
        if ($result == null) {
            sugar_die($app_strings['ERROR_NO_RECORD']);
        }
        $is_edit = true;
        $user_id = $focus->user_id;
    }
} else {
    $user_id = $focus->id;
}

$GLOBALS['log']->info('Quota list view');

$currentUserQuotaRow = '';
if (!empty($currentUserQuota['amount'])) {
    $currentUserQuotaRow .= '
<td scope="col" width="50%">
    <slot>' . htmlspecialchars($mod_strings['LBL_CURRENT_USER_QUOTA'], ENT_COMPAT) . '<br>
        <b>' . htmlspecialchars($currentUserQuota['formatted_amount'], ENT_COMPAT) . '</b>
    </slot>
</td>
';
} elseif (!empty($timeperiod_id)) {
    $currentUserQuotaRow .= '<td scope="col" width="50%"><slot>' . htmlspecialchars($mod_strings['LBL_CURRENT_USER_NO_QUOTA'], ENT_COMPAT) . '</td>';
}

$selectTimePeriod = '
<br />
<tr>
    <td width="50%" valign="top" class="dataLabel">
        <slot>' . htmlspecialchars($mod_strings['LBL_TIME_PERIOD'], ENT_COMPAT) . '</slot>
        <slot>
            <select name="timeperiod" ONCHANGE="location = this.options[this.selectedIndex].value;">'
    . $optionsTimePeriodHTML .
    '</select>
        </slot>
    </td>
';

$listViewHeader = $selectTimePeriod . $currentUserQuotaRow . '</tr>';

$where = 'quotas.deleted=0 AND users.deleted = 0 ';
if (!empty($timeperiod_id)) {
    $where .= ' AND quotas.timeperiod_id = ' . $db->quoted($timeperiod_id);
}

///////////////////////////////////////////////////////////////////////////////
////	QUOTAS MODULE LIST VIEW

$ListView = new ListView();

$ListView->initNewXTemplate('modules/Quotas/ListView.html', $mod_strings);
$ListView->setHeaderTitle(htmlspecialchars($mod_strings['LBL_LIST_FORM_TITLE'], ENT_COMPAT) . $headerHTML);
$ListView->setHeaderText($listViewHeader);
$ListView->show_export_button = false;
$ListView->show_mass_update = false;
$ListView->show_delete_button = false;
$ListView->show_select_menu = false;
$ListView->setQuery($where, '', '', 'QUOTA');

$row_count = $focus->getQuotaRowCount($focus->create_new_list_query('', $where));

if (!empty($timeperiod_id)) {
    /* if the user is not a manager, get the user's self quota
     * and use a strip down version of ListView to process the
     * quota object
     */
    if (!$focus->isManager($current_user->id)) {
        $ListView->processListView($focus, '', '');
    } /* otherwise, the user is a manager, and he/she has the available
     * tools to view and edit the quotas for their direct reports.
     */
    else {
        /* if records are available for the direct reports,
         * get the group quota and process the ListView
         */
        if ($row_count > 0) {
            $groupQuota = $focus->getGroupQuota($timeperiod_id);
            $ListView->xTemplateAssign('GROUP_QUOTA', outputGroupQuota($focus->getGroupQuota($timeperiod_id, false)));
            $currency->getSelectOptions();
            $ListView->xTemplateAssign('JAVASCRIPT2', $currency->getJavascript());
            $ListView->processListViewTwo($focus, 'main', 'QUOTA');
        } /* otherwise, process the ListView and letting them know that
         * no quotas have been entered for their direct reports
         */
        else {
            $ListView->xTemplateAssign('NOQUOTA', $mod_strings['LBL_NO_QUOTAS_TIMEPERIOD']);
            $ListView->processListViewTwo($focus, 'main', '');
        }

        ///////////////////////////////////////////////////////////////////////////////
        ////	QUOTAS MODULE EDIT VIEW

        $GLOBALS['log']->info('Quota edit view');
        $committed = '';
        if (empty($focus->currency_id)) {
            $selectCurrency = $currency->getSelectOptions();
        } else {
            $selectCurrency = $currency->getSelectOptions($focus->currency_id);
        }
        if (empty($_REQUEST['user_id'])) {
            $selectManagedUsers = $focus->getUserManagedSelectList($timeperiod_id);
        } else {
            $selectManagedUsers = $focus->getUserManagedSelectList($timeperiod_id, $_REQUEST['user_id']);
            if ($focus->committed == 1) {
                $committed = 'CHECKED';
            }
        }

        $csrf = CsrfAuthenticator::getInstance();
        $editButtonHTML = '
<form name="EditView" method="POST" action="index.php">
<input type="hidden" name="' . htmlspecialchars($csrf::FORM_TOKEN_FIELD, ENT_COMPAT) . '" value="' . htmlspecialchars($csrf->getFormToken(), ENT_COMPAT) . '" />
<input type="hidden" name="module" value="Quotas">';
        if (!$is_new) {
            $editButtonHTML .= '<input type="hidden" name="record" value="' . htmlspecialchars($focus->id, ENT_COMPAT) . '">';
        }

        $disabled = empty($_REQUEST['user_id']) ? ' disabled="disabled"' : '';

        $editButtonHTML .= '
<input type="hidden" name="user_id" value="' . htmlspecialchars($user_id, ENT_COMPAT) . '">
<input type="hidden" name="timeperiod_id" value="' . htmlspecialchars($timeperiod_id, ENT_COMPAT) . '">
<input type="hidden" name="action">
<input type="hidden" name="edit">
<input type="hidden" name="isDuplicate">
<input type="hidden" name="return_module" value="Quotas">
<input type="hidden" name="return_action" value="index">
<input type="hidden" name="return_user_id" value="' . htmlspecialchars($user_id, ENT_COMPAT) . '">
<input type="hidden" name="return_timeperiod_id" value="' . htmlspecialchars($timeperiod_id, ENT_COMPAT) . '">
<input type="hidden" name="return_id" value="">
<input title="' . htmlspecialchars($app_strings['LBL_SAVE_BUTTON_TITLE'], ENT_COMPAT) . '" accessKey="' . htmlspecialchars($app_strings['LBL_SAVE_BUTTON_KEY'], ENT_COMPAT) . '" 
    class="button" ' . $disabled . ' onclick="this.form.action.value=\'Save\'; return check_form(\'EditView\');" type="submit" 
    name="button" value="' . htmlspecialchars($app_strings['LBL_SAVE_BUTTON_LABEL'], ENT_COMPAT) . '" >
';

        $form_title = sprintf(
            '%s %s&nbsp;%s',
            htmlspecialchars($mod_strings['LBL_QUOTA'], ENT_COMPAT),
            htmlspecialchars($focus->user_full_name, ENT_COMPAT),
            $headerHTML
        );
        echo get_form_header($form_title, $editButtonHTML, false);
        $GLOBALS['log']->info('Quota edit view');
        $xtpl = new XTemplate('modules/Quotas/EditView.html');
        $xtpl->assign('MOD', $mod_strings);
        $xtpl->assign('APP', $app_strings);

        if (isset($_REQUEST['return_module'])) {
            $xtpl->assign('RETURN_MODULE', $_REQUEST['return_module']);
        }
        if (isset($_REQUEST['return_action'])) {
            $xtpl->assign('RETURN_ACTION', $_REQUEST['return_action']);
        }
        if (isset($_REQUEST['return_id'])) {
            $xtpl->assign('RETURN_ID', $_REQUEST['return_id']);
        }
        $xtpl->assign('JAVASCRIPT', get_set_focus_js());
        $xtpl->assign('ID', $focus->id);
        $xtpl->assign('USER_ID', $focus->user_id);
        $xtpl->assign('AMOUNT', $focus->amount);
        $xtpl->assign('USERNAME', $focus->user_name);
        $xtpl->assign('CURRENCY', $selectCurrency);
        $xtpl->assign('USERS', $selectManagedUsers);
        $xtpl->assign('COMMITTED', $committed);

        $xtpl->parse('main');
        $xtpl->out('main');


        $javascript = new javascript();
        $javascript->setFormName('EditView');
        $javascript->setSugarBean($focus);
        $javascript->addAllFields('');

        echo $javascript->getScript();
    }
} /* Do not process the usual "main" ListView page, just use the quota object
 * and deliver the time period.
 */
else {
    $ListView->processListViewTwo($focus, '', '');
}

function outputGroupQuota($groupQuota)
{
    $formattedGroupQuota = htmlspecialchars(format_number(
        $groupQuota,
        2,
        2,
        ['convert' => true, 'currency_symbol' => true,]
    ), ENT_COMPAT);
    $outputTotalHTML = '
<tr height="20">
<td scope="col"><slot>&nbsp;</slot></td>
<td scope="col" colspan="3" ><slot>Total</slot></td>
</tr>
<tr height="20">
<td scope="row" valign=TOP  class="oddListRowS1" bgcolor="#fdfdfd"><slot>&nbsp;</slot></td>
<td valign=TOP colspan="3" class="oddListRowS1" bgcolor="#fdfdfd"><slot>
<b><span id="groupQuota">' . $formattedGroupQuota . '</span></b>
&nbsp;&nbsp;&nbsp;&nbsp;
</slot></td>
</tr>';
    return $outputTotalHTML;
}
