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
 * Add editable 'sales stage' to layouts in rli mode.
 */
class SugarUpgradeOpportunityUpdateSalesStageMetadata extends UpgradeScript
{
    public $order = 7030;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if ($this->toFlavor('ent') &&
            version_compare($this->from_version, '10.1.0', '<') &&
            Opportunity::usingRevenueLineItems()) {
            $this->fixExistingData();
            $this->addNewData();
        }
    }

    /**
     * Adds 'sales_stage' to views.
     */
    protected function addNewData()
    {
        SugarAutoLoader::load('modules/Opportunities/include/OpportunityViews.php');
        $view = new OpportunityViews();
        $fieldMap = [
            'sales_stage' => true,
        ];
        $view->processBaseRecordLayout($fieldMap);
        $view->processMobileRecordLayout($fieldMap);
        $view->processPreviewLayout($fieldMap);
        $view->processListViews($fieldMap);
    }

    /**
     * Updates 'sales_stage' field def if it already exists.
     */
    protected function fixExistingData()
    {
        $newDef = [
            'name' => 'sales_stage',
            'type' => 'enum-cascade',
            'label' => 'LBL_LIST_SALES_STAGE',
            'enabled' => true,
            'default' => true,
            'disable_field' => [
                'total_revenue_line_items',
                'closed_revenue_line_items',
            ],
        ];
        $parser = ParserFactory::getParser(MB_RECORDVIEW, 'Opportunities', null, null, 'base');
        $parser->setFieldProps(['sales_stage'], $newDef);
        $parser->handleSave(false, false);
    }
}
