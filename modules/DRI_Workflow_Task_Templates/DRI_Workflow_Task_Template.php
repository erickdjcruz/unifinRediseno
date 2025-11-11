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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean as CJBean;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks;

class DRI_Workflow_Task_Template extends Basic
{
    public const TASK_DUE_DATE_TYPE_DAYS_FROM_CREATED = 'days_from_created';
    public const TASK_DUE_DATE_TYPE_DAYS_FROM_STAGE_STARTED = 'days_from_stage_started';
    public const TASK_DUE_DATE_TYPE_DAYS_FROM_PREVIOUS_ACTIVITY_COMPLETED = 'days_from_previous_activity_completed';
    public const TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD = 'days_from_parent_date_field';
    public const TASK_DUE_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED = 'days_from_specific_activity_completed';
    public const TASK_START_DATE_TYPE_DAYS_FROM_CREATED = 'days_from_created';
    public const TASK_START_DATE_TYPE_DAYS_FROM_STAGE_STARTED = 'days_from_stage_started';
    public const TASK_START_DATE_TYPE_DAYS_FROM_PREVIOUS_ACTIVITY_COMPLETED = 'days_from_previous_activity_completed';
    public const TASK_START_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD = 'days_from_parent_date_field';
    public const TASK_START_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED = 'days_from_specific_activity_completed';
    public const SEND_INVITES_NONE = 'none';
    public const SEND_INVITES_CREATE = 'create';
    public const SEND_INVITES_STAGE_START = 'stage_start';
    public const MOMENTUM_START_TYPE_CREATED = 'created';
    public const MOMENTUM_START_TYPE_STAGE_STARTED = 'stage_started';
    public const MOMENTUM_START_TYPE_PREVIOUS_ACTIVITY_COMPLETED = 'previous_activity_completed';
    public const MOMENTUM_START_TYPE_PARENT_DATE_FIELD = 'parent_date_field';
    public const TARGET_ASSIGNEE_INHERIT = 'inherit';
    public const ASSIGNEE_RULE_INHERIT = 'inherit';
    public const ASSIGNEE_RULE_NONE = 'none';
    public const ASSIGNEE_RULE_CREATE = 'create';
    public const ASSIGNEE_RULE_STAGE_START = 'stage_start';
    public const ASSIGNEE_RULE_PREVIOUS_ACTIVITY_COMPLETED = 'previous_activity_completed';
    public const ASSIGNEE_RULE_SPECIFIC_ACTIVITY_COMPLETED = 'specific_activity_completed';

    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'DRI_Workflow_Task_Templates';
    public $object_name = 'DRI_Workflow_Task_Template';
    public $table_name = 'dri_workflow_task_templates';
    public $importable = true;
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
    public $task_due_date_type;
    public $task_due_days;
    public $task_start_date_type;
    public $task_start_days;
    public $sort_order;
    public $tasks;
    public $dri_subworkflow_template_id;
    public $dri_subworkflow_template_name;
    public $dri_subworkflow_template_link;
    public $priority;
    public $type;
    public $activity_type;
    public $time_of_day;
    public $duration_hours;
    public $duration_minutes;
    public $direction;
    public $points;
    public $dri_workflow_template_id;
    public $dri_workflow_template_name;
    public $dri_workflow_template_link;

    /**
     * @var array
     */
    public $blocked_by;
    public $blocked_by_stages;
    public $activities;
    public $following;
    public $following_link;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $duration;
    public $calls;
    public $meetings;
    public $locked_fields;
    public $locked_fields_link;
    public $acl_team_set_id;
    public $acl_team_names;
    public $send_invites;
    public $children;
    public $is_parent;
    public $parent_id;
    public $parent_name;
    public $parent_link;
    public $target_assignee;
    public $target_assignee_user_id;
    public $target_assignee_user_name;
    public $target_assignee_user_link;
    public $target_assignee_team_id;
    public $target_assignee_team_name;
    public $target_assignee_team_link;
    public $stage_template_label;
    public $stage_template_sort_order;
    public $due_date_module;
    public $due_date_field;
    public $start_date_module;
    public $start_date_field;
    public $momentum_start_type;
    public $momentum_start_module;
    public $momentum_start_field;
    public $momentum_points;
    public $momentum_due_days;
    public $momentum_due_hours;
    public $assignee_rule;
    public $url;
    /**
     * @var Link2
     */
    public $forms;
    public $web_hooks;

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
     * Retrieves a DRI_Workflow_Task_Template with id $id and
     * returns a instance of the retrieved bean
     *
     * @param string $id : the id of the DRI_Workflow_Task_Template that should be retrieved
     * @return DRI_Workflow_Task_Template
     * @throws NotFoundException: if not found
     */
    public static function getById(string $id)
    {
        return CJBean\Repository::getInstance()->getById('DRI_Workflow_Task_Templates', $id);
    }

    /**
     * Retrieves a DRI_Workflow_Task_Template with name $name and
     * returns a instance of the retrieved bean
     *
     * @param string $name : the name of the DRI_Workflow_Task_Template that should be retrieved
     * @param string|null $parentId
     * @param string|null $skipId
     * @return DRI_Workflow_Task_Template
     * @throws NotFoundException
     */
    public static function getByNameAndParent(string $name, string $parentId = null, string $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByNameAndParent(
            [
                'table' => 'dri_workflow_task_templates',
                'module' => 'DRI_Workflow_Task_Templates',
                'name' => $name,
                'parentId' => $parentId,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * Retrieves a DRI_Workflow_Task_Template with name $name and
     * returns a instance of the retrieved bean
     *
     * @param string $sortOrder : the name of the DRI_Workflow_Task_Template that should be retrieved
     * @param string $parentId
     * @param string|null $skipId
     * @return DRI_Workflow_Task_Template
     * @throws NotFoundException
     */
    public static function getByOrderAndParent($sortOrder, $parentId, $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByOrderAndParent(
            [
                'table' => 'dri_workflow_task_templates',
                'module' => 'DRI_Workflow_Task_Templates',
                'sortOrder' => $sortOrder,
                'parentId' => $parentId,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve($id = '-1', $encode = true, $deleted = true)
    {
        $return = parent::retrieve($id, $encode, $deleted);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $this->validateUniqueName();

        $this->duration_hours = !empty($this->duration_hours) ? $this->duration_hours : 0;

        $this->setJourneyTemplate();

        $this->is_parent = $this->isParent();
        $prevStageTemplate = null;
        $parent = null;

        if ($this->hasParent()) {
            $parent = $this->getParent();
        }

        $this->calculatePoints();

        $isNew = !$this->isUpdate();

        if ($isNew && $this->isDuplicateActivityByOrder()) {
            $this->moveDuplicatedActivitiesForward($parent);
        }

        if (!empty($this->fetched_row) && $this->fetched_row['dri_subworkflow_template_id'] !== $this->dri_subworkflow_template_id) {
            $prevStageTemplate = $this->getPreviousStageTemplate();
        }

        $this->setSortOrder();

        //When task_due_date_type is changed from 'Days from Specific Activity Completed' to other type, must need to unset due_date_activity_id
        if ($this->task_due_date_type !== self::TASK_DUE_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED && !empty($this->due_date_activity_id)) {
            unset($this->due_date_activity_id);
        }

        //When task_start_date_type is changed from 'Days from Specific Activity Completed' to other type, must need to unset start_date_activity_id
        if ($this->task_start_date_type !== self::TASK_START_DATE_TYPE_DAYS_FROM_SPECIFIC_ACTIVITY_COMPLETED &&
            !empty($this->start_date_activity_id)) {
            unset($this->start_date_activity_id);
        }

        $return = parent::save($check_notify);

        if ($this->hasStageTemplate()) {
            $this->getStageTemplate()->save();
        }

        if (null !== $parent) {
            $parent->save();
        }

        if (null !== $prevStageTemplate) {
            $prevStageTemplate->retrieve();
            $prevStageTemplate->save();
        }

        $this->clearParentActivityDatesCache();

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getForms($mainTriggerType = \CJ_Form::MAIN_TRIGGER_EVENT_SG_To_SA)
    {
        $filtered = [];

        if ($this->load_relationship('forms')) {
            $forms = $this->forms->getBeans();

            foreach ($forms as $form) {
                if ($form->active &&
                    $form->parent_id === $this->id &&
                    $form->main_trigger_type === $mainTriggerType
                ) {
                    $filtered[] = $form;
                }
            }
        }

        return $filtered;
    }

    /**
     * Get the assignee rule
     *
     * @param \DRI_SubWorkflow $stage
     * @return string
     */
    public function getAssigneeRule(DRI_SubWorkflow $stage)
    {
        if ($this->assignee_rule === self::ASSIGNEE_RULE_INHERIT) {
            return $stage->getAssigneeRule();
        }

        return $this->assignee_rule;
    }

    /**
     * Get the target assignee
     *
     * @param \DRI_SubWorkflow $stage
     * @return string
     */
    public function getTargetAssignee(DRI_SubWorkflow $stage)
    {
        if ($this->target_assignee === self::TARGET_ASSIGNEE_INHERIT) {
            return $stage->getTargetAssignee();
        }

        return $this->target_assignee;
    }

    /**
     * Calculate the points from child task templates
     *
     * @throws SugarQueryException
     */
    private function calculatePoints()
    {
        if ($this->is_parent) {
            $this->points = 0;
            $this->momentum_points = 0;
            foreach ($this->getChildren() as $child) {
                $this->points += $child->points;
                $this->momentum_points += $child->momentum_points;
            }
        }
    }

    /**
     * Send the webhooks
     *
     * @param $trigger_event
     * @param array $request
     * * @throws SugarApiException
     * @throws SugarQueryException
     */
    public function sendWebHooks($trigger_event, array $request)
    {
        \CJ_WebHook::send($this, $trigger_event, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function mark_deleted($id)
    {
        if ($this->id !== $id) {
            $this->retrieve($id);
        }

        $childTemplates = $this->getChildren();

        $parent = null;
        $isParent = $this->isParent();

        if ($this->hasParent()) {
            $parent = $this->getParent();
        }

        try {
            $stage = $this->getStageTemplate();
        } catch (\Exception $e) {
            $stage = null;
        }

        CJ_WebHook::deleteWebHooks($this);
        $this->deleteForms();

        parent::mark_deleted($id);

        foreach ($childTemplates as $childTemplate) {
            $childTemplate->mark_deleted($childTemplate->id);
        }

        if (null !== $parent && !$parent->deleted) {
            $parent->save();
        }

        if (null !== $stage && !$stage->deleted) {
            $this->reorderSortOrdersAndLabels($stage->id, $parent);
            $stage->save(false, true);
        }

        $this->clearParentActivityDatesCache();
    }

    /**
     * Delete the forms
     */
    public function deleteForms()
    {

        if ($this->load_relationship('forms')) {
            foreach ($this->forms->getBeans() as $form) {
                /** @var \CJ_Form $form */
                $form->mark_deleted($form->id);
            }
        }
    }

    /**
     * Clears the cache used when updating activity due dates from the parent if this is needed.
     */
    private function clearParentActivityDatesCache()
    {
        if ($this->task_due_date_type === self::TASK_DUE_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD) {
            LogicHooks\JourneyParentHooksHelper::clearParentActivityDatesCache($this->due_date_module);
        }
        if ($this->task_start_date_type === self::TASK_START_DATE_TYPE_DAYS_FROM_PARENT_DATE_FIELD) {
            LogicHooks\JourneyParentHooksHelper::clearParentActivityDatesCache($this->start_date_module);
        }
    }

    /**
     * Check if task template is parent
     *
     * @return bool
     * @throws SugarQueryException
     */
    public function isParent()
    {
        return safeCount($this->getChildren()) > 0;
    }

    /**
     * Get the children for the task template
     *
     * @return DRI_Workflow_Task_Template[]
     * @throws SugarQueryException
     */
    public function getChildren()
    {
        $bean = \BeanFactory::newBean('DRI_Workflow_Task_Templates');

        $query = new \SugarQuery();
        $query->from($bean);
        $query->select('*');
        $query->orderBy('sort_order', 'ASC');
        $query->where()->equals('parent_id', $this->id);

        $activities = $bean->fetchFromQuery($query);

        return $this->sortChildren($activities);
    }

    /**
     * Since all php functions that sorts an array based on a function is blacklisted by the package scanner
     * we have to implement our own algorithm, this is based on quicksort
     *
     * @param \DRI_Workflow_Task_Template[] $activities
     * @return array
     */
    private function sortChildren($activities)
    {
        if (safeCount($activities) < 2) {
            return $activities;
        }

        $left = $right = [];
        $pivot_key = array_key_first($activities);
        $pivotActivity = array_shift($activities);
        $pivot = $pivotActivity->getChildOrder();

        foreach ($activities as $k => $activity) {
            $order = $activity->getChildOrder();
            if ($order < $pivot) {
                $left[$k] = $activity;
            } else {
                $right[$k] = $activity;
            }
        }

        return array_merge(
            $this->sortChildren($left),
            [$pivot_key => $pivotActivity],
            $this->sortChildren($right)
        );
    }

    /**
     * Get the order of the child
     *
     * @return int
     */
    public function getChildOrder()
    {
        $order = $this->sort_order;

        if (false !== strpos($order, '.')) {
            [$_, $order] = explode('.', $order);
        }

        return (int)$order;
    }

    private function retrieveBean($moduleName, $id)
    {
        return BeanFactory::retrieveBean($moduleName, $id);
    }

    /**
     * Get the parent task template
     *
     * @return DRI_Workflow_Task_Template
     */
    public function getParent()
    {
        return $this->hasParent() ? $this->retrieveBean('DRI_Workflow_Task_Templates', $this->parent_id) : null;
    }

    /**
     * Check if task template has parent
     *
     * @return bool
     */
    public function hasParent()
    {
        return !empty($this->parent_id);
    }

    /**
     * Check if task tamplate has stage template
     *
     * @return bool
     */
    public function hasStageTemplate()
    {
        return !empty($this->dri_subworkflow_template_id);
    }

    /**
     * Get the stage template
     *
     * @return DRI_SubWorkflow_Template
     */
    public function getStageTemplate()
    {
        return $this->hasStageTemplate() ? $this->retrieveBean('DRI_SubWorkflow_Templates', $this->dri_subworkflow_template_id) : null;
    }

    /**
     * Check if task tamplate has journey template
     *
     * @return bool
     */
    public function hasJourneyTemplate()
    {
        return !empty($this->dri_subworkflow_template_id);
    }

    /**
     * Get the journey template
     *
     * @return DRI_Workflow_Template
     */
    public function getJourneyTemplate()
    {
        return $this->hasJourneyTemplate() ? $this->retrieveBean('DRI_Workflow_Templates', $this->dri_workflow_template_id) : null;
    }

    /**
     * Get the previous stage template
     *
     * @return DRI_SubWorkflow_Template
     */
    public function getPreviousStageTemplate()
    {
        $subworkflowTemplateId = $this->fetched_row['dri_subworkflow_template_id'];
        return !empty($subworkflowTemplateId) ? $this->retrieveBean('DRI_SubWorkflow_Templates', $subworkflowTemplateId) : null;
    }

    /**
     * Get the blocked by task templates, if any exist
     *
     * @return DRI_Workflow_Task_Template[]
     */
    public function getBlockedBy()
    {
        if (empty($this->blocked_by)) {
            return [];
        }

        $blockedBy = [];

        foreach ($this->getBlockedByIds() as $id) {
            $blockedBy[] = BeanFactory::retrieveBean('DRI_Workflow_Task_Templates', $id);
        }

        return $blockedBy;
    }

    /**
     * Get the Ids for the task templates blocking the current task template
     *
     * @return string[]
     */
    public function getBlockedByIds()
    {
        if (empty($this->blocked_by)) {
            return [];
        }
        return $this->getblockedByArray('blocked_by');
    }

    /**
     * Get the Ids for the stage templates blocking the current task template
     *
     * @return string[]
     */
    public function getBlockedByStageIds()
    {
        if (empty($this->blocked_by_stages)) {
            return [];
        }
        return $this->getblockedByArray('blocked_by_stages');
    }

    private function getblockedByArray($blockedByField)
    {
        if (is_string($this->$blockedByField)) {
            return json_decode($this->$blockedByField, true);
        } elseif (is_array($this->$blockedByField)) {
            return $this->$blockedByField;
        } else {
            return [];
        }
    }

    /**
     * Check if task template is blocked
     *
     * @return bool
     */
    public function isBlocked()
    {
        return safeCount($this->getBlockedByIds()) > 0;
    }

    /**
     * Check if task template is blocked
     *
     * @return bool
     */
    public function hasBlockedBy()
    {
        return safeCount($this->getBlockedByIds()) > 0;
    }

    /**
     * Check if another task template with same name exists
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    private function validateUniqueName()
    {
        try {
            self::getByNameAndParent($this->name, $this->dri_subworkflow_template_id, $this->id);
            throw new SugarApiExceptionInvalidParameter(sprintf('Another Smart Guide template is already named %s.', $this->name));
        } catch (CJException\CustomerJourneyException $e) {
        }
    }

    /**
     * Set the sort order for the task templates
     */
    private function setSortOrder()
    {
        $parent_sort_order = null;
        if (empty($this->sort_order)) {
            $bean = \BeanFactory::newBean('DRI_Workflow_Task_Templates');
            $query = new \SugarQuery();
            $query->from($bean);
            $query->select('sort_order');
            $query->orderBy('sort_order', 'DESC');
            $query->limit(1);
            $query->where()->equals('dri_subworkflow_template_id', $this->dri_subworkflow_template_id);

            if (empty($this->parent_id)) {
                $query->where()->isNull('parent_id');
                $rows = $query->execute();

                if (empty($rows)) {
                    $this->sort_order = '1';
                } else {
                    $this->sort_order = $rows[0]['sort_order'] + 1;
                }
            } else {
                $query->where()->notNull('parent_id');
                $query->where()->equals('parent_id', $this->parent_id);
                $rows = $query->execute();

                if (empty($rows)) {
                    //If it is the first sub activity of parent then need to know the
                    //sort order of parent as well
                    $queryForParent = new \SugarQuery();
                    $queryForParent->from($bean);
                    $queryForParent->select('sort_order');
                    $queryForParent->orderBy('sort_order', 'DESC');
                    $queryForParent->limit(1);
                    $queryForParent->where()->equals('id', $this->parent_id);

                    $rowParent = $queryForParent->execute();
                    if (!empty($rowParent)) {
                        $parent_sort_order = $rowParent[0]['sort_order'];
                    }
                    $this->sort_order = $parent_sort_order . '.1';
                } else {
                    $sort_order = explode('.', $rows[0]['sort_order']);
                    $this->sort_order = $sort_order[0] . '.' . ($sort_order[1] + 1);
                }
            }
        }
    }

    /**
     * Set the journey template
     */
    private function setJourneyTemplate()
    {
        if ($this->hasStageTemplate()) {
            $stageTemplate = $this->getStageTemplate();

            if ($stageTemplate->hasJourneyTemplate()) {
                $journeyTemplate = $stageTemplate->getJourneyTemplate();

                $this->dri_workflow_template_id = $journeyTemplate->id;
                $this->dri_workflow_template_name = $journeyTemplate->name;
            }
        }
    }

    /**
     * Check if a task template has same sort order
     * For activities as well as child activties
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isDuplicateActivityByOrder()
    {
        $activities = DRI_SubWorkflow_Template::getById($this->dri_subworkflow_template_id)->getActivityTemplates();

        foreach ($activities as $activity) {
            //Duplicate Sort Order for Parent Activity has found
            if ($activity->sort_order === $this->sort_order) {
                return true;
            }

            foreach ($activity->getChildren() as $child) {
                //Duplicate Sort Order for Child has found
                if ($child->sort_order === $this->sort_order) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Moves duplicate stages forward and reorders accordingly
     *
     * @param \SugarBean $parent
     */
    public function moveDuplicatedActivitiesForward($parent)
    {
        $activities = DRI_SubWorkflow_Template::getById($this->dri_subworkflow_template_id)->getActivityTemplates();
        $this->reorderSortOrdersActivities($activities, $parent, 'move');
    }

    /**
     * After delete the activity, re-order the sort orders and labels of all the activities
     *
     * @param string $stage_template_id
     * @param \SugarBean $parent
     */
    public function reorderSortOrdersAndLabels($stage_template_id, $parent)
    {
        $activities = DRI_SubWorkflow_Template::getById($stage_template_id)->getActivityTemplates();
        $this->reorderSortOrdersActivities($activities, $parent, 'delete');
    }

    private function reorderSortOrdersActivities($activities, $parent, $moveOrDelete)
    {
        foreach ($activities as $activity) {
            if (empty($parent) && $activity->sort_order >= $this->sort_order) {
                $activity->sort_order = $this->getUpdatedSortOrder($activity->sort_order, $moveOrDelete);
                $this->updateSortOrder($activity->sort_order, $activity->id);
            }

            foreach ($activity->getChildren() as $child) {
                if (empty($parent) || (!empty($parent) && $child->sort_order >= $this->sort_order)) {
                    $child_sort_order = explode('.', $child->sort_order);
                    $firstChildSortOrder = $child_sort_order[0];
                    $secondChildSortOrder = $child_sort_order[1];
                    $child->sort_order = empty($parent) ?
                        $activity->sort_order . '.' . $firstChildSortOrder :
                        $secondChildSortOrder . '.' . $this->getUpdatedSortOrder($secondChildSortOrder, $moveOrDelete);
                    //Update the sort orders of remaining children
                    $this->updateSortOrder($child->sort_order, $child->id);
                }
            }
        }
    }

    private function getUpdatedSortOrder($sort_order, $moveOrDelete)
    {
        return $moveOrDelete === 'move' ? ++$sort_order : --$sort_order;
    }

    /**
     * Update the sort order of Activity Template
     *
     * @param string $sort_order
     * @param string $id
     */
    private function updateSortOrder($sort_order, $id)
    {
        if (empty($sort_order) || empty($id)) {
            return;
        }
        global $db;
        $sql = <<<SQL
                UPDATE
                    dri_workflow_task_templates
                SET
                    sort_order = ?
                WHERE
                    id = ?
SQL;
        $db->getConnection()->executeUpdate($sql, [$sort_order, $id]);
    }
}
