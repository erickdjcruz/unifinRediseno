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
 * Fix Notes' attachment_flag field
 */
class SugarUpgradeFixNotesAttachmentFlag extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '11.3.0', '>=')) {
            return;
        }

        // UPDATE notes
        // SET attachment_flag = 0
        // WHERE deleted = 0
        // AND note_parent_id IS NULL
        // AND (parent_id IS NULL OR parent_type <> 'KBContents')
        // AND email_id IS NULL
        // AND filename IS NOT NULL
        $qb = $this->db->getConnection()->createQueryBuilder();
        $qb->update('notes')
            ->set('attachment_flag', 0)
            ->where($qb->expr()->eq('deleted', 0))
            ->andWhere($qb->expr()->isNull('note_parent_id'))
            ->andWhere(
                $qb->expr()->or(
                    $qb->expr()->isNull('parent_id'),
                    $qb->expr()->neq('parent_type', $qb->createPositionalParameter('KBContents'))
                )
            )
            ->andWhere($qb->expr()->isNull('email_id'))
            ->andWhere($qb->expr()->isNotNull('filename'));
        $qb->execute();
    }
}
