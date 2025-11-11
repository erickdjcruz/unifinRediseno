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

namespace Sugarcrm\Sugarcrm\PackageManager\Factory;

use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageExistsException;
use UpgradeHistory;
use SugarQueryException;

class UpgradeHistoryFactory
{
    /**
     * create upgrade history object
     * @param PackageManifest $manifest
     * @param string $file
     * @param string $status
     * @return UpgradeHistory
     * @throws SugarQueryException
     * @throws PackageExistsException
     */
    public function createUpgradeHistory(PackageManifest $manifest, string $file, string $status): UpgradeHistory
    {
        $history = new UpgradeHistory();

        $md5Sum = md5_file($file);
        $md5match = $history->retrieveByMd5($md5Sum, ['add_deleted' => false]);
        if ($md5match instanceof UpgradeHistory) {
            if ($md5match->deleted) {
                $history = $md5match;
            } else {
                throw new PackageExistsException();
            }
        }

        $history->filename = $file;
        $history->md5sum = $md5Sum;
        $history->status = $status;

        $this->populateHistoryFromManifest($history, $manifest);

        return $history;
    }

    /**
     * populate upgrade history from manifest
     * @param UpgradeHistory $history
     * @param PackageManifest $manifest
     */
    public function populateHistoryFromManifest(UpgradeHistory $history, PackageManifest $manifest)
    {
        $history->type = $manifest->getPackageType();
        $history->version = $manifest->getPackageVersion();
        $history->name = $manifest->getPackageName();
        $history->description = $manifest->getManifestValue('description', '');
        $history->id_name = $manifest->getPackageIdName();
        $history->manifest = base64_encode(serialize($manifest->toArray()));
        $history->published_date = $manifest->getManifestValue('published_date', '');
        $history->uninstallable = $manifest->isPackageUninstallable();
    }
}
