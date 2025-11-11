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

namespace Sugarcrm\Sugarcrm\Cache\Backend\Redis;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Traits\RedisTrait;

/**
 * Redis implementation of the cache backend
 *
 * @link http://pecl.php.net/package/redis
 */
final class RedisAdapter extends AbstractAdapter
{
    use RedisTrait {
        doFetch as traitFetch;
        pipeline as traitPipeline;
    }

    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redisClient
     * @param string $namespace
     * @param int $defaultLifetime
     */
    public function __construct($redisClient, $namespace = '', $defaultLifetime = 0)
    {
        $this->init($redisClient, $namespace, $defaultLifetime, null);
    }

    protected function doFetch(array $ids)
    {
        if (!$ids) {
            return [];
        }
        $result = [];
        if (safeCount($ids) === 1) {
            $values = $this->traitPipeline(function () use ($ids) {
                foreach ($ids as $id) {
                    yield 'get' => [$id];
                }
            });
            foreach ($values as $id => $v) {
                if ($v) {
                    $result[$id] = $this->marshaller->unmarshall($v);
                }
            }

            return $result;
        } else {
            return $this->traitFetch($ids);
        }
    }
}
