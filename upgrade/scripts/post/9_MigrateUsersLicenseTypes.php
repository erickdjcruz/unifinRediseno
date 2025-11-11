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
use Sugarcrm\Sugarcrm\Entitlements\Subscription;

/**
 * Upgrade script to migrate user's SELL license type to Sell bundle
 */
class SugarUpgradeMigrateUsersLicenseTypes extends UpgradeScript
{
    public $order = 9601;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '12.0.0', '>=')) {
            return;
        }

        // check if there is sell bundles
        $hasSellBundles = false;
        $subscriptions = SubscriptionManager::instance()->getTopLevelSystemSubscriptionKeys();
        foreach (Subscription::SELL_KEYS as $key) {
            if ($key != Subscription::SUGAR_SELL_KEY && in_array($key, $subscriptions)) {
                $hasSellBundles = true;
                break;
            }
        }

        if ($hasSellBundles) {
            try {
                $adminUser = BeanFactory::newBean('Users')->getSystemUser();
                $adminUser->migrateUsersLicenseTypes(false);
            } catch (Exception $e) {
                $this->log('MigrateUsersLicenseTypes: failed to update users\' license types!');
            }
        }
    }
}
