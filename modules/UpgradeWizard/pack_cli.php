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


include_once __DIR__ . '/UpgradeDriver.php';

function packUpgradeWizardCli($phar, $params, $files = [])
{

    $defaults = [
        'version' => '7.6.1.0',
        'build' => '998',
    ];

    $params = array_merge($defaults, $params);

    file_put_contents(__DIR__ . '/version.json', json_encode($params));

    $chdir = __DIR__ . '/../..';

    $files = array_merge(
        [
            'modules/UpgradeWizard/SILENTUPGRADE.txt' => 'SILENTUPGRADE.txt',
            'modules/UpgradeWizard/UpgradeDriver.php' => 'UpgradeDriver.php',
            'modules/UpgradeWizard/CliUpgrader.php' => 'CliUpgrader.php',
            'modules/UpgradeWizard/version.json' => 'version.json',
            'include/SugarSystemInfo/SugarSystemInfo.php' => 'SugarSystemInfo.php',
            'include/SugarHeartbeat/SugarHeartbeatClient.php' => 'SugarHeartbeatClient.php',
        ],
        $files
    );

    foreach ($files as $file => $inArchive) {
        $phar->addFile($chdir . '/' . $file, $inArchive);
    }
    $pathToSmartyPhar = dirname(__DIR__) . '/HealthCheck/smarty.phar';
    foreach (new RecursiveIteratorIterator(new Phar($pathToSmartyPhar)) as $f) {
        if (false !== strpos($f, 'vendor/nikic/php-parser')) {
            continue;
        }
        $phar->addFile($f, str_replace('phar://' . $pathToSmartyPhar . '/', '', $f));
    }

    //Phar upgrader should be independent from Mango implementation and its dependencies
    if ($phar instanceof Phar) {
        $dir = dirname(__DIR__, 2);
        $parserPath = $dir . '/vendor/nikic/php-parser';

        /** @var SplFileInfo $f */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($parserPath)) as $f) {
            if ($f->isDir()) {
                continue;
            }

            $filePath = $f->getPathname();

            $phar->addFile($filePath, str_replace($dir . '/', '', $filePath));
        }
    }
}

if (empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) {
    return;
}

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die('This is command-line only script');
}

if (empty($argv[1])) {
    die("Use $argv[0] name (no zip or phar extension) [sugarVersion [buildNumber]]\n");
}

$pathinfo = pathinfo($argv[1]);

if (isset($pathinfo['extension']) && in_array($pathinfo['extension'], ['zip', 'phar'])) {
    $name = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'];
} else {
    $name = $argv[1];
}

$params = [];
if (isset($argv[2])) {
    $params['version'] = $argv[2];
}
if (isset($argv[3])) {
    $params['build'] = $argv[3];
}

$phar = new Phar($name . '.phar');

packUpgradeWizardCli($phar, $params);

$stub = <<<'STUB'
<?php
Phar::mapPhar();
set_include_path('phar://' . __FILE__ . PATH_SEPARATOR . get_include_path());
$basePath = 'phar://' . __FILE__ . '/';
require $basePath . 'vendor/autoload.php';
require $basePath . 'scanner/convert.php';
require $basePath . 'converter/Lexer.php';
require $basePath . 'converter/Service.php';
require_once "CliUpgrader.php"; $upgrader = new CliUpgrader(); $upgrader->start(); __HALT_COMPILER();
STUB;
$phar->setStub($stub);

$zip = new ZipArchive();
$zip->open($name . '.zip', ZipArchive::CREATE);

packUpgradeWizardCli($zip, $params);

$zip->close();

exit(0);
