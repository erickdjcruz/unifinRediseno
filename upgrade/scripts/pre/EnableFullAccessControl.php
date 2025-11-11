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
 * enable full access control, this must be run at the very first in pre scripts
 */
class SugarUpgradeEnableFullAccessControl extends UpgradeScript
{
    // make sure the order is the very first one
    public $order = 1;
    public $type = self::UPGRADE_ALL;

    public function run()
    {
        // disable access control, mark it as admin work
        if (class_exists(\Sugarcrm\Sugarcrm\AccessControl\AccessControlManager::class)) {
            Sugarcrm\Sugarcrm\AccessControl\AccessControlManager::instance()->setAdminWork(true);
        }
    }
}
