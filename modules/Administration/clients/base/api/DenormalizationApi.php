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

use Sugarcrm\Sugarcrm\Denormalization\Relate\FieldConfig;
use Sugarcrm\Sugarcrm\Denormalization\Relate\FieldDenormalizationException;
use Sugarcrm\Sugarcrm\Denormalization\Relate\PreChecker;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;
use Sugarcrm\Sugarcrm\Denormalization\Relate\SynchronizationManager;

final class DenormalizationApi extends SugarApi
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'modules' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'denormalization', 'configuration'],
                'pathVars' => [''],
                'method' => 'getConfiguration',
                'shortHelp' => 'Get modules and fields configuration',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'fields' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'denormalization', 'fields'],
                'pathVars' => [''],
                'method' => 'getDenormFieldList',
                'shortHelp' => 'Get denormalized field list',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'status' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'denormalization', 'status'],
                'pathVars' => [''],
                'method' => 'getStatus',
                'shortHelp' => 'Get the current process status',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'abort' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'denormalization', 'abort'],
                'pathVars' => [''],
                'method' => 'abortProcess',
                'shortHelp' => 'Abort the current process',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'pre-check' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'denormalization', 'pre-check'],
                'pathVars' => [''],
                'method' => 'preCheck',
                'shortHelp' => 'Check configuration',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
            'apply' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'denormalization', 'apply'],
                'pathVars' => [''],
                'method' => 'runProcess',
                'shortHelp' => 'Apply configuration',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
        ];
    }

    public function getConfiguration(ServiceBase $api, array $args): array
    {
        $this->ensureDeveloperUser();
        $tabContoller = new TabController();
        $configList = array_fill_keys($tabContoller->get_system_tabs(), []);
        foreach ((new FieldConfig())->getList() as $moduleName => $moduleConfig) {
            if (isset($configList[$moduleName])) {
                $configList[$moduleName] = $moduleConfig;
            }
        }

        return $configList;
    }

    public function getStatus(ServiceBase $api, array $args)
    {
        $this->ensureDeveloperUser();
        $syncJob = new SynchronizationManager();
        $job = $syncJob->getJob();

        return $job ? $job->toArray() : [];
    }

    public function abortProcess(ServiceBase $api, array $args)
    {
        $this->ensureDeveloperUser();
        $syncJob = new SynchronizationManager();
        $syncJob->removeJobIfExists();

        return ['ok' => true];
    }

    public function preCheck(ServiceBase $api, array $args)
    {
        $this->ensureDeveloperUser();
        [$bean, $fieldNameToProcess, $isDenormalization] = $this->parseArguments($args);

        if (!$bean || !$fieldNameToProcess) {
            return ['message' => translate('LBL_MANAGE_RELATE_DENORMALIZATION_MSG_EMPTY_REQUEST', 'Administration')];
        }

        $report = [];

        $overallPossibility = true;

        $isPossible = false;
        $details = [];
        try {
            $processEntity = new Entity($bean, $fieldNameToProcess);
            $preChecker = new PreChecker();
            if ($isDenormalization) {
                $details = $preChecker->validateDenormalization($processEntity);
            } else {
                $details = $preChecker->validateNormalization($processEntity);
            }
            $msg = '';
            $isPossible = true;
            if (!empty($details['validation_error'])) {
                $msg = translate($details['validation_error'], 'Administration');
                $isPossible = false;
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }
        $report[] = [
            'is_denormalized' => !$isDenormalization,
            'name' => $fieldNameToProcess,
            'is_possible' => $isPossible,
            'message' => $msg,
            'details' => $details,
        ];
        if (!$isPossible) {
            $overallPossibility = false;
        }

        return [
            'report' => $report,
            'overall_possibility' => $overallPossibility,
        ];
    }

    public function runProcess(ServiceBase $api, array $args)
    {
        $this->ensureDeveloperUser();
        $preCheckResult = $this->preCheck($api, $args);
        if (empty($preCheckResult['overall_possibility'])) {
            return [
                'message' => translate('LBL_MANAGE_RELATE_DENORMALIZATION_PRECHECK_PREV_JOB_IN_PROGRESS', 'Administration'),
            ];
        }

        [$bean, $fieldNameToProcess, $isDenormalization] = $this->parseArguments($args);

        if (!$bean || is_null($fieldNameToProcess)) {
            return ['message' => translate('LBL_MANAGE_RELATE_DENORMALIZATION_MSG_EMPTY_REQUEST', 'Administration')];
        }

        $result = ['ok' => true];

        $processEntity = new Entity($bean, $fieldNameToProcess);
        $process = new Process();
        if ($isDenormalization) {
            $process->denormalize($processEntity);
            $result['denormalized'] = true;
        } else {
            $process->normalize($processEntity);
            $result['normalized'] = true;
        }

        return $result;
    }

    private function parseArguments(array $args)
    {
        $module = $args['modules'] ?? null;
        $fieldLists = $args['field-lists'] ?? null;
        $fieldsNotDenormalized = (array)$fieldLists['not_denormalized'] ?? [];
        $fieldsDenormalized = (array)$fieldLists['denormalized'] ?? [];

        $fieldNameToProcess = null;
        $isDenormalization = null;
        $isNormalization = null;

        $fieldList = (new FieldConfig())->getList();

        $bean = BeanFactory::newBean($module);
        if ($bean) {
            foreach ($fieldsNotDenormalized as $fieldName) {
                $fieldDef = $bean->getFieldDefinition($fieldName);
                if (isset($fieldList[$module][$fieldName]) && $fieldDef) {
                    $fieldNameToProcess = $fieldName;
                    $isDenormalization = false;
                    break;
                }
            }
            foreach ($fieldsDenormalized as $fieldName) {
                $fieldDef = $bean->getFieldDefinition($fieldName);
                if (!isset($fieldList[$module][$fieldName]) && $fieldDef) {
                    $fieldNameToProcess = $fieldName;
                    $isDenormalization = true;
                    break;
                }
            }
        }

        return [$bean, $fieldNameToProcess, $isDenormalization];
    }

    /**
     * Ensure current user has admin permissions, or he is developer for any module
     * @throws SugarApiExceptionNotAuthorized
     */
    protected function ensureDeveloperUser()
    {
        if (empty($GLOBALS['current_user']) || !$GLOBALS['current_user']->isDeveloperForAnyModule()) {
            throw new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['EXCEPTION_NOT_AUTHORIZED']
            );
        }
    }
}
