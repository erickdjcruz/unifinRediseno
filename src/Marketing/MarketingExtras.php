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
declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Marketing;

use SugarConfig;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Security\Validator\ConstraintBuilder;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;

class MarketingExtras
{
    /**
     * The helper for MarketingExtrasHelper
     * @var MarketingExtrasHelper
     */
    private $marketingExtrasHelper = null;

    /**
     * Creates the MarketingExtrasHelper (if it doesn't exist) and return it
     *
     * @return MarketingExtrasHelper
     */
    public function getMarketingExtrasHelper(): MarketingExtrasHelper
    {
        if ($this->marketingExtrasHelper === null) {
            $this->marketingExtrasHelper = new MarketingExtrasHelper();
        }

        return $this->marketingExtrasHelper;
    }

    /**
     * Request the marketing content URL from the marketing extras server.
     * @param null|string $language The requested content language, if any.
     * @return string The URL for marketing content.
     * @throws \Exception If the marketing extras URL is not
     *   provided.
     */
    public function getMarketingContentUrl(?string $language): string
    {
        $helper = $this->getMarketingExtrasHelper();

        $sugarDetails = $helper->getSugarDetails();
        $queryParams = [
            'language' => $helper->chooseLanguage($language),
            'version' => $sugarDetails['version'],
            'flavor' => strtolower($sugarDetails['flavor']),
            'build' => $sugarDetails['build'],
        ];

        $marketingExtrasSandboxTest = $helper->getSugarConfig('marketing_extras_sandbox_test');
        if (isset($marketingExtrasSandboxTest)) {
            $queryParams['test'] = $marketingExtrasSandboxTest;
        }

        $url = $this->getMarketingExtrasUrl();
        $contentUrl = $this->fetchMarketingContentInfo($url, $queryParams)['content_url'];
        if (!$this->validateUrl($contentUrl)) {
            throw new \Exception('content_url is not actually an HTTP(S) URL');
        } else {
            return $contentUrl;
        }
    }

    /**
     * Make a request to the given URL with the given query parameters, then
     * return the result as an associative array.
     * @param string $url The URL to make a request to.
     * @param array $queryParams Query parameters in key-value form.
     * @return array The result of the request, as an associative array.
     * @throws \Exception If the request or JSON decoding fails.
     */
    public function fetchMarketingContentInfo(string $url, array $queryParams): array
    {
        $marketingContents = $this->openUrl($url, $queryParams);
        $marketingContentArray = $this->getJson($marketingContents);
        return $marketingContentArray;
    }

    /**
     * Get marketing extras URL, with check to make sure it's actually a URL.
     * @return string The marketing extras URL.
     * @throws \Exception If there is an issue with the marketing extras URL.
     */
    public function getMarketingExtrasUrl(): string
    {
        $marketingExtrasUrl = $this->getMarketingExtrasHelper()->getSugarConfig('marketing_extras_url');
        if (empty($marketingExtrasUrl)) {
            throw new \Exception('marketing_extras_url is not provided');
        }
        if (!$this->validateUrl($marketingExtrasUrl)) {
            throw new \Exception('marketing_extras_url is not actually an HTTP(S) URL');
        }
        return $marketingExtrasUrl;
    }

    /**
     * Get background image URL, with check to make sure it's actually a URL.
     * @return string The background image URL.
     * @throws \Exception If there is an issue with the background image URL.
     */
    public function getBackgroundImageUrl(): string
    {
        $helper = $this->getMarketingExtrasHelper();

        $backgroundImageUrl = $helper->getSugarConfig('background_image');
        if (!empty($backgroundImageUrl) && $this->validateUrl($backgroundImageUrl)) {
            return $backgroundImageUrl;
        }
        $defaultBackgroundImage = $helper->getSugarConfig('default_background_image');
        if (!empty($defaultBackgroundImage) && $this->validateFile($defaultBackgroundImage)) {
            $backgroundImageUrl = rtrim($helper->getSugarConfig('site_url'), '/') . '/' . $defaultBackgroundImage;
            return $backgroundImageUrl;
        }
        throw new \Exception('background_image is not provided');
    }

    /**
     * Determine the language to request marketing details for.
     * @param null|string $language The client's preferred language.
     * @return string The language to use. If set, uses the client's preferred
     *   language, then falls back to the default language of this Sugar
     *   instance, and finally to en_us.
     * @deprecated Since 10.1, will be removed in 11.0. Use MarketingExtrasHelper::chooseLanguage
     */
    public function chooseLanguage(?string $language): string
    {
        \LoggerManager::getLogger()->deprecated(
            'MarketingExtras::chooseLanguage has been moved to MarketingExtrasHelper::chooseLanguage ' .
            'and will be removed from MarketingExtras in 11.0'
        );

        return $this->getMarketingExtrasHelper()->chooseLanguage($language);
    }

    /**
     * Make a request to the given URL, with the given query parameters.
     * @param string $baseUrl The URL to make the request to.
     * @param array $queryParams Query parameters in key-value form.
     * @return string The body of the HTTP response.
     * @throws \Exception If the request fails.
     */
    private function openUrl(string $baseUrl, array $queryParams): string
    {
        $queryString = http_build_query($queryParams);
        $url = $baseUrl . '?' . $queryString;

        $curlHandle = curl_init($url);
        if ($curlHandle === false) {
            throw new \Exception('Could not open connection to marketing extras server');
        }

        // setting CURLOPT_FAILONERROR so I don't have to check curl_error later
        // and CURLOPT_RETURNTRANSFER so curl_exec returns the page contents
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);

        $this->configureProxy($curlHandle);

        $contents = curl_exec($curlHandle);
        curl_close($curlHandle);
        if ($contents === false) {
            throw new \Exception('Retrieving URL from marketing extras server failed');
        }

        return $contents;
    }

    /**
     * Configure curl for the system proxy if necessary.
     * @param $ch Curl handle.
     */
    private function configureProxy($ch)
    {
        $proxy_config = \Administration::getSettings('proxy');

        if (!empty($proxy_config) &&
            !empty($proxy_config->settings['proxy_on']) &&
            $proxy_config->settings['proxy_on'] == 1
        ) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_config->settings['proxy_host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_config->settings['proxy_port']);
            if (!empty($proxy_config->settings['proxy_auth'])) {
                curl_setopt(
                    $ch,
                    CURLOPT_PROXYUSERPWD,
                    $proxy_config->settings['proxy_username'] . ':' . $proxy_config->settings['proxy_password']
                );
            }
        }
    }

    /**
     * Parse JSON and throw an error if it's not valid.
     * @param string $jsonString The string to parse.
     * @return array The decoded JSON string as an associative array.
     * @throws \Exception In the event the JSON is invalid.
     */
    private function getJson(string $jsonString): array
    {
        $array = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Marketing URL request did not return valid JSON');
        }
        return $array;
    }

    /**
     * Get the sugar_config global variable.
     * @param string $key Key to get.
     * @param * $default Default value.
     * @return * The value of the config flag, or the default.
     * @deprecated Since 10.1, will be removed in 11.0. Use MarketingExtrasHelper::getSugarConfig
     */
    protected function getSugarConfig(string $key, $default = null)
    {
        \LoggerManager::getLogger()->deprecated(
            'MarketingExtras::getSugarConfig has been moved to MarketingExtrasHelper::getSugarConfig ' .
            'and will be removed from MarketingExtras in 11.0'
        );

        return $this->getMarketingExtrasHelper()->getSugarConfig($key, $default);
    }

    /**
     * Retrieves the Symfony validator service.
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface The
     *   validator service.
     */
    private function getValidator()
    {
        $container = Container::getInstance();
        return $container->get(Validator::class);
    }

    /**
     * Creates a Constraint enforcing that an argument is a valid URL.
     * @return \Symfony\Component\Validator\Constraint[] The created constraints.
     */
    private function getUrlConstraints()
    {
        $urlConstraintBuilder = new ConstraintBuilder();
        return $urlConstraintBuilder->build(
            [
                // only allows HTTP and HTTPS by default, which is what we want
                // (i.e. we don't want file://)
                'Assert\Url',
            ]
        );
    }

    /**
     * Creates a Constraint enforcing that an argument is a valid file.
     * @return \Symfony\Component\Validator\Constraint[] The created constraints.
     */
    private function getFileConstraints()
    {
        $fileConstraintBuilder = new ConstraintBuilder();
        return $fileConstraintBuilder->build(
            [
                // only allows files under webroot
                'Assert\File',
            ]
        );
    }

    /**
     * Retrieve the build number, flavor, and version of this Sugar instance.
     * @return array An array consisting of build number, flavor, and version
     *   details.
     * @deprecated Since 10.1, will be removed in 11.0. Use MarketingExtrasHelper::getSugarDetails
     */
    protected function getSugarDetails(): array
    {
        \LoggerManager::getLogger()->deprecated(
            'MarketingExtras::getSugarDetails has been moved to MarketingExtrasHelper::getSugarDetails ' .
            'and will be removed from MarketingExtras in 11.0'
        );

        return $this->getMarketingExtrasHelper()->getSugarDetails();
    }

    /**
     * Validate url.
     * @param string $url The url to validate.
     * @return bool True if the url is valid, false otherwise.
     */
    private function validateUrl(string $url): bool
    {
        $validator = $this->getValidator();
        $constraints = $this->getUrlConstraints();
        $errors = $validator->validate($url, $constraints);
        return safeCount($errors) === 0;
    }

    /**
     * Validate file.
     * @param string $file The file to validate.
     * @return bool True if the file is valid, false otherwise.
     */
    private function validateFile(string $file): bool
    {
        $validator = $this->getValidator();
        $constraints = $this->getFileConstraints();
        $errors = $validator->validate($file, $constraints);
        return safeCount($errors) === 0;
    }
}
