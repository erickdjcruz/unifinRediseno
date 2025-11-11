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

use Sugarcrm\Sugarcrm\DocumentMerge\Client\Adapter\AdapterFactory;
use Sugarcrm\Sugarcrm\DocumentMerge\ServiceFactory;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\DocumentMerge\Client\Constants\MergeType;
use Sugarcrm\Sugarcrm\ProcessManager;

/**
 * An api class for handling the document merging
 *
 * @class
 */
class DocumentMergeApi extends SugarApi
{
    /**
     * List with file types
     *
     * @var array
     */
    public const FILE_TYPES = [
        'pdf' => 'PDF',
        'docx' => 'DOC',
        'xlsx' => 'XLS',
        'pptx' => 'PPT',
    ];

    /**
     * Setting to limit the number of related records retrieved from the server
     *
     * @var const
     */
    public const MAX_RETRIEVE = 20;

    public function registerApiRest()
    {
        return [
            'merge' => [
                'reqType' => 'POST',
                'path' => ['DocumentMerge', 'merge'],
                'pathVars' => [],
                'method' => 'merge',
                'shortHelp' => 'Merge the document',
                'longHelp' => 'include/api/help/document_merges_merge.html',
            ],
            'createDocument' => [
                'reqType' => 'POST',
                'path' => ['DocumentMerge', 'createDocument'],
                'pathVars' => [],
                'method' => 'createDocument',
                'shortHelp' => 'Create the document',
                'longHelp' => 'include/api/help/document_merges_create_document.html',
            ],
            'getCurrencySymbol' => [
                'reqType' => 'GET',
                'path' => ['DocumentMerge', 'getCurrencySymbol', '?', '?'],
                'pathVars' => ['DocumentMerge', 'getCurrencySymbol', 'module', 'record_id'],
                'method' => 'getCurrencySymbol',
                'shortHelp' => 'Retrieves the currency symbol from a certain record',
                'longHelp' => 'include/api/help/document_merges_get_currency_symbol.html',
            ],
            'getMergeModules' => [
                'reqType' => 'GET',
                'path' => ['DocumentMerge', 'mergeModules'],
                'pathVars' => ['DocumentMerge', 'mergeModules'],
                'method' => 'getMergeModules',
                'shortHelp' => 'Retrieves the modules that are compatible with document merging',
                'longHelp' => 'include/api/help/document_merges_get_merge_modules.html',
                'minVersion' => '11.15',
            ],
        ];
    }

    /**
     * Perform the document merge
     *
     * @param Servicebase api
     * @param array args
     *
     * @return string
     * @throws Exception
     */
    public function merge(ServiceBase $api, array $args): string
    {
        // there are the minimmum arguments to start a document merge
        $this->requireArgs($args, [
            'mergeType',
            'useRevision',
            'templateId',
            'recordModule',
        ]);

        $request = InputValidation::create([], $args);
        $data = [
            'mergeType' => $request->getValidInputPost('mergeType'),
            'useRevision' => $request->getValidInputPost('useRevision') ?? true,
            'templateName' => $request->getValidInputPost('templateName'),
            'templateId' => $request->getValidInputPost('templateId', 'Assert\Guid'),
            'recordId' => $request->getValidInputPost('recordId', 'Assert\Guid'),
            'recordModule' => $request->getValidInputPost('recordModule'),
            'parentId' => $request->getValidInputPost('parentId'),
            'parentModule' => $request->getValidInputPost('parentModule'),
            'maxRelate' => $request->getValidInputPost('maxRelate') ?? static::MAX_RETRIEVE,
        ];

        //handle for multimerge
        $data['modelIds'] = [];
        if (array_key_exists('selectedRecords', $args) && $args['selectedRecords']) {
            $data['selectedRecords'] = $args['selectedRecords'];
            foreach ($data['selectedRecords'] as $record) {
                $data['modelIds'][] = $record['id'];
            }
        }

        // not going through normal request, because it only comes from pmse.
        $flowData = array_key_exists('flowData', $args) ? $args['flowData'] : null;
        $mergeRequestId = $this->createMergeRequest($data, $flowData);
        $data['mergeRequestId'] = $mergeRequestId;

        $payload = (AdapterFactory::getDataAdapterInstance($data))->getData();
        $service = $this->getService();
        if (!$service) {
            throw new SugarException('DOCUMENT_MERGE_SERVICE_NOT_FOUND', null, 'DocumentMerges');
        }

        try {
            $service->merge($payload);
        } catch (\Exception $ex) {
            throw new SugarException($ex->getMessage());
        }

        return $mergeRequestId;
    }

    /**
     * Retrieves the modules for document merge
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getMergeModules(ServiceBase $api, array $args): array
    {
        require_once 'src/DocumentMerge/DocumentTemplateHelper.php';
        return getTargetModules();
    }

    /**
     * Returns and instance of the merge service
     *
     * @return Service
     */
    protected function getService()
    {
        return ServiceFactory::getService();
    }

    /**
     * Receives the generated document from the api and it creates a sugar document.
     * Also establishes the relationship between the record and the Documents module
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return string
     * @throws SugarException
     */
    public function createDocument(ServiceBase $api, array $args): string
    {
        $this->requireArgs($args, [
            'record_module',
            'sugar_mr_id',
            'use_revision',
        ]);
        /**
         * We might already know the id of the document
         */
        $useRevision = filter_var($args['use_revision'], FILTER_VALIDATE_BOOLEAN);

        if ($useRevision) {
            $documentId = $args['gen_doc_id'] ?? Uuid::uuid4();
        } else {
            $documentId = Uuid::uuid4();
        }

        $revisionId = Uuid::uuid4();

        try {
            $this->uploadFile($revisionId);
            $fileName = $this->getFileName($args);

            $docId = $this->createSugarDocument([
                'fileName' => $fileName,
                'documentId' => $documentId,
                'useRevision' => $args['use_revision'],
                'revisionId' => $revisionId,
            ]);

            $this->updateMergeRequest($args['sugar_mr_id'], [
                'generated_document_id' => $docId,
                'status' => 'success',
                'messsage' => 'Success',
            ]);
            $this->createDocumentRelationship($documentId, $args);
        } catch (\SugarException $ex) {
            throw $ex;
        }

        return $documentId;
    }

    /**
     * Returns the curreny symbol used for a certain record
     *
     * @param Servicebase api
     * @param array args
     *
     * @return string
     */
    public function getCurrencySymbol(ServiceBase $api, array $args): string
    {
        $this->requireArgs($args, [
            'record_id',
            'module',
        ]);

        $recordId = $args['record_id'];
        $module = $args['module'];

        $bean = \BeanFactory::getBean($module, $recordId);
        $currencyId = $bean->currency_id;
        if (!$currencyId) {
            throw new \SugarException('DOCUMENT_MERGE_CURRENCY_ERROR', null, 'DocumentMerges');
        }
        $currency = \SugarCurrency::getCurrencyByID($currencyId);

        return $currency->symbol;
    }

    /**
     * Create a DocumentMerge record
     *
     * @param array data
     * @param string flowData
     * @return string
     */
    protected function createMergeRequest(array $data, ?string $flowData): string
    {
        global $current_user;

        $templateBean = BeanFactory::retrieveBean('DocumentTemplates', $data['templateId']);

        if (is_null($templateBean)) {
            throw new \SugarException('DOCUMENT_MERGE_ERROR', null, 'DocumentMerges');
        }

        $fileType = $this->getFileType($templateBean->filename, $data['mergeType']);

        $documentMergeBean = new DocumentMerge();
        $documentMergeBean->merge_type = $data['mergeType'];
        $documentMergeBean->status = 'processing';
        $documentMergeBean->parent_id = $data['recordId'];
        $documentMergeBean->parent_type = $data['recordModule'];
        $documentMergeBean->template_id = $data['templateId'];
        $documentMergeBean->file_type = $fileType;
        $documentMergeBean->name = $data['templateName'];
        $documentMergeBean->assigned_user_id = $current_user->id;
        $documentMergeBean->record_ids = array_key_exists('selectedRecords', $data) ? json_encode($data['selectedRecords']) : '';

        // do this only if there is flow data coming from pmse
        if (is_string($flowData)) {
            $documentMergeBean->flow_data = $flowData;
        }

        $documentMergeBean->save();

        return $documentMergeBean->id;
    }

    /**
     * Upload a file to the upload folder
     *
     * @param string id - the upload id
     * @throws SugarException
     */
    protected function uploadFile(string $id): void
    {
        $upload_file = new UploadFile('data');

        if ($upload_file->confirm_upload()) {
            $upload_file->final_move($id);
        } else {
            throw new \SugarException('DOCUMENT_MERGE_UPLOAD_ERROR', null, 'DocumentMerges');
        }
    }

    /**
     * Calculates the filename of the generated document
     *
     * @param array args
     *
     * @return string
     * @throws SugarException
     */
    protected function getFileName(array $args): string
    {
        $record = null;
        $prefix = null;
        $postfix = null;
        $fileName = '';
        if (!empty($args['record_id'])) {
            $record = BeanFactory::retrieveBean($args['record_module'], $args['record_id']);
        }
        $mergeRequest = BeanFactory::retrieveBean('DocumentMerges', $args['sugar_mr_id']);

        if (!isset($mergeRequest)) {
            throw new \SugarException('merge: Document Creation failed', null, 'DocumentMerges');
        }

        $documentTemplate = BeanFactory::retrieveBean('DocumentTemplates', $mergeRequest->template_id);

        if ($documentTemplate) {
            $prefix = $documentTemplate->prefix ?? '';
            $postfix = $documentTemplate->postfix ?? '';
        }

        if ($record) {
            $recordName = $record->name;
        } else {
            //it must be a multimerge
            $recordName = 'Multimerge';
        }

        $fileName = $prefix . $recordName . ' - ' . $mergeRequest->name . $postfix;
        return $this->setFileNameExtension($fileName, $args['extension']);
    }

    /**
     * Creates a sugar document
     *
     * Example options
     * [
     *      'fileName' => 'SugarAccount.docx',
     *      'documentId' => 'docId',
     *      'userId' => '1',
     *      'revisionId => 'revId',
     *      'useRevision' => true
     * ]
     *
     * @param array options
     * @return string - document id
     */
    protected function createSugarDocument($options): string
    {
        global $current_user;
        $fileName = $options['fileName'];
        $documentId = $options['documentId'];
        $useRevision = filter_var($options['useRevision'], FILTER_VALIDATE_BOOLEAN);
        $revisionId = $options['revisionId'];

        $defaultTeam = $current_user->default_team;

        if ($useRevision) {
            /**
             * We need tp guess the primary document by filename.(because we cannot referentiate it by id)
             * The flow is:
             * 1. Generate a document_id before merging the template
             * 2. After the merge is complete, then we need to create that document
             * 3. We have a document_id(which we generated previously), but if use_revision
             *    is checked, then we need to try and find/guess the document for which we need to create a revision.
             *    If we find the parent document, then the generated document id, becomes useless,
             *    because we have another id.
             *    Otherwise use the previously generated id.
             */
            $documentData = $this->findDocument($fileName);

            if (is_array($documentData)) {
                $this->createRevisionBean($documentData['id'], [
                    'revisionId' => $revisionId,
                    'fileName' => $fileName,
                    'defaultTeam' => $defaultTeam,
                ]);
                $this->updateDocumentRevisionId($documentData['id'], $revisionId);
                return $documentData['id'];
            }
        }

        return $this->createDocumentWithRevision([
            'fileName' => $fileName,
            'documentId' => $documentId,
            'revisionId' => $revisionId,
            'defaultTeam' => $defaultTeam,
        ]);
    }

    /**
     * Updates the document_revision_id field for a document
     *
     * @param string $documentId
     * @param string $revisionId
     * @return void
     */
    private function updateDocumentRevisionId(string $documentId, string $revisionId): void
    {
        $document = BeanFactory::retrieveBean('Documents', $documentId);
        if ($document) {
            $document->document_revision_id = $revisionId;
            $document->save();
        }
    }

    /**
     * Update the DocumentMerge bean
     *
     * @param string $mergeRequestId
     * @param array data
     * @throws SugarException
     */
    protected function updateMergeRequest(string $mergeRequestId, array $data): void
    {
        $bean = BeanFactory::retrieveBean('DocumentMerges', $mergeRequestId);

        if (is_null($bean)) {
            throw new \SugarException('DOCUMENT_MERGE_RECORD_NOT_FOUND', null, 'DocumentMerges');
        }

        foreach ($data as $fieldName => $value) {
            $bean->{$fieldName} = $value;
        }

        $bean->save();

        //check if we need to send the document as an email attachement
        if ($bean->status === 'success' && is_string($bean->flow_data)) {
            $flowData = json_decode($bean->flow_data, true);
            $evnDefBean = BeanFactory::retrieveBean('pmse_BpmEventDefinition', $flowData['bpmn_id']);

            if (!$evnDefBean) {
                return;
            }
            if (!is_string($evnDefBean->evn_params)) {
                return;
            }
            $evnParams = json_decode($evnDefBean->evn_params, true);
            if ($evnParams['act_send_email']) {
                $flowData['doc_merge_attach_id'] = $bean->generated_document_id;
                $emailHandler = ProcessManager\Factory::getPMSEObject('PMSEEmailHandler');
                $emailHandler->queueEmail($flowData);
            }
        }
    }

    /**
     * Creates the relationship between the record and the document
     *
     * @param string documentId
     * @param array args
     */
    protected function createDocumentRelationship($documentId, $args): void
    {
        //If the merge is initiated from a subpanel relate the document to the parent record also
        $parentId = $args['parent_id'];
        $parentModule = $args['parent_module'];

        $module = $args['record_module'];
        $modelId = $args['record_id'];

        if (isset($parentId) && isset($parentModule)) {
            $this->addToDocumentRelationship($parentModule, $parentId, $documentId);
        }

        if (isset($module) && isset($modelId)) {
            $this->addToDocumentRelationship($module, $modelId, $documentId);
        }
    }

    /**
     * Given a template name and merge type,
     * return the generated file type
     *
     * @param string templateName
     * @param string mergeType
     *
     * @return string
     */
    private function getFileType(string $templateName, string $mergeType): string
    {
        if ($mergeType === MergeType::Convert ||
            $mergeType === MergeType::MultiConvert ||
            $mergeType === MergeType::SpreadsheetConvert ||
            $mergeType === MergeType::PresentationConvert ||
            $mergeType === MergeType::LabelsGenerateConvert) {
            return self::FILE_TYPES['pdf'];
        }

        $ext = pathinfo($templateName, PATHINFO_EXTENSION);
        return self::FILE_TYPES[$ext];
    }

    /**
     * Makes sure the generated filename has an extension
     *
     * @param string $fileName
     * @param string $extension
     *
     * @return string
     */
    private function setFileNameExtension(string $fileName, string $extension): string
    {
        if (strpos($fileName, $extension) === false) {
            $fileName .= $extension;
        }

        return $fileName;
    }

    /**
     * Get document with revision id
     *
     * @param string name
     *
     * @return array|null
     */
    private function findDocument(string $name): ?array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(BeanFactory::newBean('Documents'));
        $sugarQuery->select(['id', 'document_revision_id', 'document_name']);
        $sugarQuery->where()->equals('document_name', $name);
        $result = $sugarQuery->execute();
        if (safeCount($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * Creates a revision for a document
     *
     * @param array documentData
     * @param array options
     */
    private function createRevisionBean($documentId, array $options): void
    {
        global $current_user;

        $document = BeanFactory::retrieveBean('Documents', $documentId);
        if (!$document) {
            throw new \SugarException('DOCUMENT_MERGE_ERROR', null, 'DocumentMerges');
        }

        $revisions = $document->get_linked_beans('revisions', 'DocumentRevision');
        if (!empty($revisions)) {
            $latestRevision = safeCount($revisions);
            $currentRevisionNumber = $latestRevision + 1;
        } else {
            $currentRevisionNumber = 1;
        }

        $fileData = $_FILES['data'];

        $revision = new \DocumentRevision();
        $revision->id = $options['revisionId'];
        $revision->document_id = $documentId;
        $revision->revision = strval($currentRevisionNumber);
        $revision->file = $fileData;
        $revision->filename = $options['fileName'];
        $revision->doc_type = 'Sugar';
        $revision->new_with_id = true;
        $revision->created_by = $current_user->id;
        $revision->assigned_user_id = $current_user->id;
        $revision->team_id = $options['defaultTeam'];
        $revision->modified_user_id = $options['userId'];
        $revision->save();
    }

    /**
     * Creates a Document bean and a Revision for it
     *
     * @param array options
     *
     * @return string - the document id
     */
    private function createDocumentWithRevision($options): string
    {
        $fileName = $options['fileName'];
        $documentId = $options['documentId'];
        $revisionId = $options['revisionId'];
        $defaultTeam = $options['defaultTeam'];

        $this->createDocumentBean($fileName, [
            'documentId' => $documentId,
            'revisionId' => $revisionId,
            'defaultTeam' => $defaultTeam,
        ]);

        $this->createRevisionBean($documentId, [
            'revisionId' => $revisionId,
            'fileName' => $fileName,
            'defaultTeam' => $defaultTeam,
        ]);

        return $documentId;
    }

    /**
     * Creates a document bean
     *
     * @param string filename
     * @param array options
     */
    protected function createDocumentBean(string $fileName, array $options): void
    {
        global $current_user;
        $uploadFolder = 'upload://';

        $id = $options['documentId'];
        $document = new \Document();
        $document->name = $fileName;

        $document->is_merged_c = 1;
        $document->filename = $fileName;
        $document->filename_file = $uploadFolder . $id;
        $document->document_name = $fileName;
        $document->id = $id;
        $document->document_revision_id = $options['revisionId'];
        $document->new_with_id = true;
        $document->created_by = $current_user->id;
        $document->assigned_user_id = $current_user->id;
        $document->team_id = $options['defaultTeam'];
        $document->modified_user_id = $current_user->id;
        $document->save();
    }

    /**
     * Adds a record to a given relationship
     *
     * @param Bean $bean - The current record
     * @param string $relationshipModule
     * @param string $relationshipId
     */
    private function addToRelationship(\SugarBean $bean, string $relationshipModule, string $relationshipId): void
    {
        foreach ($bean->field_defs as $fieldName => $def) {
            //if the field doesn't have a relationship def. It is not a rel/link field.
            if (!isset($def['relationship'])) {
                continue;
            }

            $relationship = $this->getRelationshipName($def, $relationshipModule, $bean);
            if ($bean->load_relationship($relationship)) {
                $bean->{$relationship}->add($relationshipId);
            }
        }
    }

    /**
     * Gets the name of the relationship given the defintion of a link field and a module
     *
     * @param array fieldDef
     * @param string relationshipModule
     * @param Sugarbean bean
     *
     * @return string|null
     */
    private function getRelationshipName(array $linkDef, string $relationshipModule, \SugarBean $bean): ?string
    {
        $relationshipName = null;
        $rel = SugarRelationshipFactory::getInstance()->getRelationship($linkDef['relationship']);

        if ($rel) {
            $lhsModule = $rel->getLHSModule();
            $rhsModule = $rel->getRHSModule();

            if ($lhsModule === $relationshipModule || $rhsModule === $relationshipModule) {
                $bean->load_relationship($linkDef['relationship'])
                    ? $relationshipName = $linkDef['relationship'] :
                    ($bean->load_relationship($linkDef['name']) ? $relationshipName = $linkDef['name'] : $relationshipName = null);
            }
        }

        return $relationshipName;
    }

    /**
     * Created the relationship between a record and document
     *
     * @param string module
     * @param string id
     * @param string docId
     */
    private function addToDocumentRelationship(string $module, string $id, string $docId): void
    {
        $bean = BeanFactory::retrieveBean($module, $id);
        $this->addToRelationship($bean, 'Documents', $docId);
    }
}
