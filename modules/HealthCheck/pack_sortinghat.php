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

/**
 *
 * Pack standalone CLI HealthCheck Scanner for OnDemand. This is the same
 * as the previous SortingHat CLI script and can be executed as follows:
 *
 * php ScannerCli.php (for options see ScannerCli.php)
 *
 */

function packSortingHat(Phar $archive, $params, $installdefs = null, $internalPath = '')
{
    $defaults = [
        'version' => '7.5.0.0',
        'build' => '998',
    ];

    $params = array_merge($defaults, $params);

    file_put_contents(__DIR__ . '/Scanner/version.json', json_encode($params, true));

    $files = [
        'Scanner/Scanner.php',
        'Scanner/ScannerCli.php',
        'Scanner/ScannerWeb.php',
        'Scanner/ScannerMeta.php',
        'Scanner/Checks/Dbal.php',
        'Scanner/Checks/PasswordHashAlgo.php',
        'Scanner/package-checklist.php',
        'Scanner/version.json',
        'language/en_us.lang.php',
    ];
    foreach (new RecursiveIteratorIterator(new Phar(__DIR__ . '/smarty.phar')) as $f) {
        $archive->addFile($f, str_replace('phar://' . __DIR__ . '/smarty.phar/', '', $f));
    }
    foreach ($files as $file) {
        $archive->addFile(__DIR__ . '/' . $file, $internalPath . $file);
        if (is_array($installdefs)) {
            $installdefs['copy'][] = ['from' => "<basepath>/$internalPath$file", 'to' => $internalPath . $file];
        }
    }

    return [$archive, $installdefs];
}

if (empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) {
    return;
}

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die("This is a command-line only script\n");
}

if (empty($argv[1])) {
    die("Use $argv[0] healthcheck.phar [sugarVersion [buildNumber]]\n");
}

$phar = new Phar($argv[1]);

$params = [];
if (isset($argv[2])) {
    $params['version'] = $argv[2];
}
if (isset($argv[3])) {
    $params['build'] = $argv[3];
}

packSortingHat($phar, $params);

$stub = <<<'STUB'
<?php
Phar::mapPhar();
set_include_path('phar://' . __FILE__ . PATH_SEPARATOR . get_include_path());
$basePath = 'phar://' . __FILE__ . '/';
require $basePath . 'vendor/autoload.php';
require $basePath . 'scanner/convert.php';
require $basePath . 'converter/Lexer.php';
require $basePath . 'converter/Service.php';
require_once "Scanner/ScannerCli.php"; HealthCheckScannerCli::start($argv); __HALT_COMPILER();
STUB;
$phar->setStub($stub);

if (file_exists(__DIR__ . '/Scanner/version.json')) {
    unlink(__DIR__ . '/Scanner/version.json');
}

exit(0);
