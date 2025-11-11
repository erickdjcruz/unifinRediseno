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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\TargetResolver as TargetResolver;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\Email as CJFormsEmail;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\StatusHelper;
use Sugarcrm\Sugarcrm\Util\Uuid;

class CheckAndPerformRSA
{
    /**
     * Automatically update or create a record based on the related sugar action
     *
     * @param SugarBean $stageOrJourney
     */
    public static function checkRelatedSugarAction($stageOrJourney)
    {
        $rsaBeans = self::getForms($stageOrJourney);
        foreach ($rsaBeans as $rsaBean) {
            self::performRelatedSugarAction($rsaBean, $stageOrJourney);
        }
    }

    /**
     * Automatically update related smart guides based on a parent action
     *
     * @param \SugarBean $parent
     * @param string $event
     * @param array $arguments
     */
    public static function checkAndPerformParentRSA(\SugarBean $parent, string $event, array $arguments)
    {
        $rsaRecords = self::getParentRelatedRSA($parent);
        if (!empty($rsaRecords)) {
            foreach ($rsaRecords as $rsaRecord) {
                $filterCriteria = $rsaRecord['field_trigger'];
                $filterCriteriaFieldData = json_decode($filterCriteria, true);
                $targetActionField = $rsaRecord['target_action'];
                $targetActionFieldData = json_decode($targetActionField, true);
                $workflowTemplateID = $rsaRecord['smart_guide_template_id'];
                $parentToSmartGuideRSA = new ParentToSmartGuideRSA($parent, $workflowTemplateID);
                if (!empty($targetActionFieldData) && !empty($filterCriteriaFieldData) && !empty($filterCriteriaFieldData['filterDef'])) {
                    $parentToSmartGuideRSA->performParentRSA($targetActionFieldData, $filterCriteriaFieldData['filterDef']);
                }
            }
        }
    }

    /**
     * Get the forms against the stage or journey
     *
     * @param object $stageOrJourney
     * @return array
     */
    public static function getForms($stageOrJourney, $mainTriggerType = \CJ_Form::MAIN_TRIGGER_EVENT_SG_To_SA)
    {
        $filtered = [];
        $stageOrJourneyTemplate = $stageOrJourney->getTemplate();

        if ($stageOrJourneyTemplate->load_relationship('forms')) {
            $forms = $stageOrJourneyTemplate->forms->getBeans();
            if ($forms) {
                foreach ($forms as $form) {
                    if ($form->active &&
                        $form->parent_id === $stageOrJourneyTemplate->id &&
                        $form->main_trigger_type === $mainTriggerType
                    ) {
                        $filtered[] = $form;
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * It will handle the automatic create/update of the stage and journey records
     *
     * @param \SugarBean $RSABean
     * @param \SugarBean $stageOrJourney
     * @throws \SugarApiExceptionError
     * @throws \SugarApiExceptionInvalidParameter
     */
    private static function performRelatedSugarAction(\SugarBean $RSABean, \SugarBean $stageOrJourney)
    {
        if (empty($RSABean->main_trigger_type) || $RSABean->main_trigger_type !== \CJ_Form::MAIN_TRIGGER_EVENT_SG_To_SA) {
            return;
        }

        if (!($RSABean->action_type === \CJ_Form::ACTION_TYPE_CREATE_RECORD || $RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD)) {
            return;
        }

        if (($RSABean->action_type === \CJ_Form::ACTION_TYPE_CREATE_RECORD && $RSABean->action_trigger_type !== \CJ_Form::ACTION_TRIGGER_AUTOMATIC_CREATE) ||
            ($RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD && $RSABean->action_trigger_type !== \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE)
        ) {
            return;
        }

        if (($stageOrJourney->module_dir === 'DRI_SubWorkflows' && ((($stageOrJourney->state === 'completed') && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_COMPLETED) ||
                    (($stageOrJourney->state === 'in_progress') && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_IN_PROGRESS))) ||
            ($stageOrJourney->module_dir === 'DRI_Workflows' && ($stageOrJourney->state === 'completed') && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_COMPLETED)
        ) {
            try {
                $parent = $stageOrJourney->getParent();
            } catch (CJException\ParentNotFoundException $e) {
                return;
            }

            $finder = new TargetResolver($RSABean);

            try {
                $response = $finder->resolve($parent, $stageOrJourney);
            } catch (\SugarApiExceptionError $e) {
                if ($RSABean->ignore_errors) {
                    return;
                }
                throw new \SugarApiExceptionError($e->getMessage());
            }

            $parent = $response['parent'];
            $target = $response['target'];
            $linkName = $response['linkName'];
            $module = $response['module'];
            $allBeans = $response['allBeans'];

            if (empty($parent->id) || ($RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD && $RSABean->action_trigger_type === \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE && empty($target->id))) {
                if (!$RSABean->ignore_errors) {
                    throw new \SugarApiExceptionInvalidParameter(translate('LBL_COULD_NOT_FIND_RELATED_RECORD', 'CJ_Forms'));
                }
                return;
            }

            $id = ($RSABean->action_trigger_type === \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE) ? $target->id : null;
            $target2 = \BeanFactory::retrieveBean($module, $id, ['use_cache' => false]);

            if (!empty($target2->id)) { //Update all the related records
                foreach ($allBeans as $singleBean) {
                    $child = \BeanFactory::getBean($module, $singleBean->id);
                    \CJ_Form::setTargetValues($child, $RSABean);
                    $child->save();
                }
            } else { //create records
                \CJ_Form::setTargetValues($target2, $RSABean);

                if ($target2->module_dir === 'DRI_SubWorkflows' && empty($target2->dri_subworkflow_template_id)) {
                    $target2->dri_subworkflow_template_id = '5deef3ac-fcc9-11e6-9550-5254009e5526';
                    $target2->dri_workflow_id = $target->dri_workflow_id;
                } elseif ($target2->module_dir == 'DRI_Workflows' && empty($target2->dri_workflow_template_id)) {
                    $target2->dri_workflow_template_id = 'ea1ecdea-f835-11e6-b8bd-5254009e5526';
                } elseif ($target2->module_dir === 'Emails' && !empty($RSABean->email_templates_id)) {
                    (new CJFormsEmail())->sendEmail($target2, $RSABean, $stageOrJourney);
                }
                $target2->save();
            }

            if ($parent->load_relationship($linkName)) {
                $parent->$linkName->add($target2->id);
            }
        }
    }

    /**
     * It will fetch RSAs for parent beans
     *
     * @param \SugarBean $parentBean
     * @return array $rsaRecords
     */
    private static function getParentRelatedRSA(\SugarBean $parentBean)
    {
        $rsaBean = \BeanFactory::newBean('CJ_Forms');
        $query = new \SugarQuery();
        $query->select(['id', 'smart_guide_template_id', 'field_trigger', 'target_action']);
        $query->from($rsaBean)->where()
            ->equals('module_trigger', $parentBean->getModuleName())
            ->equals('active', '1')
            ->equals('main_trigger_type', 'sugar_action_to_smart_guide');

        return $query->execute();
    }

    /**
     * Check whether there is an active RSA against the bean
     * if yes then check whether filter criteria is met or not
     *
     * @param \SugarBean $parent
     * @param array $changedFields
     * @return boolean
     */
    public static function canPerformRSA(\SugarBean $parent, $changedFields)
    {
        $rsaRecords = self::getParentRelatedRSA($parent);

        if (!empty($rsaRecords)) {
            ParentToSmartGuideRSA::setParentData($parent);

            foreach ($rsaRecords as $rsaRecord) {
                $fieldTrigger = $rsaRecord['field_trigger'];

                if (!empty($fieldTrigger)) {
                    $filterCriteria = json_decode($fieldTrigger, true);
                    $filterCriteriaDef = $filterCriteria['filterDef'];
                    $workflowTemplateID = $rsaRecord['smart_guide_template_id'];

                    if (!empty($filterCriteriaDef)) {
                        if (ParentToSmartGuideRSA::filterCriteriaSatisfies($filterCriteriaDef)) {
                            $activeSmartGuides = ParentToSmartGuideRSA::fetchAllActiveSmartGuides($workflowTemplateID);
                            $doFieldsChanged = self::doFieldsChanged($changedFields, $filterCriteriaDef);

                            if ($doFieldsChanged && safeCount($activeSmartGuides) > 0) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check whether the changed field exixts in filter criteria fields
     *
     * @param array $changedFields
     * @param array $filterCriteriaDef
     * @return boolean
     */
    private static function doFieldsChanged($changedFields, $filterCriteriaDef)
    {
        foreach ($changedFields as $fieldName => $fieldData) {
            foreach ($filterCriteriaDef as $value) {
                if (array_key_exists($fieldName, $value)) {
                    return true;
                }
            }
        }

        return false;
    }
}
