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

require_once 'clients/base/api/ExportApi.php';

use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;
use Sugarcrm\Sugarcrm\CustomerJourney\ImportExport;
use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;

class DRICustomerJourneyTemplatesImportExportApi extends ExportApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'templateImportPost' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflow_Templates', 'file', 'template-import'],
                'pathVars' => ['module', '', ''],
                'method' => 'import',
                'rawPostContents' => true,
                'acl' => 'create',
                'shortHelp' => 'Imports a Smart Guide Template from a .json file',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplatestemplateImportPost.html',
                'minVersion' => '11.19',
            ],
            'checkTemplateImportPost' => [
                'reqType' => 'POST',
                'path' => ['DRI_Workflow_Templates', 'file', 'check-template-import'],
                'pathVars' => ['module', '', ''],
                'method' => 'checkImport',
                'rawPostContents' => true,
                'acl' => 'create',
                'shortHelp' => 'Checks import of a Smart Guide Template from a .json file',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplatescheckTemplateImportPost.html',
                'minVersion' => '11.19',
            ],
            'exportGet' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflow_Templates', '?', 'export'],
                'pathVars' => ['module', 'record'],
                'method' => 'export',
                'rawReply' => true,
                'allowDownloadCookie' => true,
                'shortHelp' => 'Exports a Smart Guide Template in .json format.',
                'longHelp' => '/include/api/help/customer_journeyDRI_Workflow_TemplatesexportGet.html',
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Checks import of a Smart Guide Template from a .json file
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionRequestMethodFailure
     */
    public function checkImport(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $importer = new ImportExport\TemplateImporter();
        $file = $this->getUploadFileName($args);

        try {
            $template = $importer->parseUpload($file);
        } catch (SugarApiExceptionNotAuthorized $e) {
            throw new SugarApiExceptionNotAuthorized('ERROR_UPLOAD_ACCESS_PD');
        }

        try {
            $existing = \DRI_Workflow_Template::getByName($template['name'], $template['id']);
            $template['id'] = $existing->id;
            $duplicate = true;
        } catch (CJException\CustomerJourneyException $e) {
            $duplicate = false;
        }

        try {
            $existing = \DRI_Workflow_Template::getById($template['id']);
            $template['id'] = $existing->id;
            $update = true;
        } catch (CJException\CustomerJourneyException $e) {
            $update = false;
        }

        return [
            'duplicate' => $duplicate,
            'update' => $update,
            'record' => [
                'id' => $template['id'],
                'name' => $template['name'],
            ],
        ];
    }

    /**
     * Imports a Smart Guide Template from a .json file
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionRequestMethodFailure
     */
    public function import(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $importer = new ImportExport\TemplateImporter();
        $file = $this->getUploadFileName($args);

        try {
            $template = $importer->importUpload($file);
        } catch (SugarApiExceptionNotAuthorized $e) {
            throw new SugarApiExceptionNotAuthorized('ERROR_UPLOAD_ACCESS_PD');
        }

        return [
            'record' => [
                'id' => $template->id,
                'name' => $template->name,
                'new_with_id' => $template->new_with_id,
                'deleted' => $template->deleted,
            ],
        ];
    }

    /**
     * Get name of the upload file
     *
     * @param array $args
     * @return string
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionRequestMethodFailure
     */
    private function getUploadFileName(array $args)
    {
        $this->requireArgs($args, ['module']);

        $bean = BeanFactory::newBean('DRI_Workflow_Templates');

        if (!$bean->ACLAccess('save') || !$bean->ACLAccess('import')) {
            throw new SugarApiExceptionNotAuthorized('You do not have permission to import Smart Guide templates. Please contact your Sugar Administrator.');
        }

        if ($_FILES !== null && safeCount($_FILES) === 1) {
            $file = array_key_first($_FILES);
            $name = $_FILES[$file]['name'];

            if (false === stripos($name, '.json')) {
                throw new SugarApiExceptionRequestMethodFailure('ERROR_UPLOAD_FAILED');
            }

            return $file;
        }

        throw new SugarApiExceptionMissingParameter('ERROR_UPLOAD_FAILED');
    }

    /**
     * Exports a Smart Guide Template in .json format
     *
     * @param ServiceBase $api
     * @param array $args
     * @return string
     * @throws SugarApiExceptionNotAuthorized
     */
    public function export(ServiceBase $api, array $args = [])
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        $bean = $this->loadBean($api, $args);

        if (!$bean->ACLAccess('export')) {
            throw new SugarApiExceptionNotAuthorized('You do not have permission to export Smart Guide templates. Please contact your Sugar Administrator.');
        }

        $name = $bean->name;
        $name = str_replace(' ', '_', $name);
        $filename = sprintf('%s.json', $name);

        $exporter = new ImportExport\TemplateExporter();
        $data = $exporter->export($bean);

        if (defined('JSON_PRETTY_PRINT')) {
            $content = json_encode($data, JSON_PRETTY_PRINT);
        } else {
            $content = json_encode($data);
        }

        $content = $this->doExport($api, $filename, $content);

        $api->setHeader('Content-Type', 'application/json; charset=' . $GLOBALS['locale']->getExportCharset());
        $api->setHeader('Content-Disposition', 'attachment; filename=' . $filename);

        return $content;
    }
}
