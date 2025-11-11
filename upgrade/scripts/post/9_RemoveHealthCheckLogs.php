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
 * Schedule .git directories for removal
 */
class SugarUpgradeRemoveHealthCheckLogs extends UpgradeScript
{
    /*
     * execution order
     */
    public $order = 9999;

    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }
        $this->removeLogs();
    }

    protected function removeLogs()
    {
        $files = glob('healthcheck_*.log');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24) { // 1 day
                    unlink($file);
                }
            }
        }
    }
}
