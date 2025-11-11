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

require_once 'modules/CJ_WebHooks/CJ_WebHook.php';
require_once 'modules/CJ_Forms/CJ_Form.php';

class DRI_Workflow_Template extends Basic
{
    public const ASSIGNEE_RULE_NONE = 'none';
    public const ASSIGNEE_RULE_CREATE = 'create';
    public const ASSIGNEE_RULE_STAGE_START = 'stage_start';
    public const TARGET_ASSIGNEE_PARENT = 'parent_assignee';

    public const TARGET_ASSIGNEE_CURRENT_USER = 'current_user';
    public const TARGET_ASSIGNEE_USER = 'user';
    public const TARGET_ASSIGNEE_TEAM = 'team';
    public const TARGET_ASSIGNEE_USER_TEAM = 'user_team';

    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'DRI_Workflow_Templates';
    public $object_name = 'DRI_Workflow_Template';
    public $table_name = 'dri_workflow_templates';
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
    public $available_modules;
    public $assignee_rule;
    public $target_assignee;
    public $activities;
    public $following;
    public $following_link;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $points;
    public $related_activities;
    public $active;
    public $locked_fields;
    public $locked_fields_link;
    public $acl_team_set_id;
    public $acl_team_names;
    public $disabled_stage_actions;
    public $disabled_activity_actions;
    public $active_limit;
    public $type;

    /**
     * @var Link2
     */
    public $dri_workflows;

    /**
     * @var Link2
     */
    public $forms;

    /**
     * @var Link2
     */
    public $dri_subworkflow_templates;

    /**
     * @var string
     */
    public $copied_template_id;

    /**
     * @var string
     */
    public $copied_template_name;

    /**
     * @var Link2
     */
    public $copied_template_link;

    /**
     * @var Link2
     */
    public $copies;

    /**
     * @var Link2
     */
    public $web_hooks;

    /**
     * Retrieves a DRI_Workflow_Template with id $id and
     * returns an instance of the retrieved bean
     *
     * @param string $id : the id of the DRI_Workflow_Template that should be retrieved
     * @param bool $deleted : Set false if the bean is already deleted
     * @return DRI_Workflow_Template
     * @throws NotFoundException: if not found
     */
    public static function getById($id, $deleted = true)
    {
        return CJBean\Repository::getInstance()->getById('DRI_Workflow_Templates', $id, $deleted);
    }

    /**
     * Retrieves a DRI_Workflow_Template with id of activity
     * returns an instance of the retrieved bean
     *
     * @param SugarBean $activity : the activity for which template bean is required
     * @return DRI_Workflow_Template
     * @throws NotFoundException: if not found
     */
    public static function getBeanByActivityId($activity)
    {
        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('DRI_Workflows'));
        $query->select(['dri_workflow_template_id']);
        $join = $query->joinTable('dri_subworkflows', ['joinType' => 'INNER']);
        $join->on()->equals('dri_subworkflows.id', $activity->dri_subworkflow_id);
        $join->on()->equals('dri_subworkflows.deleted', 0);
        $query->where()->equalsField('dri_workflows.id', 'dri_subworkflows.dri_workflow_id');

        $result = $query->getOne();
        if (!empty($result) && $result) {
            return CJBean\Repository::getInstance()->getById('DRI_Workflow_Templates', $result, 0);
        }
    }

    /**
     * Retrieves a DRI_Workflow_Template with name $name and
     * returns an instance of the retrieved bean
     *
     * @param string $name : the name of the DRI_Workflow_Template that should be retrieved
     * @param string $skipId
     * @return DRI_Workflow_Template
     * @throws NotFoundException
     */
    public static function getByName($name, $skipId = null)
    {
        return CJBean\Repository::getInstance()->getByName(
            [
                'table' => 'dri_workflow_templates',
                'module' => 'DRI_Workflow_Templates',
                'name' => $name,
                'skipId' => $skipId,
            ]
        );
    }

    /**
     * Get all the journey templates
     *
     * @return DRI_Workflow_Template[]
     * @throws SugarQueryException
     * @throws NotFoundException
     */
    public static function all()
    {
        $query = new SugarQuery();
        $query->from(new self());
        $query->select(['id']);
        $results = $query->execute();

        $templates = [];

        foreach ($results as $result) {
            $templates[] = self::getById($result['id']);
        }

        return $templates;
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
     * Send the webhooks
     *
     * @param string $trigger_event
     * @param array $request
     * @throws SugarApiException
     * @throws SugarQueryException
     */
    public function sendWebHooks($trigger_event, array $request)
    {
        \CJ_WebHook::send($this, $trigger_event, $request);
    }

    /**
     * Get the actions which are disabled for the stage
     *
     * @return array|null
     */
    public function getDisabledStageActions()
    {
        return !empty($this->disabled_stage_actions)
            ? unencodeMultienum($this->disabled_stage_actions)
            : [];
    }

    /**
     * Get the actions which are disabled for the activity
     *
     * @return array|null
     */
    public function getDisabledActivityActions()
    {
        return !empty($this->disabled_activity_actions)
            ? unencodeMultienum($this->disabled_activity_actions)
            : [];
    }

    /**
     * Get the stage templates linked with the journey template
     *
     * @return DRI_SubWorkflow_Template[]
     * @throws SugarQueryException
     */
    public function getStageTemplates()
    {
        $bean = \BeanFactory::newBean('DRI_SubWorkflow_Templates');

        $query = new \SugarQuery();
        $query->from($bean, ['team_security' => false]);
        $query->select('*');
        $query->orderBy('sort_order', 'ASC');
        $query->where()
            ->equals('dri_workflow_template_id', $this->id);

        return $bean->fetchFromQuery($query);
    }

    /**
     * Get the last stage template
     *
     * @return DRI_SubWorkflow_Template
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function getLastStage()
    {
        $stages = $this->getStageTemplates();

        if (safeCount($stages) === 0) {
            throw new SugarApiExceptionNotFound();
        }

        return array_pop($stages);
    }

    /**
     * Hide stage number if field is set to hide
     *
     * @throws CustomerJourneyException\NotFoundException
     * @throws \SugarApiExceptionInvalidParameter
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
        $bean = \BeanFactory::newBean('DRI_SubWorkflow_Templates');
        $query = new \SugarQuery();
        $query->from($bean, ['team_security' => false]);
        $query->select('*');
        $query
            ->where()
            ->equals('dri_workflow_template_id', $this->id);

        return $bean->fetchFromQuery($query);
    }

    /**
     * Get the stage templates linked with the journey template
     *
     * @param string $templateID
     * @param string $table
     * @param string $column
     * @param string $name
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
     * Show stage numbers along with stage on name
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
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $this->validateUniqueName();

        $isNew = !$this->isUpdate();
        $nameChanged = isset($this->fetched_row['name']) && $this->fetched_row['name'] !== $this->name;

        $this->calculatePoints();
        $this->calculateRelatedActivities();
        $this->isHideSet();

        $return = parent::save($check_notify);

        if ($isNew && !empty($this->copied_template_id)) {
            $this->copyFromTemplate();
        }

        if (!$isNew && $nameChanged) {
            $this->syncJourneyNames();
        }

        return $return;
    }

    /**
     * Copy stage, activity and child activities templates
     * Copy webhooks and forms as well for each
     *
     * @throws SugarQueryException
     * @throws SugarApiExceptionInvalidParameter
     * @throws NotFoundException
     */
    private function copyFromTemplate()
    {
        try {
            $template = self::getById($this->copied_template_id);

            $this->dri_subworkflow_templates = null;
            $this->load_relationship('dri_subworkflow_templates');
            $this->copyFromTemplatesHelper($template);
            CJ_WebHook::copyWebHooks($this, $template);
            CJ_Form::copyForms($this, $this, $template);
        } catch (CJException\NotFoundException $e) {
            if ($e->getModuleName() === 'DRI_Workflow_Templates' && !empty($this->copied_template_id)) {
                throw new CJException\NotFoundException(null, ['moduleName', 'DRI_Workflow_Templates', 'data' => $this->copied_template_id]);
            }
        }
    }

    private function copyFromTemplatesHelper($template)
    {
        $assigneeRuleFlag = false;
        $assigneeRuleChildFlag = false;
        $startDateActivityFlag = false;
        $startDateActivityChildFlag = false;
        $dueDateActivityFlag = false;
        $dueDateActivityChildFlag = false;
        $momentumStartActivityFlag = false;
        $momentumStartActivityChildFlag = false;
        foreach ($template->getStageTemplates() as $stageTemplateBase) {
            $stageTemplate = $this->cloneAndCopyTemplateValues($stageTemplateBase, [
                'dri_workflow_template_id' => $this->id,
                'dri_workflow_template_name' => $this->name,
            ]);

            $this->dri_subworkflow_templates->add($stageTemplate);

            $stageTemplate->dri_workflow_task_templates = null;
            $stageTemplate->load_relationship('dri_workflow_task_templates');
            foreach ($stageTemplateBase->getActivityTemplates() as $activityTemplateBase) {
                $activityTemplate = $this->cloneAndCopyTemplateValues($activityTemplateBase, [
                    'dri_subworkflow_template_id' => $stageTemplate->id,
                    'dri_subworkflow_template_name' => $stageTemplate->name,
                ]);
                if (!empty($activityTemplate->assignee_rule_activity_id)) {
                    $assigneeRuleFlag = true;
                    $activityTemplatesID[$activityTemplate->assignee_rule_activity_id][] = $activityTemplate->id;
                }
                if (!empty($activityTemplate->start_date_activity_id)) {
                    $startDateActivityFlag = true;
                    $startDateActivityTemplatesID[$activityTemplate->start_date_activity_id][] = $activityTemplate->id;
                }
                if (!empty($activityTemplate->due_date_activity_id)) {
                    $dueDateActivityFlag = true;
                    $dueDateActivityTemplatesID[$activityTemplate->due_date_activity_id][] = $activityTemplate->id;
                }
                if (!empty($activityTemplate->momentum_start_activity_id)) {
                    $momentumStartActivityFlag = true;
                    $momentumStartActivityTemplatesID[$activityTemplate->momentum_start_activity_id][] = $activityTemplate->id;
                }
                $stageTemplate->dri_workflow_task_templates->add($activityTemplate);

                foreach ($activityTemplateBase->getChildren() as $childTemplateBase) {
                    $childTemplate = $this->cloneAndCopyTemplateValues($childTemplateBase, [
                        'parent_id' => $activityTemplate->id,
                        'dri_subworkflow_template_id' => $stageTemplate->id,
                        'dri_subworkflow_template_name' => $stageTemplate->name,
                        'parent_name' => $activityTemplate->name,
                    ]);
                    if (!empty($childTemplate->assignee_rule_activity_id)) {
                        $assigneeRuleChildFlag = true;
                        $activityTemplatesID[$childTemplate->assignee_rule_activity_id][] = $childTemplate->id;
                    }
                    if (!empty($childTemplate->start_date_activity_id)) {
                        $startDateActivityChildFlag = true;
                        $startDateActivityTemplatesID[$childTemplate->start_date_activity_id][] = $childTemplate->id;
                    }
                    if (!empty($childTemplate->due_date_activity_id)) {
                        $dueDateActivityChildFlag = true;
                        $dueDateActivityTemplatesID[$childTemplate->due_date_activity_id][] = $childTemplate->id;
                    }
                    if (!empty($childTemplate->momentum_start_activity_id)) {
                        $momentumStartActivityChildFlag = true;
                        $momentumStartActivityTemplatesID[$childTemplate->momentum_start_activity_id][] = $childTemplate->id;
                    }
                    $this->copyAndRegisterBean($template, $childTemplate, $childTemplateBase);
                }

                $this->copyAndRegisterBean($template, $activityTemplate, $activityTemplateBase);
            }
            if ($assigneeRuleChildFlag || $assigneeRuleFlag) {
                $this->setCopiedTemplateData($activityTemplatesID, $template->id, $this->id, 'assignee_rule_activity_id');
            }
            if ($startDateActivityFlag || $startDateActivityChildFlag) {
                $this->setCopiedTemplateData($startDateActivityTemplatesID, $template->id, $this->id, 'start_date_activity_id');
            }
            if ($dueDateActivityChildFlag || $dueDateActivityFlag) {
                $this->setCopiedTemplateData($dueDateActivityTemplatesID, $template->id, $this->id, 'due_date_activity_id');
            }
            if ($momentumStartActivityChildFlag || $momentumStartActivityFlag) {
                $this->setCopiedTemplateData($momentumStartActivityTemplatesID, $template->id, $this->id, 'momentum_start_activity_id');
            }
            $this->copyAndRegisterBean($template, $stageTemplate, $stageTemplateBase);
        }
    }

    /**
     * set the data to the copied Template
     *
     * @param array $templateID
     * @param string $parentTemplateID
     * @param string $update
     * @param string $id
    */
    private function setCopiedTemplateData($templateID, $parentTemplateID, $id, $update)
    {
        foreach ($templateID as $parentActivityID => $tempActivityID) {
            $activityName = $this->getActivityName($parentTemplateID, $parentActivityID);
            $activityID = $this->setCopiedID($id, $activityName, 'dri_workflow_template_id');
            if ($activityID) {
                $this->updateCopiedActivityID($tempActivityID, $activityID, $update);
            }
        }
    }

    /**
     * Provide the ID of the assigne rule activity
     *
     * @param array $templateID
     * @param string $activityID
     * @param string $update
    */
    private function updateCopiedActivityID($templateID, $activityID, $update)
    {
        foreach ($templateID as $key => $idValue) {
            $qb = DBManagerFactory::getConnection()->createQueryBuilder();
            $qb->update('dri_workflow_task_templates')
            ->set($update, $qb->expr()->literal($activityID))
                ->where($qb->expr()->eq('id', $qb->expr()->literal($idValue)));
            $qb->execute();
        }
    }

    /**
     * Set te assignee rule acitivity id
     *
     * @param string $stageID
     * @param string $acivityName
    */
    private function setCopiedID($templateID, $acivityName, $update)
    {
        $stageTemplateBean = \BeanFactory::newBean('DRI_Workflow_Task_Templates');
        $query = new \SugarQuery();
        $query->select('id');
        $query->from($stageTemplateBean);
        $query->where()->equals($update, $templateID)
            ->equals('name', $acivityName);
        return $query->getOne();
    }

     /**
     * Get the name of  activity
     *
     * @param string $parentStageID
     * @param string $parentStageAcivityID
    */
    private function getActivityName($parentStageID, $parentStageAcivityID)
    {
        $stageTemplateBean = \BeanFactory::newBean('DRI_Workflow_Task_Templates');
        $query = new \SugarQuery();
        $query->select('name');
        $query->from($stageTemplateBean);
        $query->where()->equals('dri_workflow_template_id', $parentStageID)
            ->equals('id', $parentStageAcivityID);
        return $query->getOne();
    }

    private function cloneAndCopyTemplateValues($templateBase, $copyFieldsArr)
    {
        $template = clone $templateBase;
        $template->id = \Sugarcrm\Sugarcrm\Util\Uuid::uuid1();
        $template->new_with_id = true;
        foreach ($copyFieldsArr as $fieldName => $value) {
            $template->$fieldName = $value;
        }
        $template->save();
        return $template;
    }

    private function copyAndRegisterBean($template, $cjTemplate, $templateBase)
    {
        CJ_WebHook::copyWebHooks($cjTemplate, $templateBase);
        CJ_Form::copyForms($template, $cjTemplate, $templateBase);
        BeanFactory::unregisterBean($cjTemplate);
    }

    /**
     * {@inheritdoc}
     */
    public function mark_deleted($id)
    {
        if ($this->id !== $id) {
            $this->retrieve($id);
        }

        $this->checkIfTemplateInUse($id);

        $subWorkflowTemplates = $this->getStageTemplates();
        CJ_WebHook::deleteWebHooks($this);

        parent::mark_deleted($id);

        foreach ($subWorkflowTemplates as $subWorkflowTemplate) {
            $subWorkflowTemplate->mark_deleted($subWorkflowTemplate->id);
        }
    }

    /**
     * Check if a journey template is in use so that it cannot be deleted
     *
     * @param string $id
     * @throws SugarApiExceptionInvalidParameter
     */
    private function checkIfTemplateInUse($id)
    {
        if (!empty($id)) {
            $query = new SugarQuery();
            $query->select()->setCountQuery();
            $query->from(BeanFactory::newBean('DRI_Workflows'));
            $query
                ->where()
                ->queryAnd()
                ->equals('dri_workflow_template_id', $id)
                ->equals('deleted', 0);
            $journeys = $query->execute();

            if ($journeys[0]['record_count'] > 0) {
                throw new SugarApiExceptionInvalidParameter(
                    'This Smart Guide template is being used in active Smart Guides and cannot be deleted.'
                );
            }
        }
    }

    /**
     * Chek if a journey template with the same name already exists
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    private function validateUniqueName()
    {
        try {
            self::getByName($this->name, $this->id);
            throw new SugarApiExceptionInvalidParameter(sprintf('Another Smart Guide template is already named %s.', $this->name));
        } catch (CJException\CustomerJourneyException $e) {
        }
    }

    /**
     * Calculate the points from the linked stage templates
     */
    private function calculatePoints()
    {
        $this->points = $this->getSumCalculationQuery('points');
    }

    /**
     * Calculate the related activities from the linked stage templates
     */
    private function calculateRelatedActivities()
    {
        $this->related_activities = $this->getSumCalculationQuery('related_activities');
    }

    private function getSumCalculationQuery($sumFieldName)
    {
        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean('DRI_SubWorkflow_Templates'), ['team_security' => false]);
        $query->select()->fieldRaw("SUM($sumFieldName)");
        $query->where()->equals('deleted', 0);
        $query->where()->equals('dri_workflow_template_id', $this->id);
        return $query->getOne() ?: 0;
    }

    /**
     * Sync the names of the creaeted journeys with the journey template
     */
    private function syncJourneyNames()
    {
        $query = new SugarQuery();
        $query->select('id');
        $query->from(BeanFactory::newBean('DRI_Workflows'));
        $query->where()->equals('dri_workflow_template_id', $this->id);

        foreach ($query->execute() as $row) {
            $journey = BeanFactory::retrieveBean('DRI_Workflows', $row['id'], ['use_cache' => false]);

            if ($journey) {
                $journey->save();
            }
        }
    }
}
