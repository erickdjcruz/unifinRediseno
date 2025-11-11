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

use Doctrine\DBAL\Connection;

/**
 * Update fields that have been modified to be calculated.
 */
class SugarUpgradeOpportunityUpdateForecastingFields extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_ALL;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '12.1.0', '<')) {
            if (Opportunity::usingRevenueLineItems()) {
                $this->updateOpportunitiesWithRlis(new OpportunityWithRevenueLineItem);
            } else {
                $this->updateOpportunitiesWithoutRlis(new OpportunityWithOutRevenueLineItem);
            }
        }
    }

    /**
     * Updates the lost and forecasted_likely fields on all Opportunities records based on their related
     * Revenue Line Items
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function updateOpportunitiesWithRlis($converter)
    {
        // Set Lost field vardefs for Opps+RLI mode
        $converter->updateFieldVardef('lost', [
            'calculated' => true,
            'enforced' => true,
            'formula' => 'rollupConditionalSum($revenuelineitems, "likely_case", "sales_stage", ' .
                'forecastOnlySalesStages(false,true,false))',
        ]);

        // Enable filtering on Lost field
        $this->setFieldFiltering('lost', true);

        // Set Lost values on existing Opportunities based on their RLI properties
        $query = $this->db->getConnection()->createQueryBuilder();
        $closedLostSalesStages = Forecast::getSettings()['sales_stage_lost'] ?? [Opportunity::STAGE_CLOSED_LOST];
        $lostSubquery = $this->db->getConnection()->createQueryBuilder();
        $lostSubquery->select([
            'COALESCE(SUM(likely_case / revenue_line_items.base_rate), 0) * opportunities.base_rate',
        ])
            ->from('revenue_line_items')
            ->where($lostSubquery->expr()->eq('opportunity_id', 'opportunities.id'))
            ->andWhere($lostSubquery->expr()->in(
                'sales_stage',
                $query->createPositionalParameter($closedLostSalesStages, Connection::PARAM_STR_ARRAY)
            ));
        $query->update('opportunities')
            ->set('lost', '(' . $lostSubquery->getSQL() . ')')
            ->executeStatement();
    }

    /**
     * Updates the search view to show or hide "Lost" as a filterable field
     *
     * @param string $fieldName The name of the field
     * @param bool $allowFiltering The filterability of the field to set
     */
    protected function setFieldFiltering(string $fieldName, bool $allowFiltering)
    {
        $filterDefParser = ParserFactory::getParser(MB_BASICSEARCH, 'Opportunities', null, null, 'base');
        if ($allowFiltering) {
            $filterDefParser->addField($fieldName);
        } else {
            $filterDefParser->removeField($fieldName);
        }
        $filterDefParser->handleSave(false, false);
    }

    /**
     * Updates the lost and forecasted_likely fields on all Opportunities records
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function updateOpportunitiesWithoutRlis($converter)
    {
        // Set Lost field vardefs for Opps-only mode
        $converter->updateFieldVardef('lost', [
            'calculated' => true,
            'enforced' => true,
            'formula' => 'ifElse(equal(indexOf($sales_stage, forecastOnlySalesStages(false, true, false)), -1), 0, ' .
                '$amount)',
            'studio' => true,
        ]);

        // Update Forecasted Likely field vardefs ONLY if they have the formula set to "''".
        // This is because the Forecasted Likely field was introduced in 11.3, but not yet
        // calculated in Opps-only mode. We only change the vardefs if we can reasonably
        // assume the admins have kept its default behavior, as we do not want to override
        // any custom formula they have created
        $oppBean = $this->getOpportunityBean();
        $forecastedLikelyDef = $oppBean->getFieldDefinition('forecasted_likely');
        if ($forecastedLikelyDef['calculated'] === false && $forecastedLikelyDef['formula'] === '') {
            $converter->updateFieldVardef('forecasted_likely', [
                'calculated' => true,
                'enforced' => true,
                'formula' => 'ifElse(equal(indexOf($commit_stage, forecastIncludedCommitStages()), -1), 0, $amount)',
            ]);
        }

        // Set Lost values on existing Opportunities
        $closedLostSalesStages = Forecast::getSettings()['sales_stage_lost'] ?? [Opportunity::STAGE_CLOSED_LOST];
        $query = $this->db->getConnection()->createQueryBuilder();
        $salesStagesSQL = $query->createPositionalParameter($closedLostSalesStages, Connection::PARAM_STR_ARRAY);
        $query->update('opportunities')
            ->set('lost', "(CASE WHEN sales_stage IN ($salesStagesSQL)  THEN amount ELSE 0 END)");
        $query->executeStatement();
    }

    /**
     * Returns a fresh Opportunity bean
     *
     * @return SugarBean a base Opportunity bean
     */
    protected function getOpportunityBean()
    {
        return BeanFactory::newBean('Opportunities');
    }
}
