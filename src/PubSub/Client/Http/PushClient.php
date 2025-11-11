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

namespace Sugarcrm\Sugarcrm\PubSub\Client\Http;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Sugarcrm\Sugarcrm\PubSub\Client\PushClientInterface;
use Sugarcrm\Sugarcrm\Security\HttpClient\Method;
use Sugarcrm\Sugarcrm\Security\HttpClient\RequestException;
use Sugarcrm\Sugarcrm\Security\HttpClient\RequestFactory;

final class PushClient implements PushClientInterface
{
    /**
     * Send requests through this HTTP client.
     */
    private HttpClientInterface $client;

    /**
     * Creates a client for sending events over HTTP.
     *
     * @param HttpClientInterface $client Send requests through this client.
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Sends events to a webhook.
     *
     * @param string $url The webhook URL.
     * @param array $events The list of events to send.
     *
     * @return void
     * @throws RequestException if HTTP status code is 4xx or 5xx.
     */
    public function sendEvents(string $url, array $events): void
    {
        $requestFactory = new RequestFactory();
        $request = $requestFactory->createJsonRequest(Method::POST, $url, $events);
        $response = $this->client->sendRequest($request);
        $statusCode = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        if ($statusCode >= 400) {
            throw new RequestException(
                "failed to send events to {$url} (HTTP {$statusCode} {$reason})",
                $statusCode
            );
        }
    }
}
