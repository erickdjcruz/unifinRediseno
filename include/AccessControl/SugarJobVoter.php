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
 * Class SugarJobVoter
 * using Symfony's Voter to make decision to access control modules' fields
 * @package Sugarcrm\Sugarcrm\AccessControl
 */
class SugarJobVoter extends SugarVoter
{
    /**
     * supported keys in access_config.php
     * @var array
     */
    protected $supportedKeys = [
        AccessControlManager::JOBS_KEY,
    ];

    /**
     * {@inheritdoc}
     */
    public function vote(string $key, string $subject, ?string $value = null): bool
    {
        if (!$this->supports($key)) {
            return true;
        }
        $entitled = $this->getAllSystemSubscriptionKeys();
        $controlledList = $this->getProtectedList($key);
        if (!isset($controlledList[$subject]) || array_intersect($entitled, $controlledList[$subject])) {
            return true;
        }

        return false;
    }

    /**
     * get system subscription keys
     * @return array
     */
    protected function getAllSystemSubscriptionKeys(): array
    {
        $sm = SubscriptionManager::instance();
        return $sm->getAllImpliedSubscriptions(array_keys($sm->getAllSystemSubscriptionKeys()));
    }
}
//END REQUIRED CODE DO NOT MODIFY
