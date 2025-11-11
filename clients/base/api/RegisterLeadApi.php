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


class RegisterLeadApi extends SugarApi
{
    /**
     * Fields that are expected from the client
     * @var array
     */
    protected $expectedFields = [
        'first_name',
        'last_name',
        'phone_work',
        'email',
        'primary_address_country',
        'primary_address_state',
        'account_name',
        'title',
        'preferred_language',
    ];

    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['Leads', 'register'],
                'pathVars' => ['module'],
                'method' => 'createLeadRecord',
                'shortHelp' => 'This method registers leads',
                'longHelp' => 'include/api/help/leads_register_post_help.html',
                'noLoginRequired' => true,
            ],
        ];
    }

    /**
     * Creates lead records
     * @param ServiceBase $apiServiceBase The API class of the request, used in cases where the API changes how the fields are pulled from the args array.
     * @param array $args The arguments array passed in from the API
     * @return array properties on lead bean formatted for display
     */
    public function createLeadRecord(ServiceBase $api, array $args)
    {
        $fields = [];
        $msg = 'This endpoint is deprecated for portal lead creation as of 9.2.0';
        LoggerManager::getLogger()->deprecated($msg);

        // Clients should always send the last name and the lead source
        $this->requireArgs($args, ['last_name', 'lead_source']);

        // Bug56194: Creation of a Lead SugarBean uses messages that require the use of the app strings.
        // In this case, lead_source is parsed in the email message that is sent out when a new lead is created.
        global $app_list_strings;
        global $current_language;
        if (!isset($app_list_strings)) {
            $app_list_strings = return_app_list_strings_language($current_language);
        }

        $bean = BeanFactory::newBean('Leads');

        // Force team and teamset because there is no current user to get them from
        $bean->team_set_id = '1';
        $bean->team_id = '1';

        // Bug 54515: Set modified by and created by users to assigned to user. If not set default to admin.
        $bean->update_modified_by = false;
        $bean->set_created_by = false;
        $bean->created_by = '1';
        $bean->modified_user_id = '1';

        foreach ($this->expectedFields as $field) {
            if (isset($args[$field])) {
                $fields[$field] = $args[$field];
            }
        }

        // Mimic the essential behavior of updateBean
        // Bug 54516 users not getting notified on new record creation
        $this->populateBean($bean, $api, $fields);
        $bean->save(true);
        return $bean->id;
    }
}
