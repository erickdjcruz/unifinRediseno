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

require_once 'include/utils/layout_utils.php';

use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;
use Sugarcrm\Sugarcrm\PackageManager\Exception\NoUploadFileException;
use Sugarcrm\Sugarcrm\PackageManager\File\PackageZipFile;
use Sugarcrm\Sugarcrm\PackageManager\PackageManager;
use UploadFile as BaseUploadFile;
use Sugarcrm\Sugarcrm\PackageManager\File\UploadFile;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\AccessControl\SugarFeatureVoter;

/**
 * Administration API PackageApiRest
 */
final class PackageApiRest extends FileApi
{
    /**
     * @var PackageManager
     */
    private $packageManager;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->packageManager = new PackageManager();
    }

    /**
     * Register endpoints
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'uploadPackage' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'packages'],
                'pathVars' => ['', ''],
                'method' => 'uploadPackage',
                'rawPostContents' => true,
                'shortHelp' => 'Uploads a package to an instance. Does not install or enable the package',
                'longHelp' => 'include/api/help/administration_packages_upload_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiException',
                ],
            ],
            'installPackage' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'packages', '?', 'install'],
                'pathVars' => ['', '', 'id', ''],
                'method' => 'installPackage',
                'shortHelp' => 'Install the given package',
                'longHelp' => 'include/api/help/administration_packages_install_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'unInstallPackage' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'packages', '?', 'uninstall'],
                'pathVars' => ['', '', 'id', ''],
                'method' => 'unInstallPackage',
                'shortHelp' => 'Uninstall the given package',
                'longHelp' => 'include/api/help/administration_packages_uninstall_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'enablePackage' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'packages', '?', 'enable'],
                'pathVars' => ['', '', 'id', ''],
                'method' => 'enablePackage',
                'shortHelp' => 'Enable the given package',
                'longHelp' => 'include/api/help/administration_packages_enable_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'disablePackage' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'packages', '?', 'disable'],
                'pathVars' => ['', '', 'id', ''],
                'method' => 'disablePackage',
                'shortHelp' => 'Disable the given package',
                'longHelp' => 'include/api/help/administration_packages_disable_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotFound',
                    'SugarApiException',
                ],
            ],
            'deletePackage' => [
                'reqType' => ['DELETE'],
                'path' => ['Administration', 'packages', '?'],
                'pathVars' => ['', '', 'id'],
                'method' => 'deletePackage',
                'shortHelp' => 'Delete the given package by file hash',
                'longHelp' => 'include/api/help/administration_packages_delete_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiException',
                ],
            ],
            'listPackages' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'packages'],
                'pathVars' => [''],
                'method' => 'getPackages',
                'keepSession' => true,
                'shortHelp' => 'List uploaded but not installed packages ready to be installed',
                'longHelp' => 'include/api/help/administration_packages_list_all_packages_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'listStagedPackages' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'packages', 'staged'],
                'pathVars' => [''],
                'method' => 'getStagedPackages',
                'keepSession' => true,
                'shortHelp' => 'List uploaded but not installed packages ready to be installed',
                'longHelp' => 'include/api/help/administration_packages_list_staged_packages_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'listInstalledPackages' => [
                'reqType' => 'GET',
                'path' => ['Administration', 'packages', 'installed'],
                'pathVars' => [''],
                'method' => 'getInstalledPackages',
                'keepSession' => true,
                'shortHelp' => 'List of installed packages',
                'longHelp' => 'include/api/help/administration_packages_list_installed_packages_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'status' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'packages', '?', 'installation-status'],
                'pathVars' => ['', '', 'id', ''],
                'method' => 'getStatus',
                'shortHelp' => 'Get a package installation status',
                'minVersion' => '11.14',
                'longHelp' => 'include/api/help/package_installation_status_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
                'ignoreMetaHash' => true,
                'ignoreSystemStatusError' => true,
                'noLoginRequired' => true,
            ],
        ];
    }

    /**
     * Upload package zip archive from $_FILES['upgrade_zip'].
     * Check this zip. Return status.
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiException
     */
    public function uploadPackage(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $this->ensureModuleLoaderIsAllowed();

        $_REQUEST['view'] = 'module';
        try {
            if (empty($_FILES['upgrade_zip']) || empty($_FILES['upgrade_zip']['tmp_name'])) {
                throw new NoUploadFileException();
            }
            $uploadFile = new UploadFile(new BaseUploadFile('upgrade_zip'));
            $uploadFile->moveToUpload();
            $zipFile = new PackageZipFile($uploadFile->getPath(), $this->packageManager->getBaseTempDir());
            $upgradeHistory = $this->packageManager->uploadPackageFromFile(
                $zipFile,
                PackageManifest::PACKAGE_TYPE_MODULE
            );
            return $upgradeHistory->getData();
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'upload_package_error');
        }
    }

    /**
     * Delete package files. Return status.
     *
     * @param RestService $api
     * @param array $args
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiException
     */
    public function deletePackage(RestService $api, array $args): void
    {
        $this->ensureAdminUser();
        $this->requireArgs($args, ['id']);
        try {
            $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($args['id']);
            $this->packageManager->deletePackage($upgradeHistory);
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'delete_package_error');
        }
    }

    /**
     * Uninstall package by id.
     * @param RestService $api
     * @param array $args
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiException
     */
    public function unInstallPackage(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $this->requireArgs($args, ['id']);

        $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($args['id']);
        try {
            $upgradeHistory = $this->packageManager->uninstallPackage(
                $upgradeHistory,
                $upgradeHistory->getPackageManifest()->shouldTablesBeRemoved()
            );
            return $upgradeHistory->getData();
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'uninstall_package_error');
        } finally {
            MetaDataManager::clearAPICache();
        }
    }

    /**
     * Enable package by ID.
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiException
     */
    public function enablePackage(RestService $api, array $args): array
    {
        $this->ensureAdminUser();

        $this->requireArgs($args, ['id']);

        $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($args['id']);
        try {
            $upgradeHistory = $this->packageManager->enablePackage($upgradeHistory, true);
            return $upgradeHistory->getData();
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'enable_package_error');
        } finally {
            MetaDataManager::clearAPICache();
        }
    }

    /**
     * Disable package by ID.
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiException
     */
    public function disablePackage(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $this->requireArgs($args, ['id']);

        $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($args['id']);
        try {
            $upgradeHistory = $this->packageManager->disablePackage($upgradeHistory, true);
            return $upgradeHistory->getData();
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'disable_package_error');
        } finally {
            MetaDataManager::clearAPICache();
        }
    }

    /**
     * Install package and return newly installed package id
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiException
     * @throws Exception
     */
    public function installPackage(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $this->requireArgs($args, ['id']);

        $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($args['id']);
        try {
            $upgradeHistory = $this->packageManager->installPackage($upgradeHistory);
            return $upgradeHistory->getData();
        } catch (SugarException $e) {
            throw $this->getSugarApiException($e, 'install_package_error');
        } catch (Exception $e) {
            throw new SugarApiException();
        }
    }

    /**
     * Returns a list of packages in the 'staged' status
     *
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarQueryException
     */
    public function getStagedPackages(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $packages = $this->packageManager->getStagedModulePackages();
        $packages = array_map(function (UpgradeHistory $history) {
            return $history->getData();
        }, $packages);
        return ['packages' => $packages];
    }

    /**
     * Returns a list of packages in the 'installed' status
     *
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarQueryException
     */
    public function getInstalledPackages(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $packages = $this->packageManager->getInstalledModulePackages();
        $packages = array_map(function (UpgradeHistory $history) {
            return $history->getData();
        }, $packages);
        return ['packages' => $packages];
    }

    /**
     * Returns a list of all packages
     *
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarQueryException
     */
    public function getPackages(RestService $api, array $args): array
    {
        $this->ensureAdminUser();
        $packages = $this->packageManager->getModulePackages();
        $packages = array_map(function (UpgradeHistory $history) {
            $data = $history->getData();
            $data['installed'] = $history->status === UpgradeHistory::STATUS_INSTALLED;
            return $data;
        }, $packages);
        return ['packages' => $packages];
    }

    /**
     * Returns a package installation status
     *
     * @param RestService $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function getStatus(RestService $api, array $args): array
    {
        $this->requireArgs($args, ['id']);
        $id = $args['id'];

        $upgradeHistory = $this->getUpgradeHistoryByIdOrFail($id);

        $mi = new ModuleInstaller();
        $mi->setUpgradeHistory($upgradeHistory);
        $progress = $mi->getInstallationProgress();
        $progress['is_staged'] = $upgradeHistory->status === UpgradeHistory::STATUS_STAGED;

        return $progress;
    }

    /**
     * return UpgradeHistory by ID or throw no found exception
     * @param string $id
     * @return UpgradeHistory
     * @throws SugarApiExceptionNotFound
     */
    private function getUpgradeHistoryByIdOrFail(string $id): UpgradeHistory
    {
        /** @var UpgradeHistory $upgradeHistory */
        $upgradeHistory = BeanFactory::retrieveBean('UpgradeHistory', $id);

        if (is_null($upgradeHistory) || empty($upgradeHistory->id)) {
            throw new SugarApiExceptionNotFound('ERR_UW_NO_PACKAGE', null, 'Administration');
        }

        return $upgradeHistory;
    }

    /**
     * Ensure current user has admin permissions
     * @throws SugarApiExceptionNotAuthorized
     */
    private function ensureAdminUser()
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isAdmin()) {
            throw new SugarApiExceptionNotAuthorized(translate('EXCEPTION_NOT_AUTHORIZED'));
        }
    }

    /**
     * ensure module loader feature is allowed
     * @throws SugarApiExceptionNotAuthorized
     */
    private function ensureModuleLoaderIsAllowed()
    {
        if (!AccessControlManager::instance()->allowFeatureAccess(SugarFeatureVoter::MODULE_LOADER_UPLOAD_FEATURE_NAME)) {
            throw new SugarApiExceptionNotAuthorized(translate('EXCEPTION_MODULELOADER_UPLOAD'));
        }
    }

    /**
     * convert SugarException into SugarApiException
     * @param SugarException $sugarException
     * @param string $errorLabel
     * @return SugarApiException
     */
    private function getSugarApiException(SugarException $sugarException, string $errorLabel): SugarApiException
    {
        $apiException = new SugarApiException($sugarException->getMessage(), null, 'Administration', 0, $errorLabel);
        $apiException->extraData = $sugarException->extraData;
        return $apiException;
    }
}
