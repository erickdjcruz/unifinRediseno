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

class EAPMViewMicrosoftOauth2Redirect extends SugarView
{
    use Oauth2RedirectTrait;

    /**
     * @var string $context the context in which this redirect URL was called
     */
    private $context;

    /**
     * @var ExternalAPIBase $api the API object used to communicate with Microsoft
     */
    private $api;

    private $templateFile = 'modules/EAPM/tpls/MicrosoftOauth2Redirect.tpl';

    private $dataSource = 'microsoftEmailRedirect';

    protected function authenticate()
    {
        if (!isset($_REQUEST['code'])) {
            return false;
        }

        switch ($this->context) {
            case 'email':
                $this->api = new ExtAPIMicrosoftEmail();
                break;
            case 'drive':
                $this->api = new ExtAPIMicrosoft();
                break;
            default:
                return false;
        }

        return $this->api->authenticate($_REQUEST['code']);
    }

    /**
     * Parses the authentication token data received from Microsoft and builds a
     * response object that will be sent to the frontend
     *
     * @param $tokenData
     * @return array
     */
    protected function buildResponse($tokenData): array
    {
        switch ($this->context) {
            case 'email':
            case 'drive':
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
     * @param string $token the token received from Microsoft
     * @return array
     */
    protected function buildBasicResponse($token)
    {
        if (empty($token)) {
            return [
                'result' => false,
                'dataSource' => 'microsoftOauthRedirect',
            ];
        }

        // Build a basic response object indicating authentication success
        $response = [
            'result' => true,
            'hasRefreshToken' => !empty($token->getRefreshToken()),
            'dataSource' => 'microsoftOauthRedirect',
        ];

        return $response;
    }
}
