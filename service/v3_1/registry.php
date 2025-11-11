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

require_once 'service/v3/registry.php';

class registry_v3_1 extends registry_v3
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
            'get_entry',
            'get_entries',
            'get_entry_list',
            'search_by_module',
            'get_available_modules',
            'get_module_fields_md5',
        ]);
        $this->getLogger()->info('END: registry->registerFunction');
    }
}
