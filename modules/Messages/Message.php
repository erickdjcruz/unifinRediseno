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

class Message extends Basic
{
    public $aws_contact_id;
    public $module_dir = 'Messages';
    public $object_name = 'Message';
    public $table_name = 'messages';
    public $module_name = 'Messages';
    public $importable = true;

    public $rel_users_table = 'messages_users';
    public $rel_contacts_table = 'messages_contacts';
    public $rel_leads_table = 'messages_leads';

    /**
     * @inheritdoc
     */
    public function save($check_notify = false)
    {
        $id = parent::save($check_notify);
        $this->handleInviteesForUserAssign();

        return $id;
    }

    /**
     * Handles invitees list when Message is assigned to a user.
     * - new user should be added to invitees, if it is not already there;
     * - on create when current user does not assign Message to themselves, add current user to invitees.
     */
    protected function handleInviteesForUserAssign()
    {
        $this->load_relationship('invitee_users');
        $existingUsers = $this->invitee_users->get();

        if (isset($this->assigned_user_id) && !safeInArray($this->assigned_user_id, $existingUsers)) {
            $this->invitee_users->add($this->assigned_user_id);
        }
    }

    /**
     * {@inheritDoc}
     *
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
