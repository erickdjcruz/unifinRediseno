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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage;

use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\GeneralHooks;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\CheckAndPerformRSA as CheckAndPerformRSA;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\CustomerJourneyException;

class StageHooks
{
    /**
     * All after_save logic hooks is inside this function.
     *
     * @param object $bean
     * @param string $event
     * @param array $arguments
     */
    public function afterSave($bean, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }

        $this->checkStatusUpdate($bean, $event, $arguments);
        $this->startNextJourneyOnStageComplete($bean, $event, $arguments);
    }

    /**
     * after_save logic hook
     *
     * Start Next Smart Guide if stage completed on Smart Guide template related to stage
     *
     * Also triggers the completed events if applicable
     *
     * @param \SugarBean $stage
     * @param string $event
     * @param array $arguments
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     * @throws Exception
     */
    public function startNextJourneyOnStageComplete(\SugarBean $stage, $event, array $arguments)
    {
        $stateAfterValue = GeneralHooks::getBeanValueFromArgs($arguments, 'state', 'after');

        if ($stateAfterValue === \DRI_SubWorkflow::STATE_COMPLETED &&
            !empty($stage->start_next_journey_id) &&
            !empty($arguments['dataChanges']) &&
            !empty($arguments['dataChanges']['state'])) {
            try {
                $journey = \BeanFactory::retrieveBean('DRI_Workflows', $stage->dri_workflow_id);
                if (!empty($journey)) {
                    foreach ($journey->getParentDefinitions() as $parentDef) {
                        if (!empty($journey->{$parentDef['id_name']})) {
                            $template = \BeanFactory::retrieveBean('DRI_Workflow_Templates', $stage->start_next_journey_id);
                            if (!empty($template)) {
                                $availableModules = unencodeMultienum($template->available_modules);
                                if (!in_array($parentDef['module'], $availableModules)) {
                                    throw new \SugarApiExceptionInvalidParameter($parentDef['module'] . ' is not in available modules of template');
                                }
                            }
                            $parent = \BeanFactory::retrieveBean($parentDef['module'], $journey->{$parentDef['id_name']});
                            if (!empty($parent)) {
                                \DRI_Workflow::start($parent, $stage->start_next_journey_id);
                            }
                        }
                    }
                }
            } catch (CustomerJourneyException\InvalidLicenseException $e) {
                // omit errors when license is not valid or user missing access
            } catch (CustomerJourneyException\NotFoundException|\SugarApiExceptionError $e) {
                throw new \SugarApiExceptionInvalidParameter($e->getMessage());
            }
        }
    }

    /**
     * Perform the RSA logic against the stage
     *
     * @param object $stage
     * @param string $event
     * @param array $arguments
     */
    private function checkStatusUpdate(\SugarBean $stage, $event, array $arguments)
    {
        if (!$arguments['isUpdate']) {
            return;
        }

        $stateAfterValue = GeneralHooks::getBeanValueFromArgs($arguments, 'state', 'after');

        if ($stage->hasTemplate() && ($stateAfterValue == \DRI_SubWorkflow::STATE_COMPLETED || $stateAfterValue == \DRI_SubWorkflow::STATE_IN_PROGRESS)) {
            CheckAndPerformRSA::checkRelatedSugarAction($stage);
        }
    }
}
