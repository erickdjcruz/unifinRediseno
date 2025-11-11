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

class DRI_SubWorkflow_Template extends Basic
{
    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'DRI_SubWorkflow_Templates';
    public $object_name = 'DRI_SubWorkflow_Template';
    public $table_name = 'dri_subworkflow_templates';
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
    public $sort_order;
    public $label;
    public $activities;
    public $following;
    public $following_link;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $points;
    public $related_activities;
    public $locked_fields;
    public $locked_fields_link;
    public $acl_team_set_id;
    public $acl_team_names;
    public $tasks;
    public $meetings;
    public $calls;

    /**
     * @var Link2
     */
    public $dri_subworkflows;

    /**
     * @var Link2
     */
    public $dri_workflow_task_templates;
    public $dri_workflow_template_id;
    public $dri_workflow_template_name;

    /**
     * @var Link2
     */
    public $dri_workflow_template_link;

    /**
     * Retrieves a DRI_SubWorkflow_Template with id $id and
     * returns a instance of the retrieved bean
     *
     * @param string $id : the id of the DRI_SubWorkflow_Template that should be retrieved
     * @return DRI_SubWorkflow_Template
     * @throws NotFoundException: if not found
     */
    public static function getById(string $id)
    {
        return CJBean\Repository::getInstance()->getById('DRI_SubWorkflow_Templates', $id);
    }

    /**
     * Retrieves a DRI_SubWorkflow_Template with name $name and
     * returns a instance of the retrieved bean
     *
     * @param string $name : the name of the DRI_SubWorkflow_Template that should be retrieved
     * @param string | null $parentId
     * @param string | null $skipId
     * @return DRI_SubWorkflow_Template
     * @throws NotFoundException
     */
    public static function getByNameAndParent(string $name, string $parentId = null, string $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByNameAndParent(
            [
                'table' => 'dri_subworkflow_templates',
                'module' => 'DRI_SubWorkflow_Templates',
                'name' => $name,
                'parentId' => $parentId,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * Retrieves a DRI_SubWorkflow_Template with name $name and
     * returns a instance of the retrieved bean
     *
     * @param string $sortOrder : the name of the DRI_SubWorkflow_Template that should be retrieved
     * @param string $parentId
     * @param string | null $skipId
     * @return DRI_SubWorkflow_Template
     * @throws NotFoundException
     */
    public static function getByOrderAndParent(string $sortOrder, string $parentId, string $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByOrderAndParent(
            [
                'table' => 'dri_subworkflow_templates',
                'module' => 'DRI_SubWorkflow_Templates',
                'sortOrder' => $sortOrder,
                'parentId' => $parentId,
                'skipId' => $skipId,
            ]
        );
    }

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
     * Get the activity templates
     *
     * @return DRI_Workflow_Task_Template[]
     * @throws SugarQueryException
     */
    public function getActivityTemplates()
    {
        $bean = BeanFactory::newBean('DRI_Workflow_Task_Templates');

        $query = new SugarQuery();
        $query->from($bean, ['team_security' => false]);
        $query->select('*');
        $query->where()
            ->equals('dri_subworkflow_template_id', $this->id)
            ->isEmpty('parent_id');

        $activities = $bean->fetchFromQuery($query);

        return $this->sortActivities($activities);
    }

    /**
     * Sort the activities using usort function
     * @param \DRI_Workflow_Task_Template[] $activities
     * @return array
     */
    private function sortActivities($activities)
    {
        usort($activities, ['DRI_SubWorkflow_Template', 'sortActivitiesCallback']);
        return $activities;
    }

    /**
     * This is the callback function used to sort activities
     * @param \DRI_Workflow_Task_Template $activity1
     * @param \DRI_Workflow_Task_Template $activity2
     * @return int
     */
    private function sortActivitiesCallback(
        \DRI_Workflow_Task_Template $activity1,
        \DRI_Workflow_Task_Template $activity2
    ) {

        $sortOrder1 = (int)$activity1->sort_order;
        $sortOrder2 = (int)$activity2->sort_order;
        return $sortOrder1 - $sortOrder2;
    }

    /**
     * Get the last task template
     *
     * @return DRI_Workflow_Task_Template
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function getLastTask()
    {
        $activityTemplates = $this->getActivityTemplates();

        if (safeCount($activityTemplates) === 0) {
            throw new SugarApiExceptionNotFound();
        }

        return array_pop($activityTemplates);
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false, $stageDeleted = false)
    {
        if ($stageDeleted !== true) {
            $this->validateUniqueName();
        }
        $this->calculatePoints();
        $this->calculateRelatedActivities();

        $isNew = !$this->isUpdate();

        if ($isNew && $this->isDuplicateStageByOrder()) {
            $this->moveDuplicatedStagesForward();
        }

        $nameChanged = isset($this->fetched_row['name']) && $this->fetched_row['name'] !== $this->name;

        $this->setSortOrder();
        $this->setLabel();

        $return = parent::save($check_notify);

        try {
            DRI_Workflow_Template::getById($this->dri_workflow_template_id)->save();
        } catch (\Exception $e) {
            $GLOBALS['log']->debug(__FILE__ . ' ' . __LINE__, $e->getMessage());
        }

        if (!$isNew && $nameChanged) {
            //For fetching the labels
            $query = new SugarQuery();
            $query->select('id', 'label');
            $query->from(BeanFactory::newBean('DRI_SubWorkflows'));
            $query->where()->equals('dri_subworkflow_template_id', $this->id);

            foreach ($query->execute() as $row) {
                $label = $row['label'];
                $label_split = explode('.', $label);
                $new_label = $label_split[0] . '. ' . $this->name;
                global $db;
                $sql = <<<SQL
                        UPDATE
                            dri_subworkflows
                        SET
                            name = ? , label = ?
                        WHERE
                            id = ?
SQL;
                $db->getConnection()->executeUpdate($sql, [$this->name, $new_label, $this->id]);
            }
        }
        return $return;
    }

    /**
     * Set the label
     */
    private function setLabel()
    {
        $order = "{$this->sort_order}";
        if (strlen($order) === 1) {
            $order = "0{$order}";
        }

        $this->label = sprintf('%s. %s', $order, $this->name);
    }

    /**
     * Send the webhooks
     *
     * @param string $trigger_event
     * @param array $request
     * @throws SugarApiException
     * @throws SugarQueryException
     */
    public function sendWebHooks(string $trigger_event, array $request)
    {
        \CJ_WebHook::send($this, $trigger_event, $request);
    }

    /**
     * Validate the unique name, if it already exists
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    private function validateUniqueName()
    {
        try {
            self::getByNameAndParent($this->name, $this->dri_workflow_template_id, $this->id);
            throw new SugarApiExceptionInvalidParameter(sprintf('Another Smart Guide template is already named %s.', $this->name));
        } catch (CJException\CustomerJourneyException $e) {
            $GLOBALS['log']->debug(__FILE__ . ' ' . __LINE__, $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mark_deleted($id)
    {
        if ($this->id !== $id) {
            $this->retrieve($id);
        }

        $activityTemplates = $this->getActivityTemplates();

        try {
            $journey = $this->getJourneyTemplate(false);
        } catch (\Exception $e) {
            $journey = null;
            $GLOBALS['log']->debug(__FILE__ . ' ' . __LINE__, $e->getMessage);
        }

        CJ_WebHook::deleteWebHooks($this);

        parent::mark_deleted($id);

        foreach ($activityTemplates as $activityTemplate) {
            $activityTemplate->mark_deleted($activityTemplate->id);
        }

        if (!is_null($journey) && !$journey->deleted) {
            $this->reorderSortOrdersAndLabels($journey->id);
            $journey->save();
        }
    }

    /**
     * Get the journey template
     *
     * @param bool $deleted : Set false if the bean is already deleted
     * @return DRI_Workflow_Template
     */
    public function getJourneyTemplate($deleted = true)
    {
        return DRI_Workflow_Template::getById($this->dri_workflow_template_id, $deleted);
    }

    /**
     * Check if journey template exists
     *
     * @return bool
     */
    public function hasJourneyTemplate()
    {
        return !empty($this->dri_workflow_template_id);
    }

    /**
     * Calculate the sum of points from task templates
     */
    private function calculatePoints()
    {
        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean('DRI_Workflow_Task_Templates'), ['team_security' => false]);
        $query->select()->fieldRaw('SUM(points)');
        $query->where()->equals('deleted', 0);
        $query->where()->equals('is_parent', 0);
        $query->where()->equals('dri_subworkflow_template_id', $this->id);
        $this->points = $query->getOne() ?: 0;
    }

    /**
     * Calculate the total related activities
     */
    private function calculateRelatedActivities()
    {
        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean('DRI_Workflow_Task_Templates'), ['team_security' => false]);
        $query->select()->setCountQuery();
        $query->where()->equals('deleted', 0);
        $query->where()->equals('dri_subworkflow_template_id', $this->id);
        $this->related_activities = $query->getOne() ?: 0;
    }

    /**
     * Set the sort order for the stage templates
     */
    private function setSortOrder()
    {
        if (empty($this->sort_order)) {
            $bean = \BeanFactory::newBean('DRI_SubWorkflow_Templates');

            $query = new \SugarQuery();
            $query->from($bean);
            $query->select('sort_order');
            $query->orderBy('sort_order', 'DESC');
            $query->limit(1);
            $query->where()->equals('dri_workflow_template_id', $this->dri_workflow_template_id);

            $rows = $query->execute();

            if (empty($rows)) {
                $this->sort_order = '1';
            } else {
                $this->sort_order = $rows[0]['sort_order'] + 1;
            }
        }
    }

    /**
     * Check if a stage template has same sort order
     *
     * @return bool
     * @throws NotFoundException
     * @throws SugarApiExceptionError
     */
    public function isDuplicateStageByOrder()
    {
        $stages = $this->getJourneyTemplate()->getStageTemplates();

        foreach ($stages as $stage) {
            if ($stage->sort_order === $this->sort_order) {
                return true;
            }
        }

        return false;
    }

    /**
     * Moves duplicate stages forward and reorders accordingly
     */
    public function moveDuplicatedStagesForward()
    {
        $this->reorderSortOrdersAndLabels($this->dri_workflow_template_id, 'add');
    }

    /**
     * After delete the stage, re-order the sort orders and labels of all the stages
     *
     * @param string $workflow_template_id
     * @param string $defaultSortOrderOperation
     */
    public function reorderSortOrdersAndLabels(string $workflow_template_id, string $defaultSortOrderOperation = 'minus')
    {
        $stages = DRI_Workflow_Template::getById($workflow_template_id)->getStageTemplates();
        CJBean\Repository::getInstance()->reorderSortOrdersAndLabels($this, $stages, $defaultSortOrderOperation, 'dri_subworkflow_templates');
    }
}
