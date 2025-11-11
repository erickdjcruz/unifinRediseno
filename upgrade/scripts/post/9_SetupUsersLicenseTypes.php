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

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

/**
 * Upgrade script to schedule a full FTS index.
 */
class SugarUpgradeSetupUsersLicenseTypes extends UpgradeScript
{
    public $order = 9600;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }

        // check if this is single license type
        if (!SubscriptionManager::instance()->isSingleMangoTypeEntitlement()) {
            return;
        }

        try {
            $adminUser = BeanFactory::newBean('Users')->getSystemUser();
            $adminUser->updateUsersEmptyLicenseTypes();
        } catch (Exception $e) {
            $this->log('SetupUsersLicenseTypes: failed to update users\' license types!');
        }
    }
}
