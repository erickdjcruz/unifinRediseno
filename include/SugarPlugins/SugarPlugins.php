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
 * Plugin management
 * @api
 */
class SugarPlugins
{
    /**
     * @const URL of the Sugar plugin server
     */
    private const SUGAR_PLUGIN_SERVER = 'https://www.sugarcrm.com/crm/plugin_service.php';

    /**
     * @var SoapClient
     */
    private $client;

    /**
     * Constructor
     *
     * Initializes the SOAP session
     */
    public function __construct()
    {
        $params = [
            'soap_version' => SOAP_1_1,
            'exceptions' => 0,
        ];

        $proxy_config = Administration::getSettings('proxy');
        if (!empty($proxy_config)
            && !empty($proxy_config->settings['proxy_on'])
            && $proxy_config->settings['proxy_on'] == 1) {
            $params['proxy_host'] = $proxy_config->settings['proxy_host'];
            $params['proxy_port'] = $proxy_config->settings['proxy_port'];
            if (!empty($proxy_config->settings['proxy_auth'])) {
                $params['proxy_login'] = $proxy_config->settings['proxy_username'];
                $params['proxy_password'] = $proxy_config->settings['proxy_password'];
            }
        }

        try {
            $this->client = new \SoapClient(self::SUGAR_PLUGIN_SERVER . '?wsdl', $params);
        } catch (SoapFault $e) {
            // don't assign client
        }
    }

    /**
     * Returns an array of available plugins to download for this instance
     *
     * @return array
     */
    public function getPluginList()
    {
        if (!$this->client) {
            return [];
        }

        if (empty($GLOBALS['license'])) {
            loadLicense();
        }

        $result = $this->client->get_plugin_list(
            $GLOBALS['license']->settings['license_key'] ?? null,
            $GLOBALS['sugar_version']
        );
        $result = object_to_array_deep($result);

        if (empty($result[0]['item'])) {
            return [];
        }

        return $result[0]['item'];
    }

    /**
     * Returns the download token for the given plugin
     *
     * @param string $plugin_id
     * @return string token
     */
    private function getPluginDownloadToken($plugin_id)
    {
        if (!$this->client) {
            return '';
        }

        $result = $this->client->get_plugin_token(
            $GLOBALS['license']->settings['license_key'],
            $GLOBALS['current_user']->id,
            $plugin_id
        );

        return $result->token;
    }

    /**
     * Downloads the plugin from Sugar using an HTTP redirect
     *
     * @param string $plugin_id
     */
    public function downloadPlugin($plugin_id)
    {
        $token = $this->getPluginDownloadToken($plugin_id);
        ob_clean();
        SugarApplication::redirect(self::SUGAR_PLUGIN_SERVER . '?token=' . urlencode($token));
    }

    /**
     * Returns a download link for the given plugin
     *
     * @param string $pluginId the plugin ID as stored on the plugin server
     * @return string
     */
    public function getPluginLink($pluginId)
    {
        $token = $this->getPluginDownloadToken($pluginId);
        return self::SUGAR_PLUGIN_SERVER . '?token=' . urlencode($token);
    }
}
