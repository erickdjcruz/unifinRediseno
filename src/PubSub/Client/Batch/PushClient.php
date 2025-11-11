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

namespace Sugarcrm\Sugarcrm\PubSub\Client\Batch;

use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Sugarcrm\Sugarcrm\PubSub\Buffer\InMemory\PushSubscriptionBuffer;
use Sugarcrm\Sugarcrm\PubSub\Buffer\PushSubscriptionBufferInterface;
use Sugarcrm\Sugarcrm\PubSub\Client\PushClientInterface;

final class PushClient implements BatchClientInterface, LoggerAwareInterface, PushClientInterface
{
    use LoggerAwareTrait;

    /**
     * The buffer backend.
     */
    private PushSubscriptionBufferInterface $buffer;

    /**
     * Send events using this client when flushing the buffer.
     */
    private PushClientInterface $client;

    /**
     * Wraps $client with a buffered client for sending events in batches. Uses
     * an in-memory buffer by default.
     *
     * The default buffer uses a pseudo-timeout. The expiration date of the
     * buffer is checked when events are written to the buffer. If no events are
     * written to the buffer then the timer may far exceed the timeout. The
     * assumption is that few processes will exceed the timeout and the
     * registered shutdown function will flush the buffer and dispatch the
     * events to ensure that all events are eventually delivered.
     *
     * @param PushClientInterface $client Send events using this client when
     *                                    flushing the buffer.
     */
    public function __construct(PushClientInterface $client)
    {
        $this->client = $client;
        $this->setBuffer(new PushSubscriptionBuffer());
        $this->setLogger(new NullLogger());
    }

    /**
     * Sends all events in the buffer before shutdown.
     */
    public function __destruct()
    {
        try {
            $this->flushEvents();
        } catch (Exception $e) {
            $this->logger->alert("pubsub: push: flush events: {$e->getMessage()}: {$e->getTraceAsString()}");
        }
    }

    /**
     * Sends all events in the buffer to the appropriate webhook(s).
     *
     * @return void
     */
    public function flushEvents(): void
    {
        $buffer = $this->buffer->flushEvents();

        foreach ($buffer as $url => $events) {
            try {
                $this->client->sendEvents($url, $events);
            } catch (Exception $e) {
                $this->logger->alert("pubsub: push: send batch of events to {$url}: {$e->getMessage()}: {$e->getTraceAsString()}");
            }
        }
    }

    /**
     * Writes events to the buffer. Flushes the buffer if it is full or expired.
     *
     * @param string $url The webhook URL.
     * @param array $events The list of events to send.
     *
     * @return void
     */
    public function sendEvents(string $url, array $events): void
    {
        $this->buffer->writeEvents($url, $events);

        if ($this->buffer->isExpired() || $this->buffer->isFull()) {
            $this->flushEvents();
        }
    }

    /**
     * Sets the buffer backend.
     *
     * @param PushSubscriptionBufferInterface $buffer Use this buffer backend.
     *
     * @return void
     */
    public function setBuffer(PushSubscriptionBufferInterface $buffer): void
    {
        $this->buffer = $buffer;
    }
}
