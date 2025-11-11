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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdMConfig;

/**
 * Upgrade script to remove IdM-related settings from config_override file, since all the settings now reside in the DB.
 */
class SugarUpgradeRemoveIdmSettingsFromConfigFile extends UpgradeScript
{
    public $order = 9651;
    public $type = self::UPGRADE_CORE;

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        // Proceed only if there is the idm-related section in the config file.
        if (empty($this->getConfigurator()->config[IdMConfig::IDM_MODE_KEY])) {
            return;
        }

        // Proceed only if the idm-related config has been already put in the DB.
        if (!$this->isIdmDataInDb()) {
            return;
        }

        $this->removeSettingsFromConfigFile();
    }

    /**
     * Remove idm-related section from the config file
     */
    protected function removeSettingsFromConfigFile(): void
    {
        $configurator = $this->getConfigurator();
        [$config, $configOverride] = $this->upgrader->readConfigFiles();

        unset($configOverride[IdMConfig::IDM_MODE_KEY]);

        // Configurator does not allow to delete a key via handleOverride(), so we mimic its behaviour here.
        $overrideString = "<?php\n/***CONFIGURATOR***/\n";
        foreach ($configOverride as $key => $val) {
            $overrideString .= override_value_to_string_recursive2('sugar_config', $key, $val, true, $config);
        }
        $overrideString .= '/***CONFIGURATOR***/';
        $configurator->saveOverride($overrideString);
    }

    /**
     * Is idm-related config in the DB?
     * @return bool
     */
    protected function isIdmDataInDb(): bool
    {
        $admin = \Administration::getSettings(IdMConfig::IDM_MODE_KEY, true);
        return isset($admin->settings['idm_mode_enabled']);
    }

    /**
     * @return Configurator
     */
    protected function getConfigurator(): \Configurator
    {
        return new \Configurator();
    }
}
