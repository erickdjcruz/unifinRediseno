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

namespace Sugarcrm\Sugarcrm\SugarConnect\Client\Http;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @deprecated Will be removed in the next release.
 */
class AuthMiddleware
{
    /**
     * OAuth 2.0 service provider client using Bearer token authentication.
     *
     * @var AbstractProvider
     */
    private $provider;

    /**
     * Access tokens are cached for reuse.
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * Guzzle middleware to add OAuth 2.0 authentication using a client
     * credentials grant.
     *
     * @param AbstractProvider $provider OAuth 2.0 service provider client using
     *                                   Bearer token authentication.
     * @param CacheInterface $cache Access tokens are cached for reuse.
     */
    public function __construct(AbstractProvider $provider, CacheInterface $cache)
    {
        $this->provider = $provider;
        $this->cache = $cache;
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
            return $handler(
                $this->addAuthorizationHeader($request),
                $options
            )->then(
                function (ResponseInterface $response) use ($request, $options, $handler) {
                    if ($response->getStatusCode() == 401) {
                        // Force a new access token to be retrieved.
                        $this->cache->delete('sugar_connect_access_token');

                        return $handler(
                            $this->addAuthorizationHeader($request),
                            $options
                        );
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * Adds the Bearer token to the request.
     *
     * @param RequestInterface $request Add the token to this request.
     *
     * @return RequestInterface
     */
    private function addAuthorizationHeader(RequestInterface $request): RequestInterface
    {
        return $request->withAddedHeader(
            'Authorization',
            'Bearer ' . $this->getAccessToken()
        );
    }

    /**
     * Obtains an access token using a client credentials grant.
     *
     * @return AccessToken
     */
    private function getAccessToken(): AccessToken
    {
        $token = $this->cache->get('sugar_connect_access_token');

        // Reuse the existing token.
        if ($token instanceof AccessToken && !$token->hasExpired()) {
            return $token;
        }

        // Get a new access token.
        $token = $this->provider->getAccessToken(
            'client_credentials',
            [
                'scope' => 'https://apis.sugarcrm.com/auth/bankshot.notify',
            ]
        );

        $this->cache->set('sugar_connect_access_token', $token);

        return $token;
    }
}
