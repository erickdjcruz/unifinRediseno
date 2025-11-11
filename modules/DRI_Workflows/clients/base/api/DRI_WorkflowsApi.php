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
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityDatesHelper;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey as Journey;
use Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA\CheckAndPerformRSA as CheckAndPerformRSA;
use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;
use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\ActivityHooksHelper;

class DRI_WorkflowsApi extends SugarApi
{
    // Stage level fields definition
    protected static $stageFieldsForBeanPopulate = [
        'id',
        'name',
        'label',
        'sort_order',
        'state',
        'score',
        'points',
        'progress',
        'momentum_ratio',
        'momentum_points',
        'momentum_score',
        'dri_subworkflow_template_id',
        'dri_subworkflow_template_name',
    ];

    // Activity level fields definition
    protected static $activityFieldsForBeanPopulate = [
        'id',
        'name',
        'status',
        'dri_workflow_sort_order',
        'customer_journey_type',
        'customer_journey_score',
        'customer_journey_progress',
        'customer_journey_points',
        'cj_parent_activity_type',
        'is_cj_parent_activity',
        'is_customer_journey_activity',
        'dri_subworkflow_id',
        'cj_parent_activity_id',
        'assigned_user_id',
        'assigned_user_name',
        'cj_momentum_start_date',
        'cj_momentum_end_date',
        'cj_momentum_points',
        'cj_momentum_ratio',
        'cj_momentum_score',
        'cj_url',
        'start_next_journey_id',
    ];

    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'chartData' => [
                'reqType' => 'GET',
                'path' => ['?', '?', 'customer-journey', 'chart-data'],
                'pathVars' => ['module', 'record', '', '', 'selected'],
                'method' => 'chartData',
                'shortHelp' => 'Provide journey and related stages data of chart',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowschartData.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'momentumChart' => [
                'reqType' => 'GET',
                'path' => ['?', '?', 'customer-journey', 'momentum-chart'],
                'pathVars' => ['module', 'record', '', '', 'selected'],
                'method' => 'momentumChart',
                'shortHelp' => 'Provide journey related data for momentum chart',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsmomentumChart.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'widgetData' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflows', '?', 'widget-data'],
                'pathVars' => ['module', 'record'],
                'method' => 'widgetData',
                'shortHelp' => 'Provide journey, related forms and stages data for widget',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowswidgetData.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'cancel' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', 'cancel'],
                'pathVars' => ['module', 'action'],
                'method' => 'cancel',
                'shortHelp' => 'Cancel current journey and also related stages and activities',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflowscancel.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'archive' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', '?', 'archive'],
                'pathVars' => ['module', 'record'],
                'method' => 'archive',
                'shortHelp' => 'Set the archived status of current journey to true',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflowsarchive.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'unarchive' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', '?', 'unarchive'],
                'pathVars' => ['module', 'record'],
                'method' => 'unarchive',
                'shortHelp' => 'Set the archived status of current journey to false',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflowsunarchive.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'start' => [
                'reqType' => 'POST',
                'path' => ['?', '?', 'customer-journey', 'start-cycle'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'start',
                'shortHelp' => 'Start Smart Guide template on parent module',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflowsstart.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'notApplicableSubActivities' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', 'not-applicable'],
                'pathVars' => ['module', 'action'],
                'method' => 'notApplicableSubActivities',
                'shortHelp' => 'Set Activty and its sub activities status to Not Applicable',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsnotApplicableSubActivities.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'updateActivityState' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', 'update-activity-state'],
                'pathVars' => ['module', 'action'],
                'method' => 'updateActivityState',
                'shortHelp' => 'Set status, update start and end dates of the specific activity',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsupdateActivityState.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'create-relationship' => [
                'reqType' => 'POST',
                'path' => ['dri-workflows', 'create-relationship', '?'],
                'pathVars' => ['module', 'action', 'repair'],
                'method' => 'createModuleRelationships',
                'shortHelp' => 'Create relationship of DRI_Workflows with some Sugar Core Modules',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsCreateDefRelationships.html',
                'minVersion' => '11.19',
            ],
            'graceperiod-remaining-days' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflows', 'graceperiod-remaining-days'],
                'pathVars' => ['module', 'record'],
                'method' => 'getGracePeriodRemainingDays',
                'shortHelp' => 'Provide grace period remaining days',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsgetGracePeriodRemainingDays.html',
                'minVersion' => '11.19',
            ],
            'delete-stage-journey' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflows', 'delete-stage-journey'],
                'pathVars' => ['module', 'action'],
                'method' => 'deleteStageOrJourney',
                'shortHelp' => 'Delete journey or stage according to the provide module and id',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsDeleteStageOrJourney.html',
                'minVersion' => '11.21',
            ],
            'getActiveSmartGuidesCount' => [
                'reqType' => 'GET',
                'path' => ['?', '?', 'activeSmartGuidesCount'],
                'pathVars' => ['module', 'record'],
                'method' => 'getActiveSmartGuidesCount',
                'shortHelp' => 'Provide the count of active smart guides related to a particular record',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsgetActiveSmartGuidesCount.html',
                'minVersion' => '11.22',
            ],
            'getSmartGuidesCount' => [
                'reqType' => 'GET',
                'path' => ['?', '?', 'get-smartguides-count'],
                'pathVars' => ['module', 'record'],
                'method' => 'getSmartGuidesCount',
                'shortHelp' => 'give the count of smart guides',
                'longHelp' => '/include/api/help/customer_journeyDRI_WorkflowsgetSmartGuidesCount.html',
                'minVersion' => '11.22',
            ],
        ];
    }

    /**
     * Get the count of the smart guides
     *
     * @param ServiceBase $api
     * @param array $args
     * @throws SugarQueryException
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     */
    public function getSmartGuidesCount(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);
        $configuratorObj = new Configurator();
        $configuratorObj->loadConfig();

        $parentModuleBean = \BeanFactory::getBean($args['module']);
        $parentIDFieldName = strtolower($parentModuleBean->object_name) . '_id';
        $workflowBean = \BeanFactory::getBean('DRI_Workflows');
        $query = new \SugarQuery();

        $query->select(['id']);
        $query->from($workflowBean)->where()
            ->equals($parentIDFieldName, $args['record']);

        $configCount = $configuratorObj->config['list_max_entries_per_page'];
        $count = safeCount($query->execute());

        return $count > $configCount ? $configCount : $count;
    }

    /**
     * Provide journey and related stages data of chart
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    public function chartData(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        try {
            $this->checkLicense();

            $journey = $this->getChartJourneyFromArgs($api, $args);

            $data = [
                'id' => $journey->id,
                'name' => $journey->name,
                'state' => $journey->state,
                'progress' => $journey->progress,
                'stages' => [],
            ];

            if ($journey) {
                $stages = $journey->getStages();
                $coverage = safeCount($stages) > 0 ? 100 / safeCount($stages) : 0;
                foreach ($stages as $stage) {
                    $data['stages'][] = [
                        'id' => $stage->id,
                        'label' => $stage->label,
                        'name' => $stage->name,
                        'state' => $stage->state,
                        'values' => [$coverage],
                        'count' => $stage->score,
                        'percentage' => SugarMath::init($stage->progress)->mul(100)->result(),
                    ];
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $data;
    }

    /**
     * Provide journey related data for momentum chart
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionError
     * @throws NotFoundException
     * @throws Exception
     */
    public function momentumChart(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        try {
            $this->checkLicense();

            $journey = $this->getChartJourneyFromArgs($api, $args);
            $ratio = round($journey->momentum_ratio * 100);

            $data = [
                'id' => $journey->id,
                'name' => $journey->name,
                'ratio' => $ratio,
                'values' => [
                    [
                        'group' => 1,
                        't' => $ratio,
                        'value' => $ratio,
                    ],
                ],
                'data' => [
                    [
                        'key' => 'Range 1',
                        'y' => 25,
                        'value' => 25,
                        'color' => '#e61718',
                    ],
                    [
                        'key' => 'Range 2',
                        'y' => 50,
                        'value' => 25,
                        'color' => '#fb8724',
                    ],
                    [
                        'key' => 'Range 3',
                        'y' => 75,
                        'value' => 25,
                        'color' => '#e5a117',
                    ],
                    [
                        'key' => 'Range 4',
                        'y' => 100,
                        'value' => 25,
                        'color' => '#33800d',
                    ],
                ],
            ];
        } catch (\Exception $e) {
            throw $e;
        }

        return $data;
    }

    /**
     * Provide Journey bean
     *
     * @param ServiceBase $api
     * @param array $args
     * @return DRI_Workflow|null|SugarBean
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    private function getChartJourneyFromArgs(ServiceBase $api, array $args)
    {
        $journey = null;
        $bean = $this->loadBean($api, $args);

        if (!$bean) {
            // Couldn't load the bean
            throw new SugarApiExceptionNotFound(
                "Could not find record: {$args['record']} in module: {$args['module']}"
            );
        }

        if ($bean instanceof DRI_Workflow) {
            $journey = $bean;
        } elseif ($bean->load_relationship('dri_workflows')) {
            $journeys = $bean->dri_workflows->getBeans(['orderby' => 'date_entered DESC']);

            $GLOBALS['log']->info(
                "Loading chart data for Smart Guide {$bean->id} for parent {$args['module']}:{$args['record']}"
            );

            if (!empty($args['selected'])) {
                $journey = BeanFactory::retrieveBean('DRI_Workflows', $args['selected']);
            } else {
                $journey = $this->getChartJourney($journeys);
            }
        }

        return $journey;
    }

    /**
     * Provide journey, related forms and stages data for widget
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionError
     * @throws NotFoundException
     */
    public function widgetData(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        $userAccess = true;

        try {
            $this->checkLicense();
        } catch (CJException\InvalidLicenseException $e) {
            $userAccess = false;
        }

        try {
            /** @var DRI_Workflow $journey */
            $journey = $this->loadBean($api, $args);
        } catch (\SugarApiExceptionNotFound $e) {
            throw new SugarApiExceptionNotFound('Journey has been deleted');
        }

        $fields = [
            'id',
            'name',
            'progress',
            'state',
            'score',
            'points',
            'archived',
            'description',
            'dri_workflow_template_id',
            'dri_workflow_template_name',
            'disabled_stage_actions',
            'disabled_activity_actions',
            'momentum_ratio',
            'momentum_points',
            'momentum_score',
            'assigned_user_id',
            'assigned_user_name',
            'assigned_user_picture',
        ];

        foreach ($journey->getParentDefinitions() as $def) {
            $fields[] = $def['name'];
            $fields[] = $def['id_name'];
        }

        // start with loading the complete journey so we
        // can do this in the most optimised fashion
        $journey->load();

        $template = $journey->getTemplate();

        $data = $this->formatBean($api, ['fields' => $fields], $journey);
        $data['stages'] = [];
        $data['journey'] = [];
        $data['user_access'] = $userAccess;
        $data['disabled_stage_actions'] = $template->getDisabledStageActions();
        $data['disabled_activity_actions'] = $template->getDisabledActivityActions();

        $GLOBALS['log']->info(
            "Loading widget data for Smart Guide {$journey->id} for parent {$args['module']}:{$args['record']}"
        );

        if ($userAccess) {
            foreach ($journey->getStages() as $stage) {
                if ($stage->ACLAccess('view')) {
                    $data['stages'][] = $this->formatStage($api, $args, $stage);
                }
            }

            $data['journey']['forms'] = $this->getFormsOfJourney($journey, $api, $args);
        }

        return $data;
    }

    /**
     * Cancel current journey and also related stages and activities
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array|null
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function cancel(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $this->checkLicense();

        $response = $this->validateActivities($args['activities'], $args['fieldsToValidate'], 'not_applicable');

        if (!empty($response)) {
            return $response;
        }

        /** @var DRI_Workflow $journey */
        $journey = $this->loadBean($api, $args);

        $response = $journey->cancel();
        if (!empty($response)) {
            $args = array_merge($args, $response);
        }

        return $this->formatSuccessResponse($api, $args);
    }

    /**
     * Validate activities according to fieldsToValidate
     *
     * @param array $activities
     * @param array $fieldsToValidate
     * @param string $statusType
     * @return array|null
     */
    private function validateActivities($activities, $fieldsToValidate, $statusType)
    {
        foreach ($activities as $activityInfo) {
            $activity = BeanFactory::retrieveBean($activityInfo['module'], $activityInfo['id']);

            if (!empty($activity)) {
                $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

                if (!$activityHandler->isCompleted($activity)) {
                    if ($statusType === 'not_applicable') {
                        $status = $activityHandler->getNotApplicableStatus($activity);
                    } elseif ($statusType === 'complete') {
                        $status = $activityHandler->getCompletedStatus($activity);
                    } else {
                        $status = $statusType;
                    }
                    $requiredField = $this->validateFields($fieldsToValidate[$activity->module_dir], $activity, $status);
                }

                if (!empty($requiredField)) {
                    return $this->formatRequiredFieldResponse($activity, $requiredField);
                }
            }
        }
    }

    /**
     * Validate fields of an activity
     *
     * @param array $fields
     * @param SugarBean $activity
     * @param string $status
     * @return array|null
     */
    private function validateFields($fields, $activity, $status)
    {
        $backupStatus = $activity instanceof SugarBean && property_exists($activity, 'status');

        if ($backupStatus) {
            // store activity status
            $previousStatus = $activity->status;
            // set given status to validate Required Formula for bean
            $activity->status = $status;
        }

        $requiredField = validateRequiredFormula($fields, $activity);

        if ($backupStatus) {
            // reset status
            $activity->status = $previousStatus;
        }

        return $requiredField;
    }

    /**
     * Format the required field error response
     *
     * @param SugarBean $bean
     * @param string $field
     * @return array
     */
    private function formatRequiredFieldResponse($bean, $field)
    {
        return [
            'id' => $bean->id,
            'name' => $bean->name,
            'module' => $bean->module_dir,
            'field' => $field,
            'isValid' => false,
        ];
    }

    /**
     * Format the response after successfully performing the action
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    private function formatSuccessResponse(ServiceBase $api, array $args)
    {
        $data = [];
        $childActivitiesCount = $args['childActivitiesCount'] ?? null;

        if (empty($childActivitiesCount) || $childActivitiesCount <= 1) {
            $data = $this->widgetData($api, $args);
        }

        return [
            'isValid' => true,
            'isChildReadOnly' => isset($args['is_child_read_only']) ? $args['is_child_read_only'] : '',
            'isSelfReadOnly' => isset($args['self_read_only_activity']) ? $args['self_read_only_activity'] : '',
            'isActivityChangeNotAllowed' => isset($args['activity_change_not_allowed']) ? $args['activity_change_not_allowed'] : '',
            'isValidParent' => isset($args['is_valid_parent']) ? $args['is_valid_parent'] : '',
            'data' => $data,
        ];
    }

    /**
     * Set the archived status of current journey to true
     *
     * @param ServiceBase $api
     * @param array $args
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     * @throws JourneyNotCompletedException
     */
    public function archive(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $this->checkLicense();

        /** @var DRI_Workflow $journey */
        $journey = $this->loadBean($api, $args);

        $journey->archive();
    }

    /**
     * Set the archived status of current journey to false
     *
     * @param ServiceBase $api
     * @param array $args
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function unarchive(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $this->checkLicense();

        /** @var DRI_Workflow $journey */
        $journey = $this->loadBean($api, $args);
        $journey->archived = false;

        $journey->save();
    }

    /**
     * Provide any incomplete journey or first journey
     *
     * @param DRI_Workflow[] $journeys
     * @return DRI_Workflow | boolean
     * @throws SugarApiExceptionNotFound
     */
    private function getChartJourney(array $journeys)
    {
        if (safeCount($journeys) === 0) {
            return false;
        }

        foreach ($journeys as $journey) {
            if ($journey->state !== DRI_Workflow::STATE_COMPLETED) {
                return $journey;
            }
        }

        $journey = array_shift($journeys);

        if (!($journey instanceof DRI_Workflow)) {
            throw new SugarApiExceptionNotFound();
        }

        return $journey;
    }

    /**
     * Check Sugar Automate license
     *
     * @throws InvalidLicenseException
     */
    private function checkLicense()
    {
        if (!hasAutomateLicense()) {
            throw new CJException\InvalidLicenseException('Invalid License');
        }
    }

    /**
     * Start Smart Guide template on parent module
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function start(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'template_id']);
        $this->checkLicense();

        $bean = $this->loadBean($api, $args);

        $GLOBALS['log']->info(
            "Starting Smart Guide template {$args['template_id']} on parent {$args['module']}:{$args['record']}"
        );

        $journey = DRI_Workflow::start($bean, $args['template_id']);

        return [
            'parentData' => $this->formatBean($api, $args, $bean),
            'journeyId' => $journey->id,
        ];
    }

    /**
     * Format Stage bean fields according to stageFieldsForBeanPopulate array
     *
     * @param ServiceBase $api
     * @param array $args
     * @param \DRI_SubWorkflow $stage
     * @return array
     */
    protected function formatStage(ServiceBase $api, array $args, $stage)
    {
        $data = $this->formatBean($api, ['fields' => self::$stageFieldsForBeanPopulate], $stage);

        $data['progress'] = round((float)$data['progress'] * 100);
        $data['activities'] = $this->getActivitiesOfStage($stage, $api, $args);
        $data['forms'] = $this->getFormsOfStage($stage, $api, $args);

        return $data;
    }

    /**
     * Get related activities of a stage
     *
     * @param \DRI_SubWorkflow $stage
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    private function getActivitiesOfStage($stage, $api, $args)
    {
        $activities = [];

        foreach ($stage->getActivities() as $activity) {
            if ($activity->ACLAccess('view')) {
                $activities[] = $this->formatActivity($api, $args, $activity);
            }
        }

        return $activities;
    }

    /**
     * Get related forms of a stage
     *
     * @param \DRI_SubWorkflow $stage
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    private function getFormsOfStage($stage, $api, $args)
    {
        $forms = [];

        if ($stage->hasTemplate()) {
            foreach (CheckAndPerformRSA::getForms($stage) as $form) {
                $forms[] = $this->formatBean($api, $args, $form);
            }
        }

        return $forms;
    }

    /**
     * Get related forms of a journey
     *
     * @param DRI_Workflow[] $journey
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    private function getFormsOfJourney($journey, $api, $args)
    {
        $forms = [];

        foreach (CheckAndPerformRSA::getForms($journey) as $form) {
            $forms[] = $this->formatBean($api, $args, $form);
        }

        return $forms;
    }

    /**
     * Format Activity bean fields according to activityFieldsForBeanPopulate array
     *
     * @param ServiceBase $api
     * @param array $args
     * @param \SugarBean $activity
     * @return array
     */
    protected function formatActivity(ServiceBase $api, array $args, \SugarBean $activity)
    {
        $fields = self::$activityFieldsForBeanPopulate;

        if ($activity instanceof Task) {
            $fields[] = 'date_due';
        } elseif ($activity instanceof Call) {
            $fields[] = 'date_start';
            $fields[] = 'date_end';
        } elseif ($activity instanceof Meeting) {
            $fields[] = 'date_start';
        }

        $data = $this->formatBean($api, ['fields' => $fields], $activity);

        $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);
        $data['is_status_readonly'] = $activityHandler->isStatusReadOnly($activity);

        $data['customer_journey_progress'] = round($data['customer_journey_progress'] * 100);
        $data['cj_momentum_ratio'] = round($data['cj_momentum_ratio'] * 100);


        $data['blocked_by'] = $activityHandler->getBlockedByActivityIds($activity);
        $data['blocked_by_stages'] = $activityHandler->getNotCompletedBlockedByStageIds($activity);

        if ($activityHandler->hasParent($activity)) {
            $parent = $activityHandler->getParent($activity);

            if ($parent) {
                $parentHandler = ActivityHandlerFactory::factory($parent->module_dir);

                if ($parentHandler->isBlocked($parent)) {
                    $data['blocked_by'] = array_merge(
                        $data['blocked_by'],
                        $parentHandler->getBlockedByActivityIds($parent)
                    );
                    $data['blocked_by'] = array_unique($data['blocked_by']);
                }

                if ($parentHandler->isBlockedByStage($parent)) {
                    $data['blocked_by_stages'] = array_merge(
                        $data['blocked_by_stages'],
                        $parentHandler->getNotCompletedBlockedByStageIds($parent)
                    );
                    $data['blocked_by_stages'] = array_unique($data['blocked_by_stages']);
                }
            }
        }

        if (!empty($data['assigned_user_id'])) {
            $user = BeanFactory::retrieveBean('Users', $data['assigned_user_id']);

            if ($user) {
                $userData = $this->formatBean($api, $args, $user);
                $data['assigned_user'] = $userData;
            }
        }

        if ($activityHandler->isParent($activity)) {
            $data['children'] = [];

            foreach ($activityHandler->getChildren($activity) as $child) {
                $data['children'][] = $this->formatActivity($api, $args, $child);
            }
        }

        $data['forms'] = [];

        if ($activityHandler->hasActivityTemplate($activity)) {
            foreach ($activityHandler->getForms($activity) as $form) {
                $data['forms'][] = $this->formatBean($api, $args, $form);
            }
        }

        return $data;
    }

    /**
     * Set Activty and its sub activities status to Not Applicable
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array|null
     * @throws SugarQueryException
     * @throws NotFoundException
     * @throws ParentNotFoundException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     */
    public function notApplicableSubActivities(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['activity_module', 'activity_id']);

        $response = $this->validateActivities($args['activities'], $args['fieldsToValidate'], 'not_applicable');

        if (!empty($response)) {
            return $response;
        }

        $parentActivity = BeanFactory::retrieveBean($args['activity_module'], $args['activity_id']);

        if (!empty($parentActivity)) {
            $canceller = new Journey\Canceller();

            $response = $canceller->notApplicableActivity($parentActivity);
            $args = array_merge($args, $response);
        }

        return $this->formatSuccessResponse($api, $args);
    }

    /**
     * Check whether the updatedStatus of an activity is a valid status or not
     *
     * @param $activity_module
     * @param $updatedStatus
     * @return boolean
     */
    private function checkValidUpdatedActivityStatus($activity_module, $updatedStatus)
    {
        try {
            $activityHandler = ActivityHandlerFactory::factory($activity_module);

            return $activityHandler->isValidStatus($activity_module, $updatedStatus);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Set status, update start and end dates of the specific activity
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array|null
     * @throws SugarApiException
     * @throws SugarApiExceptionInvalidParameter
     */
    public function updateActivityState(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['status', 'activity_id', 'activity_module']);

        $status = $args['status'];
        $parentActivity = $args['parentActivity'];
        $activities = $args['activities'];
        $fieldsToValidate = $args['fieldsToValidate'];
        $response = [];

        // while completing a sub activity validate formula for
        // current activity, sibling activities and parent activity
        if (isset($activities)) {
            $statusType = safeCount($activities) > 1 ? 'complete' : $status;
            $response = $this->validateActivities($activities, $fieldsToValidate, $statusType);

            if (!empty($response)) {
                return $response;
            }

            // validate the parent activity formula with complete status
            if (isset($parentActivity)) {
                $response = $this->validateActivities($parentActivity, $fieldsToValidate, 'complete');

                if (!empty($response)) {
                    return $response;
                }
            }
        }

        $response = $this->validateAndUpdateActivityStatus(
            'DRI_Workflows',
            $args['activity_id'],
            $args['activity_module'],
            $args['status'],
            $args['targetItem']
        );

        if (!empty($response)) {
            $args = array_merge($args, $response);
        }
        return $this->formatSuccessResponse($api, $args);
    }

    /**
     * Set start and completion of specific activity
     *
     * @param \SugarBean $bean
     * @param string $status
     */
    public function setActivityStartAndEndDates($bean, $status)
    {
        if ($bean->getModuleName() !== 'Tasks') {
            return;
        }

        $activityHandler = ActivityHandlerFactory::factory($bean->module_dir);

        if ($status === $activityHandler->getInProgressStatus($bean)) {
            $bean->cj_activity_start_date = $GLOBALS['timedate']->nowDb();
        }
        if ($status === $activityHandler->getCompletedStatus($bean)) {
            $bean->cj_activity_completion_date = $GLOBALS['timedate']->nowDb();
        }
    }

   /**
     * Check whether the updatedStatus of an activity is valid and if yes then update activity
     *
     * @param string $activityID
     * @param string $activityModule
     * @param string $updatedActivityStatus
     * @param string $targetModuleName
     * @param mixed $targetItem
     * @throws SugarApiException
     * @throws SugarApiExceptionInvalidParameter
     */
    public function validateAndUpdateActivityStatus(
        string $targetModuleName,
        string $activityID,
        string $activityModule,
        string $updatedActivityStatus,
        $targetItem
    ) {
        $response = [];
        $response['activity_change_not_allowed'] = false;
        $response['is_valid_parent'] = true;
        if ($this->checkValidUpdatedActivityStatus($activityModule, $updatedActivityStatus)) {
            $bean = BeanFactory::retrieveBean($activityModule, $activityID);

            if (isset($targetItem)) {
                $targetBean = BeanFactory::retrieveBean($targetItem['module'], $targetItem['id']);

                $activityHelper = ActivityHandlerFactory::factory($targetItem['module']);
                $targetBean->processing_smart_guide = true;
                $response['is_valid_parent'] = $activityHelper->validateParent($targetBean, $updatedActivityStatus);
            }

            if (!empty($bean) && $response['is_valid_parent']) {
                $activityHandler = ActivityHandlerFactory::factory($activityModule);
                if (!$activityHandler->isParent($bean) && $activityHandler->isStatusReadOnly($bean)) {
                    $response['is_child_read_only'] = true;
                } else {
                    $oldStatus = $bean->status;
                    $bean->status = $updatedActivityStatus;
                    (new ActivityDatesHelper())->setActivityStartAndEndDates($bean, $updatedActivityStatus);
                    if ($targetModuleName === 'dri_workflows') {
                        $bean->customer_journey_blocked_by = '';
                        $bean->cj_blocked_by_stages = '';
                    }
                    $activityHooksHelper = new ActivityHooksHelper();
                    $activityHooksHelper->validate($bean);
                    $response['activity_change_not_allowed'] = ActivityHooksHelper::getAllowedByError();
                    ActivityHooksHelper::resetAllowedByError();
                    if (!$response['activity_change_not_allowed']) {
                        $bean->save();
                    } else {
                        $bean->status = $oldStatus;
                    }
                }
            }
        } else {
            throw new \SugarApiExceptionInvalidParameter();
        }
        return $response;
    }

    /**
     * Create Customer Journey Relationships for standard modules present in the config
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    public function createModuleRelationships(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['repair']);

        return $this->createStandardModuleRelationships($api, $args);
    }

    /**
     * Create Customer Journey Relationships for standard modules present in the config
     *
     * @param ServiceBase $api
     * @param array $args
     * @return boolean
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    private function createStandardModuleRelationships($api, $args)
    {
        $configuratorObj = new Configurator();
        $configuratorObj->loadConfig();
        $vardefs = [];
        $result = false;

        if (!empty($configuratorObj->config['customer_journey']) &&
            !empty($configuratorObj->config['customer_journey']['enabled_modules'])) {
            $enabledModules = explode(',', $configuratorObj->config['customer_journey']['enabled_modules']);
            if (!empty($enabledModules)) {
                foreach ($enabledModules as $enabledModule) {
                    $moduleBean = BeanFactory::newBean($enabledModule);
                    $objectName = $moduleBean->object_name;
                    $moduleName = $moduleBean->module_dir;
                    $tableName = $moduleBean->table_name;
                    $beanName = $objectName;
                    $idName = strtolower($objectName) . '_id';
                    $name = strtolower($objectName) . '_name';
                    $linkName = strtolower($objectName) . '_link';
                    $labelName = 'LBL_' . strtoupper($objectName);
                    $enabled = true;
                    $relationship = 'dri_workflow_' . strtolower($moduleName);
                    $vardefs['fields'][$idName] = [
                        'name' => $idName,
                        'vname' => $labelName,
                        'required' => false,
                        'reportable' => false,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'id',
                    ];
                    $vardefs['fields'][$name] = [
                        'name' => $name,
                        'vname' => $labelName,
                        'required' => false,
                        'reportable' => false,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'source' => 'non-db',
                        'type' => 'relate',
                        'rname' => 'name',
                        'table' => $tableName,
                        'id_name' => $idName,
                        'sort_on' => 'name',
                        'module' => $moduleName,
                        'link' => $linkName,
                        'customer_journey_parent' => [
                            'enabled' => $enabled,
                            'rank' => 10,
                        ],
                    ];
                    $vardefs['fields'][$linkName] = [
                        'name' => $linkName,
                        'vname' => $labelName,
                        'source' => 'non-db',
                        'type' => 'link',
                        'side' => 'right',
                        'bean_name' => $beanName,
                        'relationship' => $relationship,
                        'module' => $moduleName,
                    ];
                    $vardefs['relationships'][$relationship] = [
                        'relationship_type' => 'one-to-many',
                        'lhs_key' => 'id',
                        'lhs_module' => $moduleName,
                        'lhs_table' => $tableName,
                        'rhs_module' => 'DRI_Workflows',
                        'rhs_table' => 'dri_workflows',
                        'rhs_key' => $idName,
                    ];
                    $vardefs['indices']['idx_cj_jry_' . $idName] = [
                        'name' => 'idx_cj_jry_' . $idName,
                        'type' => 'index',
                        'fields' => [
                            $idName,
                        ],
                    ];
                    $cjFilePath = 'custom/Extension/modules/' . $moduleName . '/Ext/Vardefs/customer_journey_parent.php';
                    $result = sugar_touch($cjFilePath);

                    if (!$result) {
                        $GLOBALS['log']->fatal("File $cjFilePath cannot be touched");
                        return;
                    }

                    if (is_file($cjFilePath) && !is_writable($cjFilePath)) {
                        $GLOBALS['log']->fatal("$cjFilePath: Not Writeable");
                        return;
                    }

                    $write = "<?php\n" .
                        '// created: ' . date('Y-m-d H:i:s') . "\n" .
                        "VardefManager::createVardef('$moduleName', '$objectName', [
                                'customer_journey_parent',
                        ]);";
                    //write this meta to the vardefs file
                    $fileWriteSuccess = sugar_file_put_contents_atomic($cjFilePath, $write);
                }
                $filePath = 'custom/include/SugarObjects/implements/customer_journey_enabled_modules/vardefs.php';
                $result = sugar_touch($filePath);

                if (!$result) {
                    $GLOBALS['log']->fatal("File $filePath cannot be touched");
                    return;
                }

                $is_writable = false;
                if (is_file($filePath)) {
                    $is_writable = is_writable($filePath);
                }
                if (!$is_writable) {
                    $GLOBALS['log']->fatal("$filePath: Not Writeable");
                    return;
                }

                //write this meta to the vardefs file
                $fileWriteSuccess = write_array_to_file(
                    'vardefs',
                    $vardefs,
                    $filePath
                );

                if ($fileWriteSuccess) {
                    if ($args['repair']) {
                        MetaDataManager::refreshModulesCache($enabledModules);
                        MetaDataManager::refreshSectionCache('config');

                        global $current_user;
                        $current_user = new User();
                        $current_user->getSystemUser();

                        global $moduleList;
                        $repair = new RepairAndClear();
                        $_REQUEST['execute'] = true;
                        $repair->repairAndClearAll(['clearAll'], $moduleList, true, false, '');
                        include 'modules/Administration/RebuildRelationship.php';
                    }
                    $result = true;
                } else {
                    $GLOBALS['log']->fatal("$filePath: File Write Failed");
                }
            }
        }
        return $result;
    }

    /**
     * Provide grace period remaining days
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getGracePeriodRemainingDays(ServiceBase $api, array $args)
    {
        // User must have Automate license to access this end-point
        ConfigurationManager::ensureAutomateUser();

        global $sugar_config;
        $response = [];

        if (isset($sugar_config['customer_journey']) &&
            $sugar_config['customer_journey']['grace_period_start_date'] !== '0000-00-00') {
            $daysDiff = $this->getDiffInNumberOfDays($sugar_config['customer_journey']['grace_period_start_date']);

            $remainingDays = $sugar_config['customer_journey']['grace_period_days'] - $daysDiff;
            $response['remaining_days'] = $remainingDays > 0 ? $remainingDays : 0;
        }

        return $response;
    }

    /**
     * Get Difference between grace period start date and current date
     *
     * @param string
     * @return int
     */
    public function getDiffInNumberOfDays($startDate)
    {
        $timeDate = \TimeDate::getInstance();

        $now = $timeDate->getNow();
        $gracePeriodStartDate = $timeDate->fromString($startDate);
        $interval = $gracePeriodStartDate->diff($now);

        return $interval->d;
    }

    /**
     * Remove Custom Journey Parent Vardef code from Journey disabled Modules
     *
     * @param array
     */
    public function removeCJParentVardef($removedModules)
    {
        foreach ($removedModules as $module) {
            $cjFilePath = 'custom/Extension/modules/' . $module . '/Ext/Vardefs/customer_journey_parent.php';
            if (is_file($cjFilePath) && !is_writable($cjFilePath)) {
                $GLOBALS['log']->fatal("$cjFilePath: Not Writeable");
                return;
            }

            $moduleBean = BeanFactory::newBean($module);
            $objectName = $moduleBean->object_name;
            $tableName = $moduleBean->table_name;

            $relName = strtolower($objectName). '_dri_workflow_templates';
            $idxName = 'idx_'.trim(substr(strtolower($tableName), 0, 17)). '_cjtpl_id';
            $write = "<?php"  . PHP_EOL .
                '// created: ' . date('Y-m-d H:i:s') . PHP_EOL .
                'unset($dictionary[\'' . $objectName . '\'][\'fields\'][\'dri_workflows\']);' . PHP_EOL .
                'unset($dictionary[\'' . $objectName . '\'][\'fields\'][\'dri_workflow_template_name\']);' . PHP_EOL .
                'unset($dictionary[\'' . $objectName . '\'][\'fields\'][\'dri_workflow_template_link\']);' . PHP_EOL .
                'unset($dictionary[\'' . $objectName . '\'][\'relationships\'][\'' . $relName . '\']);' . PHP_EOL .
                'unset($dictionary[\'' . $objectName . '\'][\'indices\'][\'' . $idxName . '\']);';

            if (!in_array($module, ['Meetings', 'Calls', 'Tasks'])) {
                //write this meta to the vardefs file
                $write .= PHP_EOL .
                    'unset($dictionary[\'' . $objectName . '\'][\'fields\'][\'dri_workflow_template_id\']);';
            }
            $fileWriteSuccess = sugar_file_put_contents_atomic($cjFilePath, $write);
        }
    }

    /**
     * Delete journey or stage according to the provide module and id
     *
     * @param ServiceBase $api
     * @param array $args
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    public function deleteStageOrJourney($api, $args)
    {
        $this->requireArgs($args, ['id', 'moduleName']);

        $this->checkLicense();

        $moduleName = $args['moduleName'];
        $bean = BeanFactory::retrieveBean($moduleName, $args['id']);

        $bean->mark_deleted($bean->id);

        if ($moduleName === 'DRI_SubWorkflows') {
            return $this->formatSuccessResponse($api, $args);
        } elseif ($moduleName === 'DRI_Workflows') {
            return $this->getNextJourney($moduleName, $api, $args);
        }
    }

    /**
     * Gets next journey id from DB
     *
     * @param array $args
     * @return string
     */
    private function getJourneyFromDB($args)
    {
        $parentModuleBean = \BeanFactory::getBean($args['parentModule']);
        $parentIDFieldName = strtolower($parentModuleBean->object_name) . '_id';
        $workflowBean = \BeanFactory::getBean('DRI_Workflows');
        $query = new \SugarQuery();

        $query->select(['id']);
        $query->from($workflowBean)->where()
            ->equals($parentIDFieldName, $args['parentModelId'])
            ->notIn('id', $args['currentJourneys']);
        if ($args['state'] === 'active') {
            $query->where()->equals('archived', false);
        } elseif ($args['state'] === 'archived') {
            $query->where()->equals('archived', true);
        }
        $query->orderBy('date_entered', 'ASC');

        // since one journey is deleted at a time so we will get one next journey other than current journey
        return $query->getOne();
    }

    /**
     * Get next Journey in the queue, if already below limit or no next journey in queue return null
     *
     * @param string $moduleName
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array|null
     */
    public function getNextJourney($moduleName, $api, $args)
    {
        $configuratorObj = new Configurator();
        $configuratorObj->loadConfig();
        $configCount = $configuratorObj->config['list_max_entries_per_page'];

        // if already below max entries then do not run query
        if (sizeof($args['currentJourneys']) < $configCount) {
            return;
        }

        $nextjourneyId = $this->getJourneyFromDB($args);

        if (!empty($nextjourneyId)) {
            $args['module'] = $moduleName;
            $args['record'] = $nextjourneyId;
            return $this->widgetData($api, $args);
        }

        return;
    }

    /**
     * Provide the count of active smart guides related to a particular record
     *
     * @param ServiceBase $api
     * @param array $args
     * @return int
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    public function getActiveSmartGuidesCount(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        $parentModuleBean = \BeanFactory::getBean($args['module']);
        $parentIDFieldName = strtolower($parentModuleBean->object_name) . '_id';
        $workflowBean = \BeanFactory::getBean('DRI_Workflows');
        $query = new \SugarQuery();

        $query->select(['id']);
        $query->from($workflowBean)->where()
            ->equals($parentIDFieldName, $args['record'])
            ->equals('state', 'in_progress')
            ->equals('archived', false);

        return safeCount($query->execute());
    }
}
