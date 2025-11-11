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

class HomeDefaults
{
    /**
     * Sets up the default Home config settings
     */
    public static function setupHomeSettings($property = '')
    {
        $admin = BeanFactory::newBean('Administration');
        if ($property === '') {
            $homeConfig = self::getDefaults();
        } else {
            $homeConfig = array_filter(
                self::getDefaults(),
                function ($key) use ($property) {
                    return $key === $property;
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        foreach ($homeConfig as $name => $value) {
            if (empty(self::getSettings($name))) {
                $admin->saveSetting('Home', $name, $value, 'base');
            }
        }
    }

    /**
     * Returns the default values for Home module config
     *
     * @return array
     */
    public static function getDefaults(): array
    {
        $settings = [
            'color' => 'ocean',
            'icon' => 'sicon-home-lg',
            'display_type' => 'icon',
        ];
        return self::sortedSettings($settings);
    }

    /**
     * Function to send the settings in a specific order
     *
     * @param array $settings
     * @return array
     */
    private static function sortedSettings(array $settings): array
    {
        return [
            'color' => $settings['color'],
            'display_type' => $settings['display_type'],
            'icon' => $settings['icon'],
        ];
    }

    /**
     * Function get one/all Home module config settings
     *
     * @param string $property
     * @return array|string
     */
    public static function getSettings(string $property = '')
    {
        /* @var $admin Administration */
        $admin = BeanFactory::newBean('Administration');
        if ($property === '') {
            return self::sortedSettings($admin->getConfigForModule('Home'));
        }
        return $admin->getConfigForModule('Home')[$property] ?? [];
    }
}
