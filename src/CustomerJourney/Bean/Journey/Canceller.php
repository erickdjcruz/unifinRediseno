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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityHelper;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage\StateCalculator as StageStateCalculator;
use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\ActivityHooksHelper;

class Canceller
{
    /**
     * Cancel all activities against the Journey
     *
     * @param \DRI_Workflow $journey
     * @return array
     * @throws CJException\NotFoundException
     */
    public function cancel(\DRI_Workflow $journey)
    {
        $response = [];
        try {
            $template = \DRI_Workflow_Template::getById($journey->dri_workflow_template_id);

            if ($template->cancel_action === 'remove_open_activities') {
                $this->cancelActionRemoveAllActivities($journey);
            } else {
                $response = $this->cancelActionSetNotApplicable($journey);
            }

            $journey = $journey->retrieve($journey->id);

            if ($journey->state !== \DRI_Workflow::STATE_CANCELLED &&
                $response['activity_change_not_allowed'] === false &&
                $response['is_child_read_only'] === false) {
                $journey->state = \DRI_Workflow::STATE_CANCELLED;
                $journey->save();
            }
        } catch (CJException\NotFoundException $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * Remove all the In-Progress and Not Started Activities
     * @param \DRI_Workflow $journey
     */
    private function cancelActionRemoveAllActivities(\DRI_Workflow $journey)
    {
        foreach ($journey->getStages() as $stage) {
            if (!isset($journey->process_activities)) {
                foreach ($stage->getActivities() as $activity) {
                    $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

                    if ($activityHandler->isParent($activity)) {
                        foreach ($activityHandler->getChildren($activity) as $child) {
                            $childHandler = ActivityHandlerFactory::factory($child->module_dir);
                            if (!$childHandler->isCompleted($child)) {
                                $child->mark_deleted($child->id);
                            }
                        }
                        $activityHandler->resetChildren();

                        //If no child found then need to delete the parent as well
                        if (empty($activityHandler->getChildren($activity))) {
                            $activity->mark_deleted($activity->id);
                        }
                    } elseif (!$activityHandler->isCompleted($activity)) {
                        $activity->mark_deleted($activity->id);
                    }
                }
            }
            $stage = $stage->retrieve($stage->id);
            $stage->state = \DRI_SubWorkflow::STATE_COMPLETED;
            $stage->save();
        }
    }

    /**
     * Set activities deffered on Cancel Action.
     *
     * @param \DRI_Workflow $journey
     * @return bool
     */
    private function cancelActionSetNotApplicable(\DRI_Workflow $journey)
    {
        $blocked = [];
        $blockedByStage = [];
        $statusReadOnlyActivities = [];
        $statusReadOnlyActivityStages = [];
        $activityChangeNotAllowedStages = [];
        $response = [];
        $response['activity_change_not_allowed'] = false;
        $response['is_child_read_only'] = false;
        $activityHookHelper = new ActivityHooksHelper();

        foreach ($journey->getStages() as $stage) {
            $isCompleted = true;

            if ($stage->state !== \DRI_SubWorkflow::STATE_COMPLETED) {
                $isCompleted = false;
            }
            $activityHelper = ActivityHelper::getInstance();
            if (!isset($journey->process_activities)) {
                foreach ($stage->getActivities() as $activity) {
                    $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

                    if ($activityHandler->isParent($activity)) {
                        $blockedActivity = [];
                        $blockedByStageActivity = [];
                        [$blockedActivity, $blockedByStageActivity] = $this->prepareBlockedArrayForParentActivity(
                            $activity,
                            $activityHandler
                        );
                        foreach ($blockedActivity as $currentBlockedActivity) {
                            $blocked[$currentBlockedActivity->id] = $currentBlockedActivity;
                        }
                        foreach ($blockedByStageActivity as $currentBlockedActivity) {
                            $blocked[$currentBlockedActivity->id] = $currentBlockedActivity;
                        }
                        $childActivities = $activityHandler->getChildren($activity);
                        foreach ($childActivities as $childActivity) {
                            $childActivityHandler = ActivityHandlerFactory::factory($childActivity->module_dir);
                            if (!$childActivityHandler->isBlocked($childActivity) && !$childActivityHandler->isBlockedByStage($childActivity)) {
                                if (!$childActivityHandler->isStatusReadOnly($childActivity)) {
                                    $activityOldStatus = $childActivity->status;
                                    $childActivityHandler->setStatus($childActivity, $childActivityHandler->getNotApplicableStatus($childActivity));
                                    $childActivity->processing_smart_guide = true;
                                    $activityHookHelper->validateAllowedBy($childActivity, $childActivityHandler);
                                    if (ActivityHooksHelper::getAllowedByError()) {
                                        ActivityHooksHelper::resetAllowedByError();
                                        $activityChangeNotAllowedStages[$stage->id] = true;
                                        $childActivityHandler->setStatus($childActivity, $activityOldStatus);
                                    } else {
                                        $childActivityHandler->setStatus($childActivity, $childActivityHandler->getNotApplicableStatus($activity));
                                        $activityHelper->calculate($childActivity);
                                        $childActivity->save();
                                    }
                                } else {
                                    $statusReadOnlyActivities[] = $childActivity->id;
                                    $statusReadOnlyActivityStages[$stage->id] = true;
                                }
                            }
                        }
                    } elseif (!$activityHandler->isCompleted($activity)) {
                        if ($activityHandler->isBlocked($activity)) {
                            $blocked[$activity->id] = $activity;
                        }
                        if ($activityHandler->isBlockedByStage($activity)) {
                            $blockedByStage[$activity->id] = $activity;
                        }
                        if (!$activityHandler->isBlocked($activity) && !$activityHandler->isBlockedByStage($activity)) {
                            if (!$activityHandler->isStatusReadOnly($activity)) {
                                $activityOldStatus = $activity->status;
                                $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                                $activity->processing_smart_guide = true;
                                $activityHookHelper->validateAllowedBy($activity, $activityHandler);
                                if (ActivityHooksHelper::getAllowedByError()) {
                                    ActivityHooksHelper::resetAllowedByError();
                                    $activityChangeNotAllowedStages[$stage->id] = true;
                                    $activityHandler->setStatus($activity, $activityOldStatus);
                                } else {
                                    $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                                    $activityHelper->calculate($activity);
                                    $activity->save();
                                }
                            } else {
                                $statusReadOnlyActivities[] = $activity->id;
                                $statusReadOnlyActivityStages[$stage->id] = true;
                            }
                        }
                    }
                }
            }
            if (!empty($blockedByStage)) {
                $this->resolveBlockByStages($blockedByStage);
            }
    
            foreach ($blocked as $activity) {
                $this->resolveBlock($activity, $blocked);
            }

            //Mark activities as "Not Applicable" which were blocked by stages
            foreach ($blockedByStage as $id => $activity) {
                if (!$activityHandler->isStatusReadOnly($activity)) {
                    $activityOldStatus = $activity->status;
                    $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                    $activity->processing_smart_guide = true;
                    $activityHookHelper->validateAllowedBy($activity, $activityHandler);
                    if (ActivityHooksHelper::getAllowedByError()) {
                        ActivityHooksHelper::resetAllowedByError();
                            $activityChangeNotAllowedStages[$stage->id] = true;
                            $activityHandler->setStatus($activity, $activityOldStatus);
                    } else {
                        $activity->retrieve($activity->id);
                        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);
                        $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                        $activity->save();
                    }
                } else {
                    $statusReadOnlyActivities[] = $activity->id;
                    $statusReadOnlyActivityStages[$id] = true;
                }
            }

            if (!$isCompleted && !isset($statusReadOnlyActivityStages[$stage->id]) && !isset($activityChangeNotAllowedStages[$stage->id])) {
                $stage->state = \DRI_SubWorkflow::STATE_CANCELLED;
                $stage->save();
            }
        }

        $response['is_child_read_only'] = safeCount($statusReadOnlyActivities) > 0 ? true : false;
        $response['activity_change_not_allowed'] = safeCount($activityChangeNotAllowedStages) > 0 ? true : false;
        return $response;
    }

    /**
     * Prepare the blocked activites data for parent activity
     *
     * @param object $activity
     * @param object $activityHandler
     * @return array
     */
    private function prepareBlockedArrayForParentActivity($activity, $activityHandler)
    {
        $blocked = [];
        $blockedByStage = [];
        $activityHookHelper = new ActivityHooksHelper();

        if ($activityHandler->isBlocked($activity) && !$activityHandler->isCompleted($activity)) {
            $blocked[$activity->id] = $activity;
        }
        if ($activityHandler->isBlockedByStage($activity) && !$activityHandler->isCompleted($activity)) {
            $blockedByStage[$activity->id] = $activity;
        }
        foreach ($activityHandler->getChildren($activity) as $child) {
            $childHandler = ActivityHandlerFactory::factory($child->module_dir);

            if (!$childHandler->isCompleted($child)) {
                if ($childHandler->isBlocked($child)) {
                    $blocked[$child->id] = $child;
                }
                if ($childHandler->isBlockedByStage($child)) {
                    $blockedByStage[$child->id] = $child;
                }
                if ((!$activityHandler->isBlocked($activity) && !$childHandler->isBlocked($child)) &&
                    (!$activityHandler->isBlockedByStage($activity) && !$childHandler->isBlockedByStage($child) &&
                        !$childHandler->isStatusReadOnly($child))
                ) {
                    $activityOldStatus = $child->status;
                    $activityHandler->setStatus($child, $childHandler->getNotApplicableStatus($activity));
                    $child->processing_smart_guide = true;
                    $activityHookHelper->validateAllowedBy($child, $childHandler);
                    if (ActivityHooksHelper::getAllowedByError()) {
                        ActivityHooksHelper::resetAllowedByError();
                        $childHandler->setStatus($child, $activityOldStatus);
                    } else {
                        $childHandler->setStatus($child, $childHandler->getNotApplicableStatus($child));
                        $child->save();
                    }
                }
            }
        }

        return [$blocked, $blockedByStage];
    }

    /**
     * Resolve the block activities and it's children
     *
     * @param SugarBean $activity
     * @param SugarBean[] $blocked
     */
    private function resolveBlock(\SugarBean $activity, array $blocked)
    {
        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);
        if (!$activityHandler->isCompleted($activity)) {
            $blockedByModuleNames = [];
            $blockedByList = $activityHandler->getBlockedByActivityIds($activity, $blockedByModuleNames);
            foreach ($activityHandler->getBlockedByActivityIds($activity) as $blockedById) {
                $moduleName = $blockedByModuleNames[$blockedById];
                $blockedByHandler = ActivityHandlerFactory::factory($moduleName);
                $blockedBy = $blocked[$blockedById];
                if (!$blockedByHandler->isBlocked($activity) && !$blockedByHandler->isStatusReadOnly($blockedBy)) {
                    $blockedByHandler->setStatus($blockedBy, $blockedByHandler->getNotApplicableStatus($blockedBy));
                    $blockedBy->save();
                } elseif ($blockedBy instanceof \SugarBean) {
                    $this->resolveBlock($blockedBy, $blocked);
                }
            }
            if (!$activityHandler->isBlocked($activity) && !$activityHandler->isStatusReadOnly($activity)) {
                $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                $activity->save();
                $this->markChildrenCompletedWhenBlockedParentCompleted($activity, $activityHandler);
            }
        }
    }

    /**
     * Resolve those activities which are blocked by stages
     *
     * @param array $blockedByStage
     */
    private function resolveBlockByStages($blockedByStage)
    {
        if (empty($blockedByStage)) {
            return;
        }

        global $db;
        foreach ($blockedByStage as $id => $activity) {
            $table = $activity->getTableName();

            $sql = <<<SQL
                    UPDATE
                        {$table}
                    SET
                        cj_blocked_by_stages = ?
                    WHERE
                        id = ?
SQL;
            $db->getConnection()->executeUpdate($sql, ['', $id]);
        }
    }

    /**
     * Mark the Children Activities as Completed when The Blocked Parent Activity Completed
     *
     * @param object $activity
     * @param object $activityHandler
     */
    private function markChildrenCompletedWhenBlockedParentCompleted($activity, $activityHandler)
    {
        if ($activityHandler->isParent($activity)) {
            foreach ($activityHandler->getChildren($activity) as $child) {
                $childHandler = ActivityHandlerFactory::factory($child->module_dir);
                if (!$childHandler->isCompleted($child) && !$childHandler->isBlocked($child) && !$childHandler->isStatusReadOnly($child)) {
                    $childHandler->setStatus($child, $childHandler->getNotApplicableStatus($child));
                    $child->save();
                }
            }
        }
    }

    /**
     * Mark the activities as not applicable
     *
     * @param \SugarBean $activity
     * @return bool
     */
    public function notApplicableActivity(\SugarBean $activity)
    {
        $blocked = [];
        $isChildReadOnly = false;
        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);
        $activityHookHelper = new ActivityHooksHelper();
        $response = [];
        $response['is_valid_parent'] = true;
        $response['activity_change_not_allowed'] = false;

        if ($activityHandler->isParent($activity)) {
            $parentActivityOldStatus = $activity->status;
            $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));

            $activity->processing_smart_guide = true;
            $activityHookHelper->validateAllowedBy($activity, $activityHandler);
            $response['is_valid_parent'] = !ActivityHooksHelper::getAllowedByError();
            ActivityHooksHelper::resetAllowedByError();
            if ($response['is_valid_parent']) {
                foreach ($activityHandler->getChildren($activity) as $child) {
                    $childHandler = ActivityHandlerFactory::factory($child->module_dir);
                    if (!$childHandler->isCompleted($child)) {
                        $childActivityOldStatus = $child->status;
                        $childHandler->setStatus($child, $childHandler->getNotApplicableStatus($child));
                        $child->processing_smart_guide = true;
                        $activityHookHelper->validateAllowedBy($child, $childHandler);
                        if (ActivityHooksHelper::getAllowedByError()) {
                            ActivityHooksHelper::resetAllowedByError();
                            if (!$response['activity_change_not_allowed']) {
                                $response['activity_change_not_allowed'] = true;
                            }
                            if ($response['activity_change_not_allowed']) {
                                $childHandler->setStatus($child, $childActivityOldStatus);
                                $activityHandler->setStatus($activity, $parentActivityOldStatus);
                                continue;
                            }
                        }
                        if ($childHandler->isBlocked($child)) {
                            $blocked[$child->id] = $child;
                        } elseif (!$childHandler->isStatusReadOnly($child)) {
                            $childHandler->setStatus($child, $childHandler->getNotApplicableStatus($child));
                            $child->save();
                        } elseif ($childHandler->isStatusReadOnly($child)) {
                            $isChildReadOnly = true;
                        }
                    }
                }
                if (!$isChildReadOnly && !$response['activity_change_not_allowed']) {
                    $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
                    $activity->save();
                }
            } else {
                $activityHandler->setStatus($activity, $parentActivityOldStatus);
            }
        } elseif (!$activityHandler->isStatusReadOnly($activity)) {
            $activityOldStatus = $activity->status;
            $activityHandler->setStatus($activity, $activityHandler->getNotApplicableStatus($activity));
            $activity->processing_smart_guide = true;
            $activityHookHelper->validateAllowedBy($activity, $activityHandler);
            if (ActivityHooksHelper::getAllowedByError()) {
                ActivityHooksHelper::resetAllowedByError();
                $activityHandler->setStatus($activity, $activityOldStatus);
                $response['activity_change_not_allowed'] = true;
            } else {
                $activity->save();
            }
        } elseif ($activityHandler->isStatusReadOnly($activity)) {
            $response['self_read_only_activity'] = true;
        }
        foreach ($blocked as $activity) {
            $this->resolveBlock($activity, $blocked);
        }
        if ($isChildReadOnly) {
            $response['is_child_read_only'] = true;
        }
        return $response;
    }
}
