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

use Sugarcrm\Sugarcrm\Util\Files\FileLoader;

/**
 * Implodes some parts of version with specified delimiter, beta & rc parts are removed all time
 *
 * @param string $version like 6, 6.2, 6.5.0beta1, 6.6.0rc1, 6.5.7 (separated by dot)
 * @param int $size number of the first parts of version which are requested
 * @param string $lastSymbol replace last part of version by some string
 * @param string $delimiter delimiter for result
 * @return string
 * @example ('6.5.6') returns 656
 * @example ('6.5.6beta2') returns 656
 * @example ('6.5.6rc3') returns 656
 * @example ('6.6.0.1') returns 6601
 * @example ('6.5.6', 3, 'x') returns 65x
 * @example ('6', 3, '', '.') returns 6.0.0
 *
 */
function implodeVersion($version, $size = 0, $lastSymbol = '', $delimiter = '')
{
    preg_match('/^\d+(\.\d+)*/', $version, $parsedVersion);
    if (empty($parsedVersion)) {
        return '';
    }

    $parsedVersion = $parsedVersion[0];
    $parsedVersion = explode('.', $parsedVersion);

    if ($size == 0) {
        $size = count($parsedVersion);
    }

    $parsedVersion = array_pad($parsedVersion, $size, 0);
    $parsedVersion = array_slice($parsedVersion, 0, $size);
    if ($lastSymbol !== '') {
        array_pop($parsedVersion);
        array_push($parsedVersion, $lastSymbol);
    }

    return implode($delimiter, $parsedVersion);
}

/**
 * local version of safeInArray
 * @param $needle
 * @param $haystack
 * @param bool $strict
 * @return bool
 */
function safeInArrayForCli($needle, $haystack, bool $strict = false) : bool
{
    if (!is_array($haystack)) {
        return false;
    }
    return in_array($needle, $haystack, $strict);
}

// ------------------------------------------------------------


/**
 * Array of all Modules in the version bein upgraded
 * This method returns an Array of all modules
 * @return $modules Array of modules.
 */
function getAllModules()
{
    $modules = [];
    $d = dir('modules');
    while ($e = $d->read()) {
        if (substr($e, 0, 1) == '.' || !is_dir('modules/' . $e)) {
            continue;
        }
        $modules[] = $e;
    }
    return $modules;
}


function deleteCache()
{
    //Clean modules from cache
    $cachedir = sugar_cached('modules');
    if (is_dir($cachedir)) {
        $allModFiles = [];
        $allModFiles = findAllFiles($cachedir, $allModFiles, true);
        foreach ($allModFiles as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir_recursive($file);
                } else {
                    unlink($file);
                }
            }
        }
    }

    //Clean jsLanguage from cache
    $cachedir = sugar_cached('jsLanguage');
    if (is_dir($cachedir)) {
        $allModFiles = [];
        $allModFiles = findAllFiles($cachedir, $allModFiles);
        foreach ($allModFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    //Clean smarty from cache
    $cachedir = sugar_cached('smarty');
    if (is_dir($cachedir)) {
        $allModFiles = [];
        $allModFiles = findAllFiles($cachedir, $allModFiles);
        foreach ($allModFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}

function deleteChance()
{
    //Clean folder from cache
    if (is_dir('include/SugarObjects/templates/chance')) {
        rmdir_recursive('include/SugarObjects/templates/chance');
    }
    if (is_dir('include/SugarObjects/templates/chance')) {
        if (!isset($_SESSION['chance'])) {
            $_SESSION['chance'] = '';
        }
        $_SESSION['chance'] = 'include/SugarObjects/templates/chance';
    }
}

/**
 * finalizes upgrade by setting upgrade versions in DB (config table) and sugar_version.php
 * @return bool true on success
 */
function updateVersions($version)
{
    global $db;
    global $path;

    logThis('At updateVersions()... updating config table and sugar_version.php.', $path);

    // handle file copy
    if (isset($_SESSION['sugar_version_file']) && !empty($_SESSION['sugar_version_file'])) {
        if (!copy($_SESSION['sugar_version_file'], clean_path(getcwd() . '/sugar_version.php'))) {
            logThis('*** ERROR: sugar_version.php could not be copied to destination! Cannot complete upgrade', $path);
            return false;
        } else {
            logThis('sugar_version.php successfully updated!', $path);
        }
    } else {
        logThis('*** ERROR: no sugar_version.php file location found! - cannot complete upgrade...', $path);
        return false;
    }

    $q1 = "DELETE FROM config WHERE category = 'info' AND name = 'sugar_version'";
    $q2 = "INSERT INTO config (category, name, value) VALUES ('info', 'sugar_version', '{$version}')";

    logThis('Deleting old DB version info from config table.', $path);
    $db->query($q1);

    logThis('Inserting updated version info into config table.', $path);
    $db->query($q2);

    logThis('updateVersions() complete.', $path);
    return true;
}


/**
 * gets a module's lang pack - does not need to be a SugarModule
 * @param lang string Language
 * @param module string Path to language folder
 * @return array mod_strings
 */
function getModuleLanguagePack($lang, $module)
{
    $mod_strings = [];

    if (!empty($lang) && !empty($module)) {
        $langPack = clean_path(getcwd() . '/' . $module . '/language/' . $lang . '.lang.php');
        $langPackEn = clean_path(getcwd() . '/' . $module . '/language/en_us.lang.php');

        if (file_exists($langPack)) {
            include FileLoader::validateFilePath($langPack);
        } elseif (file_exists($langPackEn)) {
            include $langPackEn;
        }
    }

    return $mod_strings;
}

/**
 * checks system compliance for 4.5+ codebase
 * @return array Mixed values
 */
function checkSystemCompliance()
{
    global $sugar_config;
    global $current_language;
    global $db;
    global $mod_strings;
    global $app_strings;

    if (!defined('SUGARCRM_MIN_MEM')) {
        define('SUGARCRM_MIN_MEM', 40);
    }

    $installer_mod_strings = getModuleLanguagePack($current_language, './install');
    $ret = [];
    $ret['error_found'] = false;

    require_once 'include/utils.php';
    // PHP version
    $php_version = constant('PHP_VERSION');
    $check_php_version_result = check_php_version();

    switch ($check_php_version_result) {
        case -1:
            $ret['phpVersion'] = "<b><span class=stop>{$installer_mod_strings['ERR_CHECKSYS_PHP_INVALID_VER']} {$php_version} )</span></b>";
            $ret['error_found'] = true;
            break;
        case 1:
            $ret['phpVersion'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_PHP_OK']} {$php_version} )</span></b>";
            break;
    }

    // database and connect
    $canInstall = $db->canInstall();
    if ($canInstall !== true) {
        $ret['error_found'] = true;
        if (safeCount($canInstall) == 1) {
            $ret['dbVersion'] = '<b><span class=stop>' . $installer_mod_strings[$canInstall[0]] . '</span></b>';
        } else {
            $ret['dbVersion'] = '<b><span class=stop>' . sprintf($installer_mod_strings[$canInstall[0]], $canInstall[1]) . '</span></b>';
        }
    }

    // XML Parsing
    if (function_exists('xml_parser_create')) {
        $ret['xmlStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    } else {
        $ret['xmlStatus'] = "<b><span class=stop>{$installer_mod_strings['LBL_CHECKSYS_NOT_AVAILABLE']}</span></b>";
        $ret['error_found'] = true;
    }

    // cURL
    if (function_exists('curl_init')) {
        $ret['curlStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    } else {
        $ret['curlStatus'] = "<b><span class=go>{$installer_mod_strings['ERR_CHECKSYS_CURL']}</span></b>";
        $ret['error_found'] = false;
    }

    // mbstrings
    if (function_exists('mb_strlen')) {
        $ret['mbstringStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    } else {
        $ret['mbstringStatus'] = "<b><span class=stop>{$installer_mod_strings['ERR_CHECKSYS_MBSTRING']}</span></b>";
        $ret['error_found'] = true;
    }

    // imap
    if (extension_loaded('imap')) {
        $ret['imapStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    } else {
        $ret['imapStatus'] = "<b><span class=go>{$installer_mod_strings['ERR_CHECKSYS_IMAP']}</span></b>";
        $ret['error_found'] = false;
    }


    // safe mode
    if ('1' == ini_get('safe_mode')) {
        $ret['safeModeStatus'] = "<b><span class=stop>{$installer_mod_strings['ERR_CHECKSYS_SAFE_MODE']}</span></b>";
        $ret['error_found'] = true;
    } else {
        $ret['safeModeStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    }


    // call time pass by ref
    if ('1' == ini_get('allow_call_time_pass_reference')) {
        $ret['callTimeStatus'] = "<b><span class=stop>{$installer_mod_strings['ERR_CHECKSYS_CALL_TIME']}</span></b>";
        //continue upgrading
    } else {
        $ret['callTimeStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    }

    // memory limit
    $ret['memory_msg'] = '';
    $memory_limit = '-1';
    $sugarMinMem = constant('SUGARCRM_MIN_MEM');
    // logic based on: http://us2.php.net/manual/en/ini.core.php#ini.memory-limit
    if ($memory_limit == '') {          // memory_limit disabled at compile time, no memory limit
        $ret['memory_msg'] = "<b><span class=\"go\">{$installer_mod_strings['LBL_CHECKSYS_MEM_OK']}</span></b>";
    } elseif ($memory_limit == '-1') {   // memory_limit enabled, but set to unlimited
        $ret['memory_msg'] = "<b><span class=\"go\">{$installer_mod_strings['LBL_CHECKSYS_MEM_UNLIMITED']}</span></b>";
    } else {
        rtrim($memory_limit, 'M');
        $memory_limit_int = (int)$memory_limit;
        if ($memory_limit_int < constant('SUGARCRM_MIN_MEM')) {
            $ret['memory_msg'] = "<b><span class=\"stop\">{$installer_mod_strings['ERR_CHECKSYS_MEM_LIMIT_1']}" . constant('SUGARCRM_MIN_MEM') . "{$installer_mod_strings['ERR_CHECKSYS_MEM_LIMIT_2']}</span></b>";
            $ret['error_found'] = true;
        } else {
            $ret['memory_msg'] = "<b><span class=\"go\">{$installer_mod_strings['LBL_CHECKSYS_OK']} ({$memory_limit})</span></b>";
        }
    }
    // zip support
    if (!class_exists('ZipArchive')) {
        $ret['ZipStatus'] = "<b><span class=stop>{$installer_mod_strings['ERR_CHECKSYS_ZIP']}</span></b>";
        $ret['error_found'] = true;
    } else {
        $ret['ZipStatus'] = "<b><span class=go>{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    }


    // Suhosin allow to use upload://
    $ret['stream_msg'] = '';
    if (UploadStream::getSuhosinStatus() == true) {
        $ret['stream_msg'] = "<b><span class=\"go\">{$installer_mod_strings['LBL_CHECKSYS_OK']}</span></b>";
    } else {
        $ret['stream_msg'] = "<b><span class=\"stop\">{$app_strings['ERR_SUHOSIN']}</span></b>";
        $ret['error_found'] = true;
    }

    // mbstring.func_overload
    $ret['mbstring.func_overload'] = '';
    $mb = ini_get('mbstring.func_overload');

    if ($mb > 1) {
        $ret['mbstring.func_overload'] = "<b><span class=\"stop\">{$mod_strings['ERR_UW_MBSTRING_FUNC_OVERLOAD']}</b>";
        $ret['error_found'] = true;
    }
    return $ret;
}


/**
 * is a file that we blow away automagically
 */
function isAutoOverwriteFile($file)
{
    $overwriteDirs = [
        './sugar_version.php',
        './modules/UpgradeWizard/uw_main.tpl',
    ];
    $file = trim('.' . str_replace(clean_path(getcwd()), '', $file));

    if (in_array($file, $overwriteDirs)) {
        return true;
    }

    $fileExtension = substr(strrchr($file, '.'), 1);
    if ($fileExtension == 'tpl' || $fileExtension == 'html') {
        return false;
    }

    return true;
}

/**
 * flatfile logger
 */
function logThis($entry, $path = '')
{
    global $mod_strings;
    if (file_exists('include/utils/sugar_file_utils.php')) {
        require_once 'include/utils/sugar_file_utils.php';
    }
    $log = empty($path) ? 'upgradeWizard.log' : $path;

    // create if not exists
    $fp = @fopen($log, 'a+');
    if (!is_resource($fp)) {
        $GLOBALS['log']->fatal('UpgradeWizard could not open/lock upgradeWizard.log file');
        die($mod_strings['ERR_UW_LOG_FILE_UNWRITABLE']);
    }

    $line = date('r') . ' [UpgradeWizard] - ' . $entry . "\n";

    if (@fwrite($fp, $line) === false) {
        $GLOBALS['log']->fatal('UpgradeWizard could not write to upgradeWizard.log: ' . $entry);
        die($mod_strings['ERR_UW_LOG_FILE_UNWRITABLE']);
    }

    if (is_resource($fp)) {
        fclose($fp);
    }
}


/**
 * test perms for CREATE queries
 */
function testPermsCreate($db, $out)
{
    logThis('Checking CREATE TABLE permissions...');
    global $mod_strings;

    if (!$db->checkPrivilege('CREATE TABLE')) {
        logThis('cannot CREATE TABLE!');
        $out['db']['dbNoCreate'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_CREATE']}</span></td></tr>";
    }
    return $out;
}

/**
 * test perms for INSERT
 */
function testPermsInsert($db, $out, $skip = false)
{
    logThis('Checking INSERT INTO permissions...');
    global $mod_strings;

    if (!$db->checkPrivilege('INSERT')) {
        logThis('cannot INSERT INTO!');
        $out['db']['dbNoInsert'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_INSERT']}</span></td></tr>";
    }
    return $out;
}


/**
 * test perms for UPDATE TABLE
 */
function testPermsUpdate($db, $out, $skip = false)
{
    logThis('Checking UPDATE TABLE permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('UPDATE')) {
        logThis('cannot UPDATE TABLE!');
        $out['db']['dbNoUpdate'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_UPDATE']}</span></td></tr>";
    }
    return $out;
}


/**
 * test perms for SELECT
 */
function testPermsSelect($db, $out, $skip = false)
{
    logThis('Checking SELECT permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('SELECT')) {
        logThis('cannot SELECT!');
        $out['db']['dbNoSelect'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_SELECT']}</span></td></tr>";
    }
    return $out;
}

/**
 * test perms for DELETE
 */
function testPermsDelete($db, $out, $skip = false)
{
    logThis('Checking DELETE FROM permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('DELETE')) {
        logThis('cannot DELETE FROM!');
        $out['db']['dbNoDelete'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_DELETE']}</span></td></tr>";
    }
    return $out;
}


/**
 * test perms for ALTER TABLE ADD COLUMN
 */
function testPermsAlterTableAdd($db, $out, $skip = false)
{
    logThis('Checking ALTER TABLE ADD COLUMN permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('ADD COLUMN')) {
        logThis('cannot ADD COLUMN!');
        $out['db']['dbNoAddColumn'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_ADD_COLUMN']}</span></td></tr>";
    }
    return $out;
}

/**
 * test perms for ALTER TABLE ADD COLUMN
 */
function testPermsAlterTableChange($db, $out, $skip = false)
{
    logThis('Checking ALTER TABLE CHANGE COLUMN permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('CHANGE COLUMN')) {
        logThis('cannot CHANGE COLUMN!');
        $out['db']['dbNoChangeColumn'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_CHANGE_COLUMN']}</span></td></tr>";
    }
    return $out;
}

/**
 * test perms for ALTER TABLE DROP COLUMN
 */
function testPermsAlterTableDrop($db, $out, $skip = false)
{
    logThis('Checking ALTER TABLE DROP COLUMN permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('DROP COLUMN')) {
        logThis('cannot DROP COLUMN!');
        $out['db']['dbNoDropColumn'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_DROP_COLUMN']}</span></td></tr>";
    }
    return $out;
}


/**
 * test perms for DROP TABLE
 */
function testPermsDropTable($db, $out, $skip = false)
{
    logThis('Checking DROP TABLE permissions...');
    global $mod_strings;
    if (!$db->checkPrivilege('DROP TABLE')) {
        logThis('cannot DROP TABLE!');
        $out['db']['dbNoDropTable'] = true;
        $out['dbOut'] .= "<tr><td align='left'><span class='error'>{$mod_strings['LBL_UW_DB_NO_DROP_TABLE']}</span></td></tr>";
    }
    return $out;
}

function getFormattedError($error, $query)
{
    $error = '<div><b>' . $error;
    $error .= "</b>::{$query}</div>";

    return $error;
}

/**
 * parses a query finding the table name
 * @param string query The query
 * @return string table The table
 */
function getTableFromQuery($query)
{
    $standardQueries = ['ALTER TABLE', 'DROP TABLE', 'CREATE TABLE', 'INSERT INTO', 'UPDATE', 'DELETE FROM'];
    $query = preg_replace("/[^A-Za-z0-9\_\s]/", '', $query);
    $query = trim(str_replace($standardQueries, '', $query));

    $firstSpc = strpos($query, ' ');
    $end = ($firstSpc > 0) ? $firstSpc : strlen($query);
    $table = substr($query, 0, $end);

    return $table;
}


function fileCopy($file_path)
{
    if (file_exists(clean_path($_SESSION['unzip_dir'] . '/' . $_SESSION['zip_from_dir'] . '/' . $file_path))) {
        $file = clean_path($_SESSION['unzip_dir'] . '/' . $_SESSION['zip_from_dir'] . '/' . $file_path);
        $destFile = str_replace(clean_path($_SESSION['unzip_dir'] . '/' . $_SESSION['zip_from_dir']), clean_path(getcwd()), $file);
        if (!is_dir(dirname($destFile))) {
            mkdir_recursive(dirname($destFile)); // make sure the directory exists
        }
        copy_recursive($file, $destFile);
    }
}

function getChecklist($steps, $step)
{
    global $mod_strings;

    $skip = ['start', 'cancel', 'uninstall', 'end'];
    $j = 0;
    $i = 1;
    $ret = '<table cellpadding="3" cellspacing="4" border="0">';
    $ret .= '<tr><th colspan="3" align="left">' . $mod_strings['LBL_UW_CHECKLIST'] . ':</th></tr>';
    foreach ($steps['desc'] as $k => $desc) {
        if (in_array($steps['files'][$j], $skip)) {
            $j++;
            continue;
        }

        $desc_mod_pre = '';
        $desc_mod_post = '';

        if ($k == $_REQUEST['step']) {
            $desc_mod_pre = '<font color=blue><i>';
            $desc_mod_post = '</i></font>';
        }

        $ret .= "<tr><td>&nbsp;</td><td><b>{$i}: {$desc_mod_pre}{$desc}{$desc_mod_post}</b></td>";
        $ret .= "<td id={$steps['files'][$j]}><i></i></td></tr>";
        $i++;
        $j++;
    }
    $ret .= '</table>';
    return $ret;
}

function prepSystemForUpgrade()
{
    global $sugar_config;
    global $sugar_flavor;
    global $mod_strings;
    global $current_language;
    global $subdirs;
    global $base_upgrade_dir;
    global $base_tmp_upgrade_dir;
    [$p_base_upgrade_dir, $p_base_tmp_upgrade_dir] = getUWDirs();
    ///////////////////////////////////////////////////////////////////////////////
    ////	Make sure variables exist
    if (empty($base_upgrade_dir)) {
        $base_upgrade_dir = $p_base_upgrade_dir;
    }
    if (empty($base_tmp_upgrade_dir)) {
        $base_tmp_upgrade_dir = $p_base_tmp_upgrade_dir;
    }
    sugar_mkdir($base_tmp_upgrade_dir, 0775, true);
    if (!isset($subdirs) || empty($subdirs)) {
        $subdirs = ['full', 'langpack', 'module', 'patch', 'theme'];
    }

    $upgrade_progress_dir = $base_tmp_upgrade_dir;
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    if (file_exists($upgrade_progress_file)) {
        if (function_exists('get_upgrade_progress') && function_exists('didThisStepRunBefore')) {
            if (didThisStepRunBefore('end')) {
                include $upgrade_progress_file;
                unset($upgrade_config);
                unlink($upgrade_progress_file);
            }
        }
    }

    // increase the cuttoff time to 1 hour
    ini_set('max_execution_time', '3600');

    // make sure dirs exist
    if ($subdirs != null) {
        foreach ($subdirs as $subdir) {
            sugar_mkdir("$base_upgrade_dir/$subdir", 0775, true);
        }
    }
    // array of special scripts that are executed during (un)installation-- key is type of script, value is filename
    if (!defined('SUGARCRM_PRE_INSTALL_FILE')) {
        define('SUGARCRM_PRE_INSTALL_FILE', 'scripts/pre_install.php');
        define('SUGARCRM_POST_INSTALL_FILE', 'scripts/post_install.php');
        define('SUGARCRM_PRE_UNINSTALL_FILE', 'scripts/pre_uninstall.php');
        define('SUGARCRM_POST_UNINSTALL_FILE', 'scripts/post_uninstall.php');
    }

    $script_files = [
        'pre-install' => constant('SUGARCRM_PRE_INSTALL_FILE'),
        'post-install' => constant('SUGARCRM_POST_INSTALL_FILE'),
        'pre-uninstall' => constant('SUGARCRM_PRE_UNINSTALL_FILE'),
        'post-uninstall' => constant('SUGARCRM_POST_UNINSTALL_FILE'),
    ];

    // check that the upload limit is set to 6M or greater
    define('SUGARCRM_MIN_UPLOAD_MAX_FILESIZE_BYTES', 6 * 1024 * 1024);  // 6 Megabytes
    $upload_max_filesize = ini_get('upload_max_filesize');
    $upload_max_filesize_bytes = return_bytes($upload_max_filesize);

    if ($upload_max_filesize_bytes < constant('SUGARCRM_MIN_UPLOAD_MAX_FILESIZE_BYTES')) {
        $GLOBALS['log']->debug("detected upload_max_filesize: $upload_max_filesize");
        $admin_strings = return_module_language($current_language, 'Administration');
        echo '<p class="error">' . $admin_strings['MSG_INCREASE_UPLOAD_MAX_FILESIZE'] . ' ' . get_cfg_var('cfg_file_path') . "</p>\n";
    }
}

if (!function_exists('extractFile')) {
    function extractFile($zip_file, $file_in_zip, $base_tmp_upgrade_dir_stub = '')
    {
        global $base_tmp_upgrade_dir;

        // strip cwd
        $absolute_base_tmp_upgrade_dir = clean_path($base_tmp_upgrade_dir);
        $relative_base_tmp_upgrade_dir = clean_path(str_replace(clean_path(getcwd()), '', $absolute_base_tmp_upgrade_dir));

        // mk_temp_dir expects relative pathing
        $my_zip_dir = mk_temp_dir($relative_base_tmp_upgrade_dir);

        unzip_file($zip_file, $file_in_zip, $my_zip_dir);

        return ("$my_zip_dir/$file_in_zip");
    }
}

if (!function_exists('extractManifest')) {
    function extractManifest($zip_file)
    {
        logThis('extracting manifest.');
        return (extractFile($zip_file, 'manifest.php'));
    }
}

if (!function_exists('getInstallType')) {
    function getInstallType($type_string)
    {
        // detect file type
        global $subdirs;
        $subdirs = ['full', 'langpack', 'module', 'patch', 'theme', 'temp'];
        foreach ($subdirs as $subdir) {
            if (preg_match("#/$subdir/#", $type_string)) {
                return ($subdir);
            }
        }
        // return empty if no match
        return ('');
    }
}

function getImageForType($type)
{
    global $image_path;
    global $mod_strings;

    $icon = '';
    switch ($type) {
        case 'full':
            $icon = SugarThemeRegistry::current()->getImage('Upgrade', '', null, null, '.gif', $mod_strings['LBL_UPGRADE']);
            break;
        case 'langpack':
            $icon = SugarThemeRegistry::current()->getImage('LanguagePacks', '', null, null, '.gif', $mod_strings['LBL_LANGPACKS']);
            break;
        case 'module':
            $icon = SugarThemeRegistry::current()->getImage('ModuleLoader', '', null, null, '.gif', $mod_strings['LBL_MODULELOADER']);
            break;
        case 'patch':
            $icon = SugarThemeRegistry::current()->getImage('PatchUpgrades', '', null, null, '.gif', $mod_strings['LBL_PATCHUPGRADES']);
            break;
        case 'theme':
            $icon = SugarThemeRegistry::current()->getImage('Themes', '', null, null, '.gif', $mod_strings['LBL_THEMES']);
            break;
        default:
            break;
    }
    return ($icon);
}

if (!function_exists('getLanguagePackName')) {
    function getLanguagePackName($the_file)
    {
        $app_list_strings = FileLoader::varFromInclude($the_file, 'app_list_strings');
        if (isset($app_list_strings['language_pack_name'])) {
            return ($app_list_strings['language_pack_name']);
        }
        return ('');
    }
}

function getUITextForType($type)
{
    if ($type == 'full') {
        return ('Full Upgrade');
    }
    if ($type == 'langpack') {
        return ('Language Pack');
    }
    if ($type == 'module') {
        return ('Module');
    }
    if ($type == 'patch') {
        return ('Patch');
    }
    if ($type == 'theme') {
        return ('Theme');
    }
}

if (!function_exists('validate_manifest')) {
    /**
     * Verifies a manifest from a patch or module to be compatible with the current Sugar version and flavor
     * @param array manifest Standard manifest array
     * @return string Error message, blank on success
     */
    function validate_manifest($manifest)
    {
        logThis('validating manifest.php file');
        // takes a manifest.php manifest array and validates contents
        global $subdirs;
        global $sugar_version;
        global $sugar_flavor;
        global $mod_strings;

        if (!isset($manifest['type'])) {
            return $mod_strings['ERROR_MANIFEST_TYPE'];
        }

        $type = $manifest['type'];

        if (getInstallType("/$type/") == '') {
            return $mod_strings['ERROR_PACKAGE_TYPE'] . ": '" . $type . "'.";
        }

        if (isset($manifest['acceptable_sugar_versions'])) {
            $version_ok = false;
            $matches_empty = true;
            if (isset($manifest['acceptable_sugar_versions']['exact_matches'])) {
                $matches_empty = false;
                foreach ($manifest['acceptable_sugar_versions']['exact_matches'] as $match) {
                    if ($match == $sugar_version) {
                        $version_ok = true;
                    }
                }
            }
            if (!$version_ok && isset($manifest['acceptable_sugar_versions']['regex_matches'])) {
                $matches_empty = false;
                foreach ($manifest['acceptable_sugar_versions']['regex_matches'] as $match) {
                    if (!empty($match) && preg_match("/$match/", $sugar_version)) {
                        $version_ok = true;
                    }
                }
            }

            if (!$matches_empty && !$version_ok) {
                return $mod_strings['ERROR_VERSION_INCOMPATIBLE'] . '<br />' .
                    $mod_strings['ERR_UW_VERSION'] . $sugar_version;
            }
        }

        if (isset($manifest['acceptable_sugar_flavors']) && sizeof($manifest['acceptable_sugar_flavors']) > 0) {
            $flavor_ok = false;
            foreach ($manifest['acceptable_sugar_flavors'] as $match) {
                if ($match == $sugar_flavor) {
                    $flavor_ok = true;
                }
            }
            if (!$flavor_ok) {
                return $mod_strings['ERROR_FLAVOR_INCOMPATIBLE'] . '<br />' .
                    $mod_strings['ERR_UW_FLAVOR'] . $sugar_flavor . '<br />' .
                    $mod_strings['ERR_UW_FLAVOR_2'] . $manifest['acceptable_sugar_flavors'][0];
            }
        }

        return '';
    }
}

/**
 * deletes files created by unzipping a package
 */
function unlinkUWTempFiles()
{
    global $sugar_config;
    global $path;

    logThis('at unlinkUWTempFiles()');
    $tempDir = '';
    [$upgDir, $tempDir] = getUWDirs();

    if (file_exists($tempDir) && is_dir($tempDir)) {
        $files = findAllFiles($tempDir, [], false);
        rsort($files);
        foreach ($files as $file) {
            if (!is_dir($file)) {
                @unlink($file);
            }
        }
        // now do dirs
        $files = findAllFiles($tempDir, [], true);
        foreach ($files as $dir) {
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }
        $cacheFile = sugar_cached('modules/UpgradeWizard/_persistence.php');
        if (is_file($cacheFile)) {
            logThis("Unlinking Upgrade cache file: '_persistence.php'", $path);
            @unlink($cacheFile);
        }
    }
    logThis('finished!');
}

/**
 * finds all files in the passed path, but skips select directories
 * @param string dir Relative path
 * @param array the_array Collections of found files/dirs
 * @param bool include_dir True if we want to include directories in the
 * returned collection
 */
function uwFindAllFiles($dir, $theArray, $includeDirs = false, $skipDirs = [], $echo = false)
{
    // check skips
    if (whetherNeedToSkipDir($dir, $skipDirs)) {
        return $theArray;
    }

    if (!is_dir($dir)) {
        return $theArray;
    }   // Bug # 46035, just checking for valid dir
    $d = dir($dir);
    if ($d === false) {
        return $theArray;
    }   // Bug # 46035, more checking

    while ($f = $d->read()) {
        // bug 40793 Skip Directories array in upgradeWizard does not function correctly
        if ($f == '.' || $f == '..' || whetherNeedToSkipDir("$dir/$f", $skipDirs)) { // skip *nix self/parent
            continue;
        }

        // for AJAX length count
        if ($echo) {
            echo '.';
            ob_flush();
        }

        if (is_dir("$dir/$f")) {
            if ($includeDirs) { // add the directory if flagged
                $theArray[] = clean_path("$dir/$f");
            }

            // recurse in
            $theArray = uwFindAllFiles("$dir/$f/", $theArray, $includeDirs, $skipDirs, $echo);
        } else {
            $theArray[] = clean_path("$dir/$f");
        }
    }
    rsort($theArray);
    $d->close();
    return $theArray;
}


/**
 * unset's UW's Session Vars
 */
function resetUwSession()
{
    logThis('resetting $_SESSION');

    if (isset($_SESSION['committed'])) {
        unset($_SESSION['committed']);
    }
    if (isset($_SESSION['sugar_version_file'])) {
        unset($_SESSION['sugar_version_file']);
    }
    if (isset($_SESSION['upgrade_complete'])) {
        unset($_SESSION['upgrade_complete']);
    }
    if (isset($_SESSION['allTables'])) {
        unset($_SESSION['allTables']);
    }
    if (isset($_SESSION['alterCustomTableQueries'])) {
        unset($_SESSION['alterCustomTableQueries']);
    }
    if (isset($_SESSION['skip_zip_upload'])) {
        unset($_SESSION['skip_zip_upload']);
    }
    if (isset($_SESSION['install_file'])) {
        unset($_SESSION['install_file']);
    }
    if (isset($_SESSION['unzip_dir'])) {
        unset($_SESSION['unzip_dir']);
    }
    if (isset($_SESSION['zip_from_dir'])) {
        unset($_SESSION['zip_from_dir']);
    }
    if (isset($_SESSION['overwrite_files'])) {
        unset($_SESSION['overwrite_files']);
    }
    if (isset($_SESSION['schema_change'])) {
        unset($_SESSION['schema_change']);
    }
    if (isset($_SESSION['uw_restore_dir'])) {
        unset($_SESSION['uw_restore_dir']);
    }
    if (isset($_SESSION['step'])) {
        unset($_SESSION['step']);
    }
    if (isset($_SESSION['files'])) {
        unset($_SESSION['files']);
    }
    if (isset($_SESSION['Upgraded451Wizard'])) {
        unset($_SESSION['Upgraded451Wizard']);
    }
    if (isset($_SESSION['Initial_451to500_Step'])) {
        unset($_SESSION['Initial_451to500_Step']);
    }
    if (isset($_SESSION['license_shown'])) {
        unset($_SESSION['license_shown']);
    }
    if (isset($_SESSION['sugarMergeRunResults'])) {
        unset($_SESSION['sugarMergeRunResults']);
    }
}

/**
 * runs rebuild scripts
 */
function UWrebuild()
{
}

function getCustomTables()
{
    global $db;

    return $db->tablesLike('%_cstm');
}

function alterCustomTables($customTables)
{
    return [];
}

function getAllTables()
{
    global $db;
    return $db->getTablesArray();
}

///////////////////////////////////////////////////////////////////////////////
////	SYSTEM CHECK FUNCTIONS

/**
 * checks files for permissions
 * @param array files Array of files with absolute paths
 * @return string result of check
 */
function checkFiles($files, $echo = false)
{
    $errors = [];
    global $mod_strings;
    $filesNotWritable = [];
    $i = 0;
    $filesOut = "
		<a href='javascript:void(0); toggleNwFiles(\"filesNw\");'>{$mod_strings['LBL_UW_SHOW_NW_FILES']}</a>
		<div id='filesNw' style='display:none;'>
		<table cellpadding='3' cellspacing='0' border='0'>
		<tr>
			<th align='left'>{$mod_strings['LBL_UW_FILE']}</th>
			<th align='left'>{$mod_strings['LBL_UW_FILE_PERMS']}</th>
			<th align='left'>{$mod_strings['LBL_UW_FILE_OWNER']}</th>
			<th align='left'>{$mod_strings['LBL_UW_FILE_GROUP']}</th>
		</tr>";

    $isWindows = is_windows();
    foreach ($files as $file) {
        if ($isWindows) {
            if (!is_writable_windows($file)) {
                logThis('WINDOWS: File [' . $file . '] not readable - saving for display');
            }
        } else {
            if (!is_writable($file)) {
                logThis('File [' . $file . '] not writable - saving for display');
                // don't warn yet - we're going to use this to check against replacement files
                $filesNotWritable[$i] = $file;
                $filesNWPerms[$i] = substr(sprintf('%o', fileperms($file)), -4);
                $owner = posix_getpwuid(fileowner($file));
                $group = posix_getgrgid(filegroup($file));
                $filesOut .= '<tr>' .
                    "<td><span class='error'>{$file}</span></td>" .
                    "<td>{$filesNWPerms[$i]}</td>" .
                    '<td>' . $owner['name'] . '</td>' .
                    '<td>' . $group['name'] . '</td>' .
                    '</tr>';
            }
        }
        $i++;
    }

    $filesOut .= '</table></div>';
    // not a stop error
    $errors['files']['filesNotWritable'] = (count($filesNotWritable) > 0) ? true : false;
    if (count($filesNotWritable) < 1) {
        $filesOut = "{$mod_strings['LBL_UW_FILE_NO_ERRORS']}";
    }

    return $filesOut;
}

function deletePackageOnCancel()
{
    global $mod_strings;
    global $sugar_config;
    [$base_upgrade_dir, $base_tmp_upgrade_dir] = getUWDirs();
    logThis('running delete');
    if (!isset($_SESSION['install_file']) || ($_SESSION['install_file'] == '')) {
        logThis('ERROR: trying to delete non-existent file: [' . $_REQUEST['install_file'] . ']');
        $error = $mod_strings['ERR_UW_NO_FILE_UPLOADED'];
    }
    // delete file in upgrades/patch
    $delete_me = "$base_upgrade_dir/patch/" . basename(urldecode($_REQUEST['install_file']));
    if (@unlink($delete_me)) {
        $out = basename($delete_me) . $mod_strings['LBL_UW_FILE_DELETED'];
    } else {
        logThis('ERROR: could not delete [' . $delete_me . ']');
        $error = $mod_strings['ERR_UW_FILE_NOT_DELETED'] . $delete_me;
    }

    if (!empty($error)) {
        $out = "<b><span class='error'>{$error}</span></b><br />";
    }
}

function handleExecuteSqlKeys($db, $tableName, $disable)
{
    if (empty($tableName)) {
        return true;
    }
    if (is_callable([$db, 'supports'])) {
        // new API
        return $disable ? $db->disableKeys($tableName) : $db->enableKeys($tableName);
    } else {
        // old api
        $op = $disable ? 'DISABLE' : 'ENABLE';
        return $db->query("ALTER TABLE $tableName $op KEYS");
    }
}

function getAlterTable($query)
{
    $query = strtolower($query);
    if (preg_match('/^\s*alter\s+table\s+/', $query)) {
        $sqlArray = explode(' ', $query);
        $key = array_search('table', $sqlArray);
        return $sqlArray[($key + 1)];
    } else {
        return '';
    }
}

function set_upgrade_vars()
{
    logThis('setting session variables...');
    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    if (!is_dir($upgrade_progress_dir)) {
        mkdir_recursive($upgrade_progress_dir);
    }
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    if (file_exists($upgrade_progress_file)) {
        include $upgrade_progress_file;
    } else {
        fopen($upgrade_progress_file, 'w+');
    }
    if (!isset($upgrade_config) || $upgrade_config == null) {
        $upgrade_config = [];
        $upgrade_config[1]['upgrade_vars'] = [];
    }
    if (isset($upgrade_config[1]) && isset($upgrade_config[1]['upgrade_vars']) && !is_array($upgrade_config[1]['upgrade_vars'])) {
        $upgrade_config[1]['upgrade_vars'] = [];
    }

    if (!isset($upgrade_vars) || $upgrade_vars == null) {
        $upgrade_vars = [];
    }
    if (isset($_SESSION['unzip_dir']) && !empty($_SESSION['unzip_dir']) && file_exists($_SESSION['unzip_dir'])) {
        $upgrade_vars['unzip_dir'] = $_SESSION['unzip_dir'];
    }
    if (isset($_SESSION['install_file']) && !empty($_SESSION['install_file']) && file_exists($_SESSION['install_file'])) {
        $upgrade_vars['install_file'] = $_SESSION['install_file'];
    }
    if (isset($_SESSION['Upgraded451Wizard']) && !empty($_SESSION['Upgraded451Wizard'])) {
        $upgrade_vars['Upgraded451Wizard'] = $_SESSION['Upgraded451Wizard'];
    }
    if (isset($_SESSION['license_shown']) && !empty($_SESSION['license_shown'])) {
        $upgrade_vars['license_shown'] = $_SESSION['license_shown'];
    }
    if (isset($_SESSION['Initial_451to500_Step']) && !empty($_SESSION['Initial_451to500_Step'])) {
        $upgrade_vars['Initial_451to500_Step'] = $_SESSION['Initial_451to500_Step'];
    }
    if (isset($_SESSION['zip_from_dir']) && !empty($_SESSION['zip_from_dir'])) {
        $upgrade_vars['zip_from_dir'] = $_SESSION['zip_from_dir'];
    }
    //place into the upgrade_config array and rewrite config array only if new values are being inserted
    if (isset($upgrade_vars) && $upgrade_vars != null && sizeof($upgrade_vars) > 0) {
        foreach ($upgrade_vars as $key => $val) {
            if ($key != null && $val != null) {
                $upgrade_config[1]['upgrade_vars'][$key] = $upgrade_vars[$key];
            }
        }
        ksort($upgrade_config);
        if (is_writable($upgrade_progress_file) && write_array_to_file(
            'upgrade_config',
            $upgrade_config,
            $upgrade_progress_file
        )) {
            //writing to the file
        }
    }
}

function initialize_session_vars()
{
    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    if (file_exists($upgrade_progress_file)) {
        include $upgrade_progress_file;
        if (isset($upgrade_config) && $upgrade_config != null && is_array($upgrade_config) && sizeof($upgrade_config) > 0) {
            $currVarsArray = $upgrade_config[1]['upgrade_vars'];
            if (isset($currVarsArray) && $currVarsArray != null && is_array($currVarsArray) && sizeof($currVarsArray) > 0) {
                foreach ($currVarsArray as $key => $val) {
                    if ($key != null && $val != null) {
                        //set session variables
                        $_SESSION[$key] = $val;
                        //set varibales
                        '$' . $key = $val;
                    }
                }
            }
        }
    }
}

//track the upgrade progress on each step
//track the upgrade progress on each step
function set_upgrade_progress($currStep, $currState, $currStepSub = '', $currStepSubState = '')
{

    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    if (!is_dir($upgrade_progress_dir)) {
        mkdir_recursive($upgrade_progress_dir);
    }
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    if (file_exists($upgrade_progress_file)) {
        include $upgrade_progress_file;
    } else {
        if (function_exists('sugar_fopen')) {
            sugar_fopen($upgrade_progress_file, 'w+');
        } else {
            fopen($upgrade_progress_file, 'w+');
        }
    }
    if (!isset($upgrade_config) || $upgrade_config == null) {
        $upgrade_config = [];
        $upgrade_config[1]['upgrade_vars'] = [];
    }
    if (!is_array($upgrade_config[1]['upgrade_vars'])) {
        $upgrade_config[1]['upgrade_vars'] = [];
    }
    if ($currStep != null && $currState != null) {
        if (sizeof($upgrade_config) > 0) {
            if ($currStepSub != null && $currStepSubState != null) {
                //check if new status to be set or update
                //get the latest in array. since it has sub components prepare an array
                if (!empty($upgrade_config[sizeof($upgrade_config)][$currStep]) && is_array($upgrade_config[sizeof($upgrade_config)][$currStep])) {
                    $latestStepSub = currSubStep($upgrade_config[sizeof($upgrade_config)][$currStep]);
                    if ($latestStepSub == $currStepSub) {
                        $upgrade_config[sizeof($upgrade_config)][$currStep][$latestStepSub] = $currStepSubState;
                        $upgrade_config[sizeof($upgrade_config)][$currStep][$currStep] = $currState;
                    } else {
                        $upgrade_config[sizeof($upgrade_config)][$currStep][$currStepSub] = $currStepSubState;
                        $upgrade_config[sizeof($upgrade_config)][$currStep][$currStep] = $currState;
                    }
                } else {
                    $currArray = [];
                    $currArray[$currStep] = $currState;
                    $currArray[$currStepSub] = $currStepSubState;
                    $upgrade_config[sizeof($upgrade_config) + 1][$currStep] = $currArray;
                }
            } else {
                //get the current upgrade progress
                $latestStep = get_upgrade_progress();
                //set the upgrade progress
                if ($latestStep == $currStep) {
                    //update the current step with new progress status
                    $upgrade_config[sizeof($upgrade_config)][$latestStep] = $currState;
                } else {
                    //it's a new step
                    $upgrade_config[sizeof($upgrade_config) + 1][$currStep] = $currState;
                }
                // now check if there elements within array substeps
            }
        } else {
            //set the upgrade progress  (just starting)
            $upgrade_config[sizeof($upgrade_config) + 1][$currStep] = $currState;
        }

        if (is_writable($upgrade_progress_file) && write_array_to_file(
            'upgrade_config',
            $upgrade_config,
            $upgrade_progress_file
        )) {
            //writing to the file
        }
    }
}

function get_upgrade_progress()
{
    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    $currState = '';

    if (file_exists($upgrade_progress_file)) {
        include $upgrade_progress_file;
        if (!isset($upgrade_config) || $upgrade_config == null) {
            $upgrade_config = [];
        }
        if ($upgrade_config != null && sizeof($upgrade_config) > 1) {
            $currArr = $upgrade_config[sizeof($upgrade_config)];
            if (is_array($currArr)) {
                foreach ($currArr as $key => $val) {
                    $currState = $key;
                }
            }
        }
    }
    return $currState;
}

function currSubStep($currStep)
{
    $currState = null;
    $currSubStep = '';
    if (is_array($currStep)) {
        foreach ($currStep as $key => $val) {
            if ($key != null) {
                $currState = $key;
            }
        }
    }
    return $currState;
}

function currUpgradeState($currState)
{
    $currState = '';
    if (is_array($currState)) {
        foreach ($currState as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if ($k != null) {
                        $currState = $k;
                    }
                }
            } else {
                $currState = $key;
            }
        }
    }
    return $currState;
}

function didThisStepRunBefore($step, $SubStep = '')
{
    if ($step == null) {
        return;
    }
    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    $currState = '';
    $stepRan = false;
    if (file_exists($upgrade_progress_file)) {
        include $upgrade_progress_file;
        if (isset($upgrade_config) && $upgrade_config != null && is_array($upgrade_config) && sizeof($upgrade_config) > 0) {
            for ($i = 1; $i <= sizeof($upgrade_config); $i++) {
                if (is_array($upgrade_config[$i])) {
                    foreach ($upgrade_config[$i] as $key => $val) {
                        if ($key == $step) {
                            if (is_array($upgrade_config[$i][$step])) {
                                //now process
                                foreach ($upgrade_config[$i][$step] as $k => $v) {
                                    if (is_array($v)) {
                                        foreach ($v as $k1 => $v1) {
                                            if ($SubStep != null) {
                                                if ($SubStep == $k1 && $v1 == 'done') {
                                                    $stepRan = true;
                                                    break;
                                                }
                                            }
                                        }//foreach
                                    } elseif ($SubStep != null) {
                                        if ($SubStep == $k && $v == 'done') {
                                            $stepRan = true;
                                            break;
                                        }
                                    } elseif ($step == $k && $v == 'done') {
                                        $stepRan = true;
                                        break;
                                    }
                                }//foreach
                            } elseif ($val == 'done') {
                                $stepRan = true;
                            }
                        }
                    }//foreach
                }
            }//for
        }
    }
    return $stepRan;
}


//get and set post install status
function post_install_progress($progArray = '', $action = '')
{
    $upgrade_config = [];
    $upgrade_progress_dir = sugar_cached('upgrades/temp');
    $upgrade_progress_file = $upgrade_progress_dir . '/upgrade_progress.php';
    if ($action == '' || $action == 'get') {
        //get the state of post install
        $currProg = [];
        if (file_exists($upgrade_progress_file)) {
            include $upgrade_progress_file;
            if (is_array($upgrade_config[sizeof($upgrade_config)]['commit']['post_install']) && sizeof($upgrade_config[sizeof($upgrade_config)]['commit']['post_install']) > 0) {
                foreach ($upgrade_config[sizeof($upgrade_config)]['commit']['post_install'] as $k => $v) {
                    $currProg[$k] = $v;
                }
            }
        }
        return $currProg;
    } elseif ($action == 'set') {
        if (!is_dir($upgrade_progress_dir)) {
            mkdir($upgrade_progress_dir);
        }
        if (file_exists($upgrade_progress_file)) {
            include $upgrade_progress_file;
        } else {
            fopen($upgrade_progress_file, 'w+');
        }
        if (empty($upgrade_config[sizeof($upgrade_config)]['commit']['post_install']) || !is_array($upgrade_config[sizeof($upgrade_config)]['commit']['post_install'])) {
            $upgrade_config[sizeof($upgrade_config)]['commit']['post_install'] = [];
            $upgrade_config[sizeof($upgrade_config)]['commit']['post_install']['post_install'] = 'in_progress';
        }
        if ($progArray != null && is_array($progArray)) {
            foreach ($progArray as $key => $val) {
                $upgrade_config[sizeof($upgrade_config)]['commit']['post_install'][$key] = $val;
            }
        }
        if (is_writable($upgrade_progress_file) && write_array_to_file(
            'upgrade_config',
            $upgrade_config,
            $upgrade_progress_file
        )) {
            //writing to the file
        }
    }
}

function repairDBForUpgrade($execute = false, $path = '')
{

    global $current_user, $beanFiles;
    global $dictionary;
    set_time_limit(3600);

    $db = DBManagerFactory::getInstance();
    $sql = '';
    VardefManager::clearVardef();
    require_once 'include/ListView/ListView.php';
    foreach ($beanFiles as $bean => $file) {
        require_once $file;
        $focus = new $bean();
        $sql .= $db->repairTable($focus, $execute);
    }
    $olddictionary = $dictionary;
    unset($dictionary);
    include 'modules/TableDictionary.php';
    foreach ($dictionary as $meta) {
        $tablename = $meta['table'];
        $fielddefs = $meta['fields'];
        $indices = $meta['indices'] ?? [];
        $sql .= $db->repairTableParams($tablename, $fielddefs, $indices, $execute);
    }
    $qry_str = '';
    foreach (explode("\n", $sql) as $line) {
        if (!empty($line) && substr($line, -2) != '*/') {
            $line .= ';';
        }
        $qry_str .= $line . "\n";
    }
    $sql = str_replace(
        [
            "\n",
            '&#039;',
        ],
        [
            '',
            "'",
        ],
        preg_replace('#(/\*.+?\*/\n*)#', '', $qry_str)
    );
    logThis('*******START EXECUTING DB UPGRADE QUERIES***************', $path);
    logThis($sql, $path);
    logThis('*******END EXECUTING DB UPGRADE QUERIES****************', $path);
    if (!$execute) {
        return $sql;
    }
}

/**
 * upgradeUserPreferences
 * This method updates the user_preferences table and sets the pages/dashlets for users
 * which have ACL access to Trackers so that the Tracker dashlets are set in their user perferences
 *
 */
function upgradeUserPreferences()
{
    $errors = [];
    global $sugar_config, $sugar_version;
    $uw_strings = return_module_language($GLOBALS['current_language'], 'UpgradeWizard');

    $localization = Localization::getObject();
    $localeCoreDefaults = $localization->getLocaleConfigDefaults();

    // check the current system wide default_locale_name_format and add it to the list if it's not there
    if (empty($sugar_config['name_formats'])) {
        $sugar_config['name_formats'] = $localeCoreDefaults['name_formats'];
        if (!rebuildConfigFile($sugar_config, $sugar_version)) {
            $errors[] = $uw_strings['ERR_UW_CONFIG_WRITE'];
        }
    }

    $currentDefaultLocaleNameFormat = $sugar_config['default_locale_name_format'];

    if ($localization->isAllowedNameFormat($currentDefaultLocaleNameFormat)) {
        upgradeLocaleNameFormat($currentDefaultLocaleNameFormat);
    } else {
        $sugar_config['default_locale_name_format'] = $localeCoreDefaults['default_locale_name_format'];
        if (!rebuildConfigFile($sugar_config, $sugar_version)) {
            $errors[] = $uw_strings['ERR_UW_CONFIG_WRITE'];
        }
        $localization->createInvalidLocaleNameFormatUpgradeNotice();
    }

    if (file_exists($cachedfile = sugar_cached('dashlets/dashlets.php'))) {
        require $cachedfile;
    } elseif (file_exists('modules/Dashboard/dashlets.php')) {
        require 'modules/Dashboard/dashlets.php';
    }

    $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Home');

    $ce_to_pro_or_ent = (isset($_SESSION['upgrade_from_flavor']) && preg_match('/^SugarCE.*?(Pro|Ent|Corp|Ult)$/', $_SESSION['upgrade_from_flavor']));

    $db = DBManagerFactory::getInstance();
    $result = $db->query('SELECT id FROM users WHERE ' . User::getLicensedUsersWhere());
    while ($row = $db->fetchByAssoc($result)) {
        $current_user = new User();
        $current_user->retrieve($row['id']);

        // get the user's name locale format, check if it's in our list, add it if it's not, keep it as user's default
        $changed = false;
        $currentUserNameFormat = $current_user->getPreference('default_locale_name_format');
        if ($localization->isAllowedNameFormat($currentUserNameFormat)) {
            upgradeLocaleNameFormat($currentUserNameFormat);
        } else {
            $current_user->setPreference('default_locale_name_format', 's f l', 0, 'global');
            $changed = true;
        }

        if (!$current_user->getPreference('calendar_publish_key')) {
            // set publish key if not set already
            $current_user->setPreference('calendar_publish_key', create_guid());
            $changed = true;
        }

        //Set the user theme to be 'Sugar' theme since this is run for CE flavor conversions
        $userTheme = $current_user->getPreference('user_theme', 'global');

        //If theme is empty or if theme was set to Classic (Sugar5) or if this is a ce to pro/ent flavor upgrade change to RacerX theme
        if (empty($userTheme) || $userTheme == 'Sugar5' || $ce_to_pro_or_ent) {
            $changed = true;
            $current_user->setPreference('user_theme', 'RacerX', 0, 'global');
        }

        //Set the number of tabs by default to 7
        $maxTabs = $current_user->getPreference('max_tabs', 'global');
        if (empty($maxTabs)) {
            $changed = true;
            $current_user->setPreference('max_tabs', '7', 0, 'global');
        }

        //If preferences have changed, save before proceeding
        if ($changed) {
            $current_user->savePreferencesToDB();
        }

        $pages = $current_user->getPreference('pages', 'Home');

        if (empty($pages)) {
            continue;
        }

        $changed = false;
        $empty_dashlets = [];
        $dashlets = $current_user->getPreference('dashlets', 'Home');
        $dashlets = !empty($dashlets) ? $dashlets : $empty_dashlets;
        $existingDashlets = [];
        foreach ($dashlets as $id => $dashlet) {
            if (!empty($dashlet['className']) && !is_array($dashlet['className'])) {
                $existingDashlets[$dashlet['className']] = $dashlet['className'];
            }
        }

        // we need to force save the changes to disk, otherwise we lose them.
        if ($changed) {
            $current_user->savePreferencesToDB();
        }
    } //while

    /*
     * This section checks to see if the Tracker settings for the corresponding versions have been
     * disabled and the regular tracker (for breadcrumbs) enabled.  If so, then it will also disable
     * the tracking for the regular tracker.  Disabling the tracker (for breadcrumbs) will no longer prevent
     * breadcrumb tracking.  It will instead only track visible entries (see trackView() method in SugarView.php).
     * This has the benefit of reducing the tracking overhead and limiting it to only visible items.
     * For the CE version, we are checking to see that there are no entries enabled for PRO/ENT versions
     * we are checking for Tracker sessions, performance and queries.
     */
    if ($ce_to_pro_or_ent) {
        //Set tracker settings. Disable tracker session, performance and queries
        $category = 'tracker';
        $value = 1;
        $key = ['Tracker', 'tracker_sessions', 'tracker_perf', 'tracker_queries'];
        $admin = new Administration();
        foreach ($key as $k) {
            $admin->saveSetting($category, $k, $value);
        }
    } else {
        $query = "select count(name) as total from config where category = 'tracker' and name = 'Tracker'";
        $results = $db->query($query);
        if (!empty($results)) {
            $row = $db->fetchByAssoc($results);
            $total = $row['total'];
            if ($GLOBALS['sugar_flavor'] == 'PRO' || $GLOBALS['sugar_flavor'] == 'ENT') {
                //Fix problem with having multiple tracker entries in config table
                if ($total > 1) {
                    $db->query("DELETE FROM config where category = 'tracker' and name = 'Tracker'");
                    $db->query("INSERT INTO config (category, name, value) VALUES ('tracker', 'Tracker', '1')");
                }
            } else {
                //We are assuming if the 'Tracker' setting is not disabled then we will just disable it
                if ($total == 0) {
                    $db->query("INSERT INTO config (category, name, value) VALUES ('tracker', 'Tracker', '1')");
                }
            }
        }
    }
}


/**
 * Checks if a locale name format is part of the default list, if not adds it to the config
 * @param $name_format string a local name format string such as 's f l'
 * @return bool true on successful write to config file, false on failure;
 */
function upgradeLocaleNameFormat($name_format)
{
    $errors = [];
    global $sugar_config, $sugar_version;

    $localization = Localization::getObject();
    $localeConfigDefaults = $localization->getLocaleConfigDefaults();

    $uw_strings = return_module_language($GLOBALS['current_language'], 'UpgradeWizard');
    if (empty($sugar_config['name_formats'])) {
        $sugar_config['name_formats'] = $localeConfigDefaults['name_formats'];
        if (!rebuildConfigFile($sugar_config, $sugar_version)) {
            $errors[] = $uw_strings['ERR_UW_CONFIG_WRITE'];
        }
    }
    if (!safeInArrayForCli($name_format, $sugar_config['name_formats'])) {
        $new_config = sugarArrayMerge($sugar_config['name_formats'], [$name_format => $name_format]);
        $sugar_config['name_formats'] = $new_config;
        if (!rebuildConfigFile($sugar_config, $sugar_version)) {
            $errors[] = $uw_strings['ERR_UW_CONFIG_WRITE'];
            return false;
        }
    }
    return true;
}


function add_custom_modules_favorites_search()
{
    $module_directories = scandir('modules');

    foreach ($module_directories as $module_dir) {
        if ($module_dir == '.' || $module_dir == '..' || !is_dir("modules/{$module_dir}")) {
            continue;
        }

        $matches = [];
        preg_match('/^[a-z0-9]{1,5}_[a-z0-9_]+$/i', $module_dir, $matches);

        // Make sure the module was created by module builder
        if (empty($matches)) {
            continue;
        }

        $full_module_dir = "modules/{$module_dir}/";
        $read_searchdefs_from = "{$full_module_dir}/metadata/searchdefs.php";
        $read_SearchFields_from = "{$full_module_dir}/metadata/SearchFields.php";
        $read_custom_SearchFields_from = "custom/{$full_module_dir}/metadata/SearchFields.php";

        // Studio can possibly override this file, so we check for a custom version of it
        if (file_exists("custom/{$full_module_dir}/metadata/searchdefs.php")) {
            $read_searchdefs_from = "custom/{$full_module_dir}/metadata/searchdefs.php";
        }

        if (file_exists($read_searchdefs_from) && file_exists($read_SearchFields_from)) {
            $found_sf1 = false;
            $found_sf2 = false;
            require $read_searchdefs_from;
            foreach ($searchdefs[$module_dir]['layout']['basic_search'] as $sf_array) {
                if (isset($sf_array['name']) && $sf_array['name'] == 'favorites_only') {
                    $found_sf1 = true;
                }
            }

            require $read_SearchFields_from;
            if (isset($searchFields[$module_dir]['favorites_only'])) {
                $found_sf2 = true;
            }

            if (!$found_sf1 && !$found_sf2) {
                $searchdefs[$module_dir]['layout']['basic_search']['favorites_only'] = ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',];
                $searchdefs[$module_dir]['layout']['advanced_search']['favorites_only'] = ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',];
                $searchFields[$module_dir]['favorites_only'] = [
                    'query_type' => 'format',
                    'operator' => 'subquery',
                    'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites
								WHERE sugarfavorites.deleted=0
									and sugarfavorites.module = \'' . $module_dir . '\'
									and sugarfavorites.assigned_user_id = \'{0}\'',
                    'db_field' => ['id'],
                ];

                if (!is_dir("custom/{$full_module_dir}/metadata")) {
                    mkdir_recursive("custom/{$full_module_dir}/metadata");
                }
                $success_sf1 = write_array_to_file('searchdefs', $searchdefs, "custom/{$full_module_dir}/metadata/searchdefs.php");
                $success_sf2 = write_array_to_file('searchFields', $searchFields, "{$full_module_dir}/metadata/SearchFields.php");

                if (!$success_sf1) {
                    logThis("add_custom_modules_favorites_search failed for searchdefs.php for {$module_dir}");
                }
                if (!$success_sf2) {
                    logThis("add_custom_modules_favorites_search failed for SearchFields.php for {$module_dir}");
                }
                if ($success_sf1 && $success_sf2) {
                    logThis("add_custom_modules_favorites_search successfully updated searchdefs and searchFields for {$module_dir}");
                }
            }
        }
    }
}


/**
 * upgradeModulesForTeamsets
 *
 * This method adds the team_set_id values to the module tables that have the new team_set_id column
 * added through the SugarCRM 5.5.x upgrade process.  It also adds the values into the team_sets and
 * team_sets_teams tables.
 *
 * @param filter Array of modules to process; empty by default
 */
function upgradeModulesForTeamsets($filter = [])
{
    require 'include/modules.php';
    foreach ($beanList as $moduleName => $beanName) {
        if (!empty($filter) && array_search($moduleName, $filter) === false) {
            continue;
        }
        if ($moduleName == 'TeamMemberships' || $moduleName == 'ForecastOpportunities') {
            continue;
        }
        $bean = loadBean($moduleName);
        if (empty($bean) ||
            empty($bean->table_name)) {
            continue;
        }

        $FieldArray = $GLOBALS['db']->helper->get_columns($bean->table_name);
        if (!isset($FieldArray['team_id'])) {
            continue;
        }

        upgradeTeamColumn($bean, 'team_id');
    } //foreach

    //Upgrade users table
    $bean = loadBean('Users');
    upgradeTeamColumn($bean, 'default_team');
    $result = $GLOBALS['db']->query('SELECT id FROM teams where deleted=0');
    while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
        $teamset = new TeamSet();
        $teamset->addTeams($row['id']);
    }
}


/**
 * upgradeTeamColumn
 * Helper function to create a team_set_id column and also set team_set_id column
 * to have the value of the $column_name parameter
 *
 * @param $bean SugarBean which we are adding team_set_id column to
 * @param $column_name The name of the column containing the default team_set_id value
 */
function upgradeTeamColumn($bean, $column_name)
{
    $vardefs = [];
    //first let's check to ensure that the team_set_id field is defined, if not it could be the case that this is an older
    //module that does not use the SugarObjects
    if (empty($bean->field_defs['team_set_id']) && $bean->module_dir != 'Trackers') {
        //at this point we could assume that since we have a team_id defined and not a team_set_id that we need to
        //add that field and the corresponding relationships
        $object = $bean->object_name;
        $module = $bean->module_dir;
        $object_name = $object;
        $_object_name = strtolower($object_name);

        if (!empty($GLOBALS['dictionary'][$object]['table'])) {
            $table_name = $GLOBALS['dictionary'][$object]['table'];
        } else {
            $table_name = strtolower($module);
        }

        $path = 'include/SugarObjects/implements/team_security/vardefs.php';
        require $path;
        //go through each entry in the vardefs from team_security and unset anything that is already set in the core module
        //this will ensure we have the proper ordering.
        $fieldDiff = array_diff_assoc($vardefs['fields'], $GLOBALS['dictionary'][$bean->object_name]['fields']);

        $file = 'custom/Extension/modules/' . $bean->module_dir . '/Ext/Vardefs/teams.php';
        $contents = "<?php\n";
        if (!empty($fieldDiff)) {
            foreach ($fieldDiff as $key => $val) {
                $contents .= "\n\$GLOBALS['dictionary']['" . $object . "']['fields']['" . $key . "']=" . var_export_helper($val) . ';';
            }
        }
        $relationshipDiff = array_diff_assoc($vardefs['relationships'], $GLOBALS['dictionary'][$bean->object_name]['relationships']);
        if (!empty($relationshipDiff)) {
            foreach ($relationshipDiff as $key => $val) {
                $contents .= "\n\$GLOBALS['dictionary']['" . $object . "']['relationships']['" . $key . "']=" . var_export_helper($val) . ';';
            }
        }
        $indexDiff = array_diff_assoc($vardefs['indices'], $GLOBALS['dictionary'][$bean->object_name]['indices']);
        if (!empty($indexDiff)) {
            foreach ($indexDiff as $key => $val) {
                $contents .= "\n\$GLOBALS['dictionary']['" . $object . "']['indices']['" . $key . "']=" . var_export_helper($val) . ';';
            }
        }
        if ($fh = @sugar_fopen($file, 'wt')) {
            fputs($fh, $contents);
            fclose($fh);
        }


        //we have written out the teams.php into custom/Extension/modules/{$module_dir}/Ext/Vardefs/teams.php'
        //now let's merge back into vardefs.ext.php
        SugarAutoLoader::requireWithCustom('ModuleInstall/ModuleInstaller.php');
        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        $mi = new $moduleInstallerClass();
        $mi->merge_files('Ext/Vardefs/', 'vardefs.ext.php');
        VardefManager::loadVardef($bean->getModuleName(), $bean->object_name, true);
        $bean->field_defs = $GLOBALS['dictionary'][$bean->object_name]['fields'];
    }

    if (isset($bean->field_defs['team_set_id'])) {
        //Create the team_set_id column
        $FieldArray = $GLOBALS['db']->helper->get_columns($bean->table_name);
        if (!isset($FieldArray['team_set_id'])) {
            $GLOBALS['db']->addColumn($bean->table_name, $bean->field_defs['team_set_id']);
        }
        $indexArray = $GLOBALS['db']->helper->get_indices($bean->table_name);

        $indexName = getValidDBName('idx_' . strtolower($bean->table_name) . '_tmst_id', true, 34);
        $indexDef = [
            [
                'name' => $indexName,
                'type' => 'index',
                'fields' => ['team_set_id'],
            ],
        ];
        if (!isset($indexArray[$indexName])) {
            $GLOBALS['db']->addIndexes($bean->table_name, $indexDef);
        }

        //Update the table's team_set_id column to have the same values as team_id
        $GLOBALS['db']->query("UPDATE {$bean->table_name} SET team_set_id = {$column_name}");
    }
}

/**
 *  Update the folder subscription table which confirms to the team security mechanism but
 *  the class SugarFolders does not extend SugarBean and is therefore never picked up by the
 *  upgradeModulesForTeamsets function.
 */
function upgradeFolderSubscriptionsTeamSetId()
{
    logThis('In upgradeFolderSubscriptionsTeamSetId()');
    $query = 'UPDATE folders SET team_set_id = team_id';
    $result = $GLOBALS['db']->query($query);
    logThis('Finished upgradeFolderSubscriptionsTeamSetId()');
}


function addNewSystemTabsFromUpgrade($from_dir)
{
    $moduleList = null;
    global $path;
    if (isset($_SESSION['upgrade_from_flavor'])) {
        //check to see if there are any new files that need to be added to systems tab
        //retrieve old modules list
        logThis('check to see if new modules exist', $path);
        $oldModuleList = [];
        $newModuleList = [];
        include $from_dir . '/include/modules.php';
        $oldModuleList = $moduleList;
        include 'include/modules.php';
        $newModuleList = $moduleList;

        //include tab controller
        require_once 'modules/MySettings/TabController.php';
        $newTB = new TabController();

        //make sure new modules list has a key we can reference directly
        $newModuleList = $newTB->get_key_array($newModuleList);
        $oldModuleList = $newTB->get_key_array($oldModuleList);

        //iterate through list and remove commonalities to get new modules
        foreach ($newModuleList as $remove_mod) {
            if (safeInArrayForCli($remove_mod, $oldModuleList)) {
                unset($newModuleList[$remove_mod]);
            }
        }
        //new modules list now has left over modules which are new to this install, so lets add them to the system tabs
        logThis('new modules to add are ' . var_export($newModuleList, true), $path);

        if (!empty($newModuleList)) {
            //grab the existing system tabs
            $tabs = $newTB->get_system_tabs();

            //add the new tabs to the array
            foreach ($newModuleList as $nm) {
                $tabs[$nm] = $nm;
            }

            $newTB->set_system_tabs($tabs);
        }
        logThis('module tabs updated', $path);
    }
}


/**
 * clearHelpFiles
 * This method attempts to delete all English inline help files.
 * This method was introduced by 5.5.0RC2.
 */
function clearHelpFiles()
{
    $modulePath = clean_path(getcwd() . '/modules');
    $allHelpFiles = [];
    getFiles($allHelpFiles, $modulePath, '/en_us.help.*/');

    foreach ($allHelpFiles as $the_file) {
        if (is_file($the_file)) {
            unlink($the_file);
            logThis("Deleted file: $the_file");
        }
    }
}

/**
 * upgradeDocumentTypeFields
 *
 */
function upgradeDocumentTypeFields($path)
{
    //bug: 39757
    global $db;

    $documentsSql = "UPDATE documents SET doc_type = 'Sugar' WHERE doc_type IS NULL";
    $meetingsSql = "UPDATE meetings SET type = 'Sugar' WHERE type IS NULL";

    logThis('upgradeDocumentTypeFields Documents SQL:' . $documentsSql, $path);
    $db->query($documentsSql);
    logThis('upgradeDocumentTypeFields Meetings SQL:' . $meetingsSql, $path);
    $db->query($meetingsSql);
}


/**
 * merge_config_si_settings
 * This method checks for the presence of a config_si.php file and, if found, merges the configuration
 * settings from the config_si.php file into config.php.  If a config_si_location parameter value is not
 * supplied it will attempt to discover the config_si.php file location from where the executing script
 * was invoked.
 *
 * @param write_to_upgrade_log boolean optional value to write to the upgradeWizard.log file
 * @param config_location String optional value to config.php file location
 * @param config_si_location String optional value to config_si.php file location
 * @param path String file of the location of log file to write to
 * @return boolean value indicating whether or not a merge was attempted with config_si.php file
 */
function merge_config_si_settings($write_to_upgrade_log = false, $config_location = '', $config_si_location = '', $path = '')
{
    $argv = [];
    if (!empty($config_location) && !file_exists($config_location)) {
        if ($write_to_upgrade_log) {
            logThis('config.php file specified in ' . $config_si_location . ' could not be found.  Skip merging', $path);
        }
        return false;
    } elseif (empty($config_location)) {
        global $argv;
        //We are assuming this is from the silentUpgrade scripts so argv[3] will point to SugarCRM install location
        if (isset($argv[3]) && is_dir($argv[3])) {
            $config_location = $argv[3] . DIRECTORY_SEPARATOR . 'config.php';
        }
    }

    //If config_location is still empty or if the file cannot be found, skip merging
    if (empty($config_location) || !file_exists($config_location)) {
        if ($write_to_upgrade_log) {
            logThis('config.php file at (' . $config_location . ') could not be found.  Skip merging.', $path);
        }
        return false;
    } else {
        if ($write_to_upgrade_log) {
            logThis('Loading config.php file at (' . $config_location . ') for merging.', $path);
        }

        include $config_location;
        if (empty($sugar_config)) {
            if ($write_to_upgrade_log) {
                logThis('config.php contents are empty.  Skip merging.', $path);
            }
            return false;
        }
    }

    if (!empty($config_si_location) && !file_exists($config_si_location)) {
        if ($write_to_upgrade_log) {
            logThis('config_si.php file specified in ' . $config_si_location . ' could not be found.  Skip merging', $path);
        }
        return false;
    } elseif (empty($config_si_location)) {
        if (isset($argv[0]) && is_file($argv[0])) {
            $php_file = $argv[0];
            $p_info = pathinfo($php_file);
            $php_dir = (isset($p_info['dirname']) && $p_info['dirname'] != '.') ? $p_info['dirname'] . DIRECTORY_SEPARATOR : '';
            $config_si_location = $php_dir . 'config_si.php';
        }
    }

    //If config_si_location is still empty or if the file cannot be found, skip merging
    if (empty($config_si_location) || !file_exists($config_si_location)) {
        if ($write_to_upgrade_log) {
            logThis('config_si.php file at (' . $config_si_location . ') could not be found.  Skip merging.', $path);
        }
        return false;
    } else {
        if ($write_to_upgrade_log) {
            logThis('Loading config_si.php file at (' . $config_si_location . ') for merging.', $path);
        }

        include $config_si_location;
        if (empty($sugar_config_si)) {
            if ($write_to_upgrade_log) {
                logThis('config_si.php contents are empty.  Skip merging.', $path);
            }
            return false;
        }
    }

    //Now perform the merge operation
    $modified = false;
    foreach ($sugar_config_si as $key => $value) {
        if (!preg_match('/^setup_/', $key)) {
            if ($write_to_upgrade_log) {
                logThis('Merge key (' . $key . ') with value (' . $value . ')', $path);
            }
            // Add config_si values to the global sugar_config array so that they
            // are available on install, since merging SI happens after capturing
            // sugar_config and only saves to file, not to memory
            $GLOBALS['sugar_config'][$key] = $sugar_config[$key] = $value;
            $modified = true;
        }
    }

    if ($modified) {
        if ($write_to_upgrade_log) {
            logThis('Update config.php file with new values', $path);
        }

        if (!write_array_to_file('sugar_config', $sugar_config, $config_location)) {
            if ($write_to_upgrade_log) {
                logThis('*** ERROR: could not write to config.php', $path);
            }
            return false;
        }
    } else {
        if ($write_to_upgrade_log) {
            logThis('config.php values are in sync with config_si.php values.  Skipped merging.', $path);
        }
        return false;
    }

    if ($write_to_upgrade_log) {
        logThis('End merge_config_si_settings', $path);
    }
    return true;
}


/**
 * upgrade_connectors
 *
 * This function handles support for upgrading connectors it is invoked from both end.php and silentUpgrade_step2.php
 *
 */
function upgrade_connectors()
{
    require_once 'include/connectors/utils/ConnectorUtils.php';
    if (!ConnectorUtils::updateMetaDataFiles()) {
        $GLOBALS['log']->fatal('Cannot update metadata files for connectors');
    }

    //Delete the custom connectors.php file if it exists so that it may be properly rebuilt
    if (file_exists('custom/modules/Connectors/metadata/connectors.php')) {
        unlink('custom/modules/Connectors/metadata/connectors.php');
    }
}

/**
 * Enable the InsideView connector for the four default modules.
 */
function upgradeEnableInsideViewConnector($path = '')
{
    $mapping = null;
    $modules_sources = [];
    logThis('Begin upgradeEnableInsideViewConnector', $path);

    // Load up the existing mapping and hand it to the InsideView connector to have it setup the correct logic hooks
    $mapFile = 'modules/Connectors/connectors/sources/ext/rest/insideview/mapping.php';
    if (file_exists('custom/' . $mapFile)) {
        logThis('Found CUSTOM mappings', $path);
        require 'custom/' . $mapFile;
    } else {
        logThis('Used default mapping', $path);
        require $mapFile;
    }

    require_once 'include/connectors/sources/SourceFactory.php';
    $source = SourceFactory::getSource('ext_rest_insideview');

    // $mapping is brought in from the mapping.php file above
    $source->saveMappingHook($mapping);

    require_once 'include/connectors/utils/ConnectorUtils.php';
    ConnectorUtils::installSource('ext_rest_insideview');

    // Now time to set the various modules to active, because this part ignores the default config
    require CONNECTOR_DISPLAY_CONFIG_FILE;
    // $modules_sources come from that config file
    foreach ($source->allowedModuleList as $module) {
        $modules_sources[$module]['ext_rest_insideview'] = 'ext_rest_insideview';
    }
    if (!write_array_to_file('modules_sources', $modules_sources, CONNECTOR_DISPLAY_CONFIG_FILE)) {
        //Log error and return empty array
        logThis('Cannot write $modules_sources to ' . CONNECTOR_DISPLAY_CONFIG_FILE, $path);
    }

    logThis('End upgradeEnableInsideViewConnector', $path);
}

function repair_long_relationship_names($path = '')
{
    logThis('Begin repair_long_relationship_names', $path);
    require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php';
    $GLOBALS['mi_remove_tables'] = false;
    $touched = [];
    foreach ($GLOBALS['moduleList'] as $module) {
        $relationships = new DeployedRelationships($module);
        foreach ($relationships->getRelationshipList() as $rel_name) {
            if (strlen($rel_name) > 27 && empty($touched[$rel_name])) {
                logThis("Rebuilding relationship fields for $rel_name", $path);
                $touched[$rel_name] = true;
                $rel_obj = $relationships->get($rel_name);
                $rel_obj->setReadonly(false);
                $relationships->delete($rel_name);
                $relationships->save();
                $relationships->add($rel_obj);
                $relationships->save();
                $relationships->build();
            }
        }
    }
    logThis('End repair_long_relationship_names', $path);
}

function removeSilentUpgradeVarsCache()
{
    global $silent_upgrade_vars_loaded;

    $cacheFileDir = "{$GLOBALS['sugar_config']['cache_dir']}/silentUpgrader";
    $cacheFile = "{$cacheFileDir}/silentUpgradeCache.php";

    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }

    $silent_upgrade_vars_loaded = []; // Set to empty to reset it

    return true;
}

function loadSilentUpgradeVars()
{
    $silent_upgrade_vars_cache = null;
    global $silent_upgrade_vars_loaded;

    if (empty($silent_upgrade_vars_loaded)) {
        $cacheFile = sugar_cached('silentUpgrader/silentUpgradeCache.php');
        // We have no pre existing vars
        if (!file_exists($cacheFile)) {
            // Set the vars array so it's loaded
            $silent_upgrade_vars_loaded = ['vars' => []];
        } else {
            require_once $cacheFile;
            $silent_upgrade_vars_loaded = $silent_upgrade_vars_cache;
        }
    }

    return true;
}

function writeSilentUpgradeVars()
{
    global $silent_upgrade_vars_loaded;

    if (empty($silent_upgrade_vars_loaded)) {
        return false; // You should have set some values before trying to write the silent upgrade vars
    }

    $cacheFileDir = sugar_cached('silentUpgrader');
    $cacheFile = "{$cacheFileDir}/silentUpgradeCache.php";

    require_once 'include/dir_inc.php';
    if (!mkdir_recursive($cacheFileDir)) {
        return false;
    }
    require_once 'include/utils/file_utils.php';
    if (!write_array_to_file('silent_upgrade_vars_cache', $silent_upgrade_vars_loaded, $cacheFile, 'w')) {
        global $path;
        logThis("WARNING: writeSilentUpgradeVars could not write to {$cacheFile}", $path);
        return false;
    }

    return true;
}

function setSilentUpgradeVar($var, $value)
{
    if (!loadSilentUpgradeVars()) {
        return false;
    }

    global $silent_upgrade_vars_loaded;

    $silent_upgrade_vars_loaded['vars'][$var] = $value;

    return true;
}

function getSilentUpgradeVar($var)
{
    if (!loadSilentUpgradeVars()) {
        return false;
    }

    global $silent_upgrade_vars_loaded;

    if (!isset($silent_upgrade_vars_loaded['vars'][$var])) {
        return null;
    } else {
        return $silent_upgrade_vars_loaded['vars'][$var];
    }
}


/**
 * add_unified_search_to_custom_modules_vardefs
 *
 * This method calls the repair code to remove the unified_search_modules.php fiel
 *
 */
function add_unified_search_to_custom_modules_vardefs()
{
    if (file_exists($cachefile = sugar_cached('modules/unified_search_modules.php'))) {
        unlink($cachefile);
    }
}

if (!function_exists('getValidDBName')) {
    /*
     * Return a version of $proposed that can be used as a column name in any of our supported databases
     * Practically this means no longer than 25 characters as the smallest identifier length for our supported DBs is 30 chars for Oracle plus we add on at least four characters in some places (for indicies for example)
     * @param string $name Proposed name for the column
     * @param string $ensureUnique
     * @return string Valid column name trimmed to right length and with invalid characters removed
     */
    function getValidDBName($name, $ensureUnique = false, $maxLen = 30)
    {
        // first strip any invalid characters - all but alphanumerics and -
        $name = preg_replace('/[^\w-]+/i', '', $name);
        $len = strlen($name);
        $result = $name;
        if ($ensureUnique) {
            $md5str = md5($name);
            $tail = substr($name, -11);
            $temp = substr($md5str, strlen($md5str) - 4);
            $result = substr($name, 0, 10) . $temp . $tail;
        } elseif ($len > ($maxLen - 5)) {
            $result = substr($name, 0, 11) . substr($name, 11 - $maxLen + 5);
        }
        return strtolower($result);
    }


}

/**
 * Get UW directories
 * Provides compatibility with both 6.3 and pre-6.3 setup
 */
function getUWDirs()
{
    if (!class_exists('UploadStream')) {
        // we're still running the old code
        global $sugar_config;
        return ['upgrades', $sugar_config['cache_dir'] . 'upload/upgrades/temp'];
    } else {
        if (!in_array('upload', stream_get_wrappers())) {
            UploadStream::register(); // just in case file was copied, but not run
        }
        return ['upgrades', sugar_cached('upgrades/temp')];
    }
}

/**
 * Whether directory exists within list of directories to skip
 * @param string $dir dir to be checked
 * @param array $skipDirs list with skipped dirs
 * @return boolean
 */
function whetherNeedToSkipDir($dir, $skipDirs)
{
    foreach ($skipDirs as $skipMe) {
        if (strpos(clean_path($dir), (string) $skipMe) !== false) {
            return true;
        }
    }
    return false;
}


/*
 * rebuildSprites
 * @param silentUpgrade boolean flag indicating whether or not we should treat running the SugarSpriteBuilder as an upgrade operation
 *
 */
function rebuildSprites($fromUpgrade = true)
{
    require_once 'modules/Administration/SugarSpriteBuilder.php';
    $sb = new SugarSpriteBuilder();
    $sb->cssMinify = true;
    $sb->fromSilentUpgrade = $fromUpgrade;
    $sb->silentRun = $fromUpgrade;

    // add common image directories
    $sb->addDirectory('default', 'include/images');
    $sb->addDirectory('default', 'themes/default/images');
    $sb->addDirectory('default', 'themes/default/images/SugarLogic');

    // add all theme image directories
    if ($dh = opendir('themes')) {
        while (($dir = readdir($dh)) !== false) {
            if ($dir != '.' && $dir != '..' && $dir != 'default' && is_dir('themes/' . $dir)) {
                $sb->addDirectory($dir, "themes/{$dir}/images");
            }
        }
        closedir($dh);
    }

    // add all theme custom image directories
    $custom_themes_dir = 'custom/themes';
    if (is_dir($custom_themes_dir)) {
        if ($dh = opendir($custom_themes_dir)) {
            while (($dir = readdir($dh)) !== false) {
                //Since the custom theme directories don't require an images directory
                // we check for it implicitly
                if ($dir != '.' && $dir != '..' && is_dir('custom/themes/' . $dir . '/images')) {
                    $sb->addDirectory($dir, "custom/themes/{$dir}/images");
                }
            }
            closedir($dh);
        }
    }

    // generate the sprite goodies
    // everything is saved into cache/sprites
    $sb->createSprites();
}


/**
 * repairSearchFields
 *
 * This method goes through the list of SearchFields files based and calls TemplateRange::repairCustomSearchFields
 * method on the files in an attempt to ensure the range search attributes are properly set in SearchFields.php.
 *
 * @param $globString String value used for glob search defaults to searching for all SearchFields.php files in modules directory
 * @param $path String value used to point to log file should logging be required.  Defaults to empty.
 *
 */
function repairSearchFields($globString = 'modules/*/metadata/SearchFields.php', $path = '')
{
    if (!empty($path)) {
        logThis('Begin repairSearchFields', $path);
    }

    require_once 'include/dir_inc.php';
    require_once 'modules/DynamicFields/templates/Fields/TemplateRange.php';
    require 'include/modules.php';

    global $beanList;
    $searchFieldsFiles = glob($globString);

    foreach ($searchFieldsFiles as $file) {
        if (preg_match('/modules\/(.*?)\/metadata\/SearchFields\.php/', $file, $matches) && isset($beanList[$matches[1]])) {
            $module = $matches[1];
            $beanName = $beanList[$module];
            VardefManager::loadVardef($module, $beanName);
            if (isset($GLOBALS['dictionary'][$beanName]['fields'])) {
                if (!empty($path)) {
                    logThis('Calling TemplateRange::repairCustomSearchFields for module ' . $module, $path);
                }
                TemplateRange::repairCustomSearchFields($GLOBALS['dictionary'][$beanName]['fields'], $module);
            }
        }
    }

    if (!empty($path)) {
        logThis('End repairSearchFields', $path);
    }
}


/**
 * Patch for bug57431
 * Compares current moduleList to base moduleList to detect if some modules have been renamed
 * Run changeModuleModStrings to create new labels based on customizations.
 */
function updateRenamedModulesLabels()
{
    require_once 'modules/Studio/wizards/RenameModules.php';
    require_once 'include/utils.php';

    $klass = new RenameModules();
    $languages = get_languages();

    foreach ($languages as $langKey => $langName) {
        //get list strings for this language
        $strings = return_app_list_strings_language($langKey);

        //get base list strings for this language
        if (file_exists("include/language/$langKey.lang.php")) {
            include FileLoader::validateFilePath("include/language/$langKey.lang.php");

            //Keep only renamed modules
            $renamedModules = array_diff($strings['moduleList'], $app_list_strings['moduleList']);

            foreach ($renamedModules as $moduleId => $moduleName) {
                if (isset($app_list_strings['moduleListSingular'][$moduleId])) {
                    $klass->selectedLanguage = $langKey;

                    $replacementLabels = [
                        'singular' => $strings['moduleListSingular'][$moduleId],
                        'plural' => $strings['moduleList'][$moduleId],
                        'prev_singular' => $app_list_strings['moduleListSingular'][$moduleId],
                        'prev_plural' => $app_list_strings['moduleList'][$moduleId],
                        'key_plural' => $moduleId,
                        'key_singular' => $klass->getModuleSingularKey($moduleId),
                    ];
                    $klass->changeModuleModStrings($moduleId, $replacementLabels);
                }
            }
        }
    }
}


/**
 * addPdfManagerTemplate
 *
 * This method adds default PDF Template in PDF Manager
 */
function addPdfManagerTemplate()
{
    logThis('Begin addPdfManagerTemplate');

    include 'install/seed_data/PdfManager_SeedData.php';

    logThis('End addPdfManagerTemplate');
}


/**
 * This is left here for legacy, just calling the new methods
 * @param int $perJob The Number of records to put in a job
 * @return array|string An array of jobs ids that were created, unless
 *      there is one, the it's just that single job id
 * @deprecated
 */
function updateOpportunitiesForForecasting($perJob = 100)
{
    SugarAutoLoader::load('include/SugarQueue/jobs/SugarJobUpdateOpportunities.php');
    return SugarJobUpdateOpportunities::updateOpportunitiesForForecasting($perJob);
}

/**
 * Add the platform to the portal config settings
 */
function updatePortalConfigToContainPlatform()
{
    $db = DBManagerFactory::getInstance();
    $sql = "UPDATE config SET platform = 'support' where category = 'portal'";
    $db->query($sql);
}
