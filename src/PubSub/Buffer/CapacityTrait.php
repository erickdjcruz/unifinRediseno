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

namespace Sugarcrm\Sugarcrm\PubSub\Buffer;

trait CapacityTrait
{
    /**
     * The maximum number of events the buffer can hold. By default, the buffer
     * never holds any events.
     */
    private int $capacity = 0;

    /**
     * Reports whether or not the buffer is full.
     *
     * @return bool
     */
    public function isFull(): bool
    {
        $length = $this->getLength();

        return $length >= $this->capacity;
    }

    /**
     * Sets the buffer's capacity.
     *
     * @param int $capacity The maximum number of events the buffer can hold.
     *                      Defaults to 0 if capacity is less than 0.
     *
     * @return void
     */
    public function setCapacity(int $capacity): void
    {
        if ($capacity < 0) {
            $capacity = 0;
        }

        $this->capacity = $capacity;
    }

    /**
     * Reports the number of events in the buffer.
     *
     * Only public and protected abstract methods were supported before PHP 8.0.
     * @access protected
     *
     * @return int
     */
    abstract protected function getLength(): int;
}
