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

class SugarUpgradeReplaceMobileRegex extends UpgradeScript
{
    public $order = 2000;
    public $type = self::UPGRADE_CUSTOM;
    public const MOBILE_PLATFORM_FILENAME = 'mobile/error-not-supported-platform.html';

    public function run()
    {
        if (file_exists(self::MOBILE_PLATFORM_FILENAME)) {
            $this->log('Updating ' . self::MOBILE_PLATFORM_FILENAME);
            $contents = sugar_file_get_contents(self::MOBILE_PLATFORM_FILENAME);
            $originalRegex = '/https?:\/\/|itms:\/\/|itms-apps:\/\//';
            $safeRegex = '/^(https?:\/\/|itms:\/\/|itms-apps:\/\/)/';
            $contents = str_replace($originalRegex, $safeRegex, $contents);
            sugar_file_put_contents(self::MOBILE_PLATFORM_FILENAME, $contents);
        } else {
            $this->log(self::MOBILE_PLATFORM_FILENAME . ' does not exist. Skipping this script');
        }
    }
}
