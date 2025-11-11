<?php

declare(strict_types=1);

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

use Sugarcrm\Sugarcrm\Search\Factory as SearchFactory;

/**
 *
 * Generic Search API
 *
 *
 */
class GenericSearchApi extends SugarApi
{
    /**
     * Register endpoints
     * @return array
     */
    public function registerApiRest()
    {
        return [
            // /genericsearch
            'genericSearch' => [
                'reqType' => ['GET', 'POST'],
                'path' => ['genericsearch'],
                'pathVars' => [''],
                'method' => 'genericSearch',
                'shortHelp' => 'Generic search',
                'longHelp' => 'include/api/help/generic_search_get_help.html',
                //'minVersion' => '11.10',
                'exceptions' => [
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionSearchUnavailable',
                    'SugarApiExceptionSearchRuntime',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionRequestMethodFailure',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
        ];
    }

    /**
     * genericSearch endpoint
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionMissingParameter
     */
    public function genericSearch(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['q']);

        $sugarConfig = \SugarConfig::getInstance();
        $settings = $sugarConfig->get('generic_search');
        if (empty($settings)) {
            $settings = $sugarConfig->get('portal'); // backward compatibility
        }
        // No modules configured for search
        if (empty($settings['modules'])) {
            throw new SugarApiExceptionRequestMethodFailure('Generic search modules not configured');
        }
        if (!empty($args['module_list'])) {
            $modulesToSearch = explode(',', $args['module_list']);
            foreach ($modulesToSearch as $module) {
                // ensure all passed in modules are suppoorted
                if (!safeInArray($module, $settings['modules'])) {
                    throw new SugarApiExceptionInvalidParameter('Module not supported: ' . $module);
                }
            }
        } else {
            // default to search all supported modules
            $args['module_list'] = implode(',', $settings['modules']);
        }

        // At the moment we only support one service.
        // If we are to support multiple services at the same time in the future,
        // we need to be able to handle pagination, etc.
        if (empty($settings['services'])) {
            $providerType = 'Elastic';
        } else {
            $providerType = $settings['services'][0];
        }

        // get the service provider
        $provider = SearchFactory::getInstance('Providers\\' . $providerType);
        if ($provider && $provider instanceof Sugarcrm\Sugarcrm\Search\Providers\Search) {
            // call the provider to get data
            return $provider->getData($api, $args);
        } else {
            throw new SugarApiExceptionRequestMethodFailure(
                sprintf('Generic search provider, %s not available', $providerType)
            );
        }
    }
}
