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

namespace Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks;

use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;

class ActivityOrStageTemplateHooksHelper
{
    /**
     *
     * Checks if selected 'Start Next Smart Guide' available in list of available modules of current Smart Guide Template
     *
     * @param \SugarBean $activityOrStageTemplate
     * @param string $event
     * @param array $arguments
     * @throws CustomerJourneyException\NotFoundException
     * @throws \SugarApiExceptionInvalidParameter
     */
    public static function checkAvailableModules(\SugarBean $activityOrStageTemplate, $event, array $arguments)
    {
        if ((self::isFieldChanged($activityOrStageTemplate, 'start_next_journey_id') ||
                self::isFieldChanged($activityOrStageTemplate, 'dri_workflow_template_id')) &&
            !empty($activityOrStageTemplate->start_next_journey_id)
        ) {
            try {
                $currentJourneyTemplate = \DRI_Workflow_Template::getById($activityOrStageTemplate->dri_workflow_template_id);
                $currentJourneyModules = unencodeMultienum($currentJourneyTemplate->available_modules);

                $nextJourneyTemplate = \DRI_Workflow_Template::getById($activityOrStageTemplate->start_next_journey_id);
                $nextJourneyModules = unencodeMultienum($nextJourneyTemplate->available_modules);

                if (empty(array_intersect($currentJourneyModules, $nextJourneyModules))) {
                    $msg = translate('LBL_START_JOURNEY_ERROR', 'DRI_Workflow_Task_Templates');
                    throw new \SugarApiExceptionInvalidParameter($msg);
                }
            } catch (CJException\NotFoundException $e) {
                throw new \SugarApiExceptionInvalidParameter('Start Next Smart Guide: ' . $e->getMessage());
            }
        }
    }

    /**
     *
     * Stores the fetched row on the bean before save to
     * make it available for after save logic hooks
     *
     * @param \SugarBean $activityOrStageTemplate
     */
    public static function saveFetchedRow(\SugarBean $bean)
    {
        $bean->fetched_row_before = $bean->fetched_row;
    }

    /**
     * Checks if given field is changed
     *
     * @param \SugarBean $activityOrStageTemplate
     * @param string $field
     * @return bool
     */
    private static function isFieldChanged(\SugarBean $activityOrStageTemplate, $field)
    {
        if (!isset($activityOrStageTemplate->fetched_row_before[$field])) {
            if (isset($activityOrStageTemplate->{$field}) && !empty($activityOrStageTemplate->{$field})) {
                return true;
            }
            return false;
        }

        return $activityOrStageTemplate->{$field} != $activityOrStageTemplate->fetched_row_before[$field];
    }
}
