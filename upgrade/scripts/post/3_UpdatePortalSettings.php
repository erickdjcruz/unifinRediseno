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
 * Update portal settings
 */
class SugarUpgradeUpdatePortalSettings extends UpgradeScript
{
    public $order = 3100;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @return bool
     */
    protected function shouldAddConfig(): bool
    {
        // upgrade, not convert
        if ($this->to_flavor == $this->from_flavor) {
            // upgrading from 9.2.0 or newer
            if (version_compare($this->from_version, '9.2.0', '>=')) {
                return false;
            }
            // pro upgrade
            if ($this->toFlavor('pro')) {
                return false;
            }
        } else { // convert
            // not converting from pro to ent/ult
            if (!($this->fromFlavor('pro') && ($this->toFlavor('ent') || $this->toFlavor('ult')))) {
                return false;
            }
        }
        return true;
    }

    /**
     * This script updates portal settings to enable search
     * @see UpgradeScript::run()
     */
    public function run()
    {
        if (!$this->shouldAddConfig()) {
            return;
        }

        $configurator = new Configurator();
        $configurator->config['portal'] = [
            'modules' => [
                'KBContents',
            ],
        ];
        $configurator->handleOverride();
        $configurator->clearCache();
        SugarConfig::getInstance()->clearCache();
    }
}
