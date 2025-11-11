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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey;

use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\GeneralHooks;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\CheckAndPerformRSA as CheckAndPerformRSA;

class JourneyHooks
{
    /**
     * All after_save logic hooks is inside this function.
     *
     * @param \SugarBean $bean The bean that was changed
     * @param string $event
     * @param array $arguments
     */
    public function afterSave($bean, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }

        $this->checkStatusUpdate($bean, $event, $arguments);

        if (!empty($bean->is_cancelled)) {
            $bean->cancel();
        }

        if (!empty($bean->is_deleted)) {
            $bean->mark_deleted($bean->id);
        }
    }

    /**
     * Perform the RSA logic against the Journey
     *
     * @param \SugarBean $journey
     * @param string $event
     * @param array $arguments
     */
    private function checkStatusUpdate(\SugarBean $journey, $event, array $arguments)
    {
        if (array_key_exists('isUpdate', $arguments) && !$arguments['isUpdate']) {
            return;
        }

        $stateAfterValue = GeneralHooks::getBeanValueFromArgs($arguments, 'state', 'after');

        if ($stateAfterValue == \DRI_Workflow::STATE_COMPLETED) {
            CheckAndPerformRSA::checkRelatedSugarAction($journey);
        }
    }
}
