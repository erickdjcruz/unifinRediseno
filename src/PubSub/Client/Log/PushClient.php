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

namespace Sugarcrm\Sugarcrm\PubSub\Client\Log;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sugarcrm\Sugarcrm\PubSub\Client\PushClientInterface;

final class PushClient implements PushClientInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Use this log level.
     */
    private string $level;

    /**
     * Creates a client that logs the JSON that would be sent.
     *
     * @param LoggerInterface $logger Write to this logger.
     * @param string $level Optional. Use this log level. Defaults to `debug`.
     */
    public function __construct(LoggerInterface $logger, string $level = LogLevel::DEBUG)
    {
        $this->setLogger($logger);
        $this->level = $level;
    }

    /**
     * Logs the JSON that would be sent to the webhook.
     *
     * @param string $url The webhook URL.
     * @param array $events The list of events to send.
     *
     * @return void
     */
    public function sendEvents(string $url, array $events): void
    {
        $this->logger->{$this->level}('pubsub: push: send events [url=' . $url . ', payload=' . json_encode($events) . ']');
    }
}
