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

define('SUGAR_BASE_DIR', str_replace('\\', '/', realpath(__DIR__ . '/..')));

if (extension_loaded('shadow') && ini_get('shadow.enabled')) {
    $shadowConfig = shadow_get_config();
    if ($shadowConfig['instance']) {
        define('SHADOW_INSTANCE_DIR', $shadowConfig['instance']);
    }
}

$cwd = getcwd();
chdir(SUGAR_BASE_DIR);

set_include_path(
    SUGAR_BASE_DIR . PATH_SEPARATOR .
    SUGAR_BASE_DIR . '/vendor' . PATH_SEPARATOR .
    get_include_path()
);

global $sugar_config;
if (is_file('config.php')) {
    require 'config.php';
}

if (is_file('config_override.php')) {
    require 'config_override.php';
}

require_once 'include/utils/autoloader.php';
SugarAutoLoader::init(!empty($sugar_config['dbconfig']['db_name']));

// PHPUnit needs the current directory to remain tests/ or testsunit/,
// otherwise it won't be able to find the config file in the the current directory
if (strcmp(basename($cwd), '{old}') == 0 || strcmp(basename($cwd), 'unit-php') == 0) {
    chdir($cwd);
    unset($cwd);
}
