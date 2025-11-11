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

class ViewSchedule extends SugarView
{
    public function display()
    {
        global $mod_strings, $timedate, $app_strings;

        $inputValidation = InputValidation::getService();

        include_once 'modules/Reports/schedule/save_schedule.php';
        $smarty = new Sugar_Smarty();
        $smarty->assign('MOD', $mod_strings);
        $smarty->assign('APP', $app_strings);
        $smarty->assign('PAGE_TITLE', $mod_strings['LBL_SCHEDULE_EMAIL']);
        $smarty->assign('STYLESHEET', SugarThemeRegistry::current()->getCSS());
        $smarty->assign('CALENDAR_LANG', substr($GLOBALS['current_language'], 0, 2));
        $smarty->assign('CALENDAR_DATEFORMAT', $timedate->get_cal_date_format());
        $requestId = $inputValidation->getValidInputRequest('id', 'Assert\Guid');
        $smarty->assign('RECORD', $requestId);

        $cache_dir = !empty($GLOBALS['sugar_config']['cache_dir']) ?
            rtrim($GLOBALS['sugar_config']['cache_dir'], '/\\') : 'cache';
        $smarty->assign('CACHE_DIR', $cache_dir);

        $refreshPage = ($_REQUEST['refreshPage'] ?? 'true');
        $refreshPage = strval($refreshPage);
        $smarty->assign('REFRESH_PAGE', $refreshPage);
        $time_interval_select = translate('DROPDOWN_SCHEDULE_INTERVALS', 'Reports');
        $time_format = $timedate->get_user_time_format();
        $smarty->assign('TIME_FORMAT', $time_format);
        $smarty->assign('TIMEDATE_JS', self::getJavascriptValidation());
        $smarty->assign('TIME_INTERVAL_OPTIONS', $time_interval_select);
        $rs = new ReportSchedule();
        $schedule = $rs->get_report_schedule_for_user($requestId);
        if ($schedule) {
            $smarty->assign('SCHEDULE_ID', $schedule['id']);
            $smarty->assign('DATE_START', $timedate->to_display_date_time($schedule['date_start'], true));

            if ($schedule['active']) {
                $smarty->assign('SCHEDULE_ACTIVE_CHECKED', 'checked');
            }

            $smarty->assign('NEXT_RUN', $timedate->to_display_date_time($schedule['next_run']));
            $smarty->assign('TIME_INTERVAL', htmlspecialchars($schedule['time_interval'], ENT_COMPAT));
            $smarty->assign('SCHEDULE_TYPE', $schedule['schedule_type']);
        } else {
            $smarty->assign('NEXT_RUN', $mod_strings['LBL_NONE']);
            $smarty->assign('TIME_INTERVAL', '');
            if (isset($_REQUEST['schedule_type']) && $_REQUEST['schedule_type'] != '') {
                $scheduleType = strval($_REQUEST['schedule_type']);
                $smarty->assign('SCHEDULE_TYPE', $scheduleType);
            }
        }

        $smarty->assign('CURRENT_LANGUAGE', $GLOBALS['current_language']);
        $smarty->assign('JS_VERSION', $GLOBALS['js_version_key']);
        $smarty->assign('JS_CUSTOM_VERSION', $GLOBALS['sugar_config']['js_custom_version']);
        $smarty->assign('JS_LANGUAGE_VERSION', $GLOBALS['sugar_config']['js_lang_version']);

        $smarty->display('modules/Reports/tpls/AddSchedule.tpl');
    }
}
