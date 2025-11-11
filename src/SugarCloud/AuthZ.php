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

namespace Sugarcrm\Sugarcrm\SugarCloud;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthZ
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var int
     */
    private $cacheTTL;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var Discovery
     */
    private $disco;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthZ check endpoint
     */
    public const API_ENDPOINT = '/v1alpha/iam/authz/authorize-token';

    /**
     * AuthZ request timeout
     */
    public const REQUEST_TIMEOUT = 10;

    /**
     * AuthZ constructor.
     * @param array $cloudConfig
     * @param ContainerInterface $container
     * @param Client $httpClient
     * @param Discovery $disco
     */
    public function __construct(array $cloudConfig, ContainerInterface $container, Client $httpClient, Discovery $disco)
    {
        $this->cache = $container->get(CacheInterface::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->cacheKey = 'authz.' . $cloudConfig['tid'];
        $this->cacheTTL = $cloudConfig['caching']['ttl']['authz'] ?? 15 * 60;
        $this->httpClient = $httpClient;
        $this->disco = $disco;
    }

    /**
     * @param string $token
     * @param string $requestedResource
     * @param array $permissions
     * @return bool
     */
    public function checkPermission(string $token, string $requestedResource, array $permissions): bool
    {
        if (!$permissions) {
            throw new \InvalidArgumentException('Each action needs at least one permission to check for');
        }

        $cacheKey = md5($token) . $this->cacheKey;

        $authorized = $this->cache->get($cacheKey);
        if (!is_null($authorized)) {
            return $authorized;
        }

        $authZURL = $this->disco->getServiceUrl('iam-authz-http:v1alpha');
        if (!$authZURL) {
            return false;
        }

        $authZBody = [
            'requested_resource' => $requestedResource,
            'required_permissions' => $permissions,
            'token' => $token,
            'return_claims' => false,
        ];
        $response = $this->httpClient->post(
            $authZURL . static::API_ENDPOINT,
            [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'body' => json_encode($authZBody),
                'timeout' => static::REQUEST_TIMEOUT,
            ]
        );

        unset($authZBody['token']);

        $encodedRequest = json_encode($authZBody);

        $statusCode = $response->getStatusCode();
        if ($statusCode >= Response::HTTP_BAD_REQUEST) {
            $this->logger->error(
                sprintf('Request %s is not allowed by AuthZ. StatusCode: %d', $encodedRequest, $statusCode)
            );
            return false;
        }

        $data = $response->getBody()->getContents();
        try {
            $result = \GuzzleHttp\json_decode($data, true);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        if (isset($result['authorized'])) {
            if (!$result['authorized']) {
                $this->logger->error(
                    sprintf('Request %s is not allowed by AuthZ %s', $encodedRequest, $data)
                );
            }
            $this->cache->set($cacheKey, $result['authorized'], $this->cacheTTL);
            return $result['authorized'];
        }

        return false;
    }
}
