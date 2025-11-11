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

use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;

class DRIWorkflowTemplatesApi extends SugarApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'getLastStage' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflow_Templates', '?', 'last-stage'],
                'pathVars' => ['module', 'record'],
                'method' => 'getLastStage',
                'shortHelp' => 'Get the last stage for the Smart Guide template',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplatesgetLastStage.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'widgetData' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflow_Templates', '?', 'widget-data'],
                'pathVars' => ['module', 'record'],
                'method' => 'widgetData',
                'shortHelp' => 'Get the data to be loaded for the Smart Guide including stages and activities',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplateswidgetData.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
            'updateActivityOrder' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflow_Templates', '?', 'update-activity-order'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateActivityOrder',
                'shortHelp' => 'Update the sort order of the activities',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplatesupdateActivityOrder.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Get the last stage for the Smart Guide template
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function getLastStage(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        /** @var DRI_Workflow_Template $bean */
        $bean = $this->loadBean($api, $args);

        return $this->formatBean($api, $args, $bean->getLastStage());
    }

    /**
     * Get the data to be loaded for the Smart Guide including stages and activities
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function widgetData(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        $userAccess = hasAutomateLicense();

        /** @var DRI_Workflow_Template $journey */
        $journey = $this->loadBean($api, $args);

        $data = $this->formatBean($api, [], $journey);
        $data['stages'] = [];
        $data['user_access'] = $userAccess;

        $GLOBALS['log']->info(
            "Loading widget data for Smart Guide {$journey->id} for parent {$args['module']}:{$args['record']}"
        );

        if ($userAccess) {
            foreach ($journey->getStageTemplates() as $stage) {
                $data['stages'][] = $this->formatStage($api, $stage);
            }
        }

        return $data;
    }

    /**
     * Formats the activity with the passed parameters
     *
     * @param ServiceBase $api
     * @param DRI_Workflow_Task_Template $activity
     * @return array
     */
    protected function formatActivity(ServiceBase $api, DRI_Workflow_Task_Template $activity)
    {
        $data = $this->formatBean($api, [], $activity);

        $data['forms'] = [];
        $data['blocked_by'] = $activity->getBlockedByIds();
        $data['blocked_by_stages'] = $activity->getBlockedByStageIds();

        if ($activity->is_parent) {
            $data['children'] = [];

            foreach ($activity->getChildren() as $child) {
                $data['children'][] = $this->formatActivity($api, $child);
            }
        }

        $data['forms'] = [];

        foreach ($activity->getForms() as $form) {
            $data['forms'][] = $this->formatBean($api, [], $form);
        }

        return $data;
    }

    /**
     * Formats the stage with the passed parameters
     *
     * @param ServiceBase $api
     * @param DRI_SubWorkflow_Template $stage
     * @return array
     */
    protected function formatStage(ServiceBase $api, DRI_SubWorkflow_Template $stage)
    {
        $data = $this->formatBean($api, [], $stage);

        $data['activities'] = [];
        $data['progress'] = 0;

        foreach ($stage->getActivityTemplates() as $activity) {
            /** @var DRI_Workflow_Task_Template $activity */
            $data['activities'][] = $this->formatActivity($api, $activity);
        }

        return $data;
    }

    /**
     * Update the sort order of the activities
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
    public function updateActivityOrder(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['stage_id']);

        $parentOrder = 1;

        //Iterate the id's of updated activities and set the sort orders
        foreach ($args['activities_ids'] as $id) {
            $childOrder = 1;

            $activity = BeanFactory::retrieveBean('DRI_Workflow_Task_Templates', $id);

            //Update the parent's activity sort order
            $activity->sort_order = $parentOrder;

            $this->updateSortOrder($activity->sort_order, $activity->id, $args['stage_id']);

            if ($activity->is_parent) {
                foreach ($activity->getChildren() as $childTemplate) {
                    //Update the child's activity sort order
                    $childTemplate->sort_order = "{$activity->sort_order}.{$childOrder}";

                    $this->updateSortOrder($childTemplate->sort_order, $childTemplate->id, $args['stage_id']);

                    $childOrder++;
                }
            }
            $parentOrder++;
        }
    }

    /**
     * Update the sort order and stage of Activity Templates When some
     * activity is moved from one stage to another then dri_subworkflow_template_id
     * will change, So there is a need to update stage id as well
     *
     * @param string $sortOrder
     * @param string $id
     * @param string $stage
     */
    private function updateSortOrder($sortOrder, $id, $stage)
    {
        if (empty($sortOrder) || empty($id) || empty($stage)) {
            return;
        }

        $db = DBManagerFactory::getInstance();
        $sql = <<<SQL
                    UPDATE
                        dri_workflow_task_templates
                    SET
                        sort_order = '%s',
                        dri_subworkflow_template_id = '%s'
                    WHERE
                        id = '%s'
SQL;
        $sql = sprintf($sql, $db->quote($sortOrder), $db->quote($stage), $db->quote($id));

        $db->query($sql);
    }
}
