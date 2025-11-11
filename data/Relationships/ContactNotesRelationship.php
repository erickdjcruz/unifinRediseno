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
 * Class ContactNotesRelationship
 *
 * Represents a bean-based one-to-many relationship for contact-notes.
 */
class ContactNotesRelationship extends One2MBeanRelationship
{
    /**
     * {@inheritDoc}
     * @see One2MBeanRelationship::getAdditionalKey()
     */
    protected function getAdditionalKey($link)
    {
        $relationship = $this->getExternalUserNotesRelationship();
        if ($relationship && $this->linkIsLHS($link)) {
            return [
                'lhs_key' => 'external_user_id',
                'rhs_key' => $relationship->getRHSKey(),
            ];
        }
        return parent::getAdditionalKey($link);
    }

    /**
     * Returns the relationship object for external_user_notes.
     * @return SugarRelationship|NULL
     */
    protected function getExternalUserNotesRelationship()
    {
        $bean = BeanFactory::newBean('ExternalUsers');
        if ($bean && $bean->load_relationship('notes')) {
            return $bean->notes->getRelationshipObject();
        }
        return null;
    }
}
