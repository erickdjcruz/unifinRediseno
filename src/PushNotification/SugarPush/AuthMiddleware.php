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

namespace Sugarcrm\Sugarcrm\PushNotification\SugarPush;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\OAuth2\Client\Provider\IdmProvider;

class AuthMiddleware
{
    /**
     * OAuth 2.0 service provider client using Bearer token authentication.
     *
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * Access tokens are cached for reuse.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Guzzle middleware to add OAuth 2.0 authentication using a client
     * credentials grant.
     *
     */
    public function __construct()
    {
        $this->provider = new IdmProvider($this->getOAuth2Config());
        $this->cache = Container::getInstance()->get(CacheInterface::class);
    }

    /**
     * Guzzle middleware invocation to add OAuth 2.0 authentication. One retry
     * is executed when a 401 is encountered.
     *
     * @param callable $handler The next handler to invoke from the middleware
     *                          chain.
     *
     * @return \Closure
     */
    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if (empty($options['authorize_as_application'])) {
                $token = $this->getUserAccessToken();
            } else {
                $token = $this->getApplicationAccessToken();
            }

            return $handler(
                $this->addAuthorizationHeader($request, $token),
                $options
            )->then(
                function (ResponseInterface $response) use ($request, $options, $handler) {
                    if ($response->getStatusCode() == 401 && !empty($options['authorize_as_application'])) {
                        // Force a new access token to be retrieved.
                        $this->cache->delete('sugar_push_access_token');
                        $token = $this->getApplicationAccessToken();

                        return $handler(
                            $this->addAuthorizationHeader($request, $token),
                            $options
                        );
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * Returns an OAuth 2.0 configuration using the client ID and secret
     * provided by IDM.
     *
     * @return array Configuration expected by IdmProvider.
     */
    private function getOAuth2Config(): array
    {
        $idm = new IdmConfig(\SugarConfig::getInstance());
        return $idm->getIDMModeConfig();
    }

    /**
     * Adds the Bearer token to the request.
     *
     * @param RequestInterface $request Add the token to this request.
     *
     * @param string $token
     * @return RequestInterface
     */
    private function addAuthorizationHeader(RequestInterface $request, string $token): RequestInterface
    {
        return $request->withAddedHeader(
            'Authorization',
            'Bearer ' . $token
        );
    }

    /**
     * Returns current user's token
     */
    protected function getUserAccessToken(): string
    {
        $restService = new \RestService();
        $token = $restService->grabToken();

        return (string)$token;
    }

    /**
     * Returns the application level access token
     */
    protected function getApplicationAccessToken(): string
    {
        $token = $this->cache->get('sugar_push_access_token');

        // Reuse the existing token.
        if ($token instanceof AccessToken && !$token->hasExpired()) {
            return (string)$token;
        }

        // Get a new access token.
        $token = $this->provider->getAccessToken('client_credentials');

        $this->cache->set('sugar_push_access_token', $token);

        return (string)$token;
    }
}
