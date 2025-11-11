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

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @deprecated Will be removed in the next release.
 */
class DebugMiddleware
{
    /**
     * Guzzle middleware invocation to log requests and responses for debugging.
     *
     * @param callable $handler The next handler to invoke from the middleware
     *                          chain.
     *
     * @return \Closure
     */
    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler(
                $request,
                $options
            )->then($this->onFulfilled($request), $this->onRejected($request));
        };
    }

    /**
     * Logs a fulfilled request and response.
     *
     * @param RequestInterface $request The request.
     *
     * @return \Closure
     */
    private function onFulfilled(RequestInterface $request): \Closure
    {
        return function (ResponseInterface $response) use ($request) {
            $formatter = $this->getMessageFormatter();
            $msg = $formatter->format($request, $response);
            $this->logMessage($msg);

            return $response;
        };
    }

    /**
     * Logs a rejected request along with the reason for the rejection.
     *
     * @param RequestInterface $request The request.
     *
     * @return \Closure
     */
    private function onRejected(RequestInterface $request): \Closure
    {
        return function ($reason) use ($request) {
            $formatter = $this->getMessageFormatter();
            $msg = $formatter->format($request, null, $reason);
            $this->logMessage($msg);

            return Promise\rejection_for($reason);
        };
    }

    /**
     * Returns a DEBUG message formatter.
     *
     * @return MessageFormatter
     */
    private function getMessageFormatter(): MessageFormatter
    {
        return new MessageFormatter(MessageFormatter::DEBUG);
    }

    /**
     * Logs the message.
     *
     * @param string $msg The string to log.
     *
     * @return void
     */
    private function logMessage(string $msg): void
    {
        $log = \LoggerManager::getLogger();
        $log->debug("sugar connect: client: http: {$msg}");
    }
}
