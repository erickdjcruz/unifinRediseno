<?php

declare(strict_types=1);
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


namespace Sugarcrm\Sugarcrm\Portal;

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use Administration;

class Settings
{
    protected $settings = [];

    /**
     * Get value of a setting from the portal configuration
     *
     * @param string $param
     * @return string
     */
    public function getParamValue($param)
    {
        if (!$this->settings) {
            $this->settings = Administration::getSettings()
                ->getConfigForModule('portal', 'support', true);
        }

        return $this->settings[$param] ?? null;
    }

    /**
     * Get Case visibility value
     *
     * @return string
     */
    public function getCaseVisibility()
    {
        return $this->getModuleVisibility('case', 'all');
    }

    public function getModuleVisibility(string $module, string $default = 'related_contacts')
    {
        return $this->getParamValue($module . 'Visibility') ?? $default;
    }

    /**
     * @return array
     */
    protected function getSubscriptions(): array
    {
        return SubscriptionManager::instance()->getSystemSubscriptions();
    }

    /**
     * @return bool
     */
    public function isServe(): bool
    {
        $sub = $this->getSubscriptions();
        return (!empty($sub['SUGAR_SERVE']) ? true : false);
    }

    /**
     * @return bool
     */
    public function allowCasesForContactsWithoutAccount(): bool
    {
        return false;
    }
}
