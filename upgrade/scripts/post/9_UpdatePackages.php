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
use Sugarcrm\Sugarcrm\PackageManager\PackageManager;
use Sugarcrm\Sugarcrm\PackageManager\File\PackageZipFile;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageManifestException;
use Sugarcrm\Sugarcrm\PackageManager\Factory\UpgradeHistoryFactory;

/**
 * Update all packages
 */
class SugarUpgradeUpdatePackages extends UpgradeScript
{
    public $order = 9998;

    /**
     * @var PackageManager
     */
    private $packageManager;

    /**
     * @var ModuleScanner
     */
    private $moduleScanner;

    /**
     * @var UpgradeHistoryFactory
     */
    private $upgradeHistoryFactory;

    /**
     * @var string
     */
    private $logPrefix;

    /**
     * @var string
     */
    private $uploadDir;

    public function __construct($upgrader)
    {
        parent::__construct($upgrader);
        $this->packageManager = new PackageManager();
        $this->moduleScanner = new ModuleScanner();
        $this->upgradeHistoryFactory = new UpgradeHistoryFactory();

        $this->uploadDir = UploadStream::getDir();

        $this->logPrefix = 'PackageManagerUpgradeScript: ';
    }


    public function run()
    {
        if (version_compare($this->from_version, '10.1.0', '>=')) {
            return;
        }

        // Some databases like MSSQL sets NULL to new field and ignores default.
        // Set Mango default value to deleted field instead of NULL
        $history = new UpgradeHistory();
        $qb = $this->db->getConnection()->createQueryBuilder();
        $qb->update($history->getTableName())
            ->set('deleted', 0)
            ->where($qb->expr()->isNull('deleted'));
        $qb->execute();


        // Those types use the same way to install.
        // Manage it together
        $packageTypes = [
            PackageManifest::PACKAGE_TYPE_LANGPACK,
            PackageManifest::PACKAGE_TYPE_MODULE,
            PackageManifest::PACKAGE_TYPE_THEME,
        ];
        foreach ($packageTypes as $packageType) {
            // Process installed packages first to avoid version miss matches
            $installedPackageMd5s = $this->processInstalledPackagesByType($packageType);
            $this->processStagedPackagesByType($packageType, $installedPackageMd5s);
        }

        // Those types don't have files because it's system upgrades
        // Just modify DB data
        $packageTypes = [
            PackageManifest::PACKAGE_TYPE_FULL,
            PackageManifest::PACKAGE_TYPE_PATCH,
        ];
        foreach ($packageTypes as $packageType) {
            $this->processInstalledSystemPackageByType($packageType);
            // The system can't be upgraded via ModuleInstaller.
            // Just count zip files
            $this->countStagedPackagesByType($packageType);
        }
    }

    /**
     * process installed packages
     * @param string $type
     * @return array
     * @throws PackageManifestException
     * @throws SugarQueryException
     * @throws Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Sugarcrm\Sugarcrm\PackageManager\Exception\NoPackageManifestFileException
     * @throws \Sugarcrm\Sugarcrm\PackageManager\Exception\PackageExistsException
     * @throws \Sugarcrm\Sugarcrm\PackageManager\Exception\UnableExtractFileException
     */
    private function processInstalledPackagesByType(string $type): array
    {
        $installedPackageMd5s = [];
        $history = new UpgradeHistory();
        /** @var UpgradeHistory[] $packages */
        $packages = $history->getInstalledPackagesByType($type);
        $processedCount = 0;
        foreach ($packages as $package) {
            if (empty($package->filename)) {
                $this->deleteHistoryFromDb($package);
                continue;
            }

            if (!check_file_name($package->filename)) {
                throw new \Exception('Path traversal attack vector detected');
            }

            $this->removeFileRelatedMetadata($package->filename);

            try {
                $zipFile = new PackageZipFile($package->filename, $this->packageManager->getBaseTempDir());
                $manifestFile = $zipFile->getPackageManifestFile();
                [$manifestData, $installDefs, $upgradeManifest] = $this->checkAndLoadManifestFile($manifestFile);
            } catch (\Throwable $e) {
                $this->deleteHistoryFromDb($package);
                $this->removeFileRelatedMetadata($package->filename);
                if (file_exists($package->filename)) {
                    unlink($package->filename);
                }
                $this->upgrader->log(
                    $this->logPrefix . 'Delete broken installed package information and related files. ' .
                    'Filename: ' . $package->filename . '. Error: ' . $e->getMessage() . ' ' .
                    'The installed package files should be removed manually.'
                );
                continue;
            }

            // Missed data should be filled for installed packages
            if (empty($installDefs['id']) && empty($manifestData['name'])) {
                $manifestData['name'] = pathinfo($package->filename, PATHINFO_FILENAME);
            }
            if (empty($manifestData['version'])) {
                $manifestData['version'] = file_exists($package->filename) ? filemtime($package->filename) : 1;
                $manifestData['version'] = strval($manifestData['version']);
            }

            if (!empty($manifestData['type']) && $manifestData['type'] !== $type) {
                $packageType = $manifestData['type'];
            } else {
                $packageType = $type;
            }

            $destinationFile = $this->getUpgradeTypeDir($packageType) . DIRECTORY_SEPARATOR
                . pathinfo($package->filename, PATHINFO_BASENAME);

            if (!check_file_name($destinationFile)) {
                throw new \Exception('Path traversal attack vector detected');
            }

            if ($package->filename !== $destinationFile) {
                rename($package->filename, $destinationFile);
                $package->filename = $destinationFile;
                $md5sum = md5_file($destinationFile);

                if ($package->md5sum !== $md5sum && $this->isDuplicateMd5($md5sum)) {
                    $this->deleteHistoryFromDb($package);
                    $this->upgrader->log(
                        $this->logPrefix . 'Delete broken installed package information and related files. ' .
                        'Filename: ' . $package->filename . '. md5sum already exists:  ' . $md5sum .
                        '. The installed package files should be removed manually.'
                    );
                    continue;
                }

                $package->md5sum = $md5sum;
                $this->removeFileRelatedMetadata($destinationFile);
            }

            $destinationManifestFile = $this->getUpgradeTypeDir($packageType) . DIRECTORY_SEPARATOR
                . pathinfo($package->filename, PATHINFO_FILENAME) . '-manifest.php';
            $zipFile->copyManifestFileTo($destinationManifestFile);
            $zipFile->deletePackageDir();

            $manifestData['type'] = $packageType;
            try {
                $manifest = new PackageManifest($manifestData, $installDefs, $upgradeManifest);
            } catch (Throwable $e) {
                // Wrong acceptable sugar versions
                $manifestData['acceptable_sugar_versions'] = [$this->from_version, $this->to_version];
                $manifest = new PackageManifest($manifestData, $installDefs, $upgradeManifest);
            }

            $this->upgradeHistoryFactory->populateHistoryFromManifest($package, $manifest);
            $package->save();
            $installedPackageMd5s[] = $package->md5sum;
            $processedCount++;

            $this->upgrader->log(
                $this->logPrefix . 'Successfully processed installed package. ' .
                'Name: ' . $manifest->getPackageName() . ' Type: ' . $manifest->getPackageType()
            );
        }
        $this->upgrader->log(
            $this->logPrefix . 'Process installed ' . $type . ' packages. Count: ' . $processedCount
        );
        return $installedPackageMd5s;
    }

    /**
     * remove all related package metadata files
     * @param string $baseFile
     */
    private function removeFileRelatedMetadata(string $baseFile)
    {
        $fileTemplate = pathinfo($baseFile, PATHINFO_DIRNAME)
            . DIRECTORY_SEPARATOR . pathinfo($baseFile, PATHINFO_FILENAME);
        foreach (PackageZipFile::PACKAGE_METADATA_FILE_ADDONS as $addon) {
            $file = sprintf('%s-%s.php', $fileTemplate, $addon);

            if (!check_file_name($file)) {
                throw new \Exception('Path traversal attack vector detected');
            }

            if (file_exists($file)) {
                unlink($file);
            }
        }
        $md5File = $baseFile . '.' . PackageZipFile::PACKAGE_METADATA_MD5_FILE_EXT;

        if (!check_file_name($md5File)) {
            throw new \Exception('Path traversal attack vector detected');
        }

        if (file_exists($md5File)) {
            unlink($md5File);
        }
    }

    /**
     * return package type dir
     * @param string $type
     * @return string
     */
    private function getUpgradeTypeDir(string $type): string
    {
        $dir = $this->packageManager->getBaseUpgradeDir() . DIRECTORY_SEPARATOR . $type;

        if (!is_dir($dir)) {
            sugar_mkdir($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * Check if a package's md5Sum already exists in db
     * @param string $md5sum
     * @return bool
     */
    private function isDuplicateMd5(string $md5sum): bool
    {
        try {
            $history = new UpgradeHistory();
            $md5Match = $history->retrieveByMd5($md5sum);

            if (empty($md5Match->id)) {
                return false;
            }
        } catch (SugarQueryException $e) {
            $this->upgrader->log($this->logPrefix . 'SQL error: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * delete old broken history from DB because it can't be used
     * @param UpgradeHistory $history
     * @throws Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    private function deleteHistoryFromDb(UpgradeHistory $history)
    {
        if (!empty($history->id)) {
            $this->db->getConnection()->delete($history->getTableName(), ['id' => $history->id]);
            $this->upgrader->log($this->logPrefix . 'successfully delete broken history ' . $history->id);
        }
    }

    /**
     * check and load manifest
     * @param string $manifestFile
     * @return array
     */
    private function checkAndLoadManifestFile(string $manifestFile): array
    {
        // check manifest file
        $issues = $this->moduleScanner->scanFile($manifestFile);
        if (!empty($issues)) {
            $exception = new PackageManifestException();
            $exception->setErrorDescription($this->moduleScanner->getFormattedIssues());
            throw $exception;
        }

        $manifest = $installdefs = $upgrade_manifest = [];
        require $manifestFile;

        if (!is_array($manifest)) {
            $manifest = [];
        }

        if (!is_array($installdefs)) {
            $installdefs = [];
        }

        if (!is_array($upgrade_manifest)) {
            $upgrade_manifest = [];
        }

        return [$manifest, $installdefs, $upgrade_manifest];
    }

    /**
     * @param string $type
     * @param array $skippedMd5s
     */
    private function processStagedPackagesByType(string $type, array $skippedMd5s)
    {
        $typeDir = $this->getUpgradeTypeDir($type);
        $zipFiles = new \GlobIterator($typeDir . DIRECTORY_SEPARATOR . '*.zip');
        $processedCount = 0;
        foreach ($zipFiles as $zipFile) {
            if ($zipFile->isDir() || in_array($zipFile->getFilename(), ['.', '..'])) {
                continue;
            }
            $relatedFile = $typeDir . DIRECTORY_SEPARATOR . $zipFile->getFilename();
            if (in_array(md5_file($relatedFile), $skippedMd5s, true)) {
                continue;
            }
            $this->removeFileRelatedMetadata($zipFile->getPathname());
            $destinationZipFile = $this->uploadDir . DIRECTORY_SEPARATOR . $zipFile->getFilename();
            rename($zipFile->getPathname(), $destinationZipFile);
            try {
                $packageZipFile = new PackageZipFile($destinationZipFile, $this->packageManager->getBaseTempDir());
                $history = $this->packageManager->uploadPackageFromFile($packageZipFile, $type);
                $manifest = $history->getPackageManifest();
            } catch (\Throwable $e) {
                unlink($destinationZipFile);
                $this->upgrader->log(
                    $this->logPrefix .
                    'Delete broken staged package file ' . $relatedFile . ' ' .
                    'Error: ' . $e->getMessage()
                );
                continue;
            }
            $processedCount++;
            $this->upgrader->log(
                $this->logPrefix . 'Successfully processed staged package. ' .
                'Name: ' . $manifest->getPackageName() . ' Type: ' . $manifest->getPackageType()
            );
        }
        $this->upgrader->log(
            $this->logPrefix . 'Process staged ' . $type . ' packages. Count: ' . $processedCount
        );
    }

    /**
     * change data for installed system packages
     * @param string $type
     * @throws PackageManifestException
     * @throws SugarQueryException
     * @throws Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    private function processInstalledSystemPackageByType(string $type)
    {
        $history = new UpgradeHistory();
        /** @var UpgradeHistory[] $packages */
        $packages = $history->getInstalledPackagesByType($type);
        $processedCount = 0;
        foreach ($packages as $package) {
            if (empty($package->filename)) {
                $this->deleteHistoryFromDb($package);
                continue;
            }

            try {
                $zipFile = new PackageZipFile($package->filename, $this->packageManager->getBaseTempDir());
                $manifestFile = $zipFile->getPackageManifestFile();
                [$manifestData, $installDefs, $upgradeManifest] = $this->checkAndLoadManifestFile($manifestFile);
            } catch (\Throwable $e) {
                $rawManifestData = unserialize(base64_decode($package->manifest), ['allowed_classes' => false]);
                foreach (['manifest', 'installdefs', 'upgrade_manifest'] as $key) {
                    if (empty($rawManifestData[$key]) || !is_array($rawManifestData[$key])) {
                        $rawManifestData[$key] = [];
                    }
                }
                $manifestData = $rawManifestData['manifest'];
                $installDefs = $rawManifestData['installdefs'];
                $upgradeManifest = $rawManifestData['upgrade_manifest'];
            }

            // Missed data should be filled for installed packages
            if (empty($installDefs['id']) && empty($manifestData['name'])) {
                $manifestData['name'] = pathinfo($package->filename, PATHINFO_FILENAME);
            }

            if (empty($manifestData['version'])) {
                $manifestData['version'] = $this->to_version;
            }

            $manifestData['type'] = $type;
            try {
                $manifest = new PackageManifest($manifestData, $installDefs, $upgradeManifest);
            } catch (Throwable $e) {
                // Wrong acceptable sugar versions
                $manifestData['acceptable_sugar_versions'] = ['exact_matches' => $this->from_version];
                $manifest = new PackageManifest($manifestData, $installDefs, $upgradeManifest);
            }

            $this->upgradeHistoryFactory->populateHistoryFromManifest($package, $manifest);
            $package->uninstallable = false;
            $package->save();
            $processedCount++;

            $this->upgrader->log(
                $this->logPrefix . 'Successfully processed installed system package. ' .
                'Name: ' . $manifest->getPackageName() . ' Type: ' . $manifest->getPackageType()
            );
        }

        $this->upgrader->log(
            $this->logPrefix . 'Process installed system ' . $type . ' packages. Count: ' . $processedCount
        );
    }

    /**
     * count staged package and log it
     * @param string $type
     */
    private function countStagedPackagesByType(string $type)
    {
        $typeDir = $this->getUpgradeTypeDir($type);
        $zipFiles = new \GlobIterator($typeDir . DIRECTORY_SEPARATOR . '*.zip');
        $this->upgrader->log(
            $this->logPrefix . 'Count staged ' . $type . ' packages. Count: ' . $zipFiles->count()
        );
    }
}
