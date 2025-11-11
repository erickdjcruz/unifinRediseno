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
 * Class AttachmentRelationship
 *
 * Represents a bean-based one-to-many relationship for attachments.
 */
class AttachmentRelationship extends One2MBeanRelationship
{
    /**
     * Unlinked attachments are deleted, as they cannot
     * exist by themself.
     *
     * {@inheritdoc}
     * @throws SugarApiExceptionNotAuthorized
     */
    public function remove($lhs, $rhs, $save = true)
    {
        $result = parent::remove($lhs, $rhs, $save);

        if ($result && !$rhs->deleted) {
            $rhs->mark_deleted($rhs->id);
        }

        return $result;
    }

    /**
     * Set teams for attachments
     *
     * {@inheritDoc}
     * @see One2MBeanRelationship::add()
     */
    public function add($lhs, $rhs, $additionalFields = [])
    {
        $rhs->setAttachmentTeams($lhs, false);
        return parent::add($lhs, $rhs, $additionalFields);
    }
}
