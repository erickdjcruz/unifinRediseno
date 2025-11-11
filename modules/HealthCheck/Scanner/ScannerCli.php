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

require_once __DIR__ . '/Scanner.php';

/**
 *
 * HealthCheck Scanner CLI support
 *
 */
class HealthCheckScannerCli extends HealthCheckScanner
{
    /**
     *
     * @param array $argv
     */
    public function parseCliArgs($argv)
    {
        if (!is_countable($argv)) {
            return false;
        }
        if (empty($argv) || count($argv) < 2) {
            return false;
        }
        for ($i = 1; $i < (count($argv) - 1); $i++) {
            // logfile name
            if ($argv[$i] == '-l') {
                $i++;
                $this->logfile = $argv[$i];
            }

            // verbose level 1
            if ($argv[$i] == '-v') {
                $this->verbose = 1;
            }

            // verbose level 2 (curently not used)
            if ($argv[$i] == '-vv') {
                $this->verbose = 2;
            }

            // generic properties
            if ($argv[$i] == '-d') {
                while (strpos($argv[++$i], '=')) {
                    [$property, $value] = $this->parsePropertyValuePair($argv[$i]);
                    if (property_exists($this, $property)) {
                        $this->$property = $value;
                    }
                }
            }
        }

        // instance directory
        $this->instance = $argv[count($argv) - 1];

        return true;
    }

    /**
     * Consumes key=value string and returns key => value hash
     *
     * @param $string
     * @return array
     */
    protected function parsePropertyValuePair($string)
    {
        return array_map('trim', explode('=', $string));
    }

    /**
     *
     * Console output - temp solution, need proper central logging
     * @see Scanner::fail()
     */
    public function fail($msg)
    {
        $result = parent::fail($msg);
        echo "$msg\n";
        return $result;
    }

    /**
     *
     * Console output - temp solution, need proper central logging
     * @see Scanner::log()
     */
    protected function log($msg, $tag = 'INFO')
    {
        $fmsg = parent::log($msg, $tag);
        if ($this->verbose > 1) {
            echo $fmsg;
        }
    }

    /**
     * @return bool
     * @see HealthCheckScanner::init
     *
     */
    protected function init()
    {
        if (!is_dir($this->instance)) {
            return $this->fail("{$this->instance} is not a directory");
        }
        $this->log('Initializing the environment');
        defined('SUGAR_SHADOW_TEMPLATEPATH') ? chdir(SUGAR_SHADOW_TEMPLATEPATH) : chdir($this->instance);
        if (!file_exists('include/entryPoint.php')) {
            return $this->fail("{$this->instance} is not a Sugar instance");
        }
        define('ENTRY_POINT_TYPE', 'api');
        global $beanFiles, $beanList, $objectList, $timedate, $moduleList, $modInvisList, $sugar_config, $locale,
        $sugar_version, $sugar_flavor, $sugar_build, $sugar_db_version, $sugar_timestamp, $db, $locale,
        $installing, $bwcModules, $app_list_strings, $modules_exempt_from_availability_check, $current_language;
        if (!defined('sugarEntry')) {
            define('sugarEntry', true);
        }
        require_once 'include/entryPoint.php';
        $app_list_strings = return_app_list_strings_language($current_language);

        return parent::init();
    }

    public function usageAndDie($script)
    {
        die("Use php {$script} [-d property1=value1... property1=valueN] [-l logfile] [-v] /path/to/instance\n");
    }

    /**
     * Runs cli scanner
     * @param $argv
     */
    public static function start($argv)
    {
        $scanner = new self();

        if (!$scanner->parseCliArgs($argv)) {
            $scanner->usageAndDie($argv[0]);
        }

        $scanner->scan();

        if ($scanner->getVerbose()) {
            echo "VERDICT: {$scanner->getStatus()}\n";
        }

        exit($scanner->getResultCode());
    }
}

if (empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) {
    return;
}

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die('This is a command-line only script');
}

HealthCheckScannerCli::start($argv);
