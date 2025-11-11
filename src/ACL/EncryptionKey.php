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

namespace Sugarcrm\Sugarcrm\ACL;

use Sugarcrm\Sugarcrm\Cache\Middleware\MultiTenant\KeyStorage;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class EncryptionKey
{
    /**
     * Encryption key storage
     *
     * @var KeyStorage
     */
    private $keyStorage;

    public function __construct(KeyStorage $keyStorage)
    {
        $this->keyStorage = $keyStorage;
    }

    public function get(): UuidInterface
    {
        return $this->keyStorage->getKey() ?: $this->generateKey();
    }

    /**
     * Generates a new key and stores it in the storage
     *
     * @return UuidInterface
     */
    private function generateKey(): UuidInterface
    {
        $key = Uuid::uuid4();
        $this->keyStorage->updateKey($key);

        return $key;
    }
}
