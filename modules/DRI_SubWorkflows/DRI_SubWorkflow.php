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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks as ActivitiesHooks;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean as CJBean;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\SharedData as CJSharedData;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage\ProgressCalculator as StageProgressCalculator;

class DRI_SubWorkflow extends Basic
{
    public const STATE_NOT_STARTED = 'not_started';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_NOT_COMPLETED = 'not_completed';
    public const STATE_COMPLETED = 'completed';
    public const STATE_CANCELLED = 'cancelled';

    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'DRI_SubWorkflows';
    public $object_name = 'DRI_SubWorkflow';
    public $table_name = 'dri_subworkflows';
    public $importable = false;
    public $team_id;
    public $team_set_id;
    public $team_count;
    public $team_name;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $state;
    public $progress;
    public $points;
    public $score;
    public $sort_order;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $label;
    public $activities;
    public $following;
    public $following_link;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $locked_fields;
    public $locked_fields_link;
    public $acl_team_set_id;
    public $acl_team_names;
    public $date_started;
    public $date_completed;
    public $momentum_ratio;
    public $momentum_points;
    public $momentum_score;

    /**
     * @var Link2
     */
    public $dri_workflows;
    public $dri_workflow_id;
    public $dri_workflow_name;

    /**
     * @var Link2
     */
    public $dri_workflow_link;
    public $dri_subworkflow_template_id;
    public $dri_subworkflow_template_name;

    /**
     * @var Link2
     */
    public $dri_subworkflow_template_link;

    /**
     * @var Link2
     */
    public $tasks;

    /**
     * @var Link2
     */
    public $meetings;

    /**
     * @var Link2
     */
    public $calls;

    /**
     * @var SugarBean[]
     */
    private $activitiesCache = [];

    /**
     * @var string
     */
    private $assigneeRule;

    /**
     * @var string
     */
    private $targetAssigneeId;

    /**
     * @var string
     */
    private $targetAssignee;

    /**
     * @var string
     */
    private $targetTeamId;

    /**
     * @var string
     */
    private $targetTeamSetId;

    /**
     * @var bool
     */
    public $created_from_journey = false;

    /**
     * @var DRI_Workflow
     */
    private $journeyCache;


    /**
     * Retrieves a DRI_SubWorkflow with id $id and
     * returns a instance of the retrieved bean
     *
     * @param string $id : the id of the DRI_SubWorkflow that should be retrieved
     * @return DRI_SubWorkflow
     * @throws NotFoundException: if not found
     */
    public static function getById($id)
    {
        return CJBean\Repository::getInstance()->getById('DRI_SubWorkflows', $id);
    }

    /**
     * Get the record by the combination of the Cycle Id and Name
     *
     * @param string $cycleId
     * @param string $name
     * @param string $skipId
     * @return DRI_SubWorkflow
     * @throws NotFoundException
     */
    public static function getByCycleIdAndName($cycleId, $name, $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByCycleIdAndName(
            [
                'module' => 'DRI_SubWorkflows',
                'cycleId' => $cycleId,
                'name' => $name,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * Get the record by the combination of the Cycle Id and Order
     *
     * @param string $cycleId
     * @param string $order
     * @param string $skipId
     * @return DRI_SubWorkflow
     * @throws NotFoundException
     */
    public static function getByCycleIdAndOrder($cycleId, $order, $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByCycleIdAndOrder(
            [
                'module' => 'DRI_SubWorkflows',
                'cycleId' => $cycleId,
                'order' => $order,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * Calculates the progress of the
     * subworkflow based on the related tasks
     */
    private function calculateProgress()
    {
        $calculator = new StageProgressCalculator($this);
        $calculator->calculate();
    }

    /**
     * Check if stage is completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->state === DRI_SubWorkflow::STATE_COMPLETED;
    }

    /**
     * Check if stage is cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->state === DRI_SubWorkflow::STATE_CANCELLED;
    }

    /**
     * Check if stages later than this stage are started
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function hasStartedLaterStages()
    {
        $stages = $this->getJourney()->getStages();

        foreach ($stages as $stage) {
            if ($stage->sort_order > $this->sort_order && in_array($stage->state, [self::STATE_IN_PROGRESS, self::STATE_COMPLETED])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any other stage has same order
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isDuplicateStageByOrder()
    {
        $stages = $this->getJourney()->getStages();

        foreach ($stages as $stage) {
            if ($stage->sort_order === $this->sort_order) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all the previous stages are completed
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isAllPreviousStagesCompleted()
    {
        $stages = $this->getJourney()->getStages();

        foreach ($stages as $stage) {
            if ($stage->sort_order < $this->sort_order && $stage->state !== self::STATE_COMPLETED) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if next stage is started
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isNextStageStarted()
    {
        $stages = $this->getJourney()->getStages();

        foreach ($stages as $stage) {
            if ($stage->sort_order > $this->sort_order) {
                return $stage->state !== self::STATE_NOT_STARTED;
            }
        }

        return true;
    }

    /**
     * Check if first stage is started
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isFirstStage()
    {
        $stages = $this->getJourney()->getStages();
        $first = array_shift($stages);
        return $first->id === $this->id;
    }

    /**
     * Check if this is the last stage
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isLastStage()
    {
        $stages = $this->getJourney()->getStages();
        $last = array_pop($stages);
        return $last->id === $this->id;
    }

    /**
     * Check if field is changed
     *
     * @param string $name
     * @return bool
     */
    public function isFieldChanged($name)
    {
        return CJBean\Repository::getInstance()->isFieldChanged($this, $name);
    }

    /**
     * Update the order of this stage if order is duplicate
     */
    public function moveDuplicatedStageForward()
    {
        $this->reorderSortOrdersAndLabels($this->dri_workflow_id, 'update_order');
    }

    /**
     * Check if stage should be started
     *
     * @return bool
     */
    public function shouldStart()
    {
        return $this->changedToInProgress() || $this->skippedToCompleted() || $this->changedToNotCompleted();
    }

    /**
     * Send the webhooks
     *
     * @param $trigger_event
     * @return null
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiException
     * @throws SugarQueryException
     */
    public function sendWebHooks($trigger_event)
    {
        if (!$this->hasTemplate()) {
            return;
        }

        $template = $this->getTemplate();
        try {
            $parent = $this->getParent();
        } catch (CJException\ParentNotFoundException $e) {
            throw $e;
        }

        $journey = $this->getJourney();

        $template->sendWebHooks($trigger_event, [
            'parent_module' => (is_null($parent)) ? $parent : $parent->module_dir,
            'parent' => (is_null($parent)) ? $parent : $parent->toArray(true),
            'journey' => $journey->toArray(true),
            'stage' => $this->toArray(true),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $isNew = !$this->isUpdate();

        if ($isNew) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_BEFORE_CREATE);
        }

        if ($isNew && $this->isDuplicateStageByOrder() && !$this->created_from_journey) {
            $this->moveDuplicatedStageForward();
        }

        if ($this->hasTemplate() && $isNew && !$this->created_from_journey) {
            BeanFactory::registerBean($this);
            $this->createActivitiesFromTemplate(false);
            BeanFactory::unregisterBean($this);
        }

        $this->validateUniqueName();
        $this->setLabel();

        $start = $this->shouldStart();

        // when the bean is new the progress should be 0 until the activities is created
        if ($isNew) {
            $this->progress = 0;
            $this->score = 0;
        } else {
            $this->calculateProgress();
        }

        if ($start) {
            $this->start();
        }

        if ($this->state === self::STATE_COMPLETED && empty($this->date_completed)) {
            $this->date_completed = \TimeDate::getInstance()->now();
        }

        if ($this->state === self::STATE_IN_PROGRESS && empty($this->date_started)) {
            $this->date_started = \TimeDate::getInstance()->now();
        }

        $changedToCompleted = $this->changedToCompleted();
        $changedToInProgress = $this->changedToInProgress();

        if ($changedToInProgress) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_BEFORE_IN_PROGRESS);
        }

        if ($changedToCompleted) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_BEFORE_COMPLETED);
        }

        $return = parent::save($check_notify);

        if ($start) {
            $this->startActivities($changedToInProgress);
        }

        if ($isNew && !$this->created_from_journey) {
            BeanFactory::registerBean($this);
            $journey = $this->getJourney();
            $journey->reloadStages();
            $journey->save();
            BeanFactory::unregisterBean($this);
        }

        if ($changedToInProgress) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_IN_PROGRESS);
        }

        if ($changedToCompleted) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_COMPLETED);
        }

        if ($isNew) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_CREATE);
        }

        return $return;
    }

    /**
     * Check if state is changed
     *
     * @param $state
     * @return bool
     */
    private function stateChangedTo($state)
    {
        return $this->isFieldChanged('state') && $this->state === $state;
    }

    /**
     * Check if stage state is changed to in progress
     *
     * @return bool
     */
    public function changedToInProgress()
    {
        return $this->stateChangedTo(self::STATE_IN_PROGRESS);
    }

    /**
     * Check if stage state is changed to not completed
     *
     * @return bool
     */
    public function changedToNotCompleted()
    {
        return $this->stateChangedTo(self::STATE_NOT_COMPLETED);
    }

    /**
     * Check if stage state is changed to completed
     *
     * @return bool
     */
    public function changedToCompleted()
    {
        return $this->stateChangedTo(self::STATE_COMPLETED);
    }

    /**
     * Check if stage state is directly changed to completed
     *
     * @return bool
     */
    public function skippedToCompleted()
    {
        $fetched_row_value = false;
        if (is_array($this->fetched_row) && isset($this->fetched_row['state'])) {
            $fetched_row_value = $this->fetched_row['state'];
        }

        return $this->stateChangedTo(self::STATE_COMPLETED) && $fetched_row_value === self::STATE_NOT_STARTED;
    }

    /**
     * Start the stage
     */
    private function start()
    {
        if ($this->getAssigneeRule() === \DRI_Workflow_Template::ASSIGNEE_RULE_STAGE_START) {
            $this->assigned_user_id = $this->getTargetAssigneeId();
            $this->team_id = $this->getTargetTeamId();
            $this->team_set_id = $this->getTargetTeamSetId();
        }
    }

    /**
     * Start the activities linked with the stage
     */
    private function startActivities($changedToInProgress)
    {
        ActivitiesHooks\ActivityHooksHelper::setInternalSave(true);

        $sharedDataObj = new CJSharedData\SharedData();
        $sharedData = $sharedDataObj->getData('assignment_summary');

        foreach ($this->getActivities() as $activity) {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);
            BeanFactory::registerBean($this->getJourney());
            BeanFactory::registerBean($this);
            $handler->setStage($this);

            if ($changedToInProgress) {
                // temp store value in a bean so that in 'start'
                // function of AbstractAppointmentHandler, we can
                // check this and send the notifications to users
                // on the base of send_invite_type field of template
                $activity->stageIsNowInProgress = true;
            }

            if (!$activity->deleted && true === $handler->start($activity)) {
                $check_notify = in_array($activity->getModuleName(), ['Meetings', 'Calls']);
                $activity->save($check_notify);

                if ($this->checkActivityNotify($activity) && $activity->module_dir === 'Tasks') {
                    $sharedData[$activity->assigned_user_id][] = [
                        'activity_id' => $activity->id,
                        'activity_name' => $activity->name,
                        'module_name' => $activity->module_dir,
                    ];
                }
            }

            BeanFactory::unregisterBean($activity);
        }

        $sharedDataObj->setData('assignment_summary', $sharedData);
        ActivitiesHooks\ActivityHooksHelper::setInternalSave(false);
    }

    /**
     * Get the assignee rule
     * From journey if not defined for the stage
     *
     * @return string
     * @throws NotFoundException
     */
    public function getAssigneeRule()
    {
        if (!$this->assigneeRule) {
            $this->assigneeRule = $this->getJourney()->getAssigneeRule();
        }

        return $this->assigneeRule;
    }

    /**
     * Get the target assignee Id
     * From journey if not defined for the stage
     *
     * @return string
     * @throws NotFoundException
     */
    public function getTargetAssigneeId()
    {
        if (!$this->targetAssigneeId) {
            try {
                $this->targetAssigneeId = $this->getJourney()->getTargetAssigneeId();
            } catch (CJException\ParentNotFoundException $e) {
                $this->targetAssigneeId = null;
            }
        }

        return $this->targetAssigneeId;
    }

    /**
     * Get the target assignee
     * From journey if not defined for the stage
     *
     * @return string
     * @throws NotFoundException
     */
    public function getTargetAssignee()
    {
        if (!$this->targetAssignee) {
            $this->targetAssignee = $this->getJourney()->getTargetAssignee();
        }

        return $this->targetAssignee;
    }

    /**
     * Get the target team Id
     * From journey if not defined for the stage
     *
     * @return string
     * @throws NotFoundException
     */
    public function getTargetTeamId()
    {
        if (!$this->targetTeamId) {
            try {
                $this->targetTeamId = $this->getJourney()->getTargetTeamId();
            } catch (CJException\ParentNotFoundException $e) {
                $this->targetTeamId = null;
            }
        }

        return $this->targetTeamId;
    }

    /**
     * Get the target team set Id
     * From journey if not defined for the stage
     * @return string|null
     * @throws NotFoundException
     */
    public function getTargetTeamSetId()
    {
        if (!$this->targetTeamSetId) {
            try {
                $this->targetTeamSetId = $this->getJourney()->getTargetTeamSetId();
            } catch (CJException\ParentNotFoundException $e) {
                $this->targetTeamSetId = null;
            }
        }

        return $this->targetTeamSetId;
    }

    /**
     * Set the label
     */
    private function setLabel()
    {
        $journey = $this->getJourney();
        if (!$journey->stage_numbering) {
            $order = $this->sort_order;
            if (strlen($order) === 1) {
                $order = "0{$order}";
            }

            $this->label = sprintf('%s. %s', $order, $this->name);
        } else {
            $this->label = $this->name;
        }
    }

    /**
     * Check if the stage with same name already exists
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    private function validateUniqueName()
    {
        try {
            self::getByCycleIdAndName($this->dri_workflow_id, $this->name, $this->id);
            throw new SugarApiExceptionInvalidParameter(sprintf('Another Stage in this Smart Guide is already named %s.', $this->name));
        } catch (CJException\CustomerJourneyException $e) {
        }
    }

    /**
     * Populates the data from the stage template
     *
     * @param DRI_SubWorkflow_Template $template
     */
    public function populateFromTemplate(DRI_SubWorkflow_Template $template)
    {
        $this->dri_subworkflow_template_id = $template->id;
        $this->dri_subworkflow_template_name = $template->name;
        $this->name = $template->name;
        $this->sort_order = $template->sort_order;
        $this->description = $template->description;
        $this->start_next_journey_id = $template->start_next_journey_id;
        $this->points = $template->points ?: 0;
    }

    /**
     * Populates the data from the journey
     *
     * @param DRI_Workflow $journey
     * @throws NotFoundException
     */
    public function populateFromJourney(DRI_Workflow $journey)
    {
        $this->dri_workflow_id = $journey->id;

        if ($this->getAssigneeRule() === \DRI_Workflow_Template::ASSIGNEE_RULE_CREATE) {
            $this->assigned_user_id = $this->getTargetAssigneeId();
            $this->team_id = $this->getTargetTeamId();
            $this->team_set_id = $this->getTargetTeamSetId();
        }
    }

    /**
     * Creates tasks based on the task templates
     * linked to the related subworkflow template
     *
     * @param bool $start
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarQueryException
     */
    public function createActivitiesFromTemplate($start)
    {
        $activities = [];
        $template = $this->getTemplate();
        $parent = $this->getParent();

        ActivitiesHooks\ActivityHooksHelper::setInternalSave(true);

        $sharedDataObj = new CJSharedData\SharedData();
        $sharedData = $sharedDataObj->getData('assignment_summary');

        foreach ($template->getActivityTemplates() as $activityTemplate) {
            $handler = ActivityHandlerFactory::factory($activityTemplate->activity_type);
            $handler->setStage($this);
            $activity = $handler->createFromTemplate($activityTemplate, $this, $parent);

            if ($start) {
                $handler->start($activity);
            }

            $handler->beforeCreate($activity, $parent);
            $activity->do_not_reset_template_id = true;

            $activity->save();
            if (method_exists($handler, 'addInvitees')) {
                $handler->addInvitees($activity);
            }

            if ($this->checkActivityNotify($activity) && $activity->module_dir === 'Tasks') {
                $sharedData[$activity->assigned_user_id][] = [
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name,
                    'module_name' => $activity->module_dir,
                ];
            }

            $activities[] = $activity;

            $handler->afterCreate($activity, $parent);

            if ($activityTemplate->is_parent) {
                foreach ($activityTemplate->getChildren() as $childTemplate) {
                    $handler = ActivityHandlerFactory::factory($childTemplate->activity_type);
                    $handler->setStage($this);
                    $child = $handler->createFromTemplate($childTemplate, $this, $parent);
                    $handler->populateFromParentActivity($child, $activity);

                    if ($start) {
                        $handler->start($child);
                    }

                    $handler->beforeCreate($child, $parent);
                    $child->save();
                    if (method_exists($handler, 'addInvitees')) {
                        $handler->addInvitees($child);
                    }

                    if ($this->checkActivityNotify($child) && $child->module_dir === 'Tasks') {
                        $sharedData[$child->assigned_user_id][] = [
                            'activity_id' => $child->id,
                            'activity_name' => $child->name,
                            'module_name' => $child->module_dir,
                        ];
                    }

                    $handler->afterCreate($child, $parent);

                    BeanFactory::unregisterBean($child);
                }
            }
            BeanFactory::unregisterBean($activity);
        }

        $sharedDataObj->setData('assignment_summary', $sharedData);

        ActivitiesHooks\ActivityHooksHelper::setInternalSave(false);

        $this->setActivities($activities);
    }

    /**
     * Retrieves the next activity in the stage after a given activity
     *
     * @param \SugarBean $activity
     * @return \SugarBean|false
     */
    public function getNextActivity(\SugarBean $activity)
    {
        $aHandler = ActivityHandlerFactory::factory($activity->module_dir);

        foreach ($this->getActivities() as $next) {
            $bHandler = ActivityHandlerFactory::factory($next->module_dir);
            if (!$next->deleted && (int)$bHandler->getSortOrder($next) > (int)$aHandler->getSortOrder($activity)) {
                return $next;
            }
        }

        return false;
    }

    /**
     * Get the first activity
     *
     * @return \SugarBean|false
     */
    public function getFirstActivity()
    {
        $activities = $this->getActivities();
        return array_shift($activities);
    }

    /**
     * Get the stage template for the current stage
     *
     * @return DRI_SubWorkflow_Template
     * @throws NotFoundException
     */
    public function getTemplate()
    {
        return DRI_SubWorkflow_Template::getById($this->dri_subworkflow_template_id);
    }

    /**
     * Check if stage template exists
     *
     * @return bool
     */
    public function hasTemplate()
    {
        return !empty($this->dri_subworkflow_template_id);
    }

    /**
     * Get the parent
     *
     * @param string $module
     * @return SugarBean
     * @throws ParentNotFoundException
     * @throws NotFoundException
     */
    public function getParent($module = null)
    {
        return $this->getJourney()->getParent($module);
    }

    /**
     * Set the journey to the cache
     *
     * @param DRI_Workflow $journey
     */
    public function setJourney(DRI_Workflow $journey)
    {
        $this->journeyCache = $journey;
    }

    /**
     * Get the journey either from cache or get it by Id
     *
     * @return DRI_Workflow
     * @throws NotFoundException
     */
    public function getJourney()
    {
        if (is_null($this->journeyCache)) {
            $this->journeyCache = DRI_Workflow::getById($this->dri_workflow_id);
        }

        return $this->journeyCache;
    }

    /**
     * Add the activity to the cache
     *
     * @param SugarBean $activity
     */
    public function insertActivity(\SugarBean $activity)
    {
        foreach ($this->getActivities() as $i => $potential) {
            if ($potential->id === $activity->id) {
                $this->activitiesCache[$potential->id] = $activity;
                break;
            }
        }
    }

    /**
     * Check if this stage has the given activity
     *
     * @param SugarBean $activity
     * @return bool
     */
    public function hasActivity(\SugarBean $activity)
    {
        foreach ($this->getActivities() as $potential) {
            if ($potential->id === $activity->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add the given activites to the cache
     *
     * @param array $activities
     */
    private function setActivities(array $activities)
    {
        foreach ($activities as $activity) {
            $this->activitiesCache[$activity->id] = $activity;
        }
    }

    /**
     * Update the given activity in the cache
     *
     * @param $activity
     */
    public function setActivity($activity)
    {
        $this->activitiesCache[$activity->id] = $activity;
    }

    /**
     * Set the loaded activities to the cache
     *
     * @param array $activities
     */
    public function setLoadedActivities(array $activities)
    {
        $activities = $this->sortActivities($activities);
        $this->setActivities($activities);
    }

    /**
     * Load the activities
     * Either from cache array or from the handler
     */
    public function loadActivities()
    {
        if (is_null($this->activitiesCache) || !safeCount((array)$this->activitiesCache)) {
            $activities = [];

            foreach (ActivityHandlerFactory::all() as $activityHandler) {
                $activities = array_merge($activities, $activityHandler->load($this));
            }

            $this->setLoadedActivities($activities);
        }
    }

    /**
     * Reloads activities
     */
    public function reloadActivities()
    {
        $this->activitiesCache = [];
        $this->loadActivities();
    }

    /**
     * Get the activities
     *
     * @return \SugarBean[]
     */
    public function getActivities()
    {
        $this->loadActivities();

        return array_values($this->activitiesCache);
    }

    /**
     * Since all php functions that sorts an array based on a function is blacklisted by the package scanner
     * we have to implement our own algorithm, this is based on quicksort
     *
     * @param \SugarBean[] $activities
     * @return array
     */
    private function sortActivities($activities)
    {
        $leastActivitiesCount = 2;
        if (safeCount($activities) < $leastActivitiesCount) {
            return $activities;
        }

        $left = $right = [];
        $pivot_key = array_key_first($activities);
        $pivotActivity = array_shift($activities);
        $pivot = (int)ActivityHandlerFactory::factory($pivotActivity->module_dir)->getSortOrder($pivotActivity);

        foreach ($activities as $k => $activity) {
            $order = (int)ActivityHandlerFactory::factory($activity->module_dir)->getSortOrder($activity);
            if ($order < $pivot) {
                $left[$k] = $activity;
            } else {
                $right[$k] = $activity;
            }
        }

        return array_merge(
            $this->sortActivities($left),
            [$pivot_key => $pivotActivity],
            $this->sortActivities($right)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $return = parent::retrieve($id, $encode, $deleted);
        $this->activitiesCache = [];
        return $return;
    }

    /**
     * Removes all related tasks on delete
     *
     * @param string $id
     */
    public function mark_deleted($id)
    {
        if ($this->id !== $id) {
            $this->retrieve($id);
        }

        $activities = $this->getActivities();

        try {
            $journey = $this->getJourney();
        } catch (\Exception $e) {
            $journey = null;
        }

        foreach ($activities as $activity) {
            $activity->mark_deleted($activity->id);
        }

        parent::mark_deleted($id);

        if (!is_null($journey) && !empty($journey->id) && !$journey->deleted) {
            $this->reorderSortOrdersAndLabels($journey->id);
            $journey = $this->getJourney();
            $journey->reloadStages();
            $journey->save();
        }
        $this->reloadActivities();
    }

    /**
     * Check if notifications can be send for the activity
     *
     * @param SugarBean $activity
     * @return bool
     */
    public function checkActivityNotify(SugarBean $activity)
    {
        if (isset($activity->assigned_user_id) && !empty($activity->assigned_user_id) && $activity->assigned_user_id !== $GLOBALS['current_user']->id) {
            $notify_user = BeanFactory::retrieveBean('Users', $activity->assigned_user_id);

            //User is able to receive notifications
            if (!empty($notify_user->id) && $notify_user->receive_notifications) {
                $admin = Administration::getSettings();
                if ($admin->settings['notify_on']) { //Assignment Notification setting is also on in system
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * After delete the stage, re-order the sort orders and labels of all the stages
     */
    public function reorderSortOrdersAndLabels($workflow_id, $defaultSortOrderOperation = 'minus')
    {
        $stages = DRI_Workflow::getById($workflow_id)->getStages();
        CJBean\Repository::getInstance()->reorderSortOrdersAndLabels($this, $stages, $defaultSortOrderOperation, $this->getTableName());
    }
}
