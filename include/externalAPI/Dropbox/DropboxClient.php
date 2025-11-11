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

class DropboxClient
{
    /**
     * @var mixed[]
     */
    public $requestTypes;
    public $authUrl = 'https://www.dropbox.com/oauth2/authorize';
    public $tokenUri = 'https://api.dropboxapi.com/oauth2/token';
    public $revokeUri = 'https://api.dropboxapi.com/2/auth/token/revoke';

    public static $POST = 'POST';
    public static $GET = 'GET';
    public static $PUT = 'PUT';
    public static $DELETE = 'DELETE';

    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $scopes;
    private $token;

    /**
     * Setter for client ID
     *
     * @param string $clientId
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Getter for client ID
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Setter for client secret
     *
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Getter for client secret
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Setter for redirect URI
     *
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * Getter for redirect URI
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * Setter for scopes
     *
     * @param array $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Getter for scopes
     *
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Getter for request type
     *
     * @return string
     */
    public function getRequestType(string $type): string
    {
        return $this->requestTypes[$type];
    }

    /**
     * Makes the HTTP call
     *
     * @param string $method
     * @param string $url
     * @param null|array $data
     * @param array $headers
     * @param null|bool $auth
     *
     * @return array|string
     */
    public function call(string $method, string $url, ?array $data, array $headers, ?bool $auth)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '2');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if ($method === self::$GET) {
            $url = ($url . '?' . http_build_query($data));
        } elseif ($method === self::$POST) {
            curl_setopt($curl, CURLOPT_POST, true);

            if (isset($data)) {
                if ($auth) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                } elseif (isset($data['content'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data['content']);
                } else {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
            }
        } elseif ($method === self::$PUT) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === self::$DELETE) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        curl_close($curl);

        if ($this->isJson($body)) {
            return json_decode($body, true);
        }

        return $body;
    }

    /**
     * Return an array of HTTP response headers
     *
     * @param string $raw_headers A string of raw HTTP response headers
     *
     * @return string[] Array of HTTP response heaers
     */
    protected function httpParseHeaders($raw_headers)
    {
        // ref/credit: http://php.net/manual/en/function.http-parse-headers.php#112986
        $headers = [];
        $key = '';

        foreach (explode("\n", $raw_headers) as $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) === "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }

    /**
     * Helper for checking if it's a JSON
     */
    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Builds options for the token call
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $code
     * @param string $redirectUri
     *
     * @return array
     */
    public function generateAccessToken(?string $clientId = null, ?string $clientSecret = null, ?string $code = null, ?string $redirectUri = null): array
    {
        if (!$clientId) {
            throw new \InvalidArgumentException('Missing the required parameter $client_id when calling generateAccessToken');
        }
        if (!$clientSecret) {
            throw new \InvalidArgumentException('Missing the required parameter $client_secret when calling generateAccessToken');
        }
        if (!$code) {
            throw new \InvalidArgumentException('Missing the required parameter $code when calling generateAccessToken');
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ];

        $response = $this->call('POST', $this->tokenUri, $postData, $headers, true);

        return $response;
    }

    /**
     * Creates the authorization URL
     *
     * @return string
     */
    public function createAuthURL(): string
    {
        $config = $this->getDropboxOauth2Config();
        $clientId = $config['properties']['client_id'];
        $redirectUri = urlencode($config['redirect_uri']);
        $responseType = 'code';
        $tokenAccessType = 'offline';
        $urlAuthorize = $this->getAuthorizationUri($clientId, $tokenAccessType, $redirectUri, $responseType);

        return $urlAuthorize;
    }

    /**
     * Builds the authorization URI
     *
     * @param string $clientId
     * @param string $accessType
     * @param string $redirectUri
     * @param string $responseType
     *
     * @return string
     */
    public function getAuthorizationUri(string $clientId, string $tokenAccessType, string $redirectUri, string $responseType): string
    {
        $authUri = "{$this->authUrl}?client_id={$clientId}&token_access_type={$tokenAccessType}&response_type={$responseType}&redirect_uri={$redirectUri}";

        return $authUri;
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
     * Setter for access token
     *
     * @param string|array $token
     *
     * @return void
     */
    public function setAccessToken($token): void
    {
        if (is_string($token)) {
            $token = json_decode($token, true);
        }

        $this->token = $token;
    }

    /**
     * Getter for access token
     *
     * @return array
     */
    public function getAccessToken(): array
    {
        return $this->token;
    }

    /**
     * Getter for refresh token
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->token['refresh_token'];
    }

    /**
     * Checks if the access token is expired
     *
     * @return bool
     */
    public function isAccessTokenExpired(): bool
    {
        if (!$this->token) {
            return true;
        }

        if (isset($this->token['expires_in'])) {
            return ($this->token['expires_in'] - 30) < time();
        }

        return false;
    }

    /**
     * Creates the options for the refresh token call
     *
     * @param string|null $refreshToken
     *
     * @return array
     */
    public function refreshToken(?string $refreshToken): array
    {
        if ($refreshToken === null) {
            if (!isset($this->token['refresh_token'])) {
                throw new LogicException();
            }
            $refreshToken = $this->token['refresh_token'];
        }

        $config = $this->getDropboxOauth2Config();
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $postData = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => isset($config['properties']) ? $config['properties']['client_id'] : '',
            'client_secret' => isset($config['properties']) ? $config['properties']['client_secret'] : '',
        ];

        $tokenData = $this->call('POST', $this->tokenUri, $postData, $headers, true);

        if (!isset($tokenData['refresh_token'])) {
            $tokenData['refresh_token'] = $refreshToken;
        }

        $tokenData['expires_in'] = time() + intVal($tokenData['expires_in']);

        if (isset($tokenData['access_token']) && $tokenData['access_token']) {
            $this->setAccessToken($tokenData);
        }

        return $tokenData;
    }

    /**
     * Creates options for the revoke token call
     *
     * @return bool
     */
    public function revokeToken(): bool
    {
        $accessToken = $this->token['access_token'];
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$accessToken}",
        ];

        $this->call('POST', $this->revokeUri, null, $headers, true);

        return true;
    }
}
