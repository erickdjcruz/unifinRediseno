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

class SugarUpgradePreGoogleClient extends UpgradeScript
{
    public $order = 9999;
    public $type = self::UPGRADE_CORE;

    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '<')) {
            // SugarUpgrade copies the new libraries before deleting the older version
            // google/apiclient-services lib does some magic in the autoloader
            // and it's causing warnings on post upgrade step.
            // Check sugarcrm/vendor/google/apiclient-services/autoload.php
            if (file_exists('vendor/google/apiclient/src/Google/Client.php')) {
                unlink('vendor/google/apiclient/src/Google/Client.php');
            }
        }
    }
}
