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

class OpportunitiesApi extends ModuleApi
{
    public function registerApiRest()
    {
        return [
            'update' => [
                'reqType' => 'PUT',
                'path' => ['Opportunities', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates a record of the specified type',
                'longHelp' => 'include/api/help/module_record_put_help.html',
            ],
        ];
    }

    /**
     * Updates the opportunities record
     *
     * {@inheritdoc}
     */
    public function updateRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        if (Opportunity::usingRevenueLineItems() && !$this->isValidServiceStartDate($api, $args)) {
            throw new SugarApiExceptionInvalidParameter(
                translate('LBL_SERVICE_START_DATE_INVALID', 'Opportunities'),
            );
        }

        parent::updateRecord($api, $args);

        // Check for any values that need to be cascaded down to related RLIs
        $settings = Opportunity::getSettings();
        if ($settings['opps_view_by'] === 'RevenueLineItems') {
            $data = [];
            $bean = $this->loadBean($api, $args, 'save');

            if (!empty($args['commit_stage']) && $bean->commit_stage !== $args['commit_stage']) {
                $data['commit_stage'] = $args['commit_stage'];
            }

            // probability is stored on the Opportunity bean as a float, but
            // the value coming in from the API is a string. We need to use
            // SugarMath rather than === to check for equality accurately
            if (!empty($args['probability']) &&
                SugarMath::init($args['probability'])->comp($bean->probability) !== 0) {
                $data['probability'] = $args['probability'];
            }

            if (!empty($data)) {
                $this->updateRevenueLineItems($bean, $data);
            }
        }

        return $this->getLoadedAndFormattedBean($api, $args);
    }


    /**
     * Rolls up data to all RLIs that are not won/lost.
     *
     * @param $bean SugarBean The Opportunity Bean
     * @param array $data Data being upgraded on the Opportunity
     */
    protected function updateRevenueLineItems($bean, $data)
    {
        Activity::disable();

        if ($bean && $bean->load_relationship('revenuelineitems')) {
            $rlis = $bean->revenuelineitems->getBeans();
            foreach ($rlis as $rli) {
                $hasChanged = false;
                if (safeInArray($rli->sales_stage, $bean->getClosedStages())) {
                    continue;
                }
                foreach ($data as $fieldName => $fieldValue) {
                    if ($rli->{$fieldName} !== $fieldValue) {
                        $hasChanged = true;
                        $rli->{$fieldName} = $fieldValue;
                    }
                }
                if ($hasChanged) {
                    $rli->save();
                }
            }
        }

        Activity::restoreToPreviousState();
    }

    /**
     * Validate that the service start date isn't being set to a date after the service
     * end date of any add on RLIs
     * @param $api
     * @param $args
     * @return bool
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    protected function isValidServiceStartDate($api, $args)
    {
        // Check for changed values in API arguments first; if no changes are
        // being made to the field, use the existing values
        $opp = $this->loadBean($api, $args, 'save');
        $service_start_date = !empty($args['service_start_date']) ? $args['service_start_date'] : $opp->service_start_date;
        if (empty($service_start_date)) {
            return true;
        }
        // Find RLIs under this Opp that are add ons and have a service end date
        // after the selected service start date.
        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('RevenueLineItems'), ['team_security' => 'false']);
        $query->select(['id']);
        $query->where()->queryAnd()
            ->equals('opportunity_id', $opp->id)
            ->isNotEmpty('add_on_to_id')
            ->lt('service_end_date', $service_start_date)
            ->notIn('sales_stage', $opp->getClosedStages());

        return empty($query->execute());
    }
}
