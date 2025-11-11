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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper;

use Sugarcrm\Sugarcrm\CustomerJourney\SharedData as CJSharedData;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\SelectToOption as SelectToOption;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate\ParseVariablesInURL as ParseVariablesInURL;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerTrait;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\ParseData;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate\AllowActivityBy as AllowActivityBy;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CustomerJourneyException;

/**
 * This class here provides functions for the activities
 */
class ActivityHelper
{
    use ActivityHandlerTrait {
        ActivityHandlerTrait::__construct as activityHandlerTraitConstruct;
    }
    /**
     * @var ActivityHelper
     */
    protected static $activityHelperObject;

    /**
     * @var string
     */
    protected $linkName;

    /**
     * @var string
     */
    protected $moduleName;

    protected $appointmentModules = ['Calls', 'Meetings'];

    private function __construct($link, $module)
    {
        $this->linkName = $link;
        $this->moduleName = $module;
    }

    /**
     * Implement Singleton for ActivityHelper
     *
     * @var string
     * @var string
     */
    public static function getInstance($link = null, $module = null)
    {
        if (empty(self::$activityHelperObject) || ($link !== null && $module !== null)) {
            self::$activityHelperObject = new ActivityHelper($link, $module);

            self::$activityHelperObject->activityHandlerTraitConstruct();
        }

        return self::$activityHelperObject;
    }

    /**
     * {@inheritdoc}
     */
    public function start(\SugarBean $activity)
    {
        $timeDate = \TimeDate::getInstance();
        $save = $this->basicStartActivity($activity); //call basic activity's 'start' method

        if ($this->hasActivityTemplate($activity)) {
            $template = $this->getActivityTemplate($activity);
            if (in_array($activity->getModuleName(), $this->appointmentModules)) {
                if ($activity->stageIsNowInProgress && $template->send_invite_type === \DRI_Workflow_Task_Template::SEND_INVITES_STAGE_START) {
                    $activity->send_invites = true;
                    $this->prepareAndSetNotificationRecipients($activity);
                    $save = true;
                }
            }

            $stageStarted = false;

            switch ($template->task_start_date_type) {
                case \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_STAGE_STARTED:
                    $stageStarted = true;
                    $save = $this->createAndSetStartDueDate(
                        $template,
                        $activity,
                        $timeDate->getNow(),
                        'date_start'
                    );
                    break;
            }

            switch ($template->task_due_date_type) {
                case \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD:
                    if ($template->due_date_criteria === 'Percentage' && $stageStarted === true) {
                        $date = $this->activityDatesHelper->getDueDateFromParentField($activity, $template);
                        if (!empty($date)) {
                            $this->createDueDateInPercentageTimeframe($template, $activity, $date);
                        }
                    }
                    break;
                case \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_STAGE_STARTED:
                    $save = $this->createAndSetStartDueDate(
                        $template,
                        $activity,
                        $timeDate->getNow(),
                        'date_due'
                    );
                    break;
            }

            $this->setStartDateAsEndDate($activity);

            switch ($template->momentum_start_type) {
                case \DRI_Workflow_Task_Template::MOMENTUM_START_TYPE_STAGE_STARTED:
                    $activity->cj_momentum_start_date = $timeDate->asUser($timeDate->getNow());
                    break;
            }
        }
        return $save;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAssigneePreviousActivityCompleted($template, \DRI_SubWorkflow $stage, \SugarBean $activity)
    {
        if ($template->getAssigneeRule($stage) ===
            \DRI_Workflow_Task_Template::ASSIGNEE_RULE_PREVIOUS_ACTIVITY_COMPLETED &&
            empty($activity->assigned_user_id)) {
            $this->applyAssigneeRuleOnActivity($template, $activity, $stage);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function previousActivityCompleted(\SugarBean $activity, \SugarBean $previous)
    {
        $timeDate = \TimeDate::getInstance();

        if ($this->hasActivityTemplate($activity)) {
            $stage = $this->getStage($activity);
            $template = $this->getActivityTemplate($activity);
            $isUpdate = false;
            $taskStartDays = \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_PREVIOUS_ACTIVITY_COMPLETED;
            $taskDueDays = \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_PREVIOUS_ACTIVITY_COMPLETED;
            if ($template->task_start_date_type === $taskStartDays) {
                $isUpdate = $this->createAndSetStartDueDate(
                    $template,
                    $activity,
                    $timeDate->getNow(),
                    'date_start'
                );
            }
            if ($template->task_due_date_type === $taskDueDays) {
                $isUpdate = $this->createAndSetStartDueDate(
                    $template,
                    $activity,
                    $timeDate->getNow(),
                    'date_due'
                );
            }

            $this->setStartDateAsEndDate($activity);
            if (in_array($activity->getModuleName(), $this->appointmentModules)) {
                $this->calculateDiffBetweenStartDateAndEndDateInHours($activity, $template);
            }
            if ($isUpdate === true) {
                $activity->save();
            }

            if ($template->momentum_start_type ===
                \DRI_Workflow_Task_Template::MOMENTUM_START_TYPE_PREVIOUS_ACTIVITY_COMPLETED) {
                $activity->cj_momentum_start_date = $timeDate->asUser($timeDate->getNow());
                $activity->save();
            }

            if ($template->getAssigneeRule($stage) ===
                \DRI_Workflow_Task_Template::ASSIGNEE_RULE_PREVIOUS_ACTIVITY_COMPLETED &&
                empty($activity->assigned_user_id)) {
                $previous->setAssignmentSummary = true;
                $activity->setAssignmentSummary = true;
                $this->applyAssigneeRuleOnActivity($template, $activity, $stage);
            }
        }
    }

    /**
     * Set start date when a specific activity is completed
     * @param \SugarBean $depActivity
     * @param \SugarBean $completedActivity
     */
    public function setStartDateWhenSpecificActivityCompleted(\SugarBean $depActivity, \SugarBean $completedActivity)
    {
        $timeDate = \TimeDate::getInstance();

        if ($this->hasActivityTemplate($depActivity)) {
            $template = $this->getActivityTemplate($depActivity);

            if ($template->task_start_date_type === \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED) {
                if ($depActivity->getModuleName() === 'Tasks' &&
                    !empty($depActivity->date_start)) {
                    return;
                }
                $isUpdate = $this->createAndSetStartDueDate(
                    $template,
                    $depActivity,
                    $timeDate->getNow(),
                    'date_start'
                );
                $this->setStartDateAsEndDate($depActivity);
                if (in_array($depActivity->getModuleName(), $this->appointmentModules)) {
                    $this->calculateDiffBetweenStartDateAndEndDateInHours($depActivity, $template);
                }
                $depActivity->save();
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function assigneeRuleSpecificActivityCompleted(\SugarBean $depActivity, \SugarBean $completedActivity)
    {
        $completedActivity->setAssignmentSummary = true;
        $depActivity->setAssignmentSummary = true;

        if ($this->hasActivityTemplate($depActivity)) {
            $template = $this->getActivityTemplate($depActivity);
            $stage = $this->getStage($depActivity);
            if ($template->getAssigneeRule($stage) ===
                \DRI_Workflow_Task_Template::ASSIGNEE_RULE_SPECIFIC_ACTIVITY_COMPLETED &&
                empty($depActivity->assigned_user_id)) {
                $this->applyAssigneeRuleOnActivity($template, $depActivity, $stage);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromTemplate(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        $activity->dri_workflow_task_template_id = $template->id;
        $activity->dri_workflow_sort_order = $template->sort_order;
        $activity->name = $template->name;
        $activity->description = $template->description;
        $activity->customer_journey_points = $template->points;
        $activity->is_cj_parent_activity = isTruthy($template->is_parent);
        $activity->customer_journey_blocked_by = $template->blocked_by;
        $activity->cj_blocked_by_stages = $template->blocked_by_stages;

        $activity->start_next_journey_id = $template->start_next_journey_id;
        $activity->cj_allow_activity_by = $template->allow_activity_by;

        \CJ_Form::setTargetValues($activity, $template);
        $this->setActualSortOrder($activity);

        if ($activity->getModuleName() === 'Calls') {
            $activity->direction = $template->direction;
        } elseif ($activity->getModuleName() === 'Tasks') {
            $activity->priority = $template->priority;
            $activity->customer_journey_type = $template->type;
        }
        $activity->status = $this->statusHelper->getNotStartedStatus($activity);
        $timeDate = \TimeDate::getInstance();

        switch ($template->task_start_date_type) {
            case \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_CREATED:
                $this->createAndSetStartDueDate(
                    $template,
                    $activity,
                    $timeDate->getNow(),
                    'date_start'
                );
                break;
            case \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD:
                $this->setStartDateFromParentField($activity);
                break;
            case \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD:
                $this->setStartDateFromParentField($activity);
                break;
            default:
                if (in_array($activity->getModuleName(), $this->appointmentModules)) {
                    // default to a "empty" start date, we always needs to
                    // set a start date for calls/meetings since this field is required
                    $activity->date_start = $timeDate->asUser($this->getEmptyStartDate());
                }
        }

        switch ($template->task_due_date_type) {
            case \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_CREATED:
                $this->createAndSetStartDueDate(
                    $template,
                    $activity,
                    $timeDate->getNow(),
                    'date_due'
                );
                break;
            case \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD:
                $this->setDueDateFromParentField($activity);
                break;
            default:
                if (in_array($activity->getModuleName(), $this->appointmentModules)) {
                    // default to a "empty" due date, we always needs to
                    // set a start date for calls/meetings since this field is required
                    $this->createAndSetStartDueDate(
                        $template,
                        $activity,
                        $this->getEmptyStartDate(),
                        'date_due'
                    );
                }
        }

        $this->setStartDateAsEndDate($activity);

        switch ($template->momentum_start_type) {
            case \DRI_Workflow_Task_Template::MOMENTUM_START_TYPE_CREATED:
                $activity->cj_momentum_start_date = $timeDate->asUser($timeDate->getNow());
                break;
            case \DRI_Workflow_Task_Template::MOMENTUM_START_TYPE_PARENT_DATE_FIELD:
                $this->setMomentumStartDateFromParentField($activity);
                break;
        }
    }

    /**
     * Validates the parent
     *
     * @param \SugarBean $activity
     * @return void
     * @throws CustomerJourneyException\NotFoundException
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function validateParent(\SugarBean $activity, string $statusChanged)
    {
        try {
            if (($statusChanged === $this->statusHelper::TASK_STATUS_COMPLETED ||
                $statusChanged === $this->statusHelper::APPOINMENT_STATUS_DEFERRED) &&
                isTruthy($activity->is_cj_parent_activity)) {
                if (!empty($activity->cj_allow_activity_by) && !$GLOBALS['current_user']->is_admin) {
                    $allowFlag = AllowActivityBy::isActivityAllow($activity, $activity->cj_allow_activity_by);
                    if (!$allowFlag) {
                        if (!isset($activity->processing_smart_guide)) {
                            throw new \SugarApiExceptionInvalidParameter(
                                translate('LBL_CURRENT_USER_UNABLE_TO_COMPLETE_STATUS', 'DRI_Workflow_Task_Templates')
                            );
                        } else {
                            return false;
                        }
                    }
                }
            }
        } catch (CustomerJourneyException\InvalidLicenseException $e) {
            // omit errors when license is not valid or user missing access
        }
        return true;
    }

    /**
     * @param \SugarBean $activity
     */
    public function setStartDateFromParentField(\SugarBean $activity)
    {
        if (!$this->hasActivityTemplate($activity)) {
            return;
        }

        $template = $this->getActivityTemplate($activity);
        $date = $this->activityDatesHelper->getStartDateFromParentField($activity, $template);

        if (!empty($date)) {
            $this->createAndSetStartDueDate($template, $activity, $date, 'date_start');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDueDateFromParentField(\SugarBean $activity)
    {
        if (!$this->hasActivityTemplate($activity)) {
            return;
        }

        $template = $this->getActivityTemplate($activity);
        $date = $this->activityDatesHelper->getDueDateFromParentField($activity, $template);

        if (!empty($date)) {
            if ($template->due_date_criteria === 'Percentage') {
                $this->createDueDateInPercentageTimeframe($template, $activity, $date);
            } else {
                $this->createAndSetStartDueDate($template, $activity, $date, 'date_due');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDueDateWhenSpecificActivityCompleted(\SugarBean $depActivity, \SugarBean $completedActivity)
    {
        $timeDate = \TimeDate::getInstance();

        if ($this->hasActivityTemplate($depActivity)) {
            $template = $this->getActivityTemplate($depActivity);

            if ($template->task_due_date_type ===
                \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED) {
                if (($depActivity->getModuleName() === 'Tasks' && empty($depActivity->date_due)) ||
                    in_array($depActivity->getModuleName(), $this->appointmentModules)
                ) {
                    $this->createAndSetStartDueDate(
                        $template,
                        $depActivity,
                        $timeDate->getNow(),
                        'date_due'
                    );
                    if ($depActivity->getModuleName() === 'Tasks') {
                        $startDate = $timeDate->asUser($timeDate->fromString($depActivity->date_start));
                        $endDate = $timeDate->asUser($timeDate->fromString($depActivity->date_due));
                        $this->setStartDateAsEndDate($depActivity);
                    } else {
                        $emptyStartDate = $timeDate->split_date_time($timeDate->asDb($this->getEmptyStartDate()));
                        $startDate = $timeDate->asDb($timeDate->fromDb($depActivity->date_start));
                        $startDate = $timeDate->split_date_time($startDate);

                        //If start date is empty at this point then set it as due date
                        if ($emptyStartDate[0] === $startDate[0]) {
                            $depActivity->date_start = $depActivity->date_end;
                        }
                        $this->setStartDateAsEndDate($depActivity);
                    }
                    $depActivity->save();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMomentumStartDateFromParentField(\SugarBean $activity)
    {
        if (!$this->hasActivityTemplate($activity)) {
            return;
        }

        $timeDate = \TimeDate::getInstance();
        $template = $this->getActivityTemplate($activity);
        $date = $this->getMomentumStartDateFromParentField($activity, $template);

        if (!empty($date)) {
            $activity->cj_momentum_start_date = $timeDate->asUser($date);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createFromTemplate(
        \DRI_Workflow_Task_Template $activityTemplate,
        \DRI_SubWorkflow            $stage,
        \SugarBean                  $parent
    ) {

        $activity = $this->create();

        $this->stageHelper->populateFromStage($activity, $parent, $stage, $activityTemplate);

        $this->populateFromStageTemplate($activity, $stage->getTemplate());
        $this->populateFromJourneyTemplate($activity, $stage->getTemplate()->getJourneyTemplate());
        $this->parentHelper->populateFromParent($activity, $parent);
        $this->populateFromTemplate($activity, $activityTemplate);

        $parser = ParseData::parseVariables($activityTemplate->url);

        if (empty($parser[0])) { //If there is no variable in URL then return it as it is
            $activity->cj_url = $activityTemplate->url;
        } else {
            $postBody = new ParseVariablesInURL();
            $info = $postBody->parseModule($parser, $activity);
            $activity->cj_url = ParseData::replaceVariablesWithValues($info, $activityTemplate->url);
        }
        if (in_array($this->getModuleName(), $this->appointmentModules)) {
            $this->prepareInviteesAndSetNotificationEmailFlag($activity, $activityTemplate, $parent);
        }
        return $activity;
    }


    /**
     * {@inheritdoc}
     */
    public function afterCreate(\SugarBean $activity, \SugarBean $parent)
    {
        $this->webooksHelper->afterCreate($activity, $parent);
        // Invites the parent Contact/Lead to Meeting/Call
        if (in_array($this->getModuleName(), $this->appointmentModules)) {
            if ($parent->module_dir === 'Contacts') {
                $activity->load_relationship('contacts');
                $activity->contacts->add($parent->id);
            } elseif ($parent->module_dir === 'Leads') {
                $activity->load_relationship('leads');
                $activity->leads->add($parent->id);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCreate(\SugarBean $activity, \SugarBean $parent)
    {
        $this->webooksHelper->beforeCreate($activity, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function afterCompleted(\DRI_Workflow $journey, \DRI_SubWorkflow $stage, \SugarBean $activity)
    {
        $this->webooksHelper->afterCompleted($journey, $stage, $activity);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCompleted(\SugarBean $activity)
    {
        $this->webooksHelper->beforeCompleted($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function afterInProgress(\SugarBean $activity)
    {
        $this->webooksHelper->afterInProgress($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeInProgress(\SugarBean $activity)
    {
        $this->webooksHelper->beforeInProgress($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function afterNotApplicable(\SugarBean $activity)
    {
        $this->webooksHelper->afterNotApplicable($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeNotApplicable(\SugarBean $activity)
    {
        $this->webooksHelper->beforeNotApplicable($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete(\SugarBean $activity)
    {
        $this->webooksHelper->afterDelete($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete(\SugarBean $activity)
    {
        $this->webooksHelper->beforeDelete($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidStatus($module_name, $status)
    {
        return $this->statusHelper->isValidStatus($module_name, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function isCompleted(\SugarBean $activity)
    {
        return $this->statusHelper->isCompleted($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateStatus(\SugarBean $activity)
    {
        return $this->statusHelper->calculateStatus($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(\SugarBean $activity, $status)
    {
        $this->statusHelper->setStatus($activity, $status);
        if (empty($activity->cj_parent_activity_id)) {
            $this->updateActivityCache($activity);
        }
    }

    /**
     * When Activity status is updated, update that activity in the cache list maintained in the journey
     *
     * @param \SugarBean $activity
     */
    private function updateActivityCache(\SugarBean $activity)
    {
        // get Stage of the given activity
        $stage = $this->getStage($activity);
        // get Journery of the stage
        $journey = $stage->getJourney();
        // Get Stages from cache of the journey
        $stages = $journey->getStages();
        // Get Stage from Cache
        $filteredStages = array_filter(
            $stages,
            function ($stageIter) use ($activity) {
                return $stageIter->id == $activity->dri_subworkflow_id;
            }
        );

        $foundStage = array_pop($filteredStages);
        if (!empty($foundStage) && !is_null($foundStage->getActivities())) {
            $foundStage->setActivity($activity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isStatusChanged(\SugarBean $activity)
    {
        return $this->statusHelper->isStatusChanged($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function haveChangedStatus(\SugarBean $activity)
    {
        return $this->statusHelper->haveChangedStatus($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotStartedStatus(\SugarBean $activity, $module_name = '')
    {
        return $this->statusHelper->getNotStartedStatus($activity, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getInProgressStatus(\SugarBean $activity, $module_name = '')
    {
        return $this->statusHelper->getInProgressStatus($activity, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletedStatus(\SugarBean $activity, $module_name = '')
    {
        return $this->statusHelper->getCompletedStatus($activity, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelledStatus(\SugarBean $activity, $module_name = '')
    {
        return $this->statusHelper->getCancelledStatus($activity, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotApplicableStatus(\SugarBean $activity, $module_name = '')
    {
        return $this->statusHelper->getNotApplicableStatus($activity, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function isInProgress(\SugarBean $activity)
    {
        return $this->statusHelper->isInProgress($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotStarted(\SugarBean $activity)
    {
        return $this->statusHelper->isNotStarted($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotApplicable(\SugarBean $activity)
    {
        return $this->statusHelper->isNotApplicable($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled(\SugarBean $activity)
    {
        return $this->statusHelper->isCancelled($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isScoreChanged(\SugarBean $activity)
    {
        return $this->scoreHelper->isScoreChanged($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getScore(\SugarBean $activity)
    {
        return $this->scoreHelper->getScore($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function setScore(\SugarBean $activity, $score)
    {
        $this->scoreHelper->setScore($activity, $score);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateScore(\SugarBean $activity)
    {
        return $this->scoreHelper->calculateScore($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromStage(
        \SugarBean                  $activity,
        \SugarBean                  $parent,
        \DRI_SubWorkflow            $stage,
        \DRI_Workflow_Task_Template $activityTemplate
    ) {

        $this->stageHelper->populateFromStage($activity, $parent, $stage, $activityTemplate);
    }

    /**
     * {@inheritdoc}
     */
    public function setStage(\DRI_SubWorkflow $stage)
    {
        $this->stageHelper->setStage($stage);
    }

    /**
     * {@inheritdoc}
     */
    public function getStage(\SugarBean $activity)
    {
        return $this->stageHelper->getStage($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getStageId(\SugarBean $activity)
    {
        return $this->stageHelper->getStageId($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function orderExistOnStage($stageId, $order, $skipId)
    {
        return $this->stageHelper->orderExistOnStage($stageId, $order, $skipId, $this->moduleName);
    }

    /**
     * {@inheritdoc}
     */
    public function isStageActivity(\SugarBean $activity)
    {
        return $this->stageHelper->isStageActivity($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isProgressChanged(\SugarBean $activity)
    {
        return $this->progressHelper->isProgressChanged($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function setProgress(\SugarBean $activity, $progress)
    {
        $this->progressHelper->setProgress($activity, $progress);
    }

    /**
     * {@inheritdoc}
     */
    public function getProgress(\SugarBean $activity)
    {
        $this->progressHelper->getProgress($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateProgress(\SugarBean $activity)
    {
        return $this->progressHelper->calculateProgress($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints(\SugarBean $activity)
    {
        return $this->pointsHelper->getPoints($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePoints(\SugarBean $activity)
    {
        return $this->pointsHelper->calculatePoints($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints(\SugarBean $activity, $points)
    {
        $this->pointsHelper->setPoints($activity, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function haveChangedPoints(\SugarBean $activity)
    {
        return $this->pointsHelper->haveChangedPoints($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isPointsChanged(\SugarBean $activity)
    {
        return $this->pointsHelper->isPointsChanged($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isBlocked(\SugarBean $activity)
    {
        if (isset($activity->ignore_blocked_by) && $activity->ignore_blocked_by === true) {
            return false;
        }
        return $this->blockedByHelper->isBlocked($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isBlockedByStage(\SugarBean $activity)
    {
        if (isset($activity->ignore_blocked_by) && $activity->ignore_blocked_by === true) {
            return false;
        }
        return $this->blockedByHelper->isBlockedByStage($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockedByIds(\SugarBean $activity)
    {
        return $this->blockedByHelper->getBlockedByIds($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockedByStageIds(\SugarBean $activity)
    {
        return $this->blockedByHelper->getBlockedByStageIds($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockedByActivityIds(\SugarBean $activity, &$moduleNames = [])
    {
        return $this->blockedByHelper->getBlockedByActivityIds($activity, $moduleNames);
    }

    /**
     * Return the ids of those blocked by stages which are not completed
     * @param \SugarBean $activity
     * @return type
     */
    public function getNotCompletedBlockedByStageIds(\SugarBean $activity)
    {
        return $this->blockedByHelper->getNotCompletedBlockedByStageIds($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBlockedBy(\SugarBean $activity)
    {
        return $this->blockedByHelper->hasBlockedBy($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBlockedByStages(\SugarBean $activity)
    {
        return $this->blockedByHelper->hasBlockedByStages($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockedBy(\SugarBean $activity)
    {
        return $this->blockedByHelper->getBlockedBy($activity);
    }

    /**
     * Return those blocked by stage beans which are currently not completed and assigned
     * to the activity
     * @param \SugarBean $activity
     * @return type
     */
    public function getNotCompletedBlockedByStages(\SugarBean $activity)
    {
        return $this->blockedByHelper->getNotCompletedBlockedByStages($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextChildActivity(\SugarBean $activity)
    {
        return $this->childActivityHelper->getNextChildActivity($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildOrder(\SugarBean $activity)
    {
        return $this->childActivityHelper->getChildOrder($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveChildren(\SugarBean $bean, $module_name)
    {
        if (empty($module_name)) {
            $module_name = $this->moduleName;
        }
        return $this->childActivityHelper->retrieveChildren($bean, $module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(\SugarBean $bean)
    {
        if ($this->isParent($bean)) {
            return $this->childActivityHelper->getChildren($bean);
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function resetChildren()
    {
        $this->childActivityHelper->resetChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function loadChildren(\SugarBean $bean)
    {
        $this->childActivityHelper->loadChildren($bean);
    }

    /**
     * {@inheritdoc}
     */
    public function insertChild(\SugarBean $activity, \SugarBean $child)
    {
        $this->childActivityHelper->insertChild($activity, $child);
    }

    /**
     * Since all php functions that sorts an array based on a function is blacklisted by the package scanner
     * we have to implement our own algorithm, this is based on quicksort
     *
     * @param \SugarBean[] $activities
     * @return array
     */
    private function sortChildren($activities)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMomentumPoints(\SugarBean $activity)
    {
        return $this->momentumHelper->getMomentumPoints($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getMomentumScore(\SugarBean $activity)
    {
        return $this->momentumHelper->getMomentumScore($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateMomentum(\SugarBean $activity)
    {
        $this->momentumHelper->calculateMomentum($activity);
    }

    /**
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $template
     * @return \DateTime|null
     */
    protected function getMomentumStartDateFromParentField(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        return $this->momentumHelper->getMomentumStartDateFromParentField($activity, $template);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMomentum(\SugarBean $activity)
    {
        return $this->momentumHelper->hasMomentum($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateMomentumPoints(\SugarBean $activity)
    {
        return $this->momentumHelper->calculateMomentumPoints($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function setMomentumPoints(\SugarBean $activity, $points)
    {
        $this->momentumHelper->setMomentumPoints($activity, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromParent(\SugarBean $activity, \SugarBean $parent)
    {
        $this->parentHelper->populateFromParent($activity, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromParentActivity(\SugarBean $activity, \SugarBean $parent)
    {
        $this->parentHelper->populateFromParentActivity($activity, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function relateToParent(\SugarBean $activity, \SugarBean $parent)
    {
        $this->parentHelper->relateToParent($activity, $parent, $this->getLinkName());
    }

    /**
     * {@inheritdoc}
     */
    public function haveChangedParent(\SugarBean $activity)
    {
        return $this->parentHelper->haveChangedParent($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function isParent(\SugarBean $activity)
    {
        return $this->parentHelper->isParent($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent(\SugarBean $activity)
    {
        return $this->parentHelper->hasParent($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(\SugarBean $activity)
    {
        return $this->parentHelper->getParent($activity);
    }

    /**
     * Get module name
     * @return String
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Start an activity
     * @return boolean
     */
    public function basicStartActivity(\SugarBean $activity)
    {
        $stage = $this->stageHelper->getStage($activity);

        $save = false;

        if ($this->hasActivityTemplate($activity)) {
            $activityTemplate = $this->getActivityTemplate($activity);
            $save = true;
            if ($activityTemplate->getAssigneeRule($stage) === \DRI_Workflow_Template::ASSIGNEE_RULE_STAGE_START) {
                try {
                    $parent = $stage->getParent();

                    if (empty($activity->assigned_user_id)) {
                        $activity->assigned_user_id = $this->getTargetAssigneeId(
                            $stage,
                            $activityTemplate,
                            $activity,
                            $parent
                        );
                    }

                    $activity->team_id = $this->getTargetTeamId(
                        $stage,
                        $activityTemplate,
                        $parent
                    );
                    $activity->team_set_id = $this->getTargetTeamSetId(
                        $stage,
                        $activityTemplate,
                        $parent
                    );
                } catch (CJException\ParentNotFoundException $e) {
                    $GLOBALS['log']->debug('Sugar Automate', __FILE__ . ' ' . __LINE__, $e->getMessage());
                }
                $save = true;
            }
        }

        if (!empty($activity->id) && $this->isParent($activity)) {
            foreach ($this->childActivityHelper->getChildren($activity) as $child) {
                if (!empty($child->module_dir)) {
                    $handler = ActivityHandlerFactory::factory($child->module_dir);
                    if ($handler->start($child)) {
                        $child->save();
                    }
                }
            }
        }

        return $save;
    }

    /**
     * Create an activity
     * @return \SugarBean
     */
    public function create($module_name = '')
    {
        if (empty($module_name)) {
            $module_name = $this->moduleName;
        }
        return \BeanFactory::newBean($module_name);
    }

    /**
     * Get a dri_subworkflow record by id and name
     * @return object
     */
    public function getByStageIdAndName($stageId, $name, $skipId)
    {
        $where = [
            'dri_subworkflow_id' => $stageId,
            'name' => $name,
        ];
        return $this->prepareAndRunStageQuery($where, $skipId);
    }

    /**
     * Get a dri_subworkflow record by id and order
     * @return object
     */
    public function getByStageIdAndOrder($stageId, $order, $skipId)
    {
        $where = [
            'dri_subworkflow_id' => $stageId,
            'dri_workflow_sort_order' => $order,
        ];
        return $this->prepareAndRunStageQuery($where, $skipId);
    }

    /**
     * Prepare the query and return the stage record
     *
     * @param array $whereFields
     * @param string $skipId
     * @return object
     */
    protected function prepareAndRunStageQuery($whereFields = [], $skipId = null)
    {
        $query = new \SugarQuery();
        $query->from($this->create());
        $query->select('id');
        $where = $query->where();

        foreach ($whereFields as $field => $value) {
            $where->equals($field, $value);
        }

        if (null !== $skipId) {
            $where->notEquals('id', $skipId);
        }

        $results = $query->execute();

        if (safeCount($results) === 0) {
            throw new \SugarApiExceptionNotFound();
        }

        $result = array_shift($results);

        return $this->getById($result['id']);
    }

    /**
     * Check the record existance and return it
     *
     * @param string $id
     * @return object
     * @throws \SugarApiExceptionNotFound
     */
    public function getById($id)
    {
        if (empty($id)) {
            throw new \SugarApiExceptionNotFound();
        }

        $activity = \BeanFactory::retrieveBean($this->create()->module_dir, $id);

        if (null === $activity) {
            throw new \SugarApiExceptionNotFound();
        }

        return $activity;
    }

    /**
     * Get activity sort order
     * @return string
     */
    public function getSortOrder(\SugarBean $activity)
    {
        return (string)$activity->dri_workflow_sort_order;
    }

    /**
     * Calculate points and scores of activity
     */
    public function calculate(\SugarBean $activity)
    {
        $this->pointsHelper->setPoints($activity, $this->pointsHelper->calculatePoints($activity));
        $this->momentumHelper->setMomentumPoints($activity, $this->momentumHelper->calculateMomentumPoints($activity));
        $this->scoreHelper->setScore($activity, $this->scoreHelper->calculateScore($activity));
        $this->progressHelper->setProgress($activity, $this->progressHelper->calculateProgress($activity));
    }

    /**
     * Set the sort order of the activity
     */
    public function setActualSortOrder(\SugarBean $activity)
    {
        $stage = $this->stageHelper->getStage($activity);
        $activitySortOrder = $activity->dri_workflow_sort_order;
        $stageSortOrder = $stage->sort_order;

        if (strlen($activitySortOrder) === 1) {
            $activitySortOrder = "0{$activitySortOrder}";
        }

        if (strlen($stageSortOrder) === 1) {
            $stageSortOrder = "0{$stageSortOrder}";
        }

        $activity->cj_actual_sort_order = "{$stageSortOrder}.{$activitySortOrder}";
    }

    /**
     * Returns true if activity template id is present
     * @return boolean
     */
    public function hasActivityTemplate(\SugarBean $activity)
    {
        $id = $this->getActivityTemplateId($activity);
        return !empty($id);
    }

    /**
     * Returns the activity template bean
     * @return Object
     */
    public function getActivityTemplate(\SugarBean $activity)
    {
        return \DRI_Workflow_Task_Template::getById($this->getActivityTemplateId($activity));
    }

    /**
     * Returns the activity template id
     * @return string
     */
    public function getActivityTemplateId(\SugarBean $activity)
    {
        if (!empty($activity->dri_workflow_task_template_id)) {
            return $activity->dri_workflow_task_template_id;
        }

        if ($activity->deleted && !empty($activity->fetched_row['dri_workflow_task_template_id'])) {
            return $activity->fetched_row['dri_workflow_task_template_id'];
        }

        return null;
    }

    /**
     * Increase the activity sort order
     */
    public function increaseSortOrder(\SugarBean $activity)
    {
        $activity->dri_workflow_sort_order++;
        $this->setActualSortOrder($activity);
    }

    /**
     * Populate the activity from its template
     */

    /**
     * Populate the activity from journey template
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Template $journeyTemplate
     */
    public function populateFromJourneyTemplate(\SugarBean $activity, \DRI_Workflow_Template $journeyTemplate)
    {
        $activity->dri_workflow_template_id = $journeyTemplate->id;
        $activity->dri_workflow_template_name = $journeyTemplate->name;
    }

    /**
     * Populate the activity from journey template
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Template $journeyTemplate
     */
    public function populateFromStageTemplate(\SugarBean $activity, \DRI_SubWorkflow_Template $stageTemplate)
    {
        $activity->dri_subworkflow_template_id = $stageTemplate->id;
        $activity->dri_subworkflow_template_name = $stageTemplate->name;
    }

    /**
     * Loads the stage
     * @param \DRI_SubWorkflow $stage
     * @return array stages array
     */
    public function load(\DRI_SubWorkflow $stage)
    {
        // really make sure not to load this relationship if the stage is new.
        // If doing this and the id is empty ALL activities in the system not related to a CJ
        // will be retrieved and potentially changed!
        if (empty($stage->id)) {
            return [];
        }

        $bean = \BeanFactory::newBean($this->moduleName);

        [$idFieldQuery, $allFieldsQuery] = $this->createLoadQuery();
        $idFieldQuery->where()->equals($this->getStageIdFieldName(), $stage->id);
        $ids = $idFieldQuery->compile()
            ->execute()
            ->fetchAllAssociative();

        if (safeCount($ids) < 1) {
            return [];
        }

        $recordIds = array_column($ids, 'id');

        $allFieldsQuery->where()->in('id', $recordIds);

        return $bean->fetchFromQuery($allFieldsQuery);
    }

    /**
     * Get stage ID field name
     * @return string
     */
    public function getStageIdFieldName()
    {
        return 'dri_subworkflow_id';
    }

    /**
     * Create query to load the activity data
     * @return array
     */
    public function createLoadQuery()
    {
        $bean = \BeanFactory::newBean($this->moduleName);

        $idFieldQuery = new \SugarQuery();
        $idFieldQuery->from($bean);
        $idFieldQuery->select('id');
        $idFieldQuery->where()
            ->isEmpty('cj_parent_activity_id');

        // Visibility was applied in idFieldQuery, so all the beans are 100% visible for the user
        $bean = \BeanFactory::newBean($this->moduleName);
        $bean->disable_row_level_security = true;

        $allFieldsQuery = new \SugarQuery();
        $allFieldsQuery->from($bean);
        $allFieldsQuery->select('*');

        return [$idFieldQuery, $allFieldsQuery];
    }

    /**
     * Get the forms related to this activity
     * @param \SugarBean $bean
     * @return object
     */
    public function getForms(\SugarBean $bean)
    {
        return $this->getActivityTemplate($bean)->getForms();
    }

    /**
     * Check the record existance and return it
     *
     * @param string $id
     * @return object
     * @throws \SugarApiExceptionNotFound
     */
    public function isFieldChanged(\SugarBean $activity, $field)
    {
        $fetchedRowValue = false;
        if (is_array($activity->fetched_row)) {
            $fetchedRowValue = $activity->fetched_row[$field];
        }

        return $activity->{$field} !== $fetchedRowValue;
    }

    /**
     * Get the target team id for activity
     *
     * @param \DRI_SubWorkflow $stage
     * @param \SugarBean $activityTemplate
     * @param \SugrBean $parent
     * @return string
     */
    public function getTargetTeamId(\DRI_SubWorkflow $stage, $activityTemplate, \SugarBean $parent)
    {
        switch ($activityTemplate->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                return !empty($GLOBALS['current_user']->id) ? $GLOBALS['current_user']->default_team : '1';
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $parent->team_id;
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER:
                return \BeanFactory::getBean('Users', $activityTemplate->target_assignee_user_id)->team_id;
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_TEAM:
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER_TEAM:
                return $activityTemplate->target_assignee_team_id;
        }

        return $stage->getTargetTeamId();
    }

    /**
     * Get the target team set id for activity
     *
     * @param \DRI_SubWorkflow $stage
     * @param \SugarBean $activityTemplate
     * @param \SugrBean $parent
     * @return string
     */
    public function getTargetTeamSetId(\DRI_SubWorkflow $stage, $activityTemplate, \SugarBean $parent)
    {
        switch ($activityTemplate->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                return !empty($GLOBALS['current_user']->id) ? $GLOBALS['current_user']->team_set_id : '1';
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $parent->team_set_id;
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER:
                $userTeamSetId = \BeanFactory::getBean(
                    'Users',
                    $activityTemplate->target_assignee_user_id
                )->team_set_id;
                $stageTeamSetId = $stage->getTargetTeamSetId();

                $teamSet = new \TeamSet();
                $teamSetIds = $teamSet->getTeamIds($stageTeamSetId);

                if (!empty($userTeamSetId)) {
                    $teamSetIds = array_merge($teamSetIds, $teamSet->getTeamIds($userTeamSetId));
                }

                return $teamSet->addTeams($teamSetIds);
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_TEAM:
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER_TEAM:
                $teamSet = new \TeamSet();
                $teamSetIds = array_merge(
                    $teamSet->getTeamIds($stage->getTargetTeamSetId()),
                    [$activityTemplate->target_assignee_team_id]
                );

                return $teamSet->addTeams($teamSetIds);
        }

        return $stage->getTargetTeamSetId();
    }

    /**
     * Get the target assigned user id for activity
     *
     * @param \DRI_SubWorkflow $stage
     * @param \SugarBean $activityTemplate
     * @param \SugarBean $activity
     * @param \SugrBean $parent
     * @return string
     */
    public function getTargetAssigneeId(
        \DRI_SubWorkflow $stage,
        $activityTemplate,
        \SugarBean       $activity,
        \SugarBean       $parent
    ) {

        switch ($activityTemplate->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                if (!empty($GLOBALS['current_user']->id)) {
                    return $GLOBALS['current_user']->id;
                } else {
                    return $activity->created_by;
                }
                //no break
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $parent->assigned_user_id;
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER:
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_USER_TEAM:
                return $activityTemplate->target_assignee_user_id;
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_TEAM:
                return '';
        }

        return $stage->getTargetAssigneeId();
    }

    /**
     * Updates the assignee of activity
     *
     * @param \DRI_Workflow_Task_Templates $template
     * @param \SugarBean $activity
     * @param \DRI_SubWorkflow $stage
     */
    public function applyAssigneeRuleOnActivity($template, $activity, $stage)
    {
        try {
            $parent = $stage->getParent();

            //setAssignmentSummary, flag for make sure send a single compile assignment summary email
            if (empty($activity->assigned_user_id) || ($activity->setAssignmentSummary === true)) {
                $activity->assigned_user_id = $this->getTargetAssigneeId($stage, $template, $activity, $parent);
            }

            $activity->team_id = $this->getTargetTeamId($stage, $template, $parent);
            $activity->team_set_id = $this->getTargetTeamSetId($stage, $template, $parent);

            if ($activity->setAssignmentSummary === true) {
                $activity->save();
            } else {
                $activity->save($stage->checkActivityNotify($activity));
            }

            if ($stage->checkActivityNotify($activity) &&
                $activity->setAssignmentSummary === true &&
                $activity->module_dir === 'Tasks') {
                $sharedDataObj = new CJSharedData\SharedData();
                $sharedData = $sharedDataObj->getData('assignment_summary');
                $sharedData[$activity->assigned_user_id][] = [
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name,
                    'module_name' => $activity->module_dir,
                ];
                $sharedDataObj->setData('assignment_summary', $sharedData);
            }
        } catch (CJException\ParentNotFoundException $e) {
            $GLOBALS['log']->debug('Sugar Automate', __FILE__ . ' ' . __LINE__, $e->getMessage());
        }
    }

    /**
     * load the relationship
     *
     * @param \SugarBean $bean
     * @param string $linkName
     * @throws \SugarApiExceptionError
     */
    public function loadRelationship(\SugarBean $bean, $linkName)
    {
        $bean->load_relationship($linkName);

        if (!($bean->{$linkName} instanceof \Link2)) {
            throw new \SugarApiExceptionError(sprintf('unable to load link: %s', $linkName));
        }
    }

    /**
     * Return the link name
     *
     * @return string
     */
    public function getLinkName()
    {
        return $this->linkName;
    }

    /**
     * Add the ids in contacts_arr and users_arr
     * of Calls/Meetings record so that in
     * default function, they will automatically
     * for notifications sending
     * @param \SugarBean $activity
     */
    public function prepareAndSetNotificationRecipients(\SugarBean $activity)
    {
        $inviteesIDs = [];
        if (empty($activity)) {
            return;
        }
        $module = $activity->getModuleName();
        if ($module === 'Meetings') {
            $inviteesIDs = $this->getNotificationRecipients($activity->id, 'contacts', 'users', 'meeting_id', $module);
        } elseif ($module === 'Calls') {
            $inviteesIDs = $this->getNotificationRecipients($activity->id, 'contacts', 'users', 'call_id', $module);
        }

        $activity->contacts_arr = !empty($inviteesIDs['contacts_arr']) ? $inviteesIDs['contacts_arr'] : [];
        $activity->users_arr = !empty($inviteesIDs['users_arr']) ? $inviteesIDs['users_arr'] : [];
    }

    /**
     * Get the Invitees of Meetings/Calls from
     * middle table
     * @param string $record_id
     * @param string $table_contacts
     * @param string $table_users
     * @param string $where_field
     * @return array $inviteesIDs
     */
    private function getNotificationRecipients($recordId, $contactLink, $usersLink, $whereField, $moduleName)
    {
        if (empty($recordId) || empty($contactLink) || empty($usersLink) || empty($whereField)) {
            return;
        }

        $inviteesIDs = [
            'users_arr' => [],
            'contacts_arr' => [],
        ];

        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean($moduleName), ['team_security' => false]);
        $query->where()->equals('id', $recordId);
        $contactJoin = $query->join($contactLink)->joinName();
        $userJoin = $query->join($usersLink)->joinName();
        $query->select([["$contactJoin.id", 'contact_id'], ["$userJoin.id", 'user_id']]);
        $rows = $query->execute();
        foreach ($rows as $row) {
            $inviteesIDs['contacts_arr'][$row['contact_id']] = $row['contact_id'];
            $inviteesIDs['users_arr'][$row['user_id']] = $row['user_id'];
        }
        return $inviteesIDs;
    }

    /**
     * This function sets due date for the activity
     * @param \DRI_Workflow_Task_Template $template
     * @param \SugarBean $activity
     * @param \DateTime $date
     * @param string $targetDate its value is either date_start or date_due
     */
    public function createAndSetStartDueDate(\DRI_Workflow_Task_Template $template, \SugarBean $activity, \DateTime $date, string $targetDate)
    {
        $timeDate = \TimeDate::getInstance();
        $save = false;
        $taskDays = [
            'date_start' => 'task_start_days',
            'date_due' => 'task_due_days',
        ];
        $taskTargetDays = $taskDays[$targetDate]; //task_start_days or task_due_days
        if (!in_array($activity->getModuleName(), $this->appointmentModules)) {
            $activity->$targetDate = $this->getStartDueDate($template, $date, $taskTargetDays);
            $save = true;
        } else {
            $dateClone = clone $date;
            // set correct timezone
            $timeDate->tzUser($dateClone);
            // add the days $template->task_due_days
            if ($template->$taskTargetDays > 0) {
                $dateClone->modify(sprintf('+ %d days', $template->$taskTargetDays));
            } else {
                $dateClone->modify(sprintf('- %d days', abs($template->$taskTargetDays)));
            }

            // set time
            [$hour, $minute] = explode(':', $template->time_of_day);
            $dateClone->setTime((int)$hour, (int)$minute, 0);

            if ($targetDate === 'date_start') {
                $activity->date_start = $timeDate->asUser($dateClone);
            } elseif ($targetDate === 'date_due') {
                $this->setDates($template, $activity, $dateClone);
            }
            $save = true;
        }
        return $save;
    }

    /**
     * Return the Start Date of Task
     *
     * @param \DRI_Workflow_Task_Template $template
     * @param \DateTime $date
     * @return string
     */
    private function getStartDueDate(\DRI_Workflow_Task_Template $template, \DateTime $date, $taskTargetDays)
    {
        $timeDate = \TimeDate::getInstance();
        $dateClone = clone $date;

        if (isset($template) && isset($template->$taskTargetDays)) {
            if ($template->$taskTargetDays > 0) {
                $dateClone->modify(sprintf('+ %s days', $template->$taskTargetDays));
            } elseif ($template->$taskTargetDays < 0) {
                $dateClone->modify(sprintf('- %s days', -$template->$taskTargetDays));
            }
        } else {
            if (!isset($template)) {
                $GLOBALS['log']->fatal('ActivityHelper:getStartDueDate Template is undefined');
            } elseif (!isset($template->$taskTargetDays)) {
                $GLOBALS['log']->fatal('ActivityHelper:getStartDueDate target days are undefined');
            }
        }

        return $timeDate->asUser($dateClone);
    }

    /**
     * @param \DRI_Workflow_Task_Template $template
     * @param \SugarBean $activity
     * @param \DateTime $date
     */
    public function createDueDateInPercentageTimeframe(\DRI_Workflow_Task_Template $template, \SugarBean $activity, \DateTime $date)
    {
        $timeDate = \TimeDate::getInstance();

        $due_date = $this->activityDatesHelper->setDueDateInPercentageTimeFrame($template, $activity, $date);
        if (!in_array($activity->getModuleName(), $this->appointmentModules)) {
            $activity->date_due = $timeDate->asUser($due_date);
        } else {
            [$hour, $minute] = explode(':', $template->time_of_day);
            $due_date->setTime((int)$hour, (int)$minute, 0);

            $this->setDates($template, $activity, $due_date);
        }
        return true;
    }

    /**
     * Set start and end dates for activity
     * @param \DRI_Workflow_Task_Template $template
     * @param \SugarBean $activity
     * @param $dueDate
     */
    private function setDates(\DRI_Workflow_Task_Template $template, \SugarBean $activity, \DateTime $dueDate)
    {
        $timeDate = \TimeDate::getInstance();

        // create end date
        $endDate = clone $dueDate;
        $startDate = $timeDate->fromString($activity->date_start);

        $diffHours = 0;
        if ($startDate === null) {
            $GLOBALS['log']->fatal('ActivityHelper:setDates start date is invalid');
        } else {
            //calculate the difference between start date and end date
            $interval = $endDate->diff($startDate);

            if ($interval->invert && ($endDate !== $this->getEmptyStartDate())) { // if difference is positive and due date is not empty
                if ($interval->d > 0 || $interval->m > 0 || $interval->y > 0) {
                    $diffHours = ($interval->format('%a')) * 24;
                }
                if ($interval->h > 0 && $template->due_date_criteria === 'Percentage') {
                    $diffHours = $diffHours + $interval->h;
                }
            }
        }

        // add duration hours
        $durationHours = (int)$template->duration_hours;
        if ($durationHours > 0) {
            $endDate->modify(sprintf('+ %s hours', $durationHours));
        }

        // add duration minutes
        $durationMinutes = (int)$template->duration_minutes;
        if ($durationMinutes > 0) {
            $endDate->modify(sprintf('+ %s minutes', $durationMinutes));
        }

        $totalHours = $diffHours + $template->duration_hours;

        // format and populate data
        $activity->date_end = $timeDate->asUser($endDate);
        $activity->duration_hours = $totalHours;
        $activity->duration_minutes = $template->duration_minutes;
    }

    /**
     * The empty start date for calls/meetings is a date far ahead in the future
     *
     * @return \DateTime
     */
    public function getEmptyStartDate()
    {
        return new \SugarDateTime('2100-01-01 12:00:00');
    }

    /**
     * Check if Start Date is greater than Due Date then set the Start Date as same as Due Date
     * @param \SugarBean $activity
     */
    public function setStartDateAsEndDate(\SugarBean $activity)
    {
        $dueDateVariable = '';
        if (in_array($activity->getModuleName(), $this->appointmentModules)) {
            $dueDateVariable = 'date_end';
        } else {
            $dueDateVariable = 'date_due';
        }

        if (empty($dueDateVariable)) {
            return; //stodo: exception can be thrown here as well
        }

        $timeDate = \TimeDate::getInstance();

        $startDate = 0;
        $endDate = 0;

        $startDateString = $timeDate->fromString($activity->date_start);
        if (!empty($startDateString)) {
            $startDate = $timeDate->asDB($startDateString);
        }

        $endDateString = $timeDate->fromString($activity->$dueDateVariable);
        if (!empty($endDateString)) {
            $endDate = $timeDate->asDB($endDateString);
        }

        if (!empty($activity->date_start) && !empty($activity->$dueDateVariable) &&
            strtotime($startDate) > strtotime($endDate)) {
            $activity->date_start = $activity->$dueDateVariable;
        }
    }

    /**
     * Calculate the difference between Start Date and Due Date in hours
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $template
     */
    public function calculateDiffBetweenStartDateAndEndDateInHours(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        $timeDate = \TimeDate::getInstance();

        $startDate = $timeDate->fromString($activity->date_start);
        $endDate = $timeDate->fromString($activity->date_end);
        $emptyEndDate = $timeDate->split_date_time($timeDate->asDb($this->getEmptyStartDate()));
        $endDateValue = $timeDate->split_date_time($activity->date_end);

        if ($startDate === null || $endDate === null) {
            $GLOBALS['log']->fatal(
                'ActivityHelper:calculateDiffBetweenStartDateAndEndDateInHours start/end dates are invalid'
            );
            return;
        }

        //If due date was not empty then its means its was already set so it should retain
        // for this calculate the difference between start date and due date in hours
        if (isset($endDate) && isset($startDate) && $emptyEndDate[0] !== $endDateValue[0]) {
            //calculate the difference between start date and end date
            $interval = $endDate->diff($startDate);
            $diffHours = 0;

            if ($interval->invert) { // if difference is positive
                if ($interval->d > 0 || $interval->m > 0 || $interval->y > 0) {
                    $diffHours = ($interval->format('%a')) * 24;
                }
            }
            $totalHours = $diffHours + $template->duration_hours;
            $activity->duration_hours = $totalHours;
        } elseif (!isset($endDate)) {
            $GLOBALS['log']->fatal('ActivityHelper:calculateDiffBetweenStartDateAndEndDateInHours end date is undefined');
        } elseif (!isset($startDate)) {
            $GLOBALS['log']->fatal('ActivityHelper:calculateDiffBetweenStartDateAndEndDateInHours start date is undefined');
        }
    }

    /**
     * Add the ids in contacts_arr and users_arr
     * from the guests field of activity template
     * and send_invites = true
     * so that system can send email on save
     * automatically
     *
     * @param \SugarBean $activity
     * @param \SugarBean $activityTemplate
     * @param \SugarBean $parent
     */
    public function prepareInviteesAndSetNotificationEmailFlag(\SugarBean $activity, $activityTemplate, $parent)
    {
        if (empty($activityTemplate->select_to_guests)) {
            return;
        }

        $recipientsInfo = $this->getRecipientsForInvitees($activityTemplate, $parent, $activity);
        $activity->send_invites = $activityTemplate->send_invite_type === \DRI_Workflow_Task_Template::SEND_INVITES_CREATE;
        foreach ($recipientsInfo as $module => $ids) {
            if ($module === 'Contacts') {
                $activity->contacts_arr = $ids;
            } elseif ($module === 'Users') {
                $activity->users_arr = $ids;
            }
        }
    }

    /**
     * Return the recipients info for
     * Calls and Meetings activities from the
     * guests field of Activity Templates
     *
     * @param \SugarBean $activityTemplate
     * @param \SugarBean $parentRecord
     * @param \SugarBean $activity
     * @return array
     */
    public function getRecipientsForInvitees($activityTemplate, $parentRecord, \SugarBean $activity)
    {
        if (empty($activityTemplate->select_to_guests)) {
            return;
        }

        if (empty($parentRecord)) {
            $parentRecord = SelectToOption::getParentRecord($activity);
        }

        return SelectToOption::getRecipients($activityTemplate->select_to_guests, $parentRecord);
    }

    /**
     * Add the invitees in Calls and Meetings from the
     * guests field of Activity Templates. if $force
     * flag is true then it will fetch the fetch the
     * guests again else use the contacts_arr and
     * users_arr of $activity
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parentRecord
     * @param boolean $force
     */
    public function addInvitees($activity, $activityTemplate = null, $parentRecord = null, $force = false)
    {
        if ($force && !empty($activityTemplate) && !empty($activityTemplate->select_to_guests)) {
            $recipientsInfo = SelectToOption::getRecipients($activityTemplate->select_to_guests, (empty($parentRecord)) ? SelectToOption::getParentRecord($activity) : $parentRecord);
            foreach ($recipientsInfo as $module => $ids) {
                if ($module === 'Contacts') {
                    $activity->setContactInvitees($ids);
                } elseif ($module === 'Users') {
                    $activity->setUserInvitees($ids);
                }
            }
        } else {
            if (!empty($activity->contacts_arr)) {
                $activity->setContactInvitees($activity->contacts_arr);
            }
            if (!empty($activity->users_arr)) {
                $activity->setUserInvitees($activity->users_arr);
            }
        }
    }

    /**
     * Checks if the status of this activity is read-only or not
     *
     * @param \SugarBean $activity
     * @return boolean true|false
     */
    public function isStatusReadOnly($activity)
    {
        $statusFieldDef = $activity->getFieldDefinitions()['status'];
        if (isset($statusFieldDef['readonly']) && $statusFieldDef['readonly'] === true) {
            if (!isset($statusFieldDef['readonly_formula']) || empty($statusFieldDef['readonly_formula'])) {
                return true;
            }
            $result = \Parser::evaluate($statusFieldDef['readonly_formula'], $activity)->evaluate();
            if ($result === \AbstractExpression::$TRUE) {
                return true;
            }
        }
        return false;
    }
}
