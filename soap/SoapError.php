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

require_once 'soap/SoapErrorDefinitions.php';

class SoapError
{
    public $name;
    public $number;
    public $description;

    public function __construct()
    {
        $this->set_error('no_error');
    }

    public function set_error($error_name)
    {
        global $error_defs;
        if (!isset($error_defs[$error_name])) {
            $this->name = 'An Undefined Error - ' . $error_name . ' occurred';
            $this->number = '-1';
            $this->description = 'There is no error definition for ' . $error_name;
        } else {
            $this->name = $error_defs[$error_name]['name'];
            $this->number = $error_defs[$error_name]['number'];
            $this->description = $error_defs[$error_name]['description'];
        }
    }

    public function get_soap_array()
    {
        return ['number' => $this->number,
            'name' => $this->name,
            'description' => $this->description];
    }

    public function getName()
    {
        return $this->name;
    } // fn

    public function getFaultCode()
    {
        return $this->number;
    } // fn

    public function getDescription()
    {
        return $this->description;
    } // fn

    /**
     * serialize a fault
     *
     * @param SoapFault $fault
     * @return string The serialization of the fault instance.
     * @access public
     */
    public function serialize(SoapFault $fault): string
    {
        return <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="{$GLOBALS['sugar_config']['site_url']}/service/soap-envelope.xsd"
      xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:ns1="http://www.sugarcrm.com/sugarcrm">
<soapenv:Body>
    <soapenv:Fault>
        <faultcode>$fault->faultcode</faultcode>
        <faultstring>
            {$fault->getMessage()}
        </faultstring>
        <detail>
            <ns1:FaultResponse xmlns:ns1="http://www.sugarcrm.com/sugarcrm">
                <errorCode>
                    {$fault->getCode()}
                </errorCode>
                <errorDetail>
                    {$fault->getTraceAsString()}
                </errorDetail>
            </ns1:FaultResponse>
        </detail>
    </soapenv:Fault>
</soapenv:Body>
</soapenv:Envelope>
EOT;
    }
}
