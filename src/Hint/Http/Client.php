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

namespace Sugarcrm\Sugarcrm\Hint\Http;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SugarApiExceptionError;
use SugarApiExceptionInvalidGrant;
use SugarApiExceptionNotAuthorized;
use SugarApiExceptionNotFound;
use Sugarcrm\Sugarcrm\Hint\Exception\LicenseExpiredApiException;
use Sugarcrm\Sugarcrm\Hint\Exception\NoLicenseApiException;
use Sugarcrm\Sugarcrm\Hint\Logger\Logger as HintLogger;
use Sugarcrm\IdentityProvider\Srn;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;

/**
 * Class Client
 *
 * Data Enrichment API client for Sugar's Hint service.
 */
class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const DATA_ENRICHMENT_ACTION_AUTH = '/hint/data-enrichment/v3/tokens';
    public const NOTIFICATIONS_SVC_ACTION_CREATE_TOKEN = '/hint/notifications-service/v1/createToken';
    public const COMPANY_IDENTITY_SVC_URL = '/hint/data-enrichment/v1/createIdentity';
    public const DATA_ENRICHMENT_CONFIG_SVC_URL = '/hint/data-enrichment/v1/config-fields-for-enrich';
    public const DATA_ENRICHMENT_UPDATE_LIC_URL = '/hint/data-enrichment/v1/updateLicense';
    public const DATA_ENRICHMENT_DELETE_INSTANCE = '/hint/data-enrichment/v1/instance';
    public const HINT_EU_AWS_REGIONS = ['eu-west-1', 'eu-central-1'];
    public const HINT_APSE_AWS_REGIONS = ['ap-southeast-2', 'ap-southeast-1'];
    public const US_REGION = 'US';
    public const EU_REGION = 'EU';
    public const APSE_REGION = 'APSE';

    public $endpoint;

    /**
     * Constructor
     *
     * @param string $endpoint Endpoint on which Stage2 service is to be found.
     */
    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
        $this->setLogger(new HintLogger());
    }

    /**
     * Proxy to service's authorize endpoint.
     *
     * @param array $body
     * @return mixed Response body when successful
     * @throws \SugarApiException
     * @throws \SugarApiExceptionError
     * @throws \SugarApiExceptionInvalidGrant
     * @throws \SugarApiExceptionNotAuthorized
     * @throws \SugarApiExceptionNotFound
     * @throws \Zend_Http_Client_Exception
     */
    public function newToken($body)
    {
        $uri = $this->endpoint . self::DATA_ENRICHMENT_ACTION_AUTH;

        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders(),
            $this->preparePayload($body)
        );

        return $this->processRequest($request);
    }

    /**
     * Register company identity to DE
     *
     * @param array $body
     * @return string
     */
    public function registerCompanyIdentityToDE($body)
    {
        $uri = $this->endpoint . self::COMPANY_IDENTITY_SVC_URL;
        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders(),
            $this->preparePayload($body)
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $statusCode = $response->getCode();

        // Allow conflicts (duplicate entries, code 409) to be silently ignored
        if ($statusCode >= 400 && $statusCode != 409) {
            $this->logger->alert('Unexpected HTTP return status code in registerToCompanyIdentity(): ' . $statusCode);
            $this->logger->alert('Error occurred in registerToCompanyIdentity(): ' . $response->getBody());
        }

        return $statusCode;
    }

    /**
     * Update license in data enrichment identity table
     *
     * @param array $body
     * @return string
     */
    public function updateLicenseInDataEnrichmentIdentityTable($body)
    {
        $hintApiCalls = new \HintApi();
        $hintApiCalls->createToken('', '');

        $uri = $this->endpoint . self::DATA_ENRICHMENT_UPDATE_LIC_URL;
        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders([
                'authToken: ' . $hintApiCalls->privilegeToken,
            ]),
            $this->preparePayload($body)
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $statusCode = $response->getCode();

        if ($statusCode >= 400) {
            $this->logger->alert('Unexpected HTTP return status code in updateLicenseInDataEnrichmentIdentityTable(): ' . $statusCode);
            $this->logger->alert('Error occurred in updateLicenseInDataEnrichmentIdentityTable(): ' . $response->getBody());
        }

        return $statusCode;
    }

    /**
     * Remove instance from data enrichment
     *
     * @param array $body
     * @return string
     */
    public function removeInstanceFromDataEnrichment($body)
    {
        $hintApiCalls = new \HintApi();
        $hintApiCalls->createToken('', '');

        $uri = $this->endpoint . self::DATA_ENRICHMENT_DELETE_INSTANCE;
        $request = new Request(
            Request::METHOD_DELETE,
            $uri,
            $this->prepareHeaders([
                'authToken: ' . $hintApiCalls->privilegeToken,
            ]),
            $this->preparePayload($body)
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $statusCode = $response->getCode();

        if ($statusCode !== 200) {
            $this->logger->alert('Unexpected HTTP return status code in removeInstanceFromDataEnrichment(): ' . $statusCode);
            $this->logger->alert('Error occurred in removeInstanceFromDataEnrichment(): ' . $response->getBody());
        }

        return $statusCode;
    }

    /**
     * Register config to DE
     *
     * @param string $privilegeToken
     * @param array $body
     * @return mixed
     */
    public function registerConfigToDE($privilegeToken, $body)
    {
        $uri = $this->endpoint . self::DATA_ENRICHMENT_CONFIG_SVC_URL;
        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders([
                'authToken: ' . $privilegeToken,
            ]),
            $this->preparePayload($body)
        );
        return $this->processRequest($request);
    }

    /**
     * Update config fields
     *
     * @param array $body
     * @return string
     */
    public function updateConfigFields($body)
    {
        $hintApiCalls = new \HintApi();
        $hintApiCalls->createToken('', '');

        $uri = $this->endpoint . self::DATA_ENRICHMENT_CONFIG_SVC_URL;
        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders([
                'authToken: ' . $hintApiCalls->privilegeToken,
            ]),
            $this->preparePayload($body)
        );
        return $this->processRequest($request);
    }

    /**
     * Proxy to notifications service's authorize endpoint.
     *
     * @param array $body
     * @return mixed Response body when successful
     * @throws \SugarApiException
     * @throws \SugarApiExceptionError
     * @throws \SugarApiExceptionInvalidGrant
     * @throws \SugarApiExceptionNotAuthorized
     * @throws \SugarApiExceptionNotFound
     */
    public function createNotificationsServiceToken($body)
    {
        $uri = $this->endpoint . self::NOTIFICATIONS_SVC_ACTION_CREATE_TOKEN;

        $request = new Request(
            Request::METHOD_POST,
            $uri,
            $this->prepareHeaders(),
            $this->preparePayload($body)
        );

        return $this->processRequest($request);
    }

    /**
     * Get subscription data
     *
     * @return mixed
     */
    public function getSubscriptionData()
    {
        $request = new Request(
            Request::METHOD_GET,
            $this->endpoint,
            $this->prepareHeaders(),
            $this->preparePayload()
        );
        $response = $this->processRequest($request);
        if (is_null($response) || is_null($response['subscription'])) {
            throw new \Exception('Received an unprocessable response from the subscription service.');
        }
        return $response;
    }

    /**
     *
     * returns region where the Hint services are hosted based on whitelist of EU domains.
     */
    public function getHintBackendHostGeo()
    {
        // check for set geo in config_overrides
        if (!empty($GLOBALS['sugar_config']) && !empty($GLOBALS['sugar_config']['hint'])) {
            $geo = $GLOBALS['sugar_config']['hint']['hint_install_target_geo'];
            if ($geo) {
                return $geo;
            }
        }

        $idmConfig = $this->getIdmConfig();
        $idmModeConfig = $idmConfig->getIDMModeConfig();

        if (!$idmConfig->isIDMModeEnabled()) {
            return self::US_REGION;
        }

        if (!is_string($idmModeConfig['tid'])) {
            return self::US_REGION;
        }

        $tenantSrn = Srn\Converter::fromString($idmModeConfig['tid']);
        $geoRegion = $tenantSrn->getRegion();

        // Set region to EU
        if (in_array($geoRegion, self::HINT_EU_AWS_REGIONS, true)) {
            return self::EU_REGION;
        }

        // Set region to APSE
        if (in_array($geoRegion, self::HINT_APSE_AWS_REGIONS, true)) {
            return self::APSE_REGION;
        }

        return self::US_REGION;
    }

    /**
     * @return Config
     */
    protected function getIdmConfig(): Config
    {
        return new Config(\SugarConfig::getInstance());
    }

    //
    //  Raw access methods
    //

    /**
     * Invokes the given HTTP request, handles any error code returns, and upon success returns the
     * decoded JSON response body.
     *
     * @param $request
     * @param $customOpts specify the custom options in CURL request.
     * @return mixed
     * @throws LicenseExpiredApiException
     * @throws NoLicenseApiException
     * @throws SugarApiException
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidGrant
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    private function processRequest($request, $customOpts = null, $suppressNotFoundLog = false)
    {
        $response = $client = $this->getHttpClient($customOpts)->sendRequest($request);
        $body = json_decode($response->getBody(), true);
        $requestInfo = [$request->getMethod(), $request->getUri()];

        switch ($response->getCode()) {
            case 401:
                $this->logger->alert('401: Unauthorized client error: ' . print_r($requestInfo, true));
                $this->logger->alert('Error cause: ' . print_r($body, true));
                throw new SugarApiExceptionInvalidGrant();
            case 402:
                if (is_null($body) || is_null($body['message'])) {
                    $this->logger->alert('Invalid response from Hint /token endpoint: ' . print_r($requestInfo, true));
                    throw new NoLicenseApiException();
                }

                // In the message we can find the reason for the failure.
                // Consult SubscriptionServiceClient class on Hint backend for details.
                switch ($body['message']) {
                    case 'ExpiredSugarCRMLicense':
                    case 'ExpiredHintLicense':
                        throw new LicenseExpiredApiException();
                    case 'NoHintLicense':
                    default:
                        throw new NoLicenseApiException();
                }
                break;
            case 403:
                $this->logger->alert('403: Forbidden client error: ' . print_r($requestInfo, true));
                $this->logger->alert('Error cause: ' . print_r($body, true));
                throw new SugarApiExceptionNotAuthorized();
            case 404:
                if (!$suppressNotFoundLog) {
                    $this->logger->alert('404: Unknown API accessed: ' . print_r($requestInfo, true));
                    $this->logger->alert('Error cause: ' . print_r($body, true));
                }
                throw new SugarApiExceptionNotFound();
            case 200:
                return json_decode($response->getBody(), true);
                // This is to handle /config-enrich-bean endpoint as it returns 201 status
            case 201:
                return 201;
            default:
                $this->logger->alert('Unexpected HTTP return status code: ' . $response->getCode());
                $this->logger->alert('Error: ' . print_r($requestInfo, true));
                $this->logger->alert('Error cause: ' . print_r($body, true));
                throw new SugarApiExceptionError();
        }
    }

    private function getHttpClient($customOpts = null): HttpClient
    {
        if ($customOpts) {
            $proxySettings = new ProxySettings();
            $customCurlOpts = array_merge_recursive($proxySettings->toCurlOpts(), $customOpts);
            $adapter = new Adapter\Curl($customCurlOpts);
            return new HttpClient($adapter);
        }
        return new HttpClient();
    }

    /**
     * Prepare request headers
     * @param array $headers additional headers beyond the standard JSON and cache control ones
     * @return array
     */
    private function prepareHeaders(array $headers = []): array
    {
        return array_merge([
            'Accept: application/json',
            'Content-Type: application/json',
            'cache-control: no-cache',
        ], $headers);
    }

    /**
     * Prepare service payload
     *
     * @param array $payload (hashmap)
     * @return string JSON string encoding of payload
     */
    private function preparePayload(array $payload = []): string
    {
        return json_encode($payload, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Ends with
     *
     * @param string $heystack
     * @param string $needle
     * @return bool
     */
    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }
}
