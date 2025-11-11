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

namespace Sugarcrm\Sugarcrm\Cache\Backend;

use Sugarcrm\Sugarcrm\Cache\Backend\Redis\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Redis implementation of the cache backend
 *
 * @link http://pecl.php.net/package/redis
 */
final class Redis extends Psr16Cache
{
    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redisClient
     * @param string $namespace
     * @param int $defaultLifetime
     */
    public function __construct($redisClient, $namespace = '', $defaultLifetime = 0)
    {
        parent::__construct(new RedisAdapter($redisClient, $namespace, $defaultLifetime));
    }
}
