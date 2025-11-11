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
 * Create default Maps configurations
 */
class SugarUpgradeCreateMapsConfigurations extends UpgradeScript
{
    public $order = 9251;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        require_once 'install/MapsDefaultData.php';

        $this->log('Adding default maps configurations...');

        try {
            if (version_compare($this->from_version, '12.0.0', '<')) {
                $admin = BeanFactory::newBean('Administration');
                $mapsDefaultConfig = [
                    'logLevel' => $mapsDefaultLogLevel,
                    'unitType' => $mapsDefaultUnitType,
                    'enabled_modules' => $mapsDefaultEnabledModules,
                    'modulesData' => $mapsDefaultModulesData,
                ];

                foreach ($mapsDefaultConfig as $name => $value) {
                    $admin->saveSetting('maps', $name, $value, 'base');
                }
            }

            $this->log('Done creating maps configurations');
        } catch (Exception $e) {
            $this->log('Failed to insert Maps default data');
        }
    }
}
