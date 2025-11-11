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
 * Update config.php settings
 */
class SugarUpgradeConfigSettings extends UpgradeScript
{
    public $order = 3000;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * Keys that should be updated on all upgrades, provided their values are
     * different than default
     * @var array
     */
    private $forceUpgrade = [
        'preview_edit',
        'default_background_image',
    ];

    public function run()
    {
        // Fill the missing settings.
        $defaultSettings = get_sugar_config_defaults();
        foreach ($defaultSettings as $key => $defaultValue) {
            // This adds new configs that have not been previously set
            if (!array_key_exists($key, $this->upgrader->config)) {
                $this->log("Setting $key does not exist. Setting the default value.");
                $this->upgrader->config[$key] = $defaultValue;
            }

            // This adds config settings that were previously set but are now
            // different, but only if they are not the default value already
            if (in_array($key, $this->forceUpgrade)) {
                $this->log("Config key $key is set to be updated.");
                if ($this->upgrader->config[$key] != $defaultValue) {
                    $this->log("Config key $key updated to new value.");
                    $this->upgrader->config[$key] = $defaultValue;
                } else {
                    $this->log("Config key $key was not changed. It is already set to default.");
                }
            }
        }

        $this->upgrader->config['sugar_version'] = $this->to_version;

        if (!isset($this->upgrader->config['logger'])) {
            $this->upgrader->config['logger'] = [
                'level' => 'fatal',
                'file' => [
                    'name' => 'sugarcrm',
                    'dateFormat' => '%c',
                    'maxSize' => '10MB',
                    'maxLogs' => 10,
                    'suffix' => '', // bug51583, change default suffix to blank for backwards comptability
                ],
            ];
        }

        if (!isset($this->upgrader->config['lead_conv_activity_opt'])) {
            $this->upgrader->config['lead_conv_activity_opt'] = 'copy';
        }


        // We no longer have multiple themes support.

        // We removed the ability for the user to choose his preferred theme.
        // In the future, we'll add this feature back, in the new Sidecar Themes
        // format.
        // Backward compatibilty modules look and feel must be in accordance to
        // Sidecar modules, thus there is only one possible theme: `RacerX`
        $this->upgrader->config['default_theme'] = 'RacerX';

        $this->removeMassActionsDefaultSettings();
        $this->fixConfigSettings($defaultSettings);
        $this->unserializeXssTags($defaultSettings);
        $this->setDefaultPrivateIps();
        $this->fixGeneratedPasswordSetting();

        $this->removeFallbackVersionFromConfig();
    }

    private function setDefaultPrivateIps(): void
    {
        if (!isset($this->upgrader->config['security']['private_ips'])) {
            $this->upgrader->config['security']['private_ips'] = [
                '10.0.0.0|10.255.255.255',
                '172.16.0.0|172.31.255.255',
                '192.168.0.0|192.168.255.255',
                '169.254.0.0|169.254.255.255',
                '127.0.0.0|127.255.255.255',
            ];
        }
    }

    private function unserializeXssTags($config)
    {
        if (is_array($config['email_xss'])) {
            return;
        }
        $this->upgrader->config['email_xss'] = unserialize(base64_decode($config['email_xss']), ['allowed_classes' => false]);
    }

    /**
     * Overwrites some existing config keys for specific versions
     *
     * @param array $config Default Sugar config
     */
    private function fixConfigSettings($config)
    {
        $data = [
            '7.8.0.0' => [
                'snip_url',
            ],
        ];

        foreach ($data as $version => $config_keys) {
            if (version_compare($this->from_version, $version, '<')) {
                foreach ($config_keys as $key) {
                    if (isset($config[$key])) {
                        $this->upgrader->config[$key] = $config[$key];
                    }
                }
            }
        }
    }

    /**
     * Removes system's mass actions settings if they are same as pre - 7.6.0
     * default settings.
     */
    private function removeMassActionsDefaultSettings()
    {
        if (version_compare($this->from_version, '7.6.0', '>') || empty($this->upgrader->config['mass_actions'])) {
            return;
        }
        $this->log('Checking mass config action values...');
        $settings = [
            'mass_update_chunk_size' => 20,
            'mass_delete_chunk_size' => 20,
            'mass_link_chunk_size' => 20,
        ];
        foreach ($settings as $setting => $previousDefaultValue) {
            if (!empty($this->upgrader->config['mass_actions'][$setting]) &&
                $this->upgrader->config['mass_actions'][$setting] === $previousDefaultValue
            ) {
                unset($this->upgrader->config['mass_actions'][$setting]);
            }
        }
    }

    private function fixGeneratedPasswordSetting()
    {
        if (version_compare($this->from_version, '12.3.0', '>') || empty($this->upgrader->config['passwordsetting'])) {
            return;
        }
        $this->log('Disabling system generated passwords...');
        $this->upgrader->config['passwordsetting']['SystemGeneratedPasswordON'] = false;
        if (isset($this->upgrader->config['passwordsetting']['generatepasswordtmpl'])) {
            $id = $this->upgrader->config['passwordsetting']['generatepasswordtmpl'];
            $tpl = BeanFactory::getBean('EmailTemplates', $id);
            $tpl->mark_deleted($id);
            unset($this->upgrader->config['passwordsetting']['generatepasswordtmpl']);
        }
    }

    /**
     * @return void
     */
    private function removeFallbackVersionFromConfig(): void
    {
        if (version_compare($this->from_version, '12.3.0', '>')
            || empty($this->upgrader->config['product_definition']['options']['fallback_version'])) {
            return;
        }
        unset($this->upgrader->config['product_definition']['options']['fallback_version']);
    }
}
