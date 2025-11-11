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

/**
 * Exports a Business Process Management Project, including the Process
 * design and all related elements for it
 *
 * @package PMSE
 * @codeCoverageIgnore
 */
class PMSEProjectExporter extends PMSEExporter
{
    /**
     * @inheritDoc
     */
    protected $beanModule = 'pmse_Project';

    /**
     * @inheritDoc
     */
    protected $uid = 'id';

    /**
     * @inheritDoc
     */
    protected $name = 'name';

    /**
     * @inheritDoc
     */
    protected $extension = 'bpm';

    /**
     * Method to retrieve a record of the database to export.
     * @param array $args
     * @return array
     */
    public function getProject(array $args)
    {
        $bean = $this->getBean();
        $bean->retrieve($args['id']);

        $project = [];

        if (($data = $this->getBeanData($bean)) !== []) {
            $project = PMSEEngineUtils::unsetCommonFields($data, ['name', 'description', 'assigned_user_id']);
            $project['process'] = $this->getProjectProcess($bean->id);
            $project['diagram'] = $this->getProjectDiagram($bean->id);
            $project['definition'] = $this->getProcessDefinition($bean->id);
            $project['dynaforms'] = $this->getDynaforms($bean->id);

            return ['metadata' => $this->getMetadata(), 'project' => $project];
        } else {
            return ['error' => true];
        }
    }

    /**
     * Get the project process data
     * @return array
     */
    public function getProjectProcess($prjID)
    {
        $processBean = BeanFactory::newBean('pmse_BpmnProcess');
        $processData = [];
        $processBean->retrieve_by_string_fields(['prj_id' => $prjID]);
        if (!empty($processBean->fetched_row)) {
            $processData = PMSEEngineUtils::unsetCommonFields($processBean->fetched_row, ['name', 'description']);
            $processData = PMSEEngineUtils::sanitizeKeyFields($processData);
        }
        return $processData;
    }

    /**
     * Get the project Diagram data with a determined Project Id
     * @param string $prjID
     * @return array
     */
    public function getProjectDiagram($prjID)
    {
        $diagramBean = BeanFactory::newBean('pmse_BpmnDiagram'); //new BpmnDiagram();
        $diagramData = [];

        if ($diagramBean->retrieve_by_string_fields(['prj_id' => $prjID])) {
            $diagramData = PMSEEngineUtils::unsetCommonFields($diagramBean->fetched_row, ['name', 'description', 'assigned_user_id']);

            //Get Activities
            $diagramData['activities'] = $this->getElementData($prjID, 'bpmnActivity');

            //Get Events
            $diagramData['events'] = $this->getElementData($prjID, 'bpmnEvent');

            //Get Gateways
            $diagramData['gateways'] = $this->getElementData($prjID, 'bpmnGateway');

            //Get Artifacts
            $diagramData['artifacts'] = $this->getArtifactData($prjID);

            //Get Flows
            $diagramData['flows'] = $this->getFlowData($prjID);
        }
        return $diagramData;
    }

    private function getElementData($prjID, $element)
    {
        $definition = $this->getElementDefinitions($element);
        if (empty($definition)) {
            return [];
        }

        $activityBean = BeanFactory::newBean($definition['element']['module']);

        $q = new SugarQuery();
        $q->from($activityBean, ['add_deleted' => true]);
        $q->distinct(false);
        $fields = $this->getFields($definition['element']['module'], ['id', 'name']);

        //INNER JOIN BOUND TABLE
        $q->joinTable('pmse_bpmn_bound', ['alias' => 'bound', 'joinType' => 'INNER', 'linkingTable' => true])
            ->on()
            ->equalsField('id', 'bound.bou_element')
            ->equals('bound.deleted', 0);
        $fields = array_merge($fields, $this->getFields('pmse_BpmnBound', [], 'bound'));

        //INNER JOIN DEFINITION TABLE
        $q->joinTable($definition['definition']['table'], ['alias' => 'def', 'joinType' => 'INNER', 'linkingTable' => true])
            ->on()
            ->equalsField('id', 'def.id')
            ->equals('def.deleted', 0);
        $fields = array_merge($fields, $this->getFields($definition['definition']['module'], [], 'def'));

        $q->where()
            ->equals('prj_id', $prjID)
            ->equals('bound.bou_element_type', $element);

        $q->select($fields);

        return $q->execute();
    }

    private function getArtifactData($prjID)
    {
        $artifactBean = BeanFactory::newBean('pmse_BpmnArtifact');

        $q = new SugarQuery();
        $q->from($artifactBean, ['add_deleted' => true]);
        $q->distinct(false);
        $fields = $this->getFields('pmse_BpmnArtifact', ['id', 'name']);

        //INNER JOIN BOUND TABLE
        $q->joinTable('pmse_bpmn_bound', ['alias' => 'bound', 'joinType' => 'INNER', 'linkingTable' => true])
            ->on()
            ->equalsField('id', 'bound.bou_element')
            ->equals('bound.deleted', 0);
        $fields = array_merge($fields, $this->getFields('pmse_BpmnBound', [], 'bound'));

        $q->where()
            ->equals('prj_id', $prjID)
            ->equals('bound.bou_element_type', 'bpmnArtifact');

        $q->select($fields);

        return $q->execute();
    }

    private function getFlowData($prjID)
    {
        $flowBean = BeanFactory::newBean('pmse_BpmnFlow');

        $q = new SugarQuery();
        $q->from($flowBean, ['add_deleted' => true]);
        $q->distinct(false);
        $fields = $this->getFields('pmse_BpmnFlow', ['id', 'name']);

        $q->where()
            ->equals('prj_id', $prjID);

        $q->select($fields);

        return $q->execute();
    }

    /**
     * Get the Process Definition data
     * @return array
     */
    public function getProcessDefinition($prjID)
    {
        $definitionBean = BeanFactory::newBean('pmse_BpmProcessDefinition');
        $definitionData = [];
        $definitionBean->retrieve_by_string_fields(['prj_id' => $prjID]);
        if (!empty($definitionBean->fetched_row)) {
            $definitionData = PMSEEngineUtils::unsetCommonFields(
                $definitionBean->fetched_row,
                ['name', 'description']
            );
            $definitionData = PMSEEngineUtils::sanitizeKeyFields($definitionBean->fetched_row);
        }
        return $definitionData;
    }

    /**
     * Get the object list of dyanform records
     * @return array
     */
    public function getDynaForms($prjID)
    {
        $dynaFormBean = BeanFactory::newBean('pmse_BpmDynaForm');

        $q = new SugarQuery();
        $q->from($dynaFormBean, ['add_deleted' => true]);
        $q->distinct(false);
        $fields = $this->getFields('pmse_BpmDynaForm', ['id']);

        $q->where()
            ->equals('prj_id', $prjID);

        $q->select($fields);

        return $q->execute();
    }

    /**
     * Additional processing to the Business Rules Data.
     * @param array $conditionArray
     * @return array
     */
    public function processBusinessRulesData($conditionArray = [])
    {
        if (is_array($conditionArray)) {
            foreach ($conditionArray as $key => $value) {
                if (isset($value->expType) && $value->expType == 'BUSINESS_RULES') {
                    $activityBeam = BeanFactory::getBean('pmse_BpmnActivity', $value->expField);
                    $conditionArray[$key]->expField = $activityBeam->act_uid;
                }
            }
        }
        return $conditionArray;
    }

    private function getFields($module, $except = [], $alias = '')
    {
        $result = [];
        $rows = PMSEEngineUtils::getAllFieldsBean($module);
        $rows = PMSEEngineUtils::unsetCommonFields($rows, $except, true);
        if (!empty($alias)) {
            foreach ($rows as $value) {
                $result[] = [$alias . '.' . $value, $alias . '_' . $value];
            }
        } else {
            $result = $rows;
        }
        return $result;
    }

    private function getElementDefinitions($element)
    {
        $result = [];
        switch ($element) {
            case 'bpmnActivity':
                $result = [
                    'element' => [
                        'module' => 'pmse_BpmnActivity',
                        'table' => 'pmse_bpmn_activity',
                    ],
                    'definition' => [
                        'module' => 'pmse_BpmActivityDefinition',
                        'table' => 'pmse_bpm_activity_definition',
                    ],
                ];
                break;
            case 'bpmnEvent':
                $result = [
                    'element' => [
                        'module' => 'pmse_BpmnEvent',
                        'table' => 'pmse_bpmn_event',
                    ],
                    'definition' => [
                        'module' => 'pmse_BpmEventDefinition',
                        'table' => 'pmse_bpm_event_definition',
                    ],
                ];
                break;
            case 'bpmnGateway':
                $result = [
                    'element' => [
                        'module' => 'pmse_BpmnGateway',
                        'table' => 'pmse_bpmn_gateway',
                    ],
                    'definition' => [
                        'module' => 'pmse_BpmGatewayDefinition',
                        'table' => 'pmse_bpm_gateway_definition',
                    ],
                ];
                break;
        }
        return $result;
    }
}
