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

/**
 * Update all packages
 */
class SugarUpgradeUpdateUpgradesFolder extends UpgradeScript
{
    public $order = 9999;

    /**
     * @var PackageManager
     */
    private $packageManager;

    /**
     * @var string
     */
    private $logPrefix;

    public function __construct($upgrader)
    {
        parent::__construct($upgrader);
        $this->packageManager = new PackageManager();
        $this->logPrefix = 'PackageManagerUpgradeScript: ';
    }


    public function run()
    {
        if (version_compare($this->from_version, '11.0.0', '>=')) {
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
            $typeDir = UploadStream::getDir() . DIRECTORY_SEPARATOR . $this->getUpgradeTypeDir($packageType);
            foreach (glob($typeDir . DIRECTORY_SEPARATOR . '*.zip') as $zipFile) {
                if (is_dir($zipFile)) {
                    continue;
                }

                $sq = new \SugarQuery();
                $sq->from($history);
                $sq->select(['id', 'filename']);
                $sq->where()->like('filename', '%' . basename($zipFile));
                $records = $sq->execute();

                $destinationFile = $this->getUpgradeTypeDir($packageType) . DIRECTORY_SEPARATOR
                    . basename($zipFile);

                foreach ($records as $record) {
                    if (file_exists($record['filename'])) {
                        $qb = $this->db->getConnection()->createQueryBuilder();
                        $qb->update($history->getTableName())
                            ->set('filename', $qb->createPositionalParameter($destinationFile))
                            ->where($qb->expr()->eq('id', $qb->createPositionalParameter($record['id'])));
                        $qb->execute();
                    }
                }
                sugar_mkdir(dirname($destinationFile), 0755, true);
                rename($zipFile, $destinationFile);
                $this->upgrader->log(
                    $this->logPrefix . 'Moved ' . $zipFile . ' to: ' . $destinationFile
                );
            }

            foreach (glob($typeDir . DIRECTORY_SEPARATOR . '*manifest.php') as $manifestFile) {
                if (is_dir($manifestFile)) {
                    continue;
                }
                $destinationFile = $this->getUpgradeTypeDir($packageType) . DIRECTORY_SEPARATOR
                    . basename($manifestFile);
                sugar_mkdir(dirname($destinationFile), 0755, true);
                rename($manifestFile, $destinationFile);
                $this->upgrader->log(
                    $this->logPrefix . 'Moved ' . $manifestFile . ' to: ' . $destinationFile
                );
            }
        }
    }

    /**
     * return package type dir
     * @param string $type
     * @return string
     */
    private function getUpgradeTypeDir(string $type): string
    {
        return $this->packageManager->getBaseUpgradeDir() . DIRECTORY_SEPARATOR . $type;
    }
}
