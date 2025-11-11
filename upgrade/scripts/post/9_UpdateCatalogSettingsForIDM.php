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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;

/**
 * Class SugarUpgradeUpdateCatalogSettingsForIDM
 *
 * Update catalog_enabled and catalog_url for IDM enabled instances
 */
class SugarUpgradeUpdateCatalogSettingsForIDM extends UpgradeScript
{
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '10.3.0', '<')) {
            $sugarConfig = \SugarConfig::getInstance();
            $idmConfig = $this->getIdmConfig($sugarConfig);
            $idmEnabled = $idmConfig->isIDMModeEnabled();
            if ($idmEnabled) {
                $configurator = $this->getConfigurator();
                $configurator->config['catalog_enabled'] = true;
                $catalogURL = $idmConfig->getCatalogURL();
                if ($catalogURL) {
                    $configurator->config['catalog_url'] = $catalogURL;
                }
                $configurator->handleOverride();
                $configurator->clearCache();
                $sugarConfig->clearCache();
            }
        }
    }

    /**
     * @param SugarConfig $sugarConfig
     * @return IdmConfig
     */
    protected function getIdmConfig($sugarConfig)
    {
        return new IdmConfig($sugarConfig);
    }

    /**
     * @return Configurator
     */
    protected function getConfigurator()
    {
        return new \Configurator();
    }
}
