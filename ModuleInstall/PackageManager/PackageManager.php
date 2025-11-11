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

use Sugarcrm\Sugarcrm\PackageManager\PackageManager as UpgradedPackageManager;
use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;

require_once 'include/utils.php';

class PackageManager
{
    /**
     * @var UpgradedPackageManager
     */
    private $upgradedPackageManager;

    /**
     * Constructor: In this method we will initialize the soap client to point to the hearbeat server
     */
    public function __construct()
    {
        $this->upgradedPackageManager = new UpgradedPackageManager();
    }

    public static function fromNameValueList($nvl)
    {
        $array = [];
        foreach ($nvl as $list) {
            $array[$list['name']] = $list['value'];
        }

        return $array;
    }

    /**
     * return array of staging packages
     * @return array
     * @throws SugarQueryException
     */
    public function getPackagesInStaging(): array
    {
        $packages = $this->upgradedPackageManager->getStagedModulePackages();

        return array_map(
            function (UpgradeHistory $history) {
                $data = $history->getData();

                // Convert to old format because field order is critical for old YAHOO UI.
                return [
                    'name' => $data['name'],
                    'version' => $data['version'],
                    'published_date' => $data['published_data'],
                    'description' => $data['description'],
                    'uninstallable' => $history->isPackageUninstallable() ? 'Yes' : 'No',
                    'type' => htmlspecialchars(translate('LBL_UW_TYPE_' . strtoupper($data['type']), 'Administration'), ENT_COMPAT),
                    'file' => $data['id'],
                    'file_install' => $data['id'],
                    'unFile' => $data['id'],
                ];
            },
            array_values($packages)
        );
    }

    /**
     * return array of installed packages
     * @return array
     * @throws SugarQueryException
     */
    public function getinstalledPackages()
    {
        $packages = $this->upgradedPackageManager->getInstalledModulePackages();

        return array_map(
            function (UpgradeHistory $history) {
                $data = $history->getData();

                // Convert to old format because field order is critical for old YAHOO UI.
                return [
                    'name' => $data['name'],
                    'version' => $data['version'],
                    'type' => htmlspecialchars(translate('LBL_UW_TYPE_' . strtoupper($data['type']), 'Administration'), ENT_COMPAT),
                    'published_date' => $data['date_modified'],
                    'description' => $data['description'],
                    'uninstallable' => $history->isPackageUninstallable() ? 'Yes' : 'No',
                    'file_install' => $data['id'],
                    'file' => $data['id'],
                    'enabled' => $history->isPackageEnabled() ? 'ENABLED' : 'DISABLED',
                    'id' => $history->id,
                ];
            },
            array_values($packages)
        );
    }
}
