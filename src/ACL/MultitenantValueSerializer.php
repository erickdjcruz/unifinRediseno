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

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use Sugarcrm\Sugarcrm\Security\Crypto\AES256GCM;

final class MultitenantValueSerializer implements ValueSerializer
{
    /**
     * @var ValueSerializer
     */
    private $valueSerializer;

    /**
     * Encryption key
     *
     * @var UuidInterface
     */
    private $key;

    /**
     * Cryptographic algorithm implementation
     *
     * @var AES256GCM
     */
    private $crypto;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ValueSerializer $valueSerializer,
        UuidInterface   $encryptionKey,
        LoggerInterface $logger
    ) {

        $this->valueSerializer = $valueSerializer;
        $this->key = $encryptionKey;
        $this->logger = $logger;

        $this->initializeKey();
    }

    public function serialize($value): string
    {
        return $this->encrypt($this->valueSerializer->serialize($value));
    }

    public function unserialize(string $value)
    {
        $decryptedValue = $this->decrypt($value);
        if ($decryptedValue === null) {
            return null;
        }
        return $this->valueSerializer->unserialize($decryptedValue);
    }

    /**
     * Initializes the updated encryption key
     */
    private function initializeKey(): void
    {
        $this->crypto = new AES256GCM($this->key->toString());
    }

    /**
     * Encrypts the value to be cached
     *
     * @param string $value
     * @return string
     */
    private function encrypt(string $value): string
    {
        return $this->crypto->encrypt($value);
    }

    /**
     * Decrypts the cached value
     *
     * @param mixed $value
     * @return mixed
     */
    private function decrypt($value)
    {
        try {
            return $this->crypto->decrypt($value);
        } catch (RuntimeException $e) {
            $this->logger->warning(sprintf('Failed to decrypt cached value: %s', $e->getMessage()));

            return null;
        }
    }
}
