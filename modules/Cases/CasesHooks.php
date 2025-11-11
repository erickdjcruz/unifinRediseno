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

class CasesHooks
{
    /**
     * Set Primary Contact if it's not filled yet
     *
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function afterRelationshipAdd($bean, $event, $args)
    {
        $link = $args['link'] ?? '';
        if ($link == 'contacts' && ($args['related_id'] ?? '')) {
            if (!$bean->primary_contact_id) {
                $bean->primary_contact_id = $args['related_id'];
                // If it is a new case created through a contact the save is done through the subpanel
                if ($bean->isUpdate()) {
                    $bean->save();
                }
            }
        }
    }

    /**
     * It's not possible to remove the relationship if Contact is primary
     *
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function beforeRelationshipDelete($bean, $event, $args)
    {
        $link = $args['link'] ?? '';
        if ($link == 'contacts') {
            $primaryField = $bean->getFieldDefinition('primary_contact_name');

            if (($primaryField['required'] ?? false) == true &&
                !$bean->deleted &&
                $bean->primary_contact_id == ($args['related_id'] ?? '')) {
                throw new Exception('Attempted to remove a contact related to the required primary');
            }
        }
    }

    /**
     * After the relationship is deleted, it needs to clear Primary Contact
     *
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function afterRelationshipDelete($bean, $event, $args)
    {
        $link = $args['link'] ?? '';
        if ($link == 'contacts') {
            if ($bean->primary_contact_id == ($args['related_id'] ?? '')) {
                $bean->primary_contact_id = null;
                $bean->save();
            }
        }
    }
}
