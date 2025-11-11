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

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

/**
 *
 * PortalSearch API
 *
 *
 */
class SearchPortalApi extends SugarApi
{
    /**
     * Register endpoints
     * @return array
     */
    public function registerApiRest()
    {
        return [
            // /portalsearch
            'portalSearch' => [
                'reqType' => ['GET', 'POST'],
                'path' => ['portalsearch'],
                'pathVars' => [''],
                'method' => 'portalSearch',
                'shortHelp' => 'Portal search',
                'longHelp' => 'include/api/help/portal_search_get_help.html',
                'minVersion' => '11.6',
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
     * portalSearch endpoint
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionMissingParameter
     * @deprecated Since 10.2.0.
     */
    public function portalSearch(ServiceBase $api, array $args): array
    {
        $msg = 'This endpoint is deprecated as of 10.2.0 and will be removed in a future release.';
        $msg .= ' Use genericsearch instead.';
        LoggerManager::getLogger()->deprecated($msg);

        $this->requireArgs($args, ['q']);

        $sugarConfig = \SugarConfig::getInstance();
        $settings = $sugarConfig->get('portal');
        // No modules configured for search
        if (empty($settings['modules'])) {
            throw new SugarApiExceptionRequestMethodFailure('Portal search modules not configured');
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
        $provider = PortalFactory::getInstance('Search\\' . $providerType);
        if ($provider && $provider instanceof Sugarcrm\Sugarcrm\Portal\Search\Search) {
            // call the provider to get data
            return $provider->getData($api, $args);
        } else {
            throw new SugarApiExceptionRequestMethodFailure(
                sprintf('Portal search provider, %s not available', $providerType)
            );
        }
    }
}
