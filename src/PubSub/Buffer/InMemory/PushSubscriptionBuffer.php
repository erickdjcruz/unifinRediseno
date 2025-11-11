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

namespace Sugarcrm\Sugarcrm\PubSub\Buffer\InMemory;

use Sugarcrm\Sugarcrm\Clock\Timer;
use Sugarcrm\Sugarcrm\PubSub\Buffer\PushSubscriptionBufferInterface;

final class PushSubscriptionBuffer implements PushSubscriptionBufferInterface
{
    use BufferTrait;

    /**
     * Creates an empty in-memory buffer.
     */
    public function __construct()
    {
        $this->setTimer(new Timer());
        $this->clearEvents();
    }

    /**
     * Writes events to the buffer.
     *
     * @param string $url The webhook URL.
     * @param array $events The list of events to send.
     *
     * @return void
     */
    public function writeEvents(string $url, array $events): void
    {
        if (!array_key_exists($url, $this->buffer)) {
            $this->buffer[$url] = [];
        }

        $this->buffer[$url] = array_merge($this->buffer[$url], $events);

        $this->timer->start();
    }

    /**
     * Reports the number of events in the buffer.
     *
     * Only public and protected abstract methods were supported before PHP 8.0.
     * @access protected
     *
     * @return int
     */
    protected function getLength(): int
    {
        return array_reduce($this->buffer, function ($length, $events) {
            return $length + safeCount($events);
        }, 0);
    }
}
