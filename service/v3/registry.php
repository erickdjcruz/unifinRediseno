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

require_once 'service/v2/registry.php'; //Extend off of v2 registry

class registry_v3 extends registry
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
            'get_module_fields_md5',
            'get_available_modules',
            'get_last_viewed',
            'get_upcoming_activities',
            'search_by_module',
            'get_relationships',
        ]);

        $this->getLogger()->info('END: registry->registerFunction');

        // END OF REGISTER FUNCTIONS
    }
}
