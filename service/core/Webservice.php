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

class Webservice
{
    protected $webserviceUrl;
    protected $registryPath;
    protected $webservicePath;
    protected $webserviceClass;
    protected $webserviceImplPath;
    protected $webserviceImplClass;
    protected $registryClass;
    protected $rpcEncodedFile;

    public static function rest(string $version, ?string $style, ?string $use, ?string $wsdl)
    {
        self::applyRpcStyle($version, $style, $use, $wsdl);
        $service = new static();
        $service->rpcEncodedFile = "service/v{$version}/rpcEncoded.php";
        $service->webserviceUrl = $GLOBALS['sugar_config']['site_url'] . "/service/v{$version}/rest.php";
        $service->registryPath = "service/v{$version}/registry.php";
        $service->registryClass = ($version !== '2_1') ? 'registry' : 'registry_v' . $version;
        $service->webservicePath = 'service/core/SugarRestService.php';
        $service->webserviceClass = 'SugarRestService';
        $service->webserviceImplClass =
            ($version === '2')
                ? 'SugarRestServiceImpl'
                : 'SugarWebServiceImplv' . $version;
        $service->webserviceImplPath =
            ($version === '2')
                ? 'service/core/SugarRestServiceImpl.php'
                : "service/v{$version}/SugarWebServiceImplv{$version}.php";

        return $service;
    }

    public static function soap(string $version, ?string $style, ?string $use, ?string $wsdl)
    {
        self::applyRpcStyle($version, $style, $use, $wsdl);
        $service = new static();
        $service->rpcEncodedFile = "service/v{$version}/rpcEncoded.php";
        $service->webserviceUrl = $GLOBALS['sugar_config']['site_url'] . "/service/v{$version}/soap.php";
        $service->registryPath = "service/v{$version}/registry.php";
        $service->registryClass = ($version === '2') ? 'registry' : 'registry_v' . $version;
        $service->webservicePath = 'service/v2/SugarSoapService2.php';
        $service->webserviceClass = 'SugarSoapService2';
        $service->webserviceImplClass =
            ($version === '2')
                ? 'SugarWebServiceImpl'
                : 'SugarWebServiceImplv' . $version;
        $service->webserviceImplPath =
            ($version === '2')
                ? 'service/core/SugarWebServiceImpl.php'
                : "service/v{$version}/SugarWebServiceImplv{$version}.php";

        return $service;
    }

    public static function applyRpcStyle(string $version, ?string $style, ?string $use, ?string $wsdl): void
    {
        if ($wsdl && $style === 'rpc' && $use === 'literal') {
            header('Content-Type: text/xml; charset=utf-8');
            echo require "service/v{$version}/rpcLiteral.php";
            exit;
        } elseif ($wsdl && $style === 'rpc' && $use === 'encoded') {
            header('Content-Type: text/xml; charset=utf-8');
            echo require "service/v{$version}/rpcEncoded.php";
            exit;
        }
    }

    public function run(?string $requestMethod = ''): void
    {
        ob_start();
        require_once $this->webserviceImplPath;
        require $this->webservicePath;
        require $this->registryPath;

        $xml = require $this->rpcEncodedFile;
        $wsdl = 'data://text/plain;base64,' . base64_encode($xml);
        $service = new $this->webserviceClass($this->webserviceUrl, $wsdl);
        $service->registerClass($this->registryClass);
        $service->register();
        $service->registerImplClass($this->webserviceImplClass);

        SugarMetric_Manager::getInstance()->setTransactionName('soap_' . ($requestMethod));

        // set the service object in the global scope so that any error, if happens, can be set on this object
        global $service_object;
        $service_object = $service;

        $service->serve();
    }
}
