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

use Sugarcrm\Sugarcrm\ProcessManager;

/**
 * Description of PMSEUserAssignmentHandler
 *
 */
class PMSEUserAssignmentHandler
{
    /**
     *
     * @var PMSEWrapper
     */
    private $wrapper;

    /**
     * PMSEEmailHandler object
     * @var PMSEEmailHandler
     */
    private $emailHandler;

    /**
     *
     * @var PMSELogger
     */
    protected $logger;

    /**
     *
     * Class constructor
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->wrapper = ProcessManager\Factory::getPMSEObject('PMSEWrapper');
        $this->emailHandler = ProcessManager\Factory::getPMSEObject('PMSEEmailHandler');
        $this->logger = PMSELogger::getInstance();
    }

    /**
     *
     * @return PMSEWrapper
     * @codeCoverageIgnore
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     *
     * @return PMSELogger
     * @codeCoverageIgnore
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param PMSELogger $logger
     * @codeCoverageIgnore
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @param PMSEWrapper $wrapper
     * @codeCoverageIgnore
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }


    /**
     *
     * @param type $module
     * @param type $beanId
     * @return type
     * @codeCoverageIgnore
     */
    public function retrieveBean($module, $beanId = null)
    {
        return BeanFactory::getBean($module, $beanId);
    }

    /**
     *
     * @param type $flowData
     * @return type
     */
    public function taskAssignment($flowData)
    {
        $activityBean = $this->retrieveBean('pmse_BpmnActivity'); //new BpmnActivity();
        $activityDefinitionBean = $this->retrieveBean('pmse_BpmActivityDefinition'); //new BpmActivityDefinition();
        $actId = $flowData['bpmn_id'];
        $activities = $activityBean->get_list('pmse_bpmn_activity.id', "pmse_bpmn_activity.id = '$actId'");
        $activityRow = get_object_vars($activities['list'][0]);
        $currentUserId = $flowData['cas_user_id'];
        $currentSugarId = $flowData['cas_sugar_object_id'];
        $currentSugarModule = $flowData['cas_sugar_module'];
        $today = TimeDate::getInstance()->nowDb();

        $activitiesDef = $activityDefinitionBean->get_list(
            'pmse_bpm_activity_definition.id',
            "pmse_bpm_activity_definition.id = '$actId' ",
            0,
            -1,
            -1,
            []
        );
        if (!isset($activitiesDef['list'][0])) {
            //$this->bpmLog('ERROR', "[$flowData['cas_id']][$flowData['cas_index']] Activity Definition not found using act_id: $actId");
            $this->logger->error("[{$flowData['cas_id']}][{$flowData['cas_index']}] Activity Definition not found using act_id: $actId");
            $activityDefRow = [];
        } else {
            $activityDefRow = get_object_vars($activitiesDef['list'][0]);
        }
        $bpmnElement = array_merge($activityRow, $activityDefRow);
        //todo: throw an error if something was wrong
        //$expectedTimeObject = json_decode(base64_decode($activityDefRow['act_expected_time']));
        $caseData = new stdClass();
        $caseData->cas_start_date = '';
        $caseData->cas_delegate_date = $today;

        //$expectedTime = PMSEEngineUtils::processExpectedTime($expectedTimeObject, $caseData);
        //$dueDate = (!empty($expectedTime)) ? date('Y-m-d H:i:s', $expectedTime) : null;
        $activityType = $bpmnElement['act_task_type'];

        if ($activityType == 'SCRIPTTASK') {
            $cas_flow_status = 'SCRIPT';
            $cas_sugar_action = $activityType;
            //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] next flow is a script");
            $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] next flow is a script");
        } else {
            $cas_flow_status = 'FORM';
            $cas_sugar_action = $bpmnElement['act_type'];

            //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] next flow is an activity");
            $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] next flow is an activity");
            //check assignment rules
            $assignUser = (isset($bpmnElement['act_assign_user']) == true) ? $bpmnElement['act_assign_user'] : 'unknown';
            $assign_method = (isset($bpmnElement['act_assignment_method']) == true) ? strtolower($bpmnElement['act_assignment_method']) : 'unknown';
            $assign_team = (isset($bpmnElement['act_assign_team']) == true) ? $bpmnElement['act_assign_team'] : 'unknown';
            //$last_assigned = $bpmnElement['act_last_user_assigned'];

            if ($assign_method == 'static') {
                switch ($assignUser) {
                    case 'owner':
                        $currentUserId = $this->getRecordOwnerId($currentSugarId, $currentSugarModule);
                        break;
                    case 'supervisor':
                        $currentUserId = $this->getSupervisorId($currentUserId);
                        break;
                    case 'currentuser':
                        $currentUserId = $currentUserId; //$this->getCurrentUserId();
                        break;
                    default:
                        $currentUserId = $assignUser;
                        break;
                }
                //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] form assigned to user '$currentUserId'");
                $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] form assigned to user '$currentUserId'");
            } elseif ($assign_method == 'selfservice') {
                $currentUserId = $assign_team;
                //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] form assigned to team $currentUserId (Selfservice)");
                $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] form assigned to team $currentUserId (Selfservice)");
            } elseif ($assign_method == 'balanced') {
                $currentUserId = $this->getNextUserUsingRoundRobin($actId);
                //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] form assigned to user $currentUserId (Round Robin)");
                $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] form assigned to user $currentUserId (Round Robin)");
            } else {
                //$this->bpmLog('INFO', "[$flowData['cas_id']][$flowData['cas_index']] 'unknown' assigned to user $currentUserId");
                $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] 'unknown' assigned to user $currentUserId");
            }
            //parent::execute($flowData, $bean);
        }
        return $currentUserId;
    }

    /**
     * Realize the adhoc reassignment passing the caseData and the userID parameter and also
     * if the isRoundTripReassign option
     * @param type $caseData
     * @param type $userId
     * @param type $isRoundTripReassign
     * @return boolean
     */
    public function adhocReassign($caseData, $userId, $isRoundTripReassign = false, $isFormRequest = false)
    {
        $today = TimeDate::getInstance()->nowDb();
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseData['cas_user_id'] = $userId;

        $flowRow = $this->retrieveBean('pmse_BpmFlow');
        $flowRow->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);

        $selectFields = ['max(cas_index) as max_index'];
        $maxIndexFlow = $this->wrapper->getSelectRows(
            $caseBean,
            '',
            'cas_id=' . $caseData['cas_id'],
            0,
            -1,
            -1,
            $selectFields,
            []
        );

        $newFlowRow = $this->retrieveBean('pmse_BpmFlow');
        $newFlowRow->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);

        $newFlowRow->id = null;
        $newFlowRow->cas_index = $maxIndexFlow['rowList'][0]['max_index'] + 1;
        //$this->setCloseStatusInCaseFlow($caseData['cas_id'], $caseData['cas_index']);
        $newFlowRow->cas_previous = $caseData['cas_index'];
        $newFlowRow->cas_adhoc_type = $caseData['cas_adhoc_type'] ?? $flowRow->cas_adhoc_type;
        $newFlowRow->cas_task_start_date = !isset($flowRow->cas_task_start_date) ? $flowRow->cas_delegate_date : $flowRow->cas_task_start_date;
        $newFlowRow->cas_delegate_date = $today;
        if ($newFlowRow->cas_adhoc_type == 'ONE_WAY') {
            $newFlowRow->cas_adhoc_actions = $flowRow->cas_adhoc_actions;
        } else {
            if ($isFormRequest) {
                $newFlowRow->cas_adhoc_actions = json_encode(['link_cancel', 'route', 'edit']);
            } else {
                $newFlowRow->cas_adhoc_actions = $caseData['cas_adhoc_actions'];
            }
        }

        if ($newFlowRow->cas_adhoc_type != $flowRow->cas_adhoc_type) {
            $newFlowRow->cas_adhoc_parent_id = $flowRow->id;
        } else {
            $newFlowRow->cas_adhoc_parent_id = $flowRow->cas_adhoc_parent_id;
        }

        if ($isRoundTripReassign) {
            $newFlowRow->cas_reassign_level--;
        } else {
            $newFlowRow->cas_reassign_level++;
        }

        if ($newFlowRow->cas_reassign_level <= 0) {
            $newFlowRow->cas_adhoc_type = '';
        }

        $caseData['cas_index'] = $newFlowRow->cas_index;
        //$caseBean->new_with_id = true;
        $newFlowRow->save();
        //$caseBean->create($flowRow);


        return $this->reassignCaseToUser($caseData, $userId);
    }

    /**
     * Reassign case to the first user.
     * @param type $caseData
     * @param type $userId
     * @return boolean
     */
    public function originReassign($caseData, $userId)
    {
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseData['cas_user_id'] = $userId;

        $where = 'cas_id=' . $caseData['cas_id'] . ' AND cas_index=' . $caseData['cas_index'];

        $flowList = $caseBean->get_list('', $where);
        $flowRow = $flowList['list'][0];
        $selectFields = ['max(cas_index) as max_index'];
        $maxIndexFlow = $this->wrapper->getSelectRows(
            $caseBean,
            '',
            'cas_id=' . $caseData['cas_id'],
            0,
            -1,
            -1,
            $selectFields,
            []
        );

        $newFlowRow = $this->retrieveBean('pmse_BpmFlow');
        $newFlowRow->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $newFlowRow->id = null;
        $newFlowRow->cas_index = $maxIndexFlow['rowList'][0]['max_index'] + 1;
        $newFlowRow->cas_previous = $caseData['cas_index'];
        $newFlowRow->cas_adhoc_type = $caseData['cas_adhoc_type'];
        $newFlowRow->cas_adhoc_parent_id = $caseData['cas_adhoc_parent_id'];
        $newFlowRow->cas_adhoc_actions = $caseData['cas_adhoc_actions'];
        $newFlowRow->cas_task_start_date = isset($flowRow->cas_task_start_date) ? $flowRow->cas_delegate_date : $flowRow->cas_task_start_date;
        $newFlowRow->cas_reassign_level = $caseData['cas_reassign_level'];

        $caseData['cas_index'] = $newFlowRow->cas_index;
        $newFlowRow->save();
        return $this->reassignCaseToUser($caseData, $userId);
    }

    /**
     * Reassign a case to a determined user (alias for method reassignRecordToUser)
     * @param type $caseData
     * @param type $reassignToUser
     * @return type
     * @codeCoverageIgnore
     */
    public function reassignRecord($caseData, $reassignToUser = '')
    {
        return $this->reassignRecordToUser($caseData, $reassignToUser);
    }

    /**
     * Round trip reassign of a case.
     * @param type $caseData
     */
    public function roundTripReassign($caseData)
    {
        $db = DBManagerFactory::getInstance();
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $previousFlow = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $where = sprintf(
            'bpmn_id=%s AND cas_id=%d AND bpmn_type=%s AND bpmn_id=%s AND cas_reassign_level=%d AND cas_index=
                (SELECT max(cas_index) FROM pmse_bpm_flow WHERE cas_id=%d AND cas_thread=%d AND cas_reassign_level=%d)',
            $db->quoted($caseBean->bpmn_id),
            $caseData['cas_id'],
            $db->quoted($caseBean->bpmn_type),
            $db->quoted($caseBean->bpmn_id),
            ($caseBean->cas_reassign_level - 1),
            $caseData['cas_id'],
            $caseData['cas_thread'],
            ($caseBean->cas_reassign_level - 1)
        );
        $previousFlowRecord = $previousFlow->get_full_list('', $where);
        //$previousFlowRecord = $this->wrapper->getSelectRows($previousFlow, '', $where);
        $previousFlowRecord = $previousFlowRecord[0];
        $caseData['cas_adhoc_actions'] = $previousFlowRecord->cas_adhoc_actions;
        $this->adhocReassign($caseData, $previousFlowRecord->cas_user_id, true);
    }

    /**
     * Check if the task and step executed is of type Round Trip
     * @param type $caseData
     * @return boolean
     */
    public function isRoundTrip($caseData)
    {
        $result = false;
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        if ($caseBean->bpmn_type == 'bpmnActivity' && $caseBean->cas_adhoc_type == 'ROUND_TRIP') {
            $result = true;
        }
        return $result;
    }

    /**
     * This executes the reassignment of a case if that the reassignment should be in one way.
     * @param type $caseData
     */
    public function oneWayReassign($caseData)
    {
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $firstDerivatedFlow = $this->retrieveBean('pmse_BpmFlow', $caseBean->cas_adhoc_parent_id); //new BpmFlow();
        $originalFlow = $this->retrieveBean('pmse_BpmFlow', $firstDerivatedFlow->cas_adhoc_parent_id); //new BpmFlow();
        $caseData['cas_adhoc_type'] = $originalFlow->cas_adhoc_type;
        $caseData['cas_adhoc_parent_id'] = $originalFlow->cas_adhoc_parent_id;
        $caseData['cas_adhoc_actions'] = $originalFlow->cas_adhoc_actions;
        $caseData['cas_reassign_level'] = $originalFlow->cas_reassign_level;
        $this->originReassign($caseData, $originalFlow->cas_user_id);
    }

    /**
     * Check if the reassignment task is one way
     * @param type $caseData
     * @return boolean
     */
    public function isOneWay($caseData)
    {
        $result = false;
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        if ($caseBean->bpmn_type == 'bpmnActivity' && $caseBean->cas_adhoc_type == 'ONE_WAY') {
            $result = true;
        }
        return $result;
    }

    /**
     * Check if the reassignment task is one way
     * @param type $caseData
     * @return boolean
     */
    public function previousIsNormal($caseData)
    {
        $result = false;
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseBean->cas_id,
            'cas_index' => $caseBean->cas_previous,
        ]);
        if ($caseBean->bpmn_type == 'bpmnActivity' && $caseBean->cas_adhoc_type == '') {
            $result = true;
        }
        return $result;
    }

    /**
     * Reassign a case to a determined user.
     * @param type $caseData
     * @param type $userId
     * @return boolean
     */
    public function reassignCaseToUser($caseData, $userId)
    {
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $caseBean->cas_user_id = $userId;
        if ($caseBean->save()) {
            if ($caseBean->cas_flow_status === 'FORM' && $caseBean->bpmn_type === 'bpmnActivity') {
                $activityDefinitionBean = BeanFactory::getBean('pmse_BpmActivityDefinition', $caseBean->bpmn_id);
                if (trim($activityDefinitionBean->act_assignment_method) === 'selfservice') {
                    if ($activityDefinitionBean->act_email_process_user) {
                        $flowData = [
                            'cas_sugar_module' => $caseBean->cas_sugar_module,
                            'cas_sugar_object_id' => $caseBean->cas_sugar_object_id,
                        ];
                        $this->emailHandler->queueActivityEmail($flowData, $activityDefinitionBean, $userId);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Reassign the ownership to a determined user
     * @param type $caseData
     * @param type $userId
     * @return boolean
     */
    public function reassignRecordToUser($caseData, $userId)
    {
        $caseBean = $this->retrieveBean('pmse_BpmFlow'); //$this->beanFactory->getBean('BpmFlow');
        $caseBean->retrieve_by_string_fields([
            'cas_id' => $caseData['cas_id'],
            'cas_index' => $caseData['cas_index'],
        ]);
        $beanObject = $this->retrieveBean($caseBean->cas_sugar_module, $caseBean->cas_sugar_object_id);
        if (is_null($beanObject)) {
            return false;
        }
        $beanObject->assigned_user_id = $userId;

        if (PMSEEngineUtils::saveAssociatedBean($beanObject)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whenever a reassignment with round trip takes place, there exists a list of users
     * already reassigned using this option, this method obtains that list.
     *
     * @param type $caseId
     * @param type $bpmnId
     * @param type $bpmnType
     * @param type $casReassignLevel
     * @return type
     */
    public function getReassignedUserList($caseId, $bpmnId, $bpmnType, $casReassignLevel = 0)
    {
        $flowBean = $this->retrieveBean('pmse_BpmFlow'); //new BpmFlow();
        $where = 'cas_id=' . $caseId . ' AND bpmn_id=\'' . $bpmnId . '\' AND cas_reassign_level<=' . $casReassignLevel . ' AND bpmn_type=\'' . $bpmnType . '\' AND cas_index IN (SELECT max(cas_index) FROM pmse_bpm_flow WHERE cas_id=' . $caseId . ' AND bpmn_id=\'' . $bpmnId . '\' AND cas_reassign_level<=' . $casReassignLevel . ' GROUP BY cas_reassign_level)';
        $reassignList = $flowBean->get_full_list('', $where);
        //$reassignList = $this->wrapper->getSelectRows($flowBean, '', $where);
        //$reassignList = $reassignList['rowList'];
        $assignedUsers = [];
        foreach ($reassignList as $reassign) {
            $assignedUsers[] = $reassign->cas_user_id;
        }
        return $assignedUsers;
    }

    /**
     * Get the list of assignable users to a determinate task
     * (alias for the getAssignableUserList method)
     * @param type $caseId
     * @param type $caseIndex
     * @param type $fullList
     * @return type
     * @codeCoverageIgnore
     */
    public function getReassignableUserList($beanFlow, $fullList = false, $filter = null)
    {
        return $this->getAssignableUserList($beanFlow, $fullList, 'REASSIGN', $filter);
    }

    /**
     * Get the list of assignable users to a determinate task using adhoc reassignment
     * (alias for the getAssignableUserList method)
     * @param type $caseId
     * @param type $caseIndex
     * @param type $fullList
     * @return type
     * @codeCoverageIgnore
     */
    public function getAdhocAssignableUserList($beanFlow, $fullList = false, $filter = null)
    {
        return $this->getAssignableUserList($beanFlow, $fullList, 'ADHOC', $filter);
    }

    /**
     * Get the user list of assignable users
     * @param $beanFlow
     * @param bool $fullList
     * @param string $type
     * @return array
     */
    public function getAssignableUserList($beanFlow, $fullList = false, $type = 'ADHOC', $filter = null)
    {
        $membersList = [];
        $membersIds = [];
        $reassignedUsers = [];
        $assignableUsers = [];
        if (!$fullList) {
            $reassignedUsers = $this->getReassignedUserList(
                $beanFlow->cas_id,
                $beanFlow->bpmn_id,
                $beanFlow->bpmn_type,
                $beanFlow->cas_reassign_level
            );
        }
        $activityDefinition = $this->retrieveBean('pmse_BpmActivityDefinition');
        $memberList = [];
        if ($beanFlow->bpmn_type == 'bpmnActivity') {
            $activityDefinition->retrieve($beanFlow->bpmn_id);
            $teamBean = $this->retrieveBean('Teams'); //$this->beanFactory->getBean('Teams');
            $teamId = ($type == 'ADHOC') ? $activityDefinition->act_adhoc_team : $activityDefinition->act_reassign_team;
            if ($teamId == 'current_team') {
                global $current_user;
                $teamList = $teamBean->getTeamsByUser($current_user->id);
                foreach ($teamList as $team) {
                    if ($team->id != '1' && $team->id != 1) {
                        $teamProxy = $teamBean->getTeamObject();
                        $teamProxy->setTeamObject($team);
                        $teamBean->setTeamObject($teamProxy);
                        $members = $teamBean->getMembers();
                        foreach ($members as $member) {
                            if (!in_array($member->id, $membersIds)) {
                                $membersList[] = $member;
                                $membersIds[] = $member->id;
                            }
                        }
                        $memberList = array_merge($members, $memberList);
                    }
                }
            } else {
                $teamBean = $this->retrieveBean('Teams', $teamId);
                //$membersList = $teamBean->get_team_members(true, $filter);
                $membersList = $this->getTeamMembers($teamBean, $filter);
                usort($membersList, function ($a, $b) {
                    return strcmp($a->full_name, $b->full_name);
                });
            }
        }
        if (!empty($membersList)) {
            foreach ($membersList as $member) {
                if (!safeInArray($member->user_id, $reassignedUsers)) {
                    $assignableUsers[] = $member;
                }
            }
        }
        return $assignableUsers;
    }

    /**
     * Get the next user assigned to a task if the assignment is of type Round Robin,
     * which is a form of sequential assignment inside a user group or team.
     * @param $act_id
     * @return int
     */
    public function getNextUserUsingRoundRobin($act_id)
    {
        //getting record from bpm_activity_definition
        $beanBpmActivity = $this->retrieveBean('pmse_BpmActivityDefinition', $act_id);
        $assign_team = $beanBpmActivity->act_assign_team;
        $last_assigned = $beanBpmActivity->act_last_user_assigned;

        if (empty($assign_team)) {
            //set default team to global
            $assign_team = '1';
        }

        $q = $this->prepareTeamUserIdsQuery($assign_team);
        $q->where()->gt('id', $last_assigned);
        $q->limit(1);
        $nextUserId = $q->execute();

        if (!$nextUserId) {
            $q = $this->prepareTeamUserIdsQuery($assign_team);
            $q->limit(1);
            $nextUserId = $q->execute();
        }

        $nextUserId = $nextUserId ? $nextUserId[0]['id'] : '';

        //updating last user selected
        $beanBpmActivity->act_last_user_assigned = $nextUserId;
        $beanBpmActivity->save();

        return $nextUserId;
    }

    /**
     * Get the next user assigned to a task if the assignment is of type Round Robin,
     * which is a form of availibility required inside a user group or team.
     *
     * @param $bpmnElement BPM element
     * @param $bean bean of target module
     * @param $flowData the flow data
     * @return int
     */
    public function getNextAvailableUser($bpmnElement, $bean, $flowData)
    {
        // Set "Assigned To" by Availability is unchecked. We use old Round Robin logic to assign users.
        if (empty($bpmnElement['act_set_by_avl'])) {
            return $this->getNextUserUsingRoundRobin($bpmnElement['id']);
        }

        //getting record from bpm_activity_definition
        $beanBpmActivity = $this->retrieveBean('pmse_BpmActivityDefinition', $bpmnElement['id']);
        $assign_team = $beanBpmActivity->act_assign_team;
        $last_assigned = $beanBpmActivity->act_last_user_assigned;

        if (empty($assign_team)) {
            //set default team to global
            $assign_team = BeanFactory::getBean('Teams')->getGlobalTeamID();
        }

        $q = $this->prepareTeamUserIdsQuery($assign_team, true);
        // ids greater than the last assigned user id
        // ordered by id ascending
        $q->where()->gt('id', $last_assigned);
        $gtRows = $q->execute();

        $q = $this->prepareTeamUserIdsQuery($assign_team, true);
        // ids less than or equal to the last assigned user id
        // ordered by id ascending
        $q->where()->lte('id', $last_assigned);
        $ltRows = $q->execute();

        // maintains the ascending order on user ids like the old behavior
        // this array starts from the next user of the last assigned user in the ascending list,
        // continues iterating the list one by one and checking, when reaches the end of the list,
        // iterates from the beginning until the last assigned user
        $orderedUsers = array_column(array_merge($gtRows, $ltRows), 'id');

        // used when required shift availability is set, otherwise will remain 0
        $shiftTimeReqInSec = 0;

        $checkTime = null;
        if (!empty($bpmnElement['act_avl_count']) &&
            !empty($bpmnElement['act_avl_before_type']) &&
            !empty($bean->{$bpmnElement['act_avl_before_type']})) {
            // 'act_avl_before_type' has converted to UTC already when the record is saved.
            // We need to explicitly set UTC timezone in case the default timezone is not UTC.
            $checkTime = new \SugarDateTime($bean->{$bpmnElement['act_avl_before_type']}, new DateTimeZone('UTC'));

            // convert act_avl_count (required shift availability) to seconds
            switch ($bpmnElement['act_avl_type']) {
                case 'minutes':
                    $shiftTimeReqInSec = $bpmnElement['act_avl_count'] * 60;
                    break;
                case 'hours':
                    $shiftTimeReqInSec = $bpmnElement['act_avl_count'] * 60 * 60;
                    break;
            }
        } else {
            // Required shift availability has count value set to 0 or blank.
            // Or other availability criteria is not set, we want to set the availability datetime to current time
            // based on the UTC timezone.
            $checkTime = new \SugarDateTime(TimeDate::getInstance()->nowDb(), new DateTimeZone('UTC'));
        }
        $this->getLogger()->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] time is sent to check for " .
            "Round Robin availability: {$checkTime}");
        $nextUserId = null;
        foreach ($orderedUsers as $userId) {
            $user = BeanFactory::retrieveBean('Users', $userId);
            if (isset($user) &&
                !$this->userHasShiftExceptions($user, $checkTime) &&
                !$this->userHasHoliday($user, $checkTime) &&
                $this->userAvailableInShifts($user, $checkTime, $shiftTimeReqInSec, $flowData)) {
                $nextUserId = $user->id;
                break;
            }
        }
        if (!$nextUserId) {
            // uses selected pre-defined user
            if (isset($bpmnElement['act_reserve_user'])) {
                switch ($bpmnElement['act_reserve_user']) {
                    case 'owner':
                        $nextUserId = $this->getRecordOwnerId(
                            $flowData['cas_user_id'],
                            $flowData['cas_sugar_module']
                        );
                        break;
                    case 'supervisor':
                        $nextUserId = $this->getSupervisorId(
                            $flowData['cas_user_id']
                        );
                        break;
                    case 'currentuser':
                        $nextUserId = $flowData['cas_user_id'];
                        break;
                    default:
                        // user id from search and select
                        $nextUserId = $bpmnElement['act_reserve_user'];
                        break;
                }
            } else {
                // no pre-defined user is set
                $nextUserId = '';
                $this->getLogger()->info("[{$flowData['cas_id']}][{$flowData['cas_index']}] no default user " .
                    'is defined from Round Robin action');
            }
        } else {
            //updating last user selected only if its from assigned team
            $beanBpmActivity->act_last_user_assigned = $nextUserId;
            $beanBpmActivity->save();
        }

        return $nextUserId;
    }

    /**
     * Checks the user has Holiday set or not
     *
     * @param SugarBean $user the user bean
     * @param SugarDateTime $checkTime the datetime to be checked
     * @return Bool
     */
    protected function userHasHoliday(SugarBean $user, \SugarDateTime $checkTime)
    {
        if (!$user->load_relationship('holidays')) {
            return false;
        }

        $holidays = $user->holidays->getBeans();
        foreach ($holidays as $holiday) {
            if (!empty($holiday->holiday_date)) {
                // as in user profile, uses guess timezone if user timezone is not set in user preference
                $timezone = $user->getPreference('timezone') ?? TimeDate::guessTimezone();
                $holidayStart = new \SugarDateTime($holiday->holiday_date, new DateTimeZone($timezone));
                $holidayStart = $holidayStart->setTimezone(new DateTimeZone('UTC'));
                $holidayEnd = new \SugarDateTime($holiday->holiday_date, new DateTimeZone($timezone));
                $holidayEnd = $holidayEnd->setTime('23', '59', '59');
                $holidayEnd = $holidayEnd->setTimezone(new DateTimeZone('UTC'));
                if ($checkTime >= $holidayStart && $checkTime <= $holidayEnd) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks the user has Shift Exceptions or not
     *
     * @param SugarBean $user the user bean
     * @param SugarDateTime $checkTime the datetime to be checked for availablility
     * @return Bool
     */
    protected function userHasShiftExceptions(SugarBean $user, \SugarDateTime $checkTime)
    {
        if (!$user->load_relationship('shift_exceptions')) {
            return false;
        }

        $shiftExceptionBeans = $user->shift_exceptions->getBeans();
        foreach ($shiftExceptionBeans as $shiftExceptionBean) {
            if ($shiftExceptionBean->enabled) {
                $start_date = new \SugarDateTime(
                    $shiftExceptionBean->start_date,
                    new DateTimeZone($shiftExceptionBean->timezone)
                );
                $end_date = new \SugarDateTime(
                    $shiftExceptionBean->end_date,
                    new DateTimeZone($shiftExceptionBean->timezone)
                );
                $start_date->setTime($shiftExceptionBean->start_hour, $shiftExceptionBean->start_minutes);
                $start_date = $start_date->setTimezone(new DateTimeZone('UTC'));
                $end_date->setTime($shiftExceptionBean->end_hour, $shiftExceptionBean->end_minutes);
                $end_date = $end_date->setTimezone(new DateTimeZone('UTC'));
                if ($checkTime >= $start_date && $checkTime <= $end_date) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks the user's availability in Shifts
     *
     * @param SugarBean $user the user bean
     * @param SugarDateTime $checkTime the datetime to be checked for availablility
     * @param int $shiftTimeReqInSec the shift time required (in seconds)
     * @param $flowData the flow data
     * @return bool
     */
    protected function userAvailableInShifts(SugarBean $user, \SugarDateTime $checkTime, int $shiftTimeReqInSec, $flowData)
    {
        if (!$user->load_relationship('shifts')) {
            return false;
        }

        $timeAvail = $shiftNum = 0;

        $nowTime = TimeDate::getInstance()->getNow();

        $shiftBeans = $user->shifts->getBeans();
        foreach ($shiftBeans as $shiftBean) {
            $shiftNum++;

            $date_start = new \SugarDateTime(
                $shiftBean->date_start,
                new DateTimeZone($shiftBean->timezone)
            );
            $date_end = new \SugarDateTime(
                $shiftBean->date_end,
                new DateTimeZone($shiftBean->timezone)
            );
            $date_end->setTime('23', '59', '59');

            $checkTime = $checkTime->setTimezone(new DateTimeZone($shiftBean->timezone));
            $nowTime = $nowTime->setTimezone(new DateTimeZone($shiftBean->timezone));

            if ($shiftTimeReqInSec > 0) {
                // return false if deadline is in the past
                if ($checkTime < $nowTime) {
                    return false;
                }

                $timeAvail += $this->getAvailableTimeInShift($shiftBean, $nowTime, $checkTime);

                if ($timeAvail >= $shiftTimeReqInSec) {
                    $this->logger->info("[{$flowData['cas_id']}][{$flowData['cas_index']}]" . ' User ' . $user->full_name .
                        ' meets the ' . $shiftTimeReqInSec . ' sec time requirement with ' . $timeAvail .
                        ' sec availability via ' . $shiftNum . ' shift(s) for ' . $checkTime . ' deadline');

                    return true;
                }
            } elseif ($checkTime >= $date_start && $checkTime <= $date_end) {
                $weekDay = strtolower($checkTime->format('l'));

                $dayShift = $this->getShiftTimeForDay($shiftBean, $weekDay, $checkTime);

                if ($dayShift['isOpen']) {
                    if ($checkTime >= $dayShift['startTime'] && $checkTime <= $dayShift['endTime']) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Calculate the shift time available between now and the specified deadline
     *
     * Calculation: first week time + (num of weeks * week time) + last week time
     * Note: A week is defined as Sunday - Saturday, and NOT 7 days from today
     * Example: if today is Thursday, first week time is Thursday + Friday + Saturday
     *
     * @param SugarBean $shiftBean
     * @param SugarDateTime $nowTime the current time
     * @param SugarDateTime $checkTime the deadline
     * @return int the time available
     */
    protected function getAvailableTimeInShift(SugarBean $shiftBean, \SugarDateTime $nowTime, \SugarDateTime $checkTime)
    {
        $timeAvail = 0;

        if ($checkTime < $nowTime) {
            return $timeAvail;
        }

        $shiftTimes = $this->getShiftTimesForWeek($shiftBean);

        [
            'daysInFirstWeek' => $daysInFirstWeek,
            'daysInLastWeek' => $daysInLastWeek,
            'numOfWeeks' => $numOfWeeks,
        ] = $this->getDayWeekTotalInShift($nowTime, $checkTime, $shiftTimes['shiftEndDate']);

        $timeAvail += $this->getFirstAndFullWeekAvailabilityInShift(
            $nowTime,
            $checkTime,
            $shiftTimes,
            $daysInFirstWeek,
            $daysInLastWeek,
            $numOfWeeks
        );

        $timeAvail += $this->getLastWeekAvailabilityInShift($checkTime, $shiftTimes, $daysInLastWeek);

        return $timeAvail;
    }

    /**
     * Get the total number of days and weeks between today and deadline or shift end date (whichever is sooner)
     *
     * @param SugarDateTime $nowTime the current time
     * @param SugarDateTime $checkTime the deadline
     * @param string $shiftEndDateStr
     * @return array
     */
    protected function getDayWeekTotalInShift(\SugarDateTime $nowTime, \SugarDateTime $checkTime, string $shiftEndDateStr)
    {
        $todayNum = intval($nowTime->format('w'));

        // calculate the total number of days, including today
        // if shift ends before the deadline, calculate days between now and shift end
        // otherwise, calculate days between now and deadline
        $nowTimeClone = clone $nowTime;
        $nowTimeClone->setTime('00', '00', '00');
        $shiftEndDate = new \SugarDateTime($shiftEndDateStr, $nowTimeClone->getTimezone());
        $shiftEndDate->setTime('23', '59', '59');
        $checkTimeClone = ($shiftEndDate < $checkTime) ? clone $shiftEndDate : clone $checkTime;
        $checkTimeClone->setTime('00', '00', '00');
        $totalDays = $checkTimeClone->diff($nowTimeClone)->days + 1;

        $daysInFirstWeek = ($todayNum + $totalDays > \SugarDateTime::DOW_SAT) ?
            \SugarDateTime::DOW_SAT - $todayNum + 1 :
            $totalDays;
        $totalDays -= $daysInFirstWeek;
        $daysInLastWeek = $totalDays % 7;
        $numOfWeeks = intval($totalDays / 7);

        return [
            'totalDays' => $totalDays,
            'daysInFirstWeek' => $daysInFirstWeek,
            'daysInLastWeek' => $daysInLastWeek,
            'numOfWeeks' => $numOfWeeks,
        ];
    }

    /**
     * Calculates time available in shift for first and full weeks
     *
     * @param SugarDateTime $nowTime the current time
     * @param SugarDateTime $checkTime the deadline
     * @param array $shiftTimes
     * @param int $daysInFirstWeek
     * @param int $daysInLastWeek
     * @param int $numOfWeeks
     * @return int the time available in seconds
     */
    protected function getFirstAndFullWeekAvailabilityInShift(
        \SugarDateTime $nowTime,
        \SugarDateTime $checkTime,
        array          $shiftTimes,
        int            $daysInFirstWeek,
        int            $daysInLastWeek,
        int            $numOfWeeks
    ) {

        $timeAvail = 0;
        $todayNum = intval($nowTime->format('w'));

        // first week
        for ($i = 0; $i < $daysInFirstWeek; $i++) {
            $dayShiftTime = $shiftTimes[$todayNum + $i];

            if (!$dayShiftTime['isOpen']) {
                continue;
            }

            if ($i === 0) {
                // the current day
                $startTime = $dayShiftTime['startTime'];
                $endTime = $dayShiftTime['endTime'];
                $startOffset = $startTime->getTimezone()->getOffset($nowTime);
                $endOffset = $endTime->getTimezone()->getOffset($nowTime);
                $timeFormat = TimeDate::getInstance()->get_time_format();

                $nowTimeStr = strtotime($nowTime);
                $checkTimeStr = strtotime($checkTime);

                $startTimeStr = strtotime($startTime->format($timeFormat), $nowTimeStr) - $startOffset;
                $endTimeStr = strtotime($endTime->format($timeFormat), $nowTimeStr) - $endOffset;

                if ($nowTimeStr < $startTimeStr) {
                    // if shift has not started yet

                    // if the deadline is before the shift end, calculate time between shift start and deadline
                    // otherwise, use entire shift duration
                    $timeLeft = ($checkTimeStr < $endTimeStr) ?
                        $checkTimeStr - $startTimeStr :
                        $dayShiftTime['durationInSec'];

                    $timeAvail += $timeLeft;
                } elseif ($nowTimeStr >= $startTimeStr && $nowTimeStr <= $endTimeStr) {
                    // if shift is currently in progress

                    // if the deadline is before the shift end, use deadline to calculate time
                    // otherwise, use shift end to calculate
                    $timeLeft = (($checkTimeStr < $endTimeStr) ? $checkTimeStr : $endTimeStr) - $nowTimeStr;

                    $timeAvail += $timeLeft;
                }
                // else do nothing if shift ended for the day
            } else {
                if ($i === ($daysInFirstWeek - 1) && $daysInLastWeek === 0) {
                    // last day and deadline is the same week

                    $timeAvail += $this->getAvailableTimeInShiftOnLastDay($checkTime, $dayShiftTime);
                } else {
                    $timeAvail += $dayShiftTime['durationInSec'];
                }
            }
        }

        // calculate time for the full weeks
        $timeAvail += $numOfWeeks * $shiftTimes['weekDurationInSec'];

        return $timeAvail;
    }

    /**
     * Calculates time available in shift for the last week
     *
     * @param SugarDateTime $checkTime the deadline
     * @param array $shiftTimes
     * @param int $daysInLastWeek
     * @return int the time available in seconds
     */
    protected function getLastWeekAvailabilityInShift(\SugarDateTime $checkTime, array $shiftTimes, int $daysInLastWeek)
    {
        $timeAvail = 0;

        for ($i = 0; $i < $daysInLastWeek; $i++) {
            $dayShiftTime = $shiftTimes[$i];

            if (!$dayShiftTime['isOpen']) {
                continue;
            }

            if ($i === ($daysInLastWeek - 1)) {
                // last day

                $timeAvail += $this->getAvailableTimeInShiftOnLastDay($checkTime, $dayShiftTime);
            } else {
                $timeAvail += $dayShiftTime['durationInSec'];
            }
        }

        return $timeAvail;
    }

    /**
     * Calculate the time available on the deadline day (last day)
     *
     * @param SugarDateTime $checkTime the deadline
     * @param array $dayShift
     * @return int the time available
     */
    protected function getAvailableTimeInShiftOnLastDay(\SugarDateTime $checkTime, array $dayShift)
    {
        $timeAvail = 0;

        $startTime = $dayShift['startTime'];
        $endTime = $dayShift['endTime'];
        $startOffset = $startTime->getTimezone()->getOffset($checkTime);
        $endOffset = $endTime->getTimezone()->getOffset($checkTime);
        $timeFormat = TimeDate::getInstance()->get_time_format();

        $checkTimeStr = strtotime($checkTime);
        $startTimeStr = strtotime($startTime->format($timeFormat), $checkTimeStr) - $startOffset;
        $endTimeStr = strtotime($endTime->format($timeFormat), $checkTimeStr) - $endOffset;

        if ($checkTimeStr > $endTimeStr) {
            // if the deadline is after the shift, use entire shift duration

            $timeAvail = $dayShift['durationInSec'];
        } elseif ($checkTimeStr >= $startTimeStr && $checkTimeStr <= $endTimeStr) {
            // if the deadline is in between the shift, calc the time between shift start and deadline

            $timeAvail = $checkTimeStr - $startTimeStr;
        }
        // else do nothing if the deadline is before the shift start

        return $timeAvail;
    }

    /**
     * Get and return the shift times for the entire week
     *
     * @param SugarBean $shiftBean
     * @return array
     */
    protected function getShiftTimesForWeek(SugarBean $shiftBean)
    {
        $daysOfWeek = [
            \SugarDateTime::DOW_SUN => 'sunday',
            \SugarDateTime::DOW_MON => 'monday',
            \SugarDateTime::DOW_TUE => 'tuesday',
            \SugarDateTime::DOW_WED => 'wednesday',
            \SugarDateTime::DOW_THU => 'thursday',
            \SugarDateTime::DOW_FRI => 'friday',
            \SugarDateTime::DOW_SAT => 'saturday',
        ];

        $shiftTimes = [
            'shiftStartDate' => $shiftBean->date_start,
            'shiftEndDate' => $shiftBean->date_end,
        ];

        $weekDurationInSec = 0;

        foreach ($daysOfWeek as $dayKey => $day) {
            $shiftForDay = $this->getShiftTimeForDay($shiftBean, $day);

            if ($shiftForDay['isOpen']) {
                $shiftForDay['durationInSec'] = strtotime($shiftForDay['endTime']) - strtotime($shiftForDay['startTime']);

                $weekDurationInSec += $shiftForDay['durationInSec'];
            }

            $shiftForDay['day'] = $day;
            $shiftTimes[$dayKey] = $shiftForDay;
        }

        $shiftTimes['weekDurationInSec'] = $weekDurationInSec;

        return $shiftTimes;
    }

    /**
     * Get and return the shift times for the specified weekday
     *
     * @param SugarBean $shiftBean the Shift Bean
     * @param string $weekDay the weekday to get
     * @param SugarDateTime|null $dateTime
     * @return array
     */
    protected function getShiftTimeForDay(SugarBean $shiftBean, string $weekDay, \SugarDateTime $dateTime = null)
    {
        $shiftTime = [
            'isOpen' => false,
        ];

        $weekDay = strtolower($weekDay);

        if ($shiftBean->isOpen($weekDay)) {
            if (is_null($dateTime)) {
                // set all fields to Unix Epoch with '!', as we only care about time
                $dateTime = \SugarDateTime::createFromFormat('!H:i:s', '00:00:00', new DateTimeZone($shiftBean->timezone));
            }

            $openTime = $shiftBean->getOpenTime($weekDay);
            $start_time = clone $dateTime;
            $start_time->setTime($openTime['hour'], $openTime['minutes']);

            $closeTime = $shiftBean->getCloseTime($weekDay);
            $end_time = clone $dateTime;
            $end_time->setTime($closeTime['hour'], $closeTime['minutes']);

            $shiftTime = [
                'isOpen' => true,
                'startTime' => $start_time,
                'endTime' => $end_time,
            ];
        }

        return $shiftTime;
    }

    /**
     * Gets all members of a team who are both active users and active employees
     *
     * @param string $teamId the team id
     * @param bool $shiftAvailable user has shift defined if true
     * @return SugarQuery
     */
    protected function prepareTeamUserIdsQuery($teamId, $shiftAvailable = false)
    {
        $q = new SugarQuery();
        $q->select(['id']);
        $q->from(BeanFactory::newBean('Users'));

        $q->joinTable('team_memberships', ['alias' => 'membership'])->on()
            ->equals('membership.team_id', $teamId)
            ->equalsField('membership.user_id', 'id')
            ->equals('membership.explicit_assign', 1)
            ->equals('membership.deleted', 0);

        if ($shiftAvailable) {
            $q->joinTable('shifts_users', ['alias' => 'shift'])->on()
                ->equalsField('shift.user_id', 'id')
                ->equals('shift.deleted', 0);
        }

        $q->where()
            ->equals('status', 'Active')
            ->equals('employee_status', 'Active');
        $q->orderBy('id', 'ASC');

        return $q;
    }

    /**
     * Get the id of the current user
     * @return type
     * @global type $current_user
     */
    public function getCurrentUserId()
    {
        global $current_user;
        return $current_user->id;
    }

    /**
     * Get the id of the owner of the current record.
     * @param type $currentSugarId
     * @param type $currentSugarModule
     * @return string
     */
    public function getRecordOwnerId($currentSugarId, $currentSugarModule)
    {
        $bean = $this->retrieveBean($currentSugarModule, $currentSugarId);
        if (isset($bean->assigned_user_id)) {
            $currentUserId = $bean->assigned_user_id;
        } elseif (isset($bean->created_by)) {
            $currentUserId = $bean->created_by;
        } else {
            $currentUserId = 'unknown';
        }
        return $currentUserId;
    }

    /**
     * Get the supervisor id from a determined user.
     * @param type $currentUserId
     * @return type
     * @global type $db
     */
    public function getSupervisorId($currentUserId)
    {
        global $db;
        $supervisor = $currentUserId;

        $query = "select reports_to_id from users where id = '$currentUserId' ";
        $result = $db->Query($query);
        $row = $db->fetchByAssoc($result);

        if (is_array($row)) {
            if (isset($row['reports_to_id']) && trim($row['reports_to_id']) != '') {
                $supervisor = $row['reports_to_id'];
            }
        }
        return $supervisor;
    }

    private function getTeamMembers($teamBean, $args)
    {
        $options = [];
        // Set up the defaults
        $options['limit'] = 20;
        $options['offset'] = 0;
        $options['add_deleted'] = true;

        if (!empty($args['max_num'])) {
            $options['limit'] = (int)$args['max_num'];
        }

        if (!empty($args['deleted'])) {
            $options['add_deleted'] = false;
        }

        if (!empty($args['offset'])) {
            if ($args['offset'] == 'end') {
                $options['offset'] = 'end';
            } else {
                $options['offset'] = (int)$args['offset'];
            }
        }

        // Get the list of members
        $membersBean = BeanFactory::newBean('TeamMemberships');

        $fields = [
            'user_id',
        ];

        $q = new SugarQuery();
        $q->from($membersBean, ['add_deleted' => true]);
        $q->distinct(false);

        $q->joinTable('users', ['alias' => 'users', 'joinType' => 'INNER', 'linkingTable' => true])
            ->on()
            ->equalsField('users.id', 'user_id')
            ->equals('users.deleted', 0);

        $fields[] = ['users.first_name', 'first_name'];
        $fields[] = ['users.last_name', 'last_name'];
        $fields[] = ['users.status', 'status'];

        $q->where()
            ->equals('team_id', $teamBean->id)
            ->equals('explicit_assign', 1)
            ->notEquals('users.status', 'Inactive');

        $q->where()
            ->queryOr()
            ->starts('users.first_name', $args['filter'] . '%')
            ->starts('users.last_name', $args['filter'] . '%');

        $q->select($fields);

        $q->limit($options['limit'] + 1);
        $q->offset($options['offset']);

        $member_list = $q->execute();

        $user_list = [];

        foreach ($member_list as $current_member) {
            $user = BeanFactory::getBean('Users', $current_member['user_id']);
            if ($user->status == 'Active') {
                $user_list[] = $user;
            }
        }

        return $user_list;
    }
}
