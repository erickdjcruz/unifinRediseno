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

namespace Sugarcrm\Sugarcrm\PushNotification\SugarPush;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Sugarcrm\Sugarcrm\PushNotification\Service as NotificationService;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\IdentityProvider\Srn\Converter as SrnConverter;

class SugarPush implements NotificationService
{
    /** @var int Connection timeout in seconds */
    private const SOCKET_TIMEOUT_SEC = 3;

    /**
     * Send requests through this HTTP client.
     *
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Default mapping of platform to the application ID
     * Can be overridden in config
     */
    private const PLATFORM_APPLICATION_DEFAULTS = [
        'android' => 'fcm',
        'ios' => 'apns',
        'ios_sandbox' => 'apns_sandbox',
    ];

    /**
     * Creates a http client for SugarPush service.
     *
     * @throws Exception Throws if the instance cannot be created.
     */
    public function __construct()
    {
        $url = $this->getServiceURL();

        if ($url) {
            $this->client = $this->getHTTPClient($url);
        } else {
            throw new Exception('SugarPush is not available');
        }
    }

    /**
     * Returns a guzzle client.
     *
     * @param string $baseURI The url for SugarPush service
     * @return GuzzleClient
     */
    protected function getHTTPClient(string $baseURI): GuzzleClient
    {
        $proxy = new ProxyMiddleware();
        $auth = new AuthMiddleware();
        $retry = new RetryMiddleware();
        $debug = new DebugMiddleware();

        $stack = HandlerStack::create(GuzzleHttp\choose_handler());
        $stack->push($proxy, 'proxy');
        $stack->push($auth, 'auth');
        $stack->push($retry, 'retry');
        $stack->push($debug, 'debug');

        return new GuzzleClient(['base_uri' => $baseURI, 'handler' => $stack]);
    }

    /**
     * Returns the URL to SugarPush service for the given region.
     *
     * @return string
     */
    protected function getServiceURL(): string
    {
        $sugarConfig = \SugarConfig::getInstance();
        $pushConfig = $sugarConfig->get('sugar_push');
        [$region, $environment] = $this->getRegionAndEnvironment($sugarConfig);

        if (!$region) {
            return '';
        }

        if (!empty($pushConfig['service_urls'][$region])) {
            $url = $pushConfig['service_urls'][$region];
        } else {
            $url = sprintf($pushConfig['service_urls']['default'] ?? '', $region);
        }

        if ($environment === 'stage') {
            $url = str_replace('prod.service', 'stage.service', $url);
        }

        return $url;
    }

    /**
     * Gets aws region and environment(stage, prod) from idm config.
     *
     * @return array [$stage, $environment]
     */
    protected function getRegionAndEnvironment(\SugarConfig $sugarConfig): array
    {
        $region = '';
        $environment = '';

        $idmConfig = new IdmConfig($sugarConfig);
        $config = $idmConfig->getIDMModeConfig();

        if (!empty($config['tid'])) {
            $tenantSrn = SrnConverter::fromString($config['tid']);

            if ($tenantSrn) {
                $region = $tenantSrn->getRegion();
                $environment = 'prod';
                if ($tenantSrn->getPartition() === 'stage') {
                    $environment = 'stage';
                }
            }
        }

        return [$region, $environment];
    }

    /**
     * Checks server response.
     *
     * @param Response $respone Server response.
     * @return bool
     */
    protected function isSuccess(Response $response): bool
    {
        $success = false;

        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            $success = $body && empty($body['error']);
        }

        if (!$success) {
            $log = \LoggerManager::getLogger();
            $statusCode = $response->getStatusCode();
            $body = (string)$response->getBody();
            $log->error('sugar push: statusCode: ' . $statusCode . ' body: ' . $body);
        }

        return $success;
    }

    /**
     * Registers a user's device.
     *
     * @param string $platform The device's platform.
     * @param string $deviceId The device's ID.
     * @return bool
     */
    public function register(string $platform, string $deviceId): bool
    {
        $response = $this->client->request(
            'PUT',
            '/device',
            [
                'timeout' => self::SOCKET_TIMEOUT_SEC,
                'connect_timeout' => self::SOCKET_TIMEOUT_SEC,
                GuzzleHttp\RequestOptions::JSON => [
                    'application_id' => $this->getApplicationId($platform),
                    'device_id' => $deviceId,
                ],
            ]
        );

        return $this->isSuccess($response);
    }

    /**
     * Updates a user's device.
     */
    public function update(string $platform, string $oldDeviceId, string $newDeviceId = ''): bool
    {
        $post = [
            'application_id' => $this->getApplicationId($platform),
            'device_id' => $oldDeviceId,
        ];
        if ($newDeviceId && $oldDeviceId !== $newDeviceId) {
            $post['new_device_id'] = $newDeviceId;
        }

        $response = $this->client->request(
            'POST',
            '/device',
            [
                'timeout' => self::SOCKET_TIMEOUT_SEC,
                'connect_timeout' => self::SOCKET_TIMEOUT_SEC,
                GuzzleHttp\RequestOptions::JSON => $post,
            ]
        );

        return $this->isSuccess($response);
    }

    /**
     * Removes a user's device.
     *
     * @param string $platform The device's platform.
     * @param string $deviceId The device's ID.
     * @return bool
     */
    public function delete(string $platform, string $deviceId): bool
    {
        $response = $this->client->request(
            'DELETE',
            '/device',
            [
                'timeout' => self::SOCKET_TIMEOUT_SEC,
                'connect_timeout' => self::SOCKET_TIMEOUT_SEC,
                GuzzleHttp\RequestOptions::JSON => [
                    'application_id' => $this->getApplicationId($platform),
                    'device_id' => $deviceId,
                ],
            ]
        );

        return $this->isSuccess($response);
    }

    /**
     * Activates/deactivates a user (makes possible/impossible to receive push notifications on user's devices)
     * Important: This must not block user login/logout processes:
     * - use short timeouts
     * - do not throw errors or exceptions
     */
    public function setActive(string $userId, bool $flag): bool
    {
        $log = \LoggerManager::getLogger();
        if ($flag) {
            $payload = ['user_logged_in' => true];
        } else {
            $payload = ['user_logged_out' => true];
        }
        $payload['user_id'] = $userId;

        try {
            $response = $this->client->request(
                'POST',
                '/device',
                [
                    'timeout' => self::SOCKET_TIMEOUT_SEC,
                    'connect_timeout' => self::SOCKET_TIMEOUT_SEC,
                    GuzzleHttp\RequestOptions::JSON => $payload,
                    'authorize_as_application' => true,
                ]
            );

            return $this->isSuccess($response);
        } catch (\Throwable $e) {
            $log->error('sugar push [setActive]: ' . $e);
            return false;
        }
    }

    /**
     * Sends a message to users.
     *
     * @param array $userIds The user ids.
     * @param array $message The message to send. Options are:
     *
     * $message['title'] string The message title (required)
     * $message['body'] string The message body (required)
     * $message['data'] array Extra data to send (optional)
     * $message['android'] array Android specific attributes (optional)
     * $message['ios'] array IOS specific attributes (optional)
     *
     * @return bool
     */
    public function send(array $userIds, array $message): bool
    {
        $data = array_merge(['target_user_id' => implode(',', $userIds)], $message);

        $response = $this->client->request(
            'PUT',
            '/notification',
            [
                'timeout' => self::SOCKET_TIMEOUT_SEC,
                'connect_timeout' => self::SOCKET_TIMEOUT_SEC,
                GuzzleHttp\RequestOptions::JSON => $data,
                'authorize_as_application' => true,
            ]
        );

        return $this->isSuccess($response);
    }

    /**
     * Mobile device provides a platform name and the request should be directed to an appropriate application.
     * The Application ID is based on the platform name and can be defined in the config. If it's not
     * configured - the default value will be taken from PLATFORM_APPLICATION_DEFAULTS constant.
     *
     * @param string $platform
     * @return string
     * @throws Exception
     */
    private function getApplicationId(string $platform): string
    {
        $sugarConfig = \SugarConfig::getInstance();
        $config = $sugarConfig->get('sugar_push');

        $applicationId = $config['platform_applications'][$platform]
            ?? self::PLATFORM_APPLICATION_DEFAULTS[$platform] ?? null;
        if ($applicationId === null) {
            throw new Exception('cannot find Application ID for the provided platform');
        }

        return $applicationId;
    }
}
