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

class RevenueLineItemHooks
{
    /**
     * @return bool
     */
    protected static function useRevenueLineItems(): bool
    {
        // get the OpportunitySettings
        $settings = Opportunity::getSettings();
        return (isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems');
    }

    /**
     * After the relationship is deleted, we need to resave the RLI.  This
     * will ensure that it will pick up an accout from the associated Opportunity.
     *
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function afterRelationshipDelete($bean, $event, $args)
    {
        if ($event == 'after_relationship_delete') {
            if ($args['link'] == 'account_link' && $bean->deleted == 0) {
                $bean->save();
                return true;
            }
        }
        return false;
    }


    /**
     * Generate Purchases/PLIs for the RLI only if all of the following are true
     * 1. We're in Opps+RLIs mode
     * 2. "Generate Purchase" is changing to "Yes"
     * 3. The Sales Stage is already "Closed Won" (not changing on this save)
     * 4. The parent Opp is also "Closed Won"
     *
     * Return false if job wasn't scheduled, true if it was
     * @param $bean SugarBean
     * @param $event string
     * @param $args array
     * @return bool
     */
    public static function queuePurchaseGeneration($bean, $event, $args): bool
    {
        // Check everything we don't need the db for first to avoid unnecessary work
        $isNewRli = !empty($args['dataChanges']['id']) && empty($args['dataChanges']['id']['before']);
        if (!static::useRevenueLineItems() ||
            (!empty($args['dataChanges']['sales_stage']) && !$isNewRli) ||
            empty($args['dataChanges']['generate_purchase']) ||
            $args['dataChanges']['generate_purchase']['after'] !== 'Yes') {
            return false;
        }
        // Check parent opp and this bean for sales stage
        $opp = BeanFactory::retrieveBean('Opportunities', $bean->opportunity_id);
        $closedStages = $opp->getRliClosedWonStages();
        if (!safeInArray($bean->sales_stage, $closedStages) ||
            !safeInArray($opp->sales_stage, $closedStages)) {
            return false;
        }
        // At this point, we know that generate_purchase has changed to yes,
        // sales_stage hasn't changed this save, and both the RLI and its parent
        // are Closed Won. Schedule the job.
        $data = [['id' => $bean->id,],];
        RevenueLineItem::schedulePurchaseGenerationJob($data);
        return true;
    }

    /**
     * Generate Renewal for parent opportunity for a new RLI if
     * 1. The new Revenue Line Item is 'Closed Won'
     * 2. The Opportunity is already 'Closed Won'
     *
     * @param $bean SugarBean
     * @param $event string
     * @param $args array
     */
    public static function generateRenewalOpportunity($bean, $event, $args)
    {
        if (!$args['isUpdate']) {
            $opportunityBean = BeanFactory::retrieveBean('Opportunities', $bean->opportunity_id);
            if ($bean->service == 1 &&
                $bean->renewable == 1 &&
                $opportunityBean &&
                $opportunityBean->sales_status === Opportunity::STATUS_CLOSED_WON &&
                safeInArray($bean->sales_stage, $opportunityBean->getRliClosedWonStages())) {
                // Spoof that sales status has changed in the Opportunity to
                // trigger renewal opportunity update
                $args['dataChanges']['sales_status']['after'] = Opportunity::STATUS_CLOSED_WON;
                OpportunityHooks::generateRenewalOpportunity($opportunityBean, $event, $args);
            }
        }
    }
}
