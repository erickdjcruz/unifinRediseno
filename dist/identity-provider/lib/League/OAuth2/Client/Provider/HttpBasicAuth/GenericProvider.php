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

namespace Sugarcrm\IdentityProvider\League\OAuth2\Client\Provider\HttpBasicAuth;

use League\OAuth2\Client\Provider\GenericProvider as BasicGenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class GenericProvider extends BasicGenericProvider
{
    /**
     * @var string
     */
    protected $urlIntrospectToken;

    /**
     * @var string
     */
    protected $urlRevokeToken;

    /**
     * @var \Psr\Log\LoggerInterface;
     */
    protected $logger;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $collaborators['optionProvider'] = new HttpBasicAuthOptionProviderUrlEncoded();
        parent::__construct($options, $collaborators);
    }

    /**
     * Reads access token from the local file and returns it if found.
     *
     * @inheritdoc
     */
    public function getAccessToken($grant, array $options = [])
    {
        $tokenData = $this->getAccessTokenFileData();
        if (array_key_exists('access_token', $tokenData)) {
            return new AccessToken($tokenData);
        }
        $this->logger->warning("Failed to read file '{file_name}' with access_token. Using direct request for it.", [
            'file_name' => $this->accessTokenFile,
            'tags' => ['IdM.oauth.authentication'],
        ]);
        return parent::getAccessToken($grant, $options);
    }

    /**
     * Checks the response. Triggers token refresh if token is expired.
     *
     * @inheritdoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        // ToDo: STS sends 500 if token is expired. We should wait/fix for proper response code.
        if (isset($data['error']['code']) && isset($data['error']['message']) &&
            $data['error']['code'] == 500 &&
            strpos($data['error']['message'], 'request is not allowed') !== false) {
            $this->refreshAccessToken();
        }

        if (isset($data['error']) && \is_array($data['error'])) {
            $data['code'] = $data['error']['code'] ?? 0;
            $data['error'] = $data['error']['message'] ?? '';
        }

        return parent::checkResponse($response, $data);
    }

    /**
     * @inheritdoc
     */
    protected function getRequiredOptions()
    {
        return array_merge(
            parent::getRequiredOptions(),
            ['clientId', 'clientSecret', 'accessTokenFile', 'accessTokenRefreshUrl']
        );
    }

    /**
     * Introspect token and return resource owner details
     * @param AccessToken $token
     * @throws \RuntimeException
     * @return array
     */
    public function introspectToken(AccessToken $token)
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'token' => $token->getToken(),
        ];
        $options = $this->optionProvider->getAccessTokenOptions(self::METHOD_POST, $params);
        $options['headers']['Accept'] = 'application/json';

        $request = $this->getRequestFactory()->getRequestWithOptions(
            self::METHOD_POST,
            $this->urlIntrospectToken,
            $options
        );
        return $this->getParsedResponse($request);
    }

    /**
     * @param AccessToken $token
     * @return array|mixed|string
     */
    public function revokeToken(AccessToken $token)
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'token' => $token->getToken(),
        ];
        $options = $this->optionProvider->getAccessTokenOptions(self::METHOD_POST, $params);
        $options['headers']['Accept'] = 'application/json';

        $request = $this->getRequestFactory()->getRequestWithOptions(
            self::METHOD_POST,
            $this->urlRevokeToken,
            $options
        );
        return $this->getParsedResponse($request);
    }

    /**
     * Call token inject refresh token endpoint
     * @return boolean
     */
    public function refreshAccessToken()
    {
        $result = false;
        if (!empty($this->accessTokenRefreshUrl)) {
            // We do a fire-and-forget call to a refresh-token endpoint.
            $request = $this->getRequestFactory()->getRequestWithOptions(
                self::METHOD_GET,
                $this->accessTokenRefreshUrl,
                ['timeout' => 0.00001]
            );
            try {
                $this->getHttpClient()->send($request);
                $result = true;
                $this->logger->debug("The access_token is refreshed.", ['tags' => ['IdM.oauth.authentication']]);
            } catch (\Exception $e) {
                $this->logger->warning(
                    sprintf("Failed to send access_token refresh request. Error: %s", $e->getMessage()),
                    ['tags' => ['IdM.oauth.authentication']]
                );
            }
        } else {
            $this->logger->warning("Failed to trigger access_token refresh. Please set up Refresh URL in ENV", [
                'tags' => ['IdM.oauth.authentication'],
            ]);
        }
        return $result;
    }

    /**
     * Get OAuth2 client ID for the application
     *
     * @return string
     */
    public function getClientID(): string
    {
        // clientId from config takes precedence if set
        if ($this->clientId) {
            return $this->clientId;
        }
        // then we try to get it from injected access-token aux information
        $accessTokenData = $this->getAccessTokenFileData();
        if (array_key_exists('client_id', $accessTokenData)) {
            return $accessTokenData['client_id'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        // clientSecret from config takes precedence if set
        if ($this->clientSecret) {
            return $this->clientSecret;
        }
        // then we try to get it from injected access-token aux information
        $accessTokenData = $this->getAccessTokenFileData();
        if (array_key_exists('client_secret', $accessTokenData)) {
            return $accessTokenData['client_secret'];
        }

        return '';
    }

    /**
     * Get contents of accessToken file.
     * We assume this is a PHP file.
     * Also useful for testing.
     *
     * @return array
     */
    public function getAccessTokenFileData(): array
    {
        if (is_readable($this->accessTokenFile)) {
            $accessTokenData = include $this->accessTokenFile;
        }
        if (isset($accessTokenData) && is_array($accessTokenData)) {
            return $accessTokenData;
        }
        return [];
    }
}
