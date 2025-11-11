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

/**
 * Register upgrade with the system
 */
class SugarUpgradeRegisterUpgrade extends UpgradeScript
{
    public $order = 9900;

    public function __construct($upgrader)
    {
        $this->type = self::UPGRADE_CUSTOM | self::UPGRADE_DB;
        parent::__construct($upgrader);
    }

    public function run()
    {
        if (!empty($this->context['zip_as_dir'])) {
            $md5sum = trim(file_get_contents($this->context['zip'] . DIRECTORY_SEPARATOR . 'md5sum'));
        } elseif (file_exists($this->context['zip'])) {
            $md5sum = md5_file($this->context['zip']);
        } else {
            // if file is not there, just md5 the filename
            $md5sum = md5($this->context['zip']);
        }

        // if error was encountered, script should have died before now
        $history = new UpgradeHistory();
        try {
            $md5Match = $history->retrieveByMd5($md5sum);
        } catch (SugarQueryException $e) {
            $this->error(sprintf('Try register upgrade package, md5: %s. SQL error: %s', $md5sum, $e->getMessage()));
            return;
        }

        if (!empty($md5Match->id)) {
            // Not failing it - by now there's no point, we're at the end anyway
            $this->error('Duplicate install for upgrade package, md5:' . $md5sum);
            return;
        }

        $manifestData = array_merge($this->manifest, ['name' => pathinfo($this->context['zip'], PATHINFO_FILENAME)]);
        if (empty($manifestData['acceptable_sugar_versions'])) {
            $manifestData['acceptable_sugar_versions'] = ['exact_matches' => $this->from_version];
        }

        try {
            $manifest = new PackageManifest($manifestData, [], []);
        } catch (SugarException $e) {
            $this->error(sprintf(
                'Try register upgrade package, md5: %s. Manifest error: %s',
                $md5sum,
                $e->getMessage()
            ));
            return;
        }

        $history->filename = $this->context['zip'];
        $history->md5sum = $md5sum;
        $history->type = $manifest->getPackageType();
        $history->version = $this->to_version;
        $history->status = UpgradeHistory::STATUS_INSTALLED;
        $history->name = $manifest->getPackageName();
        $history->description = $manifest->getManifestValue('description', '');
        $history->id_name = $manifest->getPackageIdName();
        $history->manifest = base64_encode(serialize($manifest->toArray()));
        $history->published_date = $manifest->getManifestValue('published_date', '');
        $history->uninstallable = false;

        $history->save();
    }
}
