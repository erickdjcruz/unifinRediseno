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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionPrefetcher;

class NotificationsFilterApi extends FilterApi
{
    //Override the parent definition to allow responses to be cached for a short period client side
    public function registerApiRest()
    {
        $parentDef = parent::registerApiRest();
        if (!empty($parentDef['filterModuleAll'])) {
            $def = array_merge($parentDef['filterModuleAll'], [
                'path' => ['Notifications'],
                //Should be the only change from the parent method
                'cacheEtag' => true,
            ]);

            return ['retrieve' => $def];
        }

        return [];
    }

    /**
     * Returns the records for the module and filter provided.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array The REST response as a PHP array.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function filterList(ServiceBase $api, array $args, $acl = 'list')
    {
        $result = parent::filterList($api, $args, $acl);
        Container::getInstance()->get(SubscriptionPrefetcher::class)->register();
        return $result;
    }
}
