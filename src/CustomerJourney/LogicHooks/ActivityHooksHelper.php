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
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\TargetResolver as TargetResolver;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\Email as CJFormsEmail;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CustomerJourneyException;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate\AllowActivityBy as AllowActivityBy;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey as Journey;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityDatesHelper;

/**
 * This class contains the actual logic for the
 * activity modules that is called from the LogicHooks file
 */
class ActivityHooksHelper
{
    /**
     * @var bool
     */
    private static $inReorderActivities = false;

    /**
     * @var bool
     */
    private static $internalSave = false;

    /**
     * @var array
     */
    private static $disabled = [];

    /**
     * @var bool
     */
    private static $allowedByError = false;

    /**
     * @param string $id
     */
    public static function disable($id)
    {
        self::$disabled[$id] = true;
    }

    /**
     * @param string $id
     * @return void
     */
    public static function enable($id)
    {
        unset(self::$disabled[$id]);
    }

    /**
     * @param boolean $internalSave
     * @return void
     */
    public static function setInternalSave($internalSave)
    {
        self::$internalSave = $internalSave;
    }

    /**
     *
     * Stores the fetched row on the bean before save to
     * make it available for after save logic hooks
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function saveFetchedRow(\SugarBean $activity)
    {
        $activity->fetched_row_before = $activity->fetched_row;
    }

    /**
     * Set the sort order of activity
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function setActualSortOrder(\SugarBean $activity)
    {
        try {
            if (self::$inReorderActivities) {
                $GLOBALS['log']->error('setActualSortOrder: $inReorderActivities');
                return;
            }

            if (self::$internalSave) {
                $GLOBALS['log']->error('setActualSortOrder: $internalSave');
                return;
            }

            $handler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$handler->isStageActivity($activity)) {
                $GLOBALS['log']->debug('Sugar Automate: ActivityHooksHelper::setActualSortOrder : Actvity:' . $activity->id . 'is not a stage\'s activity');
                return;
            }

            $handler->setActualSortOrder($activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        } catch (CustomerJourneyException\NotFoundException $e) {
            // Previously, For DRI_Workflows_Exception_IdNotFound we throw 'Smart Guide: journey not found'
            // and for 'DRI_SubWorkflows_Exception_IdNotFound' we throw Smart Guide: stage not found
            // this error gets thrown when a complete journey/stage is deleted since the related stage has already
            // been deleted when updating the activities relationships, it's not a real error so we can just skip it
            $GLOBALS['log']->error('Smart Guide: Smart Guide/stage not found - ' . $activity->id);
        }
    }

    /**
     * Some logic that should run before status change
     *
     * @param \SugarBean $activity
     * @return void
     * @throws SugarApiExceptionError
     */
    public function beforeStatusChange(\SugarBean $activity)
    {
        try {
            if (self::$inReorderActivities) {
                $GLOBALS['log']->error('beforeStatusChange: $inReorderActivities');
                return;
            }

            if (self::$internalSave) {
                $GLOBALS['log']->error('beforeStatusChange: $internalSave');
                return;
            }

            $handler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$handler->isStageActivity($activity)) {
                $GLOBALS['log']->debug('Sugar Automate: ActivityHooksHelper::beforeStatusChange : Actvity:' . $activity->id . 'is not a stage\'s activity');
                return;
            }

            if ($this->isNew($activity)) {
                $GLOBALS['log']->error('beforeStatusChange: isNew');
                return;
            }

            if (!$handler->haveChangedStatus($activity) && $handler->haveChangedPoints($activity)) {
                $GLOBALS['log']->error('beforeStatusChange: haveChangedStatus');
                return;
            }

            $this->beforeCompleted($handler, $activity);
            $this->beforeNotApplicable($handler, $activity);
            $this->beforeInProgress($handler, $activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        } catch (CustomerJourneyException\NotFoundException $e) {
            // Previously, For DRI_Workflows_Exception_IdNotFound we throw 'Smart Guide: journey not found'
            // and for 'DRI_SubWorkflows_Exception_IdNotFound' we throw Smart Guide: stage not found
            // this error gets thrown when a complete journey/stage is deleted since the related stage has already
            // been deleted when updating the activities relationships, it's not a real error so we can just skip it
            $GLOBALS['log']->error('Smart Guide: Smart Guide/stage not found - ' . $activity->id);
        }
    }

    /**
     * Re saves the related DRI_SubWorkflow when the task's status gets updated
     *
     * Also triggers the completed events if applicable
     *
     * @param \SugarBean $activity
     * @return void
     * @throws ParentCustomerJourneyException\NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     * @throws CustomerJourneyException\NotFoundException
     */
    public function resaveIfChanged(\SugarBean $activity)
    {
        try {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);
            $this->calculateMomentumForParent($handler, $activity);

            if (self::$inReorderActivities) {
                return;
            }

            if (self::$internalSave) {
                return;
            }

            if (!$handler->isStageActivity($activity)) {
                return;
            }

            if ($this->isNew($activity)) {
                return;
            }

            // do not resave Journey when parent have been changed
            if (!$handler->haveChangedStatus($activity) && $handler->haveChangedParent($activity)) {
                return;
            }

            /** @var \DRI_Workflow $journey */
            /** @var \DRI_SubWorkflow $stage */
            [$journey, $stage] = $this->getJourney($activity);

            // load the complete journey and make sure that the
            // saved activity is inserted to the objects loaded into memory
            $journey->load();
            $journey->insertActivity($activity);

            $this->afterCompleted($journey, $stage, $handler, $activity);
            $this->afterNotApplicable($handler, $activity);
            $this->afterInProgress($handler, $activity);

            $this->updateChildrenCache($handler, $activity);

            $this->calculateMomentumForParent($handler, $activity);
            $this->doResave($journey, $handler, $activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        } catch (CustomerJourneyException\NotFoundException $e) {
            // Previously, For DRI_Workflows_Exception_IdNotFound we throw 'Smart Guide: journey not found'
            // and for 'DRI_SubWorkflows_Exception_IdNotFound' we throw Smart Guide: stage not found
            // this error gets thrown when a complete journey/stage is deleted since the related stage has already
            // been deleted when updating the activities relationships, it's not a real error so we can just skip it
            $GLOBALS['log']->error('Smart Guide: Smart Guide/stage not found - ' . $activity->id);
        }
    }

    /**
     * Update Static children array in case the activity that is being updated here is a child
     * @param $handler
     * @param $activity
     * @return void
     */
    private function updateChildrenCache($handler, $activity)
    {
        if (!empty($handler) && !empty($activity) &&
            !empty($handler->getParent($activity)) &&
            !empty(!empty($handler->getParent($activity)))) {
            $parentId = $handler->getParent($activity)->id;
            if (!empty($parentId) && !empty($handler->childActivityHelper::$children[$parentId])) {
                foreach ($handler->childActivityHelper::$children[$parentId] as &$child) {
                    if ($child->id == $activity->id) {
                        $child = $activity;
                    }
                }
            }
        }
    }

    /**
     * Automatically update or create a record based on the related sugar action
     *
     * @param \SugarBean $activity
     * @param string $event
     * @param array $arguments
     * @return void
     */
    public function checkRelatedSugarAction(\SugarBean $activity, $event, array $arguments)
    {
        if (!$arguments['isUpdate']) {
            return;
        }
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        if (!$handler->isStageActivity($activity) || !$handler->hasActivityTemplate($activity)) {
            return;
        }
        if ($handler->haveChangedStatus($activity)) {
            $RSABeans = $handler->getForms($activity);

            foreach ($RSABeans as $RSABean) {
                $this->performRelatedSugarAction($activity, $handler, $RSABean);
            }
        }
    }

    /**
     * Start Next Smart Guide if task completed on Smart Guide template related to task
     *
     * Also triggers the completed events if applicable
     *
     * @param \SugarBean $activity
     * @param string $event
     * @param array $arguments
     * @return void
     * @throws CustomerJourneyException\NotFoundException
     * @throws ParentCustomerJourneyException\NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     * @throws Exception
     */
    public function startNextJourneyIfCompleted(\SugarBean $activity, $event, array $arguments)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        $statusAfterValue = GeneralHooks::getBeanValueFromArgs($arguments, 'status', 'after');

        if (!empty($activity->start_next_journey_id) && $statusAfterValue === $handler->getCompletedStatus($activity)) {
            try {
                if ($this->isNew($activity)) {
                    return;
                }
                $template = \BeanFactory::retrieveBean('DRI_Workflow_Templates', $activity->start_next_journey_id);
                if (!empty($template->id) && !empty($template->available_modules)) {
                    $availableModules = unencodeMultienum($template->available_modules);
                    if (!in_array($activity->parent_type, $availableModules)) {
                        throw new \SugarApiExceptionInvalidParameter("$activity->parent_type is not in available modules of template");
                    }
                }
                $parent = \BeanFactory::retrieveBean($activity->parent_type, $activity->parent_id);
                if (!empty($parent)) {
                    \DRI_Workflow::start($parent, $activity->start_next_journey_id);
                }
            } catch (CustomerJourneyException\InvalidLicenseException $e) {
                // omit errors when license is not valid or user missing access
            } catch (CustomerJourneyException\NotFoundException|\SugarApiExceptionError $e) {
                throw new \SugarApiExceptionInvalidParameter($e->getMessage());
            } catch (CustomerJourneyException\ParentNotFoundException $e) {
                throw new \SugarApiExceptionInvalidParameter("Start next journey - $e->getMessage()");
            }
        }
    }

    /**
     * Logic after status has been changed to completed
     *
     * @param \DRI_Workflow $journey
     * @param \DRI_SubWorkflow $stage
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function afterCompleted(\DRI_Workflow $journey, \DRI_SubWorkflow $stage, ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isCompleted($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity after completed - ' . $activity->id);
            $handler->afterCompleted($journey, $stage, $activity);
            $this->activeUser();
        }
    }

    /**
     * Logic before status is going to change to completed
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function beforeCompleted(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isCompleted($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity before completed - ' . $activity->id);
            $handler->beforeCompleted($activity);
        }
    }

    /**
     * Logic after status is going to change to InProgress
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function afterInProgress(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isInProgress($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity after in progress - ' . $activity->id);
            $handler->afterInProgress($activity);
            $this->activeUser();
        }
    }

    /**
     * Logic before status is going to change to InProgress
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function beforeInProgress(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isInProgress($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity before in progress - ' . $activity->id);
            $handler->beforeInProgress($activity);
        }
    }

    /**
     * Logic after status is going to change to NotApplicable
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function afterNotApplicable(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isNotApplicable($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity after not applicable - ' . $activity->id);
            $handler->afterNotApplicable($activity);
            $this->activeUser();
        }
    }

    /**
     * Logic before status is going to change to NotApplicable
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    private function beforeNotApplicable(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->haveChangedStatus($activity) && $handler->isNotApplicable($activity)) {
            $GLOBALS['log']->debug('Smart Guide: activity before not applicable - ' . $activity->id);
            $handler->beforeNotApplicable($activity);
        }
    }

    /**
     * Resaves Journey after Activity gets deleted
     *
     * @param \SugarBean $activity
     * @return void
     * @throws ParentCustomerJourneyException\NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     * @throws CustomerJourneyException\NotFoundException
     */
    public function resave(\SugarBean $activity)
    {
        try {
            if (isset(self::$disabled[$activity->id])) {
                return;
            }

            $handler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$handler->isStageActivity($activity)) {
                return;
            }

            [$journey, $stage] = $this->getJourney($activity);
            $this->doResave($journey, $handler, $activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        } catch (CustomerJourneyException\NotFoundException $e) {
            // Previously, For DRI_Workflows_Exception_IdNotFound we throw 'Smart Guide: journey not found'
            // and for 'DRI_SubWorkflows_Exception_IdNotFound' we throw Smart Guide: stage not found
            // this error gets thrown when a complete journey/stage is deleted since the related stage has already
            // been deleted when updating the activities relationships, it's not a real error so we can just skip it
            $GLOBALS['log']->error('Smart Guide: Smart Guide/stage not found - ' . $activity->id);
        }
    }

    /**
     * To remove the children of Activity
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function removeChildren(\SugarBean $activity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        if ($handler->isStageActivity($activity) && $handler->isParent($activity)) {
            $children = $handler->getChildren($activity);

            foreach ($children as $child) {
                $child->mark_deleted($child->id);
            }
        }
    }

    /**
     * Logic to run before delete of Activity
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function beforeDelete(\SugarBean $activity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        if ($handler->isStageActivity($activity)) {
            $handler->beforeDelete($activity);
        }
    }

    /**
     * Logic to run after delete of Activity
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function afterDelete(\SugarBean $activity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        if ($handler->isStageActivity($activity)) {
            $handler->afterDelete($activity);
        }

        $this->calculateMomentumForParent($handler, $activity);
    }

    /**
     * Re saves the parent journey
     *
     * @param DRI_Workflow $journey
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     * @throws ParentCustomerJourneyException\NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     * @throws CustomerJourneyException\NotFoundException
     */
    private function doResave(\DRI_Workflow $journey, ActivityHelper $handler, \SugarBean $activity)
    {
        try {
            if ($handler->hasParent($activity)) {
                $parent = $handler->getParent($activity);
                if ($parent) {
                    if (!empty($activity->ignore_blocked_by) && $activity->ignore_blocked_by) {
                        $parent->ignore_blocked_by = true;
                    }

                    $handler = ActivityHandlerFactory::factory($parent->module_dir);

                    $handler->insertChild($parent, $activity);
                    $handler->calculate($parent);
                    $handler->calculateStatus($parent);

                    if ($handler->isStatusChanged($parent)) {
                        (new ActivityDatesHelper())->setActivityStartAndEndDates($parent, $parent->status);
                    }

                    if (($handler->isPointsChanged($parent) ||
                        $handler->isStatusChanged($parent) ||
                        $handler->isScoreChanged($parent) ||
                        $handler->isProgressChanged($parent) ||
                        !$handler->isParent($parent))) {
                        $parent->is_cj_parent_activity = true;
                        $parent->save();
                        $this->setActivityCache($journey, $parent);
                    }

                    if (empty($handler->getChildren($parent))) {
                        $parent->is_cj_parent_activity = false;
                        $parent->save();
                        $this->setActivityCache($journey, $parent);
                    }
                }
            }

            if (!isset(self::$disabled[$journey->id])) {
                $GLOBALS['log']->debug('Smart Guide: saving Smart Guide - ' . $activity->id);
                $journey->save();
            }
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        } catch (CustomerJourneyException\NotFoundException $e) {
            // Previously, For DRI_Workflows_Exception_IdNotFound we throw 'Smart Guide: journey not found'
            // and for 'DRI_SubWorkflows_Exception_IdNotFound' we throw Smart Guide: stage not found
            // this error gets thrown when a complete journey/stage is deleted since the related stage has already
            // been deleted when updating the activities relationships, it's not a real error so we can just skip it
            $GLOBALS['log']->error('Smart Guide: Smart Guide/stage not found - ' . $activity->id);
        }
    }

    /**
     * Set Activity Cache
     *
     * @param DRI_Workflow $journey
     * @param \SugarBean $activity
     */
    public function setActivityCache(\DRI_Workflow $journey, \SugarBean $activity)
    {
        // Check if the journey is not disabled and has stages
        $stages = $journey->getStages();
        if (!empty($stages)) {
            $stages = array_column($stages, null, 'id');
            if (isset($stages[$activity->dri_subworkflow_id])) {
                // Update the activity in the cache
                $stages[$activity->dri_subworkflow_id]->setActivity($activity);
            }
        }
    }
    
    /**
     * Return the Smart Guide/stage of activity
     *
     * @param \SugarBean $activity
     * @return array
     * @throws CustomerJourneyException\NotFoundException
     */
    private function getJourney(\SugarBean $activity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        $stage = $handler->getStage($activity);
        $GLOBALS['log']->debug('Smart Guide: stage found - ' . $activity->id);

        $journey = $stage->getJourney();
        $GLOBALS['log']->debug('Smart Guide: journey found - ' . $activity->id);

        return [$journey, $stage];
    }

    /**
     * Calculate the Momentum, progress, state etc on activity
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function calculate(\SugarBean $activity)
    {
        try {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$handler->isStageActivity($activity)) {
                return;
            }

            $handler->calculate($activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        }
    }

    /**
     * Calculate the momentum
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function calculateMomentum(\SugarBean $activity)
    {
        try {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$handler->isStageActivity($activity)) {
                return;
            }

            if (!$handler->hasMomentum($activity)) {
                return;
            }

            if (!$handler->hasActivityTemplate($activity)) {
                return;
            }

            $handler->calculateMomentum($activity);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        }
    }

    /**
     * Calculate the Momentum of Parent Activity whenever Child Activity Momentum
     * will change
     *
     * @param ActivityHelper $handler
     * @param \SugarBean $activity
     * @return void
     */
    public function calculateMomentumForParent(ActivityHelper $handler, \SugarBean $activity)
    {
        if ($handler->hasParent($activity)) { //Child Activity
            //Need to update the parent momentum as well
            $parent = $handler->getParent($activity);

            if ($parent) {
                $parent->cj_momentum_ratio = 0;
                $parent->cj_momentum_points = 0;
                $parent->cj_momentum_score = 0;
                foreach ($handler->getChildren($parent) as $child) {
                    if (!empty($child->cj_momentum_points)) {
                        $parent->cj_momentum_score += $child->cj_momentum_score;
                        $parent->cj_momentum_points += $child->cj_momentum_points;
                    }
                }
                $parent->cj_momentum_ratio = $parent->cj_momentum_points > 0 ? $parent->cj_momentum_score / $parent->cj_momentum_points : 1;
                $this->updateParentMomentum($parent, $parent->getTableName());
            }
        }
    }

    /**
     * Update the Parent Momentum with the updated data
     *
     * @param \SugarBean $activity
     * @param string $table_name
     * @return void
     */
    public function updateParentMomentum(\SugarBean $activity, $table_name)
    {
        global $db;

        $sql = <<<SQL
                    UPDATE
                        {$table_name}
                    SET
                        cj_momentum_ratio = ?,
                        cj_momentum_points = ?,
                        cj_momentum_score = ?
                    WHERE
                        id = ?
SQL;
        $db->getConnection()->executeUpdate($sql, [$activity->cj_momentum_ratio, $activity->cj_momentum_points, $activity->cj_momentum_score, $activity->id]);
    }

    /**
     * Reorder the acitivites
     *
     * @param \SugarBean $bean
     * @return void
     * @throws SugarQueryException
     */
    public function reorder(\SugarBean $bean)
    {
        try {
            if (self::$inReorderActivities) {
                return;
            }

            if (self::$internalSave) {
                return;
            }

            $activityHandler = ActivityHandlerFactory::factory($bean->module_dir);

            if (!$activityHandler->isStageActivity($bean)) {
                return;
            }

            if (!$this->orderExistOnStage($bean)) {
                return;
            }

            $stage = $activityHandler->getStage($bean);
            $order = (int)$activityHandler->getSortOrder($bean);

            self::$inReorderActivities = true;

            $i = -1;

            foreach ($stage->getActivities() as $activity) {
                $handler = ActivityHandlerFactory::factory($activity->module_dir);
                $sortOrder = (int)$handler->getSortOrder($activity);

                // start sequence check on the duplicated index
                if ($sortOrder === $order) {
                    $i = $order;
                } elseif ($sortOrder > $order) {
                    $i++;
                }

                if ($sortOrder >= $order && $activity->id !== $bean->id && $sortOrder === $i) {
                    $handler->increaseSortOrder($activity);
                    $activity->save();
                }
            }

            self::$inReorderActivities = false;
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        }
    }

    /**
     * This will check if Sort Order agianst of activity
     * against stage is there or not
     *
     * @param \SugarBean $activity
     * @return boolean
     * @throws SugarQueryException
     */
    private function orderExistOnStage(\SugarBean $activity)
    {
        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

        foreach (ActivityHandlerFactory::all() as $handler) {
            if ($handler->orderExistOnStage($activityHandler->getStageId($activity), $activityHandler->getSortOrder($activity), $activity->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the bean in its current state is new
     *
     * @param \SugarBean $activity
     * @return boolean
     */
    protected function isNew(\SugarBean $activity)
    {
        return empty($activity->id) || (!empty($activity->id) && !empty($activity->new_with_id));
    }

    /**
     * validates the activity
     *
     * @param \SugarBean $activity
     * @return void
     * @throws CustomerJourneyException\NotFoundException
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function validate(\SugarBean $activity)
    {
        try {
            if (self::$inReorderActivities) {
                return;
            }

            if (self::$internalSave) {
                return;
            }

            $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

            if (!$activityHandler->isStageActivity($activity)) {
                return;
            }

            $GLOBALS['log']->info('Smart Guide: validating activity - ' . $activity->id);
            $this->validateUniqueName($activity, $activityHandler);
            $this->validateDependency($activity, $activityHandler);
            $this->validateAllowedBy($activity, $activityHandler);
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        }
    }

    /**
     * Set the active user in the tracker
     *
     * @return void
     */
    protected function activeUser()
    {
        Journey\Tracker::activeUser($GLOBALS['current_user']);
    }

    /**
     * Validate the dependencies
     *
     * @param \SugarBean $activity
     * @param ActivityHelper $activityHandler
     * @return void
     * @throws CustomerJourneyException\NotFoundException
     * @throws \SugarApiExceptionInvalidParameter
     */
    protected function validateDependency(\SugarBean $activity, ActivityHelper $activityHandler)
    {
        // only perform this check if the activity is blocked
        // and the status is changed to completed/in progress/not applicable.
        // we should do the simple check if the status is changed
        // first to not cause an performance impact for non status updates
        if (!$activityHandler->haveChangedStatus($activity) ||
            (!$activityHandler->isCompleted($activity) &&
                !$activityHandler->isInProgress($activity) &&
                !$activityHandler->isNotApplicable($activity))
        ) {
            return;
        }

        if (isset($activity->ignore_blocked_by) && $activity->ignore_blocked_by === true) {
            return;
        }

        if ($activityHandler->isBlocked($activity)) {
            $stage = $activityHandler->getStage($activity);
            $journey = $stage->getJourney();

            $names = [];
            foreach ($activityHandler->getBlockedBy($activity) as $blockedBy) {
                if ($activityHandler->isCancelled($blockedBy)) {
                    return;
                }
                $names[] = $blockedBy->name;
            }

            throw new \SugarApiExceptionInvalidParameter(
                sprintf(
                    'This record cannot be completed because it is blocked by an activity in a Smart Guide. Please complete "%s" in the "%s" journey.',
                    implode(', ', $names),
                    $journey->name
                )
            );
        }

        if ($activityHandler->isBlockedByStage($activity)) {
            $stage = $activityHandler->getStage($activity);
            $journey = $stage->getJourney();

            $names = [];
            foreach ($activityHandler->getNotCompletedBlockedByStages($activity) as $blockedByStage) {
                if ($blockedByStage->state == \DRI_SubWorkflow::STATE_CANCELLED) {
                    return;
                }
                $names[] = $blockedByStage->name;
            }

            throw new \SugarApiExceptionInvalidParameter(
                sprintf(
                    'This record cannot be completed because it is blocked by a stage in a Smart Guide. Please complete "%s" in the "%s" journey.',
                    implode(', ', $names),
                    $journey->name
                )
            );
        }
    }

    /**
     * Check either Current User has permission to complete this activity or not
     *
     * @param \SugarBean $activity
     * @param ActivityHelper $activityHandler
     * @return void
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function validateAllowedBy(\SugarBean $activity, ActivityHelper $activityHandler)
    {
        if (isset($activity->is_cj_parent_activity) && isTruthy($activity->is_cj_parent_activity) &&
        !$this->hasChildInProgress($activity, $activityHandler)) {
            return ;
        }

        if ($activityHandler->haveChangedStatus($activity) && $activityHandler->isCompleted($activity)) {
            if (!empty($activity->cj_allow_activity_by) && !$GLOBALS['current_user']->is_admin) {
                $allowFlag = AllowActivityBy::isActivityAllow($activity, $activity->cj_allow_activity_by);
                if (!$allowFlag) {
                    if (!isset($activity->processing_smart_guide)) {
                        throw new \SugarApiExceptionInvalidParameter(
                            translate('LBL_CURRENT_USER_UNABLE_TO_COMPLETE_STATUS', 'DRI_Workflow_Task_Templates')
                        );
                    } else {
                        self::$allowedByError = true;
                    }
                }
            }
        }
    }

    /**
     * Checks if at least a child is in progress
     *
     * @param \SugarBean $activity
     * @param ActivityHelper $activityHandler
     * @return bool
     */
    protected function hasChildInProgress(\SugarBean $activity, ActivityHelper $activityHandler) : bool
    {
        $children = $activityHandler->childActivityHelper->getChildren($activity);

        foreach ($children as $child) {
            if (!$activityHandler->isCompleted($child) && !$activityHandler->isNotApplicable($child)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate uniqueness of Name for activity
     *
     * @param \SugarBean $activity
     * @param ActivityHelper $activityHandler
     * @return void
     * @throws \SugarApiExceptionInvalidParameter
     */
    protected function validateUniqueName(\SugarBean $activity, ActivityHelper $activityHandler)
    {
        try {
            $activityHandler->getByStageIdAndName($activityHandler->getStageId($activity), $activity->name, $activity->id);

            throw new \SugarApiExceptionInvalidParameter(sprintf('Activity with name %s does already exist', $activity->name));
        } catch (\SugarApiExceptionNotFound $e) {
        }
    }

    /**
     * Check and perform the RSA on Activity
     *
     * @param \SugarBean $activity
     * @param ActivityHelper $activityHandler
     * @param \SugarBean $RSABean
     * @return void
     * @throws \SugarApiExceptionInvalidParameter
     * @throws \SugarApiExceptionError
     */
    private function performRelatedSugarAction(\SugarBean $activity, ActivityHelper $handler, \SugarBean $RSABean)
    {
        if (!$this->preRequisitesRSA($RSABean)) {
            return;
        }

        if (($handler->getCompletedStatus($activity) === $activity->status && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_COMPLETED) ||
            ($handler->isInProgress($activity) && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_IN_PROGRESS) ||
            ($handler->isNotApplicable($activity) && $RSABean->trigger_event === \CJ_Form::TRIGGER_EVENT_NOT_APPLICABLE)
        ) {
            $stage = $handler->getStage($activity);
            $finder = new TargetResolver($RSABean);

            try {
                $response = $finder->resolve($stage, $activity);
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

            if (empty($parent->id) ||
                (
                    $RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD &&
                    $RSABean->action_trigger_type === \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE &&
                    empty($target->id)
                )
            ) {
                if (!$RSABean->ignore_errors) {
                    throw new \SugarApiExceptionInvalidParameter(translate('LBL_COULD_NOT_FIND_RELATED_RECORD', 'CJ_Forms'));
                }
                return;
            }

            $this->createActionRelatedSugarAction($RSABean, $module, $activity, $linkName, $parent);
            $this->updateActionRelatedSugarAction($RSABean, $module, $activity, $allBeans);
        }
    }

    /**
     * Pre-requisties on activity RSA
     *
     * @param \SugarBean $RSABean
     * @return boolean
     */
    private function preRequisitesRSA($RSABean)
    {
        if (!($RSABean->action_type === \CJ_Form::ACTION_TYPE_CREATE_RECORD || $RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD)) {
            return false;
        }

        if ((
            $RSABean->action_type === \CJ_Form::ACTION_TYPE_CREATE_RECORD &&
                $RSABean->action_trigger_type != \CJ_Form::ACTION_TRIGGER_AUTOMATIC_CREATE
        ) ||
            (
                $RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD &&
                $RSABean->action_trigger_type != \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE
            )
        ) {
            return false;
        }
        return true;
    }

    /**
     * Create action of RSA against activity
     *
     * @param \SugarBean $RSABean
     * @param string $module
     * @param \SugarBean $activity
     * @param string linkName
     * @param \SugarBean $parent
     * @return void
     */
    private function createActionRelatedSugarAction($RSABean, $module, $activity, $linkName, $parent)
    {
        if ($RSABean->action_type === \CJ_Form::ACTION_TYPE_CREATE_RECORD && $RSABean->action_trigger_type === \CJ_Form::ACTION_TRIGGER_AUTOMATIC_CREATE) {
            $child = \BeanFactory::getBean($module, null);

            if (!empty($activity->parent_id) && !empty($activity->parent_type) && !empty($linkName)) {
                $child->parent_id = $activity->parent_id;
                $child->parent_type = $activity->parent_type;
            }
            \CJ_Form::setTargetValues($child, $RSABean);

            if ($module === 'Emails' && !empty($RSABean->email_templates_id)) {
                (new CJFormsEmail())->sendEmail($child, $RSABean, $activity);
            }
            $child->save();

            if ($parent->load_relationship($linkName)) {
                $parent->$linkName->add($child->id);
            }
        }
    }

    /**
     * Update action of RSA against activity
     *
     * @param \SugarBean $RSABean
     * @param string $module
     * @param \SugarBean $activity
     * @param array $allBeans
     * @return void
     */
    private function updateActionRelatedSugarAction($RSABean, $module, $activity, $allBeans)
    {
        if ($RSABean->action_type === \CJ_Form::ACTION_TYPE_UPDATE_RECORD && $RSABean->action_trigger_type === \CJ_Form::ACTION_TRIGGER_AUTOMATIC_UPDATE) {
            foreach ($allBeans as $singleBean) {
                $child = \BeanFactory::getBean($module, $singleBean->id);

                if ($activity->id === $singleBean->id) {
                    $singleBean->processed = true;
                }

                \CJ_Form::setTargetValues($child, $RSABean);
                $child->save();
            }
        }
    }

    /**
     * Set the cj_days_to_complete field on the base
     * of cj_activity_start_date and cj_activity_completion_date
     *
     * @param \SugarBean $activity
     * @return void
     */
    public function setCJDaysToComplete(\SugarBean $activity)
    {
        if ($activity->getModuleName() === 'Tasks' && !empty($activity->cj_activity_start_date) && !empty($activity->cj_activity_completion_date)) {
            $dt1 = $GLOBALS['timedate']->fromDb($activity->cj_activity_start_date);
            $dt2 = $GLOBALS['timedate']->fromDb($activity->cj_activity_completion_date);

            if (!is_null($dt1) || !is_null($dt2)) {
                $activity->cj_days_to_complete = $dt2->diff($dt1)->format('%a days, %h hours, %i minutes and %s seconds');
            }
        }
    }

    /**
     * Resets the allowed by error field to false
     *
     * @return void
     */
    public static function resetAllowedByError()
    {
        self::$allowedByError = false;
    }

    /**
     * Returns the allowed by error field
     *
     * @return void
     */
    public static function getAllowedByError()
    {
        return self::$allowedByError;
    }
}
