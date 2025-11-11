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

namespace Sugarcrm\Sugarcrm\ACL;

/**
 * ACL data cache interface
 */
interface Cache
{
    /**
     * Retrieve a value for a key from the cache. Returns NULL in case if the entry is not found.
     */
    public function retrieve(string $userId, string $key): ?array;

    /**
     * Set a value for a key in the cache.
     */
    public function store(string $userId, string $key, array $value): void;

    /**
     * Clear cache by user ID.
     */
    public function clearByUser(string $userId): void;

    /**
     * Clear cache.
     */
    public function clearAll(): void;
}
