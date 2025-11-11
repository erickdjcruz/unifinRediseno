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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use SugarDateTime;
use TimeDate;

class Clock implements ClockInterface
{
    /**
     * Returns the current UTC time.
     *
     * @return SugarDateTime
     */
    public function now(): SugarDateTime
    {
        $container = Container::getInstance();
        $timedate = $container->get(TimeDate::class);

        return $timedate->getNow();
    }

    /**
     * Sleeps for the specified number of seconds.
     *
     * @param int $seconds Sleep for this many seconds.
     *
     * @return void
     */
    public function sleep(int $seconds): void
    {
        sleep($seconds);
    }

    /**
     * Returns the current Unix timestamp.
     *
     * @return int The number of seconds since the Unix Epoch.
     */
    public function time(): int
    {
        return time();
    }

    /**
     * Sleeps for the specified number of microseconds.
     *
     * @param int $microseconds Sleep for this many microseconds.
     *
     * @return void
     */
    public function usleep(int $microseconds): void
    {
        usleep($microseconds);
    }
}
