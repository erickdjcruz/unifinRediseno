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

require_once 'include/externalAPI/Dropbox/DropboxClient.php';

/**
 * ExtAPIDropbox
 */
class ExtAPIDropbox extends ExternalAPIBase
{
    public $authMethod = 'oauth2';
    public $connector = 'ext_eapm_dropbox';

    public $useAuth = true;
    public $requireAuth = true;
    public $supportedModules = [];

    protected $client;
    protected $baseApiUrl = 'https://api.dropboxapi.com/2';
    protected $contentUrl = 'https://content.dropboxapi.com/2';
    protected $scopes = [
        'account_info.read',
        'files.metadata.read',
        'files.content.read',
        'files.content.read',
        'files.content.write',
    ];

    /**
     * Gets the client and checks for the access token
     *
     * @return DropboxClient
     */
    public function getClient(): DropboxClient
    {
        $client = $this->getDropboxClient();
        $eapm = $this->getLoginInfo();

        if ($eapm && !empty($eapm->api_data)) {
            $client->setAccessToken($eapm->api_data);

            if ($client->isAccessTokenExpired()) {
                $this->refreshToken($client);
            }
        }

        return $client;
    }

    /**
     * Gets the dropbox client
     *
     * @return DropboxClient
     */
    public function getDropboxClient(): DropboxClient
    {
        $config = $this->getDropboxOauth2Config();

        $client = new DropboxClient();
        $client->setClientId($config['properties']['client_id']);
        $client->setClientSecret($config['properties']['client_secret']);
        $client->setRedirectUri($config['redirect_uri']);

        return $client;
    }

    /**
     * Calls for the refresh token and saves the new access token
     *
     * @param DropboxClient $client
     *
     * @return array
     */
    protected function refreshToken(DropboxClient $client): array
    {
        $token = null;
        $refreshToken = $client->getRefreshToken();
        if ($refreshToken) {
            try {
                $client->refreshToken($refreshToken);
            } catch (\Exception $e) {
                $GLOBALS['log']->error($e->getMessage());
            }

            $token = $client->getAccessToken();
            $this->saveToken($token);
        }

        return $token;
    }

    /**
     * Saves the access token
     *
     * @param array $accessToken
     * @return void
     */
    protected function saveToken(array $accessToken)
    {
        global $current_user;
        $bean = $this->getLoginInfo();
        if (!$bean) {
            $bean = BeanFactory::newBean('EAPM');
            $bean->assigned_user_id = $current_user->id;
            $bean->application = 'Dropbox';
            $bean->validated = true;
        }

        $bean->api_data = json_encode($accessToken);
        $bean->save();
    }

    /**
     * Returns the EAPM bean
     *
     * @return null|SugarBean
     */
    protected function getLoginInfo(): ?SugarBean
    {
        return EAPM::getLoginInfo('Dropbox');
    }

    /**
     * Revokes the access token
     *
     * @return bool
     */
    public function revokeToken(): bool
    {
        $client = $this->getClient();

        try {
            $client->revokeToken();
        } catch (\Exception $e) {
            return false;
        }

        $eapm = $this->getLoginInfo();
        if ($eapm) {
            $eapm->mark_deleted($eapm->id);
        }

        return true;
    }

    /**
     * Calls for the authentication flow and saves the token
     *
     * @param string $code
     *
     * @return array
     */
    public function authenticate($code): array
    {
        $eapmBean = null;
        global $current_user;

        $config = $this->getDropboxOauth2Config();
        $clientId = $config['properties']['client_id'];
        $clientSecret = $config['properties']['client_secret'];
        $redirectUri = $config['redirect_uri'];
        $client = $this->getClient();
        $accessTokenInfo = $client->generateAccessToken($clientId, $clientSecret, $code, $redirectUri);

        if ($accessTokenInfo) {
            $accessToken = $accessTokenInfo['access_token'];

            if (!empty($accessToken)) {
                $refreshToken = $accessTokenInfo['refresh_token'];
                $expiresIn = $accessTokenInfo['expires_in'];
                $accountId = $accessTokenInfo['account_id'];

                $eapmBean = $this->getUserEAPM();

                if (!$eapmBean) {
                    $eapmBean = BeanFactory::newBean('EAPM');
                }

                $eapmBean->name = 'Dropbox';
                $eapmBean->assigned_user_id = $current_user->id;
                $eapmBean->application = 'Dropbox';
                $eapmBean->validated = true;

                $apiData = [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_in' => time() + intVal($expiresIn),
                    'account_id' => $accountId,
                ];

                $eapmBean->api_data = json_encode($apiData);
                $eapmBean->save();
            }
        }

        return [
            'eapmId' => $eapmBean->id,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * Gets the user EAPM
     *
     * @return SugarBean
     */
    public function getUserEAPM(): ?SugarBean
    {
        global $current_user;

        $eapmSeed = BeanFactory::newBean('EAPM');
        $query = new SugarQuery();
        $query->from($eapmSeed);
        $query->select('*');
        $query->where()->equals('application', 'Dropbox');
        $query->where()->equals('assigned_user_id', $current_user->id);
        $query->where()->equals('deleted', 0);
        $query->limit(1);

        $results = $query->execute();

        if (empty($results)) {
            return null;
        }

        $row = $results[0];
        $eapm = BeanFactory::newBean('EAPM');
        $eapm->fromArray($row);

        return $eapm;
    }

    /**
     * Lists files and folders
     *
     * @param array $data
     *
     * @return array
     */
    public function listFolder(array $data): array
    {
        $cursor = isset($data['cursor']) ? $data['cursor'] : null;

        $url = "{$this->baseApiUrl}/files/list_folder";

        $callParams = $this->getCallParams($data, $url);
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Lists Shared with me folders
     *
     * @param array $data
     *
     * @return array
     */
    public function listSharedFolder(array $data): array
    {

        $url = "{$this->baseApiUrl}/sharing/list_folders";
        $callParams = $this->getCallParams($data, $url);
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Lists Shared with me files
     *
     * @param array $data
     *
     * @return array
     */
    public function listSharedFiles(array $data): array
    {
        $url = "{$this->baseApiUrl}/sharing/list_received_files";
        $callParams = $this->getCallParams($data, $url);
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Creates a dropbox folder
     *
     * @param array $data
     *
     * @return array
     */
    public function createFolder(array $data): array
    {
        $url = "{$this->baseApiUrl}/files/create_folder_v2";
        $data['autorename'] = false;
        $callParams = $this->getCallParams($data, $url);

        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Deletes a dropbox file or folder
     *
     * @param array $data
     *
     * @return array
     */
    public function deleteFile(array $data): array
    {
        $url = "{$this->baseApiUrl}/files/delete_v2";
        $callParams = $this->getCallParams($data, $url);
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Downloads a dropbox file
     *
     * @param string $fileId
     *
     * @return string
     */
    public function downloadFile(string $fileId): string
    {
        $url = "{$this->baseApiUrl}/files/delete_v2";
        $data['fileId'] = $fileId;
        $callParams = $this->getCallParams($data, $url, 'downloadFile');
        $response = $callParams['client']->call('POST', $callParams['url'], null, $callParams['headers'], false);

        if (!is_string($response)) {
            return false;
        }

        return $response;
    }

    /**
     * Creates a dropbox share link
     *
     * @param array $data
     */
    public function getSharedLink(array $data)
    {
        $url = "{$this->baseApiUrl}/sharing/create_shared_link_with_settings";
        $callParams = $this->getCallParams($data, $url);
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Uploads a file to dropbox
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    public function uploadFile(string $path, array $data)
    {
        $url = "{$this->contentUrl}/files/upload";
        $data['path'] = $path;
        $callParams = $this->getCallParams($data, $url, 'uploadFile');
        return $callParams['client']->call('POST', $callParams['url'], $callParams['data'], $callParams['headers'], false);
    }

    /**
     * Gets the dropbox oauth config
     *
     * @return array
     */
    public function getDropboxOauth2Config(): array
    {
        $config = [];
        require SugarAutoLoader::existingCustomOne('modules/Connectors/connectors/sources/ext/eapm/dropbox/config.php');
        $config['redirect_uri'] = rtrim(SugarConfig::getInstance()->get('site_url'), '/')
            . '/oauth-handler/DropboxOauth2Redirect';

        return $config;
    }

    /**
     * Get common parameters for client-call() function
     *
     * @param array $data
     * @param string $url
     * @return array
     */
    private function getCallParams(array $data, string $url, ?string $typeOfOperation = null)
    {
        $client = $this->getClient();
        $tokenData = $client->getAccessToken();
        $accessToken = $tokenData['access_token'];
        $headers = [];
        $headers[] = "Authorization: Bearer {$accessToken}";

        if ($typeOfOperation === null) {
            $headers[] = 'Content-Type: application/json';
        } else {
            switch ($typeOfOperation) {
                case "uploadFile":
                    $headers[] = 'Content-Type: application/octet-stream';
                    $headers[] = 'Dropbox-API-Arg: {"path":"' . ($data['path'] ?? '') . '", "mode":{".tag":"overwrite"}}';
                    break;
                case 'downloadFile':
                    $headers[] = 'Content-Type: application/octet-stream';
                    $headers[] = 'Dropbox-API-Arg: {"path":"' . ($data['fileId'] ?? '') . '"}';
                    break;
                default:
                    $headers[] = 'Content-Type: application/json';
                    break;
            }
        }

        if (isset($data['cursor'])) {
            $url .= '/continue';
            $data = [
                'cursor' => $data['cursor'],
            ];
        }

        $params = [
            'client' => $client,
            'headers' => $headers,
            'data' => $data,
            'url' => $url,
        ];

        return $params;
    }
}
