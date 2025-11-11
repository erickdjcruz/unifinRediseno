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

/**
 * Convert the contact_id into parent_id.
 */
class SugarUpgradeUpgradeContactIdForExternalUsers extends UpgradeScript
{
    public $order = 9999;
    public $type = self::UPGRADE_DB;

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        if (version_compare($this->from_version, '14.0.0', '>=')) {
            return;
        }

        $externalUsersBean = BeanFactory::newBean('ExternalUsers');
        $table = $externalUsersBean->getTableName();

        $this->log("Copy contact_id value into parent_id in $table table.");

        $this->db->query("UPDATE $table SET parent_id = contact_id WHERE contact_id IS NOT NULL");
        $this->db->query("UPDATE $table SET parent_type = 'Contacts' WHERE contact_id IS NOT NULL");

        $this->log('Add external_user_id to the parent contact record.');

        $this->db->query("UPDATE contacts SET external_user_id = (SELECT id FROM external_users WHERE contact_id = contacts.id)");

        $this->log("Drop the contact_id column from the $table table.");
        $this->db->query("ALTER TABLE $table DROP COLUMN contact_id");
    }
}
