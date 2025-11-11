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

use Sugarcrm\Sugarcrm\FeatureToggle\Features\UserDownloadsHideOpiWpiPlugins;

/**
 * When upgrade to 13.2 we add the `disable_outlook_and_word_plugins` config param
 * When upgrade to > 15.0.0 we remove the `disable_outlook_and_word_plugins` config param
 */
class SugarUpgradeUpdateConfigValueUserDownloadsHideOpiWpiPlugins extends UpgradeScript
{
    public $order = 9999;

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        $targetVersion = '13.2.0';
        $endVersion = '15.0.0';
        $configField = UserDownloadsHideOpiWpiPlugins::getName();

        $configurator = new Configurator();

        $needClearCache = false;
        if (version_compare($this->to_version, $targetVersion, '=')) {
            $this->log('Adding config field: ' . $configField);
            $configurator->config['features'][$configField] = false;
            $configurator->handleOverride();
            $needClearCache = true;
        }

        if (version_compare($this->from_version, $endVersion, '>=')) {
            $this->log('Removing config field: ' . $configField);
            $this->removeFeatureFromConfigFile($configField);
            $needClearCache = true;
        }

        if ($needClearCache) {
            $configurator->clearCache();
            SugarConfig::getInstance()->clearCache();
        }
    }

    protected function removeFeatureFromConfigFile(string $featureName): void
    {
        $configurator = new Configurator();
        [$config, $configOverride] = $this->upgrader->readConfigFiles();

        unset($configOverride['features'][$featureName]);

        // Configurator does not allow to delete a key via handleOverride(), so we mimic its behaviour here.
        $overrideString = "<?php\n/***CONFIGURATOR***/\n";
        foreach ($configOverride as $key => $val) {
            $overrideString .= override_value_to_string_recursive2('sugar_config', $key, $val, true, $config);
        }
        $overrideString .= '/***CONFIGURATOR***/';
        $configurator->saveOverride($overrideString);
    }
}
