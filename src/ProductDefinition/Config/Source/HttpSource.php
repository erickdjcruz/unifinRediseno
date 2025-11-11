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

namespace Sugarcrm\Sugarcrm\ProductDefinition\Config\Source;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Symfony\Component\HttpFoundation\Response;

class HttpSource implements SourceInterface
{
    /**
     * Http client client timeout
     */
    public const HTTP_CLIENT_TIMEOUT = 2;

    /**
     * Default server uri
     */
    public const DEFAULT_BASE_URI = 'https://updates.sugarcrm.com/spds/';

    /**
     * Default fallback version
     * This should be 1 minor version below the current version (or the current minor version for the fixpack)
     * e.g. 12.2.0 for Sugar 12.3.0; 12.0.0 for Sugar 12.0.4
     */
    public const DEFAULT_FALLBACK_VERSION = '13.3.0';

    /**
     * Http client
     * @var HttpClient
     */
    protected $client;

    /**
     * Fallback version
     * @var string
     */
    protected $fallbackVersion;

    /**
     * constructor.
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (empty($options['base_uri'])) {
            throw new \InvalidArgumentException('base URL should not be empty');
        }

        $base_uri = rtrim($options['base_uri'], " \t\n\r\0\x0B/") . '/';

        $httpOptions = [
            'base_uri' => $base_uri,
            'http_errors' => false,
            'timeout' => static::HTTP_CLIENT_TIMEOUT,
        ];
        $proxyOption = $this->getHTTPClientProxy();
        if (!empty($proxyOption)) {
            $httpOptions = array_merge($httpOptions, ['proxy' => $proxyOption]);
        }
        $this->setHttpClient(new HttpClient($httpOptions));

        $this->fallbackVersion = static::DEFAULT_FALLBACK_VERSION;
    }

    /**
     * Return HTTP client proxy
     *
     * @return string
     */
    protected function getHTTPClientProxy(): string
    {
        $proxy = '';
        if (!empty(\BeanFactory::getBeanClass('Administration'))) {
            $config = \Administration::getSettings('proxy');
            if (!empty($config->settings)
                && !empty($config->settings['proxy_on'])
                && !empty($config->settings['proxy_host'])) {
                $proxy = $config->settings['proxy_host'] . ':' . $config->settings['proxy_port'];
                if (!empty($config->settings['proxy_auth'])) {
                    $proxy = $config->settings['proxy_username'] . ':' . $config->settings['proxy_password'] . '@' . $proxy;
                }
            }
        }
        return $proxy;
    }

    /**
     * set http client
     * @param HttpClient $client
     * @return $this
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * make request and return product definition array
     * @return string|null
     */
    public function getDefinition(): ?string
    {
        $raw = $this->makeRequest($this->getSugarVersion());
        if (is_null($raw)) {
            $this->getLogger()->warn(sprintf(
                'Can\'t download product definition for version %s.',
                $this->getSugarVersion()
            ));
            if (!empty($this->fallbackVersion)) {
                $raw = $this->makeRequest($this->fallbackVersion);
            }
        }

        if ($this->validateResponse($raw)) {
            return $raw;
        }
        return null;
    }

    /**
     * validate response
     * @param null|string $data
     * @return bool
     */
    protected function validateResponse(?string $response): bool
    {
        if (empty($response)) {
            return false;
        }
        // verify json format
        $decodedData = json_decode($response, true);
        if (empty($decodedData)) {
            return false;
        }

        $validEntries = [
            AccessControlManager::MODULES_KEY,
            AccessControlManager::FIELDS_KEY,
            AccessControlManager::RECORDS_KEY,
            AccessControlManager::DASHLETS_KEY,
        ];

        foreach ($validEntries as $key) {
            if (empty($decodedData[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Make HTTP GET request and return response
     * @param string $version
     * @return string|null
     */
    protected function makeRequest(string $version): ?string
    {
        try {
            $response = $this->client->request('GET', $version);
        } catch (\Exception|GuzzleException $e) {
            $this->getLogger()->error('Can\'t download product definition for version: ' . $version);
            return null;
        }

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->getLogger()->error(sprintf(
                'wrong product definition service response code %s for version %s',
                Response::HTTP_OK,
                $version
            ));
            return null;
        }

        return (string)$response->getBody();
    }

    /**
     * return sugar version
     * @return string
     */
    protected function getSugarVersion(): string
    {
        global $sugar_version;
        return $sugar_version;
    }

    /**
     * @return \LoggerManager
     */
    protected function getLogger(): \LoggerManager
    {
        global $log;
        return $log;
    }
}
