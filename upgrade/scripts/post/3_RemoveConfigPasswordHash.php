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
 * Remove 'passwordHash' property from $sugar_config
 */
class SugarUpgradeRemoveConfigPasswordHash extends UpgradeScript
{
    public function run()
    {
        $configurator = new \Configurator();
        [$config, $configOverride] = $this->upgrader->readConfigFiles();

        unset($config['passwordHash']);
        unset($this->upgrader->config['passwordHash']);
        unset($configOverride['passwordHash']);

        // Configurator does not allow to delete a key via handleOverride(), so we mimic its behaviour here.
        $overrideString = "<?php\n/***CONFIGURATOR***/\n";
        foreach ($configOverride as $key => $val) {
            $overrideString .= override_value_to_string_recursive2('sugar_config', $key, $val, true, $config);
        }
        $overrideString .= '/***CONFIGURATOR***/';
        $configurator->saveOverride($overrideString);

        rebuildConfigFile($config, $this->upgrader->context['versionInfo'][0]);
    }
}
