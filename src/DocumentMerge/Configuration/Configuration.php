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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Configuration;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;
use Sugarcrm\IdentityProvider\Srn\Converter as SrnConverter;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \SugarConfig
     */
    private $config;

    public function __construct(\SugarConfig $sugarConfig)
    {
        $this->config = $sugarConfig;
    }

    /**
     * Returns the URL to DocumentMerge service for the given region.
     *
     * @return string
     */
    public function getServiceURL(): string
    {
        $mergeConfig = $this->config->get('document_merge');
        $region = $this->getRegion();

        if ($region && !empty($mergeConfig['service_urls'][$region])) {
            return $mergeConfig['service_urls'][$region];
        } else {
            return $mergeConfig['service_urls']['default'] ?? '';
        }
    }

    /**
     * Gets aws region from idm config.
     *
     * @return string
     */
    protected function getRegion(): string
    {
        $region = 'default';
        $idmConfig = new IdmConfig($this->config);
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
     * Retrieves the system's unique key.
     * It is being used on the merge server to identify the client
     *
     * @return string
     */
    public function getSystemKey(): string
    {
        return $this->config->get('unique_key');
    }

    /**
     * Returns the url of the sugar system
     *
     * @return string
     */
    public function getSystemUrl(): string
    {
        return $this->config->get('site_url');
    }

    /**
     * Get the number of times the service will try to merge the document
     *
     * @return int
     */
    public function getMaxRetries(): int
    {
        $mergeConfig = $this->config->get('document_merge');

        return $mergeConfig['max_retries'] ?? 3;
    }
}
