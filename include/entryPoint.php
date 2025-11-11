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

use Sugarcrm\Sugarcrm\Deprecation\Symfony as SymfonyDeprecationHandler;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

/**
 * Known Entry Points as of 4.5
 * acceptDecline.php
 * campaign_tracker.php
 * campaign_trackerv2.php
 * cron.php
 * dictionary.php
 * download.php
 * emailmandelivery.php
 * export_dataset.php
 * export.php
 * index.php
 * install.php
 * json.php
 * json_server.php
 * maintenance.php
 * metagen.php
 * pdf.php
 * phprint.php
 * process_queue.php
 * process_workflow.php
 * removeme.php
 * schedulers.php
 * soap.php
 * su.php
 * sugar_version.php
 * TreeData.php
 * tree_level.php
 * tree.php
 * vcal_server.php
 * vCard.php
 * zipatcher.php
 * WebToLeadCapture.php
 * HandleAjaxCall.php */

/*
 * for 50, added:
 * minify.php
 */

/*
* for 510, added:
* dceActionCleanup.php
*/
if (strpos(PHP_SAPI, 'cli') !== 0
    && in_array('phar', stream_get_wrappers(), true)) {
    stream_wrapper_unregister('phar');
}

$GLOBALS['startTime'] = microtime(true);

if (empty($GLOBALS['installing']) && !file_exists('config.php')) {
    header('Location: install.php');
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

if (empty($GLOBALS['installing']) && empty($sugar_config['dbconfig']['db_name'])) {
    header('Location: install.php');
    exit();
}

require_once 'include/utils.php';
require_once 'include/dir_inc.php';

require_once 'include/utils/array_utils.php';
require_once 'include/utils/file_utils.php';
require_once 'include/utils/security_utils.php';
require_once 'include/utils/logic_utils.php';
require_once 'include/utils/sugar_file_utils.php';
require_once 'include/utils/mvc_utils.php';
require_once 'include/utils/db_utils.php';
require_once 'include/utils/encryption_utils.php';

require_once 'include/SugarCache/SugarCache.php';

if (empty($GLOBALS['installing'])) {
    $GLOBALS['log'] = LoggerManager::getLogger();
}

if (!empty($sugar_config['xhprof_config'])) {
    SugarXHprof::getInstance()->start();
}

register_shutdown_function('sugar_cleanup');


// cn: set php.ini settings at entry points
setPhpIniSettings();

require_once 'sugar_version.php'; // provides $sugar_version, $sugar_db_version, $sugar_flavor

if ($sugar_config['symfony_deprecation_log'] ?? false) {
    new SymfonyDeprecationHandler(LoggerFactory::getLogger('deprecation'));
}

// Initialize InputValdation service as soon as possible. Up to this point
// it is expected that no code has altered any input superglobals.
InputValidation::initService();

// Check to see if custom utils exist and load them too
// not doing it in utils since autoloader is not loaded there yet
foreach (SugarAutoLoader::existing('include/custom_utils.php', 'custom/include/custom_utils.php', SugarAutoLoader::loadExtension('utils')) as $file) {
    require_once $file;
}

require_once 'include/modules.php'; // provides $moduleList, $beanList, $beanFiles, $modInvisList, $adminOnlyList, $modInvisListActivities
require_once 'modules/Administration/updater_utils.php';
require_once 'modules/Currencies/Currency.php';

UploadStream::register();

///////////////////////////////////////////////////////////////////////////////
////    Handle loading and instantiation of various Sugar* class
if (!defined('SUGAR_PATH')) {
    define('SUGAR_PATH', realpath(__DIR__ . '/..'));
}
if (empty($GLOBALS['installing'])) {
    ///////////////////////////////////////////////////////////////////////////////
    ////	SETTING DEFAULT VAR VALUES
    $error_notice = '';
    $use_current_user_login = false;

    LogicHook::initialize()->call_custom_logic('', 'entry_point_variables_setting');

    if (!empty($sugar_config['session_dir'])) {
        session_save_path($sugar_config['session_dir']);
    }

    if (class_exists('SessionHandler') && !isCli()) {
        session_set_save_handler(new SugarSessionHandler());
    }

    $GLOBALS['sugar_version'] = $sugar_version;
    $GLOBALS['sugar_flavor'] = $sugar_flavor;
    $GLOBALS['js_version_key'] = get_js_version_key();
    // Because this line is *supposed* to be indented...
    $GLOBALS['sugar_mar_version'] = $sugar_mar_version;

    SugarApplication::preLoadLanguages();

    $timedate = TimeDate::getInstance();
    $GLOBALS['timedate'] = $timedate;

    if (!empty($sugar_config['dbal_deprecation_log'])) {
        \Doctrine\Deprecations\Deprecation::enableWithPsrLogger(LoggerFactory::getLogger('deprecation'));
    }

    $db = DBManagerFactory::getInstance();
    $db->resetQueryCount();
    $locale = Localization::getObject();

    // Emails uses the REQUEST_URI later to construct dynamic URLs.
    // IIS does not pass this field to prevent an error, if it is not set, we will assign it to ''.
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = '';
    }

    $current_user = BeanFactory::newBean('Users');
    $current_entity = null;

    if (!$GLOBALS['sugar_config']['activity_streams_enabled']) {
        Activity::disable();
    }

    LogicHook::initialize()->call_custom_logic('', 'after_entry_point');
}


////	END SETTING DEFAULT VAR VALUES
///////////////////////////////////////////////////////////////////////////////
