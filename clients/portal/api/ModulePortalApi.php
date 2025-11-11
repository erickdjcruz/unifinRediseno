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

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class ModulePortalApi extends ModuleApi
{
    public function registerApiRest(): array
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['<module>'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This API creates a new record through the portal platform and it relates automatically 
                    the Portal Contact (and optionally the Contact\'s Account) based on the portal visibility relationships',
                'longHelp' => 'include/api/help/module_post_help.html',
            ],
        ];
    }

    /**
     * Create the record and perform additional actions for Portal
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function createRecord(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['module']);

        // act for portal specific sessions
        $ps = PortalFactory::getInstance('Session');
        if ($ps->isActive()) {
            // load the bean definition
            $bean = BeanFactory::newBean($args['module']);

            // get the visibility context
            $visContext = $ps->getVisibilityContext($bean);

            // get the logged in contact
            $contact = $ps->getContact();

            if (!empty($contact)) {
                $args['assigned_user_id'] = $contact->assigned_user_id;
                $args['team_id'] = $contact->team_id;
                $args['team_set_id'] = $contact->team_set_id;
                $args['acl_team_set_id'] = $contact->acl_team_set_id;
                $args['primary_contact_id'] = $contact->id;

                // the following code populates the relationships with Contacts and Accounts on the same api call
                // by leveraging the parent ModuleApi's getRelatedRecordArguments and linkRelatedRecords

                // handle contact relationship
                if (!empty($visContext->getContactsRelationshipLink())) {
                    // this is required so that the visibility for Contacts without Accounts works immediately for the record that is being saved
                    // otherwise the Contacts without Accounts would not be able to retrieve the record just saved

                    $args[$visContext->getContactsRelationshipLink()] = [
                        'add' => [
                            $contact->id,
                        ],
                    ];
                }

                // handle account relationship
                if (!empty($contact->account_id) && !empty($visContext->getAccountsRelationshipLink())) {
                    // this is required so that the visibility for Contacts with Accounts works immediately for the record that is being saved
                    // otherwise the Contacts with Accounts would not be able to retrieve the record just saved

                    $args[$visContext->getAccountsRelationshipLink()] = [
                        'add' => [
                            $contact->account_id,
                        ],
                    ];
                }
            }
        }

        // create the record using the ModuleApi parent method
        return parent::createRecord($api, $args);
    }
}
