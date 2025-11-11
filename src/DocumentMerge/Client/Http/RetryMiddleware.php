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

use GuzzleHttp\RetryMiddleware as GuzzleRetryMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RetryMiddleware
{
    /**
     * Maximum number of retries to before quitting.
     *
     * @var int
     */
    private $maxRetries;

    /**
     * Guzzle middleware to add a retry policy with exponential backoff.
     *
     * @param int $maxRetries Maximum number of retries.
     */
    public function __construct(int $maxRetries = 0)
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * Guzzle middleware invocation to add a retry policy.
     *
     * @param callable $handler The next handler to invoke from the middleware
     *                          chain.
     *
     * @return GuzzleRetryMiddleware
     */
    public function __invoke(callable $handler): GuzzleRetryMiddleware
    {
        return new GuzzleRetryMiddleware(
            $this->shouldRetry($this->maxRetries),
            $handler
        );
    }

    /**
     * Specifies the conditions for retrying a request.
     *
     * @param int $maxRetries Maximum number of retries.
     *
     * @return \Closure
     */
    private function shouldRetry(int $maxRetries): \Closure
    {
        return function (
            $retries,
            Request $request,
            ?Response $response = null,
            ?RequestException $exception = null
        ) use ($maxRetries) {
            if ($retries >= $maxRetries) {
                return false;
            }

            if ($response && $response->getStatusCode() >= 500) {
                return true;
            }

            return false;
        };
    }
}
