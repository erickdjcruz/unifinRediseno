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

class SugarUpgradeSetPortalEnableSelfSignUp extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.1.0', '<')) {
            if ($this->isUpgradeRequired()) {
                $this->enableSelfSignUp();
                $this->log('Portal setting enableSelfSignUp is now enabled');
            } else {
                $this->log('Portal setting enableSelfSignUp remains disabled (default)');
            }
        }
    }

    /**
     * Check if we need to enable self sign up. By default this is off, but will be enabled if:
     * 1. Portal is already enabled
     * 2. At least one Contact has a portal username and password set
     */
    private function isUpgradeRequired()
    {
        $portalSettings = Administration::getSettings('portal');
        if (isFalsy($portalSettings->settings['portal_on'])) {
            return false;
        }

        $query = new SugarQuery();
        $query->select()->setCountQuery();
        $query->from(BeanFactory::newBean('Contacts'));
        $query->where()->queryAnd()
            ->isNotEmpty('portal_name')
            ->isNotEmpty('portal_password');

        $result = $query->execute();
        $count = $result[0]['record_count'];

        return $count > 0;
    }

    /**
     * Enable portal self sign up
     */
    private function enableSelfSignUp()
    {
        $admin = new Administration();
        $admin->saveSetting('portal', 'enableSelfSignUp', 'enabled', 'support');
    }
}
