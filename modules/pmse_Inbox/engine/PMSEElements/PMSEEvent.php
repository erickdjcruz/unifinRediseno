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
 * Description of PMSEEvent
 *
 */
class PMSEEvent extends PMSEShape
{
    protected $evaluator;
    protected $definitionBean;

    /**
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->evaluator = ProcessManager\Factory::getPMSEObject('PMSEEvaluator');
        $this->definitionBean = BeanFactory::newBean('pmse_BpmEventDefinition');
        parent::__construct();
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     *
     * @param type $expressionEvaluator
     * @codeCoverageIgnore
     */
    public function setEvaluator($expressionEvaluator)
    {
        $this->evaluator = $expressionEvaluator;
    }

    /**
     *
     * @param type $id
     * @return type
     * @codeCoverageIgnore
     */
    protected function retrieveDefinitionData($id)
    {
        $this->definitionBean->retrieve($id);
        return $this->definitionBean->fetched_row;
    }

    /**
     * Checks if the event is after an event based gateway
     * @param type $casId
     * @param type $casPrevious
     * @return boolean
     */
    public function checkIfUsesAnEventBasedGateway($casId, $casPrevious)
    {
        $bean = $this->caseFlowHandler->retrieveBean('pmse_BpmFlow'); //$this->beanFactory->getBean('BpmFlow');
        $where = "cas_id='" . $casId . "' AND cas_index='" . $casPrevious . "'";
        $result = $bean->get_list('', $where);
        $element = array_pop($result['list']);

        $flowBean = $this->caseFlowHandler->retrieveBean('pmse_BpmnFlow');
        $where = "flo_element_dest='" . $element->bpmn_id . "' AND flo_element_dest_type='" . $element->bpmn_type . "'";
        $result = $flowBean->get_list('', $where);
        $flowElement = array_pop($result['list']);

        if ($flowElement->flo_element_origin_type == 'bpmnGateway') {
            $gateway = $this->caseFlowHandler->retrieveBean(
                'pmse_BpmnGateway',
                $flowElement->flo_element_origin
            ); //$this->beanFactory->getBean('BpmnGateway');
            if ($gateway->gat_type === 'EVENTBASED') {
                return true;
            }
        }
        return false;
    }

    /**
     * Check the threads in order to view wich ones are sibblings in order to check the
     * events dependant of an event based gateway.
     * @param type $cas_id
     * @param type $cas_previous
     * @param type $isEventBased
     * @return boolean
     */
    public function checkIfExistEventBased($cas_id, $cas_previous, $isEventBased = false)
    {
        $bean = $this->caseFlowHandler->retrieveBean('pmse_BpmFlow');
        $where = sprintf(
            'cas_id=%s AND cas_index=%s',
            $this->dbHandler->quoted($cas_id),
            $this->dbHandler->quoted($cas_previous)
        );
        $result = $bean->get_list('', $where);
        $flow = array_pop($result['list']);

        $rowThread = $this->dbHandler
            ->getConnection()
            ->executeQuery(
                'SELECT * FROM pmse_bpm_thread WHERE cas_id=? and cas_flow_index=?',
                [$cas_id, $flow->cas_previous]
            )->fetchAssociative();
        $cas_thread_index = $rowThread['cas_thread_index'];
        $cas_thread_parent = $rowThread['cas_thread_parent'];

        if (!empty($cas_thread_parent) && !empty($cas_thread_index)) {
            //select siblings
            if ($isEventBased) {
                $rows = $this->dbHandler
                    ->getConnection()
                    ->executeQuery(
                        'select * from pmse_bpm_thread where cas_id=? and cas_thread_parent=? and cas_thread_index !=?',
                        [$cas_id, $cas_thread_parent, $cas_thread_index]
                    );
                foreach ($rows->iterateAssociative() as $row) {
                    $this->caseFlowHandler->closeThreadByThreadIndex($cas_id, $row['cas_thread_index']);
                    $flowBean = $this->caseFlowHandler->retrieveBean('pmse_BpmFlow');
                    $flowBean->retrieve_by_string_fields([
                        'cas_id' => $cas_id,
                        'cas_previous' => $row['cas_flow_index'],
                    ]);
                    $this->caseFlowHandler->closeFlow($cas_id, $flowBean->cas_index);
                }
            }
            //closing parent
            $this->caseFlowHandler->closeThreadByThreadIndex($cas_id, $rowThread['cas_thread_parent']);
        }
        return true;
    }

    /**
     * Since all evaluations are made into the PMSEPreProcessor.
     * @param type $flowData
     * @param type $bean
     * @return type
     * @deprecated since version pmse2
     * @codeCoverageIgnore
     */
    public function evaluateExpression($flowData, $bean)
    {
        if ($flowData['evn_criteria'] == '' || $flowData['evn_criteria'] == '[]') {
            $resultEvaluation = true;
        } else {
            $resultEvaluation = $this->evaluator->evaluateExpression(trim($flowData['evn_criteria']), $bean);
        }

        return $resultEvaluation;
    }
}
