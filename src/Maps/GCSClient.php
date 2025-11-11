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

namespace Sugarcrm\Sugarcrm\Maps;

use BeanFactory;
use Configurator;
use LoggerManager;
use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;
use Sugarcrm\Sugarcrm\Maps\Client\TokenGenerator;
use SugarQuery;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\IdentityProvider\Srn\Converter as SrnConverter;

class GCSClient
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var Sugar URL
     */
    protected $systemUrl;

    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new GuzzleClient();
        $this->logger = new Logger(LoggerManager::getLogger());

        $tokenGenerator = new TokenGenerator();

        $this->token = $tokenGenerator->createToken();
        $this->systemUrl = $this->getSystemUrl();
    }

    /**
     * Create a sugar batch of addresses
     * @param array $data
     * @return void
     */
    public function createBatch(array $data)
    {
        $url = $this->getGCSConfig()['createBatch'];

        // inject sugar token
        $data['token'] = $this->token;
        $data['url'] = $this->systemUrl;

        $this->guzzle->post(
            $url,
            [
                GuzzleHttp\RequestOptions::JSON => $data,
            ],
        );
    }

    /**
     * Get coods from gcs
     *
     * @param string $zipCode
     * @param string $country
     * @return array
     */
    public function getCoordsByZip(string $zipCode, string $country): array
    {
        $url = $this->getGCSConfig()['getGeocodeByZipcode'];

        $data = [
            'zipCode' => $zipCode,
            'country' => $country,
            'token' => $this->token,
            'url' => $this->systemUrl,
        ];

        $response = $this->guzzle->post(
            $url,
            [
                GuzzleHttp\RequestOptions::JSON => $data,
            ]
        );

        $response = GuzzleHttp\json_decode($response->getBody(), true);

        if (!$response || !is_array($response)) {
            return [];
        }

        return $response;
    }

    /**
     * Get addresses from external client
     *
     * @return array
     */
    public function getData(): array
    {
        $batchId = $this->getBatchId();

        if (!$batchId) {
            return [];
        }

        $url = $this->getGCSConfig()['checkStatus'];

        $options = [
            'query' => [
                'sugar_batch_id' => $batchId,
                'url' => $this->systemUrl,
            ],
        ];

        $response = $this->guzzle->get($url, $options);

        $responseBody = GuzzleHttp\json_decode($response->getBody(), true);

        $response = [
            'batchId' => $batchId,
            'response' => $responseBody,
        ];

        return $response;
    }

    /**
     * Requeue a batch that is not anymore on the server
     *
     * @param string $batchId
     * @return void
     * @throws \SugarQueryException
    */
    public function requeueBatch(string $batchId): void
    {
        $externalSchedulerJob = BeanFactory::newBean(Constants::GEOCODE_SCHEDULER_MODULE);

        $sq = new SugarQuery();
        $sq->select(['id', 'addresses_data']);
        $sq->from($externalSchedulerJob)
            ->where()
            ->equals('id', $batchId);
        $sq->limit(1);

        $result = $externalSchedulerJob->fetchFromQuery($sq, ['id', 'addresses_data']);

        if (empty($result)) {
            $this->logger->warning("No external batch found for requeue, batch id: {$batchId}");
            return;
        }

        $geocodeBeanResult = $result[$batchId];

        $geocodeDataEncoded = $geocodeBeanResult->addresses_data;

        if (!$geocodeBeanResult) {
            $this->logger->warning("Unable to requeue the batch, batch id: {$batchId}");
            return;
        }

        $geocodeDataDecoded = json_decode($geocodeDataEncoded, true);

        if ($geocodeDataDecoded === null || !array_key_exists('addresses_data', $geocodeDataDecoded)) {
            $this->logger->warning("Unable to requeue the batch, batch id: {$batchId}");
            return;
        }

        $addressesData = $geocodeDataDecoded['addresses_data'];

        $batchData = [
            'batch_id' => $batchId,
            'addresses_data' => $addressesData,
        ];

        $this->createBatch($batchData);
    }

    /**
     * Get the first sugar batch available for processing
     *
     * @return void
     */
    private function getBatchId()
    {
        $externalSchedulerJob = BeanFactory::newBean(Constants::GEOCODE_SCHEDULER_MODULE);

        $sq = new SugarQuery();
        $sq->select('id');
        $sq->from($externalSchedulerJob)
            ->where()
            ->equals('status', Constants::GEOCODE_SCHEDULER_STATUS_QUEUED);
        $sq->limit(1);

        $result = $externalSchedulerJob->fetchFromQuery($sq, ['id']);

        if (empty($result)) {
            $this->logger->info('No external batch records queued');
            return;
        }

        $batchId = array_keys($result)[0];

        return $batchId;
    }

    /**
     * Returns the url of the sugar system
     *
     * @return string
     */
    public function getSystemUrl(): string
    {
        $config = \SugarConfig::getInstance();
        return $config->get('site_url');
    }

    /**
     * Get gcs service config
     *
     * @return array
     */
    protected function getGCSConfig(): array
    {
        $serviceUrl = $this->getGCSClientUrl();

        if (strlen($serviceUrl) > 0) {
            $lastChar = substr($serviceUrl, -1);

            if ($lastChar !== '/') {
                $serviceUrl .= '/';
            }
        }

        $version = 'v1/';
        $geocodeEndpoint = 'geocode';
        $geocodeByZipcodeEndpoint = 'geocode/zipcode';
        $statusEndpoint = 'checkStatus';

        $urls = [
            'createBatch' => "{$serviceUrl}{$version}{$geocodeEndpoint}",
            'getGeocodeByZipcode' => "{$serviceUrl}{$version}{$geocodeByZipcodeEndpoint}",
            'checkStatus' => "{$serviceUrl}{$version}{$statusEndpoint}",
        ];

        return $urls;
    }

    /**
     * Returns the URL to Maps service for the given region.
     *
     * @return string
     */
    protected function getGCSClientUrl(): string
    {
        $config = \SugarConfig::getInstance();
        $mapsServiceConfig = $config->get('gcs_client');
        $region = $this->getRegion();

        if ($region && !empty($mapsServiceConfig['service_urls'][$region])) {
            return $mapsServiceConfig['service_urls'][$region];
        } else {
            return $mapsServiceConfig['service_urls']['default'] ?? '';
        }
    }

    /**
     * Gets aws region from idm config.
     *
     * @return string
     */
    private function getRegion(): string
    {
        $sugarConfig = \SugarConfig::getInstance();
        $region = 'default';
        $idmConfig = new IdmConfig($sugarConfig);
        $modeConfig = $idmConfig->getIDMModeConfig();

        if (!empty($modeConfig['tid'])) {
            $tenantSrn = SrnConverter::fromString($modeConfig['tid']);

            if ($tenantSrn) {
                $region = $tenantSrn->getRegion();
            }
        }

        return $region;
    }
}
