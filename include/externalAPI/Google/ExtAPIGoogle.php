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

use Doctrine\DBAL\Driver\Exception;
use Google\Client;
use Google\Exception as Google_Exception;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use GuzzleHttp\Psr7\Request;

require_once 'vendor/Zend/Gdata/Contacts.php';

/**
 * ExtAPIGoogle
 */
class ExtAPIGoogle extends ExternalAPIBase implements WebDocument
{
    public $supportedModules = ['Documents', 'Import'];
    public $authMethod = 'oauth2';
    public $connector = 'ext_eapm_google';

    public $useAuth = true;
    public $requireAuth = true;

    protected $scopes = [
        'https://www.googleapis.com/auth/contacts.readonly',
        Drive::DRIVE_READONLY,
        Drive::DRIVE_FILE,
        Drive::DRIVE,
        Drive::DRIVE_APPDATA,
        Drive::DRIVE_METADATA,
    ];

    public $docSearch = true;
    public $needsUrl = false;
    public $sharingOptions = null;

    public const APP_STRING_ERROR_PREFIX = 'ERR_GOOGLE_API_';

    public function getClient()
    {
        $client = $this->getGoogleClient();
        $eapm = EAPM::getLoginInfo('Google');
        if ($eapm && !empty($eapm->api_data)) {
            $client->setAccessToken($eapm->api_data);
            if ($client->isAccessTokenExpired()) {
                $this->refreshToken($client);
            }
        }

        return $client;
    }

    protected function refreshToken(Client $client)
    {
        $refreshToken = $client->getRefreshToken();
        if ($refreshToken) {
            try {
                $client->refreshToken($refreshToken);
            } catch (\Exception $e) {
                $GLOBALS['log']->error($e->getMessage());

                return;
            }

            $token = $client->getAccessToken();
            $this->saveToken($token);
        }
    }

    protected function saveToken($accessToken)
    {
        global $current_user;
        $bean = EAPM::getLoginInfo('Google');
        if (!$bean) {
            $bean = BeanFactory::newBean('EAPM');
            $bean->assigned_user_id = $current_user->id;
            $bean->application = 'Google';
            $bean->validated = true;
        }

        $bean->api_data = json_encode($accessToken);
        $bean->save();
    }

    public function revokeToken()
    {
        $client = $this->getClient();

        try {
            $client->revokeToken();
        } catch (\Exception $e) {
            return false;
        }

        $eapm = EAPM::getLoginInfo('Google');
        if ($eapm) {
            $eapm->mark_deleted($eapm->id);
        }

        return true;
    }

    protected function getGoogleClient()
    {
        $config = $this->getGoogleOauth2Config();

        $client = new Client();
        $client->setClientId($config['properties']['oauth2_client_id']);
        $client->setClientSecret($config['properties']['oauth2_client_secret']);
        $client->setRedirectUri($config['redirect_uri']);
        $client->setAccessType('offline');
        $client->setScopes($this->scopes);
        $client->setPrompt('consent');

        return $client;
    }

    protected function getGoogleOauth2Config()
    {
        $config = [];
        require SugarAutoLoader::existingCustomOne('modules/Connectors/connectors/sources/ext/eapm/google/config.php');
        $config['redirect_uri'] = rtrim(SugarConfig::getInstance()->get('site_url'), '/')
            . '/index.php?module=EAPM&action=GoogleOauth2Redirect';

        return $config;
    }

    public function authenticate($code)
    {
        $client = $this->getClient();
        try {
            $client->fetchAccessTokenWithAuthCode($code);
        } catch (\Exception $e) {
            $GLOBALS['log']->error($e->getMessage());

            return false;
        }

        $token = $client->getAccessToken();
        if ($token) {
            $this->saveToken($token);
        }

        return $token === null ? null : json_encode($token);
    }

    public function uploadDoc($bean, $fileToUpload, $docName, $mimeType)
    {
        $client = $this->getClient();
        $service = new Drive($client);

        $file = new DriveFile();
        $file->setName($docName);
        $file->setDescription($bean->description);

        try {
            $createdFile = $service->files->create($file, [
                'data' => file_get_contents($fileToUpload),
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink',
            ]);
        } catch (Google_Exception $e) {
            return [
                'success' => false,
                'errorMessage' => $GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL'],
            ];
        }

        $bean->doc_id = $createdFile->id;
        $bean->doc_url = $createdFile->webViewLink;

        return [
            'success' => true,
        ];
    }

    /**
     * Uploads a file to a certain folder
     *
     * @param mixed $bean
     * @param mixed $fileToUpload
     * @param mixed $folderId
     * @return array
     */
    public function uploadDocToFolder($bean, $fileToUpload, $folderId)
    {
        $client = $this->getClient();
        $service = new Drive($client);

        $file = new DriveFile();
        $file->setName($bean->filename);
        $file->setDescription($bean->description);
        $file->setParents([$folderId]);

        try {
            $createdFile = $service->files->create($file, [
                'data' => sugar_file_get_contents($fileToUpload),
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink',
            ]);
        } catch (Google_Exception $e) {
            $GLOBALS['log']->fatal($e->getMessage());
            return [
                'success' => false,
                'errorMessage' => $GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL'],
            ];
        }

        $bean->doc_id = $createdFile->id;
        $bean->doc_url = $createdFile->webViewLink;

        return [
            'success' => true,
            'message' => 'LBL_FILE_UPLOADED',
        ];
    }

    /**
     * Uploads a file to drive
     *
     * @param string $fileName
     * @param string $parentId
     * @param mixed $data
     * @return array
     */
    public function uploadFileToFolder($fileName, $parentId, $data): array
    {
        $client = $this->getClient();
        $service = new Drive($client);

        $file = new DriveFile();
        $file->setName($fileName);
        $file->setParents([$parentId]);

        try {
            $service->files->create($file, [
                'data' => sugar_file_get_contents($data['tmp_name']),
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink',
            ]);
        } catch (Google_Exception $e) {
            $GLOBALS['log']->fatal($e->getMessage());
            return [
                'success' => false,
                'errorMessage' => $GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL'],
            ];
        }

        return [
            'success' => true,
            'message' => 'LBL_FILE_UPLOADED',
        ];
    }

    /**
     * Used to download a file
     *
     * @param mixed $documentId
     * @param mixed $documentFormat
     * @return (true|string)[]
     */
    public function downloadDoc($documentId, $documentFormat)
    {
        $client = $this->getClient();
        $service = new Drive($client);

        $exportableTypes = [
            'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.google-apps.document' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        try {
            if (in_array($documentFormat, array_keys($exportableTypes))) {
                $response = $service->files->export($documentId, $exportableTypes[$documentFormat], ['alt' => 'media']);
                $content = $response->getBody()->getContents();
            } else {
                $response = $service->files->get($documentId, ['alt' => 'media',]);
                $content = $response->getBody()->getContents();
            }
        } catch (Google_Exception $e) {
            // we should be trying again with "get".
            // it might have failed from the export and work with get
            try {
                $response = $service->files->get($documentId, ['alt' => 'media',]);
                $content = $response->getBody()->getContents();
            } catch (Google_Exception $ex) {
                throw $ex;
            }
        }

        return [
            'success' => true,
            'message' => base64_encode($content),
        ];
    }

    /**
     * Delete document from drive
     *
     * @param string $documentId
     * @return array
     */
    public function deleteDoc($documentId)
    {
        $client = $this->getClient();
        $service = new Drive($client);
        try {
            $response = $service->files->delete($documentId);
        } catch (Google_Exception $e) {
            throw $e;
        }

        return [
            'success' => true,
            'message' => $response,
        ];
    }

    public function shareDoc($documentId, $emails)
    {
    }

    public function searchDoc($keywords, $flushDocCache = false)
    {
        global $sugar_config;

        $client = $this->getClient();
        $drive = new Drive($client);

        $options = [
            'pageSize' => $sugar_config['list_max_entries_per_page'],
            'fields' => 'files(id,name,webViewLink,modifiedTime)',
        ];

        $queryString = 'trashed = false ';
        if (!empty($keywords)) {
            $queryString .= "and name contains '{$keywords}'";
        }
        $options['q'] = $queryString;

        try {
            $files = $drive->files->listFiles($options);
        } catch (Google_Exception $e) {
            $GLOBALS['log']->fatal('Unable to retrieve google drive files:' . $e);
            return false;
        }

        $results = [];
        foreach ($files as $file) {
            $results[] = [
                'url' => $file->webViewLink,
                'name' => $file->name,
                'date_modified' => $file->modifiedTime,
                'id' => $file->id,
            ];
        }

        return $results;
    }

    /**
     * Retrieve data about a file
     *
     * @param string $fileId
     */
    public function retrieveFileInfo($fileId)
    {
        $client = $this->getClient();
        $service = new Drive($client);
        $file = $service->files->get($fileId, ['fields' => '*']);
        return $file;
    }

    /**
     * Create a folder on drive
     *
     * @param string $name
     * @param string $parentId
     * @return DriveFile|null
     */
    public function createFolder(string $name, string $parentId): ?DriveFile
    {
        $client = $this->getClient();
        $service = new Drive($client);
        $file = new DriveFile();
        $file->setName($name);
        $file->setMimeType('application/vnd.google-apps.folder');
        $file->setParents([$parentId]);

        try {
            $createdFile = $service->files->create($file);
            return $createdFile;
        } catch (Google_Exception $e) {
            $GLOBALS['log']->fatal($e->getMessage());
            return [
                'success' => false,
                'errorMessage' => $GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL'],
            ];
        }

        return null;
    }
}
