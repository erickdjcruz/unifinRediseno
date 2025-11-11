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

namespace Sugarcrm\Sugarcrm\CloudDrive\Drives;

use ExtAPIDropbox;
use DropBoxClient;
use EAPM;
use InvalidArgumentException;
use Sugarcrm\Sugarcrm\CloudDrive\Drive;
use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;
use Sugarcrm\Sugarcrm\CloudDrive\Model\DriveItemMapper;
use SugarException;

class DropboxDrive extends Drive
{
    /**
     * Lists files and folders
     *
     * @param array @options
     *
     * @return array
     */
    public function listFolders($options): array
    {
        global $sugar_config;

        $nextPageToken = isset($options['nextPageToken']) ? $options['nextPageToken'] : null;
        $sharedWithMe = isset($options['sharedWithMe']) ? $options['sharedWithMe'] : null;
        $folderPath = array_key_exists('folderPath', $options) ? $options['folderPath'] : null;
        $limit = isset($options['limit']) ? $options['limit'] : null;

        $dropboxApi = $this->getExternalApiClient();

        if (safeCount($folderPath) > 1) {
            $path = $this->parseFolderPath($folderPath);
        }

        $data = [
            'path' => $path ?? '',
            'limit' => $limit ?? $sugar_config['list_max_entries_per_page'],
        ];

        if ($nextPageToken) {
            $data['cursor'] = $nextPageToken;
        }

        if ($sharedWithMe) {
            if ($data['path'] === '') {
                $sharedFolderData = [
                    'limit' => $data['limit'],
                ];
                $responseFolder = $dropboxApi->listSharedFolder($sharedFolderData);
                $responseFiles = $dropboxApi->listSharedFiles($sharedFolderData);

                if (isset($responseFolder['error']) || isset($responseFiles['error'])) {
                    $response = [
                        'error' => $responseFolder['error'] ?? $responseFiles['error'],
                    ];
                } else {
                    $combinedEntries = array_merge($responseFolder['entries'], $responseFiles['entries']);
                    $response = [
                        'entries' => $combinedEntries,
                    ];
                }
            } else {
                $response = $response = $dropboxApi->listFolder($data);
            }
        } else {
            $response = $dropboxApi->listFolder($data);
        }

        if (isset($response['error'])) {
            return $this->handleErrors($response);
        }

        $mapper = new DriveItemMapper($response['entries'], DriveType::DROPBOX);
        $mappedData = $mapper->mapToArray();

        if (!$sharedWithMe) {
            $mappedData = array_filter($mappedData, function ($item) {
                return $item->shared === null;
            });
        }

        return [
            'files' => $mappedData,
            'nextPageToken' => (array_key_exists('has_more', $response) && $response['has_more']) ? $response['cursor'] : null,
        ];
    }

    /**
     * Lists files and folders
     *
     * @param array @options
     *
     * @return array
     */
    public function getFile($options): array
    {
        return $this->listFolders($options);
    }

    /**
     * Lists files and folders
     *
     * @param array @options
     *
     * @return array
     */
    public function listFiles($options): array
    {
        return $this->listFolders($options);
    }

    /**
     * Handles dropbox api error responses
     *
     * @param array @error
     *
     * @return array
     */
    protected function handleErrors($error): array
    {
        $errorTag = $error['error'];

        if ($errorTag['.tag'] === 'expired_access_token') {
            return [
                'success' => false,
                'message' => 'LBL_CHECK_DROPBOX_CONNECTION',
            ];
        }

        if ($errorTag['.tag'] === 'path' && $errorTag['path']['.tag'] === 'not_found') {
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Creates a dropbox folder
     *
     * @param array @options
     *
     * @return array
     */
    public function createFolder($options): array
    {
        $folderPath = $options['folderPath'];
        $dropboxApi = $this->getExternalApiClient();

        if (safeCount($folderPath) > 1) {
            $path = $this->parseFolderPath($folderPath);
        }

        $data = [
            'path' => $path ?? '',
        ];

        $response = $dropboxApi->createFolder($data);
        $folderMetadata = $response['metadata'];

        return [
            'id' => $folderMetadata['id'],
        ];
    }

    /**
     * Download a dropbox file
     *
     * @param array @options
     *
     * @return array
     */
    public function downloadFile($options): array
    {
        $fileId = $options['fileId'];
        $dropboxApi = $this->getExternalApiClient();

        $content = $dropboxApi->downloadFile($fileId);

        if (!$content) {
            return [
                'success' => false,
                'message' => 'LBL_INVALID_DRIVE_FILE',
            ];
        }

        return [
            'success' => true,
            'content' => base64_encode($content),
        ];
    }

    /**
     * Delete a dropbox file
     *
     * @param array @options
     *
     * @return array
     */
    public function deleteFile($options): array
    {
        $folderPath = $options['folderPath'];
        $dropboxApi = $this->getExternalApiClient();

        if (safeCount($folderPath) > 1) {
            $path = $this->parseFolderPath($folderPath);
        }

        $data = [
            'path' => $path ?? '',
        ];

        $response = $dropboxApi->deleteFile($data);
        $metadata = $response['metadata'];

        return [
            'id' => $metadata['id'],
        ];
    }

    /**
     * Parser for the root folder path
     *
     * @param array @folderPath
     *
     * @return string
     */
    protected function parseFolderPath($folderPath): string
    {
        $path = '';

        foreach ($folderPath as $folderData) {
            $name = $folderData['name'];
            if ($name !== 'My files' && $name !== 'Shared') {
                $path .= "/{$name}";
            }
        }

        return $path;
    }

    /**
     * Download a dropbox file
     *
     * @param array @options
     *
     * @return array
     */
    public function getSharedLink($options): array
    {
        $folderPath = $options['folderPath'];
        $dropboxApi = $this->getExternalApiClient();

        if (safeCount($folderPath) > 1) {
            $path = $this->parseFolderPath($folderPath);
        }

        $data = [
            'path' => $path ?? '',
        ];

        $response = $dropboxApi->getSharedLink($data);

        $url = isset($response['url']) ? $response['url'] : null;

        if (isset($response['error'])) {
            $sharedLink = $response['error']['shared_link_already_exists'];
            $linkMetadata = $sharedLink['metadata'];
            $url = $linkMetadata['url'];
        }

        return [
            'url' => $url,
        ];
    }

    /**
     * Uploads a file to dropbox
     *
     * @param array @options
     *
     * @return array
     */
    public function uploadFile($options): array
    {
        $folderPath = $options['folderPath'];
        $folderPath = is_string($folderPath) ? json_decode($folderPath, true) : $folderPath;
        $dropboxApi = $this->getExternalApiClient();
        $path = '/' . $options['fileName'];

        if (safeCount($folderPath) > 1) {
            $path = $this->parseFolderPath($folderPath);
        }

        if ($options['data']) {
            $data = $options['data'];
            $filePath = $data['tmp_name'];
        } else {
            $filePath = $this->getFilePath($options['documentBean']);
        }

        $content = sugar_file_get_contents($filePath);
        $data = [
            'content' => $content,
        ];

        $response = $dropboxApi->uploadFile($path, $data);

        if (isset($response['error'])) {
            return $this->handleErrors($response);
        }

        if (is_string($response) && strpos($response, 'Error') >= 0) {
            return [
                'success' => false,
                'message' => $response,
            ];
        }

        return [
            'success' => true,
            'message' => 'LBL_FILE_UPLOADED',
        ];
    }

    /**
     * Uploads a larget file to the drive
     *
     * @param array $options
     * @return array
     */
    public function uploadLargeFile(array $options): array
    {
        return $this->uploadFile($options);
    }

    /**
     * Retrieves the dropbox client
     *
     * @return array|DropboxClient
     */
    public function getClient()
    {
        $api = new ExtAPIDropbox();
        $client = $api->getClient();
        $eapm = EAPM::getLoginInfo('Dropbox');

        if (empty($eapm->api_data) || $client->isAccessTokenExpired()) {
            return [
                'success' => false,
                'message' => 'LBL_CHECK_DROPBOX_CONNECTION',
            ];
        }

        return $client;
    }

    /**
     * Retrieves the external api client
     */
    public function getExternalApiClient(): ExtAPIDropbox
    {
        return new ExtAPIDropbox();
    }
}
