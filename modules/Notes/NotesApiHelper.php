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

class NotesApiHelper extends SugarBeanApiHelper
{
    /**
     * This function sets the team & assigned user and sets up the contact & account relationship
     * for new Notes submitted via portal users.
     *
     * @param SugarBean $bean
     * @param array $submittedData
     * @param array $options
     * @return array
     */
    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        //TODO: need a more generic way to deal with file types
        if (isset($submittedData['file_mime_type'])) {
            unset($submittedData['file_mime_type']);
        }

        $data = parent::populateFromApi($bean, $submittedData, $options);

        //Only needed for Portal sessions
        $portalSession = PortalFactory::getInstance('Session');
        if ($portalSession->isActive()) {
            if (empty($bean->id)) {
                $bean->id = create_guid();
                $bean->new_with_id = true;
            }

            $this->addPortalUserDataToBean($portalSession->getContact(), $bean);
        }

        return $data;
    }

    /**
     * Adds portal user data to the Note record if created through the support portal
     * @param Contact $contact Contact bean
     * @param Note $note Note bean
     */
    public function addPortalUserDataToBean(Contact $contact, Note $note)
    {
        // This is an externally created record, mark it
        $note->entry_source = 'external';

        // Handle assignment
        $note->assigned_user_id = $contact->assigned_user_id;

        // And teams
        $note->team_id = $contact->fetched_row['team_id'];
        $note->team_set_id = $contact->fetched_row['team_set_id'];
        $note->acl_team_set_id = $contact->fetched_row['acl_team_set_id'];

        // And related data
        $note->account_id = $contact->account_id;
        $note->contact_id = $contact->id;
    }
}
