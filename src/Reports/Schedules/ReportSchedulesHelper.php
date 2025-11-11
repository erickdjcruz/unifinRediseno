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

namespace Sugarcrm\Sugarcrm\Reports\Schedules;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\IdentityProvider\Srn\Converter as SrnConverter;
use Sugarcrm\Sugarcrm\Security\HttpClient\ExternalResourceClient;
use League\OAuth2\Client\Token\AccessToken;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\OAuth2\Client\Provider\IdmProvider;

class ReportSchedulesHelper
{
    /**
     * Returns the URL to ChartGen service for the given region.
     *
     * @return string
     */
    protected function getChartGenServiceURL($config): string
    {
        $chartServiceConfig = $config->get('chart_service');
        $region = $this->getRegion();

        if ($region && !empty($chartServiceConfig['service_urls'][$region])) {
            return $chartServiceConfig['service_urls'][$region];
        } else {
            return $chartServiceConfig['service_urls']['default'] ?? '';
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

    /**
     * Get chart data
     *
     * @param array $postData
     */
    public function retrieveChartData($postData)
    {
        $sugarConfig = \SugarConfig::getInstance();
        $serviceUrl = $this->getChartGenServiceURL($sugarConfig);
        $client = new ExternalResourceClient();
        $response = $client->post("{$serviceUrl}/generate", json_encode($postData), ['Content-Type' => 'application/json']);

        return $response;
    }
}
