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

class EAPMViewDropboxOauth2Redirect extends SugarView
{
    use Oauth2RedirectTrait;

    /**
     * @var string $context the context in which this redirect URL was called
     */
    private $context;

    /**
     * @var ExternalAPIBase $api the API object used to communicate with Google
     */
    private $api;

    private $templateFile = 'modules/EAPM/tpls/DropboxOauth2Redirect.tpl';

    /**
     * Authenticates a Dropbox authorization code with the Dropbox servers
     *
     * @return bool|string
     */
    protected function authenticate()
    {
        if (!isset($_GET['code'])) {
            return false;
        }

        $this->api = new ExtAPIDropbox();

        $tokenData = $this->api->authenticate($_GET['code']);

        return $tokenData;
    }

    /**
     * Parses the authentication token data received from Dropbox and builds a
     * response object that will be sent to the frontend
     *
     * @param $tokenData
     * @return array
     */
    protected function buildResponse($tokenData): array
    {
        if (empty($tokenData)) {
            return [
                'result' => false,
            ];
        }

        // Build a basic response object indicating authentication success
        // tokenData now contains eapmId, access_token and refresh_token
        $response = [
            'result' => true,
            'hasRefreshToken' => isset($tokenData['refresh_token']),
            'access_token' => $tokenData,
        ];

        return $response;
    }
}
