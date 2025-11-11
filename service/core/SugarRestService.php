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
require_once 'service/core/SugarRestServiceImpl.php';

/**
 * Base class for rest services
 *
 */
class SugarRestService extends SugarWebService
{
    /**
     * @var string|mixed
     */
    public $responseClass;
    /**
     * @var string|mixed
     */
    public $serverClass;
    /**
     * @var mixed|object
     */
    public $implementation;
    /**
     * @var mixed|string
     */
    public $registryClass;
    protected $implementationClass = 'SugarRestServiceImpl';
    protected $restURL = '';
    protected $registeredFunc = [];

    /**
     * Get Sugar REST class name for input/return type
     *
     * @param string $name
     * @return string
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getTypeName($name)
    {
        if (empty($name)) {
            return 'SugarRest';
        }

        $name = clean_string($name, 'ALPHANUM');
        $type = '';
        switch (strtolower($name)) {
            case 'json':
                $type = 'JSON';
                break;
            case 'rss':
                $type = 'RSS';
                break;
            case 'serialize':
                $type = 'Serialize';
                break;
        }
        $classname = "SugarRest$type";
        if (!file_exists('service/core/REST/' . $classname . '.php')) {
            return 'SugarRest';
        }
        return $classname;
    }

    /**
     * Constructor.
     *
     * @param String $url - REST url
     */
    public function __construct($url)
    {
        $GLOBALS['log']->info('Begin: SugarRestService->__construct');
        $this->restURL = $url;

        $this->responseClass = $this->_getTypeName(@$_REQUEST['response_type']);
        $this->serverClass = $this->_getTypeName(@$_REQUEST['input_type']);
        $GLOBALS['log']->info('SugarRestService->__construct serverclass = ' . $this->serverClass);
        require_once 'service/core/REST/' . $this->serverClass . '.php';
        $GLOBALS['log']->info('End: SugarRestService->__construct');
    } // ctor

    /**
     * This method registers all the functions you want to expose as services with REST
     *
     * @param String $function - name of the function
     * @param Array $input - assoc array of input values: key = param name, value = param type
     * @param Array $output - assoc array of output values: key = param name, value = param type
     * @access public
     */
    public function registerFunction($function, $input, $output)
    {
        if (in_array($function, $this->excludeFunctions)) {
            return;
        }
        $this->registeredFunc[$function] = ['input' => $input, 'output' => $output];
    } // fn

    /**
     * It passes request data to REST server and sends response back to client
     * @access public
     */
    public function serve()
    {
        $GLOBALS['log']->info('Begin: SugarRestService->serve');
        require_once 'service/core/REST/' . $this->responseClass . '.php';
        $response = $this->responseClass;

        $responseServer = new $response($this->implementation);
        $this->server->faultServer = $responseServer;
        $responseServer->faultServer = $responseServer;
        $responseServer->generateResponse($this->server->serve());
        $GLOBALS['log']->info('End: SugarRestService->serve');
    } // fn

    /**
     * Enter description here...
     *
     * @param Array $excludeFunctions - All the functions you don't want to register
     */
    public function register($excludeFunctions = [])
    {
    } // fn

    /**
     * This mehtod returns registered implementation class
     *
     * @return String - implementationClass
     * @access public
     */
    public function getRegisteredImplClass()
    {
        return $this->implementationClass;
    } // fn

    /**
     * This mehtod returns registry class
     *
     * @return String - registryClass
     * @access public
     */
    public function getRegisteredClass()
    {
        return $this->registryClass;
    } // fn

    /**
     * Sets the name of the registry class
     *
     * @param String $registryClass
     * @access public
     */
    public function registerClass($registryClass)
    {
        $this->registryClass = $registryClass;
    }

    /**
     * This function registers implementation class name and creates an instance of rest implementation class
     * it will be made on this class object
     *
     * @param String $implementationClass
     * @access public
     */
    public function registerImplClass($className)
    {
        $GLOBALS['log']->info('Begin: SugarRestService->registerImplClass');
        $this->implementationClass = $className;
        $this->implementation = new $this->implementationClass();
        $this->server = new $this->serverClass($this->implementation);
        $GLOBALS['log']->info('End: SugarRestService->registerImplClass');
    } // fn

    /**
     * This function sets the fault object on the REST
     *
     * @param SoapError $errorObject - This is an object of type SoapError
     * @access public
     */
    public function error($errorObject)
    {
        $GLOBALS['log']->info('Begin: SugarRestService->error');
        $this->server->fault($errorObject);
        $GLOBALS['log']->info('End: SugarRestService->error');
    } // fn

    /**
     * This mehtod returns server
     *
     * @return String - server
     * @access public
     */
    public function getServer()
    {
        return $this->server;
    } // fn
}
