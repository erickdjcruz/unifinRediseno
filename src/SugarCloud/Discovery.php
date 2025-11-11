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
use Sugarcrm\IdentityProvider\Srn\Converter;
use Symfony\Component\HttpFoundation\Response;

class Discovery
{
    /**
     * @var array
     */
    private $cloudConfig;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Discovery version
     */
    public const VERSION = 'v1';

    /**
     * Discovery request timeout
     */
    public const REQUEST_TIMEOUT = 2;

    /**
     * Discovery constructor.
     * @param array $cloudConfig
     * @param ContainerInterface $container
     * @param Client $httpClient
     */
    public function __construct(array $cloudConfig, ContainerInterface $container, Client $httpClient)
    {
        $this->cloudConfig = $cloudConfig;
        $this->cache = $container->get(CacheInterface::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->cacheKey = 'discovery.' . $this->cloudConfig['tid'];
        $this->cacheTTL = $this->cloudConfig['caching']['ttl']['discovery'] ?? 24 * 60 * 60;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getServiceUrl(string $name): ?string
    {
        if (empty($this->cloudConfig['discoveryUrl'])) {
            return null;
        }

        $url = $this->cloudConfig['discoveryUrl'];
        $servicesEndpoint = rtrim($url, '/') . '/' . static::VERSION . '/services';
        $services = $this->cache->get($this->cacheKey);
        if (empty($services)) {
            $response = $this->httpClient->get(
                $servicesEndpoint,
                ['timeout' => static::REQUEST_TIMEOUT]
            );
            if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST) {
                $this->logger->error('Can not get data from Discovery', ['endpoint' => $servicesEndpoint]);
                return null;
            }

            $data = $response->getBody()->getContents();
            try {
                $services = \GuzzleHttp\json_decode($data, true);
            } catch (\InvalidArgumentException $e) {
                $this->logger->error('Invalid Discovery response', ['data' => $data]);
                return null;
            }

            if (empty($services['services'])) {
                $this->logger->error('Invalid Discovery response', ['data' => $services]);
                return null;
            }

            $this->cache->set($this->cacheKey, $services, $this->cacheTTL);
        }

        $srn = Converter::fromString($this->cloudConfig['tid']);

        foreach ($services['services'] as $service) {
            if ($service['name'] !== $name) {
                continue;
            }

            foreach ($service['endpoints'] as $endpoint) {
                if ($endpoint['region'] === $srn->getRegion()) {
                    return $endpoint['url'];
                }
            }
        }

        $this->logger->error(
            sprintf('Service %s can not be found in Discovery for region %s', $name, $srn->getRegion())
        );

        return null;
    }
}
