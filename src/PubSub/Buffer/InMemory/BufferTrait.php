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

use Sugarcrm\Sugarcrm\Clock\TimerAwareTrait;
use Sugarcrm\Sugarcrm\PubSub\Buffer\CapacityTrait;
use Sugarcrm\Sugarcrm\PubSub\Buffer\TimeoutTrait;

trait BufferTrait
{
    use CapacityTrait;
    use TimeoutTrait;
    use TimerAwareTrait;

    /**
     * The in-memory buffer.
     */
    private array $buffer;

    /**
     * Clears the buffer and returns the events.
     *
     * @return array
     */
    public function flushEvents(): array
    {
        $events = $this->buffer;
        $this->clearEvents();

        return $events;
    }

    /**
     * Deletes all events from the buffer.
     *
     * @return void
     */
    private function clearEvents(): void
    {
        $this->buffer = [];
        $this->timer->reset();
    }

    /**
     * Reports the duration that events have been in the buffer.
     *
     * Only public and protected abstract methods were supported before PHP 8.0.
     * @access protected
     *
     * @return int The duration in seconds.
     */
    protected function getAgeInSeconds(): int
    {
        return $this->timer->time();
    }
}
