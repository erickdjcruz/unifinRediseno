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

use DateInterval;
use SugarDateTime;

class FakeClock implements ClockInterface
{
    /**
     * Holds the clock's current time.
     */
    private SugarDateTime $time;

    /**
     * Make time stand still. The clock only advances through time when you tell
     * it to.
     *
     * @param ?SugarDateTime $now The clock's initial time. The real current
     *                            time is used if null.
     */
    public function __construct(?SugarDateTime $now = null)
    {
        $this->time = $now ?? (new Clock())->now();
    }

    /**
     * Returns the clock's current time.
     *
     * @return SugarDateTime
     */
    public function now(): SugarDateTime
    {
        return $this->time;
    }

    /**
     * Adds time to the clock to mimic moving forward through time.
     *
     * @param int $seconds The number of seconds to add.
     *
     * @return void
     */
    public function sleep(int $seconds): void
    {
        $interval = new DateInterval("PT{$seconds}S");
        $this->time->add($interval);
    }

    /**
     * Returns the clock's Unix timestamp.
     *
     * @return int The number of seconds since the Unix Epoch.
     */
    public function time(): int
    {
        return $this->time->getTimestamp();
    }

    /**
     * Adds time to the clock to mimic moving forward through time.
     *
     * @param int $microseconds The number of microseconds to add.
     *
     * @return void
     */
    public function usleep(int $microseconds): void
    {
        $interval = DateInterval::createFromDateString("{$microseconds} microseconds");

        if ($interval) {
            $this->time->add($interval);
        }
    }
}
