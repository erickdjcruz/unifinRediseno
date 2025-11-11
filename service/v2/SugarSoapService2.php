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

class SugarSoapService2 extends SugarSoapService
{
    /**
     * This function registers implementation class name with SOAP so when SOAP makes a call to a funciton,
     * it will be made on this class object
     *
     * @param String $implementationClass
     * @access public
     */
    public function registerImplClass($implementationClass)
    {
        $this->getLogger()->info('Begin: SugarSoapService2->registerImplClass');
        $this->server->setClass($implementationClass);
        $this->getLogger()->info('End: SugarSoapService2->registerImplClass');
    } // fn

    /**
     * It passes request data to SOAP server and sends response back to client
     * @access public
     */
    public function serve()
    {
        $this->getLogger()->info('Begin: SugarSoapService2->serve');
        ob_clean();
        $this->in_service = true;
        register_shutdown_function([$this, 'shutdown']);
        ob_start();
        $this->server->handle();
        $this->in_service = false;
        ob_end_flush();
        flush();
        $this->getLogger()->info('End: SugarSoapService2->serve');
    }


    /**
     * This method registers all the functions which you want to be available for SOAP.
     *
     * @param array $excludeFunctions - All the functions you don't want to register
     */
    public function register($excludeFunctions = [])
    {
        $this->getLogger()->info('Begin: SugarSoapService2->register');
        $this->excludeFunctions = $excludeFunctions;
        $registryObject = new $this->registryClass($this);
        $registryObject->register();
        $this->excludeFunctions = [];
        $this->getLogger()->info('End: SugarSoapService2->register');
    }
}
