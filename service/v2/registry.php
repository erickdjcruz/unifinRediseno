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

/**
 * This class is responsible for providing all the registration of all the functions and complex types
 *
 */
class registry
{
    use SoapLogTrait;

    protected $serviceClass = null;

    /**
     * Constructor.
     *
     * @param Class - $serviceClass
     */
    public function __construct($serviceClass)
    {
        $this->serviceClass = $serviceClass;
    } // fn

    /**
     * It registers all the functions and types by doign a call back method on service object
     *
     */
    public function register()
    {
        $this->registerFunction();
    }

    /**
     * This mehtod registers all the functions on the service class
     *
     */
    protected function registerFunction()
    {
        // START OF REGISTER FUNCTIONS

        $this->getLogger()->info('Begin: registry->registerFunction');
        $this->serviceClass->getServer()->addFunction([
            'login',
            'logout',
            'get_entry',
            'get_entries',
            'get_entry_list',
            'set_relationship',
            'set_relationships',
            'get_relationships',
            'set_entry',
            'set_entries',
            'get_server_info',
            'get_user_id',
            'get_module_fields',
            'seamless_login',
            'set_note_attachment',
            'get_note_attachment',
            'set_document_revision',
            'get_document_revision',
            'search_by_module',
            'get_available_modules',
            'get_user_team_id',
            'set_campaign_merge',
            'get_entries_count',
            'get_report_entries',
        ]);

        $this->getLogger()->info('END: registry->registerFunction');

        // END OF REGISTER FUNCTIONS
    } // fn
} // clazz
