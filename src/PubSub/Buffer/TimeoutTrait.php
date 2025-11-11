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

trait TimeoutTrait
{
    /**
     * The maximum number of seconds before the buffer expires.
     */
    private int $timeoutInSeconds = 0;

    /**
     * Reports whether or not events have been in the buffer for too long.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        $ageInSeconds = $this->getAgeInSeconds();

        return $ageInSeconds >= $this->timeoutInSeconds;
    }

    /**
     * Sets the buffer's max age to determine expiration.
     *
     * @param int $seconds The maximum number of seconds before the buffer
     *                     expires. Defaults to 0 if the duration is less than
     *                     0.
     *
     * @return void
     */
    public function setTimeout(int $seconds): void
    {
        if ($seconds < 0) {
            $seconds = 0;
        }

        $this->timeoutInSeconds = $seconds;
    }

    /**
     * Reports the duration that events have been in the buffer.
     *
     * Only public and protected abstract methods were supported before PHP 8.0.
     * @access protected
     *
     * @return int The duration in seconds.
     */
    abstract protected function getAgeInSeconds(): int;
}
