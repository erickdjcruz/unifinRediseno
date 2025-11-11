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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\StatusHelper;

class ParentToSmartGuideRSA
{
    private static array $activityModules = [
        'Tasks',
        'Meetings',
        'Calls',
    ];
    private static array $targetModuleMapping = [
        'Smart Guide Activities' => 'dri_workflow_task_templates',
        'Smart Guide Stage' => 'dri_subworkflows',
        'Smart Guide' => 'dri_workflows',
    ];

    private static array $targetActionMapping = [
        'completed' => 'Completed',
        'in_progress' => 'In Progress',
        'not_applicable' => 'Not Applicable',
        'deleted' => 'Deleted',
        'Completed' => 'Completed',
    ];

    private static array $activityFields = [
        'id',
        'dri_subworkflow_id',
        'dri_workflow_id',
        'cj_actual_sort_order',
        'customer_journey_blocked_by',
        'cj_blocked_by_stages',
        'is_cj_parent_activity',
        'dri_workflow_template_id',
        'status',
        'cj_parent_activity_type',
        'cj_parent_activity_id',
    ];

    private static $parentModuleName;
    private static $parentRecordID;
    private static \SugarBean $parentBean;
    private static array $activityRecords = [];
    private static array $cancelledWorkflowRecords = [];
    private static array $completedWorkflowRecords = [];
    private static array $parentActivityRecords = [];
    private static int $currentDepth = 0;
    private static int $maxDepth = 10;

    /**
     * Set the parent related information
     *
     * @param SugarBean $parentBean
     */
    public static function setParentData($parentBean)
    {
        self::$parentModuleName = $parentBean->getModuleName();
        self::$parentRecordID = $parentBean->id;
        self::$parentBean = $parentBean;
    }

    /**
     * Automatically update related smart guides based on a parent action
     *
     * @param \SugarBean $parent
     * @return array
     */
    public static function checkAndPerformParentRSA(\SugarBean $parent)
    {
        self::$activityRecords = [];
        self::$currentDepth = 0;
        if (!empty($parent)) {
            if (!self::parentHasSmartGuides($parent)) {
                return self::$activityRecords;
            }
            self::setParentData($parent);
            $smartGuideTemplateRecords = self::getParentRecordSmartGuideTemplates($parent);
            if (!empty($smartGuideTemplateRecords)) {
                $rsaRecords = self::getParentRelatedRSA($parent, $smartGuideTemplateRecords);
                if (!empty($rsaRecords)) {
                    foreach ($rsaRecords as $rsaRecord) {
                        $filterCriteria = $rsaRecord['field_trigger'];
                        $filterCriteriaFieldData = self::getJsonDecodedData($filterCriteria);
                        $targetActionField = $rsaRecord['target_action'];
                        $targetActionFieldData = self::getJsonDecodedData($targetActionField);
                        $workflowTemplateID = $rsaRecord['smart_guide_template_id'];

                        if (!empty($targetActionFieldData) && !empty($filterCriteriaFieldData) &&
                            !empty($filterCriteriaFieldData['filterDef']) && self::filterCriteriaSatisfies($filterCriteriaFieldData['filterDef'])) {
                            $lastIndex = safeCount($targetActionFieldData) - 1;
                            if ($lastIndex >= 0 && $targetActionFieldData[$lastIndex]['id'] === 'Smart Guide') {
                                $targetActionValue = $targetActionFieldData[$lastIndex]['action_value'];
                                if (empty($targetActionValue)) {
                                    $targetActionValue = 'mark_all_completed';
                                }

                                $smartGuideAction = $targetActionFieldData[$lastIndex]['action_id'];
                                if (!empty($smartGuideAction)) {
                                    self::setActivityRecordsForSmartGuideOption($workflowTemplateID, $smartGuideAction, $targetActionValue);
                                }
                            } else {
                                foreach ($targetActionFieldData as $targetActionItem) {
                                    self::populateTargetArrays($targetActionItem, $workflowTemplateID);
                                }
                            }
                        }
                    }
                    if (!empty(self::$activityRecords)) {
                        self::sortActivityRecords(self::$activityRecords);
                        self::refineBlockedByActivityRecords();
                        self::setErrorForBlockedChildren();
                        self::removeAndSetParentActivities();
                        self::removeParentsOfBlockedByChildren();
                    }
                }
            }
        }

        $resultSet = array_merge(self::$cancelledWorkflowRecords, self::$activityRecords, self::$parentActivityRecords, self::$completedWorkflowRecords);
        return array_merge(array_values($resultSet), self::$cancelledWorkflowRecords);
    }

    /**
     * Checks if parent record has a smart guide or not
     *
     * @param SugarBean $parent
     * @return boolean true|false
     */
    private static function parentHasSmartGuides(\SugarBean $parent)
    {
        $smartGuideBean = \BeanFactory::newBean('DRI_Workflows');
        $parentIDFieldName = strtolower($parent->object_name) . '_id';
        $query = new \SugarQuery();
        $query->select(['id']);
        $query->from($smartGuideBean)->where()
            ->notNull($parentIDFieldName);

        return !empty($query->execute());
    }

    /**
     * Populate the target arrays based on the target items selected in RSA
     *
     * @param array $targetActionItem
     * @param string $workflowTemplateID
     */
    private static function populateTargetArrays(array $targetActionItem, string $workflowTemplateID)
    {
        $targetModule = self::$targetModuleMapping[$targetActionItem['id']];
        $parentModuleBean = \BeanFactory::getBean(self::$parentModuleName);
        $parentIDFieldName = strtolower($parentModuleBean->object_name) . '_id';
        $targetAction = $targetActionItem['action_id'];
        $parentActivity = [];
        $childActivityRecords = [];
        if ($targetModule === 'dri_workflow_task_templates') {
            self::handleRSAForTaskTemplates($workflowTemplateID, $targetActionItem, $targetAction, $childActivityRecords, $parentActivity);
        } elseif ($targetModule === 'dri_subworkflows') {
            $targetActionValue = $targetActionItem['action_value'];
            $targetValue = $targetActionItem['value'];
            if ($targetAction === 'in_progress') {
                self::initMarkingParentsAsInProgress($childActivityRecords, $parentActivity, $workflowTemplateID, $targetActionValue, $targetAction);
            } elseif ($targetAction === 'completed') {
                if ($targetActionValue === 'mark_all_completed') {
                    $status = 'completed';
                } else {
                    $status = 'not_applicable';
                }
                $subworkflowIDs = self::fetchAllSubWorflowIDs($targetValue, $workflowTemplateID);
                if (!empty($subworkflowIDs)) {
                    $subworkflowIDs = array_column($subworkflowIDs, 'id');
                    $moduleActivities = self::getActivitiesWithSubWorkflowIDs($subworkflowIDs, $parentIDFieldName);
                    foreach ($moduleActivities as $activityModule => $activities) {
                        $moduleBean = \BeanFactory::newBean($activityModule);
                        foreach ($activities as $activity) {
                            self::addActivityRecord($activity, $status, $activityModule, $moduleBean);
                        }
                    }
                }
            }
        }
    }

    /**
     * Initializes Mark Parent activity as In Progress
     *
     * @param array $childActivityRecords
     * @param array $parentActivity
     * @param string $workflowTemplateID
     * @param $targetActionValue
     * @param string $targetAction
     */
    private static function initMarkingParentsAsInProgress(array &$childActivityRecords, array &$parentActivity, string $workflowTemplateID, $targetActionValue, string $targetAction)
    {
        $moduleActivities = self::getActivitiesWithTaskTemplateIDs($workflowTemplateID, $targetActionValue, '');
        foreach ($moduleActivities as $activityModule => $activities) {
            $moduleBean = \BeanFactory::newBean($activityModule);
            foreach ($activities as $activity) {
                if (!array_key_exists($activity['id'], self::$activityRecords)
                && isTruthy($activity['is_cj_parent_activity'])) {
                    $childActivityRecords = [];
                    $childActivities = self::getChildActivitiesOfParent($activity['id']);
                    foreach ($childActivities as $childActivityModule => $moduleChildActivity) {
                        foreach ($moduleChildActivity as $childActivity) {
                            $parentActivity = $activity;
                            $parentActivity['module'] = $activityModule;
                            array_push($childActivityRecords, self::createActivityRecord($childActivity, $targetAction, $childActivityModule, $moduleBean));
                        }
                    }
                }
                self::addActivityRecord($activity, $targetAction, $activityModule, $moduleBean);
                if (!empty($childActivityRecords) && safeCount($childActivityRecords) > 0 && !empty($parentActivity)) {
                    self::markParentAsInProgress($childActivityRecords, $parentActivity);
                }
            }
        }
    }

    /**
     * Mark Parent activity as In Progress
     *
     * @param array $childActivityRecords
     * @param array $parentActivity
     */
    private static function markParentAsInProgress(array &$childActivityRecords, array &$parentActivity)
    {
        self::sortActivityRecords($childActivityRecords);
        $childActivityRecords = array_values($childActivityRecords);
        $firstelement = array_shift($childActivityRecords);
        $activitymodule = $firstelement['module'];
        $activityid = $firstelement['id'];
        if (isset($firstelement) && isset($activitymodule) && isset($activityid)) {
            if (in_array($parentActivity['module'], ['Tasks', 'Calls']) &&
                in_array($activitymodule, ['Tasks', 'Calls'])
            ) {
                $parentActivityBean = \BeanFactory::getBean($parentActivity['module'], $parentActivity['id']);
                $childActivityBean = \BeanFactory::getBean($activitymodule, $activityid);
                if (!empty($parentActivityBean) && !empty($childActivityBean)) {
                    self::addActivityRecord($firstelement, 'in_progress', $childActivityBean->module_dir, $childActivityBean);
                }
            } else {
                unset(self::$activityRecords[$parentActivity['id']]);
            }
        }
        $parentActivity = [];
        $childActivityRecords = [];
    }

    /**
     * Handles the RSA for Task Templates
     *
     * @param array $childActivityRecords
     * @param array $parentActivity
     */
    private static function handleRSAForTaskTemplates(string $workflowTemplateID, $targetActionItem, string $targetAction, array &$childActivityRecords, array &$parentActivity)
    {
        $moduleActivities = self::getActivitiesWithTaskTemplateIDs($workflowTemplateID, $targetActionItem['value'], '');
        foreach ($moduleActivities as $activityModule => $activities) {
            $moduleBean = \BeanFactory::newBean($activityModule);
            foreach ($activities as $activity) {
                if (!array_key_exists($activity['id'], self::$activityRecords) && isTruthy($activity['is_cj_parent_activity'])) {
                    $childActivities = self::getChildActivitiesOfParent($activity['id']);
                    foreach ($childActivities as $childActivityModule => $moduleChildActivity) {
                        foreach ($moduleChildActivity as $childActivity) {
                            if ($targetAction === 'in_progress') {
                                $parentActivity = $activity;
                                $parentActivity['module'] = $activityModule;
                                array_push($childActivityRecords, self::createActivityRecord($childActivity, $targetAction, $childActivityModule, $moduleBean));
                            } else {
                                self::addActivityRecord($childActivity, $targetAction, $childActivityModule, $moduleBean);
                            }
                        }
                    }
                }
                self::addActivityRecord($activity, $targetAction, $activityModule, $moduleBean);
                if (!empty($childActivityRecords) && safeCount($childActivityRecords) > 0 && !empty($parentActivity)) {
                    self::markParentAsInProgress($childActivityRecords, $parentActivity);
                }
            }
        }
    }

    /**
     * Adds the activity record to the activityRecords array
     *
     * @param array $activity
     * @param string $targetAction
     * @param string $activityModule
     * @param \SugarBean $moduleBean
     * @param bool $updateJourney
     */
    private static function addActivityRecord(array $activity, string $targetAction, string $activityModule, \SugarBean $moduleBean, bool $updateJourney = false)
    {
        self::$activityRecords[$activity['id']] = self::createActivityRecord($activity, $targetAction, $activityModule, $moduleBean, $updateJourney);
    }

    /**
     * Creates the activity record and returns an array
     *
     * @param array $activity
     * @param string $targetAction
     * @param string $activityModule
     * @param \SugarBean $moduleBean
     * @param bool $updateJourney
     * @return array
     */
    private static function createActivityRecord(array $activity, string $targetAction, string $activityModule, \SugarBean $moduleBean, bool $updateJourney = false)
    {
        return [
            'id' => $activity['id'],
            'status' => self::getActivityStatus($activityModule, self::$targetActionMapping[$targetAction], $moduleBean),
            'module' => $activityModule,
            'order' => self::formatActivitySortOrder($activity['cj_actual_sort_order']),
            'dri_subworkflow_id' => $activity['dri_subworkflow_id'],
            'dri_workflow_id' => $activity['dri_workflow_id'],
            'dri_workflow_template_id' => $activity['dri_workflow_template_id'],
            'customer_journey_blocked_by' => $activity['customer_journey_blocked_by'],
            'cj_blocked_by_stages' => $activity['cj_blocked_by_stages'],
            'is_cj_parent_activity' => $activity['is_cj_parent_activity'],
            'cj_parent_activity_id' => $activity['cj_parent_activity_id'],
            'cj_parent_activity_type' => $activity['cj_parent_activity_type'],
            'update_journey' => $updateJourney,
            'cj_actual_sort_order' => $activity['cj_actual_sort_order'],
        ];
    }

    /**
     * Gets the activities which have the target task template id
     *
     * @param string|null $workflowTemplateID
     * @param $taskTemplateIDs
     * @return array $moduleActivities
     */
    private static function getActivitiesWithTaskTemplateIDs(?string $workflowTemplateID, $taskTemplateIDs, string $workflowID)
    {
        $moduleActivities = [];
        if (!empty($workflowTemplateID)) {
            if (!is_array($taskTemplateIDs)) {
                $taskTemplateIDs = [$taskTemplateIDs];
            }

            $stageTemplateIDs = self::getStageTemplatesFromTaskTemplateIDs($taskTemplateIDs);
            $workflowStageIDs = [];
            if (is_array($stageTemplateIDs)) {
                foreach ($stageTemplateIDs as $stageTemplateID) {
                    $stageIDs = self::fetchAllSubWorflowIDs($stageTemplateID['dri_subworkflow_template_id'], $workflowTemplateID, $workflowID);
                    if (!empty($stageIDs)) {
                        $stageIDs = array_column($stageIDs, 'id');
                        $workflowStageIDs = array_merge($workflowStageIDs, $stageIDs);
                    }
                }
            }
        
            if (empty($workflowStageIDs)) {
                return [];
            }


            foreach (self::$activityModules as $activityModule) {
                $activityModuleBean = \BeanFactory::newBean($activityModule);
                $query = new \SugarQuery();
                $query->select(self::$activityFields);
                $query->from($activityModuleBean)->where()
                    ->in('dri_subworkflow_id', $workflowStageIDs)
                    ->in('dri_workflow_task_template_id', $taskTemplateIDs)
                    ->notEquals('status', self::getActivityStatus($activityModule, 'Completed', $activityModuleBean))
                    ->notEquals('status', self::getActivityStatus($activityModule, 'Not Applicable', $activityModuleBean));
                $moduleActivities[$activityModule] = $query->execute();
            }
        }
        return $moduleActivities;
    }

    /**
     * Gets the stage template ids from task template ids
     *
     * @param array $taskTemplateIDs
     * @return array $stageTemplateIDs
     */
    private static function getStageTemplatesFromTaskTemplateIDs(array $taskTemplateIDs): array
    {
        $taskTemplateBean = \BeanFactory::newBean('DRI_Workflow_Task_Templates');
        $query = new \SugarQuery();
        $query->select(['dri_subworkflow_template_id']);
        $query->from($taskTemplateBean)->where()
            ->in('id', $taskTemplateIDs);
        return $query->execute();
    }

    /**
     * Gets the activities which have the target subworkflow id
     *
     * @param array $subworkflowIDs
     * @return array $moduleActivities
     */
    private static function getActivitiesWithSubWorkflowIDs(array $subworkflowIDs)
    {
        $moduleActivities = [];
        foreach (self::$activityModules as $activityModule) {
            $activityModuleBean = \BeanFactory::newBean($activityModule);
            $query = new \SugarQuery();
            $query->select(self::$activityFields);
            $query->from($activityModuleBean)->where()
                ->notEquals('status', self::getActivityStatus($activityModule, 'Completed', $activityModuleBean))
                ->notEquals('status', self::getActivityStatus($activityModule, 'Not Applicable', $activityModuleBean))
                ->in('dri_subworkflow_id', $subworkflowIDs);
            $moduleActivities[$activityModule] = $query->execute();
        }
        return $moduleActivities;
    }

    /**
     * Fetch the subworkflow IDs for Subworkflow Template
     *
     * @param string $subWorkflowTemplateID
     * @return array
     */
    private static function fetchAllSubWorflowIDs(string $subWorkflowTemplateID, ?string $workflowTemplateID, string $workflowID = '')
    {
        if (!empty($workflowTemplateID)) {
            $activeSmartGuides=[];

            if ($workflowID !== '') {
                $activeSmartGuides[0] = $workflowID;
            } else {
                $activeSmartGuides = array_column(self::fetchAllActiveSmartGuides($workflowTemplateID), 'id');
            }

            if (empty($activeSmartGuides)) {
                return [];
            }


            $subWorkflowBean = \BeanFactory::newBean('DRI_SubWorkflows');
            $query = new \SugarQuery();
            $query->select(['id']);
            $query->from($subWorkflowBean);
            $query->where()->equals('dri_subworkflow_template_id', $subWorkflowTemplateID)
                ->in('dri_workflow_id', $activeSmartGuides);
            return $query->execute();
        }
        return [];
    }

    /**
     * Fetch the child activities using the parent activity id
     *
     * @param string $parentActivityID
     * @return array $activities
     */
    private static function getChildActivitiesOfParent(string $parentActivityID)
    {
        $activities = [];
        foreach (self::$activityModules as $activityModule) {
            $activityModuleBean = \BeanFactory::newBean($activityModule);
            $query = new \SugarQuery();
            $query->select(self::$activityFields);
            $query->from($activityModuleBean)->where()
                ->equals('parent_id', self::$parentRecordID)
                ->notEquals('status', self::getActivityStatus($activityModule, 'Completed', $activityModuleBean))
                ->notEquals('status', self::getActivityStatus($activityModule, 'Not Applicable', $activityModuleBean))
                ->equals('cj_parent_activity_id', $parentActivityID);
            $results = $query->execute();
            $activities[$activityModule] = $results;
        }
        return $activities;
    }

    /**
     * Sort the activity records based on sorting order
     *
     */
    private static function sortActivityRecords(&$activities)
    {
        uasort($activities, fn ($recordA, $recordB) => $recordA['order'] <=> $recordB['order']);
    }

    /**
     * Check if the parent record satisfies the criteria for triggering an RSA
     *
     * @param array $filterCriteria
     * @return bool true|false
     */
    public static function filterCriteriaSatisfies(array $filterCriteria)
    {
        global $current_user;
        if (!empty($filterCriteria)) {
            $filterCriteria[] = [
                'id' => [
                    '$equals' => self::$parentBean->id,
                ],
            ];
        }

        $api = new \RestService();
        $api->user = $current_user;
        $filterApi = new \FilterApi();
        $api->action = 'list';
        $result = $filterApi->filterList($api, ['module' => self::$parentBean->getModuleName(), 'filter' => $filterCriteria]);
        return !empty($result['records']);
    }

    /**
     * Get updated activity status according to the activity's module
     *
     * @param string $module
     * @param $status
     * @param \SugarBean $bean
     * @return string
     */
    private static function getActivityStatus(string $module, $status, \SugarBean $bean)
    {
        $statusHelper = new StatusHelper();
        switch ($status) {
            case $statusHelper::TASK_STATUS_COMPLETED:
                return $statusHelper->getCompletedStatus($bean, $module);
            case $statusHelper::APPOINMENT_STATUS_DEFERRED:
                return $statusHelper->getNotApplicableStatus($bean, $module);
            case $statusHelper::APPOINMENT_STATUS_DEFERRED:
                return $statusHelper->getCancelledStatus($bean, $module);
            case $statusHelper::TASK_STATUS_IN_PROGRESS:
                return $statusHelper->getInProgressStatus($bean, $module);
            default:
                return $status;
        }
    }

    /**
     * Set all open and in progress smart guide activities for that workflow template id
     *
     * @param string $workflowTemplateID
     * @param string $smartGuideAction
     * @param string $targetActionValue
     * @return null
     */
    private static function setActivityRecordsForSmartGuideOption(string $workflowTemplateID, string $smartGuideAction, string $targetActionValue)
    {
        $updateJourney = true;
        $isCancellingSmartGuide = false;

        $activeSmartGuides = array_column(self::fetchAllActiveSmartGuides($workflowTemplateID), 'id');

        if (empty($activeSmartGuides)) {
            return [];
        }

        if ($smartGuideAction === 'completed') {
            if ($targetActionValue === 'mark_all_completed') {
                $status = 'completed';
            } else {
                $status = 'not_applicable';
            }
        } else {
            $workflowTemplateQuery = new \SugarQuery();
            $workflowTemplateBean = \BeanFactory::getBean('DRI_Workflow_Templates');
            $workflowTemplateQuery->select(['cancel_action']);
            $workflowTemplateQuery->from($workflowTemplateBean)->where()
                ->equals('id', $workflowTemplateID);

            $cancelAction = $workflowTemplateQuery->getOne();
            if ($cancelAction === 'set_not_applicable') {
                $status = 'not_applicable';
            } else {
                $status = 'deleted';
            }
            $isCancellingSmartGuide = true;
        }
        $module = 'DRI_Workflows';
        $workflowStatus = $isCancellingSmartGuide ? 'Cancelled' : 'Completed';

        foreach (self::$activityModules as $activityModule) {
            $activityModuleBean = \BeanFactory::newBean($activityModule);
            $query = new \SugarQuery();
            $query->select(self::$activityFields);
            $query->from($activityModuleBean)->where()
                ->in('dri_workflow_id', $activeSmartGuides)
                ->notEquals('status', self::getActivityStatus($activityModule, 'Completed', $activityModuleBean))
                ->notEquals('status', self::getActivityStatus($activityModule, 'Not Applicable', $activityModuleBean));

            $moduleActivities = $query->execute();

            foreach ($moduleActivities as $activity) {
                self::addActivityRecord($activity, $status, $activityModule, $activityModuleBean, $updateJourney);

                $workflowRecord = [
                    'id' => $activity['dri_workflow_id'],
                    'module' => $module,
                    'status' => $workflowStatus,
                ];

                if ($isCancellingSmartGuide && !array_key_exists($activity['dri_workflow_id'], self::$cancelledWorkflowRecords)) {
                    self::$cancelledWorkflowRecords[$activity['dri_workflow_id']] = $workflowRecord;
                } else {
                    self::$completedWorkflowRecords[$activity['dri_workflow_id']] = $workflowRecord;
                }
            }
        }
    }

    /**
     * Format Activity Sort Order
     *
     * @param string $sortOrder
     * @return string
     */
    private static function formatActivitySortOrder(string $sortOrder)
    {
        $sortOrderArr = explode('.', $sortOrder);
        foreach ($sortOrderArr as $index => $sortNumber) {
            if (strlen($sortNumber) === 1 && $sortNumber !== '0') {
                $sortOrderArr[$index] = '0' . $sortNumber;
            }
        }
        return implode('.', $sortOrderArr);
    }

    /**
     * It will fetch RSAs for parent beans
     *
     * @param \SugarBean parentBean
     * @param array smartGuideTemplateRecords
     * @return array $rsaRecords
     */
    private static function getParentRelatedRSA(\SugarBean $parentBean, array $smartGuideTemplateRecords = [])
    {
        $rsaBean = \BeanFactory::newBean('CJ_Forms');
        $query = new \SugarQuery();
        $query->select(['id', 'smart_guide_template_id', 'field_trigger', 'target_action']);
        $query->from($rsaBean)->where()
            ->equals('active', true)
            ->equals('module_trigger', $parentBean->getModuleName())
            ->equals('main_trigger_type', 'sugar_action_to_smart_guide')
            ->notNull('parent_id');
        $query->orderBy('date_modified', 'DESC');

        if (!empty($smartGuideTemplateRecords)) {
            $query->where()->in('smart_guide_template_id', $smartGuideTemplateRecords);
        }
        return $query->execute();
    }

    /**
     * Refine the blocked by activity records and remove the activities
     * which can't be marked as blocked by
     *
     * @return null
     */
    private static function refineBlockedByActivityRecords()
    {
        foreach (self::$activityRecords as $activityID => $activity) {
            if ($activity['update_journey'] !== true) {
                if (!self::areBlockerActivitiesPartOfRSA($activity)) {
                    self::$activityRecords[$activityID]['error'] = 'Some activity is blocking this activity';
                }
                self::$currentDepth = 0;
            }
        }
    }

    /**
     * Remove the parent activities because we are not going to perform any action on Parent
     *
     * @return null
     */
    private static function removeAndSetParentActivities()
    {
        foreach (self::$activityRecords as $activityID => $activity) {
            if (!empty($activity['is_cj_parent_activity']) &&
            isTruthy($activity['is_cj_parent_activity']) && $activity['status'] !== 'Deleted') {
                self::$parentActivityRecords[$activityID] = $activity;
                unset(self::$activityRecords[$activityID]);
            }
        }
    }

    /**
     * Remove the parent activities because their children are blocked by
     *
     * @return null
     */
    private static function removeParentsOfBlockedByChildren()
    {
        foreach (self::$activityRecords as $activityID => $activity) {
            if ((empty($activity['is_cj_parent_activity']) ||
                (isset($activity['is_cj_parent_activity']) && isFalsy($activity['is_cj_parent_activity'])))
                && $activity['status'] !== 'Deleted' && isset($activity['error'])) {
                    unset(self::$parentActivityRecords[$activity['cj_parent_activity_id']]);
            }
        }
    }

    /**
     * Sets error for the blocked children
     *
     * @return null
     */
    private static function setErrorForBlockedChildren()
    {
        foreach (self::$activityRecords as $activityID => $activity) {
            if (empty($activity['is_cj_parent_activity']) && $activity['status'] !== 'Deleted') {
                if (!empty($activity['cj_parent_activity_type']) && !empty($activity['cj_parent_activity_id'])) {
                    if (array_key_exists($activity['cj_parent_activity_id'], self::$activityRecords) && !empty(self::$activityRecords[$activity['cj_parent_activity_id']]['error'])) {
                        self::$activityRecords[$activityID]['error'] = self::$activityRecords[$activity['cj_parent_activity_id']]['error'];
                    }
                }
            }
        }
    }

    /**
     * Does the blocker activities exist in activityRecords list
     *
     * @param array $activity
     * @return boolean true|false
     */
    private static function areBlockerActivitiesPartOfRSA(array $activity)
    {
        if (self::$currentDepth < self::$maxDepth) {
            self::$currentDepth++;
            [$blockingActivityTemplates, $blockingStageTemplates] = [$activity['customer_journey_blocked_by'], $activity['cj_blocked_by_stages']];
            $blockingActivityTemplates = self::getJsonDecodedData($blockingActivityTemplates);
            $blockingStageTemplates = self::getJsonDecodedData($blockingStageTemplates);
            $blockerStageIDs = [];
            $blockerActivities = [];
            [$blockerActivities, $blockerStageIDs] = self::getBlockingActivitiesAndStages(
                $blockingActivityTemplates,
                $blockingStageTemplates,
                $activity['dri_workflow_id']
            );
            $hasBlockerActivities = !empty($blockerActivities);
            $hasBlockerStages = !empty($blockerStageIDs);
            if (!$hasBlockerActivities && !$hasBlockerStages) {
                return true;
            } elseif (self::hasNoBlockerActivity($blockerActivities) && self::hasNoBlockerStage($blockerStageIDs)) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * These activities are getting completed for sure
     *
     * @param array $blockerActivities
     * @return boolean true|false
     */
    private static function hasNoBlockerActivity(array $blockerActivities = [])
    {
        if (!empty($blockerActivities)) {
            $statusHelper = new StatusHelper();
            foreach ($blockerActivities as $blockerActivity) {
                if ($blockerActivity['status'] !== $statusHelper::TASK_STATUS_COMPLETED &&
                    $blockerActivity['status'] !== $statusHelper::TASK_STATUS_NOT_APPLICABLE &&
                    $blockerActivity['status'] !== $statusHelper::APPOINMENT_STATUS_HELD &&
                    $blockerActivity['status'] !== $statusHelper::APPOINMENT_STATUS_NOT_HELD) {
                    //if activity exists in the activities list but it is blocked by another activity/stage then continue further
                    if (array_key_exists($blockerActivity['id'], self::$activityRecords) && (self::$activityRecords[$blockerActivity['id']]['status'] === $statusHelper::TASK_STATUS_COMPLETED ||
                            self::$activityRecords[$blockerActivity['id']]['status'] === $statusHelper::TASK_STATUS_NOT_APPLICABLE ||
                            self::$activityRecords[$blockerActivity['id']]['status'] === $statusHelper::APPOINMENT_STATUS_HELD ||
                            self::$activityRecords[$blockerActivity['id']]['status'] === $statusHelper::APPOINMENT_STATUS_NOT_HELD)) {
                        //returns false if the blocking activity is part of RSA but has another blocking activity which cannot be marked completed/not applicable
                        if (!self::areBlockerActivitiesPartOfRSA($blockerActivity)) {
                            return false;
                        } else {
                            self::$currentDepth--;
                        }
                    } else {
                        //If not in final activities list then return false
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * These stages are getting completed for sure
     *
     * @param array $blockerStageIDs
     * @return boolean true|false
     */
    private static function hasNoBlockerStage($blockerStageIDs = [])
    {
        if (!empty($blockerStageIDs)) {
            foreach ($blockerStageIDs as $blockerStage) {
                $allActivities = self::fetchAllSubWorkflowActivities($blockerStage);
                $statusHelper = new StatusHelper();
                foreach ($allActivities as $actvity) {
                    if ($actvity['status'] !== $statusHelper::TASK_STATUS_COMPLETED &&
                        $actvity['status'] !== $statusHelper::TASK_STATUS_NOT_APPLICABLE &&
                        $actvity['status'] !== $statusHelper::APPOINMENT_STATUS_HELD &&
                        $actvity['status'] !== $statusHelper::APPOINMENT_STATUS_NOT_HELD) {
                        //if activity exists in the activities list but it is blocked by another activity/stage then continue further
                        if (array_key_exists($actvity['id'], self::$activityRecords) && (self::$activityRecords[$actvity['id']]['status'] === $statusHelper::TASK_STATUS_COMPLETED ||
                            self::$activityRecords[$actvity['id']]['status'] === $statusHelper::TASK_STATUS_NOT_APPLICABLE ||
                            self::$activityRecords[$actvity['id']]['status'] === $statusHelper::APPOINMENT_STATUS_HELD ||
                            self::$activityRecords[$actvity['id']]['status'] === $statusHelper::APPOINMENT_STATUS_NOT_HELD)) {
                            //returns false if the blocking activity is part of RSA but has another blocking activity which cannot be marked completed/not applicable
                            if (!self::areBlockerActivitiesPartOfRSA($actvity)) {
                                return false;
                            } else {
                                self::$currentDepth--;
                            }
                        } else {
                            //If not in final activities list then return false
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Get the blocking activities and stages id
     *
     * @param string $activityTemplates
     * @param string $stageTemplates
     * @param string $workflowID
     * @return array
     */
    private static function getBlockingActivitiesAndStages($activityTemplates, $stageTemplates, $workflowID)
    {
        $blockingActivities = [];
        $blockingActivitiesArr = [];
        $workflowTemplateID = self::getWorkflowTemplateId($workflowID);
        if ($workflowID == null) {
            $workflowID = '';
        }
        $blockingActivitiesArr = self::getActivitiesWithTaskTemplateIDs($workflowTemplateID, $activityTemplates, $workflowID);
        foreach ($blockingActivitiesArr as $moduleActivities) {
            foreach ($moduleActivities as $blockingActvity) {
                $blockingActivities[$blockingActvity['id']] = $blockingActvity;
            }
        }
        $blockingStageIDs = [];
        if (is_array($stageTemplates)) {
            foreach ($stageTemplates as $blockingStageTemplate) {
                $stageIDs = self::fetchAllSubWorflowIDs($blockingStageTemplate, $workflowTemplateID, $workflowID);
                if (!empty($stageIDs)) {
                    $stageIDs = array_column($stageIDs, 'id');
                    $blockingStageIDs = array_merge($blockingStageIDs, $stageIDs);
                }
            }
        }
        return [$blockingActivities, $blockingStageIDs];
    }

    /**
     * Get the journey template Id given journey ID
     * @param string $workflowId Id of the Journey
     * @return string Id of the Journey Template
     */
    private static function getWorkflowTemplateId(?string $workflowID): string
    {
        $query = new \SugarQuery();
        $query->from(\BeanFactory::newBean('DRI_Workflows'));
        $query->select(['dri_workflow_template_id']);

        $query->where()->equals('id', $workflowID);
        return $query->getOne();
    }

    /**
     * It will fetch unique smart guide templates added for this parent record
     *
     * @param \SugarBean $parentBean
     * @return array
     */
    private static function getParentRecordSmartGuideTemplates(\SugarBean $parentBean)
    {
        $workflowBean = \BeanFactory::newBean('DRI_Workflows');
        $parentIDFieldName = strtolower($parentBean->object_name) . '_id';
        $query = new \SugarQuery();
        $query->select(['dri_workflow_template_id']);
        $query->from($workflowBean)->where()
            ->equals($parentIDFieldName, $parentBean->id);

        $smartGuideTemplateRecords = $query->execute();

        $templateIDs = array_column($smartGuideTemplateRecords, 'dri_workflow_template_id');

        return array_unique($templateIDs);
    }

    /**
     * It will fetch all the activities in the subworkflow
     *
     * @param string $subWorkflowID
     * @return array $activities
     */
    private static function fetchAllSubWorkflowActivities(string $subWorkflowID)
    {
        $activities = [];
        foreach (self::$activityModules as $activityModule) {
            $activityModuleBean = \BeanFactory::newBean($activityModule);
            $query = new \SugarQuery();
            $query->select(self::$activityFields);
            $query->from($activityModuleBean)->where()
                ->equals('dri_subworkflow_id', $subWorkflowID);
            $moduleActivities = $query->execute();
            if (!empty($moduleActivities)) {
                $activities = array_merge($activities, $moduleActivities);
            }
        }
        return $activities;
    }

    /**
     * Decode the json data into an array
     * @param string $stringData
     * @return array
     */
    private static function getJsonDecodedData($stringData)
    {
        if (empty($stringData)) {
            return [];
        }

        $data = json_decode($stringData, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        return [];
    }

    /**
     * Returns true if there is an RSA for the record and its criteria satisfies
     * @param string $stringData
     * @return boolean true|false
     */
    public static function hasParentRSAsAndCriteriaSatisfies(\SugarBean $parent)
    {
        self::setParentData($parent);
        $rsaRecords = self::getParentRelatedRSA($parent);
        if (!empty($rsaRecords)) {
            foreach ($rsaRecords as $rsaRecord) {
                $filterCriteria = $rsaRecord['field_trigger'];
                $filterCriteriaFieldData = self::getJsonDecodedData($filterCriteria);
                $filterCriterias[] = self::filterCriteriaSatisfies($filterCriteriaFieldData['filterDef']);
                return true;
            }
        }
        return false;
    }

    /**
     * Create activity records for RSA execution and schedule them
     *
     * @param string $parentID
     * @param string $parentModule
     * @return null
     */
    public static function createActivityRecordsAndJobQueue(string $parentID, string $parentModule)
    {
        global $timedate;
        if (!empty($parentID)&&!empty($parentModule)) {
            $data = [
                'parent_id' => $parentID,
                'parent_module' => $parentModule,
            ];
            $job = self::getSchedulersJobs();
            $job->name = 'Perform RSA On Smart Guides: ' . $timedate->getNow();
            $job->target = 'class::SugarJobPerformRSAOnSmartGuides';
            $job->data = json_encode($data);
            $job->retry_count = 0;
            $jobQueue = new \SugarJobQueue();
            $jobQueue->submitJob($job);
        }
    }

    /**
     * Gets a new instance of the SchedulersJobs bean
     *
     * @return null|SugarBean
     */
    private static function getSchedulersJobs()
    {
        return \BeanFactory::newBean('SchedulersJobs');
    }

    /**
     * Fetch all in progress smart guides for that workflow template id
     *
     * @param string $workflowTemplateID
     * @return array $smartGuideRecords
     */
    public static function fetchAllActiveSmartGuides(string $workflowTemplateID)
    {
        $parentModuleBean = \BeanFactory::getBean(self::$parentModuleName);
        $parentIDFieldName = strtolower($parentModuleBean->object_name) . '_id';
        $workflowBean = \BeanFactory::getBean('DRI_Workflows', $workflowTemplateID);
        $query = new \SugarQuery();

        $query->select(['id']);
        $query->from($workflowBean)->where()
            ->equals('dri_workflow_template_id', $workflowTemplateID)
            ->equals($parentIDFieldName, self::$parentRecordID)
            ->equals('state', 'in_progress')
            ->equals('archived', false);

        return $query->execute();
    }
}
