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

class SugarUpgradeOpportunitySetServiceDuration extends UpgradeScript
{
    public $order = 7550;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        if ($this->toFlavor('ent') &&
            version_compare($this->from_version, '10.2.0', '<')
        ) {
            $settings = Opportunity::getSettings();
            if ($settings['opps_view_by'] !== 'RevenueLineItems') {
                $this->log('Not using Revenue Line Items; Skipping Upgrade Script');
                return;
            }

            $opps = $this->getOppsWithServiceRLIs();
            $this->log('Setting service duration for opportunities - found ' . sizeof($opps) . ' to modify');

            foreach ($opps as $opp) {
                $this->setServiceDurationFields($opp['opportunity_id']);
            }
        }
    }

    /**
     * Gets opps that need to be updated. If there are no service RLIs then the
     * opp-level service duration field remains blank, so we can exclude those
     * from the update.
     * @return array
     * @throws SugarQueryException
     */
    private function getOppsWithServiceRLIs()
    {
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['opportunity_id']);
        $q->distinct(true);
        $q->where()->equals('service', 1);
        return $q->execute();
    }

    /**
     * Sets the service duration fields for the given opp.
     * @param $opp_id
     */
    private function setServiceDurationFields($opp_id)
    {
        $opp = BeanFactory::retrieveBean('Opportunities', $opp_id);
        if (empty($opp->id)) {
            return;
        }

        $service_duration_fields = $opp->calculateServiceDuration();
        $opp->service_duration_value = $service_duration_fields['service_duration_value'];
        $opp->service_duration_unit = $service_duration_fields['service_duration_unit'];
        $opp->service_open_flex_duration_rlis = sizeof($opp->getEditableDurationServiceRLIs());

        $opp->save();
    }
}
