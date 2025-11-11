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

class SugarLicensing
{
    // @codingStandardsIgnoreLine PSR2.Classes.PropertyDeclaration.Underscore
    protected $_server = 'https://authenticate.sugarcrm.com';

    /**
     * @var resource
     */
    // @codingStandardsIgnoreLine PSR2.Classes.PropertyDeclaration.Underscore
    protected $_curl;

    /**
     * Constructor Method with Curl URL
     *
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Destructor Method
     * This will clear out the curl connect if it's still alive.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Start the Curl Connection Process and create the curl object so it's ready for a connection
     *
     * @return void;
     */
    public function connect()
    {
        if ($this->isConnected()) {
            // we are still connected return nothing;
            return;
        }

        $curl = curl_init();

        // Tell curl not to return headers, but do return the response
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $this->_curl = $curl;
    }

    /**
     * Test if curl is setup and ready for a connection
     *
     * @return bool
     */
    public function isConnected()
    {
        return is_resource($this->_curl);
    }

    /**
     * Disconnect from curl if an object already exists
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            // ok we have a curl so kill the connection
            curl_close($this->_curl);
        }
    }

    /**
     * Make a request to the end point on the licensing server
     *
     * @param $endpoint
     * @param $payload
     * @param bool $doDecode
     * @param int $timeout
     * @return array
     */
    public function request($endpoint, $payload, $doDecode = true, $timeout = 30)
    {
        // make sure that the first char is a "/"
        if (substr($endpoint, 0, 1) != '/') {
            $endpoint = '/' . $endpoint;
        }

        $endpoint = $this->getServerName() . $endpoint;
        curl_setopt($this->_curl, CURLOPT_URL, $endpoint);

        curl_setopt($this->_curl, CURLOPT_CONNECTTIMEOUT, $timeout);

        // proxy settings
        $proxy_config = Administration::getSettings('proxy');
        if (!empty($proxy_config)
            && !empty($proxy_config->settings['proxy_on'])
            && $proxy_config->settings['proxy_on'] == 1) {
            curl_setopt($this->_curl, CURLOPT_PROXY, $proxy_config->settings['proxy_host']);
            curl_setopt($this->_curl, CURLOPT_PROXYPORT, $proxy_config->settings['proxy_port']);
            if (!empty($proxy_config->settings['proxy_auth'])) {
                curl_setopt(
                    $this->_curl,
                    CURLOPT_PROXYUSERPWD,
                    $proxy_config->settings['proxy_username'] . ':' . $proxy_config->settings['proxy_password']
                );
            }
        }

        if (!empty($payload)) {
            if (is_array($payload)) {
                $payload = json_encode($payload);
            }

            curl_setopt($this->_curl, CURLOPT_POST, true);
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $payload);
        }

        $response = $this->_reqeust();

        if ($doDecode && $response != false) {
            return json_decode($response, true);
        }

        return $response;
    }

    /**
     * Run the curl exec
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    private function _reqeust()
    {
        $results = curl_exec($this->_curl);

        if ($results === false) {
            $GLOBALS['log']->error('Sugar Licensing encountered an error: ' . curl_error($this->_curl));
        }

        return $results;
    }

    /**
     * get Sugar licensing server name
     */
    protected function getServerName()
    {
        global $sugar_config;
        if (isset($sugar_config['license_server'])) {
            return rtrim(trim($sugar_config['license_server']), '/');
        }

        return $this->_server;
    }
}
