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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Client\Http;

use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Sugarcrm\Sugarcrm\DocumentMerge\Client\Client as ClientInterface;

class Client implements ClientInterface
{
    /**
     * Send requests through this HTTP client.
     *
     * @var GuzzleClient
     */
    private $client;

    /**
     * The URL to the Document Merge api.
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
     */
    public function __construct(string $url, int $maxRetries = 3)
    {
        $retry = new RetryMiddleware($maxRetries);
        $debug = new DebugMiddleware();

        $stack = HandlerStack::create(GuzzleHttp\choose_handler());
        $stack->push($retry, 'retry');
        // The debug middleware must be added last. PSR-7 requests are
        // immutable, so this is the only way to guarantee that the middleware
        // is logging the final request that is sent.
        $stack->push($debug, 'debug');

        $this->client = new GuzzleClient(['handler' => $stack]);
        $this->url = $url;
    }

    /**
     * Calls the Document Merge api.
     *
     * @param string $method HTTP Method
     * @param array $options The data to send to the api.
     *
     * @return ResponseInterface
     */
    public function call(string $method, array $options): ResponseInterface
    {
        $endpoint = $this->url . '/merge';

        return $this->client->request($method, $endpoint, ['json' => $options]);
    }
}
