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

use Microsoft\Graph\Model;
use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;
use Sugarcrm\Sugarcrm\CloudDrive\Model\DriveItemMapper;

class SharepointDrive extends OneDrive
{
    /**
     * List files in a folder
     *
     * @param array $options
     * @return void
     */
    public function listFiles(array $options)
    {
        $driveId = isset($options['driveId']) ? $options['driveId'] : null;
        $siteId = isset($options['siteId']) ? $options['siteId'] : null;
        $sortOptions = isset($options['sortOptions']) ? $options['sortOptions'] : null;

        $parentId = $options['parentId'] ?? null;
        $nextPageToken = array_key_exists('nextPageToken', $options) ? $options['nextPageToken'] : null;

        if (array_key_exists('folderId', $options)) {
            $parentId = $options['folderId'];
        }

        // if the request doesn't have a site id, it means we need to display sites
        if (!$siteId) {
            return $this->listSites();
        }

        //we're navigating into document libraries
        if ($siteId && !$driveId) {
            return $this->listDocumentLibraries($siteId);
        }

        if ($siteId && $driveId) {
            $onlyFolders = false;
            if (array_key_exists('onlyFolders', $options)) {
                $onlyFolders = $options['onlyFolders'];
            }
            return $this->listDriveFolders($driveId, $parentId, $nextPageToken, $onlyFolders, $sortOptions);
        }
    }

    /**
     * List folders in a drive
     *
     * @param array $options
     * @return FileList|bool
     */
    public function listFolders(array $options)
    {
        $options['onlyFolders'] = true;
        $files = $this->listFiles($options);
        // filter only folders
        return $files;
    }

    /**
     * Lists a sharepoint resource
     *
     * @param mixed $url
     * @return array
     */
    private function listSharepointResource($url)
    {
        $client = $this->getClient();

        if (is_array($client) && !$client['success']) {
            return $client;
        }
        $request = $client->createCollectionRequest('GET', $url);

        try {
            $response = $request->execute();
        } catch (\Exception $e) {
            $GLOBALS['log']->error($e->getMessage());
        }
        $data = $response->getResponseAsObject(Model\DriveItem::class);

        $nextLink = $response->getNextLink();

        $mapper = new DriveItemMapper($data, DriveType::SHAREPOINT);
        $mappedData = $mapper->mapToArray();

        return [
            'files' => $mappedData,
            'nextPageToken' => $nextLink,
        ];
    }

    /**
     * List sites
     * @return array
     */
    private function listSites()
    {
        $url = $this->getSitesUrl();
        $result = $this->listSharepointResource($url);
        $result['displayingSites'] = true;
        $result['displayingDocumentDrives'] = false;
        return $result;
    }

    /**
     * List Document drives
     *
     * @param string $siteId
     * @return array
     */
    private function listDocumentLibraries(string $siteId)
    {
        $url = $this->getDocumentLibrariesUrl($siteId);
        $result = $this->listSharepointResource($url);
        $result['displayingDocumentDrives'] = true;
        $result['displayingSites'] = false;
        return $result;
    }

    /**
     * List drive folders
     *
     * @param string $driveId
     * @param null|string $parentId
     * @param null|string $nextPageToken
     * @param null|bool $onlyFolders
     * @param null|array $sortOptions
     * @return array
     */
    private function listDriveFolders(string $driveId, ?string $parentId, ?string $nextPageToken, ?bool $onlyFolders, ?array $sortOptions)
    {
        $url = $this->getListUrl($driveId, $parentId, $nextPageToken, $onlyFolders, $sortOptions);
        $result = $this->listSharepointResource($url);
        $result['displayingDocumentDrives'] = false;
        $result['displayingSites'] = false;
        return $result;
    }

    /**
     * Get document libraries url
     *
     * @param string $siteId
     * @return string
     */
    private function getDocumentLibrariesUrl(string $siteId)
    {
        return "/sites/$siteId/drives";
    }

    /**
     * Get sites url
     * @return string
     */
    private function getSitesUrl()
    {
        return '/sites?search=';
    }

    /**
     * Get list url
     *
     * @param string $driveId
     * @param null|string $parentId
     * @param null|string $nextPageToken
     * @param null|string $onlyFolders
     * @param null|array $sortOptions
     * @return string
     */
    private function getListUrl(string $driveId, ?string $parentId, ?string $nextPageToken, ?bool $onlyFolders, ?array $sortOptions)
    {
        global $sugar_config;

        $url = "/drives/$driveId/root/children";

        if (isset($parentId)) {
            $url = "/drives/$driveId/items/$parentId/children";
        }

        $top = $sugar_config['list_max_entries_per_page'];

        if ($nextPageToken) {
            $url = $nextPageToken;
            // limit result set
            $url = $this->urlHasParam($url, '$top') ? $url : $url.'&$top='.$top;
        } else {
            // limit result set
            $url .= '?$top='.$top;
        }

        if ($onlyFolders) {
            $url .= '&filter=folder ne null';
        }

        if ($sortOptions) {
            $direction = $sortOptions['direction'];
            $field = $sortOptions['fieldName'];
            $url .= "&orderby={$field} {$direction}";
        }

        return $url;
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
        $parentId = $options['parent'] ?? 'root';
        $driveId = isset($options['driveId']) ? $options['driveId'] : null;

        if (!$folderName) {
            return null;
        }
        $url = "/drives/$driveId/root/children";

        if (isset($parentId)) {
            $url = "/drives/$driveId/items/$parentId/children";
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

        $parentReference = $response->getParentReference();
        $parentDriveId = $parentReference->getDriveId();
        $parentId = $response->getId();

        return [
            'driveId' => $parentDriveId,
            'id' => $parentId,
        ];
    }

     /**
     *  Deletes a file/folder  from a Drive
     *
     * @param array $options
     * @return null|array
     */
    public function deleteFile(array $options)
    {
        $client = $this->getClient();
        $fileId = $options['fileId'];
        $driveId = $options['driveId'];

        $url = "/drives/$driveId/items/$fileId";

        $request = $client->createRequest('DELETE', $url);
        $response = $request->execute();
        return $response->getBody();
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
        $data = isset($options['data']) ? $options['data'] : null;

        if ($data) {
            $filePath = $data['tmp_name'];
        } else {
            if (isset($options['filePath'])) {
                $filePath = $options['filePath'];
            } else {
                $filePath = $this->getFilePath($options['documentBean']);
            }
        }

        $url = "/drives/{$driveId}/items/{$parentId}:/{$fileName}:/content";

        $request = $client->createRequest('PUT', $url);
        $request->attachBody(sugar_file_get_contents($filePath));
        $request->execute();

        return [
            'success' => true,
            'message' => 'LBL_FILE_UPLOADED',
        ];
    }

    /**
     * Get microsoft api client
     *
     * @return object|array
     */
    public function getClient()
    {
        $client = parent::getClient();

        if (is_array($client) && !$client['success']) {
            return $client;
        }

        $data = $this->getClientProfile('/me?$select=userPrincipalName,assignedPlans', $client);

        if (!isset($data) || !$data || !$this->hasSharePointService($data)) {
            return [
                'success' => false,
                'message' => 'LBL_CHECK_MICROSOFT_CONNECTION',
            ];
        }

        return $client;
    }

    /**
     * Get the client's profile including assigned plans
     *
     * @param string $url
     * @param object $client
     * @return array|bool
     */
    public function getClientProfile(string $url, object $client)
    {
        $request = $client->createCollectionRequest('GET', $url);
        $response = false;

        try {
            $requestExecution = $request->execute();
            $response = $requestExecution->getBody();
        } catch (\Exception $e) {
            $GLOBALS['log']->error($e->getMessage());
        }

        return $response;
    }

    /**
     * Checks if the client has SharePoint Service
     *
     * @param array $data
     * @return bool
     */
    public function hasSharePointService(array $data): bool
    {
        if (isset($data['assignedPlans']) && is_array($data['assignedPlans'])) {
            foreach ($data['assignedPlans'] as $assignedPlan) {
                if (isset($assignedPlan['service']) && $assignedPlan['service'] === 'SharePoint') {
                    return true;
                }
            }
        }

        return false;
    }
}
