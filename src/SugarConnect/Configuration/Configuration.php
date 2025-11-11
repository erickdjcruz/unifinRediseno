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

namespace Sugarcrm\Sugarcrm\SugarConnect\Configuration;

use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use League\OAuth2\Client\Provider\GenericProvider;
use Psr\SimpleCache\CacheInterface;
use Sugarcrm\IdentityProvider\STS\EndpointInterface;
use Sugarcrm\IdentityProvider\STS\EndpointService;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\Sugarcrm\SugarConnect\Client\Client;
use Sugarcrm\Sugarcrm\SugarConnect\Client\Http\Client as HttpClient;
use Sugarcrm\Sugarcrm\SugarConnect\Client\Http\ProxyMiddleware;
use Sugarcrm\Sugarcrm\SugarConnect\Client\Http\RetryMiddleware;

/**
 * @deprecated Will be removed in the next release.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * The category under which all configurations are found.
     *
     * @var string
     */
    public const CATEGORY = 'sugar_connect';

    /**
     * The name of the Sugar Connect webhook service. It is used to obtain the
     * webhook URL using service discovery.
     *
     * @var string
     */
    public const WEBHOOK = 'connect-webhook:v1';

    /**
     * Configuration API.
     *
     * @var \Administration
     */
    private static $admin;

    /**
     * Sugar Connect webhook client.
     *
     * @var Client
     */
    private $client;

    /**
     * Read and write Sugar Connect configurations.
     */
    public function __construct()
    {
        if (!static::$admin) {
            $admin = Container::getInstance()->get(\Administration::class);
            static::$admin = $admin->retrieveSettings(static::CATEGORY);
        }
    }

    /**
     * Tells whether or not Sugar Connect is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->get('enabled', false);
    }

    /**
     * Enables Sugar Connect.
     *
     * @return void
     */
    public function enable(): void
    {
        $this->set('enabled', true);
    }

    /**
     * Disables Sugar Connect.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->set('enabled', false);
    }

    /**
     * Returns the Sugar Connect webhook client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        if (empty($this->client)) {
            $cache = Container::getInstance()->get(CacheInterface::class);
            $url = $this->getWebhookURL($cache);
            $maxRetries = (int)$this->get('max_retries', 2);
            $provider = new GenericProvider($this->getOAuth2Config());
            $this->client = new HttpClient($url, $maxRetries, $provider, $cache);
        }

        return $this->client;
    }

    /**
     * Returns the URL to the Sugar Connect webhook for the given region.
     *
     * @param CacheInterface $cache The webhook URL cached for reuse.
     *
     * @return string
     * @throws \Exception Throws if the URL can't be discovered.
     *
     */
    private function getWebhookURL(CacheInterface $cache): string
    {
        $webhook = $cache->get('sugar_connect_webhook_url');

        if (!empty($webhook)) {
            return $webhook;
        }

        $maxRetries = (int)$this->get('max_retries', 2);
        $region = $this->get('region', 'us-west-2');
        $version = $this->get('disco_version', 'v1');
        $url = $this->get(
            'disco_url',
            'https://discovery.service.sugarcrm.com'
        );
        $url = rtrim($url, '/') . '/' . trim($version, '/') . '/services';

        $proxy = new ProxyMiddleware();
        $retry = new RetryMiddleware($maxRetries);

        $stack = HandlerStack::create(GuzzleHttp\choose_handler());
        $stack->push($proxy, 'proxy');
        $stack->push($retry, 'retry');

        $client = new GuzzleClient(
            [
                'handler' => $stack,
                'timeout' => 20.0,
            ]
        );

        $response = $client->get($url);
        $responseBody = GuzzleHttp\json_decode($response->getBody(), true);
        $services = $responseBody['services'] ?? [];

        foreach ($services as $service) {
            if ($service['name'] === static::WEBHOOK) {
                foreach ($service['endpoints'] as $endpoint) {
                    if ($endpoint['region'] === $region) {
                        $webhook = rtrim($endpoint['url'], '/');
                        $cache->set('sugar_connect_webhook_url', $webhook, 3600);

                        return $webhook;
                    }
                }
            }
        }

        throw new \Exception('endpoint not found');
    }

    /**
     * Returns an OAuth 2.0 configuration using the client ID and secret
     * provided by IDM.
     *
     * @return array Configuration expected by GenericProvider.
     */
    private function getOAuth2Config(): array
    {
        $idm = new IdmConfig(\SugarConfig::getInstance());
        $settings = $idm->get(IdmConfig::IDM_MODE_KEY);
        $stsUrl = rtrim($settings['stsUrl'], '/ ');
        $eptSvc = new EndpointService(['host' => $stsUrl]);

        return [
            'clientId' => $settings['clientId'],
            'clientSecret' => $settings['clientSecret'],
            'urlAuthorize' => $eptSvc->getOAuth2Endpoint(EndpointInterface::AUTH_ENDPOINT),
            'urlAccessToken' => $eptSvc->getOAuth2Endpoint(EndpointInterface::TOKEN_ENDPOINT),
            'urlResourceOwnerDetails' => $eptSvc->getOAuth2Endpoint(EndpointInterface::INTROSPECT_ENDPOINT),
        ];
    }

    /**
     * Returns the value for the given key.
     *
     * @param string $key The key for the value to retrieve.
     * @param mixed $default An optional default value in the event that there
     *                        is no value under the key.
     *
     * @return mixed
     */
    private function get(string $key, $default = null)
    {
        // Prefix the key with the category to find the value in settings.
        $key = static::CATEGORY . '_' . $key;
        $settings = static::$admin->settings;

        return $settings[$key] ?? $default;
    }

    /**
     * Saves the value for the given key.
     *
     * @param string $key The key for the value to retrieve.
     * @param mixed $value The value to store.
     *
     * @return void
     */
    private function set(string $key, $value): void
    {
        static::$admin->saveSetting(static::CATEGORY, $key, $value);
        static::$admin = static::$admin->retrieveSettings(static::CATEGORY);
    }
}
