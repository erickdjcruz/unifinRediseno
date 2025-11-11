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

use DocuSign\eSign\Client\ApiClient;
use Sugarcrm\Sugarcrm\DocuSign\DocuSignUtils;

class DocuSignClient extends ApiClient
{
    /**
     * Returns the authorization URL to be used by the front-end for authorizing
     * a user with DocuSign servers.
     *
     * @return string|null the authorization URL
     */
    public function createAuthURL()
    {
        $config = DocuSignUtils::getDocuSignOauth2Config();
        $clientId = $config['properties']['integration_key'];
        $redirectUri = urlencode(rtrim(\SugarConfig::getInstance()->get('site_url'), '/') .
            '/oauth-handler/DocuSignOauth2Redirect');

        $scopes = ['signature'];
        $responseType = 'code';
        $urlAuthorize = $this->getAuthorizationUri($clientId, $scopes, $redirectUri, $responseType);

        return $urlAuthorize;
    }
}
