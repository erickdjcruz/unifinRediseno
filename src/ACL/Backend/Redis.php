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

namespace Sugarcrm\Sugarcrm\ACL\Backend;

use Redis as Client;
use Sugarcrm\Sugarcrm\ACL\Cache;
use Sugarcrm\Sugarcrm\ACL\KeyConverter;
use Sugarcrm\Sugarcrm\ACL\ValueSerializer;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Psr\SimpleCache\CacheInterface;

/**
 * ACL data cache
 */
class Redis implements Cache
{
    private const HASH_KEY = 'ACL';

    /** @var Client */
    private $redis;

    /** @var KeyConverter */
    private $keyConverter;

    /** @var ValueSerializer */
    private $valueSerializer;

    private $hashes = [];

    public function __construct(
        Client          $client,
        KeyConverter    $keyConverter,
        ValueSerializer $valueSerializer
    ) {

        $this->redis = $client;
        $this->keyConverter = $keyConverter;
        $this->valueSerializer = $valueSerializer;
    }

    public function retrieve(string $userId, string $key): ?array
    {
        if (isset($this->hashes[$userId][$key])) {
            $hash = $this->hashes[$userId][$key];
            return Container::getInstance()->get(CacheInterface::class)->get($hash);
        }
        $userHashes = $this->redis->hGet($this->getKey(self::HASH_KEY), $userId);
        if ($userHashes === false) {
            return null;
        }
        $userHashesArray = $this->valueSerializer->unserialize($userHashes);
        if (isset($userHashesArray[$key])) {
            $hash = $userHashesArray[$key];
            $this->hashes[$userId] = $userHashesArray;

            return Container::getInstance()->get(CacheInterface::class)->get($hash);
        }
        return null;
    }

    public function store(string $userId, string $key, array $value): void
    {
        $hash = hash('sha256', serialize($value));
        if (!isset($this->hashes[$userId][$key]) || $this->hashes[$userId][$key] !== $hash) {
            $userHashes = $this->redis->hGet($this->getKey(self::HASH_KEY), $userId);
            if ($userHashes === false) {
                $userHashesArray = [];
            } else {
                $userHashesArray = $this->valueSerializer->unserialize($userHashes);
            }
            if (!isset($userHashesArray[$key]) || $userHashesArray[$key] !== $hash) {
                $userHashesArray[$key] = $hash;
                $this->redis->hSet(
                    $this->getKey(self::HASH_KEY),
                    $userId,
                    $this->valueSerializer->serialize($userHashesArray)
                );
            }
            $this->hashes[$userId] = $userHashesArray;
        }

        Container::getInstance()->get(CacheInterface::class)->set($hash, $value, session_cache_expire() * 60);
    }

    public function clearByUser(string $userId): void
    {
        unset($this->hashes[$userId]);
        $this->redis->hDel($this->getKey(self::HASH_KEY), $userId);
    }

    public function clearAll(): void
    {
        $this->redis->del($this->getKey(self::HASH_KEY));
        $this->hashes = [];
    }

    private function getKey($key): string
    {
        return $this->keyConverter->convert($key);
    }
}
