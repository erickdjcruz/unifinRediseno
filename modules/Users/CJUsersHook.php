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

namespace Sugarcrm\Sugarcrm\modules\Users;

use Sugarcrm\Sugarcrm\Entitlements\Subscription;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

class CJUsersHook
{
    /**
     * Set the grace period start date if user limit exceeds
     *
     * @param $user
     * @param $event
     * @param $arguments
     * @throws \Exception
     */
    public function afterSave($user, $event, $arguments)
    {
        $configurator = new \Configurator();

        $changed = false;
        $limit = $this->getAutomateLimit();
        $currentUsers = $this->getCurrentAutomateUsers($user);
        $startDate = $configurator->config['customer_journey']['grace_period_start_date'];

        if ($currentUsers > $limit) {
            // Set grace_period_start_date only if it was not set previously
            if ($startDate === '0000-00-00') {
                $timeDate = \TimeDate::getInstance();
                // Set current DateTime as grace_period_start_date
                $configurator->config['customer_journey']['grace_period_start_date'] = $timeDate->getNow()->asDbDate();
                $changed = true;
            }
        } else {
            // Reset grace_period_start_date only if it was set previously
            if ($startDate !== '0000-00-00') {
                $configurator->config['customer_journey']['grace_period_start_date'] = '0000-00-00';
                $changed = true;
            }
        }

        if ($changed) {
            $configurator->saveConfig();
        }
    }

    /**
     * Get User Limit for current Automate License
     *
     * @return int
     */
    public function getAutomateLimit()
    {
        $subscriptionManager = SubscriptionManager::instance();
        $subscriptions = $subscriptionManager->getSystemSubscriptions();

        return isset($subscriptions[Subscription::SUGAR_AUTOMATE_KEY]) ?
            $subscriptions[Subscription::SUGAR_AUTOMATE_KEY]['quantity'] : 0;
    }

    /**
     * Get Number of users who have Automate License enabled
     *
     * @param \SugarBean $user
     * @return int
     */
    public function getCurrentAutomateUsers($user)
    {
        $sugarQuery = new \SugarQuery();

        $sugarQuery->select->selectReset()->setCountQuery();
        $sugarQuery->from($user, ['team_security' => false]);

        $sugarQuery->where()->equals('status', 'Active');
        $sugarQuery->where()->like('license_type', '%' . Subscription::SUGAR_AUTOMATE_KEY . '%');

        return $sugarQuery->getOne();
    }
}
