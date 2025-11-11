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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityHelper as ActivityHelper;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CustomerJourneyException;

/**
 * This class contains logic hooks related to the parent modules
 *
 */
class JourneyParentHooksHelper
{
    public const ACTIVITY_DUE_DATE_CACHE_PREFIX = 'DRI_Workflows_LogicHook_ParentHook_updateActivityDueDate';
    public const ACTIVITY_START_DATE_CACHE_PREFIX = 'DRI_Workflows_LogicHook_ParentHook_updateActivityStartDate';
    public const ACTIVITY_MOMENTUM_START_DATE_CACHE_PREFIX = 'DRI_Workflows_LogicHook_ParentHook_updateActivityMomentumStartDate';

    /**
     * All after_save logic hooks is inside this function.
     *
     * @param \SugarBean $bean
     * @param string $event
     * @param array $arguments
     */
    public function afterSave(\SugarBean $bean, $event, $arguments)
    {
        if (!hasSystemAutomateLicense()) {
            return;
        }

        $moduleName = $bean->getModuleName();

        if (!self::isEnabledForModule($moduleName) || isset($bean->do_not_reset_template_id)) {
            return;
        }

        $this->startJourney($bean, $arguments);

        if ($moduleName === 'Leads') {
            $this->convertLead($bean, $arguments);
        }

        $this->updateActivityDates($bean, $arguments);
        $this->autoReassignActivities($bean, $event, $arguments);
    }

    /**
     * Checks whether a specific module is enabled
     * for Customer Journey - License Check
     *
     * @param $moduleName
     * @return bool
     */
    public static function isEnabledForModule($moduleName)
    {
        $configurator = new \Configurator();

        return str_contains($configurator->config['customer_journey']['enabled_modules'], $moduleName);
    }

    /**
     * @param string $module
     */
    public static function clearParentActivityDatesCache($module)
    {
        sugar_cache_clear(self::ACTIVITY_DUE_DATE_CACHE_PREFIX . ':' . $module);
        sugar_cache_clear(self::ACTIVITY_START_DATE_CACHE_PREFIX . ':' . $module);
        sugar_cache_clear(self::ACTIVITY_MOMENTUM_START_DATE_CACHE_PREFIX . ':' . $module);
    }

    /**
     * This logic hook gets triggered after a parent is saved.
     *
     * If the conditions of self::shouldStartJourney is met, a new journey
     * will be started related to the parent if not already started..
     *
     * If the conditions of self::toRemoveJourneys is met,
     * all non completed journeys will be unlinked
     *
     * @param \SugarBean $bean
     * @param array $arguments
     * @throws \SugarApiException
     */
    public function startJourney(\SugarBean $bean, $arguments)
    {
        try {
            $shouldStart = $this->shouldStartJourney($bean, $arguments);
            $templateId = $bean->dri_workflow_template_id;
            $this->unsetJourneyTemplateID($bean);

            if ($shouldStart) {
                \DRI_Workflow::start($bean, $templateId);
                $bean->dri_customer_journey_started_template = $templateId;
            }
        } catch (CustomerJourneyException\SameJourneyLimitReachException $ex) {
            $GLOBALS['log']->fatal($ex->getMessage());
            $this->unsetJourneyTemplateID($bean);
        } catch (\SugarApiException $e) {
            $this->unsetJourneyTemplateID($bean);
            if ('ERROR_INVALID_LICENSE' !== $e->messageLabel) {
                throw $e;
            }
        }
    }

    /**
     * @param \SugarBean $bean
     * @param array $arguments
     * @throws SugarQueryException
     */
    public function updateActivityDates(\SugarBean $bean, $arguments)
    {
        // if the bean is new there is no need to check if we should update the date
        if (isset($arguments['isUpdate']) && $arguments['isUpdate'] == false) {
            return;
        }

        $this->updateActivityDueDate($bean, $arguments);
        $this->updateActivityStartDate($bean, $arguments);
        $this->updateActivityMomentumStartDate($bean, $arguments);
    }

    /**
     * The Journey will be started if the template id is set and one of the following conditions is met:
     *
     *   - the parent is new
     *   - the template id has been changed
     *
     * @param \SugarBean $bean
     * @param array $arguments
     * @return bool
     */
    private function shouldStartJourney(\SugarBean $bean, $arguments)
    {
        return !empty($bean->dri_workflow_template_id)
            && (empty($bean->is_customer_journey_activity) || $bean->isPASaveRequest)
            && (empty($bean->dri_customer_journey_started_template) || $bean->dri_workflow_template_id !== $bean->dri_customer_journey_started_template)
            && (empty($arguments['stateChanges'])
                || $bean->dri_workflow_template_id !== $arguments['stateChanges']['dri_workflow_template_id']['before'])
            && !$bean->inOperation('delete');
    }

    /**
     * @param \SugarBean $bean
     * @param array $arguments
     * @throws SugarQueryException
     */
    private function updateActivityDueDate(\SugarBean $bean, $arguments)
    {
        $key = self::ACTIVITY_DUE_DATE_CACHE_PREFIX . ':' . $bean->module_dir;
        $rows = $this->getActivityRows($key, $bean->module_dir, 'duedate');

        // check for updates in relevant fields
        $templates = [];
        foreach ($rows as $row) {
            if (!isset($arguments['stateChanges'][$row['due_date_field']]['before'])
                || $bean->{$row['due_date_field']} !== $arguments['stateChanges'][$row['due_date_field']]['before']) {
                $templates[$row['activity_type']][] = $row['id'];
            }
        }

        $this->updateRelevantTemplatesActivities($templates, $bean, 'duedate');
    }

    /**
     * @param \SugarBean $bean
     * @param array $arguments
     * @throws SugarQueryException
     */
    private function updateActivityStartDate(\SugarBean $bean, $arguments)
    {
        $key = self::ACTIVITY_START_DATE_CACHE_PREFIX . ':' . $bean->module_dir;
        $rows = $this->getActivityRows($key, $bean->module_dir, 'startdate');

        // check for updates in relevant fields
        $templates = [];
        foreach ($rows as $row) {
            if ($bean->{$row['start_date_field']} !== $arguments['stateChanges'][$row['start_date_field']]['before']) {
                $templates[$row['activity_type']][] = $row['id'];
            }
        }

        $this->updateRelevantTemplatesActivities($templates, $bean, 'startdate');
    }

    /**
     * @param \SugarBean $bean
     * @param array $arguments
     * @throws SugarQueryException
     */
    private function updateActivityMomentumStartDate(\SugarBean $bean, $arguments)
    {
        $key = self::ACTIVITY_MOMENTUM_START_DATE_CACHE_PREFIX . ':' . $bean->module_dir;
        $rows = $this->getActivityRows($key, $bean->module_dir, 'momentum');

        // check for updates in relevant fields
        $templates = [];
        foreach ($rows as $row) {
            if ($bean->{$row['momentum_start_field']} !== $arguments['stateChanges'][$row['momentum_start_field']]['before']) {
                $templates[$row['activity_type']][] = $row['id'];
            }
        }

        $this->updateRelevantTemplatesActivities($templates, $bean, 'momentum');
    }

    /**
     * For activities momentum or due date update,
     * actual update the data accordingly
     *
     * @param array $templates
     * @param \SugarBean $bean
     * @param string $momentumOrStartDueDate
     *
     * @return void
     */
    private function updateRelevantTemplatesActivities($templates, $bean, $momentumOrStartDueDate = '')
    {
        if (empty($templates) || empty($bean)) {
            return;
        }

        $parentIDFieldName = strtolower($bean->object_name) . '_id';

        foreach ($templates as $module => $ids) {
            $handler = ActivityHandlerFactory::factory($module);
            $activity = \BeanFactory::newBean($module);

            // build query to fetch activities related to the parent and the active journeys
            $query = new \SugarQuery();
            $query->select('id');
            $query->from($activity, ['alias' => 'activity']);

            $join = $query->joinTable('dri_workflows', ['alias' => 'workflows']);
            $join->on()->equalsField('workflows.id', 'activity.dri_workflow_id');
            $join->on()->equals('workflows.deleted', 0);

            $query->where()
                    ->in('activity.dri_workflow_task_template_id', array_unique($ids))
                    ->equals("workflows.$parentIDFieldName", $bean->id)
                    ->equals('workflows.state', 'in_progress');

            foreach ($query->execute() as $row) {
                /** @var \SugarBean $activity */
                $activity = \BeanFactory::retrieveBean($module, $row['id']);

                if ($activity) {
                    if ($momentumOrStartDueDate == 'momentum') {
                        $handler->setMomentumStartDateFromParentField($activity);
                    } elseif ($momentumOrStartDueDate == 'duedate') {
                        $handler->setDueDateFromParentField($activity);
                    } elseif ($momentumOrStartDueDate == 'startdate') {
                        $handler->setStartDateFromParentField($activity);
                    }
                    $activity->save();
                }
            }
        }
    }

    /**
     * For activities momentum or due date update,
     * return the task templates according to the
     * condition
     *
     * @param string $key
     * @param string $module
     * @param string $momentumOrStartDueDate
     *
     * @return array $rows
     */
    private function getActivityRows($key, $module, $momentumOrStartDueDate = '')
    {
        if (empty($key) || empty($module) || empty($momentumOrStartDueDate) || !in_array($momentumOrStartDueDate, ['momentum', 'duedate', 'startdate'])) {
            return [];
        }

        $rows = sugar_cache_retrieve($key);
        if (null === $rows) {
            // get activity templates to check for updates in
            $query = new \SugarQuery();
            $query->from(\BeanFactory::newBean('DRI_Workflow_Task_Templates'));

            if ($momentumOrStartDueDate == 'momentum') {
                $query->select('id', 'activity_type', 'momentum_start_field');
                $query->where()
                    ->equals('momentum_start_type', \DRI_Workflow_Task_Template::MOMENTUM_START_TYPE_PARENT_DATE_FIELD)
                    ->equals('momentum_start_module', $module);
            } elseif ($momentumOrStartDueDate == 'duedate') {
                $query->select('id', 'activity_type', 'due_date_field');
                $query->where()
                    ->equals('task_due_date_type', \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD)
                    ->equals('due_date_module', $module);
            } elseif ($momentumOrStartDueDate == 'startdate') {
                $query->select('id', 'activity_type', 'start_date_field');
                $query->where()
                    ->equals('task_start_date_type', \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD)
                    ->equals('start_date_module', $module);
            }

            $rows = $query->execute();
            sugar_cache_put($key, $rows);
        }
        return $rows;
    }

    /**
     * Reassigns assignee for specific activity
     *
     * @param \SugarBean $activity
     * @param \SugarBean $stage
     * @param \SugarBean $activityTemplate
     */
    private function autoReassignAssigneeFromStage(\SugarBean $activity, \DRI_SubWorkflow $stage, \DRI_Workflow_Task_Template $activityTemplate)
    {
        if (!empty($activity->assigned_user_id)) {
            return;
        }

        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

        if ($activityTemplate->getAssigneeRule($stage) === \DRI_Workflow_Template::ASSIGNEE_RULE_CREATE ||
            ($activityTemplate->getAssigneeRule($stage) === \DRI_Workflow_Template::ASSIGNEE_RULE_STAGE_START && $stage->state == \DRI_SubWorkflow::STATE_IN_PROGRESS)) {
            //setAssignmentSummary, flag for make sure send a single compile assignment summary email
            $activity->setAssignmentSummary = true;
            $activityHandler->applyAssigneeRuleOnActivity($activityTemplate, $activity, $stage);
        }
    }

    /**
     * Reassigns assignee for specific activity
     *
     * @param \DRI_SubWorkflow $stage
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $activityTemplate
     * @param string $previousParentAssignedUserId
     */
    private function updateTargetAssigneeActivity(\DRI_SubWorkflow $stage, \SugarBean $activity, \DRI_Workflow_Task_Template $activityTemplate, $previousParentAssignedUserId)
    {
        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

        # if previously there was no assignee for parent
        if (empty($previousParentAssignedUserId)) {
            $this->autoReassignAssigneeFromStage($activity, $stage, $activityTemplate);
            return;
        }

        if ($previousParentAssignedUserId != $activity->assigned_user_id) {
            return;
        }

        //setAssignmentSummary, flag for make sure send a single compile assignment summary email
        $activity->setAssignmentSummary = true;
        $activityHandler->applyAssigneeRuleOnActivity($activityTemplate, $activity, $stage);
    }

    /**
     * Checks if activity assignee needs to be updated based on specific activity completed
     *
     * @param \SugarBean $activity
     * @param \DRI_SubWorkflow $stage
     */
    private function checkAssigneeUpdateSpecificActivityCompleted(\SugarBean $activity, \DRI_SubWorkflow $stage)
    {
        $stage->getJourney()->getDependentActivity($activity);
    }

    /**
     * Checks if activity assignee needs to be updated based on previous activity completed
     *
     * @param ActivityHelper $activityHandler
     * @param \SugarBean $activity
     * @param \DRI_SubWorkflow $stage
     */
    private function checkAssigneeUpdatePreviousActivityCompleted(ActivityHelper $activityHandler, \SugarBean $activity, \DRI_SubWorkflow $stage)
    {
        if ($activityHandler->hasParent($activity)) {
            $next = $activityHandler->getNextChildActivity($activity);
        } else {
            $next = $stage->getJourney()->getNextActivity($stage, $activity);
        }
        if ($next) {
            $handler = ActivityHandlerFactory::factory($next->module_dir);
            $nextActivityTemplate = $handler->getActivityTemplate($next);
            $nextActivityStage = $handler->getStage($next);

            //setAssignmentSummary, flag for make sure send a single compile assignment summary email
            $next->setAssignmentSummary = true;
            $handler->updateAssigneePreviousActivityCompleted($nextActivityTemplate, $nextActivityStage, $next);
        }
    }

    /**
     * Checks if activity's assignee can be reassigned or not
     *
     * @param ActivityHelper $activityHandler
     * @param \SugarBean $activity
     * @param \DRI_SubWorkflow $stage
     * @param string $previousParentAssignedUserId
     */
    private function checkActivityValidForReassigning(ActivityHelper $activityHandler, \SugarBean $activity, \DRI_SubWorkflow $stage, $previousParentAssignedUserId)
    {
        if ($activityHandler->isCompleted($activity)) {
            $this->checkAssigneeUpdatePreviousActivityCompleted($activityHandler, $activity, $stage);
            $this->checkAssigneeUpdateSpecificActivityCompleted($activity, $stage);
        }

        if (!$activityHandler->hasActivityTemplate($activity) ||
            ($activityHandler->isCompleted($activity) || $activityHandler->isNotApplicable($activity))) {
            return;
        }

        $activityTemplate = $activityHandler->getActivityTemplate($activity);
        if ($activityTemplate->getTargetAssignee($stage) == \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT) {
            $this->updateTargetAssigneeActivity($stage, $activity, $activityTemplate, $previousParentAssignedUserId);
        }
    }

    /**
     * Reassigns assignee for specific child activity
     * having target assignee as parent assignee
     *
     * @param \SugarBean $child
     * @param \DRI_SubWorkflow $stage
     * @param string $previousParentAssignedUserId
     */
    private function autoReassignAssigneeSpecificChildActivity(\SugarBean $child, \DRI_SubWorkflow $stage, $previousParentAssignedUserId)
    {
        $handler = ActivityHandlerFactory::factory($child->module_dir);
        $this->checkActivityValidForReassigning($handler, $child, $stage, $previousParentAssignedUserId);
    }

    /**
     * Reassigns assignee for specific activity
     * having target assignee as parent assignee
     *
     * @param \SugarBean $activity
     * @param \DRI_SubWorkflow $stage
     * @param string $previousParentAssignedUserId
     */
    private function autoReassignAssigneeSpecificActivity(\SugarBean $activity, \DRI_SubWorkflow $stage, $previousParentAssignedUserId)
    {
        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

        $this->checkActivityValidForReassigning($activityHandler, $activity, $stage, $previousParentAssignedUserId);

        if ($activityHandler->isParent($activity)) {
            foreach ($activityHandler->getChildren($activity) as $child) {
                $this->autoReassignAssigneeSpecificChildActivity($child, $stage, $previousParentAssignedUserId);
            }
        }
    }

    /**
     * Filters activities and sub-activities for specific stage
     * having target assignee as parent assignee
     *
     * @param \DRI_SubWorkflow $stage
     * @param string $previousParentAssignedUserId
     */
    private function autoReassignAssigneeSpecificStage(\DRI_SubWorkflow $stage, $previousParentAssignedUserId)
    {
        $activities = $stage->getActivities();
        foreach ($activities as $activity) {
            $this->autoReassignAssigneeSpecificActivity($activity, $stage, $previousParentAssignedUserId);
        }
    }

    /**
     * Filters activities and sub-activities
     * having target assignee as parent assignee
     *
     * @param array $stages
     * @param string $previousParentAssignedUserId
     */
    private function autoReassignAssigneeForActivities($stages, $previousParentAssignedUserId)
    {
        foreach ($stages as $stage) {
            $this->autoReassignAssigneeSpecificStage($stage, $previousParentAssignedUserId);
        }
    }

    /**
     * Reassigns assignee for stages for specific journey
     * having target assignee as parent assignee
     *
     * @param \DRI_Workflow $journey
     * @param string $previousParentAssignedUserId
     * @return array
     */
    private function autoReassignAssigneeSpecificJourney(\DRI_Workflow $journey, $previousParentAssignedUserId)
    {
        $stageBeans = [];
        $stages = $journey->getStages();
        foreach ($stages as $stage) {
            if ($stage->getTargetAssignee() == \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT) {
                $stage->assigned_user_id = $stage->getTargetAssigneeId();
                $stage->team_id = $stage->getTargetTeamId();
                $stage->team_set_id = $stage->getTargetTeamSetId();
                $stage->save();
            }
            array_push($stageBeans, $stage);
        }
        return $stageBeans;
    }

    /**
     * Reassigns assignee for stages
     *
     * @param array $journeys
     * @param string $previousParentAssignedUserId
     * @return array
     */
    private function autoReassignAssigneeForStages($journeys, $previousParentAssignedUserId)
    {
        $stageBeans = [];

        foreach ($journeys as $journey) {
            $journeySpecificStageBeans = $this->autoReassignAssigneeSpecificJourney($journey, $previousParentAssignedUserId);
            $stageBeans = array_merge($stageBeans, $journeySpecificStageBeans);
        }
        return $stageBeans;
    }

    /**
     * Reassigns assignee for journeys
     *
     * @param \SugarBean $parent
     * @return array
     */
    private function autoReassignAssigneeForJourneys(\SugarBean $parent)
    {
        $journeys = [];

        if (!$parent->load_relationship('dri_workflows')) {
            return;
        }
        foreach ($parent->dri_workflows->getBeans() as $journey) {
            $journeyTemplate = $journey->getTemplate();
            if (!$journeyTemplate->update_assignees) {
                return $journeys;
            }
            if ($journey->target_assignee == \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT) {
                $journey->assigned_user_id = $journey->getTargetAssigneeId();
                $journey->team_id = $journey->getTargetTeamId();
                $journey->team_set_id = $journey->getTargetTeamSetId();
                $journey->save(true);
            }
            array_push($journeys, $journey);
        }
        return $journeys;
    }

    /**
     * This logic hook auto reassigns assignee when parent assignee is changed
     *
     * @param \SugarBean $parent
     */
    public function autoReassignActivities(\SugarBean $parent, $event, $arguments)
    {
        if (!$arguments['isUpdate']) {
            return;
        }

        if ((array_key_exists('dataChanges', $arguments) && array_key_exists('assigned_user_id', $arguments['dataChanges'])) || (array_key_exists('stateChanges', $arguments) && array_key_exists('assigned_user_id', $arguments['stateChanges']))) {
            $assignedUserBeforeValue = GeneralHooks::getBeanValueFromArgs($arguments, 'assigned_user_id', 'before');

            $journeys = $this->autoReassignAssigneeForJourneys($parent);
            if (empty($journeys)) {
                return;
            }
            $stages = $this->autoReassignAssigneeForStages($journeys, $assignedUserBeforeValue);
            $this->autoReassignAssigneeForActivities($stages, $assignedUserBeforeValue);

            $journey = current($journeys);
            $journey->sendAssignmentNotifications();
        }
    }

    /**
     * This logic hook gets triggered after a lead is converted, if the lead is related to an account,
     * all related journeys will be related to the converted account
     *
     * @param \Lead $lead
     * @param array $arguments
     * @throws \SugarApiException
     */
    private function convertLead(\Lead $lead, $arguments)
    {
        $map = [];
        $j = new \DRI_Workflow();

        foreach ($j->getParentDefinitions() as $def) {
            if ($def['module'] !== 'Leads') {
                $map[$def['module']] = !empty($def[\DRI_Workflow::PARENT_VARDEF_KEY]['lead_id_name'])
                    ? $def[\DRI_Workflow::PARENT_VARDEF_KEY]['lead_id_name']
                    : $def['id_name'];
            }
        }

        try {
            if (empty($lead->converted) || !empty($arguments['stateChanges']['converted']['before'])) {
                return;
            }

            $lead->load_relationship('dri_workflows');

            foreach ($lead->dri_workflows->getBeans() as $cycle) {
                /** @var \DRI_Workflow $cycle */
                $modules = $cycle->getAvailableModules();

                foreach ($modules as $module) {
                    if (!isset($map[$module])) {
                        continue;
                    }

                    if (!empty($lead->{$map[$module]})) {
                        $target = \BeanFactory::retrieveBean($module, $lead->{$map[$module]});
                        $this->loadRelationship($target);
                        $target->dri_workflows->add($cycle);
                    }
                }

                $cycle->retrieve();
                $parent = $cycle->getParent();
                foreach ($cycle->getStages() as $stage) {
                    foreach ($stage->getActivities() as $activity) {
                        $handler = ActivityHandlerFactory::factory($activity->module_dir);
                        $handler->populateFromParent($activity, $parent);
                        $activity->save($activity->assigned_user_id !== $GLOBALS['current_user']->id);
                        $handler->relateToParent($activity, $parent);
                    }
                }
            }
        } catch (\SugarApiException $e) {
            if ('invalid_license' !== $e->errorLabel) {
                throw $e;
            }
        }
    }

    /**
     * @param \SugarBean $bean
     * @throws \SugarApiException
     */
    private function loadRelationship(\SugarBean $bean)
    {
        $bean->load_relationship('dri_workflows');

        if (!($bean->dri_workflows instanceof \Link2)) {
            throw new \SugarApiException();
        }
    }

    /**
     * Unset Journey template ID (dri_workflow_template_id) from the bean after Journey has been added so Journey
     * does not keep adding as it should only be added only once.
     * Bug Fix: CJ-91
     *
     * @param \SugarBean $bean The bean that was changed
     */
    private function unsetJourneyTemplateID($bean)
    {
        $tableName = $bean->getTableName();

        $qb = \DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($tableName)
            ->set('dri_workflow_template_id', 'NULL')
            ->where($qb->expr()->eq('id', $qb->expr()->literal($bean->id)));
        $qb->executeQuery();

        //when multiple BPMs were being triggered for this bean record this field remained populated
        //so we are unsetting it from bean as well after setting it empty in db.
        $bean->dri_workflow_template_id = null;
    }
}
