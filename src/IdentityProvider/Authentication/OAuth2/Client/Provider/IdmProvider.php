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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\OAuth2\Client\Provider;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Provider\GenericProvider as BasicGenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Sugarcrm\IdentityProvider\League\OAuth2\Client\Provider\HttpBasicAuth\HttpBasicAuthOptionProviderUrlEncoded;
use Sugarcrm\Sugarcrm\League\OAuth2\Client\Grant\JwtBearer;
use Sugarcrm\IdentityProvider\Utils\RetryHttpClientBuilder;

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Psr\SimpleCache\CacheInterface;

class IdmProvider extends BasicGenericProvider
{
    /**
     * Identity provider endpoint
     * @var string
     */
    protected $idpUrl;

    /**
     * Public/private keys endpoint
     * @var string
     */
    protected $urlKeys;

    /**
     * ID of key set to retrieve
     * @var string
     */
    protected $keySetId;

    /**
     * @var string
     */
    protected $responseError = 'error';

    /**
     * @var string
     */
    protected $responseErrorCode = 'code';

    /**
     * @var string
     */
    protected $responseErrorMessage = 'message';

    /**
     * @var string
     */
    protected $urlUserInfo;

    /**
     * @var string
     */
    protected $urlRevokeToken;

    /**
     * @var array
     */
    protected $caching = [];

    /**
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * @var array
     */
    protected $requestedOAuthScopes = [];

    /**
     * Adds HttpClient with retry policy.
     *
     * @inheritdoc
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if (!array_key_exists('httpClient', $collaborators)) {
            $collaborators['httpClient'] = $this->createHttpClient($options);
        }
        $collaborators['optionProvider'] = new HttpBasicAuthOptionProviderUrlEncoded();
        parent::__construct($options, $collaborators);
    }

    /**
     * Create HTTP Basic auth string
     * @return string
     */
    protected function getHttpBasicAuthHeader()
    {
        return 'Basic ' . base64_encode(sprintf('%s:%s', urlencode($this->clientId), urlencode($this->clientSecret)));
    }

    /**
     * @inheritdoc
     */
    protected function getRequiredOptions()
    {
        return array_merge(parent::getRequiredOptions(), [
            'clientId',
            'clientSecret',
            'urlKeys',
            'keySetId',
            'idpUrl',
            'urlUserInfo',
            'urlRevokeToken',
        ]);
    }

    /**
     * Allow to use specific handler.
     *
     * @inheritdoc
     */
    protected function getAllowedClientOptions(array $options)
    {
        return array_merge(
            parent::getAllowedClientOptions($options),
            ['handler', 'verify', 'headers']
        );
    }

    /**
     * Introspect token and return resource owner details
     * @param AccessToken $token
     * @return string
     * @throws \RuntimeException
     */
    public function introspectToken(AccessToken $token)
    {
        $cacheKey = 'oidc_introspect_token_' . hash('sha256', $token->getToken());
        $result = $this->getCache($cacheKey);
        if (!is_null($result)) {
            return $result;
        }
        $url = $this->getResourceOwnerDetailsUrl($token);
        $options = [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => $this->getHttpBasicAuthHeader(),
            ],
            'body' => $this->buildQueryString(['token' => $token->getToken()]),
        ];

        $request = $this->getRequestFactory()->getRequestWithOptions(self::METHOD_POST, $url, $options);
        $result = $this->getParsedResponse($request);
        $this->setCache($cacheKey, $result, 'introspectToken');
        return $result;
    }

    /**
     * Revoke token
     *
     * @param AccessToken $token
     * @return string
     */
    public function revokeToken(AccessToken $token)
    {
        $options = [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => $this->getHttpBasicAuthHeader(),
            ],
            'body' => $this->buildQueryString(['token' => $token->getToken()]),
        ];

        $request = $this->getRequestFactory()->getRequestWithOptions(
            self::METHOD_POST,
            $this->urlRevokeToken,
            $options
        );

        $result = $this->getParsedResponse($request);
        return $result;
    }

    /**
     * return user info
     * @param AccessToken $token
     * @return mixed
     */
    public function getUserInfo(AccessToken $token)
    {
        $cacheKey = 'oidc_user_info_' . hash('sha256', $token->getToken());
        $result = $this->getCache($cacheKey);
        if (!is_null($result)) {
            return $result;
        }
        $authHeaders = $this->getAuthorizationHeaders($token->getToken());
        $options = [
            'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ] + $authHeaders,
        ];
        $request = $this->getRequestFactory()->getRequestWithOptions(
            self::METHOD_POST,
            $this->urlUserInfo,
            $options
        );
        $result = $this->getParsedResponse($request);
        $this->setCache($cacheKey, $result, 'userInfo');
        return $result;
    }

    /**
     * Remote user authentication on IdP
     *
     * @param string $username
     * @param string $password
     * @param string $tenant Tenant SRN
     * @return array
     */
    public function remoteIdpAuthenticate($username, $password, $tenant)
    {
        $cacheKey = 'remote_idp_auth_'
            . hash('sha512', $username . $password . $tenant);
        $parsedResponse = $this->getCache($cacheKey);
        if (!empty($parsedResponse)) {
            return $parsedResponse;
        }
        $accessToken = $this->getAccessToken(
            'client_credentials',
            ['scope' => 'https://apis.sugarcrm.com/auth/iam.password']
        );
        $authHeaders = $this->getAuthorizationHeaders($accessToken->getToken());
        $options = [
            'headers' => [
                    'content-type' => 'application/x-www-form-urlencoded',
                ] + $authHeaders,
            'body' => $this->buildQueryString(['user_name' => $username, 'password' => $password, 'tid' => $tenant]),
        ];

        $request = $this->getRequestFactory()->getRequestWithOptions(
            self::METHOD_POST,
            $this->idpUrl . '/authenticate',
            $options
        );
        $parsedResponse = $this->getParsedResponse($request);
        $this->setCache($cacheKey, $parsedResponse, 'remoteIdpResponseParsed');
        return $parsedResponse;
    }


    /**
     * Obtaining access token through JWT bearer flow.
     *
     * @param $assertion
     * @return AccessToken
     */
    public function getJwtBearerAccessToken($assertion)
    {
        return $this->getAccessToken(
            new JwtBearer(),
            ['scope' => implode($this->getScopeSeparator(), $this->requestedOAuthScopes), 'assertion' => $assertion]
        );
    }

    /**
     * Get oauth2 public or private key from specified endpoint.
     *
     * @return array
     * @throws RequestException
     * @throws \UnexpectedValueException
     */
    public function getKeySet()
    {
        $cacheKey = 'oidc_key_set_' . $this->keySetId;
        $keySet = $this->getCache($cacheKey);
        if (is_null($keySet)) {
            $accessToken = $this->getAccessToken('client_credentials', ['scope' => 'hydra.keys.get']);
            $keyRequest = $this->getAuthenticatedRequest(
                self::METHOD_GET,
                $this->urlKeys,
                $accessToken,
                ['scope' => 'hydra.keys.get']
            );
            $keyResponse = $this->getParsedResponse($keyRequest);

            if (!isset($keyResponse['keys'])) {
                throw new \UnexpectedValueException('Keys not found');
            }
            $keySet = $keyResponse['keys'];
            $this->setCache($cacheKey, $keySet, 'keySet');
        }

        return [
            'keys' => $keySet,
            'keySetId' => $this->keySetId,
            'clientId' => $this->clientId,
        ];
    }

    /**
     * Creates HttpClient with retry policy.
     *
     * @param array $config
     * @return ClientInterface
     */
    protected function createHttpClient(array $config): ClientInterface
    {
        $options = [];
        if (isset($config['http_client']['retry_count'])) {
            $options['retry_count'] = (int)$config['http_client']['retry_count'];
        }
        $options['delay_strategy'] = $config['http_client']['delay_strategy'] ?? RetryHttpClientBuilder::DELAY_STRATEGY_LINEAR;

        $proxyConfig = $this->getHTTPClientProxy();
        if (!empty($proxyConfig)) {
            $options['proxy'] = $proxyConfig;
        }

        if (isset($config['http_client']['verify'])) {
            $options['verify'] = $config['http_client']['verify'];
        }

        if (isset($config['http_client']['headers'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], $config['http_client']['headers']);
        }

        return RetryHttpClientBuilder::getClient(
            array_intersect_key($options, array_flip($this->getAllowedClientOptions($options)))
        );
    }

    /**
     * Return HTTP client proxy
     *
     * @return string
     */
    protected function getHTTPClientProxy(): string
    {
        $url = '';
        $config = \Administration::getSettings('proxy');
        if (!empty($config->settings) && !empty($config->settings['proxy_on'])) {
            $url = $config->settings['proxy_host'] . ':' . $config->settings['proxy_port'];
            if (!empty($config->settings['proxy_auth'])) {
                $url = $config->settings['proxy_username'] . ':' . $config->settings['proxy_password'] . '@' . $url;
            }
        }
        return $url;
    }

    /**
     * @inheritdoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!is_array($data) && (string)$response->getBody() !== '') {
            throw new IdentityProviderException(
                'Invalid STS response ' . var_export($data, true),
                $response->getStatusCode(),
                $data
            );
        }

        if (!empty($data[$this->responseError]) && is_array($data[$this->responseError])) {
            $error = $data[$this->responseError];
            $message = !empty($error[$this->responseErrorMessage]) ? $error[$this->responseErrorMessage] : '';
            $code = !empty($error[$this->responseErrorCode]) ? $error[$this->responseErrorCode] : '';
            throw new IdentityProviderException($message, $code, $data);
        }
        return parent::checkResponse($response, $data);
    }

    /**
     * Get SugarCache instance.
     *
     * @return CacheInterface
     */
    protected function getSugarCache(): CacheInterface
    {
        return Container::getInstance()->get(CacheInterface::class);
    }

    /**
     * Set value to cache.
     * If ttlName is not found in config or is <= 0  value won't be put to cache.
     *
     * @param string $key
     * @param mixed $value
     * @param string $ttlName Value from idm_mode config that is responsible for specific request's cached value TTL
     */
    protected function setCache($key, $value, $ttlName)
    {
        $ttl = intval($this->caching['ttl'][$ttlName] ?? 0);
        if ($ttl > 0) {
            $this->getSugarCache()->set($key, $value, $ttl);
        }
    }

    /**
     * Get value from cache by key.
     *
     * @param string $key
     * @return mixed|null
     */
    protected function getCache($key)
    {
        return $this->getSugarCache()->get($key);
    }

    /**
     * @inheritdoc
     */
    public function getScopeSeparator()
    {
        return $this->scopeSeparator ?? parent::getScopeSeparator();
    }
}
