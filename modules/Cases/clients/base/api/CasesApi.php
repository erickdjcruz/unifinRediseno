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

class CasesApi extends ModuleApi
{
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['Cases'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'Deprecated api kept for backward compatibility',
                'longHelp' => 'include/api/help/module_post_help.html',
                'maxVersion' => '11.5',
            ],
        ];
    }

    /**
     * @deprecated
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $msg = sprintf(
            '%s::%s is deprecated and will be removed in a future release.',
            self::class,
            __METHOD__
        );
        $msg .= ' For Portal specific API customizations please use ModulePortalApi.';
        LoggerManager::getLogger()->deprecated($msg);
        return parent::createRecord($api, $args);
    }
}
