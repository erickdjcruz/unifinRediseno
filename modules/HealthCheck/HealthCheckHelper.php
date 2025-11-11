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
 * Class HealthCheckHelper
 */
class HealthCheckHelper
{
    protected static $instance;

    /**
     * Private constructor
     */
    private function __construct()
    {
    }

    /**
     * @return HealthCheckHelper
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @var array
     */
    protected $registry = [
        'web' => [
            'Scanner/ScannerWeb.php',
            'HealthCheckScannerWeb',
        ],
        'cli' => [
            'Scanner/ScannerCli.php',
            'HealthCheckScannerCli',
        ],
    ];

    /**
     * @return HealthCheckScannerWeb
     */
    public function getScanner($type)
    {
        if (isset($this->registry[$type])) {
            [$file, $class] = $this->registry[$type];
            require_once $file;
            return new $class();
        }

        return null;
    }

    /**
     * Notifies heartbeat server about the fact that heath check has been run.
     * Sends the licence key, the bucket and and the flag
     *
     * @param array $data
     * @return bool
     */
    public function pingHeartbeat($data)
    {
        $client = new SugarHeartbeatClient();
        $ping = $client->sugarPing();

        if (!is_soap_fault($ping)) {
            $data = array_merge($this->getSystemInfo()->getInfo(), $data);
            $result = $client->sugarHome($this->getLicenseKey(), $data);
            return !is_soap_fault($result);
        } else {
            $GLOBALS['log']->error('HealthCheck: ' . $ping->getMessage());
        }
        return false;
    }

    /**
     * Send health check log file to sugar
     * @param string $file
     * @return bool
     *
     * @deprecated since 7.9
     */
    public function sendLog($file)
    {
        $GLOBALS['log']->error('HealthCheck: Send logs to HealthCheck server is disabled.');
        return false;
    }

    /**
     * @return SugarSystemInfo
     */
    protected function getSystemInfo()
    {
        return SugarSystemInfo::getInstance();
    }

    /**
     * if SugarSystemInfo was loaded from instance instead health check,
     * 7.2.2.x instances method getLicenseKey doesn't exists in SugarSystemInfo
     * So in this case we have to load getLicenseInfo
     * @return string License key
     */
    protected function getLicenseKey()
    {
        if (method_exists('SugarSystemInfo', 'getLicenseKey')) {
            $licenseKey = $this->getSystemInfo()->getLicenseKey();
        } else {
            $licenseInfo = $this->getSystemInfo()->getLicenseInfo();
            $licenseKey = $licenseInfo['license_key'];
        }
        return $licenseKey;
    }
}
