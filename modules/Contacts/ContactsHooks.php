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

class ContactsHooks
{
    /**
     * Remove 'parent' relationship if 'tasks' or 'notes' relationships were removed
     *
     * @param RevenueLineItem $bean
     * @param string $event
     * @param array $args
     */
    public static function afterRelationshipDelete($bean, $event, $args)
    {
        if (in_array($args['link'], ['tasks', 'notes'])) {
            $relatedBean = BeanFactory::getBean($args['related_module'], $args['related_id']);

            if ($relatedBean->parent_id == $args['id'] && $relatedBean->parent_type == $args['module']) {
                $relatedBean->load_relationship('contact_parent');
                $relatedBean->contact_parent->delete($bean, $args['id']);
            }
        }
    }
}
