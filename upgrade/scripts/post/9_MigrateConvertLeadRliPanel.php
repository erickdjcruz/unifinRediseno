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
 * Remove the RLI panel from Lead convert layout, and add options to the Opportunity
 * panel if necessary
 */
class SugarUpgradeMigrateConvertLeadRliPanel extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.3.0', '<') && Opportunity::usingRevenueLineItems()) {
            $this->log('Migrating RLI panel on Lead convert layout');
            $this->updateLayout();
        }
    }

    /**
     * Updates the Lead convert layout
     */
    private function updateLayout()
    {
        $parser = new ConvertLayoutMetadataParser('Contacts');
        $newOppDefs = [
            'enableRlis' => false,
            'requireRlis' => false,
            'copyDataToRlis' => false,
        ];

        $rliDefs = $parser->getDefForModule('RevenueLineItems');
        $oppDefs = $parser->getDefForModule('Opportunities');
        if (!empty($rliDefs)) {
            $newOppDefs['enableRlis'] = true;
            if (!empty($rliDefs['required'])) {
                $newOppDefs['requireRlis'] = true;
            }
            if (!empty($rliDefs['copyData'])) {
                $newOppDefs['copyDataToRlis'] = true;
            }
        }

        if (!empty($oppDefs)) {
            $parser->setDefForModule('Opportunities', array_merge($oppDefs, $newOppDefs));
        }
        $parser->removeLayout('RevenueLineItems');
        $parser->deploy();
    }
}
