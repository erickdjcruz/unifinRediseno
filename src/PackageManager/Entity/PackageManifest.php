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

namespace Sugarcrm\Sugarcrm\PackageManager\Entity;

use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageManifestException;

class PackageManifest
{
    public const PACKAGE_TYPE_FULL = 'full';
    public const PACKAGE_TYPE_LANGPACK = 'langpack';
    public const PACKAGE_TYPE_MODULE = 'module';
    public const PACKAGE_TYPE_PATCH = 'patch';
    public const PACKAGE_TYPE_THEME = 'theme';

    public const PACKAGE_TYPES = [
        self::PACKAGE_TYPE_FULL,
        self::PACKAGE_TYPE_LANGPACK,
        self::PACKAGE_TYPE_MODULE,
        self::PACKAGE_TYPE_PATCH,
        self::PACKAGE_TYPE_THEME,
    ];

    public const MODULE_PACKAGE_TYPES = [
        self::PACKAGE_TYPE_MODULE,
        self::PACKAGE_TYPE_THEME,
        self::PACKAGE_TYPE_LANGPACK,
    ];

    /**
     * package manifest
     * @var array
     */
    private $manifest;

    /**
     * package install defs
     * @var array
     */
    private $installDefs;

    /**
     * package upgrade manifest
     * @var array
     */
    private $upgradeManifest;

    /**
     * create manifest entity and check for type and accepted versions.
     * Manifest is broken without type and accepted sugar versions and can't be processed.
     *
     * @param array $manifest
     * @param array $installDefs
     * @param array $upgradeManifest
     * @throws PackageManifestException
     */
    public function __construct(array $manifest, array $installDefs, array $upgradeManifest)
    {
        $this->manifest = $manifest;
        $this->installDefs = $installDefs;
        $this->upgradeManifest = $upgradeManifest;

        // how will package be processed without unique id or name?
        // it required for detect upgrades
        if (empty($this->installDefs['id']) && empty($this->manifest['name'])) {
            throw new PackageManifestException('ML_NO_PACKAGE_NAME');
        }

        // Don't allow packages without version
        if (empty($this->manifest['version'])) {
            throw new PackageManifestException('ML_NO_PACKAGE_VERSION');
        }

        // Don't allow packages without type
        if (empty($this->manifest['type'])) {
            throw new PackageManifestException('ERROR_MANIFEST_TYPE');
        }
        $this->manifest['type'] = strtolower($this->manifest['type']);
        if (!in_array($this->manifest['type'], self::PACKAGE_TYPES, true)) {
            throw new PackageManifestException('ERROR_PACKAGE_TYPE');
        }

        $this->fixAcceptableSugarVersions();
        if (empty($this->manifest['acceptable_sugar_versions'])) {
            throw new PackageManifestException('ERROR_VERSION_MISSING');
        }
    }

    /**
     * return package name or install def id
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->manifest['name'] ?? $this->installDefs['id'];
    }

    /**
     * return install def id or package name
     * @return string
     */
    public function getPackageIdName(): string
    {
        return $this->installDefs['id'] ?? $this->manifest['name'];
    }

    /**
     * return package version
     * @return string
     */
    public function getPackageVersion(): string
    {
        return $this->manifest['version'];
    }

    /**
     * return package type
     * @return string
     */
    public function getPackageType(): string
    {
        return $this->manifest['type'];
    }

    /**
     * return acceptable sugar versions
     * @return array
     */
    public function getAcceptableSugarVersions(): array
    {
        return $this->manifest['acceptable_sugar_versions'];
    }

    /**
     * return data value
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getManifestValue($key, $default = null)
    {
        return $this->manifest[$key] ?? $default;
    }

    /**
     * return install defs value
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getInstallDefsValue($key, $default = null)
    {
        return $this->installDefs[$key] ?? $default;
    }

    /**
     * Is package uninstallable?
     * @return bool
     */
    public function isPackageUninstallable(): bool
    {
        if (isset($this->manifest['is_uninstallable'])) {
            return $this->convertOldBoolValue($this->manifest['is_uninstallable'], true);
        }
        return true;
    }

    /**
     * Should DB tables be removed on uninstall?
     * @return bool
     */
    public function shouldTablesBeRemoved(): bool
    {
        if (isset($this->manifest['remove_tables'])) {
            return $this->convertOldBoolValue($this->manifest['remove_tables'], true);
        }
        return true;
    }

    /**
     * return all object data as array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'manifest' => $this->manifest,
            'installdefs' => $this->installDefs,
            'upgrade_manifest' => empty($this->upgradeManifest) ? [] : $this->upgradeManifest,
        ];
    }

    /**
     * convert old sugar bool field value into php boolean
     * if string doesn't equal 'false', returns TRUE, otherwise returns FALSE
     * if integer equals 1, returns TRUE, otherwise returns FALSE
     * Return value as is for boolean
     *
     * @param $value
     * @param bool $default
     * @return bool
     */
    private function convertOldBoolValue($value, bool $default): bool
    {
        if (is_string($value)) {
            $value = strtolower($value);
            return $value !== 'false';
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        return $default;
    }

    /**
     * For cases in which the manifest was written incorrectly we need to create a comparator.
     * For now we will assume that major and minor version matches are acceptable.
     * Adds version regex strings to the acceptable sugar versions array when needed.
     * Packages built prior to 7.7.1.0 (BR-4088) have incompatible relationship metadata structure
     */
    private function fixAcceptableSugarVersions(): void
    {
        $addBuildInVersionCheck = true;
        $regexMatches = [];
        if (!empty($this->manifest['acceptable_sugar_versions'])) {
            $versions = $this->manifest['acceptable_sugar_versions'];
            if (!isset($versions['exact_matches']) && !isset($versions['regex_matches'])) {
                foreach ($versions as $index => $version) {
                    if (empty($version) || !is_scalar($version)) {
                        continue;
                    }
                    $parts = explode('.', (string) $version);
                    $countParts = safeCount($parts);
                    if ($countParts < 3) {
                        $parts = array_merge($parts, array_fill($countParts, 3 - $countParts, '([0-9]+)'));
                    }
                    $regexMatches[] = '^' . implode('\.', $parts);
                    $addBuildInVersionCheck = false;
                }
            } else {
                $addBuildInVersionCheck = false;
            }
        }
        if ($addBuildInVersionCheck
            && !empty($this->manifest['built_in_version'])
            && version_compare($this->manifest['built_in_version'], '7.7.1.0', '>=')) {
            $buildVersionParts = explode('.', $this->manifest['built_in_version']);
            $regexMatches[] = '^' . $buildVersionParts[0] . '\.([0-9]+)\.([0-9]+)';
        }
        if (!empty($regexMatches)) {
            $this->manifest['acceptable_sugar_versions'] = ['regex_matches' => $regexMatches];
        }
    }
}
