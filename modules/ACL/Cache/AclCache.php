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

use Sugarcrm\Sugarcrm\ACL\Cache;

/**
 * ACL data cache
 */
class AclCache implements Cache
{
    public const HASH_KEY = 'ACL';

    /** @var SugarCacheAbstract */
    protected $cache;

    /**
     * Local copy of cached value hashes
     *
     * @var array|null
     */
    protected $hashes;

    /**
     * Local copy of cached value hashes
     *
     * @var static
     */
    protected static $instance;

    /**
     * Constructor.
     *
     * @param SugarCacheAbstract $cache
     */
    protected function __construct(SugarCacheAbstract $cache = null)
    {
        if (!$cache) {
            $cache = SugarCache::instance();
        }

        $this->cache = $cache;
    }

    /**
     * Returns single instance of the class
     * @return static
     * @deprecated Please use Container::getInstance()->get(AclCache::class)
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Resets single instance of the class
     *
     * @return static
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * Retrieve a value for a key from the cache. Returns NULL in case if the entry is not found.
     */
    public function retrieve(string $userId, string $key): ?array
    {
        $this->hashes = $this->getHashes();

        if (isset($this->hashes[$userId][$key])) {
            $hash = $this->hashes[$userId][$key];
            $value = $this->cache->get($hash);
            return $value;
        }

        return null;
    }

    /**
     * Set a value for a key in the cache.
     */
    public function store(string $userId, string $key, array $value): void
    {
        $hash = md5(serialize($value));
        $this->hashes = $this->getHashes();
        if (!isset($this->hashes[$userId][$key]) || $this->hashes[$userId][$key] !== $hash) {
            $this->hashes[$userId][$key] = $hash;
            $this->cache->set(self::HASH_KEY, $this->hashes, 0);
        }

        $this->cache->set($hash, $value, session_cache_expire() * 60);
    }

    /**
     * @deprecated Please use {@link Cache::clearAll()} and {@link Cache::clearByUser()}
     * Clear cache.
     */
    public function clear(?string $userId = null, ?string $key = null): void
    {
        // clear cache for a single user
        if ($userId) {
            $this->hashes = $this->getHashes();
            if (isset($this->hashes[$userId])) {
                if ($key) {
                    if (isset($this->hashes[$userId][$key])) {
                        unset($this->hashes[$userId][$key]);
                        $this->cache->set(self::HASH_KEY, $this->hashes, 0);
                    }
                    return;
                }
                unset($this->hashes[$userId]);
                $this->cache->set(self::HASH_KEY, $this->hashes, 0);
            }
            return;
        }
        // clear cache for all users
        $this->hashes = null;
        unset($this->cache->{self::HASH_KEY});
    }

    /**
     * to init hashes
     * @return array|null
     */
    protected function getHashes(): ?array
    {
        return $this->hashes ?? $this->cache->get(self::HASH_KEY);
    }

    public function clearByUser(string $userId): void
    {
        $this->hashes = $this->getHashes();
        if (isset($this->hashes[$userId])) {
            unset($this->hashes[$userId]);
            $this->cache->set(self::HASH_KEY, $this->hashes, 0);
        }
    }

    public function clearAll(): void
    {
        $this->hashes = null;
        unset($this->cache->{self::HASH_KEY});
    }
}
