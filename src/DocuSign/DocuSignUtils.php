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

namespace Sugarcrm\Sugarcrm\DocuSign;

use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\CloudDrive\DriveFacade;

/**
 * DocuSign Utils
 */
class DocuSignUtils
{
    /**
     * Default global values
     *
     * @var array
     */
    protected static array $defaultGlobals = [
        'recipientSelection' => 'dont_show',
    ];

    /**
     * Return global settings of DocuSign
     *
     * @method getDocusignGlobalConfigs
     * @return Array
     */
    public static function getDocusignGlobalConfigs(): array
    {
        $administrationBean = new \Administration();
        $administrationBean->retrieveSettings('Docusign');

        if (array_key_exists('Docusign_GlobalSettings', $administrationBean->settings)) {
            $docusignGlobalConfigs = $administrationBean->settings['Docusign_GlobalSettings'];
            $docusignGlobalConfigs = unserialize(base64_decode($docusignGlobalConfigs), ['allowed_classes' => false]);
        } else {
            $docusignGlobalConfigs = self::$defaultGlobals;
        }

        return (array)$docusignGlobalConfigs;
    }

    /**
     * Save global docusign configs
     *
     * @param Array $docusignGlobalConfigs
     */
    public static function saveGlobalDocusignConfigs(array $docusignGlobalConfigs)
    {
        $administrationBean = new \Administration();
        $administrationBean->saveSetting(
            'Docusign',
            'GlobalSettings',
            base64_encode(serialize($docusignGlobalConfigs))
        );

        self::refreshMetadataCache();
    }

    /**
     * refresh metadata cache
     *
     */
    protected static function refreshMetadataCache()
    {
        \MetaDataManager::refreshSectionCache(\MetaDataManager::MM_CONFIG);
    }

    /**
     * Gets the DocuSign connector properties
     *
     * @return array
     */
    public static function getDocuSignOauth2Config()
    {
        $config = [];
        require \SugarAutoLoader::existingCustomOne(
            'modules/Connectors/connectors/sources/ext/eapm/docusign/config.php'
        );

        return $config;
    }

    /**
     * Download completed document and link it to the record
     *
     * @param \DocuSignEnvelope $envelopeBean
     * @return mixed
     */
    public static function downloadCompletedDocument(\DocuSignEnvelope $envelopeBean)
    {
        $extApi = new \ExtAPIDocuSign();

        $options = [
            'envelopeId' => $envelopeBean->envelope_id,
        ];

        try {
            $documentInfo = $extApi->getCompletedDocumentInfo($options);
            if (isset($documentInfo['status']) && $documentInfo['status'] === 'error') {
                throw new \Exception($documentInfo['message']);
            }

            $documentName = $documentInfo['documentName'] . ' - ' .
                translate('LBL_DOCUMENT_COMPLETED', 'DocuSignEnvelopes');
            $documentBean = self::createDocumentInSugar(
                $documentName,
                $documentInfo['body'],
                $envelopeBean,
                $documentInfo['completedDateTime'],
            );
            if (is_array($documentBean)) {
                return [
                    'status' => 'error',
                    'message' => $documentBean['error'],
                ];
            }
            $envelopeBean->document_id = $documentBean->id;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        if (!empty($envelopeBean->cloud_path) &&
            !empty($envelopeBean->cloud_service_type) &&
            $envelopeBean->cloud_service_type === 'dropbox' &&
            isset($documentName)) {
            self::updateCloudPath($envelopeBean, $documentName);
        }
        $envelopeBean->save();

        if (!empty($envelopeBean->parent_type) && !empty($envelopeBean->parent_id)) {
            $parentBean = \BeanFactory::retrieveBean($envelopeBean->parent_type, $envelopeBean->parent_id);
            self::addToRelationship($parentBean, 'Documents', $documentBean->id);
        }

        return true;
    }

    /**
     * Creates a Document record in Sugar
     *
     * @method createDocumentInSugar
     * @param string $docName
     * @param string $docPdfBytes
     * @param \DocuSignEnvelope $envelopeBean
     * @param string $completedDateTime
     * @return \Document|Array Document bean or array with the error thown
     */
    public static function createDocumentInSugar(
        string            $docName,
        string            $docPdfBytes,
        \DocuSignEnvelope $envelopeBean,
        string            $completedDateTime = ''
    ) {

        global $log;
        try {
            $revisionId = Uuid::uuid4();

            $doc = new \Document();
            $doc->document_name = $docName;
            $doc->filename = $docName . '.pdf';//all ds completed documents are PDFs
            $doc->doc_type = 'Sugar';
            $doc->team_id = $envelopeBean->team_id;
            $doc->team_set_id = $envelopeBean->team_set_id;
            $doc->assigned_user_id = $envelopeBean->assigned_user_id;
            if (!empty($completedDateTime)) {
                global $timedate;
                $completedDateTimeObj = new \DateTime($completedDateTime);
                $completedDate = $completedDateTimeObj->format($timedate->dbDayFormat);

                $doc->active_date = $completedDate;//publish date
            }
            $doc->document_revision_id = $revisionId;
            $doc->save();

            $uploadFile = new \UploadFile('completed_doc_file');
            $uploadFile->file = $docPdfBytes;
            $uploadFile->use_soap = true;//needed to make the final move

            $docRevision = new \DocumentRevision();
            $docRevision->id = $revisionId;
            $docRevision->new_with_id = true;
            $docRevision->filename = $docName . '.pdf';//all ds completed documents are PDFs
            $docRevision->file_mime_type = 'application/pdf';
            $docRevision->file_ext = 'pdf';
            $docRevision->doc_type = 'Sugar';
            $docRevision->revision = 1;
            $docRevision->document_id = $doc->id;
            $docRevision->save();

            $uploadFile->final_move($docRevision->id);
        } catch (\Exception $ex) {
            $exMessage = $ex->getMessage();
            $log->error('DocuSign Exception: ' . $exMessage);
            return [
                'error' => $exMessage,
            ];
        }

        return $doc;
    }

    /**
     * Fix dropbox case
     *
     * Dropbox integration needs the last item to be the file name
     */
    private static function updateCloudPath(\SugarBean &$envelopeBean, $documentName)
    {
        $cloudPath = json_decode($envelopeBean->cloud_path, true);
        $lastItemInPath = $cloudPath[safeCount($cloudPath) - 1];
        if (isset($lastItemInPath['folderId'])) {
            $cloudPath[] = ['name' => "{$documentName}.pdf"];
            $envelopeBean->cloud_path = json_encode($cloudPath);
        }
    }

    /**
     * Adds a record to a given relationship
     *
     * @param \SugarBean $bean - The current record
     * @param string $relatedModule
     * @param string $relatedId
     */
    private static function addToRelationship(\SugarBean $bean, string $relatedModule, string $relatedId): void
    {
        foreach ($bean->field_defs as $fieldName => $def) {
            //if the field doesn't have a relationship def. It is not a rel/link field.
            if (!isset($def['relationship'])) {
                continue;
            }

            $relationship = self::getRelationshipName($def, $relatedModule, $bean);
            if ($bean->load_relationship($relationship)) {
                $bean->{$relationship}->add($relatedId);
            }
        }
    }

    /**
     * Gets the name of the relationship given the defintion of a link field and a module
     *
     * @param array fieldDef
     * @param string relatedModule
     * @param \Sugarbean bean
     *
     * @return string|null
     */
    private static function getRelationshipName(array $linkDef, string $relatedModule, \SugarBean $bean): ?string
    {
        $relationshipName = null;
        $rel = \SugarRelationshipFactory::getInstance()->getRelationship($linkDef['relationship']);

        if ($rel) {
            $lhsModule = $rel->getLHSModule();
            $rhsModule = $rel->getRHSModule();

            if ($lhsModule === $relatedModule || $rhsModule === $relatedModule) {
                $bean->load_relationship($linkDef['relationship']) ?
                    $relationshipName = $linkDef['relatonship'] :
                    ($bean->load_relationship($linkDef['name']) ?
                        $relationshipName = $linkDef['name'] : $relationshipName = null);
            }
        }

        return $relationshipName;
    }

    /**
     * Check if user has EAPM for DocuSign
     *
     * @return bool
     */
    public static function checkEAPM(): bool
    {
        $extApi = new \ExtAPIDocuSign();
        $userEAPM = $extApi->getUserEAPM();

        return $userEAPM instanceof \EAPM;
    }

    /**
     * Upload document in cloud
     *
     * @param \DocuSignEnvelope $envelopeBean
     * @param string $documentId
     * @return mixed
     */
    public static function uploadDocumentInCloud(\DocuSignEnvelope $envelopeBean, string $documentId)
    {
        if (empty($envelopeBean->cloud_service_type)) {
            return;
        }

        $cloudServiceName = $envelopeBean->cloud_service_type;
        $driveId = $envelopeBean->driveId;

        $documentBean = \BeanFactory::getBean('Documents', $documentId);
        $drive = new DriveFacade($cloudServiceName);

        $documentRevisionID = $documentBean->document_revision_id;
        $revisionBean = \BeanFactory::retrieveBean('DocumentRevisions', $documentRevisionID);
        $fileName = $revisionBean->filename;

        $uploadOptions = [
            'documentBean' => $documentBean,
            'fileName' => $fileName,
        ];
        if ($cloudServiceName === 'dropbox') {
            $uploadOptions['folderPath'] = $envelopeBean->cloud_path;
            $uploadOptions['filePath'] = $envelopeBean->cloud_path;
        } else {
            if ($cloudServiceName === 'sharepoint') {
                $uploadOptions['driveId'] = $envelopeBean->driveId;
            }
            $uploadOptions['pathId'] = $envelopeBean->cloud_path;
        }

        $res = $drive->uploadFile($uploadOptions);
        if (is_array($res) && isset($res['success'])) {
            if ($res['success'] === true) {
                return true;
            } else {
                return [
                    'status' => 'error',
                    'message' => $res['message'],
                ];
            }
        } else {
            return $res;
        }
    }
}
