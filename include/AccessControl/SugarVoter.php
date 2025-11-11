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

namespace Sugarcrm\Sugarcrm\AccessControl;

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

// This section of code is a portion of the code referred
// to as Critical Control Software under the End User
// License Agreement.  Neither the Company nor the Users
// may modify any portion of the Critical Control Software.

/**
 * Class SugarVoter, this class does access control to module and dashlet
 *
 * @package Sugarcrm\Sugarcrm\AccessControl
 */
class SugarVoter implements SugarVoterInterface
{
    protected $subscriptions = [];

    /**
     * supported keys in access_config.php
     * @var array
     */
    protected $supportedKeys = [
        AccessControlManager::MODULES_KEY,
        AccessControlManager::DASHLETS_KEY,
        AccessControlManager::WIDGETS_KEY,
    ];

    /**
     * get valid subscriptions for current user,
     * a valid subscription is:
     * 1. current product has subbscription
     * 2. current user has the subscription type
     *
     * @return array
     * @throws \Exception
     */
    protected function getCurrentUserSubscriptions()
    {
        if (!empty($this->subscriptions)) {
            return $this->subscriptions;
        }

        global $current_user;
        if (empty($current_user)) {
            throw new \Exception('User is not logged in');
        }

        // check subscriptions
        $sm = SubscriptionManager::instance();
        $this->subscriptions = $sm->getAllImpliedSubscriptions($sm->getAllUserSubscriptions($current_user));
        return $this->subscriptions;
    }

    /**
     * get protected list, let children classes to override
     *
     * @param string $key
     * @return array
     */
    protected function getProtectedList(string $key)
    {
        return AccessConfigurator::instance()->getAccessControlledList($key);
    }

    /**
     * @return array|mixed
     */
    protected function getNotAccessibleModuleListByLicenseTypes()
    {
        $subscriptions = $this->getCurrentUserSubscriptions();
        return AccessConfigurator::instance()->getNotAccessibleModuleListByLicenseTypes($subscriptions);
    }

    /**
     * $key
     * @param string $key section key in access_config file
     * @return bool
     */
    protected function supports(string $key): bool
    {
        return in_array($key, $this->getSupportedKeys());
    }

    /**
     * get section keys this vote is responsible for
     * @return array
     */
    protected function getSupportedKeys(): array
    {
        return $this->supportedKeys;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(string $key, string $subject, ?string $value = null): bool
    {
        if (!$this->supports($key)) {
            return true;
        }

        $entitled = $this->getCurrentUserSubscriptions();
        // no valid subscription
        if (empty($entitled)) {
            return false;
        }

        if ($key === AccessControlManager::MODULES_KEY) {
            $notAccessibleList = $this->getNotAccessibleModuleListByLicenseTypes();
            if (empty($notAccessibleList) || !isset($notAccessibleList[$subject])) {
                return true;
            }

            return false;
        }

        if ($key === AccessControlManager::DASHLETS_KEY || $key === AccessControlManager::WIDGETS_KEY) {
            $controlledList = $this->getProtectedList($key);
            if (!isset($controlledList[$subject]) || array_intersect($entitled, $controlledList[$subject])) {
                return true;
            }
        }

        return false;
    }
}
//END REQUIRED CODE DO NOT MODIFY
