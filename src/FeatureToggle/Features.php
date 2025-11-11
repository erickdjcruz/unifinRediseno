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

namespace Sugarcrm\Sugarcrm\FeatureToggle;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class Features implements FeatureFlag, FeatureToggler, FeaturesContext
{
    /**
     * @var array<string,bool> $features
     */
    private array $features;
    private string $version;
    private array $classMap = [];

    /**
     * @param string $version the version of SugarCRM
     */
    public function __construct(string $version)
    {
        $this->version = $version;
        $this->loadFeatures();
    }

    public function isEnabled(string $name): bool
    {
        $this->checkName($name);
        return $this->features[$name];
    }

    public function enable(string $name): void
    {
        $this->ensureToggleable($name);
        $this->features[$name] = true;
    }

    public function disable(string $name): void
    {
        $this->ensureToggleable($name);
        $this->features[$name] = false;
    }

    public function getAllEnabled(): array
    {
        return array_keys(array_filter($this->features));
    }

    /**
     * Loads features. Each feature has a state corresponding to the current SugarCRM version
     * @return void
     */
    private function loadFeatures(): void
    {
        $recursiveIteratorIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/Features'));
        $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.php$/', RecursiveRegexIterator::GET_MATCH);
        foreach ($regexIterator as $match) {
            $classFile = $match[0];
            $feature = '\\Sugarcrm\\Sugarcrm\\FeatureToggle\\Features\\' . basename($classFile, '.php');
            /**
             * @var Feature $feature
             */
            $this->classMap[$feature::getName()] = $feature;
            $this->features[$feature::getName()] = $feature::isEnabledIn($this->version);
        }
    }

    /**
     * @param string $name
     * @return void
     */
    private function checkName(string $name): void
    {
        if (!array_key_exists($name, $this->features)) {
            throw new \DomainException('Unknown feature ' . $name);
        }
    }

    private function ensureToggleable(string $name): void
    {
        $this->checkName($name);
        $feature = $this->classMap[$name];
        /**
         * @var Feature $feature
         */
        if (!$feature::isToggleableIn($this->version)) {
            throw new \DomainException(sprintf('%s can not be toggled in %s', $name, $this->version));
        }
    }
}
