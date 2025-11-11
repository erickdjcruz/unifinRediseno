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

namespace Sugarcrm\Sugarcrm\ProductDefinition\Config;

use Sugarcrm\Sugarcrm\ProductDefinition\Config\Cache\DbCache;

class Config
{
    /**
     * Mango config.php key
     */
    public const SUGAR_CONFIG_KEY = 'product_definition';

    /**
     * default product definition
     */
    public const DEFAULT_CONFIG = [
        'type' => 'Http',
        'options' => [
            'base_uri' => Source\HttpSource::DEFAULT_BASE_URI,
        ],
    ];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Source\SourceInterface
     */
    protected $source;

    /**
     * @var Cache\CacheInterface
     */
    protected $cache;

    /**
     * constructor.
     * @param \SugarConfig $sugarConfig
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct(\SugarConfig $sugarConfig)
    {
        $this->config = $sugarConfig->get(static::SUGAR_CONFIG_KEY, static::DEFAULT_CONFIG);

        if (empty($this->config['type'])) {
            throw new \InvalidArgumentException('product definition config type should not be empty');
        }

        $this->config['type'] = ucfirst(strtolower($this->config['type']));
        $this->config['options'] = $this->config['options'] ?? [];
    }

    /**
     * Return product definition
     *
     * @return array
     * @throws \Exception
     */
    public function getProductDefinition(): array
    {
        if ($this->isInstallInProgress()) {
            return [];
        }
        $definition = $this->getCache()->get();
        if (is_null($definition)) {
            return [];
        }
        return (array)json_decode($definition, true);
    }

    /**
     * update product definition in cache from source
     * @return bool
     */
    public function updateProductDefinition(): bool
    {
        $result = false;

        $definition = $this->getSource()->getDefinition();
        if (!empty($definition)) {
            $result = true;
            $this->getCache()->set($definition);
        }

        return $result;
    }

    /**
     * Create config source by type
     * @return Source\SourceInterface
     * @throws \RuntimeException
     */
    protected function getSource(): Source\SourceInterface
    {
        if (!$this->source) {
            $sourceClass = 'Sugarcrm\\Sugarcrm\\ProductDefinition\\Config\\Source\\' . $this->config['type'] . 'Source';
            if (!class_exists($sourceClass)) {
                throw new \RuntimeException('product definition config source class does not exist');
            }
            $this->source = new $sourceClass($this->config['options']);
        }
        return $this->source;
    }

    /**
     * Create config cache by type
     * @return Cache\CacheInterface|null
     */
    protected function getCache(): ?Cache\CacheInterface
    {
        if (!$this->cache) {
            $this->cache = new DbCache();
        }
        return $this->cache;
    }

    /**
     * Is Mango install in progress?
     * @return bool
     */
    protected function isInstallInProgress(): bool
    {
        global $installing;
        return $installing === true;
    }
}
