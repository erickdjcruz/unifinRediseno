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

use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageManifestException;
use Sugarcrm\Sugarcrm\PackageManager\VersionComparator;
use Doctrine\DBAL\Exception as DBALException;

/**
 * External module class
 */
class UpgradeHistory extends SugarBean
{
    public const STATUS_STAGED = 'staged';
    public const STATUS_INSTALLED = 'installed';

    // Defence against the case where a package makes the whole application broken.
    // If a FATAL or PARSE error caught, the package with age below this timeout will be removed.
    public const PACKAGE_EMERGENCY_ROLLBACK_TIMEOUT_MIN = 5;

    public $new_schema = true;
    public $module_dir = 'Administration';

    // Stored fields
    public $id;
    public $filename;
    public $md5sum;
    public $type;
    public $version;
    public $status;
    public $date_entered;

    /**
     * @var string
     */
    public $date_modified;

    public $name;
    public $description;
    public $id_name;

    /**
     * serialized base_64_encoded package manifest
     * @var string
     */
    public $manifest;

    /**
     * prepared package manifest
     * @var PackageManifest
     */
    private $packageManifest;

    /**
     * serialized base_64_encoded package patch
     * @var string
     */
    public $patch;

    /**
     * is package deleted?
     * @var string
     */
    public $enabled;

    /**
     * installation progress and related values
     * @var string
     */
    public $process_status;

    /**
     * is upgrade history deleted?
     * @var bool
     */
    public $deleted;

    /**
     * published date provided by package manifest. Saved as is.
     * @var string
     */
    public $published_date;

    /**
     * Is package uninstallable;
     * @var bool
     */
    public $uninstallable;

    public $tracker_visibility = false;
    public $table_name = 'upgrade_history';
    public $object_name = 'UpgradeHistory';
    public $module_name = 'UpgradeHistory';
    public $column_fields = ['id', 'filename', 'md5sum', 'type', 'version', 'status', 'date_entered'];
    public $disable_custom_fields = true;

    /**
     * UpgradeHistory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    /**
     * return unserialized and decoded package patch
     * @return array
     */
    public function getPackagePatch(): array
    {
        $packagePatch = [];
        if (!empty($this->patch)) {
            $packagePatch = unserialize(base64_decode($this->patch), ['allowed_classes' => false]);
        }
        return $packagePatch;
    }

    /**
     * return prepared package manifest
     * @return PackageManifest
     * @throws PackageManifestException
     */
    public function getPackageManifest(): PackageManifest
    {
        if (!$this->packageManifest) {
            $manifest = unserialize(base64_decode($this->manifest), ['allowed_classes' => false]);
            // Previous manifest might be saved with empty string instead of array
            foreach (['manifest', 'installdefs', 'upgrade_manifest'] as $key) {
                if (empty($manifest[$key]) || !is_array($manifest[$key])) {
                    $manifest[$key] = [];
                }
            }
            $this->packageManifest = new PackageManifest(
                $manifest['manifest'],
                $manifest['installdefs'],
                $manifest['upgrade_manifest']
            );
        }
        return $this->packageManifest;
    }

    /**
     * is package enabled?
     * @return bool
     */
    public function isPackageEnabled(): bool
    {
        return intval($this->enabled) === 1;
    }

    /**
     * is package uninstallable?
     * @return bool
     */
    public function isPackageUninstallable(): bool
    {
        return intval($this->uninstallable) === 1;
    }

    /**
     * get installed packages by type
     * @param string $type
     * @return array
     * @throws SugarQueryException
     */
    public function getInstalledPackagesByType(string $type): array
    {
        $query = new SugarQuery();
        $query->from($this);
        $query->where()
            ->equals('status', self::STATUS_INSTALLED)
            ->equals('type', $type);
        return $this->fetchFromQuery($query);
    }

    /**
     * return all packages
     * @return SugarBean[]
     * @throws SugarQueryException
     */
    public function getPackages(): array
    {
        $query = new SugarQuery();
        $query->from($this);
        $query->orderBy('date_entered');
        return $this->fetchFromQuery($query);
    }

    /**
     * return all packages by type
     * @param string $type
     * @return SugarBean[]
     * @throws SugarQueryException
     */
    public function getPackagesByType(string $type): array
    {
        $query = new SugarQuery();
        $query->from($this);
        $query->where()->equals('type', $type);
        $query->orderBy('date_entered');
        return $this->fetchFromQuery($query);
    }

    /**
     * return module packages by status
     * @param string $status
     * @return SugarBean[]
     * @throws SugarQueryException
     */
    public function getModulePackagesByStatus(string $status): array
    {
        $query = new SugarQuery();
        $query->from($this);
        $query->where()->in('type', [PackageManifest::PACKAGE_TYPE_MODULE, PackageManifest::PACKAGE_TYPE_LANGPACK]);
        $query->where()->equals('status', $status);
        return $this->fetchFromQuery($query);
    }

    /**
     * find all packages by md5
     * @param string $md5Sum
     * @param array $queryOptions
     * @return SugarBean[]
     * @throws SugarQueryException
     */
    public function findByMd5(string $md5Sum, array $queryOptions = []): array
    {
        $query = new SugarQuery();
        $query->from($this, $queryOptions);
        $query->where()->equals('md5sum', $md5Sum);
        return $this->fetchFromQuery($query);
    }

    /**
     * retrieve by MD5
     * @param string $md5Sum
     * @param array $queryOptions
     * @return SugarBean|null
     * @throws SugarQueryException
     */
    public function retrieveByMd5(string $md5Sum, array $queryOptions = []): ?SugarBean
    {
        $result = $this->findByMd5($md5Sum, $queryOptions);
        if (!empty($result)) {
            return array_shift($result);
        }
        return null;
    }

    /**
     * find a last package that was installed less than config packageRollbackMinutes if set or
     * PACKAGE_EMERGENCY_ROLLBACK_TIMEOUT_MIN minutes ago
     */
    public function getJustInstalled(): ?UpgradeHistory
    {
        $timeoutMin = SugarConfig::getInstance()->get('packageRollbackMinutes');
        $timeoutMin = is_int($timeoutMin) ? $timeoutMin : self::PACKAGE_EMERGENCY_ROLLBACK_TIMEOUT_MIN;
        $history = BeanFactory::getBean($this->getModuleName());
        $query = new SugarQuery();
        $query->select('*');
        $query->from($history);
        $date = new SugarDateTime();
        $date->modify('-' . $timeoutMin . ' minutes');
        $query->where()->gte('date_modified', $date->asDb());
        $query->where()->equals('deleted', 0);
        $query->orderBy('date_modified', 'DESC');
        $query->limit(1);
        $result = $query->compile()->execute()->fetchAssociative();
        if ($result) {
            $history->populateFromRow($result);
            return $history;
        }
        return null;
    }

    /**
     * immediately update a record status
     */
    public function updateStatus(string $status): void
    {
        $this->status = $status;
        $this->db->updateParams(
            $this->getTableName(),
            ['status' => $this->getFieldDefinition('status'), 'id' => $this->getFieldDefinition('id')],
            ['status' => $status],
            ['id' => $this->id],
        );
    }

    /**
     * immediately update a record status
     */
    public function updateProcessStatus(array $processStatus): void
    {
        $this->process_status = json_encode($processStatus);
        $this->db->updateParams(
            $this->getTableName(),
            [
                'process_status' => $this->getFieldDefinition('process_status'),
                'id' => $this->getFieldDefinition('id'),
            ],
            ['process_status' => $this->process_status],
            ['id' => $this->id],
        );
    }

    /**
     * returns installation process status
     */
    public function getProcessStatus(): array
    {
        return (array)@json_decode($this->process_status, true);
    }

    /**
     * retrieve upgrade history by id_name
     * @param string $idName
     * @return SugarBean|null
     * @throws SugarQueryException
     */
    public function retrieveByIdName(string $idName): ?SugarBean
    {
        $query = new SugarQuery();
        $query->from($this);
        $query->where()->equals('id_name', $idName);
        $query->limit(1);
        $result = $this->fetchFromQuery($query);
        if (!empty($result)) {
            return array_shift($result);
        }
        return null;
    }

    /**
     * find all matches by source names upgrade history
     * @param UpgradeHistory $source
     * @return array
     * @throws SugarQueryException
     */
    public function findMatchesByName(UpgradeHistory $source): array
    {
        if (empty($source->id_name) && empty($source->name)) {
            return [];
        }

        $query = new SugarQuery();
        $query->from($this);

        if (!empty($source->id_name)) {
            $query->where()->equals('id_name', $source->id_name);
        } else {
            $query->where()->equals('name', $source->name);
        }

        if (!empty($source->id)) {
            $query->where()->notEquals('id', $source->id);
        }

        return $this->fetchFromQuery($query);
    }

    /**
     * Given a name check if it exists in the table
     * @param UpgradeHistory $source
     * @return null|UpgradeHistory
     * @throws SugarQueryException
     */
    public function checkForExisting(UpgradeHistory $source): ?UpgradeHistory
    {
        $result = $this->findMatchesByName($source);
        if (!empty($result)) {
            return array_shift($result);
        }
        return null;
    }

    /**
     * Return upgrade history data as array
     * @return array
     */
    public function getData(): array
    {
        return [
            'id' => $this->id,
            'name' => (string)$this->name,
            'type' => (string)$this->type,
            'status' => (string)$this->status,
            'description' => (string)$this->description,
            'version' => (string)$this->version,
            'published_data' => (string)$this->published_date,
            'date_modified' => (string)$this->date_modified,
            'enabled' => $this->isPackageEnabled(),
            'uninstallable' => $this->isPackageUninstallable(),
            'file' => $this->id,
            'file_install' => $this->id,
            'unFile' => $this->id,
        ];
    }

    /**
     * return list of not installed dependencies
     * @return array
     * @throws PackageManifestException
     * @throws DBALException
     * @throws Exception
     */
    public function getListNotInstalledDependencies(): array
    {
        $result = [];
        $requiredDependencies = $this->getPackageManifest()->getManifestValue('dependencies', []);

        $conn = $this->db->getConnection();
        $sql = sprintf(
            'SELECT version FROM %s WHERE id_name = ? AND status = ? AND deleted = 0',
            $this->db->getValidDBName($this->table_name, false, 'table')
        );

        foreach ($requiredDependencies as $dependency) {
            if (empty($dependency['id_name']) || empty($dependency['version'])) {
                continue;
            }
            $installedVersions = $conn->iterateColumn($sql, [$dependency['id_name'], self::STATUS_INSTALLED]);
            $isRequiredVersionInstalled = false;
            foreach ($installedVersions as $installedVersion) {
                if (VersionComparator::greaterThanOrEqualTo($installedVersion, $dependency['version'])) {
                    $isRequiredVersionInstalled = true;
                    break;
                }
            }
            if (!$isRequiredVersionInstalled) {
                $result[] = $dependency['id_name'];
            }
        }
        return $result;
    }

    /**
     * Get previous installed version for staged upgrade history
     * @return UpgradeHistory|null
     * @throws SugarQueryException
     */
    public function getPreviousInstalledVersion(): ?UpgradeHistory
    {
        if ($this->status === self::STATUS_INSTALLED) {
            return null;
        }

        $query = new SugarQuery();
        $query->from($this);
        $query->where()
            ->equals('id_name', $this->id_name)
            ->equals('status', self::STATUS_INSTALLED);
        $query->orderBy('date_entered', 'DESC');

        /** @var UpgradeHistory[] $versions */
        $versions = $this->fetchFromQuery($query);
        if (empty($versions)) {
            return null;
        }

        $previousInstalled = array_shift($versions);
        $ph = new VersionComparator();
        foreach ($versions as $version) {
            if (VersionComparator::greaterThan($version->version, $previousInstalled->version)) {
                $previousInstalled = $version;
            }
        }
        return $previousInstalled;
    }

    /**
     * @deprecated please use mark_deleted
     */
    public function delete(): void
    {
        $this->mark_deleted($this->id);
    }
}
