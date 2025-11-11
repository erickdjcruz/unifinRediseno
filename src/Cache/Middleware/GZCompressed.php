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

namespace Sugarcrm\Sugarcrm\Cache\Middleware;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class GZCompressed implements CacheInterface
{
    private const MAX_COMPRESSION_LEVEL = 9;

    /**
     * Underlying cache backend
     *
     * @var CacheInterface
     */
    private $backend;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $compressionLevel;

    /**
     * @param CacheInterface $backend
     * @param LoggerInterface $logger
     * @param int|null $compressionLevel
     */
    public function __construct(CacheInterface $backend, LoggerInterface $logger, ?int $compressionLevel)
    {
        $this->backend = $backend;
        $this->logger = $logger;
        if (isset($compressionLevel) && $compressionLevel - self::MAX_COMPRESSION_LEVEL <= 0) {
            $this->compressionLevel = $compressionLevel;
        } else {
            $this->compressionLevel = self::MAX_COMPRESSION_LEVEL;
        }
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $miss = is_object($default) ? $default : new stdClass();
        $value = $this->backend->get($key, $miss);
        if (!is_string($value)) { //value should always be a gzipped string
            return $default;
        }
        if ($value === $miss) {
            return $default;
        }
        return $this->uncompress($value, $default);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->backend->set($key, $this->compress($value), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return $this->backend->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->backend->clear();
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        $values = $this->backend->getMultiple($keys);

        foreach ($values as $key => $compressedValue) {
            $result[$key] = $this->uncompress($compressedValue, $default);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->compress($value);
        }

        return $this->backend->setMultiple($values, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        return $this->backend->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->backend->has($key);
    }

    /**
     * @param $value
     * @return false|string
     */
    private function compress($value)
    {
        return gzcompress(serialize($value), $this->compressionLevel);
    }

    /**
     * @param String $value
     * @param $default
     * @return mixed|string
     */
    private function uncompress(string $value, $default)
    {
        $result = gzuncompress($value);
        if ($result !== false) {
            return unserialize($result, ['allowed_classes' => false]);
        }
        $this->logger->error('Unable to gzuncomress cached value.');
        return $default;
    }
}
