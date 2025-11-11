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

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

class MetricsFilterApi extends FilterApi
{
    public function registerApiRest()
    {
        return [
            'filterModuleAll' => [
                'reqType' => 'GET',
                'path' => ['Metrics'],
                'pathVars' => ['module'],
                'method' => 'filterList',
                'jsonParams' => ['filter'],
                'shortHelp' => 'List of all records in this module',
                'exceptions' => [
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionError',
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
        ];
    }

    /**
     * Metric Worksheet Filter API Handler
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function filterList(ServiceBase $api, array $args, $acl = 'list')
    {
        // if filter is not defined, define it
        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }

        $sellLicenses = [
            'CURRENT',
            'SUGAR_SELL',
            'SUGAR_SELL_ESSENTIALS',
            'SUGAR_SELL_BUNDLE',
            'SUGAR_SELL_PREMIER_BUNDLE',
            'SUGAR_SELL_ADVANCED_BUNDLE',
        ];

        //Whether the user is licensed to sell
        $isSell = false;

        global $current_user;

        if ($current_user) {
            //Get current  user  licenses
            $userLicenses = SubscriptionManager::instance()->getAllUserSubscriptions($current_user);
            foreach ($userLicenses as $license) {
                $isSell = in_array($license, $sellLicenses);

                if ($isSell) {
                    break;
                }
            }
        }

        if (!$isSell) {
            $args['filter'][] = ['metric_module' => ['$not_in' => ['Opportunities']]];
        }

        return parent::filterList($api, $args, $acl);
    }
}
