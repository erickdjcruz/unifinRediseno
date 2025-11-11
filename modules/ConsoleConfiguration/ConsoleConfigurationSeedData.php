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
 * Handles populating seed data for the Console Configuration
 */
class ConsoleConfigurationSeedData
{
    /**
     * @static
     */
    public static function populateSeedData()
    {
        /* @var $admin Administration */
        $admin = BeanFactory::newBean('Administration');

        $config = ConsoleConfigurationDefaults::getDefaults(1);

        foreach ($config as $name => $value) {
            $admin->saveSetting('ConsoleConfiguration', $name, $value, 'base');
        }
    }
}
