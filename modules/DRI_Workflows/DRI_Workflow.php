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
use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean as CJBean;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\SharedData as CJSharedData;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey as Journey;

class DRI_Workflow extends Basic
{
    public const STATE_NOT_STARTED = 'not_started';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_COMPLETED = 'completed';
    public const STATE_CANCELLED = 'cancelled';
    /**
     * DO NOT USE!
     *
     * old constants, we must keep this for some time
     * since older versions may be use these in cache files
     */
    public const ASSIGN_TO_CURRENT_USER = 'current_user';
    public const ASSIGN_TO_PARENT_ASSIGNEE = 'parent_assignee';

    public const PARENT_VARDEF_KEY = 'customer_journey_parent';
    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'DRI_Workflows';
    public $object_name = 'DRI_Workflow';
    public $table_name = 'dri_workflows';
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
    public $parent_id;
    public $parent_name;
    public $parent_type;
    public $type;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $account_id;
    public $account_name;
    public $account_link;
    public $contact_id;
    public $contact_name;
    public $contact_link;
    public $lead_id;
    public $lead_name;
    public $lead_link;
    public $opportunity_id;
    public $opportunity_name;
    public $opportunity_link;
    public $available_modules;
    public $assignee_rule;
    public $target_assignee;
    public $activities;
    public $following;
    public $following_link;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $case_id;
    public $case_name;
    public $case_link;
    public $enabled_modules;
    public $locked_fields;
    public $locked_fields_link;
    public $acl_team_set_id;
    public $acl_team_names;
    public $date_started;
    public $date_completed;
    public $momentum_ratio;
    public $momentum_points;
    public $momentum_score;
    public $archived;

    /**
     * @var Link2
     */
    public $dri_subworkflows;

    /**
     * @var Link2
     */
    public $current_activity_call;

    /**
     * @var Link2
     */
    public $current_activity_task;

    /**
     * @var Link2
     */
    public $current_activity_meeting;

    /**
     * @var string
     */
    public $current_stage_id;

    /**
     * @var string
     */
    public $current_stage_name;

    /**
     * @var Link2
     */
    public $current_stage_link;

    /**
     * @var Link2
     */
    public $dri_subworkflow_link;

    /**
     * @var string
     */
    public $dri_workflow_template_id;

    /**
     * @var string
     */
    public $dri_workflow_template_name;

    /**
     * @var Link2
     */
    public $dri_workflow_template_link;

    /**
     * @var DRI_SubWorkflow[]
     */
    private static $stages = [];

    /**
     * {@inheritdoc}
    **/
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * Retrieves a DRI_Workflow with id $id and
     * returns a instance of the retrieved bean
     *
     * @param string $id : the id of the DRI_Workflow that should be retrieved
     * @return DRI_Workflow
     * @throws NotFoundException: if not found
     */
    public static function getById($id)
    {
        return CJBean\Repository::getInstance()->getById('DRI_Workflows', $id);
    }

    /**
     * Retrieves a DRI_Workflow with name $name and
     * returns a instance of the retrieved bean
     *
     * @param string $name : the name of the DRI_Workflow that should be retrieved
     * @return DRI_Workflow
     * @throws NotFoundException
     */
    public static function getByName($name)
    {
        return CJBean\Repository::getInstance()->getByName(
            [
                'table' => 'dri_workflows',
                'module' => 'DRI_Workflows',
                'name' => $name,
            ]
        );
    }

    /**
     * @param SugarBean $parent
     * @param string $template_id
     * @return DRI_Workflow
     * @throws Exception
     */
    public static function start(\SugarBean $parent, $template_id)
    {
        // enable activity logging during creation
        if (SugarConfig::getInstance()->get('customer_journey.disable_activity_stream_on_create', true)) {
            Activity::disable();
        }

        // enable tracker logging during creation
        if (SugarConfig::getInstance()->get('customer_journey.disable_tracker_on_create', true)) {
            TrackerManager::getInstance()->pause();
        }

        Journey\Tracker::activeUser($GLOBALS['current_user']);

        try {
            $journey = new \DRI_Workflow();
            $journey->id = Uuid::uuid1();
            $journey->new_with_id = true;

            if (empty($GLOBALS['current_user']->id)) {
                $journey->update_modified_by = false;
                $journey->set_created_by = false;
                $journey->created_by = $parent->created_by;
                $journey->modified_user_id = $parent->modified_user_id;
            }

            BeanFactory::registerBean($journey);
            $journey->checkParentLimit($parent, $template_id);
            $journey->populateFromParent($parent, $template_id);
            $journey->save();

            // re disable after the save so we aren't affecting other parts of the system
            if (SugarConfig::getInstance()->get('customer_journey.disable_activity_stream_on_create', true)) {
                Activity::enable();
            }

            if (SugarConfig::getInstance()->get('customer_journey.disable_tracker_on_create', true)) {
                TrackerManager::getInstance()->unPause();
            }

            return $journey;
        } catch (\Exception $e) {
            $GLOBALS['log']->fatal("$e");
            throw $e;
        }
    }

    /**
     * @param bool $save
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     */
    private function calculateProgress($save = true)
    {
        $calculator = new Journey\ProgressCalculator($this);
        $calculator->calculate($save);
    }

    /**
     * @param bool $save
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     */
    private function calculateMomentum($save = true)
    {
        $calculator = new Journey\MomentumCalculator($this);
        $calculator->calculate($save);
    }

    /**
     * @param string $trigger_event
     * @param \SugarBean $parent
     * @param array $data
     * @param \DRI_Workflow_Template $template
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiException
     * @throws SugarQueryException
     */
    public function sendWebHooks(
        $trigger_event,
        \SugarBean $parent = null,
        \DRI_Workflow_Template $template = null,
        array $data = null
    ) {

        if (is_null($parent)) {
            try {
                $parent = $this->getParent();
            } catch (CJException\ParentNotFoundException $e) {
                $parent = null;
                throw $e;
            }
        }

        if (is_null($template)) {
            $template = $this->getTemplate();
        }

        if (is_null($data)) {
            $data = $this->toArray(true);
        }

        $template->sendWebHooks($trigger_event, [
            'parent_module' => (is_null($parent)) ? $parent : $parent->module_dir,
            'parent' => (is_null($parent)) ? $parent : $parent->toArray(true),
            'journey' => $data,
        ]);
    }

    /**
     * Checks if given field is changed and set to Hide
     *
     * @throws CustomerJourneyException\NotFoundException
     */
    public function isHideSet()
    {
        if (($this->fieldChanged('stage_numbering') &&
                $this->stage_numbering == true) ||
            $this->stage_numbering == 1) {
            $stages = $this->getCopiedActivities();
            foreach ($stages as $stage) {
                $this->updateAssigneeActivityID($stage->id, $stage->table_name, 'label', $stage->name);
            }
        } else {
            $this->defaultValueShow('stage_numbering');
        }
    }

    /**
     * Checks if given field is changed
     *
     * @param string $field
     * @return bool
     */
    public function fieldChanged($field)
    {
        if (!isset($this->fetched_row[$field])) {
            if (isset($this->$field) && !empty($this->$field)) {
                return true;
            }
            return false;
        }

        return $this->$field !== $this->fetched_row[$field];
    }

    /**
     * Get the stage templates linked with the journey template
     *
     * @return DRI_SubWorkflow_Template[]
     * @throws SugarQueryException
     */
    public function getCopiedActivities()
    {
        $bean = \BeanFactory::newBean('DRI_SubWorkflows');
        $query = new \SugarQuery();
        $query->from($bean, ['team_security' => false]);
        $query->select('*');
        $query
            ->where()
            ->equals('dri_workflow_id', $this->id);

        return $bean->fetchFromQuery($query);
    }

    /**
     * Update the stage Labels
     *
     * @param string $templateID
     * @param string $table
     * @param string $column
     * @param string $name
     * @throws SugarQueryException
     */
    public function updateAssigneeActivityID($templateID, $table, $column, $name)
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($table)
            ->set($column, $qb->expr()->literal($name))
            ->where($qb->expr()->eq('id', $qb->expr()->literal($templateID)));
        $qb->execute();
    }

    /**
     * Show stage number along with name on stage
     *
     * @param string $field
     * @throws SugarQueryException
     */
    public function defaultValueShow($field)
    {
        if ((!empty($this->fetched_row) && !empty($this->fetched_row[$field]) &&
                $this->$field == false) ||
            $this->$field == 0) {
            $stages = $this->getCopiedActivities();
            foreach ($stages as $stage) {
                if (strlen($stage->sort_order) === 1) {
                    $stage->sort_order = "0{$stage->sort_order}";
                }
                $name = sprintf('%s. %s', $stage->sort_order, $stage->name);
                $this->updateAssigneeActivityID($stage->id, $stage->table_name, 'label', $name);
            }
        }
    }

    /**
     * @param boolean $check_notify
     * @return string
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    public function save($check_notify = false)
    {
        $isNew = !$this->isUpdate();
        $template = null;

        if (!empty($this->dri_workflow_template_id) && $isNew) {
            try {
                $template = $this->getTemplate();
                $this->populateFromTemplate($template);
                $check_notify = $this->assigned_user_id !== $GLOBALS['current_user']->id;
            } catch (CJException\NotFoundException $e) {
                $GLOBALS['log']->debug('Sugar Automate', __FILE__ . ' ' . __LINE__, $e->getMessage());
                throw $e;
            }
        }

        if ($isNew) {
            $this->state = self::STATE_IN_PROGRESS;
            $this->progress = 0;
            $this->score = 0;
        }

        if ($isNew) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_BEFORE_CREATE);
        }

        if ($isNew && !empty($this->dri_workflow_template_id) && !is_null($template)) {
            // disable resaving of the journey during creation
            $this->createStagesFromTemplate($template);
        } else {
            $this->calculateState();
            $this->calculateProgress();
            $this->calculateMomentum();
        }

        $this->setName();

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

        $this->isHideSet();
        $return = parent::save($check_notify);
        $this->setCurrentStageAndActivity();

        if ($changedToInProgress) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_IN_PROGRESS);
        }

        if ($changedToCompleted) {
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_COMPLETED);
        }

        if ($isNew) {
            $this->sendAssignmentNotifications();
            $this->sendWebHooks(\CJ_WebHook::TRIGGER_EVENT_AFTER_CREATE);
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function changedToInProgress()
    {
        return $this->stateChangedTo(self::STATE_IN_PROGRESS);
    }

    /**
     * @return bool
     */
    public function changedToCompleted()
    {
        return $this->stateChangedTo(self::STATE_COMPLETED);
    }

    /**
     * @param $state
     * @return bool
     */
    private function stateChangedTo($state)
    {
        return $this->isFieldChanged('state') && $this->state === $state;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldChanged($name)
    {
        return CJBean\Repository::getInstance()->isFieldChanged($this, $name);
    }

    /**
     *
     */
    private function setName()
    {
        $parentName = null;

        foreach ($this->getParentDefinitions() as $parentDef) {
            if (!empty($this->{$parentDef['id_name']})) {
                // if the name have not been populated, only the id
                // make sure to retrieve and populate it
                if (empty($this->{$parentDef['name']})) {
                    $parent = BeanFactory::retrieveBean(
                        $parentDef['module'],
                        $this->{$parentDef['id_name']},
                        ['disable_row_level_security' => true,]
                    );

                    if ($parent) {
                        $this->{$parentDef['name']} = $parent->name;
                    }
                }

                // use the first available name according to the prio list
                if (!empty($this->{$parentDef['name']})) {
                    $parentName = $this->{$parentDef['name']};
                    break;
                }
            }
        }

        if (!empty($parentName)) {
            $this->name = sprintf('%s - %s', $parentName, $this->dri_workflow_template_name);
        } else {
            $this->name = $this->dri_workflow_template_name;
        }
    }

    /**
     * @throws SugarApiExceptionError
     */
    public function setCurrentStageAndActivity()
    {
        static $updated = [];
        foreach ($this->getStages() as $stage) {
            if (!$stage->isCompleted()) {
                foreach ($stage->getActivities() as $activity) {
                    $cacheKey = 'DRIWFUpdateCache' . md5(
                        $activity->id . $activity->module_dir . $stage->id . $this->id
                    );
                    if (array_key_exists($cacheKey, $updated)) {
                        continue;
                    }
                    $handler = ActivityHandlerFactory::factory($activity->module_dir);
                    if (!$handler->isCompleted($activity) && !empty($this->id)) {
                        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
                        $qb->update('dri_workflows')
                            ->set('parent_id', $qb->expr()->literal($activity->id))
                            ->set('parent_type', $qb->expr()->literal($activity->module_dir))
                            ->set('current_stage_id', $qb->expr()->literal($stage->id))
                            ->where($qb->expr()->eq('id', $qb->expr()->literal($this->id)));
                        $qb->execute();
                        $updated[$cacheKey] = true;
                        break;
                    }
                }
                break;
            }
        }
    }

    /**
     * Retrieves the next activity in the journey after a given activity
     * @param DRI_SubWorkflow $stage
     * @param \SugarBean $activity
     * @return \SugarBean|bool
     * @throws SugarApiExceptionError
     */
    public function getNextActivity(DRI_SubWorkflow $stage, \SugarBean $activity)
    {
        $next = $stage->getNextActivity($activity);

        while ($stage && !$next) {
            $stage = $this->getNextStage($stage);

            if ($stage) {
                $next = $stage->getFirstActivity();
            }
        }

        return $next;
    }

    /**
     * Get the dependant activity of a specific activity, based on updating assignee, dates or both
     * updates the assignee accordingly
     * @param \SugarBean $activity
     * @param \SugarBean $depActivity
     * @param string $templateId
     */
    private function getDependentActivityAssigneeDuedate(\SugarBean $activity, \SugarBean $depActivity, $templateId)
    {
        $assignee = false;
        $duedate = false;
        $startdate = false;

        $depHandler = ActivityHandlerFactory::factory($depActivity->module_dir);
        if (!$depHandler->hasActivityTemplate($depActivity)) {
            return;
        }
        $template = $depHandler->getActivityTemplate($depActivity);
        if (empty($template->id)) {
            return;
        }
        if ($templateId === $template->assignee_rule_activity_id && $template->assignee_rule === \DRI_Workflow_Task_Template::ASSIGNEE_RULE_SPECIFIC_ACTIVITY_COMPLETED) {
            $assignee = true;
        }
        if ($templateId === $template->due_date_activity_id && $template->task_due_date_type === \DRI_Workflow_Task_Template::TASK_DUE_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED) {
            $duedate = true;
        }
        if ($templateId === $template->start_date_activity_id && $template->task_start_date_type === \DRI_Workflow_Task_Template::TASK_START_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED) {
            $startdate = true;
        }

        if ($assignee !== true && $duedate !== true && $startdate !== true) {
            return;
        }

        if ($startdate) {
            $depHandler->setStartDateWhenSpecificActivityCompleted($depActivity, $activity);
        }

        if ($duedate) {
            $depHandler->setDueDateWhenSpecificActivityCompleted($depActivity, $activity);
        }

        if ($assignee) {
            $depHandler->assigneeRuleSpecificActivityCompleted($depActivity, $activity);
        }
    }

    /**
     * Get the dependant activity of a specific activity
     *
     * @param \SugarBean $activity
     * @param \SugarBean $depActivity
     * @param string $templateId
     */
    private function getDependentActivitySpecificActivity(\SugarBean $activity, \SugarBean $depActivity, $templateId)
    {
        $this->getDependentActivityAssigneeDuedate($activity, $depActivity, $templateId);

        $depHandler = ActivityHandlerFactory::factory($depActivity->module_dir);
        foreach ($depHandler->getChildren($depActivity) as $child) {
            $this->getDependentActivityAssigneeDuedate($activity, $child, $templateId);
        }
    }

    /**
     * Get the dependant activity of a specific stage
     *
     * @param \SugarBean $activity
     * @param DRI_SubWorkflow $stage
     * @param string $templateId
     */
    private function getDependentActivitySpecificStage(\SugarBean $activity, \DRI_SubWorkflow $stage, $templateId)
    {
        foreach ($stage->getActivities() as $depActivity) {
            $this->getDependentActivitySpecificActivity($activity, $depActivity, $templateId);
        }
    }

    /**
     * Get the dependant activity of a specific activity
     *
     * @param \SugarBean $activity
     */
    public function getDependentActivity(\SugarBean $activity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);
        $templateId = $handler->getActivityTemplateId($activity);
        foreach ($this->getStages() as $stage) {
            $this->getDependentActivitySpecificStage($activity, $stage, $templateId);
        }
    }

    /**
     * Retrieves the activity (with start/due date dependent) in the journey on a given activity
     *
     * @param \SugarBean $activity
     */
    public function setDatesAndAssigneeOfDependentActivities(\SugarBean $activity)
    {
        //This flag is used to make sure when parent assignee will change then 2 times summary email will not send.
        //It should send single time.
        $activity->assignmentChangeOnCompleted = true;
        $this->getDependentActivity($activity);
    }

    /**
     * Populates some of the fields of smart guide from template
     * @param DRI_SubWorkflow $stage
     * @return DRI_SubWorkflow|bool
     * @throws SugarApiExceptionError
     */
    public function getNextStage(DRI_SubWorkflow $stage)
    {
        foreach ($this->getStages() as $next) {
            if (!$next->deleted && $next->sort_order > $stage->sort_order) {
                return $next;
            }
        }

        return false;
    }

    /**
     * Calculate state of smart guide
     * @param bool $save
     */
    private function calculateState($save = true)
    {
        $calculator = new Journey\StateCalculator($this);
        $calculator->calculate($save);
    }

    /**
     * Calculate stage state
     * @param bool $save
     */
    private function calculateStageStates($save = true)
    {
        $calculator = new Journey\StateCalculator($this);
        $calculator->calculateStageStates($save);
    }

    /**
     * Create smart guide stages from template
     * @param DRI_Workflow_Template $template
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    private function createStagesFromTemplate(DRI_Workflow_Template $template)
    {
        /** @var DRI_SubWorkflow[] $stages */
        $stages = [];
        BeanFactory::registerBean($this);

        foreach ($template->getStageTemplates() as $stageTemplate) {
            $stage = new DRI_SubWorkflow();
            $stage->id = Uuid::uuid1();
            $stage->new_with_id = true;
            $stage->created_from_journey = true;

            if (empty($GLOBALS['current_user']->id)) {
                $stage->update_modified_by = false;
                $stage->set_created_by = false;
                $stage->created_by = $this->created_by;
                $stage->modified_user_id = $this->modified_user_id;
            }

            BeanFactory::registerBean($stage);
            $stage->setJourney($this);
            $stage->populateFromJourney($this);
            $stage->populateFromTemplate($stageTemplate);
            $stage->createActivitiesFromTemplate(false);
            $stages[] = $stage;
        }

        $this->setStages($stages);

        $this->calculateStageStates(false);
        $this->calculateState(false);
        $this->calculateProgress(false);
        $this->calculateMomentum(false);

        foreach ($stages as $stage) {
            $stage->setJourney($this);
            $stage->save();
            BeanFactory::unregisterBean($stage);
        }
    }

    /**
     * Populates some of the fields of smart guide from template
     * @param DRI_Workflow_Template $template
     */
    private function populateFromTemplate(DRI_Workflow_Template $template)
    {
        $this->dri_workflow_template_id = $template->id;
        $this->dri_workflow_template_name = $template->name;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->type = $template->type;
        $this->available_modules = $template->available_modules;
        $this->assignee_rule = $template->assignee_rule;
        $this->target_assignee = $template->target_assignee;
        $this->points = $template->points ?: 0;
        $this->assigned_user_id = $this->getTargetAssigneeId();
        $this->team_id = $this->getTargetTeamId();
        $this->team_set_id = $this->getTargetTeamSetId();
        $this->stage_numbering = $template->stage_numbering;
    }

    /**
     * Get the target assignee rule of this smart guide
     * @return string
     */
    public function getAssigneeRule()
    {
        return $this->assignee_rule;
    }

    /**
     * Get the target assignee id of this smart guide
     * @return string
     * @throws ParentNotFoundException
     */
    public function getTargetAssigneeId()
    {
        switch ($this->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                if (!empty($GLOBALS['current_user']->id)) {
                    return $GLOBALS['current_user']->id;
                } else {
                    return $this->created_by;
                }
                // no break
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $this->getParent()->assigned_user_id;
        }

        return null;
    }

    /**
     * Get the target assignee of this smart guide
     * @return string
     * @throws ParentNotFoundException
     */
    public function getTargetAssignee()
    {
        return $this->target_assignee;
    }

    /**
     * Get the target team id of this smart guide
     * @return string
     * @throws ParentNotFoundException
     */
    public function getTargetTeamId()
    {
        switch ($this->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                return !empty($GLOBALS['current_user']->id) ? $GLOBALS['current_user']->default_team : '1';
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $this->getParent()->team_id;
        }

        return null;
    }

    /**
     * Get the target teamset id of this smart guide
     * @return string
     * @throws ParentNotFoundException
     */
    public function getTargetTeamSetId()
    {
        switch ($this->target_assignee) {
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_CURRENT_USER:
                return !empty($GLOBALS['current_user']->id) ? $GLOBALS['current_user']->team_set_id : '1';
            case \DRI_Workflow_Template::TARGET_ASSIGNEE_PARENT:
                return $this->getParent()->team_set_id;
        }

        return null;
    }

    /**
     * Check parent limit of smart guide
     * @param SugarBean $parent
     * @param string $template_id
     * @return null
     * @throws SameJourneyLimitReachException
     */
    public function checkParentLimit(\SugarBean $parent, string $template_id)
    {
        $template = DRI_Workflow_Template::getById($template_id);

        if (empty($template->active_limit)) {
            return;
        }

        $query = new SugarQuery();
        $query->from(\BeanFactory::newBean('DRI_Workflows'));
        $query->select('id');
        $where = $query->where();
        $where
            ->equals('dri_workflow_template_id', $template_id)
            ->equals('archived', false)
            ->notEquals('state', self::STATE_COMPLETED);

        foreach ($this->getParentDefinitions() as $parentDef) {
            if ($parentDef['module'] === $parent->module_dir) {
                $where->equals($parentDef['id_name'], $parent->id);
                $where->equals($parentDef['name'], $parent->name);
            }
        }

        $results = safeCount($query->execute());

        if ($results >= $template->active_limit) {
            throw new CJException\SameJourneyLimitReachException();
        }
    }

    /**
     * Populate data from parent
     * @param SugarBean $parent
     * @param string $template_id
     */
    public function populateFromParent(\SugarBean $parent, string $template_id)
    {
        $this->dri_workflow_template_id = $template_id;

        foreach ($this->getParentDefinitions() as $parentDef) {
            if ($parentDef['module'] === $parent->module_dir) {
                $this->{$parentDef['id_name']} = $parent->id;
                $this->{$parentDef['name']} = $parent->name;
            }
        }
    }

    /**
     * Get the smart guide template
     * @return DRI_Workflow_Template
     * @throws NotFoundException
     */
    public function getTemplate()
    {
        return DRI_Workflow_Template::getById($this->dri_workflow_template_id);
    }

    /**
     * Get the parent record
     * @param string $module
     * @return \SugarBean
     * @throws ParentNotFoundException
     */
    public function getParent(string $module = null)
    {
        $parent = null;

        foreach ($this->getParentDefinitions() as $parentDef) {
            if (!empty($this->{$parentDef['id_name']}) && (is_null($module) || $module === $parentDef['module'])) {
                $parent = BeanFactory::retrieveBean($parentDef['module'], $this->{$parentDef['id_name']}, [
                    'disable_row_level_security' => true,
                ]);

                break;
            }
        }

        if ($parent instanceof SugarBean && !empty($parent->id)) {
            return $parent;
        }

        throw new CJException\ParentNotFoundException();
    }

    /**
     * Get the parent definition
     * @return array
     */
    public function getParentDefinitions()
    {
        $unsorted = [];

        foreach ($this->getFieldDefinitions() as $def) {
            if (!empty($def[self::PARENT_VARDEF_KEY]['enabled'])) {
                $unsorted[$def[DRI_Workflow::PARENT_VARDEF_KEY]['rank']][] = $def;
            }
        }

        $sorted = [];
        ksort($unsorted);

        foreach ($unsorted as $defs) {
            foreach ($defs as $def) {
                $sorted[] = $def;
            }
        }

        return $sorted;
    }

    /**
     * Cancel the smart guide
     * @return array
     */
    public function cancel()
    {
        $canceller = new Journey\Canceller();
        return $canceller->cancel($this);
    }

    /**
     * Archive the smart guide
     * @throws JourneyNotCompletedException
     */
    public function archive()
    {
        if ($this->state !== self::STATE_COMPLETED &&
            $this->state !== self::STATE_CANCELLED) {
            throw new CJException\JourneyNotCompletedException();
        }
        $this->archived = true;
        $this->save();
    }

    /**
     * Loads the complete journey into memory
     */
    public function load()
    {
        $this->loadStages();
        $this->loadActivities();
    }

    /**
     * Insert an activity into the smart guide
     * @param SugarBean $activity
     * @throws SugarApiExceptionError
     */
    public function insertActivity(\SugarBean $activity)
    {
        foreach ($this->getStages() as &$stage) {
            if ($stage->hasActivity($activity)) {
                $stage->insertActivity($activity);
            }
        }
    }

    /**
     * loads all stages related to the journey
     */
    private function loadStages()
    {
        if (!array_key_exists($this->id, self::$stages) || is_null(self::$stages[$this->id])) {
            $bean = \BeanFactory::newBean('DRI_SubWorkflows');

            $query = new \SugarQuery();
            $query->from($bean);
            $query->select('*');
            $query->orderBy('sort_order', 'ASC');
            $query->where()
                ->equals('dri_workflow_id', $this->id);

            $this->setStages($bean->fetchFromQuery($query));

            // register all stages in the global bean cache
            foreach (self::$stages[$this->id] as $stage) {
                BeanFactory::registerBean($stage);
            }
        }
    }

    /**
     * Reloads stages and activities
     */
    public function reloadStages()
    {
        self::$stages[$this->id] = null;
        $this->loadStages();
    }

    /**
     * Set stages
     * @param array $stages
     */
    private function setStages(array $stages)
    {
        self::$stages[$this->id] = $stages;
    }

    /**
     * Get stages of the smart guide
     * @return DRI_SubWorkflow[]
     * @throws SugarApiExceptionError
     */
    public function getStages()
    {
        // We should not attempt to load the stages if the bean is new,
        // this is both unnecessary and very dangerous.
        if (empty($this->id)) {
            return [];
        }

        $this->loadStages();

        return array_values(self::$stages[$this->id]);
    }

    /**
     * Get stage ids of the smart guide
     * @return array
     * @throws SugarApiExceptionError
     */
    public function getStageIds()
    {
        $ids = [];

        foreach ($this->getStages() as $stage) {
            $ids[] = $stage->id;
        }

        return $ids;
    }

    /**
     * If stages have already activities loaded or not
     */
    private function areStagesAlreadyEmpty()
    {
        $fetchAll = false;
        // if any stage has zero activity then load all activities else skip
        foreach ($this->getStages() as $stage) {
            if (empty($stage->getActivities())) {
                $fetchAll = true;
            }
        }
        return $fetchAll;
    }

    /**
     * loads all activities related to the journey
     *
     * @throws SugarApiExceptionError
     * @throws SugarQueryException
     */
    private function loadActivities()
    {
        // if any stage has zero activity then load all activities else return
        if (!$this->areStagesAlreadyEmpty()) {
            return;
        }
        // load all activities related to the journey
        $activities = [];
        foreach (ActivityHandlerFactory::all() as $activityHandler) {
            [$idFieldQuery, $allFieldsQuery] = $activityHandler->createLoadQuery();
            $idFieldQuery->where()->in($activityHandler->getStageIdFieldName(), $this->getStageIds());
            $ids = $idFieldQuery->compile()
                ->execute()
                ->fetchAllAssociative();
            $ids = array_column($ids, 'id');
            if (safeCount($ids) > 0) {
                $bean = \BeanFactory::newBean($activityHandler->getModuleName());
                $allFieldsQuery->where()->in('id', $ids);
                $activities = array_merge($activities, $bean->fetchFromQuery($allFieldsQuery));
            }
        }

        // register all activities in the global bean cache
        foreach ($activities as $activity) {
            BeanFactory::registerBean($activity);
        }

        // split up the activities between the stage it belongs to
        foreach ($this->getStages() as &$stage) {
            $filtered = [];

            foreach ($activities as $activity) {
                $handler = ActivityHandlerFactory::factory($activity->getModuleName());
                if ($handler->getStageId($activity) === $stage->id) {
                    $filtered[] = $activity;
                }
            }

            $stage->setLoadedActivities(array_values($filtered));
        }
    }

    /**
     * Get activity of the smart guide activity template
     * @param DRI_Workflow_Task_Template $activityTemplate
     * @return \SugarBean|false
     * @throws SugarApiExceptionError
     */
    public function getActivityByTemplate(DRI_Workflow_Task_Template $activityTemplate)
    {
        foreach ($this->getStages() as $stage) {
            foreach ($stage->getActivities() as $activity) {
                $handler = ActivityHandlerFactory::factory($activity->module_dir);
                if ($handler->getActivityTemplateId($activity) === $activityTemplate->id) {
                    return $activity;
                }

                foreach ($handler->getChildren($activity) as $child) {
                    $handler = ActivityHandlerFactory::factory($child->module_dir);
                    if ($handler->getActivityTemplateId($child) === $activityTemplate->id) {
                        return $child;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get activity by template's id
     * @param string $id
     * @return \SugarBean|false
     * @throws SugarApiExceptionError
     */
    public function getActivityByTemplateId(string $id)
    {
        foreach ($this->getStages() as $stage) {
            foreach ($stage->getActivities() as $activity) {
                $handler = ActivityHandlerFactory::factory($activity->module_dir);

                if ($handler->getActivityTemplateId($activity) === $id) {
                    return $activity;
                }

                if ($handler->isParent($activity)) {
                    foreach ($handler->getChildren($activity) as $child) {
                        $handler = ActivityHandlerFactory::factory($child->module_dir);
                        if ($handler->getActivityTemplateId($child) === $id) {
                            return $child;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get stages by stage template's id
     * @param string $id
     * @return \SugarBean|false
     * @throws SugarApiExceptionError
     */
    public function getStageByStageTemplateId(string $id)
    {
        foreach ($this->getStages() as $stage) {
            if ($stage->dri_subworkflow_template_id === $id) {
                return $stage;
            }
        }

        return false;
    }

    /**
     * Retrieve the record
     * @param int $id
     * @param bool $encode
     * @param bool $deleted
     * @return Basic|null
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $return = parent::retrieve($id, $encode, $deleted);
        return $return;
    }

    /**
     * Mark the record and corresponding stages and activities as deleted
     * @param string $id
     * @throws SugarApiExceptionError
     */
    public function mark_deleted($id)
    {
        if ($this->id !== $id) {
            $this->retrieve($id);
        }

        $this->load();

        $stages = $this->getStages();
        $data = $this->toArray(true);

        try {
            $parent = $this->getParent();
        } catch (\Exception $e) {
            $parent = null;
        }

        try {
            $template = $this->getTemplate();
        } catch (\Exception $e) {
            $template = null;
        }

        if ($parent && $template) {
            $this->sendWebHooks(
                CJ_WebHook::TRIGGER_EVENT_BEFORE_DELETE,
                $parent,
                $template,
                $data
            );
        }

        parent::mark_deleted($id);

        foreach ($stages as $stage) {
            $stage->mark_deleted($stage->id);
        }

        if ($parent && $template) {
            $this->sendWebHooks(
                CJ_WebHook::TRIGGER_EVENT_AFTER_DELETE,
                $parent,
                $template,
                $data
            );
        }
        $this->reloadStages();
    }

    /**
     * Get available modules
     * @return array
     */
    public function getAvailableModules()
    {
        return unencodeMultienum($this->available_modules);
    }

    /**
     * Set the momentum_start_date of all the targeted activities which are dependent
     * on the current Activity when it will get mark as "Completed"
     * @param \SugarBean $activity
     * @return \SugarBean|bool
     * @throws SugarApiExceptionError
     */
    public function setMomentumStartDateOfTargetedActivities(\SugarBean $currentActivity)
    {
        $currentActivityHandler = ActivityHandlerFactory::factory($currentActivity->module_dir);
        if (!$currentActivityHandler->isCompleted($currentActivity) || $currentActivityHandler->isNotApplicable($currentActivity)) {
            return;
        }
        $currentActivityTemplate = $currentActivityHandler->getActivityTemplate($currentActivity);
        if ($currentActivityHandler->hasActivityTemplate($currentActivity)) {
            foreach ($this->getStages() as $stage) {
                foreach ($stage->getActivities() as $activity) {
                    $this->isTargetedActivity($activity, $currentActivityTemplate, $currentActivity);
                }
            }
        }
    }

    /**
     * Find all the parent and children activities and update the Momentum Start Date
     * which meet the criteria
     * @param type \SugarBean $activity
     * @param type DRI_Workflow_Task_Template $currentActivityTemplate
     * @param type \SugarBean $currentActivity
     */
    private function isTargetedActivity(\SugarBean $activity, DRI_Workflow_Task_Template $currentActivityTemplate, \SugarBean $currentActivity)
    {
        $handler = ActivityHandlerFactory::factory($activity->module_dir);
        if ($handler->hasActivityTemplate($activity)) {
            $template = $handler->getActivityTemplate($activity);
            if ($handler->isParent($activity)) {
                $this->activityIsParent($activity, $currentActivityTemplate, $handler, $template, $currentActivity);
            } else {
                if (!empty($template->id) && ($template->momentum_start_type === 'specific_activity_completed') && ($template->momentum_start_activity_id === $currentActivityTemplate->id)) {
                    $activity->cj_momentum_start_date = $currentActivity->date_modified;
                    $this->updateMomentumStartDate($activity);
                }
            }
        }
    }

    /**
     * If activity is parent then update the Momentum Start Date of Parent
     * as well as Children
     * @param type \SugarBean $activity (Targeted Activity)
     * @param type DRI_Workflow_Task_Template $currentActivityTemplate (Template of current activity)
     * @param type $handler (Handler of targeted activity)
     * @param type DRI_Workflow_Task_Template $template (Template of targeted activity)
     * @param type \SugarBean $currentActivity
     */
    private function activityIsParent(\SugarBean $activity, DRI_Workflow_Task_Template $currentActivityTemplate, $handler, DRI_Workflow_Task_Template $template, \SugarBean $currentActivity)
    {
        foreach ($handler->getChildren($activity) as $child) {
            $childHandler = ActivityHandlerFactory::factory($child->module_dir);
            if ($childHandler->hasActivityTemplate($child)) {
                $childTemplate = $childHandler->getActivityTemplate($child);
                if (!empty($childTemplate->id) && ($childTemplate->momentum_start_type === 'specific_activity_completed') && ($childTemplate->momentum_start_activity_id === $currentActivityTemplate->id)) {
                    $child->cj_momentum_start_date = $currentActivity->date_modified;
                    $this->updateMomentumStartDate($child);
                }
            }
        }
        if (!empty($template->id) && ($template->momentum_start_type === 'specific_activity_completed') && ($template->momentum_start_activity_id === $currentActivityTemplate->id)) {
            $activity->cj_momentum_start_date = $currentActivity->date_modified;
            $this->updateMomentumStartDate($activity);
        }
    }

    /**
     * Update the Momentum Start Date of the Activity
     * @param \SugarBean $activity
     */
    private function updateMomentumStartDate(\SugarBean $activity)
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($activity->getTableName())
            ->set('cj_momentum_start_date', $qb->expr()->literal($activity->cj_momentum_start_date))
            ->where($qb->expr()->eq('id', $qb->expr()->literal($activity->id)));
        $qb->execute();

        $table = $activity->getTableName();
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->update($table)
            ->set('cj_momentum_start_date', $qb->expr()->literal($activity->cj_momentum_start_date))
            ->where($qb->expr()->eq('id', $qb->expr()->literal($activity->id)));
        $qb->execute();
    }

    /**
     * Send the Email Notification of Assignment of Activities to the users
     */
    public function sendAssignmentNotifications()
    {
        $sharedDataObj = new CJSharedData\SharedData();
        $sharedData = $sharedDataObj->getData('assignment_summary');
        $sharedDataObj->resetData('assignment_summary');

        if (empty($sharedData)) {
            return;
        }

        foreach ($sharedData as $assigned_user_id => $activityIDs) {
            $userBean = BeanFactory::retrieveBean('Users', $assigned_user_id, [
                'disable_row_level_security' => true,
            ]);
            if (empty($userBean->id)) {
                return;
            }
            $notificationTemplate = $this->buildNotificationSummaryTemplate($userBean, $activityIDs);
            $this->sendAssignmentNotificationEmail($notificationTemplate, $userBean);
        }
    }

    /**
     * Build the HTML Template for Sending Email
     * @param \SugarBean $userBean
     * @param array $activityIDs
     * @return string
     */
    private function buildNotificationSummaryTemplate(\SugarBean $userBean, array $activityIDs)
    {
        global $app_strings, $current_user;
        $notificationTemplateHtml = $app_strings['LBL_ASSIGNMENT_NOTIFICATION_HTML'];

        $site_url = $this->getURL();
        $activityPaths = $this->buildActivityPaths($activityIDs, $site_url);

        $notificationTemplateHtml = str_replace('{{activities}}', $activityPaths, $notificationTemplateHtml);
        $notificationTemplateHtml = str_replace('{{assigned_by}}', $current_user->full_name, $notificationTemplateHtml);
        $notificationTemplateHtml = str_replace('{{assigned_user}}', $userBean->full_name, $notificationTemplateHtml);
        $notificationTemplateHtml = str_replace('{{module_link}}', "$site_url/index.php#{$this->module_dir}/{$this->id}", $notificationTemplateHtml);

        return $notificationTemplateHtml;
    }

    /**
     * Returns a clean site URL
     * @return string
     */
    private function getURL()
    {
        global $sugar_config;
        $parsedSiteUrl = parse_url($sugar_config['site_url']);
        $host = $parsedSiteUrl['host'];

        if (!isset($parsedSiteUrl['port'])) {
            $parsedSiteUrl['port'] = 80;
        }

        $port = ($parsedSiteUrl['port'] !== 80) ? ':' . $parsedSiteUrl['port'] : '';
        $path = isset($parsedSiteUrl['path']) ? rtrim($parsedSiteUrl['path'], '/') : '';
        $cleanUrl = "{$parsedSiteUrl['scheme']}://{$host}{$port}{$path}";
        return $cleanUrl;
    }

    /**
     * Builds <li> structure for multiple activities
     * @param array $activityIDs
     * @param string $site_url
     * @return string
     */
    private function buildActivityPaths(array $activityIDs, $site_url)
    {
        $activityLIs = '';
        foreach ($activityIDs as $activityID) {
            $activityLIs .= "<li><a href='$site_url/index.php#$activityID[module_name]/$activityID[activity_id]'>$activityID[activity_name]</a></li>";
        }
        return $activityLIs;
    }

    /**
     * Sends the email to user for assignment of activities
     * @param string $notificationTemplate
     * @param \SugarBean $userBean
     * @throws MailerException
     */
    private function sendAssignmentNotificationEmail($notificationTemplate, \SugarBean $userBean)
    {
        global $app_strings;
        $admin = Administration::getSettings();
        $mailTransmissionProtocol = 'unknown';

        try {
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $fromEmail = $admin->settings['notify_fromaddress'];
            $fromName = $admin->settings['notify_fromname'];

            if (!empty($admin->settings['notify_send_from_assigning_user'])) {
                // the "notify_send_from_assigning_user" admin setting is set
                // use the current user's email address and name for the From email header
                $usersEmail = $GLOBALS['current_user']->emailAddress->getReplyToAddress($GLOBALS['current_user']);
                $usersName = $GLOBALS['current_user']->full_name;

                // only use it if a valid email address is returned for the current user
                if (!empty($usersEmail)) {
                    $fromEmail = $usersEmail;
                    $fromName = $usersName;
                }
            }

            $from = new EmailIdentity($fromEmail, $fromName);
            $mailer->setHeader(EmailHeaders::From, $from);
            $mailer->setHeader(EmailHeaders::ReplyTo, $from);
            $mailer->setSubject($app_strings['LBL_ASSIGNMENT_NOTIFICATION_SUBJECT']);
            $mailer->setHtmlBody($notificationTemplate);

            $recipientEmailAddress = $userBean->emailAddress->getPrimaryAddress($userBean);
            $recipientName = $userBean->full_name;

            try {
                $mailer->addRecipientsTo(new EmailIdentity($recipientEmailAddress, $recipientName));
            } catch (MailerException $me) {
                $GLOBALS['log']->warn('Notifications: no e-mail address set for user {$userBean->user_name}, cancelling send');
            }

            $mailer->send();
            $GLOBALS['log']->info('Notifications: e-mail successfully sent');
        } catch (MailerException $me) {
            $message = $me->getMessage();

            switch ($me->getCode()) {
                case MailerException::FailedToConnectToRemoteServer:
                    $GLOBALS['log']->fatal('Notifications: error sending e-mail, smtp server was not found ');
                    break;
                default:
                    $GLOBALS['log']->fatal('Notifications: error sending e-mail (method: {$mailTransmissionProtocol}), (error: {$message})');
                    break;
            }
        }
    }

    /**
     * This defines the supporting modules which have metadata needed by DRI_Workflows to be fully
     * functional on the Mobile application
     *
     * @return array
     */
    public static function getMobileSupportingModules()
    {
        $modules = parent::getMobileSupportingModules();
        return array_merge($modules, [
            'CJ_Forms',
            'CJ_WebHooks',
            'DRI_SubWorkflow_Templates',
            'DRI_Workflow_Task_Templates',
            'DRI_Workflow_Templates',
        ]);
    }
}
