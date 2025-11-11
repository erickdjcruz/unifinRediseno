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

use EAPM;
use ExtAPIMicrosoft;
use Sugarcrm\Sugarcrm\CloudDrive\Drive;
use Microsoft\Graph\Model;
use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;
use Sugarcrm\Sugarcrm\CloudDrive\Model\DriveItemMapper;
use SchedulersJob;
use Sugarcrm\Sugarcrm\Util\Uuid;
use SugarJobQueue;

class OneDrive extends Drive
{
    /**
     * List folders in a drive
     *
     * @param array $options
     * @return FileList|bool
     */
    public function listFolders(array $options)
    {
        $response = null;
        $driveId = isset($options['driveId']) ? $options['driveId'] : null;
        $parentId = $options['parentId'] ?? null;
        $sharedWithMe = $options['sharedWithMe'];
        $folderName = isset($options['folderName']) ? $options['folderName'] : null;
        $folderParent = isset($options['folderParent']) ? $options['folderParent'] : null;

        $client = $this->getClient();

        if (is_array($client) && !$client['success']) {
            return $client;
        }

        $url = $this->getListUrl($sharedWithMe, $driveId, $parentId);

        // add filter to show only folders
        if (!$sharedWithMe) {
            $url .= '?filter=folder ne null';
        }
        $pageToken = isset($options['pageToken']) ? $options['pageToken'] : null;

        if ($pageToken) {
            $url = $pageToken;
        }

        if ($folderName) {
            if ($driveId) {
                $url = "/drives/{$driveId}/root/search(q='{$folderName}')";
            } else {
                // in case we are just searching for a specific folder and we only have the name
                $url = "/me/drive/search(q='{$folderName}')";
                if ($sharedWithMe) {
                    try {
                        return $this->searchSharedFiles($folderName);
                    } catch (\Exception $e) {
                        // retry searching on local folder
                        $options['sharedWithMe'] = null;
                        return $this->listFolders($options);
                    }
                }
            }
        }

        $request = $client->createCollectionRequest('GET', $url);

        try {
            $response = $request->execute();
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() === 400 || $e->getResponse()->getStatusCode() === 403) {
                $options['driveId'] = null;
                return $this->listFolders($options);
            }
        } catch (\Exception $e) {
            // try again with no drive id
            $options['driveId'] = null;
            return $this->listFolders($options);
        }

        $data = $response->getResponseAsObject(Model\DriveItem::class);

        $filteredData = [];

        foreach ($data as $item) {
            $parentReference = $item->getParentReference();
            if ($parentReference && !is_null($parentReference->getId())) {
                $parent = $this->getFile([
                    'fileId' => $parentReference->getId(),
                    'driveId' => $parentReference->getDriveId(),
                    'select' => 'name',
                ]);
            }

            // we don't need parents to match if folderParent is root
            if ($folderParent === 'root' && is_array($parent) && $parent['success'] === false) {
                $filteredData[] = $item;
            } elseif ($item->getFolder() && $item->getName() === $folderName && $parent->name === $folderParent) {
                $filteredData[] = $item;
            }
        }

        $nextLink = $response->getNextLink();
        $driveItems = $folderName ? $filteredData : $data;
        $mapper = new DriveItemMapper($driveItems, DriveType::ONEDRIVE);
        $mappedData = $mapper->mapToArray();

        return [
            'files' => $mappedData,
            'nextPageToken' => $nextLink,
        ];
    }

    /**
     * Create a folder on the drive
     *
     * @param array $options
     * @return null|string
     * @throws InvalidArgumentException
     */
    public function createFolder(array $options): ?array
    {
        $folderName = $options['name'];
        $parent = $options['parent'] ?? 'root';
        $driveId = $options['driveId'];

        if (!$folderName) {
            return null;
        }

        if ($parent === 'root') {
            $url = "/me/drive/items/{$parent}/children";
        } else {
            $url = "/drives/{$driveId}/items/{$parent}/children";
        }

        $client = $this->getClient();
        $request = $client->createRequest('POST', $url)->setReturnType(Model\DriveItem::class);
        $request->attachBody([
            'name' => $folderName,
            'folder' => ['childCount' => '0'],
            '@microsoft.graph.conflictBehavior' => 'rename',
        ]);

        try {
            $response = $request->execute();
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $errorMessage = json_decode($e->getResponse()->getBody(true));
            throw new \Exception($errorMessage->error->message);
        }

        // $this->syncDrive($options);
        $parentReference = $response->getParentReference();
        $parentDriveId = $parentReference->getDriveId();
        $parentId = $response->getId();

        return [
            'driveId' => $parentDriveId,
            'id' => $parentId,
        ];
    }

    /**
     * List files in a folder
     *
     * @param array $options
     * @return void
     */
    public function listFiles(array $options)
    {
        global $sugar_config;

        $folderId = $options['folderId'] ?? 'root';
        $sharedWithMe = $options['sharedWithMe'] ?? false;
        $driveId = isset($options['driveId']) ? $options['driveId'] : null;
        $sortOptions = $options['sortOptions'] ?? false;

        $top = $sugar_config['list_max_entries_per_page'];

        $url = $this->getListUrl($sharedWithMe, $driveId, $folderId);

        if (isset($options['nextPageToken']) && $options['nextPageToken']) {
            $url = $options['nextPageToken'];
            // limit result set
            $url = $this->urlHasParam($url, '$top') ? $url : $url . '&$top=' . $top;
        } else {
            // limit result set
            $url .= '?$top=' . $top;
        }

        if ($sortOptions) {
            $direction = $sortOptions['direction'];
            $field = $sortOptions['fieldName'];
            $url .= "&orderby={$field} {$direction}";
        }

        $client = $this->getClient();
        $request = $client->createRequest('GET', $url);
        $response = $request->execute();

        $data = $response->getResponseAsObject(Model\DriveItem::class);
        $nextLink = $response->getNextLink();

        $mapper = new DriveItemMapper($data, DriveType::ONEDRIVE);
        $mappedData = $mapper->mapToArray();
        return [
            'files' => $mappedData,
            'nextPageToken' => $nextLink,
        ];
    }

    /**
     * Get file data from drive
     *
     * @param array $options
     * @return mixed
     */
    public function getFile(array $options)
    {
        $fileId = $options['fileId'];
        $driveId = $options['driveId'];
        $select = isset($options['select']) ? $options['select'] : null;
        $client = $this->getClient();

        if (is_array($client) && !$client['success']) {
            return $client;
        }

        if (!$driveId) {
            $url = "/me/drive/items/{$fileId}";
        } else {
            $url = "/drives/{$driveId}/items/{$fileId}";
        }

        if (!is_null($select)) {
            $url .= "?select={$select}";
        }

        $request = $client->createRequest('GET', $url);

        try {
            $response = $request->execute();
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                return [
                    'success' => false,
                    'message' => 'LBL_MICROSOFT_PERMISSION_ERROR',
                ];
            }
            $errorMessage = json_decode($e->getResponse()->getBody(true));

            return [
                'success' => false,
                'message' => $errorMessage->error->message,
            ];
        }

        $data = $response->getResponseAsObject(Model\DriveItem::class);
        $mapper = new DriveItemMapper($data, DriveType::ONEDRIVE);
        $mappedData = $mapper->mapToDriveItem();
        return $mappedData;
    }

    /**
     *  Deletes a file from the drive
     *
     * @param array $options
     * @return null|array
     */
    public function deleteFile(array $options)
    {
        $client = $this->getClient();

        $fileId = $options['fileId'];
        $driveId = $options['driveId'];

        if (!$driveId) {
            $url = "/me/drive/items/{$fileId}";
        } else {
            $url = "/drives/{$driveId}/items/{$fileId}";
        }

        $request = $client->createRequest('DELETE', $url);
        $response = $request->execute();
        return $response->getBody();
    }

    /**
     * Download file from drive
     *
     * @param array $options
     * @return mixed
     */
    public function downloadFile(array $options)
    {
        $client = $this->getClient();

        $fileId = $options['fileId'];
        $driveId = $options['driveId'];

        if (!$driveId) {
            $url = "/me/drive/items/{$fileId}/content";
        } else {
            $url = "/drives/{$driveId}/items/{$fileId}/content";
        }

        $id = Uuid::uuid4();
        $request = $client->createRequest('GET', $url);
        $request->download("upload/{$id}");
        $content = sugar_file_get_contents("upload/{$id}");
        unlink("upload/{$id}");
        return [
            'success' => true,
            'content' => base64_encode($content),
        ];
    }

    /**
     * Gets the base url for getting files
     *
     * @param bool|null $sharedWithMe
     * @param string|null $driveId
     * @param string|null $parentId
     * @return string
     */
    private function getListUrl(?bool $sharedWithMe, ?string $driveId, ?string $parentId): string
    {
        $url = '/me/drive/root/children';

        if ($sharedWithMe) {
            if ($driveId && $parentId) {
                // handle navigation
                $url = "/drives/{$driveId}/items/{$parentId}/children";
            } else {
                $url = '/me/drive/sharedWithMe';
            }
        }

        // add support for parent folder for my drive
        if ($parentId && $parentId !== 'root' && !$sharedWithMe) {
            $url = "/me/drive/items/{$parentId}/children";
        }

        return $url;
    }

    /**
     * Get microsoft api client
     *
     */
    public function getClient()
    {
        $api = new ExtAPIMicrosoft();
        $client = $api->getClient();
        $eapm = EAPM::getLoginInfo('Microsoft');

        if (empty($eapm->api_data)) {
            return [
                'success' => false,
                'message' => 'LBL_CHECK_MICROSOFT_CONNECTION',
            ];
        }

        $token = $api->getAccessToken($eapm->id);
        $client->setAccessToken($token);

        return $client;
    }

    private function getAccessibleDriveIds()
    {
        $url = '/me/drive/sharedWithMe';
        $client = $this->getClient();
        $request = $client->createRequest('GET', $url);
        $response = $request->execute();

        $shared = $response->getResponseAsObject(Model\DriveItem::class);

        $driveIds = [];

        foreach ($shared as $driveItem) {
            $remoteItem = $driveItem->getRemoteItem();
            if (!$remoteItem) {
                continue;
            }
            $driveIds [] = $remoteItem->getParentReference()->getDriveId();
        }

        return array_unique($driveIds);
    }

    /**
     * Search all shared drives for a file
     * @param string $folderName
     * @return array
     * @todo - make this search smarter, because now it just returns all folders
     * with that name - mybe add a check br parent folder name
     *
     */
    private function searchSharedFiles(string $folderName)
    {
        $client = $this->getClient();
        $driveIds = $this->getAccessibleDriveIds();

        $files = [];

        foreach ($driveIds as $driveId) {
            $url = "/drives/{$driveId}/root/search(q='{$folderName}')";
            $request = $client->createCollectionRequest('GET', $url);
            $response = $request->execute();
            $data = $response->getResponseAsObject(Model\DriveItem::class);

            foreach ($data as $sharedItem) {
                if ($sharedItem->getName() === $folderName) {
                    $files [] = $sharedItem;
                }
            }
        }
        return [
            'files' => $files,
            'nextPageToken' => null,
        ];
    }

    /**
     * Checks if a url has a certain param
     *
     * @param string $url
     * @param string $param
     * @return bool
     */
    protected function urlHasParam(string $url, string $param): bool
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);

        if (isset($queryParams[$param])) {
            return true;
        }
        return false;
    }

    /**
     * Uploads a file to the drive
     *
     * @param array $options
     * @return array
     */
    public function uploadFile($options): array
    {
        $client = $this->getClient();

        $parentId = $options['parentId'] ?? $options['pathId'];
        $fileName = $options['fileName'];
        $driveId = $options['driveId'] ?? null;

        if ($options['data']) {
            $data = $options['data'];
            $filePath = $data['tmp_name'];
        } else {
            if (isset($options['filePath'])) {
                $filePath = $options['filePath'];
            } else {
                $filePath = $this->getFilePath($options['documentBean']);
            }
        }

        if (!$driveId) {
            $url = "/me/drive/items/{$parentId}:/{$fileName}:/content";
        } else {
            $url = "/drives/{$driveId}/items/{$parentId}:/{$fileName}:/content";
        }

        $request = $client->createRequest('PUT', $url);
        $request->attachBody(sugar_file_get_contents($filePath));
        $request->execute();

        return [
            'success' => true,
            'message' => 'LBL_FILE_UPLOADED',
        ];
    }

    /**
     * Uploads a large file.
     * @param array $options
     * @return array
     */
    public function uploadLargeFile($options): array
    {
        $client = $this->getClient();

        $parentId = $options['parentId'] ?? $options['pathId'];
        $fileName = $options['fileName'];
        $driveId = $options['driveId'] ?? null;

        if ($options['data']) {
            $data = $options['data'];
            $filePath = $data['tmp_name'];
            $fileSize = $data['size'];
        } else {
            $filePath = $options['filePath'];
            $fileSize = $options['fileSize'];
        }

        if (!$driveId) {
            $url = "/me/drive/items/{$parentId}:/{$fileName}:/createUploadSession";
        } else {
            $url = "/drives/{$driveId}/items/{$parentId}:/{$fileName}:/createUploadSession";
        }

        $request = $client->createRequest('POST', $url);
        $request->attachBody([
            'name' => $fileName,
            '@microsoft.graph.conflictBehavior' => 'rename',
            'fileSystemInfo' => [
                '@odata.type' => 'microsolft.graph.fileSystemInfo',
                'name' => $fileName,
            ],
        ]);
        $uploadSession = $request->execute();
        $uploadProperties = $uploadSession->getResponseAsObject(Model\DriveItem::class)->jsonSerialize();
        $uploadUrl = $uploadProperties['uploadUrl'];
        $uploadFile = new \UploadFile('filename_file');
        $fileId = Uuid::uuid4();
        $uploadFile->set_for_soap($fileId, sugar_file_get_contents($filePath));
        $uploadFile->final_move($fileId);
        $filePath = "upload://{$fileId}";

        return $this->uploadChunks($uploadUrl, $filePath, $fileName, $fileSize);
    }

    /**
     * Uploads chunks of the file in a schedule job
     * @param string $uploadUrl
     * @param string $filePath
     * @param GraphProxy $client
     * @param string $fileName
     * @param null|int $fileSize
     *
     * @return array
     */
    protected function uploadChunks(string $uploadUrl, string $filePath, string $fileName, ?int $fileSize): array
    {
        global $current_user;

        $payload = [
            'uploadUrl' => $uploadUrl,
            'filePath' => $filePath,
            'fileName' => $fileName,
            'fileSize' => $fileSize,
        ];

        $job = new SchedulersJob();
        $job->name = 'OneDriveUploadJob- ' . Uuid::uuid4();
        $job->data = base64_encode(serialize($payload));
        $job->target = 'class::OneDriveUploadJob';
        $job->assigned_user_id = $current_user->id;
        $jq = new SugarJobQueue();
        $jq->submitJob($job);

        return [
            'success' => true,
            'message' => 'LBL_LARGE_FILE_UPLOAD',
        ];
    }

    /**
     * Syncs the files on the drive
     * @param array $options
     * @return array
     */
    protected function syncDrive(array $options): array
    {
        $client = $this->getClient();
        $driveId = $options['driveId'] ?? null;

        if (!$driveId) {
            $url = '/me/drive/root/delta';
        } else {
            $url = "/drives/{$driveId}/root/delta";
        }

        $request = $client->createRequest('GET', $url);
        $request->execute();

        return [
            'success' => true,
        ];
    }
}
