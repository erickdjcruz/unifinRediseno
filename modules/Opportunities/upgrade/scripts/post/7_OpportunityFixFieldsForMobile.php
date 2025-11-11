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

/**
 * Fix three read-only fields used in the Opportunities mobile edit view.
 */
class SugarUpgradeOpportunityFixFieldsForMobile extends UpgradeScript
{
    public $order = 7050;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '<')) {
            if ($this->shouldUpdateForOpps()) {
                $this->log('Updating three read-only fields for Opportunities');
                $this->updateForOpps();
            }
        }
    }

    /**
     * Determine if the edit view for Opportunity exists
     * @return bool
     */
    private function shouldUpdateForOpps()
    {
        return file_exists('custom/modules/Opportunities/clients/mobile/views/edit/edit.php');
    }

    /**
     * Updates the fields for Opportunities mobile edit view
     */
    private function updateForOpps()
    {
        // These three fields that have readonly set to true before 11.2.0
        // We set them to NOT readonly after upgraded from versions before 11.2.0
        $fieldNames = ['service_start_date', 'date_closed', 'sales_stage'];
        $viewdefManager = new ViewdefManager();
        $editViewDefs = $viewdefManager->loadViewdef('mobile', 'Opportunities', 'edit');

        foreach ($editViewDefs['panels'] as $panelIdx => $panel) {
            foreach ($panel['fields'] as $fieldIdx => $field) {
                if (!is_array($field)) {
                    continue;
                }
                if (in_array($field['name'], $fieldNames) &&
                    isset($field['readonly'])) {
                    unset($field['readonly']);
                }
                $editViewDefs['panels'][$panelIdx]['fields'][$fieldIdx] = $field;
            }
        }
        $viewdefManager->saveViewdef($editViewDefs, 'Opportunities', 'mobile', 'edit');
    }
}
