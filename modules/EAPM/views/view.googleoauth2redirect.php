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

class EAPMViewGoogleOauth2Redirect extends SugarView
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

    private $templateFile = 'modules/EAPM/tpls/GoogleOauth2Redirect.tpl';

    private $dataSource = 'googleEmailRedirect';

    /**
     * Authenticates a Google authorization code with Google servers, storing
     * any resulting token information in the EAPM table
     *
     * @return bool|string
     */
    protected function authenticate()
    {
        if (!isset($_GET['code'])) {
            return false;
        }

        switch ($this->context) {
            case 'email':
                $this->api = new ExtAPIGoogleEmail();
                break;
            default:
                $this->api = new ExtAPIGoogle();
                break;
        }
        return $this->api->authenticate($_GET['code']);
    }

    /**
     * Parses the authentication token data received from Google and builds a
     * response object that will be sent to the frontend
     *
     * @param $tokenData
     * @return array
     */
    protected function buildResponse($tokenData): array
    {
        switch ($this->context) {
            case 'email':
                $response = $this->buildEmailContextResponse($tokenData);
                break;
            default:
                $response = $this->buildBasicResponse($tokenData);
                break;
        }
        return $response;
    }

    /**
     * Constructs a basic response object that indicates the success status of
     * the token authentication
     *
     * @param string $tokenJSON the JSON token string received from Google
     * @return array
     */
    protected function buildBasicResponse($tokenJSON)
    {
        if (empty($tokenJSON)) {
            return [
                'result' => false,
            ];
        }

        // Build a basic response object indicating authentication success
        $token = json_decode($tokenJSON, true);
        $hasFreshAccessToken = isset($token['created']) &&
            isset($token['expires_in']) &&
            $this->isTokenExpired((int)$token['expires_in'], (int)$token['created']);
        $response = [
            'result' => true,
            'hasFreshAccessToken' => $hasFreshAccessToken,
            'hasRefreshToken' => isset($token['refresh_token']),
            'dataSource' => 'googleOauthRedirect',
        ];

        return $response;
    }

    private function isTokenExpired(int $expiresIn, int $created): bool
    {
        return time() - $expiresIn < $created;
    }
}
