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

class SugarUpgradeConsoleConfigurationAddDefaultConfigs extends UpgradeScript
{
    public $order = 9100;
    public $version = '9.3.0';
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if ($this->shouldSetConsoleConfigurationDefaults()) {
            ConsoleConfigurationDefaults::setupConsoleConfigurationSettings();
        }
    }

    /**
     * Determine if we should update the ConsoleConfiguration default settings
     *
     * @return bool true if we should update the default settings; false otherwise
     */
    public function shouldSetConsoleConfigurationDefaults(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow100Ent = $this->toFlavor('ent') && version_compare($this->from_version, '10.0.0', '<');
        return $isFlavorConversion || $isBelow100Ent;
    }
}
