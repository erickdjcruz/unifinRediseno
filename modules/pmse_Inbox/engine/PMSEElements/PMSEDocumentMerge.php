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

use Sugarcrm\Sugarcrm\DocumentMerge\Client\Constants\MergeType;
use Sugarcrm\Sugarcrm\Security\Subject\ApiClient\Rest;

class PMSEDocumentMerge extends PMSEScriptTask
{
    /**
     * This method prepares the response of the current element based on the
     * $bean object and the $flowData, an external action such as
     * ROUTE or ADHOC_REASSIGN could be also processed.
     *
     * This method probably should be override for each new element, but it's
     * not mandatory. However the response structure always must pass using
     * the 'prepareResponse' Method.
     *
     * As defined in the example:
     *
     * $response['route_action'] = 'ROUTE'; //The action that should process the Router
     * $response['flow_action'] = 'CREATE'; //The record action that should process the router
     * $response['flow_data'] = $flowData; //The current flowData
     * $response['flow_filters'] = array('first_id', 'second_id'); //This attribute is used to filter the execution of the following elements
     * $response['flow_id'] = $flowData['id']; // The flowData id if present
     *
     * @param array $flowData
     * @param null $bean
     * @param string $externalAction
     * @param array $arguments
     * @return array
     */
    public function run($flowData, $bean = null, $externalAction = '', $arguments = [])
    {
        // We need a bean for this to work and the instance needs a Sell/Serve license as well
        if ($this->isRunnable($bean)) {
            try {
                $definitionBean = $this->getDefinitionBean($flowData);
                $data = json_decode($definitionBean->act_fields);

                // make sure the event definition bean exists
                $this->buildEvnDefBean($flowData, $data);

                $this->merge($data, $bean, $flowData);
            } catch (PMSEExpressionEvaluationException $e) {
                throw new PMSEElementException('Document Merge: ' . $e, $flowData, $this);
            }
        }

        $flowAction = $externalAction === 'RESUME_EXECUTION' ? 'UPDATE' : 'CREATE';
        return $this->prepareResponse($flowData, 'ROUTE', $flowAction);
    }

    /**
     * Determines if this particular element is runnable or not.
     * @param SugarBean|null $bean
     * @return boolean
     */
    protected function isRunnable(SugarBean $bean = null): bool
    {
        return $bean instanceof SugarBean;
    }

    /**
     * Retrieves the definition bean
     *
     * @param array $flowData
     * @return pmse_BpmActivityDefinition
     */
    public function getDefinitionBean($flowData)
    {
        $bpmnElement = $this->retrieveDefinitionData($flowData['bpmn_id']);
        $definitionBean = $this->caseFlowHandler->retrieveBean('pmse_BpmActivityDefinition', $bpmnElement['id']);
        return $definitionBean;
    }

    /**
     * Perform the document merge
     *
     * @param object $data
     * @param SugarBean $bean
     * @param array $flowData
     */
    public function merge($data, \SugarBean $bean, $flowData): void
    {
        $convert = $data->act_convert_to_pdf ?? false;
        $templateId = $data->act_document_template->value;
        $templateName = $data->act_document_template->text;
        $recordId = $bean->id;
        $recordModule = $bean->module_dir;

        $mergeType = $this->getMergeType($convert, $templateId);
        if ($mergeType) {
            $docMerge = new DocumentMergeApi();
            $api = new \RestService();
            $docMerge->merge($api, [
                'mergeType' => $mergeType,
                'templateId' => $templateId,
                'templateName' => $templateName,
                'useRevision' => true,
                'recordId' => $recordId,
                'recordModule' => $recordModule,
                'flowData' => json_encode($flowData),
            ]);
        } else {
            throw new PMSEElementException('Document Merge: No merge type', $mergeType, $this);
        }
    }

    /**
     * It retrieves the template filename and identifies the mergeTye
     *
     * @param bool convert
     * @param string templateId
     * @return string
     * @throws SugarException
     */
    protected function getMergeType(bool $convert, string $templateId): ?string
    {
        $template = \BeanFactory::retrieveBean('DocumentTemplates', $templateId);
        if (!$template) {
            throw new \SugarException('Merge: Template not found', null, 'DocumentMerges');
        }

        $filename = $template->filename;
        $labels = $template->label_merging;
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $mergeType = null;

        switch ($extension) {
            case 'docx':
                if ($convert && $labels) {
                    $mergeType = MergeType::LabelsGenerateConvert;
                    break;
                }
                if ($convert) {
                    $mergeType = MergeType::Convert;
                    break;
                }

                $mergeType = MergeType::Merge;
                break;
            case 'xlsx':
                if ($convert) {
                    $mergeType = MergeType::SpreadsheetConvert;
                    break;
                }

                $mergeType = MergeType::Spreadsheet;
                break;
            case 'pptx':
                if ($convert) {
                    $mergeType = MergeType::PresentationConvert;
                    break;
                }

                $mergeType = MergeType::Presentation;
                break;
        }

        return $mergeType;
    }

    /**
     * Creates the definition bean if it not exists
     *
     * @param array $flowData
     * @param object $actFields
     */
    public function buildEvnDefBean(?array $flowData, $actFields)
    {
        $evnDefBean = BeanFactory::retrieveBean('pmse_BpmEventDefinition', $flowData['bpmn_id']);

        if ($evnDefBean instanceof SugarBean) {
            return $this->updateEvnDefBean($evnDefBean, $actFields);
        }

        $evnDefBean = BeanFactory::newBean('pmse_BpmEventDefinition');
        $evnDefBean->id = $flowData['bpmn_id'];
        $evnDefBean->new_with_id = true;
        // check if it has a email template selected and if it should send the email
        if ($actFields->evn_criteria && $actFields->act_send_email) {
            $evnDefBean = $this->updateEvnDefBean($evnDefBean, $actFields);
        }
        return $evnDefBean;
    }

    /**
     * update a event definition bean
     *
     * @param SugarBean $evnDefBean
     * @param object $actFields
     */
    public function updateEvnDefBean($evnDefBean, $actFields)
    {
        $evnDefBean->evn_criteria = $actFields->evn_criteria;

        $actFields->to = json_decode($actFields->to);
        $actFields->cc = json_decode($actFields->cc);
        $actFields->bcc = json_decode($actFields->bcc);

        $evnDefBean->evn_params = json_encode($actFields);
        $evnDefBean->save();

        return $evnDefBean;
    }
}
