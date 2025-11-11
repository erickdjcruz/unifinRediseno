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

// This section of code is a portion of the code referred
// to as Critical Control Software under the End User
// License Agreement.  Neither the Company nor the Users
// may modify any portion of the Critical Control Software.
use Sugarcrm\Sugarcrm\Entitlements\Subscription;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

/**
 * Class SugarFeatureVoter
 * using Symfony's Voter to make decision to access control modules' fields
 * @package Sugarcrm\Sugarcrm\AccessControl
 */
class SugarFeatureVoter extends SugarVoter
{
    public const MODULE_LOADER_UPLOAD_FEATURE_NAME = 'MODULE_LOADER_UPLOAD';
    /**
     * supported keys in access_config.php
     * @var array
     */
    protected $supportedKeys = [
        AccessControlManager::FEATURES_KEY,
    ];

    /**
     * mapping from "feature name" to license types, which is not offered
     * @var array[]
     */
    private $deniedFeatureList = [
        self::MODULE_LOADER_UPLOAD_FEATURE_NAME => [Subscription::SUGAR_SELL_ESSENTIALS_KEY],
    ];

    /**
     * {@inheritdoc}
     */
    public function vote(string $key, string $subject, ?string $value = null): bool
    {
        if (!$this->supports($key)) {
            return true;
        }

        $deniedList = $this->getDeniedList($subject);
        $sysCrmKeys = $this->getSystemCrmkeys();

        foreach ($sysCrmKeys as $crmKey => $value) {
            if (!in_array($crmKey, $deniedList)) {
                // there is a crmKey not in not Allowed list
                return true;
            }
        }

        return false;
    }

    /**
     * get denied list
     * @param string $key
     * @return array
     */
    protected function getDeniedList(string $key): array
    {
        return $this->deniedFeatureList[$key] ?? [];
    }

    /**
     * get System CrM key list
     * @return array
     */
    protected function getSystemCrmkeys(): array
    {
        return SubscriptionManager::instance()->getSystemCRMKeys();
    }
}
//END REQUIRED CODE DO NOT MODIFY
