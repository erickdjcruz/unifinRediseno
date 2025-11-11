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

/**
 * Split files into subfolders inside upload directory to improve filesystem lookups.
 * Script performs action for existing uploaded files
 */
class SugarUpgradeSplitUploadsIntoSubfolders extends UpgradeScript
{
    public $order = 9999;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '11.1.0', '>=')) {
            return;
        }
        $uploadDir = UploadStream::getDir();
        $dir = opendir($uploadDir);
        if (!$dir) {
            $this->log('Cannot open upload directory');
            return;
        }
        $total = 0;
        while (($file = readdir($dir)) !== false) {
            $pathFrom = $uploadDir . DIRECTORY_SEPARATOR . $file;
            if (!is_guid($file) || !is_file($pathFrom)) {
                continue;
            }
            $subdirName = $this->makeDirName($file);
            $nameTo = $subdirName . DIRECTORY_SEPARATOR . $file;
            sugar_mkdir($uploadDir . DIRECTORY_SEPARATOR . $subdirName);
            sugar_rename($pathFrom, $uploadDir . DIRECTORY_SEPARATOR . $nameTo);
            $total++;
            if ($total % 1000 === 0) {
                $this->log("Moved {$file} to {$nameTo}, processed {$total} files");
            }
        }
        $this->log("Done, processed {$total} files");
        closedir($dir);
    }

    private function makeDirName($filename)
    {
        return substr($filename, 5, 3);
    }
}
