<?php

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
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

require_once 'service/core/SugarWebService.php';
require_once 'service/core/SugarWebServiceImpl.php';

/**
 * This is an abstract class for the soap implementation for using SOAP. This class is responsible for making
 * all SOAP call by passing the client's request to SOAP server and sending response back to client
 * @api
 */
abstract class SugarSoapService extends SugarWebService
{
    use SoapLogTrait;
    /**
     * @var mixed|bool
     */
    public $in_service;

    /**
     * @var SoapFault
     */
    public $fault;
    protected $soap_version = SOAP_1_1;
    protected $namespace = 'http://www.sugarcrm.com/sugarcrm';
    protected $implementationClass = 'SugarWebServiceImpl';
    protected $registryClass = '';
    protected $soapURL = '';

    /**
     * This is the constructor. It creates an instance of SOAP server.
     *
     * @param String $url - This is the soap URL
     * @param String|null $wsdl - WSDL file, or non WSDL mode
     * @access public
     */
    public function __construct($url, $wsdl = null)
    {
        $this->getLogger()->info('Begin: SugarSoapService->__construct');
        $this->setObservers();
        $this->soapURL = $url;
        $options = [
            'soap_version' => SOAP_1_1,
            'uri' => $url,
        ];
        $this->server = new \SoapServer($wsdl, $options);
        $this->server->setClass($this->getRegisteredImplClass());
        $this->getLogger()->info('End: SugarSoapService->__construct');
    }

    /**
     * This method sets the soap server object on all the observers
     * @access public
     */
    public function setObservers()
    {
        global $observers;
        if (!empty($observers)) {
            foreach ($observers as $observer) {
                if (method_exists($observer, 'set_soap_server')) {
                    $observer->set_soap_server($this->server);
                }
            }
        }
    } // fn

    /**
     * This method returns the soapURL
     *
     * @return String - soapURL
     * @access public
     */
    public function getSoapURL()
    {
        return $this->soapURL;
    }

    public function getSoapVersion()
    {
        return $this->soap_version;
    }

    /**
     * This method returns the namespace
     *
     * @return String - namespace
     * @access public
     */
    public function getNameSpace()
    {
        return $this->namespace;
    }

    /**
     * This mehtod returns registered implementation class
     *
     * @return String - implementationClass
     * @access public
     */
    public function getRegisteredImplClass()
    {
        return $this->implementationClass;
    }

    /**
     * This mehtod returns registry class
     *
     * @return String - registryClass
     * @access public
     */
    public function getRegisteredClass()
    {
        return $this->registryClass;
    }

    /**
     * This mehtod returns server
     *
     * @return String -server
     * @access public
     */
    public function getServer()
    {
        return $this->server;
    } // fn

    /**
     * Fallback function to catch unexpected failure in SOAP
     */
    public function shutdown()
    {
        if ($this->in_service) {
            $out = ob_get_contents();
            ob_end_clean();
            $this->getLogger()->fatal('SugarSoapService->shutdown: service died unexpectedly');
            $this->server->fault('-1', 'Unknown error in SOAP call: service died unexpectedly', '', $out);
        }
    }

    /**
     * It passes request data to SOAP server and sends response back to client
     * @access public
     */
    public function serve()
    {
        $this->getLogger()->info('Begin: SugarSoapService->serve');
        ob_clean();
        $this->in_service = true;
        register_shutdown_function([$this, 'shutdown']);
        ob_start();
        $this->server->handle();
        $this->in_service = false;
        ob_end_flush();
        flush();
        $this->getLogger()->info('End: SugarSoapService->serve');
    } // fn

    /**
     * This function registers implementation class name with SOAP so when SOAP makes a call to a funciton,
     * it will be made on this class object
     *
     * @param String $implementationClass
     * @access public
     */
    public function registerImplClass($implementationClass)
    {
        $this->getLogger()->info('Begin: SugarSoapService->registerImplClass');
        $this->server->setClass($implementationClass);
        $this->getLogger()->info('End: SugarSoapService->registerImplClass');
    } // fn

    /**
     * This function sets the fault object on the SOAP
     *
     * @param SoapError $errorObject - This is an object of type SoapError
     * @access public
     */
    public function error($errorObject)
    {
        $this->getLogger()->fatal('Begin: SugarSoapService->error');

        // report all failures as caused by client since we don't have the needed attribute
        // in existing error definitions
        $this->fault = new SoapFault('soapenv:Client', $errorObject->getFaultCode() . ': ' . $errorObject->getName(), '', $errorObject->getDescription());
        $this->getLogger()->fatal('End: SugarSoapService->error');
    }

    /**
     * This method registers all the functions which you want to be available for SOAP.
     *
     * @param String $function - name of the function
     * @access public
     */
    public function registerFunction($function)
    {
        if (in_array($function, $this->excludeFunctions)) {
            return;
        }
        $this->server->addFunction($function);
    }

    /**
     * Sets the name of the registry class
     *
     * @param String $registryClass
     * @access public
     */
    public function registerClass($registryClass)
    {
        $this->getLogger()->info('Begin: SugarSoapService->registerClass');
        $this->registryClass = $registryClass;
        $this->getLogger()->info('End: SugarSoapService->registerClass');
    }
} // class
