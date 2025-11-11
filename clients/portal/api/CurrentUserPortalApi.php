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

class CurrentUserPortalApi extends CurrentUserApi
{
    /**
     * Retrieves the current portal user info
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function retrieveCurrentUser(ServiceBase $api, array $args)
    {
        global $current_user;

        // Get the basics
        $user_data = $this->getBasicUserInfo($api->platform);

        // Fill in the portal specific information
        $ps = PortalFactory::getInstance('Session');
        $contact = $ps->getContact();
        $user_data['type'] = 'support_portal';
        $user_data['user_id'] = $current_user->id;
        $user_data['user_name'] = $current_user->user_name;
        $user_data['acl'] = $this->getAcls('portal');
        $user_data['id'] = $contact->id;
        $user_data['account_ids'] = $ps->getAccountIds();
        $user_data['primary_account_id'] = $contact->account_id ?? '';
        $user_data['primary_email_address'] = $contact->email1 ?? '';
        $user_data['full_name'] = $contact->full_name;
        $user_data['picture'] = $contact->picture;
        $user_data['portal_name'] = $contact->portal_name;
        if (isset($contact->preferred_language)) {
            $user_data['preferences']['language'] = $contact->preferred_language;
        }
        $user_data['site_user_id'] = $contact->getSiteUserId(true);
        $user_data['cookie_consent'] = !empty($contact->cookie_consent);
        return ['current_user' => $user_data];
    }

    /**
     * Updates current portal users info
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateCurrentUser(ServiceBase $api, array $args)
    {
        $contact = PortalFactory::getInstance('Session')->getContact();

        // setting these for the loadBean
        $args['module'] = $contact->module_name;
        $args['record'] = $contact->id;

        $id = $this->updateBean($contact, $api, $args);

        return $this->retrieveCurrentUser($api, $args);
    }

    /**
     * Checks a given password and sends back the contact bean if the password matches
     *
     * @param string $passwordToVerify
     * @return Contact
     */
    protected function getUserIfPassword($passwordToVerify)
    {
        $contact = PortalFactory::getInstance('Session')->getContact();

        $currentPassword = $contact->portal_password;
        if (User::checkPassword($passwordToVerify, $currentPassword)) {
            return $contact;
        }

        return null;
    }

    /**
     * Changes a portal password for a contact from old to new
     *
     * @param Contact $bean Contact bean
     * @param string $old Old password
     * @param string $new New password
     * @return array
     */
    protected function changePassword(SugarBean $bean, $old, $new)
    {
        $bean->portal_password = User::getPasswordHash($new);
        $bean->save();
        return [
            'valid' => true,
            'message' => 'Password updated.',
            'expiration' => null,
        ];
    }

    /**
     * Gets the preference for user login expiration
     *
     * @return null
     */
    protected function getUserLoginExpirationPreference()
    {
        return null;
    }

    /**
     * Manipulates the ACLs for portal
     *
     * @param array $acls
     * @return array
     */
    protected function verifyACLs(array $acls)
    {
        $acls['admin'] = 'no';
        $acls['developer'] = 'no';
        $acls['delete'] = 'no';
        $acls['import'] = 'no';
        $acls['export'] = 'no';
        $acls['massupdate'] = 'no';

        return $acls;
    }

    /**
     * Enforces module specific ACLs
     *
     * @param array $acls
     * @return array
     */
    protected function enforceModuleACLs(array $acls)
    {
        // Filters! Filters for everyone!!!
        $acls['Filters']['create'] = 'yes';
        // nobody can create new Dashboards
        $acls['Dashboards']['create'] = 'no';

        // This is a change in the ACL's for users without Accounts
        $ps = PortalFactory::getInstance('Session');
        $accounts = $ps->getAccountIds();
        if (empty($accounts)) {
            // This user has no accounts, modify their ACL's so that they match up with enforcement
            $acls['Accounts']['access'] = 'no';
            if (!PortalFactory::getInstance('Settings')->allowCasesForContactsWithoutAccount()) {
                $acls['Cases']['access'] = 'no';
            }
        }
        foreach ($acls as $modName => $modAcls) {
            if ($modName === 'Contacts') {
                continue;
            }

            $acls[$modName]['edit'] = 'no';
        }

        return $acls;
    }

    /**
     * @return Contact
     * @deprecated use PortalFactory::getInstance('Session')->getContact()
     */
    protected function getPortalContact()
    {
        return PortalFactory::getInstance('Session')->getContact();
    }
}
