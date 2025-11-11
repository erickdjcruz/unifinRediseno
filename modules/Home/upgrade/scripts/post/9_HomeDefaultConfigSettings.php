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

class SugarUpgradeHomeDefaultConfigSettings extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '12.3.0', '<')) {
            $properties = ['color', 'icon', 'display_type',];
            foreach ($properties as $property) {
                if (empty(HomeDefaults::getSettings($property))) {
                    $this->log('Adding ' . $property . ' to Home module config settings');
                    HomeDefaults::setupHomeSettings($property);
                    $this->log('Finished adding ' . $property . ' as home module config settings');
                } else {
                    $this->log('Property ' . $property . ' for home module already exists in config settings');
                }
            }
        }
    }
}
