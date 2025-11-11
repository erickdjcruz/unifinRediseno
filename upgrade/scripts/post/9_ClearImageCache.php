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
 * Drop cache of image files
 */
class SugarUpgradeClearImageCache extends UpgradeScript
{
    public $order = 9999;
    public $type = self::UPGRADE_ALL;

    public function run()
    {
        if (version_compare($this->from_version, '11.1.0', '>=')) {
            return;
        }

        $cacheDir = sugar_cached('images/');
        if (!is_dir($cacheDir)) {
            $this->upgrader->log('Cache directory does not exist, nothing to do');
            return;
        }
        $directory = dir($cacheDir);
        while (($entry = $directory->read()) !== false) {
            if (in_array($entry, ['.', '..', 'index.html'])) {
                continue;
            }
            $this->upgrader->removeDir("$cacheDir/$entry");
        }
        $directory->close();
    }
}
