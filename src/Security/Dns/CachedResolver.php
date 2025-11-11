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
declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Security\Dns;

use Psr\SimpleCache\CacheInterface;

class CachedResolver implements Resolver
{
    private CacheInterface $cache;
    private Resolver $resolver;
    private int $ttl;
    protected const DEFAULT_CACHE_TTL = 300;

    public function __construct(CacheInterface $cache, Resolver $resolver, ?int $ttl = self::DEFAULT_CACHE_TTL)
    {
        $this->cache = $cache;
        $this->resolver = $resolver;
        $this->ttl = $ttl;
    }

    /**
     * @param string $hostname
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function resolveToIp(string $hostname): string
    {
        $ip = $this->cache->get('ip_of_' . $hostname);
        if (null !== $ip) {
            return $ip;
        }
        $ip = $this->resolver->resolveToIp($hostname);
        $this->cache->set('ip_of_' . $hostname, $ip, $this->ttl);
        return $ip;
    }
}
