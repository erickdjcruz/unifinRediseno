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
 * Fixes email_type field values that were incorrectly set on Note records
 * used to store EmailTemplates attachments before 12.0
 */
class SugarUpgradeFixEmailTemplateAttachments extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_DB;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->updateNotes();
        }
    }

    /**
     * Returns whether this upgrader needs to be run
     *
     * @return bool
     */
    protected function shouldRun(): bool
    {
        return version_compare($this->from_version, '12.0.0', '<');
    }

    /**
     * Updates the notes table to fix the email_type field where necessary
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function updateNotes()
    {
        // Subquery gets the IDs of EmailTemplates that have notes/attachments.
        // We do it this way because joins are not allowed in update statements
        $subquery = $this->db->getConnection()->createQueryBuilder();
        $subquery->select('email_templates.id')
            ->distinct()
            ->from('email_templates')
            ->where(
                $subquery->expr()->eq('notes.email_id', 'email_templates.id')
            );

        // Main query updates the rows of notes
        $qb = $this->db->getConnection()->createQueryBuilder();
        $qb->update('notes')
            ->set('email_type', $qb->createPositionalParameter('EmailTemplates'))
            ->where(
                $qb->expr()->eq('notes.email_type', $qb->createPositionalParameter('Emails'))
            )
            ->andWhere($qb->expr()->in('notes.email_id', $subquery->getSQL()))
            ->execute();
    }
}
