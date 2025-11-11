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

/**
 * This class here to provide functions for the
 * stage processing on Activities
 */
class StageHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
     */
    private $activityHelper;

    /**
     * @var mixed
     */
    private $stageCache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activityHelper = ActivityHelper::getInstance();
    }

    /**
     * Set the stage for activity
     *
     * @param \DRI_SubWorkflow $stage
     */
    public function setStage(\DRI_SubWorkflow $stage)
    {
        $this->stageCache = $stage;
    }

    /**
     * Retrieves the activity's related stage
     *
     * @param \SugarBean $activity
     * @return \DRI_SubWorkflow
     */
    public function getStage(\SugarBean $activity)
    {
        if (null === $this->stageCache) {
            $this->stageCache = \DRI_SubWorkflow::getById($this->getStageId($activity));
        }
        return $this->stageCache;
    }

    /**
     * Retrieves the activity's related stage id
     *
     * @param \SugarBean $activity
     * @return string
     */
    public function getStageId(\SugarBean $activity)
    {
        if (!empty($activity->dri_subworkflow_id)) {
            return $activity->dri_subworkflow_id;
        }

        if ($activity->deleted && !empty($activity->fetched_row['dri_subworkflow_id'])) {
            return $activity->fetched_row['dri_subworkflow_id'];
        }

        return null;
    }

    /**
     * Checks if a activity with a given order exist on stage
     *
     * @param string $stageId
     * @param int $order
     * @param string $skipId
     * @return bool
     * @throws \SugarQueryException
     */
    public function orderExistOnStage($stageId, $order, $skipId, $module_name = '')
    {
        $query = new \SugarQuery();
        $query->from($this->activityHelper->create($module_name));
        $query->select('id');
        $where = $query->where();
        $where->equals('dri_workflow_sort_order', $order);
        $where->equals('dri_subworkflow_id', $stageId);

        if (!empty($skipId)) {
            $where->notEquals('id', $skipId);
        }

        $results = $query->execute();

        return safeCount($results) > 0;
    }

    /**
     * Checks if a activity is a stage activity
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function isStageActivity(\SugarBean $activity)
    {
        return !empty($activity->dri_subworkflow_id) || ($activity->deleted && !empty($activity->fetched_row['dri_subworkflow_id']));
    }

    /**
     * Populates a activity from the stage
     *
     * @param \SugarBean $activity
     * @param \SugarBean $parent
     * @param \DRI_SubWorkflow $stage
     * @param \DRI_Workflow_Task_Template $activityTemplate
     */
    public function populateFromStage(
        \SugarBean                  $activity,
        \SugarBean                  $parent,
        \DRI_SubWorkflow            $stage,
        \DRI_Workflow_Task_Template $activityTemplate
    ) {

        $activity->dri_subworkflow_id = $stage->id;
        $activity->dri_subworkflow_name = $stage->name;
        $activity->dri_workflow_id = $stage->dri_workflow_id;
        $activity->dri_workflow_name = $stage->dri_workflow_name;

        if (empty($GLOBALS['current_user']->id)) {
            $activity->update_modified_by = false;
            $activity->set_created_by = false;
            $activity->created_by = $stage->created_by;
            $activity->modified_user_id = $stage->modified_user_id;
        }

        if ($activityTemplate->getAssigneeRule($stage) === \DRI_Workflow_Template::ASSIGNEE_RULE_CREATE) {
            $activity->assigned_user_id = $this->activityHelper->getTargetAssigneeId(
                $stage,
                $activityTemplate,
                $activity,
                $parent
            );
            $activity->team_id = $this->activityHelper->getTargetTeamId(
                $stage,
                $activityTemplate,
                $parent
            );
            $activity->team_set_id = $this->activityHelper->getTargetTeamSetId(
                $stage,
                $activityTemplate,
                $parent
            );
        }
    }
}
