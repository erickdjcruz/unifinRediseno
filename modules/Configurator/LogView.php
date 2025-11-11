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

global $mod_strings;
if (!is_admin($current_user)) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}
$filter = '';
if (!empty($_REQUEST['filter'])) {
    $filter = $_REQUEST['filter'];
}
$ignore_self = false;
if (!empty($_REQUEST['ignore_self'])) {
    $ignore_self = 'checked';
}
$reg_ex = false;
if (!empty($_REQUEST['reg_ex'])) {
    $reg_ex = 'checked';
}
set_time_limit(180);
?>
    <form action="index.php" name="logview">
        <input type="hidden" name="action" value="LogView">
        <input type="hidden" name="module" value="Configurator">
        <input type="hidden" name="doaction" value="">
        <input type="button" onclick="document.logview.doaction.value='all';document.logview.submit()" name="all"
               value="<?= htmlspecialchars($mod_strings['LBL_ALL'], ENT_COMPAT) ?>">
        <input type="button" onclick="document.logview.doaction.value='mark';document.logview.submit()" name="mark"
               value="<?= htmlspecialchars($mod_strings['LBL_MARK_POINT'], ENT_COMPAT) ?>">
        <input type="submit" name="display"
               value="<?= htmlspecialchars($mod_strings['LBL_REFRESH_FROM_MARK'], ENT_COMPAT) ?>">
        <input type="button" onclick="document.logview.doaction.value='next';document.logview.submit()" name="next"
               value="<?= htmlspecialchars($mod_strings['LBL_NEXT_'], ENT_COMPAT) ?>">
        <br><br>
        <?= htmlspecialchars($mod_strings['LBL_SEARCH'], ENT_COMPAT) ?><input type="text" name="filter"
                                                                              value="<?= htmlspecialchars($filter, ENT_COMPAT) ?>">&nbsp;
        <?php //@codingStandardsIgnoreStart?>
        <?= htmlspecialchars($mod_strings['LBL_REG_EXP'], ENT_COMPAT) ?><input type="checkbox"
                                                                               name="reg_ex"<?php if ($reg_ex) : ?> checked="checked"<?php endif; ?>>
        <br>
        <?= htmlspecialchars($mod_strings['LBL_IGNORE_SELF'], ENT_COMPAT) ?> <input type="checkbox"
                                                                                    name="ignore_self"<?php if ($ignore_self) : ?>
                                                                                    checked="checked"><?php endif; ?>>
        <?php //@codingStandardsIgnoreEnd?>
    </form>
<?php
define('PROCESS_ID', 1);
define('LOG_LEVEL', 2);
define('LOG_NAME', 3);
define('LOG_DATA', 4);

// bug 53041 - now that we are respecting file name suffixes for log files, we need to get the log file name properly
$config = SugarConfig::getInstance();
$logfile = $config->get('logger.file.name');
$log_dir = $config->get('log_dir');
$log_dir = $log_dir . (empty($log_dir) ? '' : '/');
$file_suffix = $config->get('logger.file.suffix');
$date_suffix = '';
if (!empty($file_suffix)) {
    $date_suffix = '_' . date(str_replace('%', '', $file_suffix));
}

$logFile = $log_dir . $logfile . $date_suffix . '.log';

if (!file_exists($logFile)) {
    die('No Log File');
}
$lastMatch = false;
$doaction = (!empty($_REQUEST['doaction'])) ? $_REQUEST['doaction'] : '';

switch ($doaction) {
    case 'mark':
        echo '<h3>' . htmlspecialchars($mod_strings['LBL_MARKING_WHERE_START_LOGGING'], ENT_COMPAT) . '</h3><br>';
        $_SESSION['log_file_size'] = filesize($logFile);
        break;
    case 'next':
        if (!empty($_SESSION['last_log_file_size'])) {
            $_SESSION['log_file_size'] = $_SESSION['last_log_file_size'];
        } else {
            $_SESSION['log_file_size'] = 0;
        }
        $_REQUEST['display'] = true;
        break;
    case 'all':
        $_SESSION['log_file_size'] = 0;
        $_REQUEST['display'] = true;
        break;
}


if (!empty($_REQUEST['display'])) {
    echo '<h3>' . htmlspecialchars($mod_strings['LBL_DISPLAYING_LOG'], ENT_COMPAT) . '</h3>';
    $process_id = getmypid();

    echo htmlspecialchars($mod_strings['LBL_YOUR_PROCESS_ID'] . ' [' . $process_id . ']', ENT_COMPAT);
    echo '<br>' . htmlspecialchars($mod_strings['LBL_YOUR_IP_ADDRESS'] . ' ' . $_SERVER['REMOTE_ADDR'], ENT_COMPAT);
    if ($ignore_self) {
        echo htmlspecialchars($mod_strings['LBL_IT_WILL_BE_IGNORED'], ENT_COMPAT);
    }
    if (empty($_SESSION['log_file_size'])) {
        $_SESSION['log_file_size'] = 0;
    }
    $cur_size = filesize($logFile);
    $_SESSION['last_log_file_size'] = $cur_size;
    $pos = 0;
    if ($cur_size >= $_SESSION['log_file_size']) {
        $pos = $_SESSION['log_file_size'] - $cur_size;
    }
    if ($_SESSION['log_file_size'] == $cur_size) {
        echo '<br>' . htmlspecialchars($mod_strings['LBL_LOG_NOT_CHANGED'], ENT_COMPAT) . '<br>';
    } else {
        $fp = sugar_fopen($logFile, 'r');
        fseek($fp, $pos, SEEK_END);
        echo '<pre>';
        while ($line = fgets($fp)) {
            $line = filter_var($line, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //preg_match('/[^\]]*\[([0-9]*)\] ([a-zA-Z]+) ([a-zA-Z0-9\.]+) - (.*)/', $line, $result);
            preg_match('/[^\]]*\[([0-9]*)\]/', substr($line, 0, 80), $result);
            ob_flush();
            flush();
            if (empty($result) && $lastMatch) {
                echo $line;
            } else {
                $lastMatch = false;
                if (empty($result) || ($ignore_self && $result[LOG_NAME] == $_SERVER['REMOTE_ADDR'])) {
                } else {
                    if (empty($filter) || (!$reg_ex && substr_count($line, $filter) > 0) || ($reg_ex && preg_match($filter, $line))) {
                        $lastMatch = true;
                        echo $line;
                    }
                }
            }
        }
        echo '</pre>';
        fclose($fp);
    }
}
