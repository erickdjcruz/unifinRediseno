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

class PMSEEmailsTemplates extends vCardApi
{
    /**
     * @var Sugarcrm\Sugarcrm\ProcessManager\PMSE|mixed
     */
    public $crmDataWrapper;

    public function __construct()
    {
        $this->crmDataWrapper = ProcessManager\Factory::getPMSEObject('PMSECrmDataWrapper');
    }

    public function registerApiRest()
    {
        return [
            'emailTemplateDownload' => [
                'reqType' => 'GET',
                'path' => ['pmse_Emails_Templates', '?', 'etemplate'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'emailTemplateDownload',
                'rawReply' => true,
                'allowDownloadCookie' => true,
                'acl' => 'view',
                'shortHelp' => 'Exports a .pet file with a Process Email Templates definition',
                'longHelp' => 'modules/pmse_Emails_Templates/clients/base/api/help/email_templates_export_help.html',
            ],
            'emailTemplatesImportPost' => [
                'reqType' => 'POST',
                'path' => ['pmse_Emails_Templates', 'file', 'emailtemplates_import'],
                'pathVars' => ['module', '', ''],
                'method' => 'emailTemplatesImport',
                'rawPostContents' => true,
                'acl' => 'create',
                'shortHelp' => 'Imports a Process Email Templates from a .pet file',
                'longHelp' => 'modules/pmse_Emails_Templates/clients/base/api/help/email_templates_import_help.html',
            ],
            'listVariables' => [
                'reqType' => 'GET',
                'path' => ['pmse_Emails_Templates', 'variables', 'find'],
                'pathVars' => ['module', '', ''],
                'method' => 'findVariables',
                'acl' => 'view',
                'shortHelp' => 'Get the variable list for a module',
                'longHelp' => 'modules/pmse_Emails_Templates/clients/base/api/help/email_templates_variable_list_get_help.html',
            ],
            'listModulesRelated' => [
                'reqType' => 'GET',
                'path' => ['pmse_Emails_Templates', '?', 'find_modules'],
                'pathVars' => ['module', '', ''],
                'method' => 'retrieveRelatedBeans',
                'acl' => 'view',
                'shortHelp' => 'Retrieve a list of related modules',
                'longHelp' => 'modules/pmse_Emails_Templates/clients/base/api/help/email_templates_module_list_get_help.html',
            ],
        ];
    }

    /**
     * Finds related modules variables that match the search term.
     *
     * Arguments:
     *    q           - search string
     *    module_list -  one of the keys from $modules
     *    order_by    -  columns to sort by (one or more of $sortableColumns) with direction
     *                   ex.: name:asc,id:desc (will sort by last_name ASC and then id DESC)
     *    offset      -  offset of first record to return
     *    max_num     -  maximum records to return
     *
     * @param $api
     * @param $args
     * @return array
     */
    public function findVariables($api, $args)
    {
        $direction = null;
        // Initialize this var since not all requests send 'q'
        $q = $args['q'] ?? null;
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $offset = 0;
        $limit = (!empty($args['max_num'])) ? (int)$args['max_num'] : 20;
        $orderBy = [];

        if (!empty($args['offset'])) {
            if ($args['offset'] === 'end') {
                $offset = 'end';
            } else {
                $offset = (int)$args['offset'];
            }
        }

        if (!empty($args['order_by'])) {
            $orderBys = explode(',', $args['order_by']);

            foreach ($orderBys as $sortBy) {
                $column = $sortBy;
                $direction = 'ASC';

                if (strpos($sortBy, ':')) {
                    // it has a :, it's specifying ASC / DESC
                    [$column, $direction] = explode(':', $sortBy);

                    if (strtolower($direction) == 'desc') {
                        $direction = 'DESC';
                    } else {
                        $direction = 'ASC';
                    }
                }

                // only add column once to the order-by clause
                if (empty($orderBy[$column])) {
                    $orderBy[$column] = $direction;
                }
            }
        }

        $records = [];
        $nextOffset = -1;

        if ($offset !== 'end') {
            $records = $this->retrieveFields(
                $args['module_list'],
                $direction,
                $limit,
                $offset,
                $args['base_module'],
                $q
            );
            $totalRecords = $records['totalRecords'];
            $trueOffset = $offset + $limit;

            if ($trueOffset < $totalRecords) {
                $nextOffset = $trueOffset;
            }
        }

        return [
            'next_offset' => $nextOffset,
            'records' => $records['records'],
        ];
    }

    public function retrieveFields($filter, $orderBy, $limit, $offset, $baseModule, $q = null)
    {
        global $beanList;
        $pmseRelatedModule = ProcessManager\Factory::getPMSEObject('PMSERelatedModule');
        if (isset($beanList[$filter])) {
            $newModuleFilter = $filter;
        } else {
            $newModuleFilter = $pmseRelatedModule->getRelatedModuleName($baseModule, $filter);
        }

        $output = [];
        $moduleBean = BeanFactory::newBean($newModuleFilter);
        $fieldsData = $moduleBean->field_defs ?? [];
        foreach ($fieldsData as $field) {
            //$retrieveId = isset($additionalArgs['retrieveId']) && !empty($additionalArgs['retrieveId']) && $field['name'] == 'id' ? $additionalArgs['retrieveId'] : false;
            if (isset($field['vname']) && PMSEEngineUtils::isValidField($field, 'ET') &&
                PMSEEngineUtils::isSupportedField($moduleBean->object_name, $field['name'], 'ET')) {
                $tmpField = [];
                $tmpField['id'] = $field['name'];
                $tmpField['_module'] = $newModuleFilter;
                $tmpField['name'] = str_replace(':', '', translate($field['vname'], $newModuleFilter));
                $tmpField['rhs_module'] = $filter;
                if (empty($q) || stripos($tmpField['name'], (string) $q) !== false) {
                    $output[] = $tmpField;
                }
            }
        }

        $text = [];
        foreach ($output as $key => $row) {
            $text[$key] = strtolower($row['name']);
        }
        if ($orderBy == 'ASC') {
            array_multisort($text, SORT_ASC, $output);
        } else {
            array_multisort($text, SORT_DESC, $output);
        }
        $start = $offset;
        $end = $offset + $limit;
        $count = 0;
        $outputTmp = [];
        foreach ($output as $field) {
            if ($count >= $start && $count < $end) {
                $outputTmp[] = $field;
            }
            $count++;
        }

        return ['totalRecords' => safeCount($output), 'records' => $outputTmp];
    }

    public function retrieveRelatedBeans($api, $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $related_modules = $this->crmDataWrapper->retrieveRelatedBeans($args['module_list'], 'one-to-one');
        return $related_modules;
    }

    public function emailTemplatesImport($api, $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $this->requireArgs($args, ['module']);

        $bean = BeanFactory::newBean($args['module']);
        if (!$bean->ACLAccess('save') || !$bean->ACLAccess('import')) {
            $sugarApiExceptionNotAuthorized = new SugarApiExceptionNotAuthorized('EXCEPTION_NOT_AUTHORIZED');
            PMSELogger::getInstance()->alert($sugarApiExceptionNotAuthorized->getMessage());
            throw $sugarApiExceptionNotAuthorized;
        }
        if (isset($_FILES) && safeCount($_FILES) === 1) {
            $first_key = array_key_first($_FILES);
            if (isset($_FILES[$first_key]['tmp_name'])
                && $this->isUploadedFile($_FILES[$first_key]['tmp_name'])
                && !empty($_FILES[$first_key]['size'])
            ) {
                $importerObject = PMSEImporterFactory::getImporter('email_template');
                $name = $_FILES[$first_key]['name'];
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                if ($extension == $importerObject->getExtension()) {
                    try {
                        $data = $importerObject->importProject($_FILES[$first_key]['tmp_name']);
                    } catch (SugarApiExceptionNotAuthorized $e) {
                        $e->setMessage('ERROR_UPLOAD_ACCESS_ET');
                        PMSELogger::getInstance()->alert($e->getMessage());
                        throw $e;
                    }
                    $result = ['emailtemplates_import' => $data];
                } else {
                    $sugarApiExceptionRequestMethodFailure = new SugarApiExceptionRequestMethodFailure(
                        'ERROR_UPLOAD_FAILED'
                    );
                    PMSELogger::getInstance()->alert($sugarApiExceptionRequestMethodFailure->getMessage());
                    throw $sugarApiExceptionRequestMethodFailure;
                }
                return $result;
            }
        } else {
            $sugarApiExceptionMissingParameter = new SugarApiExceptionMissingParameter('ERROR_UPLOAD_FAILED');
            PMSELogger::getInstance()->alert($sugarApiExceptionMissingParameter->getMessage());
            throw $sugarApiExceptionMissingParameter;
        }
    }

    public function emailTemplateDownload($api, $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyRecordAccess($api, $args);
        if (PMSEEngineUtils::isExportDisabled($args['module'])) {
            $sugarApiExceptionNotAuthorized = new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['ERR_EXPORT_DISABLED']
            );
            PMSELogger::getInstance()->alert($sugarApiExceptionNotAuthorized->getMessage());
            throw $sugarApiExceptionNotAuthorized;
        }
        $emailTemplate = $this->getEmailTemplateExporter();
        return $emailTemplate->exportProject($args['record'], $api);
    }

    protected function getEmailTemplateExporter()
    {
        $emailTemplate = ProcessManager\Factory::getPMSEObject('PMSEEmailTemplateExporter');
        return $emailTemplate;
    }
}
