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

namespace Sugarcrm\Sugarcrm\SugarCloud;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserApi
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var Discovery
     */
    private $disco;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UserApi reset MFA endpoint
     */
    public const RESET_MFA_ENDPOINT = '/v1alpha/iam/mfa/reset';

    /**
     * UserApi request timeout
     */
    public const REQUEST_TIMEOUT = 10;

    /**
     * UserApi constructor.
     * @param Client $httpClient
     * @param Discovery $disco
     * @param ContainerInterface $container
     */
    public function __construct(Client $httpClient, Discovery $disco, ContainerInterface $container)
    {
        $this->httpClient = $httpClient;
        $this->disco = $disco;
        $this->logger = $container->get(LoggerInterface::class);
    }

    /**
     * @param string $userSrn
     * @param string $userToken
     * @return bool
     */
    public function resetMfa(string $userSrn, string $userToken): bool
    {
        $userApiURL = $this->disco->getServiceUrl('iam-user-http:v1alpha');
        if (!$userApiURL) {
            return false;
        }

        try {
            $response = $this->httpClient->put(
                $userApiURL . static::RESET_MFA_ENDPOINT . '/' . $userSrn,
                [
                    'headers' => ['Authorization' => 'Bearer ' . $userToken],
                    'timeout' => static::REQUEST_TIMEOUT,
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('The error in sending reset MFA response for user %s. %s', $userSrn, $e->getMessage())
            );
            return false;
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= Response::HTTP_BAD_REQUEST) {
            $this->logger->error(
                sprintf(
                    'The error (%d) in reset MFA for user. %s',
                    $statusCode,
                    $response->getBody()->getContents()
                )
            );
            return false;
        }

        return true;
    }
}
