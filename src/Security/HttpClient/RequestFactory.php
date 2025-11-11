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

declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Security\HttpClient;

use Exception;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }

    /**
     * Create a new JSON request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request.
     * @param mixed $body The request body to be serialized or null if no body.
     *
     * @return RequestInterface
     * @throws InvalidArgumentException if the body cannot be JSON encoded.
     */
    public function createJsonRequest(string $method, $uri, $body = null): RequestInterface
    {
        if (!is_null($body)) {
            try {
                $body = json_encode($body, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new InvalidArgumentException('Failed to encode the body', $e->getCode(), $e);
            }
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        return new Request($method, $uri, $headers, $body);
    }
}
