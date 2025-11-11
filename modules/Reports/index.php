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


$GLOBALS['displayListView'] = true;

require_once 'modules/Reports/templates/templates_reports.php';
require_once 'modules/Reports/templates/templates_pdf.php';
require_once 'modules/Reports/templates/templates_export.php';
require_once 'modules/Reports/config.php';
require_once 'src/Security/InputValidation/InputValidation.php';

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

global $current_language, $report_modules, $modules_report, $mod_strings;

$args = [];

// set default
if ($_REQUEST['action'] === 'index') {
    if (isset($_REQUEST['id'])) {
        $saved_report_seed = BeanFactory::newBean('Reports');
        $saved_report_seed->disable_row_level_security = true;

        $saved_report_seed->retrieve($_REQUEST['id'], false);

        $args['reporter'] = new Report($saved_report_seed->content);
        $args['reporter']->saved_report = &$saved_report_seed;

        if (isset($_REQUEST['filter_key']) && isset($_REQUEST['filter_value'])) {
            $new_filter = [];
            [$new_filter['table_name'], $new_filter['name']] = explode(':', $_REQUEST['filter_key']);
            $new_filter['qualifier_name'] = 'is';
            $new_filter['input_name0'] = [$_REQUEST['filter_value']];

            if (!is_array($args['reporter']->report_def['filters_def'])) {
                $args['reporter']->report_def['filters_def'] = [];
            }
            array_push($args['reporter']->report_def['filters_def'], $new_filter);
            $args['reporter']->report_def['chart_type'] = 'none';
            $args['reporter']->chart_type = 'none';
        }

        $args['reporter']->is_saved_report = true;
        $args['reporter']->saved_report_id = $saved_report_seed->id;
    } else {
        $report_def = [];
        if (!empty($_REQUEST['report_def'])) {
            $report_def = html_entity_decode($_REQUEST['report_def'], ENT_COMPAT);
            $panels_def = html_entity_decode($_REQUEST['panels_def'], ENT_COMPAT);
            $filters_def = html_entity_decode($_REQUEST['filters_defs'], ENT_COMPAT);
            $args['reporter'] = new Report($report_def, $filters_def, $panels_def);

            if (!empty($_REQUEST['save_report_as'])) {
                $args['reporter']->name = $_REQUEST['save_report_as'];
            }
        } else {
            $reporter = new Report();
            $args['reporter'] = $reporter;
        }
    }

    if (!empty($_REQUEST['to_pdf']) && empty($_REQUEST['search_form_only'])) {
        template_handle_pdf($args['reporter']);
        return;
    }
    if (!empty($_REQUEST['to_csv'])) {
        //check to see if exporting is allowed
        if (!hasExportAccess($args)) {
            //die if one of the above conditions has been met
            sugar_die($mod_strings['LBL_NO_EXPORT_ACCESS']);
        }
        template_handle_export($args['reporter']);
        return;
    }


    // create report obj with the seed
    $args['list_nav'] = '';
    $args['upper_left'] = '';


    // do saves, deletes, and publish
    control($args);


    // show report interface
    if (isset($_REQUEST['page']) && $_REQUEST['page'] === 'report') {
        checkSavedReportACL($args['reporter'], $args);
        include 'modules/Reports/ReportsWizard.php';
    } else { // show report lists
        if (empty($_REQUEST['search_form_only'])) {
            $params = [];
            if (!empty($_REQUEST['favorite'])) {
                $params[] = htmlspecialchars($mod_strings['LBL_FAVORITES_TITLE'], ENT_COMPAT);
            } else {
                $params[] = htmlspecialchars($app_strings['LBL_SEARCH'], ENT_COMPAT);
            }

            //Override the create url
            $createURL = 'index.php?module=Reports&report_module=&action=index&page=report&Create+Custom+Report=Create+Custom+Report';
            echo getClassicModuleTitle('Reports', $params, true, '', $createURL) . '<div class="clear"></div>';
        }

        $fileToLoad = 'modules/Reports/ListView.php';
        if (file_exists($fileToLoad)) {
            include SugarAutoLoader::existingCustomOne($fileToLoad);
        }
    }
}

function checkACLForEachColInArr($arr, $full_table_list, $is_owner = 1)
{
    foreach ($arr as $column) {
        $col_module = $full_table_list[$column['table_key']]['module'];
        //todo: check the last param of this call (is_owner)
        if (!SugarACL::checkField($col_module, $column['name'], 'access', $is_owner ? ['owner_override' => true] : [])) {
            return false;
        }
    }
    return true;
}

function checkACLForEachColInArrForFilterDef($arr, $full_table_list, $is_owner = 1)
{
    return checkACLForEachColForFilter($arr, $full_table_list, $is_owner, true);
}

function checkACLForEachColForFilter($filters, $full_table_list, $is_owner, $hasAccess)
{
    if (!$hasAccess) {
        return false;
    } // if
    $i = 0;
    while (isset($filters[$i])) {
        $current_filter = $filters[$i];
        if (isset($current_filter['operator'])) {
            $hasAccess = checkACLForEachColForFilter($current_filter, $full_table_list, $is_owner, $hasAccess);
            if ($hasAccess) {
                return $hasAccess;
            } // if
        } elseif (!empty($full_table_list[$current_filter['table_key']]['module'])) {
            $col_module = $full_table_list[$current_filter['table_key']]['module'];

            if (!SugarACL::checkField(
                $col_module,
                $current_filter['name'],
                'detail',
                $is_owner ? ['owner_override' => true] : []
            )
            ) {
                return false;
            } // if
        }
        $i++;
    } // while
    return $hasAccess;
} // fn

function checkSavedReportACL(&$reporter, &$args)
{
    global $mod_strings;

    // Only run these checks for saved reports.
    if (!empty($_REQUEST['id'])) {
        $display_columns = $reporter->report_def['display_columns'];
        $filters_def = $reporter->report_def['filters_def']['Filter_1'];
        $group_defs = $reporter->report_def['group_defs'];
        if (!empty($reporter->report_def['order_by'])) {
            $order_by = $reporter->report_def['order_by'];
        } else {
            $order_by = [];
        }

        $summary_columns = $reporter->report_def['summary_columns'];
        $full_table_list = $reporter->report_def['full_table_list'];

        if (!checkACLForEachColInArr($display_columns, $full_table_list) ||
            !checkACLForEachColInArrForFilterDef($filters_def, $full_table_list) ||
            !checkACLForEachColInArr($group_defs, $full_table_list) ||
            !checkACLForEachColInArr($order_by, $full_table_list) ||
            !checkACLForEachColInArr($summary_columns, $full_table_list)) {
            sugar_die($mod_strings['LBL_NO_ACCESS']);
        }

        //Check for List view permissions
        $hashModules = [];
        foreach ($display_columns as $column) {
            $col_module = $full_table_list[$column['table_key']]['module'];
            $ACLenabled = false;
            if (!isset($hashModules[$col_module])) {
                $b = BeanFactory::newBean($col_module);
                // If the Module doesn't exist, just continue, and allow Report to show invalid field
                if (empty($b)) {
                    continue;
                }
                $ACLenabled = $b->bean_implements('ACL');
                $hashModules[$col_module] = !empty($b) ? $b->acltype : 'module';
            }
            $type = $hashModules[$col_module];
            //todo: check the last param of this call (is_owner)

            if ((
                $ACLenabled == true
                    &&
                    (
                        !ACLController::checkAccess($col_module, 'list', true, $type)
                        || !ACLController::checkAccess($col_module, 'view', true, $type)
                    )
            )
                && $col_module != 'Currencies'
                && $col_module != 'EmailAddresses'
                && $col_module != 'Users'
                && $col_module != 'Releases'
                && $col_module != 'Teams'
                && $col_module != 'CampaignLog'
                && $col_module != 'Manufacturers'
            ) {
                sugar_die($mod_strings['LBL_NO_ACCESS']);
            }
        }

        // Check Report module Permissions
        $is_owner = true;
        global $current_user;
        if (isset($args['reporter']->saved_report) && $args['reporter']->saved_report->assigned_user_id != $current_user->id) {
            $is_owner = false;
        }

        if ($reporter->saved_report) {
            if (!$reporter->saved_report->ACLAccess('view')) {
                sugar_die($mod_strings['LBL_NO_ACCESS']);
            }
        } elseif (!ACLController::checkAccess('Reports', 'view', $is_owner)) {
            sugar_die($mod_strings['LBL_NO_ACCESS']);
        }
    }
    return true;
}

function check_report_perms($report_id)
{
    global $current_user;
    $saved = BeanFactory::getBean('Reports', $report_id);
    if (!is_admin($current_user) && $saved->assigned_user_id != $current_user->id) {
        return false;
    }
    return true;
}

/////////////////////
/////////////////////
function control(&$args)
{
    global $current_user;
    global $mod_strings;
    $error_msg = '';

    // SAVE MAPPING IF REQUESTED
    if (isset($_REQUEST['save_report'])
        && $_REQUEST['save_report'] == 'on') {
        if (!empty($_REQUEST['record'])) {
            if (!check_report_perms($_REQUEST['record'])) {
                print $mod_strings['MSG_NO_PERMISSIONS'];
                return;
            }
        }

        $reporterName = InputValidation::getService()->getValidInputRequest('save_report_as');
        $args['save_result'] = $args['reporter']->save($reporterName);
        header('location: index.php?' .
            http_build_query(
                [
                    'module' => 'Reports',
                    'action' => 'index',
                    'page' => 'report',
                    'id' => $args['reporter']->saved_report->id,
                ]
            ));
        exit;
    }

    if (isset($_REQUEST['delete_report_id'])) {
        if (!check_report_perms($_REQUEST['delete_report_id'])) {
            print $mod_strings['MSG_NO_PERMISSIONS'];
            return;
        }

        BeanFactory::deleteBean('Reports', $_REQUEST['delete_report_id']);
    }

    if (isset($_REQUEST['publish'])) {
        if (!check_report_perms($_REQUEST['publish_report_id'])) {
            print $mod_strings['MSG_NO_PERMISSIONS'];
            return;
        }

        $saved_report = BeanFactory::newBean('Reports');
        $result = 0;

        $saved_report = $saved_report->retrieve($_REQUEST['publish_report_id'], false);

        if ($_REQUEST['publish'] == 'yes') {
            $result = $saved_report->mark_published('yes');
            if ($result == -1) {
                $error_msg = htmlspecialchars($mod_strings['MSG_UNABLE_PUBLISH_ANOTHER'], ENT_COMPAT);
            }
        } elseif ($_REQUEST['publish'] == 'no') {
            // if you don't own this importmap, you do now!
            // unless you have a map by the same name
            $result = $saved_report->mark_published('no');
            if ($result == -1) {
                $error_msg = htmlspecialchars($mod_strings['MSG_UNABLE_PUBLISH_YOU_OWN'], ENT_COMPAT);
            }
        }
        if (isset($error_msg)) {
            echo $error_msg;
        }
    }
}
