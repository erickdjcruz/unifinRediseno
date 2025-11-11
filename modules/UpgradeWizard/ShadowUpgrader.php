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
require_once 'CliUpgrader.php';

/**
 * Upgrder for the shadow environment
 */
class ShadowUpgrader extends CliUpgrader
{
    protected $options = [
        // required, short, long
        'pre_template' => [true, 'f', 'from'],
        'post_template' => [true, 't', 'to'],
        'source_dir' => [true, 's', 'source'],
        'log' => [true, 'l', 'log'],
        'admin' => [true, 'u', 'user'],
        'backup' => [false, 'b', 'backup'],
        'script_mask' => [false, 'm', 'mask'],
        'stage' => [false, 'S', 'stage'],
        'autoconfirm' => [false, 'A', 'autoconfirm'],
        'force' => [false, 'F', 'force'],
        // Appears when stage was not specified and upgrader is running step by step.
        'all' => [false, 'a', 'all'],
    ];

    /**
     * @see CliUpgrader::usage()
     */
    protected function commit()
    {
        // commit doesn't do anything
        return true;
    }

    /**
     * @see CliUpgrader::usage()
     */
    protected static function usage()
    {
        [$version, $build] = static::getVersion();
        $usage = <<<eoq2
Shadow Upgrader v.$version (build $build)
php ShadowUpgrader.php -f oldTemplate -t newTemplate -s pathToSugarInstance -l logFile -u admin-user

Example:
    php ShadowUpgrader.php -f /sugar/templates/7.0.0 -t /sugar/templates/7.1.0 -s path-to-sugar-instance/ \
    	    -l silentupgrade.log -u admin

Arguments:
    -f/--from oldTemplate                : Pre-upgrade template
    -t/--to newTemplate                  : Target template
    -s/--source pathToSugarInstance      : Sugar instance being upgraded.
    -l/--log logFile                     : Upgarde log file (by default relative to instance dir)
    -u/--user admin-user                 : admin user performing the upgrade
Optional arguments:
    -m/--mask scriptMask                 : Script mask - which types of scripts to run.
                                           Supported types: db, custom, none. Default is db,custom.
    -b/--backup 0/1                      : Create backup of deleted files? 0 means no backup, default is 0.
    -S/--stage stage                     : Run specific stage of the upgrader. 'continue' means start where it stopped last time.
    -A/--autoconfirm 0/1                 : Automatic confirm health check results, default is 1
    -F/--force 0/1                       : Force upgrade regardless of health check results, default is 0 (use with caution !)

eoq2;
        echo $usage;
    }

    /**
     * @return bool
     * @see UpgradeDriver::verifyArguments()
     */
    protected function verifyArguments()
    {
        if (!function_exists('shadow')) {
            $this->argError('Shadow module should be installed to run this script.');
        }

        if (empty($this->context['source_dir']) || !is_dir($this->context['source_dir'])) {
            $this->argError('Source dir parameter must be a valid directory.');
        }

        if (empty($this->context['pre_template']) || empty($this->context['post_template'])) {
            $this->argError('Templates should be specified');
        }

        if (!is_file("{$this->context['pre_template']}/include/entryPoint.php")) {
            $this->argError("{$this->context['pre_template']} is not a SugarCRM template.");
        }

        if (!is_file("{$this->context['post_template']}/include/entryPoint.php")) {
            $this->argError("{$this->context['post_template']} is not a SugarCRM template.");
        }

        if (!is_file("{$this->context['source_dir']}/config.php")) {
            $this->argError("{$this->context['source_dir']} is not a SugarCRM directory.");
        }

        return true;
    }

    /**
     * Returns version from the given $path
     * @param $path
     * @return string
     */
    protected function getVersionFromPath($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $f = array_pop($parts);
        $v = array_pop($parts);
        return $v . $f;
    }

    /**
     * Fix values in the context
     * @param array $context
     * @return array
     */
    public function fixupContext($context)
    {
        $context = parent::fixupContext($context);
        $context['script'] = __FILE__;
        $context['pre_template'] = realpath($context['pre_template']);
        $context['post_template'] = realpath($context['post_template']);
        $from = $this->getVersionFromPath($context['pre_template']);
        $to = $this->getVersionFromPath($context['post_template']);
        $context['zip'] = "ShadowUpgrade-$from-$to";
        // only use custom and DB scripts
        if (isset($context['script_mask'])) {
            $context['script_mask'] &= UpgradeScript::UPGRADE_CUSTOM | UpgradeScript::UPGRADE_DB;
        } else {
            $context['script_mask'] = UpgradeScript::UPGRADE_CUSTOM | UpgradeScript::UPGRADE_DB;
        }
        $context['new_source_dir'] = $context['post_template'];
        $context['original_source_dir'] = $context['source_dir'];
        $context['backup'] = 0;

        //As of 7.6.1.0 Health check is now included and packaged in the Shadow Upgrader
        $context['health_check_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'HealthCheck';

        return $context;
    }

    /**
     * @param string $zip
     * @return bool|false
     * @see CliUpgrader::extractZip()
     */
    protected function extractZip($zip)
    {
        // no zip, nothing to extract
        return true;
    }

    /**
     * @param string $file
     * @return bool
     * @see CliUpgrader::unlink()
     */
    public function unlink($file)
    {
        if ($file[0] == '/') {
            return parent::unlink($file);
        }
        // check relative paths against source dir
        if (file_exists($this->context['source_dir'] . '/' . $file)) {
            return @unlink($file);
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadFromVersion()
    {
        return $this->loadVersion($this->context['pre_template']);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadToVersion()
    {
        return $this->loadVersion($this->context['post_template']);
    }

    /**
     * @return array
     * @see CliUpgrader::getManifest()
     */
    public function getManifest()
    {
        // load target data
        [$to_version, $to_flavor] = $this->getToVersion();
        // return fake manifest
        return [
            'description' => 'Shadow Upgrade from {$this->from_version}/{$this->from_flavor} to $to_version/$to_flavor',
            'acceptable_sugar_flavors' => [$this->from_flavor],
            'acceptable_sugar_versions' => ['exact_matches' => [$this->from_version]],
            'type' => 'patch',
            'version' => $to_version,
            'flavor' => $to_flavor,
        ];
    }

    /**
     * @param string $zip
     * @param string $dir
     * @return bool|false
     * @see CliUpgrader::verify()
     */
    protected function verify($zip, $dir)
    {
        chdir($this->context['pre_template']);
        return parent::verify($zip, $dir);
    }

    /**
     * @see CliUpgrader::initSugar()
     */
    protected function initSugar()
    {
        if ($this->context['stage'] == 'pre' || $this->context['stage'] == 'unpack' || $this->context['stage'] == 'healthcheck') {
            $templ_dir = $this->context['pre_template'];
        } else {
            $templ_dir = $this->context['post_template'];
        }
        chdir($templ_dir);
        $this->log("Shadow configuration: $templ_dir -> {$this->context['original_source_dir']}");
        shadow($templ_dir, $this->context['original_source_dir'], ['cache', 'upload', 'config.php']);
        $this->context['source_dir'] = $templ_dir;
        return parent::initSugar();
    }

    /**
     * @return bool
     * @see CliUpgrader::healthcheck()
     */
    public function healthcheck()
    {
        $this->initSugar();
        return parent::healthcheck();
    }

    /**
     * @return string
     * @see UpgradeDriver::getPackageUid()
     */
    protected function getPackageUid()
    {
        return md5($this->context['post_template']);
    }

    /**
     * @param string $dir Sugar directory
     * @param string $stage
     * @return array
     * @see UpgradeDriver::getScripts()
     *
     */
    protected function getScripts($dir, $stage)
    {
        //For the pre stage step, use the post template location
        if ($stage == 'pre') {
            $dir = $this->context['post_template'];
            $this->log("Pre stage will get scripts from location: $dir");
        }

        return parent::getScripts($dir, $stage);
    }

    /**
     * @see UpgradeDriver::doHealthcheck()
     */
    protected function doHealthcheck()
    {
        //Inherits from CLI Parent
        $parentHealthCheckPass = parent::doHealthcheck();

        //Failures found
        if (!$parentHealthCheckPass && array_key_exists('force', $this->context) && $this->context['force']) {
            echo "WARNING: Health check failed (red flags). Please refer to the log file {$this->context['log']}\n";
            echo "         Option force was specified so continuing in spite of failed healthcheck\n";
            // ignore them and move on
            $this->success = true;
            return true;
        }
        return $parentHealthCheckPass;
    }
}

if (empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) {
    return;
}

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die('This is command-line only script');
}
$upgrader = new ShadowUpgrader();
$upgrader->start();
