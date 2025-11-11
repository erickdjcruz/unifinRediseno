<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant;

use Ramsey\Uuid\UuidInterface;

/**
 * Multi-tenant cache key storage
 */
interface KeyStorage
{
    /**
     * Returns the currently effective key
     *
     * @return UuidInterface
     */
    public function getKey(): ?UuidInterface;

    /**
     * Updates the key
     *
     * @param UuidInterface $key
     */
    public function updateKey(UuidInterface $key): void;
}
