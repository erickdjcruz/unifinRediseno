<?php

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
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

require_once 'service/v3_1/registry.php';

class registry_v4 extends registry_v3_1
{
    /**
     * This method registers all the functions on the service class
     *
     */
    protected function registerFunction()
    {
        $this->getLogger()->info('Begin: registry->registerFunction');
        parent::registerFunction();
        $this->serviceClass->getServer()->addFunction([
            'search_by_module',
        ]);
        $this->getLogger()->info('END: registry->registerFunction');
    }
}
