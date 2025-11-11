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

use Sugarcrm\Sugarcrm\CSP\ContentSecurityPolicy;
use Sugarcrm\Sugarcrm\CSP\Directive;

/**
 * Handler for Config API
 */
class AwsConfigApiHandler extends ConfigApiHandler
{
    /**
     * @inheritdoc
     */
    public function getConfig(ServiceBase $api, array $args)
    {
        $admin = BeanFactory::getBean('Administration');
        if ($admin->isLicensedForServe() || $admin->isLicensedForSell()) {
            return $admin->retrieveSettings('aws', true)->settings;
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function setConfig(ServiceBase $api, array $args)
    {
        $isValidUrls = $this->validateUrls(
            $args['aws_login_url'] ?? '',
            $args['aws_connect_url'] ?? '',
        );

        if (!$isValidUrls) {
            throw new \InvalidArgumentException('Invalid url was provided');
        }

        $admin = BeanFactory::getBean('Administration');

        // We only want to do this for Serve or Sell licensed instances
        if ($admin->isLicensedForServe() || $admin->isLicensedForSell()) {
            $category = 'aws';
            $prefix = $category . '_';
            $changes = [];

            $oldSettings = $admin->retrieveSettings($category)->settings;

            foreach ($args as $key => $value) {
                // Look specifically for anything prefixed with aws_
                if (substr($key, 0, 4) === $prefix) {
                    if ($admin->saveSetting($category, str_replace($prefix, '', $key), $value, $api->platform)) {
                        $changes[$key] = $value;
                    }
                }
            }

            if ($changes) {
                $this->updateAWSDomainsOnCSP($changes, $oldSettings);
                $this->updateAWSDomainsOnCSPForPortal($changes);

                // Only reset the config metadata cache if there were changes to save
                self::clearCache();
                return $changes;
            }
        }

        return [];
    }

    protected function validateUrls(string ...$urls): bool
    {
        foreach ($urls as $url) {
            if ($url === '') {
                continue;
            }

            $parts = parse_url($url);

            if (isset($parts['scheme']) && in_array($parts['scheme'], ['http', 'https'], true)) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * Update allowed AWS domains in content security policies
     *
     * @param array $settings
     * @param array $oldSettings
     */
    public function updateAWSDomainsOnCSP(array $settings, array $oldSettings)
    {
        $awsUrlKey = 'aws_connect_url';

        // if the key does not exist, nothing to update
        if (!array_key_exists($awsUrlKey, $settings)) {
            return;
        }

        $oldAwsUrl = $oldSettings[$awsUrlKey];
        $awsUrl = $settings[$awsUrlKey];

        $domainsToAppend = $domainsToRemove = [];
        $allowListDomains = $this->getAWSAllowListDomains($settings);

        if (empty($awsUrl)) {
            // remove previously added domains as the new value is empty
            $domainsToRemove = $allowListDomains;
        } else {
            $domainsToAppend = $allowListDomains;
        }

        // remove the previously added AWS URL
        if (!empty($oldAwsUrl)) {
            $domainsToRemove[] = parse_url($oldAwsUrl, PHP_URL_HOST);
        }

        $this->saveToCSP($domainsToRemove, $domainsToAppend);
    }

    /**
     * Update allowed AWS domains in content security policies for Portal
     *
     * @param array $settings
     */
    public function updateAWSDomainsOnCSPForPortal(array $settings)
    {
        $enablePortalChatKey = 'aws_connect_enable_portal_chat';

        if (!array_key_exists($enablePortalChatKey, $settings)) {
            return;
        }

        $shouldEnablePortalChat = $settings[$enablePortalChatKey];

        $domainsToAppend = $domainsToRemove = [];
        $allowListDomains = $this->getAWSAllowListDomainsForPortal();

        if ($shouldEnablePortalChat) {
            $domainsToAppend = $allowListDomains;
        } else {
            $domainsToRemove = $allowListDomains;
        }

        $this->saveToCSP($domainsToRemove, $domainsToAppend);
    }

    /**
     * Get the list of allowed domains for AWS
     *
     * @param array $args
     * @return array
     */
    public function getAWSAllowListDomains(array $args): array
    {
        $awsConfig = $this->getSugarConfig()->get('aws_connect');
        $allowListDomains = $awsConfig['allow_list_domains'];
        $awsUrl = $args['aws_connect_url'];

        if (!empty($awsUrl)) {
            $allowListDomains[] = parse_url($awsUrl, PHP_URL_HOST);
        }

        return $allowListDomains;
    }

    /**
     * Get the list of allowed domains for AWS for Portal
     *
     * @return array
     */
    public function getAWSAllowListDomainsForPortal(): array
    {
        return $this->getSugarConfig()->get('aws_connect')['portal_allow_list_domains'];
    }

    /**
     * Remove/save specified domains from CSP
     *
     * @param $domainsToRemove
     * @param $domainsToAppend
     */
    public function saveToCSP($domainsToRemove, $domainsToAppend)
    {
        $csp = ContentSecurityPolicy::fromAdministrationSettings();

        foreach ($domainsToRemove as $domain) {
            $directive = Directive::createHidden('default-src', $domain);
            $csp->removeDirective($directive);
            $directive = Directive::createHidden('connect-src', $domain);
            $csp->removeDirective($directive);
        }

        foreach ($domainsToAppend as $domain) {
            $directive = Directive::createHidden('default-src', $domain);
            $csp->appendDirective($directive);
            $directive = Directive::createHidden('connect-src', $domain);
            $csp->appendDirective($directive);
        }

        $csp->saveToSettings();
    }

    /**
     * Clears required metadata cache
     */
    protected function clearCache(): void
    {
        \MetaDataManager::refreshSectionCache(\MetaDataManager::MM_CONFIG);
    }

    /**
     * @return SugarConfig|null
     */
    public function getSugarConfig(): ?SugarConfig
    {
        return SugarConfig::getInstance();
    }
}
