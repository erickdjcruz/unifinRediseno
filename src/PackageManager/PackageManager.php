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

use ErrorException;
use LoggerManager;
use MlpLogger;
use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageExistsException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageManagerException;
use Sugarcrm\Sugarcrm\PackageManager\Factory\UpgradeHistoryFactory;
use Sugarcrm\Sugarcrm\PackageManager\File\PackageZipFile;

use Throwable;
use ModuleScanner;
use SugarConfig;
use UpgradeHistory;
use SugarQueryException;
use SugarAutoLoader;
use ModuleInstaller;
use Doctrine\DBAL\Exception as DBALException;
use SugarException;

class PackageManager
{
    /**
     * @var ModuleScanner
     */
    private $moduleScanner;

    /**
     * @var UpgradeHistoryFactory
     */
    private $upgradeHistoryFactory;

    /**
     * Base dir for temporary package files
     * @var string
     */
    private $baseTempDir;

    /**
     * Base dir for source package files
     * @var string
     */
    private $baseUpgradeDir;

    /**
     * @var bool
     */
    private $isPackageScanEnabled;

    /**
     * @var string
     */
    private $sugarVersion;

    /**
     * @var string
     */
    private $sugarFlavor;

    /**
     * Should module installer show output messages?
     * @var bool
     */
    private $silent = true;

    /**
     * $this->silent backup variable
     * @var bool
     */
    private $silentBackup = true;

    /**
     * PackageManager constructor.
     */
    public function __construct()
    {
        global $sugar_version, $sugar_flavor;

        $this->isPackageScanEnabled = (bool)SugarConfig::getInstance()->get('moduleInstaller.packageScan', false);
        $this->sugarVersion = $sugar_version;
        $this->sugarFlavor = $sugar_flavor;

        $this->moduleScanner = new ModuleScanner();
        $this->upgradeHistoryFactory = new UpgradeHistoryFactory();

        $this->baseTempDir = sugar_cached('mlp_temp');
        sugar_mkdir($this->baseTempDir, null, true);
        $this->baseUpgradeDir = 'upgrades';
    }

    /**
     * return base temp dir
     * @return string
     */
    public function getBaseTempDir(): string
    {
        return $this->baseTempDir;
    }

    /**
     * set base temp dir
     * @param string $baseTempDir
     * @return PackageManager
     */
    public function setBaseTempDir(string $baseTempDir): PackageManager
    {
        $this->baseTempDir = $baseTempDir;
        return $this;
    }

    /**
     * get base upgrade temp dir
     * @return string
     */
    public function getBaseUpgradeDir(): string
    {
        return $this->baseUpgradeDir;
    }

    /**
     * set base upgrade temp dir
     * @param string $baseUpgradeDir
     * @return PackageManager
     */
    public function setBaseUpgradeDir(string $baseUpgradeDir): PackageManager
    {
        $this->baseUpgradeDir = $baseUpgradeDir;
        return $this;
    }

    /**
     * Provide module installer with check for customization
     * @return ModuleInstaller
     */
    protected function getModuleInstaller(): ModuleInstaller
    {
        SugarAutoLoader::requireWithCustom('ModuleInstall/ModuleInstaller.php');
        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');

        $moduleInstaller = new $moduleInstallerClass();
        $moduleInstaller->silent = $this->silent;

        return $moduleInstaller;
    }

    /**
     * @param bool $isPackageScanEnabled
     * @return PackageManager
     */
    public function setIsPackageScanEnabled(bool $isPackageScanEnabled): PackageManager
    {
        $this->isPackageScanEnabled = $isPackageScanEnabled;
        return $this;
    }

    /**
     * @param string $sugarVersion
     * @return PackageManager
     */
    public function setSugarVersion(string $sugarVersion): PackageManager
    {
        $this->sugarVersion = $sugarVersion;
        return $this;
    }

    /**
     * @param string $sugarFlavor
     * @return PackageManager
     */
    public function setSugarFlavor(string $sugarFlavor): PackageManager
    {
        $this->sugarFlavor = $sugarFlavor;
        return $this;
    }

    /**
     * @param ModuleScanner $moduleScanner
     * @return PackageManager
     */
    public function setModuleScanner(ModuleScanner $moduleScanner): PackageManager
    {
        $this->moduleScanner = $moduleScanner;
        return $this;
    }

    /**
     * @param UpgradeHistoryFactory $upgradeHistoryFactory
     * @return PackageManager
     */
    public function setUpgradeHistoryFactory(UpgradeHistoryFactory $upgradeHistoryFactory): PackageManager
    {
        $this->upgradeHistoryFactory = $upgradeHistoryFactory;
        return $this;
    }

    /**
     * @param bool $silent
     * @return PackageManager
     */
    public function setSilent(bool $silent): PackageManager
    {
        $this->silent = $silent;
        return $this;
    }

    /**
     * @return PackageManager
     */
    public function backupSilentValue(): PackageManager
    {
        $this->silentBackup = $this->silent;
        return $this;
    }

    /**
     * @return PackageManager
     */
    public function restoreSilentValue(): PackageManager
    {
        $this->silent = $this->silentBackup;
        return $this;
    }

    /**
     * upload package from file to system
     * @param PackageZipFile $zipFile
     * @param string $expectedPackageType
     * @return UpgradeHistory
     * @throws Exception\InvalidPackageException
     * @throws Exception\ModuleScannerException
     * @throws Exception\NoPackageManifestFileException
     * @throws Exception\NotAcceptableTypeException
     * @throws Exception\OnlyPackagePatchTypeAcceptableException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws PackageExistsException
     * @throws PackageManagerException
     * @throws SugarQueryException
     */
    public function uploadPackageFromFile(PackageZipFile $zipFile, string $expectedPackageType): UpgradeHistory
    {
        if ($this->isPackageScanEnabled) {
            $zipFile->extractPackage();
            $this->scanPackage($zipFile->getPackageDir());
        }
        $manifestFile = $zipFile->getPackageManifestFile();
        $manifest = $this->checkAndGetManifestFromFile($manifestFile);
        $this->validateManifest($manifest, $expectedPackageType);

        $history = $this->upgradeHistoryFactory->createUpgradeHistory(
            $manifest,
            $zipFile->getRelativeZipFilePath(),
            UpgradeHistory::STATUS_STAGED
        );

        $nameMatch = $history->checkForExisting($history);
        if ($nameMatch && VersionComparator::greaterThanOrEqualTo($nameMatch->version, $manifest->getPackageVersion())) {
            throw new Exception\PackageNewerExistsException(null, [$nameMatch->version]);
        }

        $baseUpgradeTypeDir = $this->getUpgradeTypeDir($manifest->getPackageType());
        $baseFileName = $this->getUniquePackageFileName($history);
        $destinationFile = $baseUpgradeTypeDir . DIRECTORY_SEPARATOR . $baseFileName;
        $destinationManifestPath = $baseUpgradeTypeDir . DIRECTORY_SEPARATOR
            . pathinfo($baseFileName, PATHINFO_FILENAME) . '-manifest.php';

        $zipFile->copyZipFileTo($destinationFile);
        $zipFile->copyManifestFileTo($destinationManifestPath);

        $history->filename = $destinationFile;
        // restore old upgrade history
        if (!empty($history->deleted)) {
            $history->mark_undeleted($history->id);
        }
        $history->save();

        return $history;
    }

    /**
     * return upgrade type dir
     * @param string $type
     * @return string
     */
    protected function getUpgradeTypeDir(string $type): string
    {
        $baseUpgradeTypeDir = $this->getBaseUpgradeDir() . DIRECTORY_SEPARATOR . $type;
        if (!file_exists($baseUpgradeTypeDir)) {
            sugar_mkdir($baseUpgradeTypeDir, null, true);
        }
        return $baseUpgradeTypeDir;
    }

    /**
     * Check and get package manifest from file
     * Require manifest.php in isolated function is required.
     * Because some manifest comes with logic inside and could rewrite local values.
     *
     * @param string $manifestFile
     * @return PackageManifest
     *
     * @throws Exception\PackageManifestException
     */
    protected function checkAndGetManifestFromFile(string $manifestFile): PackageManifest
    {
        // lock config to detect changes by manifest file
        $this->moduleScanner->lockConfig();

        // check manifest file
        $issues = $this->moduleScanner->scanFile($manifestFile);
        if (!empty($issues)) {
            $exception = new Exception\PackageManifestException();
            $exception->setErrorDescription($this->moduleScanner->getFormattedIssues())
                ->setModuleScanner($this->moduleScanner);
            throw $exception;
        }
        if ($this->isPackageScanEnabled) {
            $strictIssues = $this->moduleScanner->strictManifestScan($manifestFile);
            if (!empty($strictIssues)) {
                $exception = new Exception\PackageManifestException();
                $exception->setErrorDescription($this->moduleScanner->getFormattedIssues())
                    ->setModuleScanner($this->moduleScanner);
                throw $exception;
            }
        }

        $manifest = $installdefs = $upgrade_manifest = [];
        require $manifestFile;

        // check config for changes
        $issues = $this->moduleScanner->checkConfig($manifestFile);
        if (!empty($issues)) {
            $exception = new Exception\PackageManifestException();
            $exception->setErrorDescription($this->moduleScanner->getFormattedIssues());
            throw $exception;
        }

        return new PackageManifest($manifest, $installdefs, $upgrade_manifest);
    }

    /**
     * Scan package
     * @param string $packageDir
     * @throws Exception\ModuleScannerException
     */
    private function scanPackage(string $packageDir): void
    {
        $this->moduleScanner->scanPackage($packageDir);
        if ($this->moduleScanner->hasIssues()) {
            $exception = new Exception\ModuleScannerException();
            $exception
                ->setErrorDescription($this->moduleScanner->getFormattedIssues())
                ->setModuleScanner($this->moduleScanner);
            throw $exception;
        }
    }

    /**
     * validate manifest
     *
     * @param PackageManifest $manifest
     * @param string $expectedType
     * @throws Exception\NotAcceptableTypeException
     * @throws Exception\OnlyPackagePatchTypeAcceptableException
     * @throws Exception\PackageManagerException
     */
    protected function validateManifest(PackageManifest $manifest, string $expectedType): void
    {
        if (!$this->isSugarVersionAcceptable($manifest)) {
            throw (new Exception\IncompatibleSugarVersionException())->setErrorDescription($this->sugarVersion);
        }

        if (!$this->isSugarFlavorAcceptable($manifest)) {
            throw (new Exception\IncompatibleSugarFlavorException())->setErrorDescription($this->sugarVersion);
        }

        $uploadedType = $manifest->getPackageType();
        if ($uploadedType === PackageManifest::PACKAGE_TYPE_MODULE
            && !in_array($expectedType, PackageManifest::MODULE_PACKAGE_TYPES, true)) {
            throw new Exception\NotAcceptableTypeException();
        } elseif ($expectedType === 'default' && $uploadedType === 'patch') {
            throw new Exception\OnlyPackagePatchTypeAcceptableException();
        }
    }

    /**
     * Is sugar version acceptable?
     *
     * @param PackageManifest $manifest
     * @return bool
     */
    private function isSugarVersionAcceptable(PackageManifest $manifest): bool
    {
        $manifestAcceptableVersions = $manifest->getAcceptableSugarVersions();
        if (!empty($manifestAcceptableVersions['exact_matches'])) {
            foreach ($manifestAcceptableVersions['exact_matches'] as $match) {
                if (version_compare($this->sugarVersion, $match, '==')) {
                    return true;
                }
            }
        }
        if (!empty($manifestAcceptableVersions['regex_matches'])) {
            foreach ($manifestAcceptableVersions['regex_matches'] as $match) {
                if (!empty($match) && preg_match("/$match/", $this->sugarVersion)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Is sugar flavor acceptable?
     *
     * @param PackageManifest $manifest
     * @return bool
     */
    private function isSugarFlavorAcceptable(PackageManifest $manifest): bool
    {
        $result = true;
        $manifestAcceptableFlavors = $manifest->getManifestValue('acceptable_sugar_flavors', []);
        if (!empty($this->sugarFlavor) && !empty($manifestAcceptableFlavors)) {
            $result = false;
            foreach ($manifestAcceptableFlavors as $flavor) {
                if (strtolower($this->sugarFlavor) === strtolower($flavor)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * return list of staged packages
     * @return UpgradeHistory[]
     * @throws SugarQueryException
     */
    public function getStagedModulePackages(): array
    {
        return (new UpgradeHistory())->getModulePackagesByStatus(UpgradeHistory::STATUS_STAGED);
    }

    /**
     * return list of installed packages
     * @return UpgradeHistory[]
     * @throws SugarQueryException
     */
    public function getInstalledModulePackages(): array
    {
        return (new UpgradeHistory())->getModulePackagesByStatus(UpgradeHistory::STATUS_INSTALLED);
    }

    /**
     * return list of all module packages
     * @return UpgradeHistory[]
     * @throws SugarQueryException
     */
    public function getModulePackages(): array
    {
        return (new UpgradeHistory())->getPackagesByType(PackageManifest::PACKAGE_TYPE_MODULE);
    }

    /**
     * create package zip file
     * @param string $file
     * @param string $baseDir
     * @return PackageZipFile
     * @throws Exception\NoPackageFileException
     */
    protected function createPackageZipFile(string $file, string $baseDir): PackageZipFile
    {
        return new PackageZipFile($file, $baseDir);
    }

    /**
     * delete package
     * @param UpgradeHistory $history
     * @throws Exception\NoPackageFileException
     * @throws PackageManagerException
     */
    public function deletePackage(UpgradeHistory $history): void
    {
        if (!empty($history->deleted)) {
            throw new PackageManagerException('ERR_UW_NO_PACKAGE');
        }

        if ($history->status === UpgradeHistory::STATUS_INSTALLED) {
            throw new PackageManagerException('ERR_UW_REMOVE_INSTALLED_PACKAGE');
        }
        $zipFile = $this->createPackageZipFile($history->getFileName(), $this->getBaseUpgradeDir());
        $zipFile->removeSelfWithMetadata();
        $history->mark_deleted($history->id);
    }

    /**
     * Install Package
     *
     * @param UpgradeHistory $history
     * @return UpgradeHistory
     * @throws Exception\NoPackageFileException
     * @throws Exception\NotAcceptableTypeException
     * @throws Exception\OnlyPackagePatchTypeAcceptableException
     * @throws Exception\PackageManagerException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws DBALException
     * @throws SugarQueryException
     * @throws SugarException
     */
    public function installPackage(UpgradeHistory $history): UpgradeHistory
    {
        if (!empty($history->deleted)) {
            throw new PackageManagerException('ERR_UW_NO_PACKAGE');
        }

        if ($history->status !== UpgradeHistory::STATUS_STAGED) {
            throw new PackageManagerException('ERR_UW_PACKAGE_ALREADY_INSTALLED');
        }
        $history->updateProcessStatus([]);

        $manifest = $history->getPackageManifest();
        $this->validateManifest($manifest, $manifest->getPackageType());

        $requiredDependencies = $history->getListNotInstalledDependencies();
        if (!empty($requiredDependencies)) {
            $moduleInstaller = $this->getModuleInstaller();
            $moduleInstaller->setUpgradeHistory($history);
            $errorMessage = translate('ERR_UW_NO_DEPENDENCY', 'Administration') . ' ' . implode(', ', $requiredDependencies);
            $moduleInstaller->setInstallationError($errorMessage);
            throw (new PackageManagerException('ERR_UW_NO_DEPENDENCY'))->setErrorDescription($requiredDependencies);
        }

        $zipFile = $this->createPackageZipFile($history->getFileName(), $this->getBaseTempDir());
        $zipFile->extractPackage();

        $isPackageModuleOrPatch = in_array($manifest->getPackageType(), [
            PackageManifest::PACKAGE_TYPE_MODULE,
            PackageManifest::PACKAGE_TYPE_PATCH,
        ], true);

        register_shutdown_function(function ($history) {
            $err = error_get_last();
            if (in_array($err['type'], [E_ERROR, E_COMPILE_ERROR])) {
                LoggerManager::getLogger()->fatal(
                    'Package Install process was not finished successfully'
                );
                LoggerManager::getLogger()->fatal(sprintf(
                    'Error: %s in %s at line: %s',
                    $err['message'],
                    $err['file'],
                    $err['line']
                ));
            } else {
                return;
            }

            $moduleInstaller = $this->getModuleInstaller();
            $moduleInstaller->setUpgradeHistory($history);
            $moduleInstaller->setInstallationDone();

            if (SugarConfig::getInstance()->get('uninstallOnError', true)) {
                $this->forceUninstall($history, true);
                throw new PackageManagerException('ERR_UW_PACKAGE_NOT_INSTALLED');
            } else {
                throw new PackageManagerException('ERR_UW_PACKAGE_INSTALLED_WITH_ERROR');
            }
        }, $history);

        try {
            if ($isPackageModuleOrPatch) {
                $zipFile->runPackageScript(PackageZipFile::PRE_INSTALL_FILE, $this->silent);
            }

            $previousInstalled = $history->getPreviousInstalledVersion();
            $moduleInstaller = $this->getModuleInstaller();
            $moduleInstaller->setPatch($history->getPackagePatch());
            $moduleInstaller->setUpgradeHistory($history);
            if ($previousInstalled && $manifest->getManifestValue('uninstall_before_upgrade', false)) {
                $this->backupSilentValue()->setSilent(true);
                $this->uninstallPackage(
                    $previousInstalled,
                    $previousInstalled->getPackageManifest()->shouldTablesBeRemoved()
                );
                $this->restoreSilentValue();
                $moduleInstaller->install($zipFile->getPackageDir(), true, $previousInstalled->version);
            } else {
                $moduleInstaller->install($zipFile->getPackageDir());
            }

            if ($isPackageModuleOrPatch) {
                $zipFile->runPackageScript(PackageZipFile::POST_INSTALL_FILE, $this->silent);
            }
        } catch (Throwable $e) {
            if (SugarConfig::getInstance()->get('uninstallOnError', true)) {
                LoggerManager::getLogger()->fatal(
                    'Package Install process was not finished successfully, uninstalling'
                );
                LoggerManager::getLogger()->fatal($e);

                $this->forceUninstall($history);
                throw new PackageManagerException('ERR_UW_PACKAGE_NOT_INSTALLED');
            } else {
                LoggerManager::getLogger()->fatal(
                    'Package Install process was not finished successfully, leaving installation files in place'
                );
                LoggerManager::getLogger()->fatal($e);
                throw new PackageManagerException('ERR_UW_PACKAGE_INSTALLED_WITH_ERROR');
            }
        }

        if ($previousInstalled) {
            $previousZipFile = $this->createPackageZipFile($previousInstalled->getFileName(), $this->getBaseTempDir());
            $previousZipFile->removeSelfWithMetadata();
            $previousInstalled->mark_deleted($previousInstalled->id);
        }
        $history->status = UpgradeHistory::STATUS_INSTALLED;
        $history->save();

        return $history;
    }

    /**
     * uninstall package
     * @param UpgradeHistory $history
     * @param bool $removeTables
     * @return UpgradeHistory
     *
     * @throws Exception\NoPackageFileException
     * @throws Exception\NotAcceptableTypeException
     * @throws Exception\OnlyPackagePatchTypeAcceptableException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws PackageManagerException
     */
    public function uninstallPackage(UpgradeHistory $history, bool $removeTables): UpgradeHistory
    {
        global $mi_remove_tables;
        $mi_remove_tables = $removeTables;

        if (!empty($history->deleted)) {
            throw new PackageManagerException('ERR_UW_NO_PACKAGE');
        }

        if ($history->status !== UpgradeHistory::STATUS_INSTALLED) {
            throw new PackageManagerException('ERR_UW_PACKAGE_NOT_INSTALLED');
        }
        $manifest = $history->getPackageManifest();

        if (!$manifest->isPackageUninstallable()) {
            throw new PackageManagerException('ERR_UW_PACKAGE_IS_UNINSTALLABLE');
        }

        $zipFile = $this->createPackageZipFile($history->getFileName(), $this->getBaseTempDir());
        $zipFile->extractPackage();

        $isPackageModuleOrPatch = in_array($manifest->getPackageType(), [
            PackageManifest::PACKAGE_TYPE_MODULE,
            PackageManifest::PACKAGE_TYPE_PATCH,
        ], true);

        if ($isPackageModuleOrPatch) {
            $zipFile->runPackageScript(PackageZipFile::PRE_UNINSTALL_FILE, $this->silent);
        }
        $moduleInstaller = $this->getModuleInstaller();
        $moduleInstaller->setPatch($history->getPackagePatch());
        $moduleInstaller->setUpgradeHistory($history);
        $moduleInstaller->uninstall($zipFile->getPackageDir());

        if ($manifest->getPackageType() === PackageManifest::PACKAGE_TYPE_PATCH) {
            $zipFile->runPackageScript(PackageZipFile::POST_UNINSTALL_FILE, $this->silent);
        }

        if ($history->status !== UpgradeHistory::STATUS_STAGED) {
            $history->status = UpgradeHistory::STATUS_STAGED;
            $history->save();
        }

        return $history;
    }

    /**
     * enable package
     *
     * @param UpgradeHistory $history
     * @param bool $overwriteFiles
     * @return UpgradeHistory
     * @throws Exception\NoPackageFileException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws PackageManagerException
     */
    public function enablePackage(UpgradeHistory $history, bool $overwriteFiles): UpgradeHistory
    {
        global $mi_overwrite_files;
        $mi_overwrite_files = $overwriteFiles;

        if (!empty($history->deleted)) {
            throw new PackageManagerException('ERR_UW_NO_PACKAGE');
        }

        if ($history->status !== UpgradeHistory::STATUS_INSTALLED) {
            throw new PackageManagerException('ERR_UW_PACKAGE_NOT_INSTALLED');
        }

        if ($history->isPackageEnabled()) {
            throw new PackageManagerException('ERR_UW_PACKAGE_ALREADY_ENABLED');
        }
        $callable = function (ModuleInstaller $moduleInstaller, string $packageDir): void {
            $moduleInstaller->enable($packageDir);
        };
        $this->processEnableDisablePackage($history, $callable);
        $history->enabled = 1;
        $history->save();
        return $history;
    }

    /**
     * disable package
     *
     * @param UpgradeHistory $history
     * @param bool $overwriteFiles
     * @return UpgradeHistory
     * @throws Exception\NoPackageFileException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws PackageManagerException
     */
    public function disablePackage(UpgradeHistory $history, bool $overwriteFiles): UpgradeHistory
    {
        global $mi_overwrite_files;
        $mi_overwrite_files = $overwriteFiles;

        if (!empty($history->deleted)) {
            throw new PackageManagerException('ERR_UW_NO_PACKAGE');
        }

        if ($history->status !== UpgradeHistory::STATUS_INSTALLED) {
            throw new PackageManagerException('ERR_UW_PACKAGE_NOT_INSTALLED');
        }

        if (!$history->isPackageEnabled()) {
            throw new PackageManagerException('ERR_UW_PACKAGE_ALREADY_DISABLED');
        }
        $callable = function (ModuleInstaller $moduleInstaller, string $packageDir): void {
            $moduleInstaller->disable($packageDir);
        };
        $this->processEnableDisablePackage($history, $callable);
        $history->enabled = 0;
        $history->save();
        return $history;
    }

    /**
     * Emergency uninstall just installed package in case if a fatal error caught
     */
    public function handleApplicationFatalError(ErrorException $err): void
    {
        $justInstalled = null;

        $justInstalled = (new UpgradeHistory())->getJustInstalled();
        if (!$justInstalled) {
            return;
        }
        $status = $justInstalled->getProcessStatus();
        $isDone = $status['is_done'] ?? false;
        if (!$isDone) {
            return;
        }
        if (SugarConfig::getInstance()->get('uninstallOnError', true)) {
            MlpLogger::replaceDefault();

            LoggerManager::getLogger()->fatal(sprintf(
                'Uninstalling a package %s because of the fatal error',
                $justInstalled->name
            ));
            LoggerManager::getLogger()->fatal($err);

            try {
                $this->forceUninstall($justInstalled, true);
            } catch (\Throwable $e) {
                LoggerManager::getLogger()->fatal($e);
            }
        } else {
            LoggerManager::getLogger()->fatal(
                'Package Install process was not finished successfully, leaving installation files in place'
            );
        }
    }

    protected function getUniquePackageFileName(UpgradeHistory $history): string
    {
        return sprintf(
            '%s_%s_%s.zip',
            $history->id_name,
            $history->version,
            time(),
        );
    }

    /**
     * enable or disable package
     * @param UpgradeHistory $history
     * @param callable $callable
     * @throws Exception\NoPackageFileException
     * @throws Exception\PackageManifestException
     * @throws Exception\UnableExtractFileException
     * @throws PackageManagerException
     */
    private function processEnableDisablePackage(UpgradeHistory $history, callable $callable): void
    {
        $manifest = $history->getPackageManifest();
        if ($manifest->getPackageType() !== PackageManifest::PACKAGE_TYPE_MODULE) {
            throw new PackageManagerException('ERR_UW_WRONG_PACKAGE_TYPE', [PackageManifest::PACKAGE_TYPE_MODULE]);
        }
        $zipFile = $this->createPackageZipFile($history->getFileName(), $this->getBaseTempDir());
        $zipFile->extractPackage();

        $moduleInstaller = $this->getModuleInstaller();
        $moduleInstaller->setPatch($history->getPackagePatch());
        $callable($moduleInstaller, $zipFile->getPackageDir());
    }

    /**
     * force uninstall a package in case of exception, fatal or deferred (after caches rebuilt) fatal error
     *
     * @param UpgradeHistory $history
     * @param bool $emergency Do not call any package scripts, just remove everything
     */
    private function forceUninstall(UpgradeHistory $history, bool $emergency = false): void
    {
        try {
            $moduleInstaller = $this->getModuleInstaller();
            $moduleInstaller->setUpgradeHistory($history);
            $moduleInstaller->setInstallationError(translate('LBL_ML_INSTALLATION_FATAL', 'Administration'));

            LoggerManager::getLogger()->fatal('Executing emergency package uninstall: ' . $history->name);

            $zipFile = $this->createPackageZipFile($history->getFileName(), $this->getBaseTempDir());
            $zipFile->extractPackage();

            $manifest = $history->getPackageManifest();
            $isPackageModuleOrPatch = in_array($manifest->getPackageType(), [
                PackageManifest::PACKAGE_TYPE_MODULE,
                PackageManifest::PACKAGE_TYPE_PATCH,
            ], true);

            $history->updateStatus(UpgradeHistory::STATUS_STAGED);

            if ($isPackageModuleOrPatch) {
                $zipFile->runPackageScript(PackageZipFile::PRE_UNINSTALL_FILE, $this->silent);
            }
            $moduleInstaller->setPatch($history->getPackagePatch());
            $moduleInstaller->setUpgradeHistory($history);
            $enableHookExecution = !$emergency;
            $moduleInstaller->forceUninstall($zipFile->getPackageDir(), $enableHookExecution);

            if ($manifest->getPackageType() === PackageManifest::PACKAGE_TYPE_PATCH) {
                $zipFile->runPackageScript(PackageZipFile::POST_UNINSTALL_FILE, $this->silent);
            }
        } catch (Throwable $e) {
            LoggerManager::getLogger()->fatal($e);
        }
    }
}
