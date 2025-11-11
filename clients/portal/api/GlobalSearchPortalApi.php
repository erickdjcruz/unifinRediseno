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

/**
 *
 * GlobalSearch API for portal
 *
 *  The /globalsearch and //globalsearch endpoints for portal are currently not supported.
 *
 */
class GlobalSearchPortalApi extends SugarApi
{
    /**
     * Register endpoints
     * @return array
     */
    public function registerApiRest()
    {
        return [
            // /globalsearch
            'globalSearch' => [
                'reqType' => ['GET', 'POST'],
                'path' => ['globalsearch'],
                'pathVars' => [''],
                'method' => 'globalSearchPortal',
                'shortHelp' => 'Global search',
                'exceptions' => [
                    'SugarApiExceptionNoMethod',
                ],
            ],

            // /<module>/globalsearch
            'modulesGlobalSearch' => [
                'reqType' => ['GET', 'POST'],
                'path' => ['<module>', 'globalsearch'],
                'pathVars' => ['module', ''],
                'method' => 'globalSearchPortal',
                'shortHelp' => 'Global search',
                'exceptions' => [
                    'SugarApiExceptionNoMethod',
                ],
            ],
        ];
    }

    /**
     * GlobalSearch endpoint for portal (currently not supported)
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function globalSearchPortal(ServiceBase $api, array $args)
    {
        throw new SugarApiExceptionNoMethod();
    }
}
