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

use Sugarcrm\Sugarcrm\PackageManager\PackageManager;

/**
 * Remove WebUpgrader useless files.
 */
class SugarUpgradeRemoveWebUpgrader extends UpgradeScript
{
    public $order = 8500;

    public $type = self::UPGRADE_CUSTOM;

    protected $filesToRemove = [
        'custom/Extension/application/Ext/Language/en_us.HealthCheck.php',
        'custom/modules/UpgradeWizard/language',
        'custom/Extension/modules/Administration/Ext/Administration/upgrader2.php',
        'custom/Extension/modules/Administration/Ext/Administration/healthcheck.php',
    ];

    public function run()
    {
        $packageManager = new PackageManager();
        /** @var UpgradeHistory[] $packages */
        $packages = (new UpgradeHistory())->getPackages();
        foreach ($packages as $package) {
            if (strpos($package->name, 'SugarCRM Upgrader') === false) {
                continue;
            }
            if ($package->status === UpgradeHistory::STATUS_INSTALLED) {
                $package->status = UpgradeHistory::STATUS_STAGED;
                $package->save();
                $this->filesToRemove[] = 'custom/Extension/application/Ext/Include/' . $package->id_name . '.php';
            }

            try {
                $packageManager->deletePackage($package);
            } catch (Exception $e) {
                $this->log('Fail to remove SugarCRM Upgrader version ' . $package->version);
            }
            $this->upgrader->fileToDelete($this->filesToRemove, $this);
        }
    }
}
