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

class ActionButtonApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'evaluateBPMEmailTemplate' => [
                'reqType' => 'POST',
                'path' => ['actionButton', 'evaluateBPMEmailTemplate'],
                'pathVars' => [''],
                'method' => 'evaluateBPMEmailTemplate',
                'shortHelp' => 'Merges a BPM Email Template and returns the merged data',
                'longHelp' => 'include/api/help/actionbutton_evaluatebpmemailtemplate_post_help.html',
                'minVersion' => '11.13',
            ],
            'evaluateEmailTemplate' => [
                'reqType' => 'POST',
                'path' => ['actionButton', 'evaluateEmailTemplate'],
                'pathVars' => [''],
                'method' => 'evaluateEmailTemplate',
                'shortHelp' => 'Merges a standard Sugar Email Template and returns the merged data',
                'longHelp' => 'include/api/help/actionbutton_evaluateemailtemplate_post_help.html',
                'minVersion' => '11.13',
            ],
            'evaluateExpressions' => [
                'reqType' => 'POST',
                'path' => ['actionButton', 'evaluateExpression'],
                'pathVars' => ['', ''],
                'method' => 'evaluateExpression',
                'shortHelp' => 'Evaluates an Sugar Expression and returns the result',
                'longHelp' => 'include/api/help/actionbutton_evaluateexpression_post_help.html',
                'minVersion' => '11.13',
            ],
            'evaluateCalculatedUrl' => [
                'reqType' => 'POST',
                'path' => ['actionButton', 'evaluateCalculatedUrl'],
                'pathVars' => ['', ''],
                'method' => 'evaluateCalculatedUrl',
                'shortHelp' => 'Evaluates a calculated URL expression and returns the result',
                'longHelp' => 'include/api/help/actionbutton_evaluatecalculatedurl_post_help.html',
                'minVersion' => '11.13',
            ],
        ];
    }

    /**
     * Merges a PMSE Email Template and returns the data.
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     * @throws Doctrine\DBAL\Query\QueryException
     * @throws Doctrine\DBAL\Exception
     * @throws Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws SugarDoctrine\DBAL\Query\QueryException
     */
    public function evaluateBPMEmailTemplate(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, [
            'targetRecordModule',
            'targetRecordId',
            'targetTemplateId',
        ]);

        $moduleName = $args['targetRecordModule'];
        $recordId = $args['targetRecordId'];
        $templateId = $args['targetTemplateId'];
        $emailToField = $args['emailToField'];

        $templateModule = 'pmse_Emails_Templates';
        $emailTo = false;

        if ($emailToField) {
            $this->requireArgs($args, ['emailToData']);

            $emailToData = $args['emailToData'];
            $formula = $emailToData['formulaElement'];

            if (!is_string($formula)) {
                $formula = '';
            }

            $emailTo = $this->getEmailAddresses(
                $formula,
                $moduleName,
                $recordId
            );
        }

        $pmseEmailHandler = ProcessManager\Factory::getPMSEObject('PMSEEmailHandler');

        $templateBean = BeanFactory::retrieveBean($templateModule, $templateId);
        $recordBean = BeanFactory::retrieveBean($moduleName, $recordId);

        $htmlSubject = from_html(
            $pmseEmailHandler->getBeanUtils()
                ->mergeBeanInTemplate($recordBean, $templateBean->subject)
        );

        $htmlBody = from_html(
            $pmseEmailHandler->getBeanUtils()
                ->mergeBeanInTemplate($recordBean, $templateBean->body_html)
        );

        $emailModelData = [
            'subject' => $htmlSubject,
            'description' => $templateBean->description,
            'body' => $htmlBody,
            'emailTo' => $emailTo,
        ];

        return $emailModelData;
    }

    /**
     * Merges a Standard Email Template and returns the data.
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws Exception
     * @throws SugarApiExceptionNotFound
     * @throws SugarDoctrine\DBAL\Query\QueryException
     * @throws SugarApiExceptionInvalidParameter
     * @throws Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     * @throws Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws Doctrine\DBAL\Exception
     * @throws Doctrine\DBAL\Query\QueryException
     */
    public function evaluateEmailTemplate(ServiceBase $api, array $args): array
    {
        global $sugar_config;

        $this->requireArgs($args, [
            'targetTemplateId',
            'targetRecordId',
            'targetRecordModule',
        ]);

        $recordId = $args['targetRecordId'];
        $recordModule = $args['targetRecordModule'];
        $templateId = $args['targetTemplateId'];
        $emailToField = $args['emailToField'];

        $templateData = [];
        $emailModuleName = 'EmailTemplates';
        $emailTo = false;

        $targetBean = [];
        $targetBean[$recordModule] = $recordId;

        $templateBean = BeanFactory::getBean($emailModuleName, $templateId);

        if ($emailToField) {
            $this->requireArgs($args, ['emailToData']);

            $emailToData = $args['emailToData'];
            $formula = $emailToData['formulaElement'];

            $emailTo = $this->getEmailAddresses(
                $formula,
                $recordModule,
                $recordId
            );
        }

        $htmlBody = $templateBean->body_html;

        if ($sugar_config['email_default_client'] !== 'sugar') {
            $htmlBody = strip_tags($htmlBody);
        }

        $templateData['body'] = $templateBean->parse_template($htmlBody, $targetBean);
        $templateData['subject'] = $templateBean->parse_template(strip_tags($templateBean->subject), $targetBean);
        $templateData['description'] = $templateBean->description;
        $templateData['emailTo'] = $emailTo;

        return $templateData;
    }

    /**
     * Returns a recipient(s) email address(es) based on given formula
     *
     * @param string $formula
     * @param string $module
     * @param string $recordId
     *
     * @return array|bool
     *
     * @throws SugarApiExceptionNotFound
     * @throws SugarDoctrine\DBAL\Query\QueryException
     * @throws Exception
     * @throws Doctrine\DBAL\Exception
     * @throws Doctrine\DBAL\Query\QueryException
     */
    private function getEmailAddresses(string $formula, string $module, string $recordId)
    {
        $formula = str_replace("'", '"', $formula);
        $record = BeanFactory::retrieveBean($module, $recordId);

        if (empty($record)) {
            throw new SugarApiExceptionNotFound(
                sprintf(
                    'Could not find parent record %s in module: %s',
                    $module,
                    $recordId
                )
            );
        }

        $result = Parser::evaluate($formula, $record)->evaluate();

        if (!is_array($result) && !is_string($result)) {
            return [];
        }

        if (is_string($result)) {
            $emailAddress = new SugarEmailAddress();
            $emailAddressId = $emailAddress->getEmailGUID($result);

            $result = [
                [
                    'email_address' => $result,
                    'email_address_id' => $emailAddressId,
                ],
            ];
        } else {
            foreach ($result as $key => $emailData) {
                if (!is_array($emailData)) {
                    return false;
                }

                $targetModule = $emailData['bean_module'];
                $targetRecordId = $emailData['bean_id'];

                $parentBean = BeanFactory::retrieveBean($targetModule, $targetRecordId);

                if (empty($parentBean)) {
                    throw new SugarApiExceptionNotFound(
                        sprintf(
                            'Could not find parent record %s in module: %s',
                            $targetModule,
                            $targetRecordId
                        )
                    );
                }

                $result[$key]['name'] = $parentBean->name ?: $parentBean->full_name;
            }
        }

        return $result;
    }

    /**
     * Evaluates a SugarLogic formula and returns the value.
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarDoctrine\DBAL\Query\QueryException
     * @throws Exception
     */
    public function evaluateExpression(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, [
            'targetModule',
            'targetRecordId',
            'targetFields',
        ]);

        $fieldsToBeUpdated = $args['targetFields'];
        $targetModule = $args['targetModule'];
        $targetRecordId = $args['targetRecordId'];
        $record = null;
        $beanFields = null;

        $calculatedValues = [];

        if (array_key_exists('beanData', $args)) {
            $beanFields = $args['beanData'];

            //create a temporary bean
            $record = BeanFactory::newBean($targetModule);
            if (!is_object($record)) {
                // if $record is not an object the method will fail on $record->updateCalculatedFields() anyway,
                // so doing it here with less potential for additional errors/warnings
                throw new \SugarApiExceptionError(
                    \sprintf(
                        'Could not create bean for module %s',
                        $targetModule,
                    )
                );
            }

            $fieldDefs = is_array($record->field_defs) ? $record->field_defs : [];

            //we cannot use the ModuleApi populateBean() function since there can be required fields
            //that are not provided at this time, they may be provided from UI
            //by user when the drawer will be opened
            foreach ($beanFields as $fieldName => $fieldValue) {
                if (array_key_exists($fieldName, $fieldDefs) && $fieldDefs[$fieldName]['type'] === 'multienum' && is_array($fieldValue)) {
                    $fieldValue = implode(',', $fieldValue);
                }

                $record->{$fieldName} = $fieldValue;
            }
        } else {
            $record = BeanFactory::retrieveBean($targetModule, $targetRecordId);
        }


        if (empty($record)) {
            throw new SugarApiExceptionNotFound(
                sprintf(
                    'Could not find record %s in module: %s',
                    $targetModule,
                    $targetRecordId
                )
            );
        }

        foreach ($fieldsToBeUpdated as $fieldName => $paramsData) {
            if (array_key_exists('formula', $paramsData)) {
                $formula = $paramsData['formula'];

                $record->field_defs[$fieldName]['enforced'] = true;
                $record->field_defs[$fieldName]['calculated'] = true;
                $record->field_defs[$fieldName]['formula'] = $formula;
            }

            if (array_key_exists('required_formula', $paramsData)) {
                $formula = $paramsData['required_formula'];

                $record->field_defs[$fieldName]['enforced'] = true;
                $record->field_defs[$fieldName]['required_formula'] = $formula;
            }
        }

        $record->updateCalculatedFields();

        foreach ($fieldsToBeUpdated as $fieldName => $paramsData) {
            $newCalculatedValue = $this->normalizeValue($record->{$fieldName});

            $calculatedValues[$fieldName] = $newCalculatedValue;
        }

        return $calculatedValues;
    }

    /**
     * Convert Formula Boolean expression result to actual boolean value
     *
     * @param $val
     *
     * @return bool
     */
    private function normalizeValue($val)
    {
        if ($val instanceof BooleanConstantExpression) {
            return $val == AbstractExpression::$TRUE;
        }

        return $val;
    }

    /**
     * Evaluates the value for a dynamically calculated URL.
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarDoctrine\DBAL\Query\QueryException
     * @throws Exception
     */
    public function evaluateCalculatedUrl(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, [
            'keepTempFieldForCalculatedURL',
            'recordType',
            'recordId',
        ]);

        $targetModule = $args['recordType'];
        $targetRecordId = $args['recordId'];

        $calculatedExpressionValues = [];
        $keepTempFieldForCalculatedURL = $args['keepTempFieldForCalculatedURL'];
        $bean = BeanFactory::retrieveBean($targetModule, $targetRecordId);

        if (empty($bean)) {
            throw new SugarApiExceptionNotFound(
                sprintf(
                    'Could not find record %s in module: %s',
                    $targetModule,
                    $targetRecordId
                )
            );
        }

        foreach ($keepTempFieldForCalculatedURL as $tempFieldName => $paramsData) {
            if (!isset($bean->field_defs[$paramsData['targetField']]['type'])) {
                $bean->field_defs[$paramsData['targetField']]['type'] = 'varchar';
            }

            $bean->field_defs[$paramsData['targetField']]['enforced'] = true;
            $bean->field_defs[$paramsData['targetField']]['calculated'] = true;
            $bean->field_defs[$paramsData['targetField']]['formula'] = $paramsData['formula'];
        }

        $formattedBeanData = $this->formatBean($api, $args, $bean);

        foreach ($formattedBeanData as $fieldName => $fieldValue) {
            if (array_key_exists($fieldName, $bean->field_defs) && $bean->field_defs[$fieldName]['type'] === 'link') {
                continue;
            }
            $bean->{$fieldName} = $fieldValue;
        }

        $bean->updateCalculatedFields();

        foreach ($keepTempFieldForCalculatedURL as $tempFieldName => $paramsData) {
            $value = $bean->{$paramsData['targetField']};

            if (is_object($value)) {
                $value = strval($value);
            }

            $fieldType = $bean->field_defs[$paramsData['targetField']]['type'];

            // handle setting a bool value to a text field
            if (($fieldType == 'varchar' || $fieldType == 'text') && is_bool($value)) {
                $value = ($value) ? 'true' : 'false';
            }

            $calculatedExpressionValues[$tempFieldName] = [];
            $calculatedExpressionValues[$tempFieldName]['value'] = $value;
            $calculatedExpressionValues[$tempFieldName]['fieldName'] = $paramsData['targetField'];
        }

        return $calculatedExpressionValues;
    }
}
