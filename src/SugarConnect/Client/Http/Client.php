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

use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\SimpleCache\CacheInterface;
use Sugarcrm\Sugarcrm\SugarConnect\Client\Client as ClientInterface;

/**
 * @deprecated Will be removed in the next release.
 */
final class Client implements ClientInterface
{
    /**
     * Send requests through this HTTP client.
     *
     * @var GuzzleClient
     */
    private $client;

    /**
     * The URL to the Sugar Connect webhook.
     *
     * @var string
     */
    private $url;

    /**
     * Creates a client for sending events to the Sugar Connect webhook as JSON
     * over HTTP.
     *
     * @param string $url The URL to the Sugar Connect webhook.
     * @param int $maxRetries Maximum number of retries.
     * @param AbstractProvider $provider OAuth 2.0 service provider client.
     * @param CacheInterface $cache Access tokens are cached for reuse.
     */
    public function __construct(string $url, int $maxRetries, AbstractProvider $provider, CacheInterface $cache)
    {
        $proxy = new ProxyMiddleware();
        $auth = new AuthMiddleware($provider, $cache);
        $retry = new RetryMiddleware($maxRetries);
        $debug = new DebugMiddleware();

        $stack = HandlerStack::create(GuzzleHttp\choose_handler());
        $stack->push($proxy, 'proxy');
        $stack->push($auth, 'auth');
        $stack->push($retry, 'retry');
        // The debug middleware must be added last. PSR-7 requests are
        // immutable, so this is the only way to guarantee that the middleware
        // is logging the final request that is sent.
        $stack->push($debug, 'debug');

        $this->client = new GuzzleClient(['handler' => $stack]);
        $this->url = $url;
    }

    /**
     * Sends events to the Sugar Connect webhook as JSON over HTTP.
     *
     * @param array $events The events to send to the webhook.
     *
     * @return void
     */
    public function send(array $events): void
    {
        $this->client->post(
            $this->url,
            [
                GuzzleHttp\RequestOptions::JSON => $events,
            ]
        );
    }
}
