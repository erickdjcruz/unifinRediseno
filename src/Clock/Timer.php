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

namespace Sugarcrm\Sugarcrm\Clock;

class Timer
{
    use ClockAwareTrait;

    /**
     * The number of seconds since the Unix Epoch that the timer was started.
     */
    private int $startDateInSeconds;

    /**
     * Creates a timer with the default clock.
     */
    public function __construct()
    {
        $this->setClock(new Clock());
        $this->reset();
    }

    /**
     * Stops the timer sets it to 0.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->startDateInSeconds = 0;
    }

    /**
     * Starts the timer by recording the current time in seconds since the
     * epoch. Does nothing if the timer was previously started.
     *
     * @return void
     */
    public function start(): void
    {
        if ($this->startDateInSeconds < 1) {
            $this->startDateInSeconds = $this->clock->time();
        }
    }

    /**
     * Reports the duration since the timer was started.
     *
     * @return int The timer duration in seconds.
     */
    public function time(): int
    {
        if ($this->startDateInSeconds < 1) {
            return 0;
        }

        return $this->clock->time() - $this->startDateInSeconds;
    }
}
