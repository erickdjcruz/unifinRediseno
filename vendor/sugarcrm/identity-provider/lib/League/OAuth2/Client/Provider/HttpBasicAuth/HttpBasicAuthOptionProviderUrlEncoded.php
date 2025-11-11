<?php

namespace Sugarcrm\IdentityProvider\League\OAuth2\Client\Provider\HttpBasicAuth;

use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;

class HttpBasicAuthOptionProviderUrlEncoded extends HttpBasicAuthOptionProvider
{
    /**
     * @inheritdoc
     */
    public function getAccessTokenOptions($method, array $params)
    {
        $options = parent::getAccessTokenOptions($method, $params);

        $encodedCredentials = base64_encode(
            sprintf('%s:%s', urlencode($params['client_id']), urlencode($params['client_secret']))
        );

        $options['headers']['Authorization'] = 'Basic ' . $encodedCredentials;

        return $options;
    }
}
