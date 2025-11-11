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

use Sugarcrm\Sugarcrm\MetaData\ViewdefManager;

class SugarUpgradeLeadFixLeadConvertDashboard extends UpgradeScript
{
    // order 9010 this should run after SugarUpgradeMigrateConvertLeadRliPanel
    public $order = 9010;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if ($this->shouldRun()) {
            $this->addLeadConvertDashboard();
            $this->fixLeadConvertDashboardDashlets();
        }
    }

    /**
     * Determines if this upgrader should be run on this upgrade
     *
     * @return bool true if this upgrader should run; false otherwise
     */
    public function shouldRun()
    {
        $isConversion = $this->fromFlavor('pro') && $this->toFlavor('ent');
        $isPre113EntUpgrade = $this->toFlavor('ent') && version_compare($this->from_version, '11.3.0', '<');
        return $isConversion || $isPre113EntUpgrade;
    }

    /**
     * Updates convert view metadata files to add the convert dashboard
     */
    public function addLeadConvertDashboard()
    {
        // We only need to do this if custom metadata already exists for the
        // convert layout
        if (empty(SugarAutoLoader::existing('custom/modules/Leads/clients/base/layouts/convert/convert.php'))) {
            return;
        }

        // Get the metadata for the convert layout
        $viewdefManager = new ViewdefManager();
        $convertMeta = $viewdefManager->loadViewdef('base', 'Leads', 'convert', false, true);

        // Add the Convert view "dashboard" to the metadata and re-save it
        $convertMeta = $this->addConvertDashboardComponent($convertMeta);
        $viewdefManager->saveViewdef($convertMeta, 'Leads', 'base', 'convert', true);
    }

    /**
     * Adds the convert dashboard to the convert layout metdata
     *
     * @param $convertMeta
     * @return array
     */
    public function addConvertDashboardComponent($convertMeta)
    {
        return $this->addComponentToSidebar($convertMeta, LeadViews::getDefaultConvertDashboardMeta());
    }

    /**
     * Recursively searches for the sidebar component and adds the given
     * component to its set of components
     *
     * @param array $convertMeta
     * @param $component
     * @return array
     */
    public function addComponentToSidebar(array $convertMeta, $component)
    {
        if (!empty($convertMeta['name']) && $convertMeta['name'] === 'sidebar') {
            $convertMeta['components'][] = $component;
        } else {
            foreach ($convertMeta as $key => $value) {
                if (is_array($value)) {
                    $convertMeta[$key] = $this->addComponentToSidebar($value, $component);
                }
            }
        }
        return $convertMeta;
    }

    /**
     * Fixes the convert-dashboard metadata to add or remove the dashlets
     * that depend on the Opps mode
     */
    public function fixLeadConvertDashboardDashlets()
    {
        $leadViews = new LeadViews();
        $enableProductDashlets = Opportunity::usingRevenueLineItems() && Lead::isUsingRLIsInConvert();
        $leadViews->toggleConvertDashboardProductDashlets($enableProductDashlets);
    }
}
