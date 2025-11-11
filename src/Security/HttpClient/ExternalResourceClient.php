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

use Administration;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sugarcrm\Sugarcrm\Security\Dns\Resolver;
use Sugarcrm\Sugarcrm\Security\ValueObjects\ExternalResource;

class ExternalResourceClient implements ClientInterface
{
    /**
     * @var float Timeout in seconds
     */
    private float $timeout;
    /**
     * @var int
     */
    private int $maxRedirects;

    /**
     * @var int
     */
    private int $redirectsCount;

    /**
     * @var bool Forcing RFC compliance.
     * Strict RFC compliant redirects mean that POST redirect requests are sent as POST requests vs
     * doing what most browsers do which is redirect POST requests with GET requests
     */
    private bool $strictRedirects = false;

    private ResponseFactoryInterface $responseFactory;

    private ?Resolver $resolver = null;

    /**
     * @var array Socket context options
     * @see https://www.php.net/manual/en/context.socket.php
     */
    private array $socketContextOptions = [];

    /**
     * @var array Context options for ssl:// and tls:// transports.
     * @see https://www.php.net/manual/en/context.ssl.php
     */
    private array $sslContextOptions = [];

    /**
     * Stop retrying a failed request after exhausting the maximum number of
     * retries.
     */
    private int $maxRetries;

    /**
     * Tracks the number the retries that have been attempted.
     */
    private int $retryCount;

    /**
     * @param float $timeout Read timeout in seconds, specified by a float (e.g. 10.5)
     * @param int $maxRedirects The max number of redirects to follow. Value 0 means that no redirects are followed
     */
    public function __construct(float $timeout = 10, int $maxRedirects = 3)
    {
        $this->setTimeout($timeout);
        $this->setMaxRedirects($maxRedirects);
        $this->setMaxRetries(0);
        $this->redirectsCount = 0;
        $this->retryCount = 0;
    }

    /**
     * @param Resolver $resolver Set custom DoH resolver
     * @return ExternalResourceClient
     */
    public function setResolver(Resolver $resolver): ExternalResourceClient
    {
        $this->resolver = $resolver;
        return $this;
    }

    /**
     * @param array $options Socket context option listing
     * @return ExternalResourceClient
     */
    public function setSocketContextOptions(array $options): ExternalResourceClient
    {
        $this->socketContextOptions = $options;
        return $this;
    }

    /**
     * @param array $options SSL context option listing
     * @return ExternalResourceClient
     */
    public function setSslContextOptions(array $options): ExternalResourceClient
    {
        // enforce safe defaults
        $this->sslContextOptions = array_merge($options, [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ]);
        return $this;
    }

    /**
     * Force RFC compliance
     * @return $this
     */
    public function useStrictRedirects(): ExternalResourceClient
    {
        $this->strictRedirects = true;
        return $this;
    }

    /**
     * Read timeout in seconds, specified by a float (e.g. 10.5)
     * Specifying a negative value means an infinite timeout.
     * @param float $timeout
     * @return $this
     */
    public function setTimeout(float $timeout): ExternalResourceClient
    {
        // Using `==` instead of `===` to catch both 0 and 0.0
        if ($timeout == 0) {
            throw new \InvalidArgumentException("Timeout can't be 0. Specifying a negative value means an infinite timeout.");
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * The max number of redirects to follow. Value 0 means that no redirects are followed
     * @param int $maxRedirects
     * @return $this
     */
    public function setMaxRedirects(int $maxRedirects): ExternalResourceClient
    {
        if ($maxRedirects < 0) {
            throw new \InvalidArgumentException('The max number of redirects can not be less than zero');
        }
        $this->maxRedirects = $maxRedirects;
        return $this;
    }

    /**
     * Set the maximum number of retries for a failed request.
     *
     * An exponential backoff algorithm retries requests exponentially,
     * increasing the waiting time between retries until all retries are
     * exhausted.
     *
     * @see https://cloud.google.com/iot/docs/how-tos/exponential-backoff#example_algorithm
     * @param int $maxRetries Stop retrying a failed request after this many
     *                        retries.
     *
     * @return $this
     */
    public function setMaxRetries(int $maxRetries): ExternalResourceClient
    {
        if ($maxRetries < 0) {
            throw new \InvalidArgumentException('The max number of retries cannot be less than zero');
        }

        $this->maxRetries = $maxRetries;

        return $this;
    }

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @return $this
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory): self
    {
        $this->responseFactory = $responseFactory;
        return $this;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        if (!isset($this->responseFactory)) {
            $this->responseFactory = new ResponseFactory();
        }
        return $this->responseFactory;
    }

    /**
     * Sends GET request to provided $url
     * @param string $url URL
     * @param array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-type' => 'application/x-www-form-urlencoded']
     * @return ResponseInterface
     */
    public function get(string $url, array $headers = []): ResponseInterface
    {
        return $this->request(Method::GET, $url, null, $headers);
    }

    /**
     * Sends POST data ($body) to provided $url
     * @param string $url URL
     * @param string|array $body Request body
     * @param array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-Type' => 'application/x-www-form-urlencoded']
     * @return ResponseInterface
     */
    public function post(string $url, $body, array $headers = []): ResponseInterface
    {
        return $this->request(Method::POST, $url, is_string($body) ? $body : http_build_query((array) $body), array_merge(['Content-type' => 'application/x-www-form-urlencoded'], $headers));
    }

    /**
     * Sends PUT data ($body) to provided $url
     * @param string $url URL
     * @param string|array $body Request body
     * @param array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-Type' => 'application/x-www-form-urlencoded']
     * @return ResponseInterface
     */
    public function put(string $url, $body, array $headers = []): ResponseInterface
    {
        return $this->request(Method::PUT, $url, is_string($body) ? $body : http_build_query((array) $body), array_merge(['Content-type' => 'application/x-www-form-urlencoded'], $headers));
    }

    /**
     * Sends PATCH data ($body) to provided $url
     * @param string $url URL
     * @param string|array $body Request body
     * @param array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-Type' => 'application/x-www-form-urlencoded']
     * @return ResponseInterface
     */
    public function patch(string $url, $body, array $headers = []): ResponseInterface
    {
        return $this->request(Method::PATCH, $url, is_string($body) ? $body : http_build_query((array) $body), array_merge(['Content-type' => 'application/x-www-form-urlencoded'], $headers));
    }

    /**
     * Sends DELETE request to provided $url
     * @param string $url
     * @param array $headers
     * @return ResponseInterface
     */
    public function delete(string $url, array $headers = []): ResponseInterface
    {
        return $this->request(Method::DELETE, $url, null, $headers);
    }

    /**
     * Sends HEAD request to provided $url
     * @param string $url
     * @param array $headers
     * @return ResponseInterface
     */
    public function head(string $url, array $headers = []): ResponseInterface
    {
        return $this->request(Method::HEAD, $url, null, $headers);
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->request(
            $request->getMethod(),
            (string)$request->getUri(),
            (string)$request->getBody(),
            array_map(static function (array $headerValues): string {
                return implode(',', $headerValues);
            }, $request->getHeaders())
        );
    }

    /**
     * Sends request to provided $url using $method method
     * @param string $method HTTP Method
     * @param string $url URL
     * @param string|null $body Request body
     * @param array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-type' => 'application/x-www-form-urlencoded']
     * @return ResponseInterface
     */
    public function request(string $method, string $url, ?string $body = null, array $headers = []): ResponseInterface
    {
        global $sugar_config;
        // Prevent SSRF against internal resources
        $privateIps = $sugar_config['security']['private_ips'] ?? [];
        $externalResource = ExternalResource::fromString($url, $privateIps, $this->resolver);
        $proxy_config = Administration::getSettings('proxy');
        if (!empty($proxy_config->settings['proxy_auth'])) {
            $auth = base64_encode($proxy_config->settings['proxy_username'] . ':' . $proxy_config->settings['proxy_password']);
            $headers['Proxy-Authorization'] = "Basic $auth";
        }
        $headerParam = $this->buildHeaders($headers, $externalResource);

        $options = [
            'http' => [
                'method' => $method,
                'header' => $headerParam,
                'follow_location' => 0, // mitigate SSRF via redirect
                'content' => $body ?? null,
                'timeout' => $this->timeout,
                'protocol_version' => 1.1,
                'ignore_errors' => true,
            ],
            'ssl' => array_merge($this->sslContextOptions, [
                'peer_name' => $externalResource->getHost(),
            ]),
        ];
        if (safeCount($this->socketContextOptions)) {
            $options['socket'] = $this->socketContextOptions;
        }

        if (!empty($proxy_config->settings['proxy_on'])) {
            $proxyHost = $proxy_config->settings['proxy_host'];
            $proxyPort = $proxy_config->settings['proxy_port'];
            $options['http']['proxy'] = 'tcp://' . $proxyHost . ':' . $proxyPort;
            $options['https']['proxy'] = 'tcp://' . $proxyHost . ':' . $proxyPort;
            $scheme = parse_url($externalResource->getConvertedUrl(), PHP_URL_SCHEME);
            $options[$scheme]['request_fulluri'] = true;
        }
        $context = stream_context_create(
            $options
        );

        $level = error_reporting(0);
        // Using URL with the host name replaced by IP address to prevent DNS rebinding
        $content = file_get_contents($externalResource->getConvertedUrl(), false, $context);
        error_reporting($level);
        if ($content === false) {
            if ($error = error_get_last()) {
                throw new RequestException($error['message']);
            }
            throw new RequestException('Failed to get response from ' . $externalResource->getConvertedUrl());
        }

        $statusCode = $this->getStatusCode($http_response_header);

        // Retry on server error with exponential backoff strategy.
        if ($statusCode >= 500 && $this->maxRetries > 0 && $this->retryCount < $this->maxRetries) {
            $backoffDelay = $this->getExponentialBackoffDelayInMicroseconds($this->retryCount);
            usleep($backoffDelay);

            $this->retryCount++;

            try {
                return $this->request($method, $url, $body, $headers);
            } catch (RequestException $exception) {
                $this->retryCount = 0;
                throw $exception;
            }
        }

        $this->retryCount = 0;

        // Follow redirects.
        if ($this->maxRedirects > 0 && $this->redirectsCount < $this->maxRedirects) {
            foreach ($http_response_header as $header) {
                $canonicalHeader = strtolower(trim($header));
                if (substr($canonicalHeader, 0, 10) === 'location: ') {
                    $this->redirectsCount++;
                    /* update $url with where we were redirected to */
                    $redirectUrl = $externalResource->resolveLocation(substr($canonicalHeader, 10));
                    if ($statusCode === 303 ||
                        ($statusCode <= 302 && !$this->strictRedirects)
                    ) {
                        $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
                        $method = in_array($method, $safeMethods) ? $method : 'GET';
                        $body = null;
                    }
                    try {
                        return $this->request($method, $redirectUrl, $body, $headers);
                    } catch (RequestException $exception) {
                        $this->redirectsCount = 0;
                        throw $exception;
                    }
                }
            }
        }
        $this->redirectsCount = 0;

        return $this->createResponse($http_response_header, $content);
    }

    /**
     * @param array $responseHeaders
     * @param string $content
     * @return ResponseInterface
     */
    private function createResponse(array $responseHeaders, string $content): ResponseInterface
    {
        $headers = $this->normalizeResponseHeaders($responseHeaders);
        $statusLine = array_shift($headers);
        [$code, $reasonPhrase] = $this->getHttpStatus($statusLine);
        $response = $this->getResponseFactory()->createResponse();
        $response = $response->withStatus($code, $reasonPhrase);
        foreach ($headers as $header) {
            [$headerName, $headerValue] = explode(':', $header, 2);
            $response = $response->withAddedHeader($headerName, $headerValue);
        }

        $response->getBody()->write($content);
        $response->getBody()->rewind();
        return $response;
    }

    /**
     * @param array $responseHeaders
     * @return int
     */
    private function getStatusCode(array $responseHeaders): int
    {
        $headers = $this->normalizeResponseHeaders($responseHeaders);
        $statusLine = array_shift($headers);
        [$code] = $this->getHttpStatus($statusLine);
        return (int)$code;
    }

    /**
     * @param \UploadFile $file
     * @param string $url
     * @param string $method
     * @param array $body
     * @param array $headers
     * @return ResponseInterface
     */
    public function upload(\UploadFile $file, string $url, string $method = 'POST', array $body = [], array $headers = []): ResponseInterface
    {
        $boundary = '--------------------------' . microtime(true);
        $requestHeaders = array_merge(['Content-type' => 'multipart/form-data; boundary=' . $boundary], $headers);
        $fileContents = $file->get_file_contents();
        $content = '--' . $boundary . "\r\n" .
            'Content-Disposition: form-data; name="' . $file->field_name . '"; filename="' . basename($file->get_temp_file_location()) . "\"\r\n" .
            "Content-Type: application/zip\r\n\r\n" .
            $fileContents . "\r\n";
        foreach ($body as $name => $value) {
            $content .= '--' . $boundary . "\r\n" .
                "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n" .
                "{$value}\r\n";
        }
        $content .= '--' . $boundary . "--\r\n";
        return $this->request($method, $url, $content, $requestHeaders);
    }

    /**
     * @param array $headers array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-type' => 'application/x-www-form-urlencoded']
     * @param ExternalResource $externalResource
     * @return string[]
     */
    protected function buildHeaders(array $headers, ExternalResource $externalResource): array
    {
        $requestHeaders = [];
        foreach ($headers as $name => $value) {
            $canonicalName = strtolower(trim($name));
            $requestHeaders[$canonicalName] = $value;
        }
        // force correct Host header, override if it was provided in $headers
        $requestHeaders['host'] = $externalResource->getHost();
        return array_map(function ($k, $v) {
            return "$k: $v";
        }, array_keys($requestHeaders), array_values($requestHeaders));
    }

    /**
     * @param array $responseHeaders
     * @return array
     */
    protected function normalizeResponseHeaders(array $responseHeaders): array
    {
        $headers = [];
        foreach ($responseHeaders as $header) {
            $trimmed = trim($header);
            if (0 === stripos($trimmed, 'http/')) {
                $headers = [];
                $headers[] = $trimmed;
                continue;
            }

            if (false === strpos($trimmed, ':')) {
                continue;
            }
            $headers[] = $trimmed;
        }
        return $headers;
    }

    /**
     * @param $statusLine
     * @return array
     */
    protected function getHttpStatus($statusLine): array
    {
        $parts = explode(' ', $statusLine, 3);
        if (safeCount($parts) < 2 || 0 !== stripos($parts[0], 'http/')) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid HTTP status line', $statusLine));
        }
        $code = $parts[1];
        $reasonPhrase = $parts[2];
        return [$code, $reasonPhrase];
    }

    /**
     * Calculates the wait time for the next retry.
     *
     * @param int $attempt The retry attempt count.
     *
     * @return int The delay in microseconds.
     */
    private function getExponentialBackoffDelayInMicroseconds(int $attempt): int
    {
        $oneSecondInMicroseconds = 1000000;
        $maxDelay = 64 * $oneSecondInMicroseconds;
        $delay = 2 ** $attempt * $oneSecondInMicroseconds;
        $jitter = random_int(0, $oneSecondInMicroseconds);

        return min($delay + $jitter, $maxDelay);
    }
}
