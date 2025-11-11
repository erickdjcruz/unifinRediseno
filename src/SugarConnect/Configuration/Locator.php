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

namespace Sugarcrm\Sugarcrm\SugarConnect\Configuration;

/**
 * @deprecated Will be removed in the next release.
 */
final class Locator
{
    /**
     * Use this in place of the standard configuration when not null.
     *
     * @var ?ConfigurationInterface
     */
    private static $config;

    /**
     * Returns a configuration.
     *
     * @return ConfigurationInterface
     */
    public static function get(): ConfigurationInterface
    {
        if (empty(static::$config)) {
            return new Configuration();
        }

        return static::$config;
    }

    /**
     * Replaces the standard configuration with another implementation. Useful
     * for functional tests with logic hooks, while still able to mock the
     * database and webhook.
     *
     * @param ConfigurationInterface $config Set an alternate implementation,
     *                                       like a mock.
     *
     * @return void
     */
    public static function set(ConfigurationInterface $config): void
    {
        static::$config = $config;
    }

    /**
     * Replaces an alternate implementation with the standard configuration.
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$config = null;
    }
}
