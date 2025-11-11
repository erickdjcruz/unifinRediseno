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

use Sugarcrm\Sugarcrm\Entitlements\Subscription;

class OpportunityHooks extends AbstractForecastHooks
{
    /**
     * List of scheduled jobs that have just be scheduled
     * @var array
     */
    protected static $scheduledJobIDs = [];

    /**
     * @return array
     */
    protected static function useRevenueLineItems()
    {
        // get the OpportunitySettings
        $settings = Opportunity::getSettings();
        return (isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems');
    }


    /**
     * Generate a renewal opportunity when an opportunity containing services is closed won.
     *
     * @param Opportunity $bean The bean we are working with
     * @param string $event Which event was fired
     * @param array $args Any additional Arguments
     * @return bool
     */
    public static function generateRenewalOpportunity(Opportunity $bean, string $event, array $args): bool
    {
        if ($bean->canRenew() &&
            !empty($args['dataChanges']['sales_status']) &&
            $args['dataChanges']['sales_status']['after'] === Opportunity::STATUS_CLOSED_WON
        ) {
            $rliBeans = $bean->getClosedWonRenewableRLIs();

            if (!empty($rliBeans)) {
                // check if parent opportunity and its renewal opportunity exist
                $parentBean = $bean->getRenewalParent();

                if ($parentBean) {
                    $renewalBean = $parentBean->getExistingRenewalOpportunity();
                }

                // check if its own renewal opportunity exists
                if (empty($renewalBean)) {
                    $renewalBean = $bean->getExistingRenewalOpportunity();
                }

                // create/update renewal RLIs if Add-On-To PLI is linked to an open renewal Opp
                $rlisUpdated = $bean->updateRenewalRLIs($rliBeans);

                // create a new renewal opportunity if it doesn't exist
                if (empty($renewalBean) && safeCount($rlisUpdated) < safeCount($rliBeans)) {
                    $renewalBean = $bean->createNewRenewalOpportunity();
                }

                // Create new renewal RLIs, if they don't exist, in a new or existing renewal Opp
                foreach ($rliBeans as $rliBean) {
                    if (!safeInArray($rliBean->id, $rlisUpdated)) {
                        // If there is was no previous renewal RLI created, link a newly generated renewal RLI
                        // to our parent RLI
                        if (empty($rliBean->renewal_rli_id)) {
                            $newRliBean = $renewalBean->createNewRenewalRLI($rliBean);

                            $rliBean->renewal_rli_id = $newRliBean->id;
                            $rliBean->save();
                        }
                    }
                }

                if ($renewalBean) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Queue Purchase generation for Closed Won RLIs when an Opportunity becomes
     * Closed Won.
     *
     * Return true if the job was scheduled, otherwise false
     * @param Opportunity $bean
     * @param string $event
     * @param array $args
     * @return bool
     * @throws SugarQueryException
     */
    public static function queueRLItoPurchaseJob(Opportunity $bean, string $event, array $args): bool
    {
        if (!static::useRevenueLineItems()) {
            return false;
        }
        $closedWon = $bean->getRliClosedWonStages();
        if (empty($args['dataChanges']['sales_stage']) ||
            !safeInArray($args['dataChanges']['sales_stage']['after'], $closedWon)) {
            return false;
        }

        static::$scheduledJobIDs = array_merge(
            static::$scheduledJobIDs,
            RevenueLineItem::schedulePurchaseGenerationJob($bean->getGeneratePurchaseRliIds())
        );

        return true;
    }

    /**
     * Gets the list of all ScheduledJobs IDs
     * @return array
     */
    public static function getScheduledJobIDs(): array
    {
        return static::$scheduledJobIDs;
    }

    /**
     * Resets the stack of scheduled job IDs
     */
    public static function resetScheduledJobIDs(): void
    {
        static::$scheduledJobIDs = [];
    }

    /**
     * This is a general hook that takes the Opportunity and saves it to the forecast worksheet record.
     *
     * @param Opportunity $bean The bean we are working with
     * @param string $event Which event was fired
     * @param array $args Any additional Arguments
     * @return bool
     */
    public static function saveWorksheet(Opportunity $bean, $event, $args)
    {
        if (static::isForecastSetup() && !static::useRevenueLineItems()) {
            /* @var $worksheet ForecastWorksheet */
            $worksheet = BeanFactory::newBean('ForecastWorksheets');
            $worksheet->saveRelatedOpportunity($bean);
            return true;
        }

        return false;
    }

    /**
     * Mark all related RLI's on a given opportunity to be deleted
     *
     * @param Opportunity $bean
     * @param $event
     * @param $args
     */
    public static function deleteOpportunityRevenueLineItems(Opportunity $bean, $event, $args)
    {
        if (static::useRevenueLineItems()) {
            $rlis = $bean->get_linked_beans('revenuelineitems', 'RevenueLineItems');
            foreach ($rlis as $rli) {
                $rli->mark_deleted($rli->id);
            }
        }
    }

    /**
     * Set the Sales Status based on the associated RLI's sales_stage
     *
     * @param Opportunity $bean
     * @param string $event
     * @param array $args
     */
    public static function setSalesStatus(Opportunity $bean, $event, $args)
    {
        if (static::useRevenueLineItems() && $bean->ACLFieldAccess('sales_status', 'write') && !empty($bean->id)) {
            // Load forecast config so we have the sales_stage data.
            static::loadForecastSettings();

            // we don't have a new row, so figure out what we need to set it to
            $closed_won = static::$settings['sales_stage_won'];
            $closed_lost = static::$settings['sales_stage_lost'];

            try {
                $won_rlis = static::getCountOfRevenueLineItems($bean->id, $closed_won);
                $lost_rlis = static::getCountOfRevenueLineItems($bean->id, $closed_lost);
                $total_rlis = static::getCountOfRevenueLineItems($bean->id);
            } catch (SugarQueryException $exception) {
                LoggerManager::getLogger()->fatal($exception->getMessage() . $exception->getTraceAsString());
                return;
            }

            if ($total_rlis > ($won_rlis + $lost_rlis)) {
                // still in progress
                $bean->sales_status = Opportunity::STATUS_IN_PROGRESS;
            } elseif ($total_rlis === 0) {
                $bean->sales_status = Opportunity::STATUS_NEW;
            } else {
                // they are equal so if the total lost == total rlis then it's closed lost,
                // otherwise it's always closed won
                if ($lost_rlis == $total_rlis) {
                    $bean->sales_status = Opportunity::STATUS_CLOSED_LOST;
                } else {
                    // update $bean->fetched_row['sales_status'] to avoid triggering generateRenewalOpportunity again
                    $bean->retrieveSalesStatus();
                    $bean->sales_status = Opportunity::STATUS_CLOSED_WON;
                }
            }
        }
    }

    /**
     * @param string $opportunityId
     * @param array|null $salesStages
     * @return int
     * @throws SugarQueryException
     */
    private static function getCountOfRevenueLineItems(string $opportunityId, $salesStages = []) : int
    {
        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('RevenueLineItems'), ['team_security' => false]);
        $query->where()->equals('opportunity_id', $opportunityId);

        if (!empty($salesStages)) {
            $query->where()->in('sales_stage', $salesStages);
        }

        $query->select()->selectReset()->setCountQuery();
        return (int) $query->getOne();
    }

    /**
     * Before we save, we need to check to see if this opp is in a closed state. If so,
     * set it to the proper included/excluded state in case mass_update tried to set it to something wonky
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function beforeSaveIncludedCheck($bean, $event, $args)
    {
        $settings = Forecast::getSettings(true);

        if ($settings['is_setup'] && $event == 'before_save') {
            $won = $settings['sales_stage_won'];
            $lost = $settings['sales_stage_lost'];

            //Check to see if we are in a won state. if so, set the probability to 100 and commit_stage to include.
            //if not, set the probability to 0 and commit_stage to exclude
            if (safeInArray($bean->sales_stage, $won)) {
                $bean->probability = 100;
                $bean->commit_stage = 'include';
            } elseif (safeInArray($bean->sales_stage, $lost)) {
                $bean->probability = 0;
                $bean->commit_stage = 'exclude';
            }
        }
    }

    /**
     * If the account relationship on an opportunity is changed via merging accounts, we need to resave the opportunity
     * so that the worksheet will reflect the new account.
     * @param $bean
     * @param $event
     * @param $args
     */
    public static function fixWorksheetAccountAssignment($bean, $event, $args)
    {
        if (!empty($args)
            && $args['relationship'] == 'accounts_opportunities'
            && static::isForecastSetup()
            && !static::useRevenueLineItems()) {
            $bean->account_id = $args['related_id'];
            static::saveWorksheet($bean, $event, $args);
            return true;
        }

        return false;
    }
}
