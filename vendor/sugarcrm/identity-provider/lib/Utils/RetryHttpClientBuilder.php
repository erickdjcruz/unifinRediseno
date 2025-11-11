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

namespace Sugarcrm\IdentityProvider\Utils;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RetryHttpClientBuilder
{
    const DELAY_STRATEGY_LINEAR = 'linear';
    const DELAY_STRATEGY_EXPONENTIAL = 'exponential';
    const DELAY_STRATEGY_DEFAULT = self::DELAY_STRATEGY_LINEAR;

    /**
     * Specifies conditions of how should retry of sending the request should be performed.
     *
     * @param int $maxRetries Maximum number of retries to get the response.
     * @return \Closure
     */
    public static function retryDecider($maxRetries)
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) use ($maxRetries) {
            if ($retries >= $maxRetries) {
                return false;
            }

            if ($response) {
                $statusCode = $response->getStatusCode();
                if (($statusCode >= 500) || ($statusCode == 425) || ($statusCode == 429)) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * Get retry delay strategy based on config value.
     *
     * @param array $config IDM-mode http_client config.
     * @return \Closure
     */
    public static function getDelayStrategy($config)
    {
        $value = $config['delay_strategy'] ?? self::DELAY_STRATEGY_DEFAULT;

        switch ($value) {
            case self::DELAY_STRATEGY_EXPONENTIAL:
                return self::retryDelayExponential();

            case self::DELAY_STRATEGY_LINEAR:
            default:
                return self::retryDelayLinear();
        }
    }

    /**
     * Increases delay time between http request retries by 1 second.
     *
     * @return \Closure that returns milliseconds of delay.
     */
    public static function retryDelayLinear()
    {
        return function ($retries) {
            return 1000 * $retries;
        };
    }

    /**
     * Increases delay time between http request retries by 2^n-1 where n is the retry attempt counter.
     *
     * @return \Closure that returns milliseconds of delay.
     */
    public static function retryDelayExponential()
    {
        return function ($retries) {
            return (int) pow(2, $retries - 1) * 1000;
        };
    }

    /**
     * Creates an HTTP client with retry policy.
     *
     * @param array $config
     * @return ClientInterface
     */
    public static function getClient(array $config): ClientInterface
    {
        $retryCount = (isset($config['retry_count'])) ? (int) $config['retry_count'] : 0;

        $handlerStack = HandlerStack::create(GuzzleHttp\choose_handler());
        $handlerStack->push(
            Middleware::retry(self::retryDecider($retryCount), self::getDelayStrategy($config)),
            'retryDecider'
        );

        $options['handler'] = $handlerStack;

        if (isset($config['proxy'])) {
            $options['proxy'] = $config['proxy'];
        }

        if (isset($config['verify'])) {
            $options['verify'] = $config['verify'];
        }

        if (isset($config['headers'])) {
            $options['headers'] = $config['headers'];
        }

        return new Client($options);
    }
}
