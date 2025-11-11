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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Marketing\MarketingExtras;
use Sugarcrm\Sugarcrm\Security\Validator\ConstraintBuilder;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;
use Sugarcrm\Sugarcrm\Marketing\MarketingExtrasContent;

/**
 * Marketing Extras API implementation.
 */
class MarketingExtrasApi extends SugarApi
{
    /**
     * The service for MarketingExtrasContent
     * @var MarketingExtrasContent
     */
    private $marketingExtrasContentService;

    /**
     * Instantiates (if empty) and returns the service for MarketingExtrasContent
     *
     * @return MarketingExtrasContent
     */
    protected function getMarketingExtrasContentService()
    {
        if (!isset($this->marketingExtrasContentService)) {
            $this->marketingExtrasContentService = new MarketingExtrasContent();
        }

        return $this->marketingExtrasContentService;
    }

    public function registerApiRest()
    {
        return [
            'getMarketingExtras' => [
                'reqType' => 'GET',
                'path' => ['login', 'content'],
                'method' => 'getMarketingExtras',
                'shortHelp' => 'An API to receive marketing extra URLs',
                'longHelp' => 'include/api/help/marketing_extras_get_help.html',
                'minVersion' => '11.2',
                'maxVersion' => '11.8',
                'noLoginRequired' => true,
            ],
            'getMarketingContentUrl' => [
                'reqType' => 'GET',
                'path' => ['login', 'marketingContentUrl'],
                'method' => 'getMarketingContentUrl',
                'shortHelp' => 'Gets the SugarCRM marketing content URL',
                'longHelp' => 'include/api/help/marketing_extras_content_get_help.html',
                'noLoginRequired' => true,
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.9',
            ],
        ];
    }

    /**
     * Gets and returns the marketing content URL
     *
     * @param ServiceBase $api
     * @param array $args
     * @return string The marketing content URL
     */
    public function getMarketingContentUrl(ServiceBase $api, array $args): string
    {
        $options = $this->parseArgs($args);
        $static = $args['static'] ?? false;
        return $this->getMarketingExtrasContentService()->getMarketingExtrasContentUrl($options['language'], $static);
    }

    /**
     * Retrieve JSON for receiving SugarCRM marketing content.
     *
     * @param ServiceBase $api The REST API instance.
     * @param array $args REST API arguments.
     * @return array Information on how to receive SugarCRM marketing content.
     * @deprecated Since 10.1.0.
     * @todo To be deprecated in the future and replaced with login/marketingContentUrl
     */
    public function getMarketingExtras(ServiceBase $api, array $args): array
    {
        $msg = 'This endpoint is deprecated as of 10.1.0 and will be removed in a future release.';
        LoggerManager::getLogger()->deprecated($msg);

        $marketingExtras = $this->getMarketingExtrasService();
        $contentUrl = '';
        $imageUrl = '';
        try {
            $options = $this->parseArgs($args);
            $lang = $options['language'];
            $contentUrl = $marketingExtras->getMarketingContentUrl($lang);
        } catch (Exception $e) {
            // deliberately swallow exceptions so we don't throw errors to the client
            LoggerManager::getLogger()->warn('Marketing Extras: ' . $e->getMessage());
        }
        try {
            $imageUrl = $marketingExtras->getBackgroundImageUrl();
        } catch (Exception $e) {
            // deliberately swallow exceptions so we don't throw errors to the client
            LoggerManager::getLogger()->warn('Marketing Extras: ' . $e->getMessage());
        }

        return [
            'content_url' => $contentUrl,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * Parse the REST API arguments to return desired options.
     * Also perform any necessary security checks.
     * @param array $args Associative array of REST API arguments.
     * @return array Associative array of options.
     */
    public function parseArgs(array $args): array
    {
        if (isset($args['selected_language'])) {
            $langConstraints = $this->getLanguageConstraints();
            $validator = $this->getValidator();
            $errors = $validator->validate($args['selected_language'], $langConstraints);
            if (safeCount($errors) === 0) {
                $lang = $args['selected_language'];
            }
        }
        return [
            'language' => $lang ?? null,
        ];
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
     * Creates a Constraint enforcing that an argument is a valid language.
     * @return \Symfony\Component\Validator\Constraint[] The created constraints.
     */
    private function getLanguageConstraints()
    {
        $langConstraintBuilder = new ConstraintBuilder();
        return $langConstraintBuilder->build(
            [
                'Assert\Language',
            ]
        );
    }

    public function getMarketingExtrasService()
    {
        return new MarketingExtras();
    }
}
