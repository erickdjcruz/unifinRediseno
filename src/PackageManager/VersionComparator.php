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

namespace Sugarcrm\Sugarcrm\PackageManager;

/**
 * Compare package versions with some workarounds for varying versioning schemas
 *
 * Class VersionComparator
 * @package Sugarcrm\Sugarcrm\PackageManager
 */
class VersionComparator
{
    public static function compare(string $version1, string $operator, string $version2): bool
    {
        return self::versionCompare($version1, $version2, $operator);
    }

    /**
     * @param string $version1
     * @param string $version2
     * @param string|null $operator same as in version_compare
     * @return bool|int
     */
    private static function versionCompare(string $version1, string $version2, ?string $operator)
    {
        $version1 = ltrim($version1, 'v');
        $version2 = ltrim($version2, 'v');
        return version_compare($version1, $version2, $operator);
    }

    public static function greaterThan(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '>');
    }

    public static function greaterThanOrEqualTo(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '>=');
    }

    public static function lessThan(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '<');
    }

    public static function lessThanOrEqualTo(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '<=');
    }

    public static function equalTo(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '=');
    }

    public static function notEqualTo(string $version1, string $version2): bool
    {
        return self::versionCompare($version1, $version2, '!=');
    }
}
