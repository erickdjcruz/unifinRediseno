<?php
Activity::disable();
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
require_once 'modules/Teams/TeamSetManager.php';
require_once 'modules/Users/reassign_user_records_functions.php';

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Dbal\Connection;

global $mod_strings, $app_strings, $current_user;
$mod_strings_users = $mod_strings;

if (!$current_user->isAdminForModule('Users')) {
    sugar_die($app_strings['EXCEPTION_NOT_AUTHORIZED']);
}

global $locale, $dictionary;

$db = DBManagerFactory::getInstance();

$return_module = InputValidation::getService()->getValidInputRequest('return_module', 'Assert\Mvc\ModuleName', '');
$return_action = InputValidation::getService()->getValidInputRequest('return_action', '');
$return_id = InputValidation::getService()->getValidInputRequest('return_id', 'Assert\Guid', '');
$fromuser = InputValidation::getService()->getValidInputRequest('fromuser', 'Assert\Guid');
$touser = InputValidation::getService()->getValidInputRequest('touser', 'Assert\Guid');
$record = InputValidation::getService()->getValidInputRequest('record', 'Assert\Guid');

if (!empty($return_module)) {
    $queryData = [
        'module' => $return_module,
        'action' => $return_action,
        'record' => $return_id,
    ];
    $cancel_location = 'index.php?' . http_build_query($queryData);
} else {
    $cancel_location = '';
}

?>
<h2 class="moduleTitle" style="margin-bottom:0;">
    <?= htmlspecialchars($mod_strings_users['LBL_REASS_SCRIPT_TITLE'], ENT_COMPAT); ?>
</h2>
<?php
// Include Metadata for processing
foreach (SugarAutoLoader::existingCustom('modules/Users/metadata/reassignScriptMetadata.php', 'modules/Users/reassignScriptMetadata_override.php') as $file) {
    include $file;
}

if (!empty($record)) {
    unset($_SESSION['reassignRecords']);
    $_SESSION['reassignRecords']['fromuser'] = $record;
}

if (empty($fromuser) && !isset($_GET['execute'])) :
    if (isset($_GET['clear']) && $_GET['clear'] == 'true') {
        unset($_SESSION['reassignRecords']);
    }
    ?>
    <form method=post action="index.php?module=Users&action=reassignUserRecords" name="EditView" id="EditView">
        <table cellspacing="1" cellpadding="1" border="0">
            <tr>
                <td>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_DESC_PART1'], ENT_COMPAT); ?>
                    <br/><br/>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" class="button"
                           value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_CONTINUE'], ENT_COMPAT); ?>"
                           name="steponesubmit"/>
                    &nbsp;<input type=button class="button"
                                 value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_CLEAR'], ENT_COMPAT); ?>"
                                 onclick="clearCurrentRecords();"/>
                    <input type="button" class="button"
                           value="<?= htmlspecialchars($app_strings['LBL_CANCEL_BUTTON_LABEL'], ENT_COMPAT); ?>"
                        <?php if (!empty($cancel_location)) : ?>
                           onclick="document.location=<?= htmlspecialchars(json_encode($cancel_location, JSON_HEX_TAG, JSON_HEX_QUOT), ENT_COMPAT); ?>"
                        <?php else : ?>
                           onclick='if(parent.SUGAR.App) parent.SUGAR.App.router.navigate("#Users", {trigger: true});'
                        <?php endif; ?>
                    >
                </td>
            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="edit view">
            <tr>
                <td>
                    <br>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_USER_FROM'], ENT_COMPAT); ?>
                    <br>
                    <select name="fromuser" id="fromuser">
                        <?php
                        $all_users = User::getAllUsers();
                        echo get_select_options_with_id($all_users, $_SESSION['reassignRecords']['fromuser'] ?? '');
    ?>
                    </select>
                    <br>
                    <br>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_USER_TO'], ENT_COMPAT); ?>
                    <br>
                    <select name="touser" id="touser">
                        <?php
                        if (isset($_SESSION['reassignRecords']['fromuser'])
                        && isset($all_users[$_SESSION['reassignRecords']['fromuser']])) {
                            unset($all_users[$_SESSION['reassignRecords']['fromuser']]);
                        }
                        echo get_select_options_with_id($all_users, $_SESSION['reassignRecords']['touser'] ?? '');
    ?>
                    </select>
                    <br>
                    <br>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_TEAM_TO'], ENT_COMPAT); ?>
                    <br>
                    <?php
                    $teamSetField = new SugarFieldTeamset('Teamset');
                    $lead = BeanFactory::newBean('Leads');
                    $teamSetField->initClassicView($lead->field_defs, 'EditView');
                    $sqs_objects = $teamSetField->getClassicViewQS();

                    echo $teamSetField->getClassicView();
    ?>
                    <br>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_MOD_REASSIGN'], ENT_COMPAT); ?>
                    <br>
                    <select size="6" name="modules[]" multiple="true" id="modulemultiselect"
                            onchange="updateDivDisplay(this);">
                        <?= getModuleMultiSelectOptions(); ?>
                    </select>
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    foreach ($moduleFilters as $modFilter => $fieldArray) :
                        $display = !empty($fieldArray['display_default']) ? 'block' : 'none';
                        //Leon bug 20739
                        $t_mod_strings = return_module_language($GLOBALS['current_language'], $modFilter);
                        ?>
                        <div id="reassign_<?= htmlspecialchars($modFilter, ENT_COMPAT); ?>"
                             style="display:<?= htmlspecialchars($display, ENT_COMPAT); ?>">
                            <h5 style="padding-left:0; margin-bottom:4px;">
                                <?= htmlspecialchars($app_list_strings['moduleList'][$modFilter], ENT_COMPAT); ?> <?= htmlspecialchars($mod_strings_users['LBL_REASS_FILTERS'], ENT_COMPAT); ?>
                            </h5>
                            <?php
                            foreach ($fieldArray['fields'] as $meta) :
                                $multi = '';
                                $name = !empty($meta['name']) ? $meta['name'] : '';
                                $sizeHtml = !empty($meta['size']) ? 'size="' . intval($meta['size']) . '"' : '';
                                //Leon bug 20739
                                ?>
                                <?= htmlspecialchars($t_mod_strings[$meta['vname']], ENT_COMPAT); ?><br>
                                <?php
                                $extraHtml = '';
                                switch ($meta['type']) {
                                    case 'text':
                                        $tag = 'input';
                                        break;
                                    case 'multiselect':
                                        $multi = 'multiple';
                                        $name .= '[]';
                                        // no break - Continue into select
                                    case 'select':
                                        $tag = 'select';
                                        $sel = '';
                                        if (!empty($_SESSION['reassignRecords']['filters'][$meta['name']])) {
                                            $sel = $_SESSION['reassignRecords']['filters'][$meta['name']];
                                        }
                                        $extraHtml = get_select_options_with_id($meta['dropdown'], $sel);
                                        $extraHtml .= "\n</select>";
                                        break;
                                    default:
                                        continue 2;
                                }
                                ?>
                                <<?= htmlspecialchars($tag, ENT_COMPAT); ?> <?= $sizeHtml; ?> name="<?= htmlspecialchars($name, ENT_COMPAT); ?>" <?= htmlspecialchars($multi, ENT_COMPAT); ?>>
                                <?= $extraHtml; ?>
                                <br>
<?php
                            endforeach;
                                        ?>
                                        </div>
                                                    <?php
                    endforeach;
    ?>
                </td>
            </tr>
        </table>
        <table cellspacing="1" cellpadding="1" border="0">
            <tr>
                <td>
                    <input type="submit" class="button"
                           value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_CONTINUE'], ENT_COMPAT); ?>"
                           name="steponesubmit">
                    <input type="button" class="button"
                           value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_CLEAR'], ENT_COMPAT); ?>"
                           onclick="clearCurrentRecords();">
                    <input type="button" class="button"
                           value="<?= htmlspecialchars($app_strings['LBL_CANCEL_BUTTON_LABEL'], ENT_COMPAT); ?>"
                        <?php if (!empty($cancel_location)) : ?>
                           onclick="document.location=<?= htmlspecialchars(json_encode($cancel_location, JSON_HEX_TAG, JSON_HEX_QUOT), ENT_COMPAT); ?>"
                        <?php else : ?>
                           onclick='if(parent.SUGAR.App) parent.SUGAR.App.router.navigate("#Users", {trigger: true});'
                        <?php endif; ?>
                    >
                </td>
            </tr>
        </table>
    </form>
<?php
else :
    if (empty($_GET['execute'])) :
        $modules = InputValidation::getService()
            ->getValidInputRequest(
                'modules',
                ['Assert\All' => ['constraints' => 'Assert\Mvc\ModuleName']]
            );

        if (empty($modules)) {
            sugar_die($mod_strings_users['ERR_REASS_SELECT_MODULE']);
        }

        if ($fromuser == $touser) {
            sugar_die($mod_strings_users['ERR_REASS_DIFF_USERS']);
        }

        global $current_user;
        // Set the from and to user names so that we can display them in the results
        $result = $db->getConnection()
            ->executeQuery(
                'SELECT user_name, id FROM users WHERE id IN (?)',
                [[$fromuser, $touser]],
                [Connection::PARAM_STR_ARRAY]
            );

        foreach ($result->iterateAssociative() as $row) {
            if ($row['id'] == $fromuser) {
                $fromusername = $row['user_name'];
            }
            if ($row['id'] == $touser) {
                $tousername = $row['user_name'];
            }
        }

        //rrs bug: 31056 - instead of setting the team_id let's set the team_set_id and set the team_id as the primary
        $sugarFieldTeamSet = new SugarFieldTeamset('Teamset');
        $teams = $sugarFieldTeamSet->getTeamsFromRequest('team_name');
        $team_ids = array_keys($teams);
        $team_id = SugarFieldTeamset::getPrimaryTeamIdFromRequest('team_name', $_REQUEST);
        $teamSet = BeanFactory::newBean('TeamSets');
        $team_set_id = $teamSet->addTeams($team_ids);


        $toteamname = TeamSetManager::getCommaDelimitedTeams($team_set_id, $team_id, true);
        ?>
        <?= htmlspecialchars($mod_strings_users['LBL_REASS_DESC_PART2'], ENT_COMPAT); ?>
        <form action="index.php?module=Users&action=reassignUserRecords&execute=true" method="post">
            <br><?= htmlspecialchars($mod_strings_users['LBL_REASS_NOTES_TITLE'], ENT_COMPAT); ?>
            <ul>
                <li><?= htmlspecialchars($mod_strings_users['LBL_REASS_NOTES_ONE'], ENT_COMPAT); ?></li>
                <li><?= htmlspecialchars($mod_strings_users['LBL_REASS_NOTES_TWO'], ENT_COMPAT); ?></li>
                <li><?= htmlspecialchars($mod_strings_users['LBL_REASS_NOTES_THREE'], ENT_COMPAT); ?></li>
            </ul>
            <?php
            require_once 'include/SugarSmarty/plugins/function.sugar_help.php';
            $sugar_smarty = new Sugar_Smarty();
            $help_img = smarty_function_sugar_help(['text' => $mod_strings['LBL_REASS_VERBOSE_HELP']], $sugar_smarty);
        ?>
            <br>
            <input type="checkbox" name="verbose">
            <?= htmlspecialchars($mod_strings_users['LBL_REASS_VERBOSE_OUTPUT'], ENT_COMPAT); ?>
            <?= $help_img; ?>
            <br>
            <?php
            unset($_SESSION['reassignRecords']['modules']);
            unset($_SESSION['reassignRecords']['POST']);
            $_SESSION['reassignRecords']['POST'] = $_POST;

            $beanListFlip = $_SESSION['reassignRecords']['assignedModuleListCache'];

            $_SESSION['reassignRecords']['toteam'] = $team_id;
            $_SESSION['reassignRecords']['toteamsetid'] = $team_set_id;
            $_SESSION['reassignRecords']['toteamname'] = $toteamname;
            $_SESSION['reassignRecords']['fromuser'] = $fromuser;
            $_SESSION['reassignRecords']['touser'] = $touser;
            $_SESSION['reassignRecords']['fromusername'] = $fromusername;
            $_SESSION['reassignRecords']['tousername'] = $tousername;

            foreach ($modules as $module) :
                if ($beanListFlip instanceof \ArrayObject) {
                    $beanListFlip = $beanListFlip->getArrayCopy();
                }
                if (empty($beanListFlip) || !is_array($beanListFlip) || !array_key_exists($module, $beanListFlip)) {
                    continue;
                }

                $object = BeanFactory::newBean($module);

                if (empty($object->table_name)) {
                    continue;
                }

                $moduleLabel = $app_list_strings['moduleList'][$module] ?? $module;
                ?>
                <h5>
                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_ASSESSING'], ENT_COMPAT); ?> <?= htmlspecialchars($moduleLabel, ENT_COMPAT); ?>
                </h5>
                <table border="0" cellspacing="0" cellpadding="0" class="detail view">
                    <tr>
                        <td>
                            <?php
                            [$q_tables, $q_where] = processConditions($object, $fromuser, $moduleFilters, $module, $_POST);
                            $_SESSION['reassignRecords']['modules']['list'][] = $module;
                            $count = $db->getOne("SELECT COUNT(*) AS count FROM $q_tables $q_where");
                    ?>
                            <?= $count; ?>
                            <?= htmlspecialchars($mod_strings_users['LBL_REASS_RECORDS_FROM'], ENT_COMPAT); ?>
                            <?= htmlspecialchars($moduleLabel, ENT_COMPAT); ?>
                            <?= htmlspecialchars($mod_strings_users['LBL_REASS_WILL_BE_UPDATED'], ENT_COMPAT); ?>
                            <br>
                            <input type="checkbox" name="<?= htmlspecialchars($module, ENT_COMPAT); ?>_workflow">
                            <?= htmlspecialchars($mod_strings_users['LBL_REASS_WORK_NOTIF_AUDIT'], ENT_COMPAT); ?>
                            <br>
                        </td>
                    </tr>
                </table>
                <?php
            endforeach;
        ?>
            <br><input type="button" class="button"
                       value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_GO_BACK'], ENT_COMPAT); ?>"
                       onclick="document.location='index.php?module=Users&action=reassignUserRecords';">
            &nbsp;<input type="submit" class="button"
                         value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_CONTINUE'], ENT_COMPAT); ?>">
            &nbsp;<input type="button" class="button"
                         value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_RESTART'], ENT_COMPAT); ?>"
                         onclick="document.location='index.php?module=Users&action=reassignUserRecords&clear=true';">
        </form>
    <?php
    else :
        $fromuser = $_SESSION['reassignRecords']['fromuser'];
        $touser = $_SESSION['reassignRecords']['touser'];
        $fromusername = $_SESSION['reassignRecords']['fromusername'];
        $tousername = $_SESSION['reassignRecords']['tousername'];
        $toteam = $_SESSION['reassignRecords']['toteam'];
        $toteamsetid = $_SESSION['reassignRecords']['toteamsetid'];
        $toteamname = $_SESSION['reassignRecords']['toteamname'];

        $sugarFieldTeamSet = new SugarFieldTeamset('Teamset');
        $teams = $sugarFieldTeamSet->getTeamsFromRequest('team_name');
        $team_ids = array_keys($teams);
        $team_id = SugarFieldTeamset::getPrimaryTeamIdFromRequest('team_name', $_REQUEST);
        $teamSet = BeanFactory::newBean('TeamSets');
        $team_set_id = $teamSet->addTeams($team_ids);

        $POST = $_SESSION['reassignRecords']['POST'];

        $teamSetSelectedId = null;

        $tbaConfigurator = new TeamBasedACLConfigurator();
        if ($tbaConfigurator->isEnabledGlobally()) {
            $selectedIds = $sugarFieldTeamSet->getSelectedTeamIdsFromRequest('team_name', $_REQUEST);
            if (!empty($selectedIds)) {
                $teamSetSelectedId = $teamSet->addTeams($selectedIds);
            }
        }

        foreach ($_SESSION['reassignRecords']['modules']['list'] as $module) :
            $object = BeanFactory::newBean($module);

            if (empty($object->table_name)) {
                continue;
            }

            $moduleLabel = $app_list_strings['moduleList'][$module] ?? $module;

            $workflow = !empty($_POST[$module . '_workflow']);
            ?>
            <h5>
                <?= htmlspecialchars($mod_strings_users['LBL_PROCESSING'], ENT_COMPAT); ?> <?= htmlspecialchars($moduleLabel, ENT_COMPAT); ?>
            </h5>
            <?php

            $q_set = ' SET assigned_user_id = ' . $db->quoted($touser) . ', ' .
                "date_modified = '" . TimeDate::getInstance()->nowDb() . "'";

            //@todo  $obj is defined inside function getModuleMultiSelectOptions()
            if (isset($object->field_defs['modified_user_id'])) {
                $q_set .= ', modified_user_id = ' . $db->quoted($current_user->id);
            }

            //make sure team_id and team_set_id columns are available
            if (!empty($team_id) && isset($object->field_defs['team_id'])) {
                $q_set .= sprintf(
                    ', team_id = %s, team_set_id = %s ',
                    $db->quoted($team_id),
                    $db->quoted($team_set_id)
                );
            }
            if (!empty($teamSetSelectedId) && $tbaConfigurator->isEnabledForModule($module)) {
                $q_set .= ', acl_team_set_id = ' . $db->quoted($teamSetSelectedId);
            }
            [$q_tables, $q_where] = processConditions($object, $fromuser, $moduleFilters, $module, $POST->getArrayCopy());
            // nutmeg sfa-219 : Fix reassignment of records when user set to Inactive
            if ($module == 'ForecastWorksheets') {
                $affected_rows = ForecastWorksheet::reassignForecast($fromuser, $touser);
                printf(
                    '%s: %s %s<br>',
                    htmlspecialchars($mod_strings_users['LBL_UPDATE_FINISH'], ENT_COMPAT),
                    htmlspecialchars($affected_rows, ENT_COMPAT),
                    htmlspecialchars($mod_strings_users['LBL_AFFECTED'], ENT_COMPAT)
                );
                continue;
            } else {
                if ($workflow) {
                    $query = "SELECT id FROM $q_tables $q_where";
                } else {
                    $query = "UPDATE $q_tables $q_set $q_where";
                }

                $res = $db->query($query, true);
                $affected_rows = $db->getAffectedRowCount($res);
            }
            ?>
            <table border="0" cellspacing="0" cellpadding="0" class="detail view">
                <tr>
                    <td>
                        <?php
                        if (!$workflow) :
                            printf(
                                '%s: %s %s<br>',
                                htmlspecialchars($mod_strings_users['LBL_UPDATE_FINISH'], ENT_COMPAT),
                                htmlspecialchars($affected_rows, ENT_COMPAT),
                                htmlspecialchars($mod_strings_users['LBL_AFFECTED'], ENT_COMPAT)
                            );
                        else :
                            $succeed = [];
                            $failed = [];

                            while ($row = $db->fetchByAssoc($res)) {
                                if (empty($row['id'])) {
                                    continue;
                                }
                                $bean = BeanFactory::getBean($module, $row['id']);

                                // So that we don't create new blank records.
                                if (empty($bean->id)) {
                                    continue;
                                }

                                $bean->assigned_user_id = $touser;

                                if ($toteam != '0') {
                                    $bean->team_id = $toteam;
                                }
                                if ($toteamsetid != '0') {
                                    $bean->team_set_id = $toteamsetid;
                                }
                                $linkname = "record with id {$bean->id}";
                                if (!empty($bean->name)) {
                                    $linkname = $bean->name;
                                } else {
                                    if (!empty($bean->last_name)) {
                                        $linkname = $locale->formatName($bean);
                                    } else {
                                        if (!empty($bean->document_name)) {
                                            $linkname = $bean->document_name;
                                        }
                                    }
                                }
                                if ($bean->save()) {
                                    $href = 'index.php?' . http_build_query([
                                            'module' => $bean->module_dir,
                                            'action' => 'DetailView',
                                            'record' => $bean->id,
                                        ]);
                                    [
                                        $successLabel,
                                        $objectNameHtml,
                                        $hrefAttr,
                                        $linkNameHtml,
                                        $fromLabelHtml,
                                        $fromNameHtml,
                                        $toLabelHtml,
                                        $toNameHtml,
                                    ] = array_map(
                                        function (string $item): string {
                                            return htmlspecialchars($item, ENT_COMPAT);
                                        },
                                        [
                                            $mod_strings_users['LBL_REASS_SUCCESS_ASSIGN'],
                                            $bean->object_name,
                                            $href,
                                            $linkname,
                                            $mod_strings_users['LBL_REASS_FROM'],
                                            $fromusername,
                                            $mod_strings_users['LBL_REASS_TO'],
                                            $tousername,
                                        ]
                                    );
                                    $successMessage = <<<HTML
$successLabel $objectNameHtml"<i><a href="{$hrefAttr}">{$linkNameHtml}</a></i>" 
$fromLabelHtml $fromNameHtml $toLabelHtml $toNameHtml
HTML;
                                    $successMessage .= $toteam != '0' ? sprintf(', %s %s.', htmlspecialchars($mod_strings_users['LBL_REASS_TEAM_SET_TO'], ENT_COMPAT), htmlspecialchars($toteamname, ENT_COMPAT)) : '.';
                                    $succeed[] = $successMessage;
                                } else {
                                    $href = 'index.php?'
                                        . http_build_query(
                                            [
                                                'module' => $bean->module_dir,
                                                'action' => 'DetailView',
                                                'record' => $bean->id,
                                            ]
                                        );
                                    $failed[] = sprintf(
                                        '%s "<i><a href="%s">%s</a></i>".',
                                        htmlspecialchars($mod_strings_users['LBL_REASS_FAILED_SAVE'], ENT_COMPAT),
                                        htmlspecialchars($href, ENT_COMPAT),
                                        htmlspecialchars($linkname, ENT_COMPAT)
                                    );
                                }
                            }

                            if (isset($_POST['verbose']) && $_POST['verbose'] == 'on') {
                                ?>
                                <h5>
                                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_THE_FOLLOWING'], ENT_COMPAT); ?>
                                    <?= htmlspecialchars($app_list_strings['moduleList'][$module], ENT_COMPAT); ?>
                                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_HAVE_BEEN_UPDATED'], ENT_COMPAT); ?>
                                </h5>
                                <?php

                                foreach ($succeed as $ord) {
                                    echo $ord . '<br>';
                                }

                                if (empty($succeed)) {
                                    echo htmlspecialchars($mod_strings_users['LBL_REASS_NONE'], ENT_COMPAT) . '<br>';
                                }

                                ?>
                                <h5>
                                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_THE_FOLLOWING'], ENT_COMPAT); ?>
                                    <?= htmlspecialchars($app_list_strings['moduleList'][$module], ENT_COMPAT); ?>
                                    <?= htmlspecialchars($mod_strings_users['LBL_REASS_CANNOT_PROCESS'], ENT_COMPAT); ?>
                                </h5>
                                <?php
                                foreach ($failed as $failure) {
                                    echo $failure . '<br>';
                                }

                                if (empty($failed)) {
                                    echo htmlspecialchars($mod_strings_users['LBL_REASS_NONE'], ENT_COMPAT) . '<br>';
                                }
                            } else {
                                echo htmlspecialchars($mod_strings_users['LBL_REASS_UPDATE_COMPLETE'], ENT_COMPAT) . '<br>';
                                echo '&nbsp;&nbsp;' . safeCount($succeed) . ' ' . htmlspecialchars($mod_strings_users['LBL_REASS_SUCCESSFUL'], ENT_COMPAT) . '<br>';
                                echo '&nbsp;&nbsp;' . safeCount($failed) . ' ' . htmlspecialchars($mod_strings_users['LBL_REASS_FAILED'], ENT_COMPAT);
                            }
                            ?>
                            <br>
                        <?php
                        endif;
            ?>
                    </td>
                </tr>
            </table>
        <?php
        endforeach;
        Activity::restoreToPreviousState();
        ?>
        <br>
        <input type="button" class="button"
               value="<?= htmlspecialchars($mod_strings_users['LBL_REASS_BUTTON_RETURN'], ENT_COMPAT); ?>"
               onclick="document.location='index.php?module=Users&action=reassignUserRecords'">
    <?php
    endif;
endif;
if (!empty($sqs_objects)) {
    //rrs - bug: 31056 - move to end to allow for form field to render
    $json = JSON::encode($sqs_objects);
    $script = <<<SCRIPT
<script type="text/javascript" language="javascript">
sqs_objects = $json ;
</script>
SCRIPT;
    echo $script;
}
?>
<script type="text/javascript">

    function clearCurrentRecords() {
        var callback = {
            success: function () {
                document.getElementById('fromuser').selectedIndex = 0;
                document.getElementById('touser').selectedIndex = 0;
                document.getElementById('modulemultiselect').selectedIndex = -1;
                updateDivDisplay(document.getElementById('modulemultiselect'));
            }
        };

        YAHOO.util.Connect.asyncRequest('POST', 'index.php?module=Users&action=clearreassignrecords&to_pdf=1', callback, null);
    }

    var allselected = [];

    function updateDivDisplay(multiSelectObj) {
        for (var i = 0; i < multiSelectObj.options.length; i++) {
            if (multiSelectObj.options[i].selected != allselected[i]) {
                allselected[i] = multiSelectObj.options[i].selected;

                if (allselected[i]) {
                    theElement = document.getElementById('reassign_' + multiSelectObj.options[i].value);
                    if (theElement != null) {
                        theElement.style.display = 'block';
                    }
                } else {
                    theElement = document.getElementById('reassign_' + multiSelectObj.options[i].value);
                    if (theElement != null) {
                        theElement.style.display = 'none';
                    }
                }
            }
        }
    }
    <?php if (empty($fromuser) && !isset($_GET['execute'])) :?>
    updateDivDisplay(document.getElementById('modulemultiselect'));
    <?php endif;?>
</script>
